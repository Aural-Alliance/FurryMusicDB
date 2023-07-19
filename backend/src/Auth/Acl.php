<?php

namespace App\Auth;

use App\Entity\Artist;
use App\Entity\Label;
use Doctrine\ORM\EntityManagerInterface;

final class Acl
{
    public function __construct(
        private readonly CurrentUser $currentUser,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function canUserManageArtist(Artist $artist): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        // TODO
        return false;
    }

    public function canManageLabel(Label $label): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        // TODO
        return false;
    }

    public function isAdministrator(): bool
    {
        $permissions = $this->currentUser->getPermissions();
        return in_array(Permissions::AdministerAll, $permissions, true);
    }
}
