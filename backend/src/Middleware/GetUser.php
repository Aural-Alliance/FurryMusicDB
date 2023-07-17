<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Entity\Repository\UserRepository;
use App\Http\ServerRequest;
use App\Service\Auth0;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class GetUser implements MiddlewareInterface
{
    public function __construct(
        private readonly Auth0 $auth0,
        private readonly UserRepository $userRepo
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $this->auth0->getTokenFromRequest($request);

        $user = $this->userRepo->getOrCreate($token);

        $request = $request->withAttribute(
            ServerRequest::ATTR_USER,
            $user
        );

        return $handler->handle($request);
    }
}
