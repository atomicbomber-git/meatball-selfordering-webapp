<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    public function outlet_menu_item()
    {
        return $this->hasOne(OutletMenuItem::class);
    }
}