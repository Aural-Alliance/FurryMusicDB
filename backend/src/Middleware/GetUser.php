<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Auth\Acl;
use App\Auth\Auth;
use App\Http\ServerRequest;
use Doctrine\ORM\EntityManagerInterface;
use Mezzio\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class GetUser implements MiddlewareInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(ServerRequest::ATTR_SESSION);

        $auth = new Auth(
            $this->em,
            $session,
        );

        $user = $auth->getUser();

        $acl = new Acl(
            $this->em,
            $user
        );

        $request = $request->withAttribute(ServerRequest::ATTR_AUTH, $auth)
            ->withAttribute(ServerRequest::ATTR_USER, $user)
            ->withAttribute(ServerRequest::ATTR_ACL, $acl);

        return $handler->handle($request);
    }
}
