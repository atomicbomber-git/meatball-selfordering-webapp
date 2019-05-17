<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class DiscountMenuItem extends Model
{
    public $fillable = [
        "discount_id",
        "percentage",
        "outlet_menu_item_id",
    ];

    public function outlet_menu_item()
    {
        return $this->belongsTo(OutletMenuItem::class);
    }
}