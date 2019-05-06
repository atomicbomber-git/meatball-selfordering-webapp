<?php

namespace App\Policies;

use App\EloquentModels\User;
use App\Enums\UserLevel;
use App\EloquentModels\MenuCategory;

class MenuCategoryPolicy
{
    public static function canIndex(?User $user)
    {
        if ($user === null) {
            return false;
        }

        return $user->level === UserLevel::ADMIN;
    }

    public static function canDelete(?User $user, MenuCategory $menuCategory)
    {
        if ($user === null || $user->level !== UserLevel::ADMIN) {
            return false;
        }

        $menuCategory->loadCount("menu_items");

        if ($menuCategory->menu_items_count > 0) {
            return false;
        }

        return true;
    }
}