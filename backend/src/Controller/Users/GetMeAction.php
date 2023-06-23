<?php

declare(strict_types=1);

namespace App\Controller\Users;

use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

final class GetMeAction
{
    public function __invoke(ServerRequest $request, Response $response): ResponseInterface
    {
        $session = $request->getAttribute(ServerRequest::AUTH_SESSION);

        return $response->withJson($session);
    }
}
