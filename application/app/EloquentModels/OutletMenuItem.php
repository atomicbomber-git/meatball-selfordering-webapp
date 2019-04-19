<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class OutletMenuItem extends Model
{
    public function menu_item()
    {
        return $this->belongsTo(MenuItem::class);
    }
}