<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\Permissions;
use App\Entity\User;
use App\Http\Response;
use App\Http\ServerRequest;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

final class LoginAction
{
    public function __construct(
        private readonly GenericProvider $oauth,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function __invoke(ServerRequest $request, Response $response): ResponseInterface
    {
        $session = $request->getSession();
        $auth = $request->getAuth();

        if ($auth->isLoggedIn()) {
            return $response->withRedirect('/');
        }

        if ($request->getQueryParam('code') === null) {
            $authorizationUrl = $this->oauth->getAuthorizationUrl();

            $session->set('oauth2state', $this->oauth->getState());
            $session->set('oauth2pkce', $this->oauth->getPkceCode());

            return $response->withRedirect($authorizationUrl);
        }

        $queryState = $request->getQueryParam('state');

        if (empty($queryState) || $queryState !== $session->get('oauth2state')) {
            $session->unset('oauth2state');
            throw new \RuntimeException('Invalid state!');
        }

        $this->oauth->setPkceCode($session->get('oauth2pkce'));

        /** @var AccessToken $accessToken */
        $accessToken = $this->oauth->getAccessToken('authorization_code', [
            'code' => $request->getQueryParam('code'),
        ]);

        $resourceOwner = $this->oauth->getResourceOwner($accessToken);
        $uid = $resourceOwner->getId();
        $userInfo = $resourceOwner->toArray();

        $user = $this->em->find(User::class, $uid);

        if (!($user instanceof User)) {
            $user = new User($uid);
        }

        $user->setName($userInfo['name']);
        $user->setEmail($userInfo['email']);
        $user->setPermissions(Permissions::fromOAuth($userInfo['groups']));

        $this->em->persist($user);
        $this->em->flush();

        $auth->setUser($user);

        return $response->withRedirect('/');
    }
}
