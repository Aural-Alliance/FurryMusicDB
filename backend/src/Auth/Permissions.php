<?php

namespace App\Auth;

enum Permissions: string
{
    // Avoid permissions ever being an empty array.
    case Authenticated = 'authenticated';
    case AdministerAll = 'administer:all';

    /**
     * @param array<array{
     *  id: string,
     *  name: string,
     *  description: string
     * }> $groups
     * @return self[]
     */
    public static function fromOAuth(array $groups = []): array
    {
        $localGroups = [
            self::Authenticated,
        ];

        foreach ($groups as $group) {
            if (false !== stristr($group['name'], 'admin')) {
                $localGroups[] = self::AdministerAll;
                continue;
            }

            $localGroup = self::tryFrom($group['name']);
            if (null !== $localGroup) {
                $localGroups[] = $localGroup;
            }
        }

        return $localGroups;
    }

    /**
     * @return self[]
     */
    public static function authenticated(): array
    {
        return [
            self::Authenticated,
        ];
    }
}
