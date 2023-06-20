<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

final class IndexController
{
    public function __invoke(ServerRequest $request, Response $response): ResponseInterface
    {
        return $response->withJson(
            [
                'success' => 'Hello, world!',
            ]
        );
    }
}
