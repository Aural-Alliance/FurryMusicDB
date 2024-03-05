<?php

namespace App\Service;

use Auth0\SDK\Auth0 as Auth0Sdk;
use Auth0\SDK\Contract\TokenInterface;
use Auth0\SDK\Token;
use Psr\Http\Message\ServerRequestInterface;

final class Auth0
{
    public function __construct(
        private readonly Auth0Sdk $auth0
    ) {
    }

    public function getTokenFromRequest(
        ServerRequestInterface $request
    ): string {
        $token = trim($request->getHeaderLine('Authorization'));

        if (empty($token)) {
            throw new \InvalidArgumentException('No "Authorization" header provided.');
        }

        if (str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }

        return $token;
    }

    public function getSessionFromToken(
        string $token
    ): TokenInterface {
        return $this->auth0->decode(
            $token,
            tokenType: Token::TYPE_ACCESS_TOKEN
        );
    }

    public function getUserInfoFromToken(
        string $token
    ): array {
        $response = $this->auth0->authentication()->userInfo($token);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Could not retrieve user info from remote.');
        }

        $body = (string)$response->getBody();

        return json_decode(
            $body,
            true,
            flags: JSON_THROW_ON_ERROR
        );
    }
}
