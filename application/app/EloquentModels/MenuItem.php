<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    public $fillable = [
        "name", "menu_category_id",
    ];

    public function menu_category()
    {
        return $this->belongsTo(MenuCategory::class);
    }

    public function outlet_menu_item()
    {
        return $this->hasOne(OutletMenuItem::class);
    }

    public function outlet_menu_items()
    {
        return $this->hasMany(OutletMenuItem::class);
    }

    public function planned_sales_invoice_items()
    {
        return $this->hasMany(PlannedSalesInvoiceItem::class);
    }
}