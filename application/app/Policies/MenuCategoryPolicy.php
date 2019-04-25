<?php

namespace App\Policies;

use App\EloquentModels\User;
use App\Enums\UserLevel;
use App\EloquentModels\MenuCategory;

class MenuCategoryPolicy
{
    public static function canIndex(User $user)
    {
        return $user->level === UserLevel::OUTLET_ADMIN;
    }

    public static function canDelete(User $user, MenuCategory $menuCategory)
    {
        $menuCategory->loadCount("menu_items");

        if ($menuCategory->menu_items_count > 0) {
            return FALSE;
        }

        return TRUE;
    }
}