<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class DiscountMenuItem extends Model
{
    public $fillable = [
        "discount_id", "sales_menu_item",
    ];
}