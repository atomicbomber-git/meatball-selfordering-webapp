<?php

namespace App\Policies;

use App\EloquentModels\User;
use App\Enums\UserLevel;

class MenuCategoryPolicy
{
    public static function canIndex(User $user)
    {
        return $user->level === UserLevel::OUTLET_ADMIN;
    }
}