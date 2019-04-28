<?php

namespace App\Policies;

use App\EloquentModels\User;
use App\Enums\UserLevel;

class SalesInvoicePolicy
{
    public static function canIndex(?User $user)
    {
        if ($user === null) {
            return false;
        }

        return in_array($user->level, [
            UserLevel::CASHIER,
            UserLevel::SUPERVISOR,
        ]);
    }

    public function canConfirm(?User $user)
    {
        if ($user === null) {
            return false;
        }

        return in_array($user->level, [
            UserLevel::CASHIER,
            UserLevel::SUPERVISOR,
        ]);
    }
}