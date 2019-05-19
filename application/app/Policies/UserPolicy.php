<?php

namespace App\Policies;

use App\EloquentModels\User;
use App\Enums\UserLevel;

class UserPolicy
{
    public static function canDelete(?User $user, User $target_user)
    {
        if ($user === null || $user->level !== UserLevel::ADMIN || $target_user->level === UserLevel::ADMIN) {
            return false;
        }

        return true;
    }
}