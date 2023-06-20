<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ApplyResponseDefaults
{
    public function __invoke(
        ServerRequest $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->handle($request);

        if (($response instanceof Response) && !$response->hasCacheLifetime()) {
            $response = $response->withCacheLifetime(30);
        }

        // Only set global CORS for GET requests and API-authenticated requests;
        // Session-authenticated, non-GET requests should only be made in a same-host situation.
        if ('GET' === $request->getMethod()) {
            $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        }

        return $response;
    }
}
