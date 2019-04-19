<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    public function outlet_menu_items()
    {
        return $this->hasMany(OutletMenuItem::class);
    }
}