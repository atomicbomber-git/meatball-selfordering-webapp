<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    public $fillable = [
        "outlet_id", "name", "starts_at", "ends_at"
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function discount_menu_items()
    {
        return $this->hasMany(DiscountMenuItem::class);
    }
}