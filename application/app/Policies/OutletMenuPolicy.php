<?php

namespace App\Policies;

use App\EloquentModels\User;
use App\Enums\UserLevel;
use App\EloquentModels\Outlet;

class OutletMenuPolicy
{
    public static function canIndex(?User $user)
    {
        return ($user !== null) && ($user->level === UserLevel::ADMIN);
    }

    public static function canCreate(?User $user)
    {
        return ($user !== null) && ($user->level === UserLevel::ADMIN);
    }

    public static function canUpdate(?User $user)
    {
        return ($user !== null) && ($user->level === UserLevel::ADMIN);
    }
}