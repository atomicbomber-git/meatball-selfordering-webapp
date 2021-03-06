<?php

namespace App\Policies;

use App\EloquentModels\User;
use App\Enums\UserLevel;

class UserPolicy
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

    public static function canDelete(?User $user, User $target_user)
    {
        if ($user === null || $user->level !== UserLevel::ADMIN || $target_user->level === UserLevel::ADMIN) {
            return false;
        }

        if ($target_user->has_related_entities) {
            return false;
        }

        return true;
    }

    public static function canToggleActivationStatus(?User $user, User $target_user)
    {
        if ($user === null || $user->level !== UserLevel::ADMIN || $target_user->level === UserLevel::ADMIN) {
            return false;
        }

        return true;
    }
}