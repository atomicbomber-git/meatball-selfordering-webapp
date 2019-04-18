<?php

namespace App\Helpers;

use App\Enums\UserLevel;

class DefaultRoute
{
    public static function get()
    {
        $user = get_instance()->session->user;

        if ($user === null) {
            return "login";
        }
        else {
            if ($user->level === UserLevel::OUTLET_ADMIN) {
                return "itemType/index";
            }
            else if ($user->level === UserLevel::WAITER) {
                return "home/show";
            }
            else {
                return "itemType/index";
            }
        }
    }
}