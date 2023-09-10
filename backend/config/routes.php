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

    $app->group(
        '',
        function (RouteCollectorProxy $group) {
            $group->group(
                '/users',
                function (RouteCollectorProxy $group) {
                    $group->get('/me', App\Controller\Users\GetMeAction::class);
                }
            );

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

                    $group->group(
                        '/artist/{artist_id}',
                        function (RouteCollectorProxy $group) {
                            $apiEndpoints = [
                                [
                                    'album',
                                    'albums',
                                    App\Controller\Profile\AlbumsController::class,
                                ],
                            ];

                            buildCrudApiEndpoints($group, $apiEndpoints, 'api:profile:artist:');

                            $group->group(
                                '/album/{album_id}',
                                function (RouteCollectorProxy $group) {
                                    $apiEndpoints = [
                                        [
                                            'track',
                                            'tracks',
                                            App\Controller\Profile\TracksController::class,
                                        ],
                                    ];

                                    buildCrudApiEndpoints($group, $apiEndpoints, 'api:profile:artist:album:');
                                }
                            );
                        }
                    )->add(new App\Middleware\Acl\CheckCanManageArtist())
                        ->add(App\Middleware\GetArtist::class);
                }
            );
        }
    )->add(App\Middleware\GetUser::class);

    $app->get('/', App\Controller\IndexAction::class)
        ->setName('home');
};
