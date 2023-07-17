<?php

namespace App\Entity\Repository;

use App\Entity\User;
use App\Service\Auth0;
use Doctrine\ORM\EntityManagerInterface;

final class UserRepository
{
    public const UPDATE_THRESHOLD = 86400;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Auth0 $auth0
    ) {
    }

    public function getOrCreate(
        string $token
    ): User {
        $session = $this->auth0->getSessionFromToken($token);
        $id = $session->getSubject();

        $user = $this->em->find(User::class, $id);

        if (!($user instanceof User)) {
            $user = new User($id);
        }

        if ($user->getUpdatedAt() < (time() - self::UPDATE_THRESHOLD)) {
            $userInfo = $this->auth0->getUserInfoFromToken($token);

            $user->setName($userInfo['name'] ?? null);
            $user->setEmail($userInfo['email'] ?? null);
            $user->setAvatar($userInfo['picture'] ?? null);
            $user->updated();

            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }
}
