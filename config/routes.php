<?php
use App\Controller;
use App\Middleware;
use Slim\Routing\RouteCollectorProxy;

return function(Slim\App $app)
{
    $app->group('/api', function (RouteCollectorProxy $group) {

        $group->options('/{routes:.+}', function (Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response) {
            return $response
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->withHeader('Access-Control-Allow-Headers', 'x-requested-with, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Origin', '*');
        });

        $group->get('', Controller\Api\IndexController::class)
            ->setName('api:index');

    }) // END /api GROUP
        ->add(Middleware\Module\Api::class);

    $app->get('/', Controller\Frontend\IndexController::class)
        ->setName('home');
};
