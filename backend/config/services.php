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
        $httpFactory = new App\Http\HttpFactory();

        Slim\Factory\ServerRequestCreatorFactory::setSlimHttpDecoratorsAutomaticDetection(false);
        Slim\Factory\ServerRequestCreatorFactory::setServerRequestCreator($httpFactory);

        $app = new Slim\App(
            responseFactory: $httpFactory,
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
                'clear-cache' => App\Console\Command\ClearCacheCommand::class,
                'init' => App\Console\Command\InitCommand::class,
                'migrate' => App\Console\Command\MigrateCommand::class,
                'uptime-wait' => App\Console\Command\UptimeWaitCommand::class,
            ]
        );
        $console->setCommandLoader($commandLoader);

        return $console;
    },

    // HTTP client
    GuzzleHttp\Client::class => function (Psr\Log\LoggerInterface $logger) {
        $stack = GuzzleHttp\HandlerStack::create();

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
            ]
        );
    },

    Doctrine\DBAL\Connection::class => function (
        Environment $environment
    ) {
        $parser = new Doctrine\DBAL\Tools\DsnParser([
            'postgres' => 'pdo_pgsql',
        ]);
        $connectionOptions = $parser->parse($environment->getDatabaseUrl());

        return Doctrine\DBAL\DriverManager::getConnection($connectionOptions);
    },

    // Doctrine Entity Manager
    Doctrine\ORM\EntityManager::class => function (
        Doctrine\DBAL\Connection $connection,
        Psr\Cache\CacheItemPoolInterface $psr6Cache,
        Environment $environment
    ) {
        // Fetch and store entity manager.
        $config = Doctrine\ORM\ORMSetup::createAttributeMetadataConfiguration(
            [$environment->getBaseDirectory() . '/backend/src/Entity'],
            !$environment->isProduction(),
            $environment->getTempDirectory() . '/proxies',
            $psr6Cache
        );

        $config->setAutoGenerateProxyClasses(
            Doctrine\ORM\Proxy\ProxyFactory::AUTOGENERATE_FILE_NOT_EXISTS_OR_CHANGED
        );

        return new EntityManager($connection, $config);
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

    Symfony\Component\Cache\Adapter\AdapterInterface::class => static function (
        Environment $environment,
        Psr\Log\LoggerInterface $logger
    ) {
        if ($environment->isTesting()) {
            $cache = new Symfony\Component\Cache\Adapter\ArrayAdapter();
        } else {
            $cache = new Symfony\Component\Cache\Adapter\FilesystemAdapter(
                '',
                0,
                $environment->getTempDirectory() . DIRECTORY_SEPARATOR . 'cache'
            );
        }

        $cache->setLogger($logger);
        return $cache;
    },

    Psr\Cache\CacheItemPoolInterface::class => DI\get(
        Symfony\Component\Cache\Adapter\AdapterInterface::class
    ),

    Psr\SimpleCache\CacheInterface::class => function (Psr\Cache\CacheItemPoolInterface $cache) {
        return new Symfony\Component\Cache\Psr16Cache($cache);
    },

    // Symfony Serializer
    App\Serializer\ApiSerializerInterface::class => DI\get(
        App\Serializer\ApiSerializer::class
    ),

    // Symfony Validator
    Symfony\Component\Validator\Validator\ValidatorInterface::class => static function (
        Symfony\Component\Validator\ContainerConstraintValidatorFactory $constraintValidatorFactory
    ) {
        $builder = new Symfony\Component\Validator\ValidatorBuilder();
        $builder->setConstraintValidatorFactory($constraintValidatorFactory);
        $builder->enableAttributeMapping();

        return $builder->getValidator();
    },

    // Flysystem (Filesystem uploads)
    League\Flysystem\Filesystem::class => function (Environment $environment) {
        return new League\Flysystem\Filesystem(
            new League\Flysystem\Local\LocalFilesystemAdapter(
                $environment->getParentDirectory() . '/uploads'
            )
        );
    },

    // Intervention Image Manager
    Intervention\Image\ImageManager::class => fn() => new Intervention\Image\ImageManager(
        new Intervention\Image\Drivers\Gd\Driver()
    ),

    // Auth0
    Auth0\SDK\Auth0::class => static function (
        Environment $environment,
        Psr\Cache\CacheItemPoolInterface $psr6Cache,
    ) {
        $httpFactory = new GuzzleHttp\Psr7\HttpFactory();

        $sdk = new Auth0\SDK\Auth0($environment->getAuth0Info());
        $sdk->configuration()->setTokenCache($psr6Cache)
            ->setHttpRequestFactory($httpFactory)
            ->setHttpResponseFactory($httpFactory)
            ->setHttpStreamFactory($httpFactory);

        return $sdk;
    },
];
