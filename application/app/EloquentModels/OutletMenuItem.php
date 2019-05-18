<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class OutletMenuItem extends Model
{
    public $fillable = [
        "outlet_id", "menu_item_id", "price"
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function menu_item()
    {
        return $this->belongsTo(MenuItem::class);
    }
}