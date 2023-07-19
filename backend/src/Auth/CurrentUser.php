<?php

namespace App\Auth;

use App\Entity\User;
use App\Service\Auth0;
use Auth0\SDK\Contract\TokenInterface;
use Doctrine\ORM\EntityManagerInterface;

final class CurrentUser
{
    public const UPDATE_THRESHOLD = 86400;

    private User $localUser;

    private TokenInterface $session;

    public function __construct(
        private readonly string $token,
        private readonly Auth0 $auth0,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function getSession(): TokenInterface
    {
        if (!isset($this->session)) {
            $this->session = $this->auth0->getSessionFromToken($this->token);
        }

        return $this->session;
    }

    public function getId(): string
    {
        $uid = $this->getSession()->getSubject();
        if (null === $uid) {
            throw new \RuntimeException('Token has no valid UID.');
        }

        return $uid;
    }

    /**
     * @return array<Permissions>
     */
    public function getPermissions(): array
    {
        $session = $this->getSession();

        return array_filter(
            array_map(
                fn(string $perm) => Permissions::tryFrom($perm),
                $session->toArray()['permissions'] ?? []
            )
        );
    }

    public function getLocalUser(): User
    {
        if (!isset($this->localUser)) {
            $session = $this->getSession();
            $id = $session->getSubject();

            if (null === $id) {
                throw new \RuntimeException('Session token ID is invalid.');
            }

            $user = $this->em->find(User::class, $id);

            if (!($user instanceof User)) {
                $user = new User($id);
            }

            if ($user->getUpdatedAt() < (time() - self::UPDATE_THRESHOLD)) {
                $userInfo = $this->auth0->getUserInfoFromToken($this->token);

                $user->setName($userInfo['name'] ?? null);
                $user->setEmail($userInfo['email'] ?? null);
                $user->setAvatar($userInfo['picture'] ?? null);
                $user->updated();

                $this->em->persist($user);
                $this->em->flush();
            }

            $this->localUser = $user;
        }

        return $this->localUser;
    }
}
