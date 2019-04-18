<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    public $fillable = [
        "name", "image"
    ];

    const IMAGE_STORAGE_PATH = "storage/menu_categories";

    public function menu_items()
    {
        return $this->hasMany(MenuItem::class);
    }
}