<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class ReceiptPrinter extends Model
{
    const CASHIER_TYPE = "CASHIER";
    const KITCHEN_TYPE = "KITCHEN";
    const SERVICE_TYPE = "SERVICE";

    const TYPES = [
        self::CASHIER_TYPE => "Kasir",
        self::KITCHEN_TYPE => "Dapur",
        self::SERVICE_TYPE => "Service",
    ];

    public $fillable = [
        "outlet_id", "name", "ipv4_address", "port", "type", "is_active",
    ];
}