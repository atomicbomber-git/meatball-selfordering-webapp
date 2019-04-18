<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    public function item_group()
    {
        return $this->belongsTo(ItemGroup::class);
    }
}