<?php

declare(strict_types=1);

namespace App\Auth;

use App\Entity\User;
use App\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Mezzio\Session\SessionInterface;

final class Auth
{
    public const SESSION_USER_ID_KEY = 'user_id';

    private bool|User|null $user = null;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SessionInterface $session
    ) {
    }

    public function isLoggedIn(): bool
    {
        $user = $this->getUser();
        return ($user instanceof User);
    }

    /**
     * Get the authenticated user entity.
     *
     * @throws Exception
     */
    public function getUser(): ?User
    {
        if (null === $this->user) {
            $userId = $this->session->get(self::SESSION_USER_ID_KEY);

            if (null === $userId) {
                $this->user = false;
                return null;
            }

            $user = $this->em->find(User::class, $userId);
            if ($user instanceof User) {
                $this->user = $user;
            } else {
                $this->user = false;
                $this->logout();

                throw new Exception('Invalid user!');
            }
        }

        return ($this->user instanceof User)
            ? $this->user
            : null;
    }

    /**
     * Set the currently authenticated user.
     *
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->session->set(self::SESSION_USER_ID_KEY, $user->getId());
        $this->session->regenerate();

        $this->user = $user;
    }

    /**
     * End the user's currently logged in session.
     */
    public function logout(): void
    {
        if (isset($this->session) && $this->session instanceof SessionInterface) {
            $this->session->clear();
        }

        $this->session->regenerate();
        $this->user = null;
    }
}
