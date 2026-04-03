<?php

namespace App\Models\Concerns;

trait HasRole
{
    public const ROLE_VIEWER = 'viewer';
    public const ROLE_ANALYST = 'analyst';
    public const ROLE_ADMIN = 'admin';

    public static function roles(): array
    {
        return [
            self::ROLE_VIEWER,
            self::ROLE_ANALYST,
            self::ROLE_ADMIN,
        ];
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}
