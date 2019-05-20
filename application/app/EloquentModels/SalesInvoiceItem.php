<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    public $fillable = [
        "sales_invoice_id", "name", "price", "discount", "quantity",
    ];
}