<?php

namespace App\Policies;

use App\EloquentModels\User;
use App\Enums\UserLevel;

class ReceiptPrinterPolicy
{
    public static function canIndex(?User $user)
    {
        if ($user === null) {
            return false;
        }

        return $user->level === UserLevel::SUPERVISOR;
    }
}