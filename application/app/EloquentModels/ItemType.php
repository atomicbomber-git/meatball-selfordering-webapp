<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    const IMAGE_STORAGE_PATH = "storage/item_types";

    public $timestamps = false;

    public $fillable = [
        "name", "image", "item_group_id",
    ];

    public function item_group()
    {
        return $this->belongsTo(ItemGroup::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function delete()
    {
        unlink($this->image);
        parent::delete();
    }
}