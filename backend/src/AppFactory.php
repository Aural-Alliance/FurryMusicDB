<?php

declare(strict_types=1);

namespace App;

use App\Http\Factory\ServerRequestFactory;
use DI;
use Monolog\ErrorHandler;
use Monolog\Logger;
use Monolog\Registry;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;
use Symfony\Component\Console\Application;

class AppFactory
{
    /**
     * @param array<string, mixed> $appEnvironment
     * @param array<string, mixed> $diDefinitions
     *
     */
    public static function createApp(
        array $appEnvironment = [],
        array $diDefinitions = []
    ): App {
        $di = self::buildContainer($appEnvironment, $diDefinitions);
        return self::buildAppFromContainer($di);
    }

    /**
     * @param array<string, mixed> $appEnvironment
     * @param array<string, mixed> $diDefinitions
     */
    public static function createCli(
        array $appEnvironment = [],
        array $diDefinitions = []
    ): Application {
        $di = self::buildContainer($appEnvironment, $diDefinitions);
        self::buildAppFromContainer($di);

        return $di->get(Application::class);
    }

    public static function buildAppFromContainer(DI\Container $container): App
    {
        ServerRequestCreatorFactory::setSlimHttpDecoratorsAutomaticDetection(false);
        ServerRequestCreatorFactory::setServerRequestCreator(new ServerRequestFactory());

        return $container->get(App::class);
    }

    /**
     * @param array<string, mixed> $appEnvironment
     * @param array<string, mixed> $diDefinitions
     */
    public static function buildContainer(
        array $appEnvironment = [],
        array $diDefinitions = []
    ): DI\Container {
        $_ENV = getenv();

        $environment = new Environment([
            ...$_ENV,
            ...$appEnvironment,
        ]);

        self::applyPhpSettings($environment);

        // Override DI definitions for settings.
        $diDefinitions[Environment::class] = $environment;

        $containerBuilder = new DI\ContainerBuilder();
        $containerBuilder->useAutowiring(true);

        if ($environment->isProduction()) {
            $containerBuilder->enableCompilation($environment->getTempDirectory());
        }

        $containerBuilder->addDefinitions($diDefinitions);

        // Check for services.php file and include it if one exists.
        $config_dir = $environment->getConfigDirectory();
        if (file_exists($config_dir . '/services.php')) {
            $containerBuilder->addDefinitions($config_dir . '/services.php');
        }

        $di = $containerBuilder->build();

        $logger = $di->get(Logger::class);
        $errorHandler = new ErrorHandler($logger);
        $errorHandler->registerFatalHandler();

        Registry::addLogger($logger, 'app', true);

        return $di;
    }

    protected static function applyPhpSettings(Environment $environment): void
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_STRICT);

        $displayStartupErrors = (!$environment->isProduction() || $environment->isCli())
            ? '1'
            : '0';
        ini_set('display_startup_errors', $displayStartupErrors);
        ini_set('display_errors', $displayStartupErrors);

        ini_set('log_errors', '1');
        ini_set('error_log', '/dev/stderr');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_lifetime', '86400');
        ini_set('session.use_strict_mode', '1');

        date_default_timezone_set('UTC');

        session_cache_limiter('');
    }
}
