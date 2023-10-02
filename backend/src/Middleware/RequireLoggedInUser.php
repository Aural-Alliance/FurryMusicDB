<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Entity\User;
use App\Exception\PermissionDeniedException;
use App\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequireLoggedInUser implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $request->getAttribute(ServerRequest::ATTR_USER);

        if (!($user instanceof User)) {
            throw new PermissionDeniedException();
        }

        return $handler->handle($request);
    }
}
