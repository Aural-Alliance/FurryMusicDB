<?php

use App\Environment;
use Doctrine\ORM\EntityManager;

return [
    // Slim app
    Slim\App::class => static function (
        Psr\Container\ContainerInterface $di,
        Environment $environment,
        Psr\Log\LoggerInterface $logger
    ) {
        $app = new Slim\App(
            responseFactory: new App\Http\Factory\ResponseFactory(),
            container: $di,
        );

        $routeCollector = $app->getRouteCollector();
        $routeCollector->setDefaultInvocationStrategy(new Slim\Handlers\Strategies\RequestResponse());

        if ($environment->isProduction()) {
            $routeCollector->setCacheFile($environment->getTempDirectory() . '/app_routes.cache.php');
        }

        call_user_func(include(__DIR__ . '/routes.php'), $app);

        // Injections
        $app->add(App\Middleware\GetUser::class);
        $app->add(new App\Middleware\InjectRouter());
        $app->add(App\Middleware\InjectSession::class);

        // System middleware for routing and body parsing.
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        // Redirects and updates that should happen before system middleware.
        $app->add(new App\Middleware\ApplyXForwardedProto());
        $app->add(new App\Middleware\ApplyResponseDefaults());

        $app->add(
            new RKA\Middleware\IpAddress(
                true,
                ['172.*.*.*'],
                'ip',
                [
                    'CF-Connecting-IP',
                    'True-Client-IP',
                    'Forwarded',
                    'X-Forwarded-For',
                    'X-Forwarded',
                    'X-Cluster-Client-Ip',
                    'Client-Ip',
                ]
            )
        );

        // Add an error handler for most in-controller/task situations.
        $app->addErrorMiddleware(
            !$environment->isProduction(),
            true,
            true,
            $logger
        );

        return $app;
    },

    // Console
    Symfony\Component\Console\Application::class => function (
        DI\Container $di,
        Doctrine\ORM\EntityManagerInterface $em
    ) {
        $console = new Symfony\Component\Console\Application(
            'Command Line Interface',
            '1.0.0'
        );

        // Doctrine ORM/DBAL
        Doctrine\ORM\Tools\Console\ConsoleRunner::addCommands(
            $console,
            new Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider($em)
        );

        // Add Doctrine Migrations
        $migrateConfig = new Doctrine\Migrations\Configuration\Migration\ConfigurationArray([
            'migrations_paths' => [
                'App\Entity\Migration' => dirname(__DIR__) . '/src/Entity/Migration',
            ],
            'table_storage' => [
                'table_name' => 'app_migrations',
            ],
        ]);

        $migrateFactory = Doctrine\Migrations\DependencyFactory::fromEntityManager(
            $migrateConfig,
            new Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager($em)
        );
        Doctrine\Migrations\Tools\Console\ConsoleRunner::addCommands($console, $migrateFactory);

        $commandLoader = new Symfony\Component\Console\CommandLoader\ContainerCommandLoader(
            $di,
            [
                // TODO Cli Commands
            ]
        );
        $console->setCommandLoader($commandLoader);

        return $console;
    },

    // HTTP client
    GuzzleHttp\Client::class => function (Psr\Log\LoggerInterface $logger) {
        $stack = GuzzleHttp\HandlerStack::create();

        $stack->unshift(
            function (callable $handler) {
                return function (Psr\Http\Message\RequestInterface $request, array $options) use ($handler) {
                    $options[GuzzleHttp\RequestOptions::VERIFY] = Composer\CaBundle\CaBundle::getSystemCaRootBundlePath(
                    );
                    return $handler($request, $options);
                };
            },
            'ssl_verify'
        );

        $stack->push(
            GuzzleHttp\Middleware::log(
                $logger,
                new GuzzleHttp\MessageFormatter('HTTP client {method} call to {uri} produced response {code}'),
                Psr\Log\LogLevel::DEBUG
            )
        );

        return new GuzzleHttp\Client(
            [
                'handler' => $stack,
                GuzzleHttp\RequestOptions::HTTP_ERRORS => false,
                GuzzleHttp\RequestOptions::TIMEOUT => 3.0,
            ]
        );
    },

    // Doctrine Entity Manager
    Doctrine\ORM\EntityManager::class => function (
        Psr\Cache\CacheItemPoolInterface $psr6Cache,
        Environment $environment
    ) {
        $parser = new Doctrine\DBAL\Tools\DsnParser([
            'postgres' => 'pdo_pgsql',
        ]);
        $connectionOptions = $parser->parse($environment->getDatabaseUrl() ?? '');

        // Fetch and store entity manager.
        $config = Doctrine\ORM\ORMSetup::createAttributeMetadataConfiguration(
            [$environment->getBaseDirectory() . '/src/Entity'],
            !$environment->isProduction(),
            $environment->getTempDirectory() . '/proxies',
            $psr6Cache
        );

        $config->setAutoGenerateProxyClasses(
            Doctrine\Common\Proxy\AbstractProxyFactory::AUTOGENERATE_FILE_NOT_EXISTS_OR_CHANGED
        );

        if (!Doctrine\DBAL\Types\Type::hasType('carbon_immutable')) {
            Doctrine\DBAL\Types\Type::addType('carbon_immutable', Carbon\Doctrine\CarbonImmutableType::class);
        }

        $eventManager = new Doctrine\Common\EventManager();

        $connection = Doctrine\DBAL\DriverManager::getConnection(
            $connectionOptions,
            $config,
            $eventManager
        );
        return new EntityManager($connection, $config, $eventManager);
    },

    Doctrine\ORM\EntityManagerInterface::class => DI\Get(Doctrine\ORM\EntityManager::class),

    // Monolog Logger
    Monolog\Logger::class => function (Environment $environment) {
        $logger = new Monolog\Logger('App');
        $loggingLevel = $environment->getLogLevel();

        $log_stderr = new Monolog\Handler\StreamHandler('php://stderr', $loggingLevel, true);
        $logger->pushHandler($log_stderr);

        $log_file = new Monolog\Handler\RotatingFileHandler(
            $environment->getTempDirectory() . '/app.log',
            5,
            $loggingLevel,
            true
        );
        $logger->pushHandler($log_file);

        return $logger;
    },

    Psr\Log\LoggerInterface::class => DI\get(Monolog\Logger::class),

    Symfony\Contracts\Cache\CacheInterface::class => static function (
        Environment $environment,
        Psr\Log\LoggerInterface $logger
    ) {
        /** @var Symfony\Contracts\Cache\CacheInterface $cacheInterface */
        if ($environment->isTesting()) {
            $cacheInterface = new Symfony\Component\Cache\Adapter\ArrayAdapter();
        } else {
            $tempDir = $environment->getTempDirectory() . DIRECTORY_SEPARATOR . 'cache';
            $cacheInterface = new Symfony\Component\Cache\Adapter\FilesystemAdapter(
                '',
                0,
                $tempDir
            );
        }

        $cacheInterface->setLogger($logger);
        return $cacheInterface;
    },

    Psr\Cache\CacheItemPoolInterface::class => DI\get(
        Symfony\Contracts\Cache\CacheInterface::class
    ),
    Psr\SimpleCache\CacheInterface::class => function (Psr\Cache\CacheItemPoolInterface $cache) {
        return new Symfony\Component\Cache\Psr16Cache($cache);
    },

    // Doctrine annotations reader
    Doctrine\Common\Annotations\Reader::class => static function (
        Psr\Cache\CacheItemPoolInterface $psr6Cache,
        Environment $environment
    ) {
        $proxyCache = new Symfony\Component\Cache\Adapter\ProxyAdapter($psr6Cache, 'annotations.');

        return new Doctrine\Common\Annotations\PsrCachedReader(
            new Doctrine\Common\Annotations\AnnotationReader(),
            $proxyCache,
            !$environment->isProduction()
        );
    },

    // Symfony Serializer
    App\Serializer\ApiSerializerInterface::class => DI\get(
        App\Serializer\ApiSerializer::class
    ),

    // Symfony Validator
    Symfony\Component\Validator\Validator\ValidatorInterface::class => static function (
        Doctrine\Common\Annotations\Reader $reader,
        Symfony\Component\Validator\ContainerConstraintValidatorFactory $constraintValidatorFactory
    ) {
        $builder = new Symfony\Component\Validator\ValidatorBuilder();
        $builder->setConstraintValidatorFactory($constraintValidatorFactory);
        $builder->enableAnnotationMapping();
        $builder->setDoctrineAnnotationReader($reader);

        return $builder->getValidator();
    },

    // Flysystem (Filesystem uploads)
    League\Flysystem\Filesystem::class => function (Environment $environment) {
        return new League\Flysystem\Filesystem(
            new League\Flysystem\Local\LocalFilesystemAdapter(
                dirname($environment->getParentDirectory()) . '/uploads'
            )
        );
    },

    // OAuth client
    League\OAuth2\Client\Provider\GenericProvider::class => function (
        Environment $environment,
        GuzzleHttp\Client $httpClient
    ) {
        $baseUrl = $environment->getBaseUrl();
        $serviceUrl = $environment->getOAuthServiceUrl();
        $internalServiceUrl = $environment->getOAuthInternalServiceUrl();

        $provider = new League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $environment->getOAuthClientId(),
            'clientSecret' => $environment->getOAuthClientSecret(),
            'redirectUri' => $baseUrl . '/api/login',
            'urlAuthorize' => $serviceUrl . '/application/o/authorize/',
            'urlAccessToken' => $internalServiceUrl . '/application/o/token/',
            'urlResourceOwnerDetails' => $internalServiceUrl . '/application/o/userinfo/',
            'pkceMethod' => League\OAuth2\Client\Provider\AbstractProvider::PKCE_METHOD_S256,
            'scopes' => 'openid profile email',
            'responseResourceOwnerId' => 'sub',
        ]);

        $provider->setHttpClient($httpClient);

        return $provider;
    },
];
