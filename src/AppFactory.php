<?php

declare(strict_types=1);

namespace App;

use App\Http\Factory\ServerRequestFactory;
use DI;
use Monolog\Registry;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;
use Symfony\Component\Console\Application;

use const E_COMPILE_ERROR;
use const E_CORE_ERROR;
use const E_ERROR;
use const E_PARSE;
use const E_USER_ERROR;

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
        // Register Annotation autoloader
        $environment = self::buildEnvironment($appEnvironment);

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

        $logger = $di->get(LoggerInterface::class);

        register_shutdown_function(
            static function (LoggerInterface $logger): void {
                $error = error_get_last();
                if (null === $error) {
                    return;
                }

                $errno = $error["type"];
                $errfile = $error["file"];
                $errline = $error["line"];
                $errstr = $error["message"];

                if ($errno &= E_PARSE | E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR) {
                    $logger->critical(
                        sprintf(
                            'Fatal error: %s in %s on line %d',
                            $errstr,
                            $errfile,
                            $errline
                        )
                    );
                }
            },
            $logger
        );

        Registry::addLogger($logger, 'app', true);

        return $di;
    }

    /**
     * @param array<string, mixed> $environment
     */
    public static function buildEnvironment(array $environment = []): Environment
    {
        if (!isset($environment[Environment::BASE_DIR])) {
            throw new \LogicException('No base directory specified!');
        }

        $environment[Environment::IS_DOCKER] = file_exists(
            dirname($environment[Environment::BASE_DIR]) . '/.docker'
        );

        $environment[Environment::TEMP_DIR] ??= dirname($environment[Environment::BASE_DIR]) . '/www_tmp';
        $environment[Environment::CONFIG_DIR] ??= $environment[Environment::BASE_DIR] . '/config';

        $_ENV = getenv();
        $environment = array_merge(array_filter($_ENV), $environment);

        return new Environment($environment);
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
        ini_set(
            'error_log',
            $environment->isDocker()
                ? '/dev/stderr'
                : $environment->getTempDirectory() . '/php_errors.log'
        );
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_lifetime', '86400');
        ini_set('session.use_strict_mode', '1');

        date_default_timezone_set('UTC');

        session_cache_limiter('');
    }
}
