<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    public function discount_menu_items()
    {
        return $this->hasMany(DiscountMenuItem::class);
    }
}