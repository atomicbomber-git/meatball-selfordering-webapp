<?php

namespace App\Policies;

use App\EloquentModels\User;
use App\Enums\UserLevel;
use App\EloquentModels\MenuItem;

class MenuItemPolicy
{
    public static function canDelete(?User $user, MenuItem $menu_item)
    {
        if ($user === null || $user->level !== UserLevel::ADMIN) {
            return false;
        }

        $menu_item->loadCount(["outlet_menu_items", "planned_sales_invoice_items"]);
        if (($menu_item->outlet_menu_items_count + $menu_item->planned_sales_invoice_items_count) > 0) {
            return false;
        }

        return true;
    }
}