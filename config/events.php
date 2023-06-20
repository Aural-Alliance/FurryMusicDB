<?php
use App\Event;
use App\Environment;
use App\Middleware;
use RKA\Middleware\IpAddress;
use Symfony\Component\EventDispatcher\EventDispatcher;

return function (EventDispatcher $dispatcher) {
    $dispatcher->addListener(Event\BuildConsoleCommands::class, function (Event\BuildConsoleCommands $event) {
        $console = $event->getConsole();
        $di = $event->getContainer();

        // Add Doctrine Migrations
        /** @var Doctrine\ORM\EntityManagerInterface $em */
        $em = $di->get(Doctrine\ORM\EntityManagerInterface::class);

        // Doctrine ORM/DBAL
        Doctrine\ORM\Tools\Console\ConsoleRunner::addCommands(
            $console,
            new Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider($em)
        );

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

        call_user_func(include(__DIR__ . '/cli.php'), $event);
    });

    $dispatcher->addListener(Event\BuildRoutes::class, function (Event\BuildRoutes $event) {
        $app = $event->getApp();

        // Load app-specific route configuration.
        $container = $app->getContainer();

        /** @var Environment $environment */
        $environment = $container->get(Environment::class);

        call_user_func(include(__DIR__ . '/routes.php'), $app);

        // Request injection middlewares.
        $app->add(Middleware\InjectRouter::class);

        // System middleware for routing and body parsing.
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        // Redirects and updates that should happen before system middleware.
        $app->add(new Middleware\RemoveSlashes);
        $app->add(new Middleware\ApplyXForwardedProto);

        $app->add(new IpAddress(
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
            $container->get(Psr\Log\LoggerInterface::class)
        );
    });
};
