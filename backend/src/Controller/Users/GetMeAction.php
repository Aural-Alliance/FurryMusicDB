<?php

declare(strict_types=1);

namespace App\Controller\Users;

use App\Controller\AbstractEntityController;
use App\Http\Response;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

final class GetMeAction extends AbstractEntityController
{
    public function __invoke(ServerRequest $request, Response $response): ResponseInterface
    {
        $user = $request->getUser();

        return $response->withJson(
            $this->toArray($user)
        );
    }
}
