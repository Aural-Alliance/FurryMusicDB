<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\Permissions;
use App\Entity\User;
use App\Http\Response;
use App\Http\ServerRequest;
use Auth0\SDK\Auth0;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final class AuthController
{
    public function __construct(
        private readonly Auth0 $auth0,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger
    ) {
    }

    public function loginAction(ServerRequest $request, Response $response): ResponseInterface
    {
        // It's a good idea to reset user sessions each time they go to login to avoid "invalid state" errors,
        // should they hit network issues or other problems that interrupt a previous login process:
        $this->auth0->clear();

        // Finally, set up the local application session, and redirect the user to the Auth0 Universal Login Page
        // to authenticate.
        return $response->withRedirect(
            $this->auth0->login($this->getCallbackUrl($request))
        );
    }

    public function callbackAction(ServerRequest $request, Response $response): ResponseInterface
    {
        $session = $request->getSession();
        $auth = $request->getAuth();

        if ($auth->isLoggedIn()) {
            return $response->withRedirect('/');
        }

        // Have the SDK complete the authentication flow:
        $this->auth0->exchange($this->getCallbackUrl($request));

        $session = $this->auth0->getCredentials();
        assert($session !== null);

        if (!isset($session->user)) {
            throw new \RuntimeException('No user info available.');
        }

        $userInfo = $session->user;

        $uid = $userInfo['sub'];
        $user = $this->em->find(User::class, $uid);

        if (!($user instanceof User)) {
            $user = new User($uid);
        }

        $user->setName($userInfo['name']);
        $user->setEmail($userInfo['email']);

        try {
            $userRolesResponse = $this->auth0->management()->users()->getRoles($uid);
            $userRoles = json_decode(
                $userRolesResponse->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
            $user->setPermissions(Permissions::fromOAuth((array)$userRoles));
        } catch (\Throwable $e) {
            $this->logger->error(
                'Could not fetch user roles.',
                [
                    'exception' => $e,
                ]
            );
        }

        $this->em->persist($user);
        $this->em->flush();

        $auth->setUser($user);

        return $response->withRedirect('/');
    }

    public function logoutAction(ServerRequest $request, Response $response): ResponseInterface
    {
        $auth = $request->getAuth();
        $auth->logout();

        return $response->withRedirect(
            $this->auth0->logout(
                (string)$request->getRouter()->getBaseUrl()
            )
        );
    }

    private function getCallbackUrl(ServerRequest $request): string
    {
        return $request->getRouter()->named(
            'callback',
            absolute: true
        );
    }
}
