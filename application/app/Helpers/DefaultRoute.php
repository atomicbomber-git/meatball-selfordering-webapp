<?php

namespace App\Helpers;

use App\Enums\UserLevel;

class DefaultRoute
{
    public static function get()
    {
        $user = Auth::user();

        if ($user === null) {
            return "login";
        }
        else {
            switch ($user->level) {
                case UserLevel::ADMIN:
                    return "menuCategory/index";
                case UserLevel::SUPERVISOR:
                    return "salesInvoice/index";
                case UserLevel::WAITER:
                    return "home/show";
                case UserLevel::CASHIER:
                    return "salesInvoice/index";
                default:
                    get_instance()->error404();
            }
        }
    }
}