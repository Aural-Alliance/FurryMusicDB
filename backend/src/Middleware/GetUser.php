<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\ServerRequest;
use Auth0\SDK\Auth0;
use Auth0\SDK\Token;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class GetUser implements MiddlewareInterface
{
    public function __construct(
        private readonly Auth0 $auth0
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = trim($request->getHeaderLine('Authorization'));

        if (empty($token)) {
            throw new \InvalidArgumentException('No "Authorization" header provided.');
        }

        if (str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }

        $session = $this->auth0->decode(
            $token,
            tokenType: Token::TYPE_TOKEN
        );

        $request = $request->withAttribute(ServerRequest::AUTH_SESSION, $session);

        return $handler->handle($request);
    }
}
