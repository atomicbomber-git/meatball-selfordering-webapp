<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    const UNPAID = 'UNPAID';
    const FINISHED = 'FINISHED';

    public $fillable = [
        "outlet_id", "waiter_id", "cashier_id", "number", "type", "status", "cash", "finished_at"
    ];

    public $appends = [
        "pretax_total", "tax", "service_charge", "total", "rounding", "total_change"
    ];

    protected $dates = [
        "finished_at",
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

    public function cashier()
    {
        return $this->belongsTo(User::class);
    }

    /* The `pretax_total` attrribute */
    public function getPretaxTotalAttribute()
    {
        $this->loadMissing("outlet");

        $this->loadMissing(["planned_sales_invoice_items.menu_item.outlet_menu_item" => function ($query) {
            $query->where("outlet_id", $this->outlet->id);
        }]);

        return $this->planned_sales_invoice_items->sum(function ($sales_invoice_item) {
            return $sales_invoice_item->quantity * $sales_invoice_item->menu_item->outlet_menu_item->price;
        });
    }

    /* The `tax` attribute */
    public function getTaxAttribute()
    {
        $this->loadMissing("outlet");
        return $this->pretax_total * ($this->outlet->pajak_pertambahan_nilai / 100);
    }

    /* The `service_charge` attribute */
    public function getServiceChargeAttribute()
    {
        $this->loadMissing("outlet");
        return $this->pretax_total * ($this->outlet->service_charge / 100);
    }

    /* The `total` attribute */
    public function getTotalAttribute()
    {
        return $this->pretax_total + ($this->tax + $this->service_charge);
    }

    /* The `rounding` attribute */
    public function getRoundingAttribute()
    {
       return round($this->total / 100) * 100;
    }

    /* The `total change` attribute */
    public function getTotalChangeAttribute()
    {
        return $this->cash - $this->rounding;
    }
}