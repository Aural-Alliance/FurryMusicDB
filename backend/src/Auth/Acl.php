<?php

namespace App\Auth;

use Doctrine\ORM\EntityManagerInterface;

final class Acl
{
    public function __construct(
        private readonly CurrentUser $currentUser,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function canManageArtist(string $artistId): bool
    {
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
            ->setParameter('user', $this->currentUser->getLocalUser())
            ->getOneOrNullResult();

        return null !== $aclResult;
    }

    public function canManageLabel(string $labelId): bool
    {
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
            ->setParameter('user', $this->currentUser->getLocalUser())
            ->getOneOrNullResult();

        return null !== $aclResult;
    }

    public function isAdministrator(): bool
    {
        $permissions = $this->currentUser->getPermissions();
        return in_array(Permissions::AdministerAll, $permissions, true);
    }
}
