<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Auth\Acl;
use App\Auth\CurrentUser;
use App\Http\ServerRequest;
use App\Service\Auth0;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

final class GetArtist implements MiddlewareInterface
{
    public function __construct(
        private readonly Auth0 $auth0,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeArgs = RouteContext::fromRequest($request)->getRoute()?->getArguments();

        $id = $routeArgs['artist_id'] ?? null;


        $token = $this->auth0->getTokenFromRequest($request);

        $currentUser = new CurrentUser(
            $token,
            $this->auth0,
            $this->em
        );

        $request = $request->withAttribute(
            ServerRequest::ATTR_USER,
            $currentUser
        );

        $acl = new Acl(
            $currentUser,
            $this->em
        );

        $request = $request->withAttribute(
            ServerRequest::ATTR_ACL,
            $acl
        );

        return $handler->handle($request);
    }
}
