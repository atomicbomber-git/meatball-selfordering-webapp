<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRelatedEntitiesCount;

class DiscountMenuItem extends Model
{
    use HasRelatedEntitiesCount;

    public $fillable = [
        "discount_id",
        "percentage",
        "outlet_menu_item_id",
    ];

    public function outlet_menu_item()
    {
        return $this->belongsTo(OutletMenuItem::class);
    }
}