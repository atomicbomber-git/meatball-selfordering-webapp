<?php

namespace App\Policies;

use App\EloquentModels\User;
use App\Enums\UserLevel;
use App\EloquentModels\Discount;

class DiscountPolicy
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

    public static function canDelete(?User $user, Discount $discount)
    {
        if ($user === null || $user->level !== UserLevel::ADMIN) {
            return false;
        }

        if ($discount->has_related_entities) {
            return false;
        }

        return true;
    }
}