<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class ReceiptPrinter extends Model
{
    const CASHIER_TYPE = "CASHIER";
    const KITCHEN_TYPE = "KITCHEN";
    const QUEUE_TYPE = "QUEUE";

    public $fillable = [
        "name", "ipv4_address", "port",
    ];
}