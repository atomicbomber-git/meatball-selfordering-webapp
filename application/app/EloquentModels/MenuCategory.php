<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    public $fillable = [
        "name", "image", "description", "column", "priority",
    ];

    const IMAGE_STORAGE_PATH = "storage/menu_categories";
    const IMAGE_MAX_SIZE = 1024 * 40; // In bytes

    public function menu_items()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function outlet_menu_items()
    {
        return $this->hasManyThrough(OutletMenuItem::class, MenuItem::class);
    }
}