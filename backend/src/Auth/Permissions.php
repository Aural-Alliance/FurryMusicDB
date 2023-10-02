<?php

namespace App\Auth;

enum Permissions: string
{
    case AdministerAll = 'administer:all';

    /**
     * @param string[] $groups
     * @return self[]
     */
    public static function fromOAuth(array $groups = []): array
    {
        $localGroups = [];

        foreach ($groups as $group) {
            if (false !== stristr($group, 'admin')) {
                $localGroups[] = self::AdministerAll;
                continue;
            }

            $localGroup = self::tryFrom($group);
            if (null !== $localGroup) {
                $localGroups[] = $localGroup;
            }
        }

        return $localGroups;
    }
}
