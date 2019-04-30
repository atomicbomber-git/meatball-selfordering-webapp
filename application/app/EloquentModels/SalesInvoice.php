<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    const UNPAID = 'UNPAID';
    const FINISHED = 'FINISHED';

    public $fillable = [
        "outlet_id", "waiter_id", "number", "type", "status", "cash"
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function sales_invoice_items()
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function planned_sales_invoice_items()
    {
        return $this->hasMany(PlannedSalesInvoiceItem::class);
    }
}