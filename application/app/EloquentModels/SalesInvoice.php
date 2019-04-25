<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    public $fillable = [
        "outlet_id", "waiter_id", "number"
    ];
}