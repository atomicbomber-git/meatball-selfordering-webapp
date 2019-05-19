<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;

class SalesInvoice extends Model
{
    const UNPAID = 'UNPAID';
    const FINISHED = 'FINISHED';

    const DINE_IN_TYPE = 'DINE_IN';
    const TAKEAWAY_TYPE = 'TAKEAWAY';

    const TYPES_EN = [
        self::DINE_IN_TYPE => 'Dine In',
        self::TAKEAWAY_TYPE => 'Takeway',
    ];

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

    public function discounts()
    {
        return $this->hasMany(Discount::class, "outlet_id", "outlet_id");
    }

    public function getDiscountMapAttribute()
    {
        $this->loadMissing(["discounts" => function ($query) {
            $query->whereTime("starts_at", "<", $this->created_at);
            $query->whereTime("ends_at", ">", $this->created_at);
        }, "discounts.discount_menu_items"]);

        return $this->discounts->reduce(function ($curr, $next) {
            return $curr->merge($next->discount_menu_items);
        }, collect())
        ->keyBy("outlet_menu_item_id");
    }

    /* The `pretax_total` attrribute */
    public function getPretaxTotalAttribute()
    {
        $this->loadMissing("outlet");

        $this->loadMissing(["planned_sales_invoice_items.menu_item.outlet_menu_item" => function ($query) {
            $query->where("outlet_id", $this->outlet->id);
        }]);

        return $this->planned_sales_invoice_items->sum(function ($sales_invoice_item) {
            return $sales_invoice_item->quantity *
                $sales_invoice_item->menu_item->outlet_menu_item->price *
                1 - ($this->discount_map[$sales_invoice_item->menu_item->outlet_menu_item->id]->percentage ?? 1);
        });
    }

    /* The `tax` attribute */
    public function getTaxAttribute()
    {
        $this->loadMissing("outlet");
        return $this->pretax_total * ($this->outlet->pajak_pertambahan_nilai);
    }

    /* The `service_charge` attribute */
    public function getServiceChargeAttribute()
    {
        $this->loadMissing("outlet");
        return $this->pretax_total * ($this->outlet->service_charge);
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