<?php

use Slim\Routing\RouteCollectorProxy;

return function (Slim\App $app) {
    $app->setBasePath('/api');

    $app->options(
        '/{routes:.+}',
        function (Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response) {
            return $response
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->withHeader(
                    'Access-Control-Allow-Headers',
                    'x-requested-with, Content-Type, Accept, Origin, Authorization'
                )
                ->withHeader('Access-Control-Allow-Origin', '*');
        }
    );

    $app->group(
        '',
        function (RouteCollectorProxy $group) {
            $group->group(
                '/users',
                function (RouteCollectorProxy $group) {
                    $group->get('/me', App\Controller\Users\GetMeAction::class);
                }
            );

            $apiEndpoints = [
                [
                    'label',
                    'labels',
                    App\Controller\LabelsController::class,
                ],
            ];

            foreach ($apiEndpoints as [$singular, $plural, $class, $permission]) {
                $group->group(
                    '',
                    function (RouteCollectorProxy $group) use ($singular, $plural, $class) {
                        $group->get('/' . $plural, $class . ':listAction')
                            ->setName('api:' . $plural);
                        $group->post('/' . $plural, $class . ':createAction');

                        $group->get('/' . $singular . '/{id}', $class . ':getAction')
                            ->setName('api:' . $singular);
                        $group->put('/' . $singular . '/{id}', $class . ':editAction');
                        $group->delete('/' . $singular . '/{id}', $class . ':deleteAction');
                    }
                );
            }
        }
    )->add(App\Middleware\GetUser::class);

    $app->get('/', App\Controller\IndexAction::class)
        ->setName('home');
};
