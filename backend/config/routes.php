<?php

return function(Slim\App $app)
{
    $app->setBasePath('/api');

    $app->options('/{routes:.+}', function (Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response) {
        return $response
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'x-requested-with, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Origin', '*');
    });

    $app->get('/', App\Controller\IndexController::class)
        ->setName('home');
};
