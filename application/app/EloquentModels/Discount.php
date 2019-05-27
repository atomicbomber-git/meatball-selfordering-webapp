<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRelatedEntitiesCount;
use Carbon\Carbon;

class Discount extends Model
{
    use HasRelatedEntitiesCount;

    public $fillable = [
        "outlet_id", "name", "starts_at", "ends_at"
    ];

    const RELATED_ENTITIES = [
        "discount_menu_items",
    ];

    public function scopeActiveAndUpcoming($query)
    {
        return $query->whereTime("ends_at", ">", Carbon::now());
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function discount_menu_items()
    {
        return $this->hasMany(DiscountMenuItem::class);
    }
}