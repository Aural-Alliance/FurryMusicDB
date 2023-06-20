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
            container:       $di,
        );

        $routeCollector = $app->getRouteCollector();
        $routeCollector->setDefaultInvocationStrategy(new Slim\Handlers\Strategies\RequestResponse());

        if ($environment->isProduction()) {
            $routeCollector->setCacheFile($environment->getTempDirectory() . '/app_routes.cache.php');
        }

        call_user_func(include(__DIR__ . '/routes.php'), $app);

        // System middleware for routing and body parsing.
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        // Redirects and updates that should happen before system middleware.
        $app->add(new App\Middleware\RemoveSlashes());
        $app->add(new App\Middleware\ApplyXForwardedProto());
        $app->add(new App\Middleware\ApplyResponseDefaults());

        $app->add(new RKA\Middleware\IpAddress(
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
        ));

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
            'postgres' => 'pdo_pgsql'
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
        $logger = new Monolog\Logger($environment->getAppName());
        $loggingLevel = $environment->getLogLevel();

        if ($environment->isDocker() || $environment->isCli()) {
            $log_stderr = new Monolog\Handler\StreamHandler('php://stderr', $loggingLevel, true);
            $logger->pushHandler($log_stderr);
        }

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
];
