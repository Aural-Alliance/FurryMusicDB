<?php

declare(strict_types=1);

namespace App\Middleware\Acl;

use App\Exception\PermissionDeniedException;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CheckIsAdministrator
{
    public function __invoke(
        ServerRequest $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $acl = $request->getAcl();
        if (!$acl->isAdministrator()) {
            throw new PermissionDeniedException();
        }

        return $handler->handle($request);
    }
}
