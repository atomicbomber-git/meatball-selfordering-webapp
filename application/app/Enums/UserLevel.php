<?php

namespace App\Enums;

class UserLevel
{
    const SUPERVISOR = "SUPERVISOR";
    const WAITER = "WAITER";
    const CASHIER = "CASHIER";
    const ADMIN = "ADMIN";

    const LEVELS = [
        self::SUPERVISOR => "Supervisor",
        self::WAITER => "Waiter / Pramusaji",
        self::CASHIER => "Kasir",
        self::ADMIN => "Admin Aplikasi",
    ];
}