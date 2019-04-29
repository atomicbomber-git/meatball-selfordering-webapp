<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class ReceiptPrinter extends Model
{
    public $fillable = [
        "name", "ipv4_address", "port",
    ];
}