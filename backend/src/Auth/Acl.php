<?php

namespace App\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class Acl
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ?User $user
    ) {
    }

    public function canManageArtist(string $artistId): bool
    {
        if (null === $this->user) {
            return false;
        }

        if ($this->isAdministrator()) {
            return true;
        }

        $aclResult = $this->em->createQuery(
            <<<'DQL'
                SELECT a.id FROM App\Entity\Artist a
                WHERE a.id = :id
                AND (
                    a.owner = :user OR
                    a.label IN (SELECT l FROM App\Entity\Label l WHERE l.owner = :user)
                )
            DQL
        )->setParameter('id', $artistId)
            ->setParameter('user', $this->user)
            ->getOneOrNullResult();

        return null !== $aclResult;
    }

    public function canManageLabel(string $labelId): bool
    {
        if (null === $this->user) {
            return false;
        }

        if ($this->isAdministrator()) {
            return true;
        }

        $aclResult = $this->em->createQuery(
            <<<'DQL'
                SELECT l.id FROM App\Entity\Label l
                WHERE l.id = :id
                AND l.owner = :user
            DQL
        )->setParameter('id', $labelId)
            ->setParameter('user', $this->user)
            ->getOneOrNullResult();

        return null !== $aclResult;
    }

    public function isAdministrator(): bool
    {
        if (null === $this->user) {
            return false;
        }

        $permissions = $this->user->getPermissions();
        return in_array(Permissions::AdministerAll, $permissions, true);
    }
}
