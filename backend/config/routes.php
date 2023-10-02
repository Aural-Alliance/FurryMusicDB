<?php

use Slim\Routing\RouteCollectorProxy;

function buildCrudApiEndpoints(
    RouteCollectorProxy $group,
    array $apiEndpoints,
    string $routePrefix = 'api:'
): void {
    foreach ($apiEndpoints as [$singular, $plural, $class]) {
        $group->get('/' . $plural, $class . ':listAction')
            ->setName($routePrefix . $plural);
        $group->post('/' . $plural, $class . ':createAction');

        $group->get('/' . $singular . '/{id}', $class . ':getAction')
            ->setName($routePrefix . $singular);
        $group->put('/' . $singular . '/{id}', $class . ':editAction');
        $group->delete('/' . $singular . '/{id}', $class . ':deleteAction');
    }
}

return function (Slim\App $app) {
    $app->setBasePath('/api');

    $app->options(
        '/{routes:.+}',
        function (Psr\Http\Message\ServerRequestInterface $request, Psr\Http\Message\ResponseInterface $response) {
            return $response
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->withHeader(
                    'Access-Control-Allow-Headers',
                    'x-requested-with, Content-Type, Accept, Origin, Authorization'
                )
                ->withHeader('Access-Control-Allow-Origin', '*');
        }
    );

    $app->get(
        '/artists',
        App\Controller\Artists\ListArtistsAction::class
    )->setName('artists');

    $app->get(
        '/artist/{artist_id}',
        App\Controller\Artists\GetArtistAction::class
    )->setName('artist');

    $app->get(
        '/artist/{artist_id}/art[-{timestamp}.jpg]',
        App\Controller\Artists\GetArtAction::class
    )->setName('artist:art');

    $app->get(
        '/labels',
        App\Controller\Labels\ListLabelsAction::class
    )->setName('labels');

    $app->get(
        '/label/{label_id}',
        App\Controller\Labels\GetLabelAction::class
    )->setName('label');

    $app->get(
        '/label/{label_id}/art[-{timestamp}.jpg]',
        App\Controller\Labels\GetArtAction::class
    )->setName('label:art');

    $app->get('/login', App\Controller\LoginAction::class);

    $app->get('/logout', App\Controller\LogoutAction::class);

    $app->group(
        '/users',
        function (RouteCollectorProxy $group) {
            $group->get('/me', App\Controller\Users\GetMeAction::class);
        }
    );

    $app->group(
        '',
        function (RouteCollectorProxy $group) {
            $group->group(
                '/admin',
                function (RouteCollectorProxy $group) {
                    $apiEndpoints = [
                        [
                            'label',
                            'labels',
                            App\Controller\Admin\LabelsController::class,
                        ],
                        [
                            'artist',
                            'artists',
                            App\Controller\Admin\ArtistsController::class,
                        ],
                    ];

                    buildCrudApiEndpoints($group, $apiEndpoints, 'api:admin:');
                }
            )->add(new App\Middleware\Acl\CheckIsAdministrator());

            $group->group(
                '/profile',
                function (RouteCollectorProxy $group) {
                    $apiEndpoints = [
                        [
                            'label',
                            'labels',
                            App\Controller\Profile\LabelsController::class,
                        ],
                        [
                            'artist',
                            'artists',
                            App\Controller\Profile\ArtistsController::class,
                        ],
                    ];

                    buildCrudApiEndpoints($group, $apiEndpoints, 'api:profile:');

                    $group->group(
                        '/label/{label_id}',
                        function (RouteCollectorProxy $group) {
                            $apiEndpoints = [
                                [
                                    'artist',
                                    'artists',
                                    App\Controller\Profile\LabelArtistsController::class,
                                ],
                            ];

                            buildCrudApiEndpoints($group, $apiEndpoints, 'api:profile:label:');
                        }
                    )->add(new App\Middleware\Acl\CheckCanManageLabel())
                        ->add(App\Middleware\GetLabel::class);
                }
            );
        }
    )->add(App\Middleware\RequireLoggedInUser::class);

    $app->get('/', App\Controller\IndexAction::class)
        ->setName('home');
};
