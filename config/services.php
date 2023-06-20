<?php

use App\Environment;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

return [
    // Slim interface
    Slim\Interfaces\RouteCollectorInterface::class => static function (Slim\App $app) {
        return $app->getRouteCollector();
    },

    Slim\Interfaces\RouteParserInterface::class => static function (
        Slim\Interfaces\RouteCollectorInterface $routeCollector
    ) {
        return $routeCollector->getRouteParser();
    },

    // URL Router helper
    App\Http\RouterInterface::class => DI\Get(App\Http\Router::class),

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

    // DBAL
    Doctrine\DBAL\Connection::class => function (Doctrine\ORM\EntityManager $em) {
        return $em->getConnection();
    },

    // Console
    Symfony\Component\Console\Application::class => function (
        DI\Container $di,
        Psr\EventDispatcher\EventDispatcherInterface $dispatcher
    ) {
        $console = new Symfony\Component\Console\Application(
            'Command Line Interface',
            '1.0.0'
        );

        // Trigger an event for the core app and all plugins to build their CLI commands.
        $event = new App\Event\BuildConsoleCommands($console, $di);
        $dispatcher->dispatch($event);

        $commandLoader = new Symfony\Component\Console\CommandLoader\ContainerCommandLoader(
            $di,
            $event->getAliases()
        );
        $console->setCommandLoader($commandLoader);

        return $console;
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
        $eventManager->addEventSubscriber(
            new App\Doctrine\Event\SqlitePragmaSetup()
        );

        $connection = Doctrine\DBAL\DriverManager::getConnection(
            $connectionOptions,
            $config,
            $eventManager
        );
        return new EntityManager($connection, $config, $eventManager);
    },

    Doctrine\ORM\EntityManagerInterface::class => DI\Get(Doctrine\ORM\EntityManager::class),

    // Event Dispatcher
    Psr\EventDispatcher\EventDispatcherInterface::class => function() {
        $dispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();

        if (file_exists(__DIR__ . '/events.php')) {
            call_user_func(include(__DIR__ . '/events.php'), $dispatcher);
        }

        return $dispatcher;
    },

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
    }
];
