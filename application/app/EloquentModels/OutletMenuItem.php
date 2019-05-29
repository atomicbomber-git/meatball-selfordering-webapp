<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRelatedEntitiesCount;

class OutletMenuItem extends Model
{
    use HasRelatedEntitiesCount;

    public $fillable = [
        "outlet_id", "menu_item_id", "price"
    ];

    const RELATED_ENTITIES = [
        "discount_menu_items",
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope("active", function ($query) {
            $query->where("is_active", true);
        });
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function menu_item()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function discount_menu_items()
    {
        return $this->hasMany(DiscountMenuItem::class);
    }
}