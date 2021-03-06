<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class PlannedSalesInvoiceItem extends Model
{
    public $fillable = [
        "sales_invoice_id", "menu_item_id", "quantity"
    ];

    public function menu_item()
    {
        return $this->belongsTo(MenuItem::class);
    }
}