<?php

namespace App\Enums;

class UserLevel
{
    const OUTLET_ADMIN = "OUTLET_ADMIN";
    const WAITER = "WAITER";
    const CASHIER = "CASHIER";
    const SUPERADMIN = "SUPERADMIN";

    const LEVELS = [
        self::OUTLET_ADMIN => "Administrator Outlet",
        self::WAITER => "Waiter / Pramusaji",
        self::CASHIER => "Kasir",
        self::SUPERADMIN => "Super Admin Aplikasi",
    ];
}