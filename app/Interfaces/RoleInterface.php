<?php

namespace App\Interfaces;

interface RoleInterface
{

    public const ROLE_SUPERADMIN = 'superuser';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_ADVISOR = 'advisor';
    public const ROLE_CLIENT = 'client';

    public const ROLE_IDS = [
        self::ROLE_SUPERADMIN => 1,
        self::ROLE_ADMIN => 2,
        self::ROLE_ADVISOR => 3,
        self::ROLE_CLIENT => 4
    ];

}
