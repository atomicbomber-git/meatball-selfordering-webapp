<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

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
        "outlet_id", "waiter_id", "cashier_id", "number", "type", "status", "cash", "special_discount",
        "total_paid", "pajak_pertambahan_nilai", "service_charge",
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

    public function waiter()
    {
        return $this->belongsTo(User::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class, "outlet_id", "outlet_id");
    }

    public function scopeIsFinished($query)
    {
        return $query->where("status", self::FINISHED);
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

    /* Planned sales invoice items that are not discounted */
    public function getUndiscountedItemsAttribute()
    {
        $this->loadMissing(["planned_sales_invoice_items.menu_item.outlet_menu_item" => function ($query) {
            $query->where("outlet_id", $this->outlet->id);
        }]);

        $items = $this->planned_sales_invoice_items->filter(function ($item) {
            $discount = $this->discount_map[$item->menu_item->outlet_menu_item->id]->percentage ?? 0;  
            return $discount == 0;
        });

        return $items;
    }

    /* Sum of total price * qtys of undiscounted items */
    public function getUndiscountedPretaxTotalAttribute() {
        return $this->undiscounted_items->sum(function ($item) {
            return $item->quantity * $item->menu_item->outlet_menu_item->price;
        });
    }

    /* Calculated special discount */
    public function getSpecialDiscountTotalAttribute()
    {
        return $this->undiscounted_pretax_total * $this->special_discount;
    }

    /* The `pretax_total` attrribute, without any discount applied */
    public function getPretaxTotalAttribute()
    {
        $this->loadMissing("outlet");
        $this->loadMissing(["planned_sales_invoice_items.menu_item.outlet_menu_item" => function ($query) {
            $query->where("outlet_id", $this->outlet->id);
        }]);

        return $this->planned_sales_invoice_items->sum(function ($sales_invoice_item) {
            return $sales_invoice_item->quantity *
                $sales_invoice_item->menu_item->outlet_menu_item->price *
                (1 - ($this->discount_map[$sales_invoice_item->menu_item->outlet_menu_item->id]->percentage ?? 0));
        });
    }

    /* Like `pretax_total`, but discounted */
    public function getPrediscountPretaxTotalAttribute()
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
    public function getTaxTotalAttribute()
    {
        $this->loadMissing("outlet");
        return $this->prediscount_pretax_total * ($this->outlet->pajak_pertambahan_nilai);
    }

    /* The `service_charge` attribute */
    public function getServiceChargeTotalAttribute()
    {
        $this->loadMissing("outlet");
        return $this->prediscount_pretax_total * ($this->outlet->service_charge);
    }

    /* The `total` attribute */
    public function getTotalAttribute()
    {
        return $this->pretax_total + ($this->tax_total + $this->service_charge_total - $this->special_discount_total);
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

    public function getArchivedPrediscountTotalPriceAttribute()
    {
        $this->loadMissing("sales_invoice_items");
        return $this->sales_invoice_items->sum(function ($sales_invoice_item) {
            return $sales_invoice_item->price * $sales_invoice_item->quantity;
        });
    }

    public function getArchivedTotalPriceAttribute()
    {
        $this->loadMissing("sales_invoice_items");
        return $this->sales_invoice_items->sum(function ($sales_invoice_item) {
            return $sales_invoice_item->price *
                $sales_invoice_item->quantity *
                (1 - $sales_invoice_item->discount);
        });
    }

    public function getArchivedSpecialDiscountAttribute()
    {
        $this->loadMissing("sales_invoice_items");

        /* Total of the price of items that isn't discounted by item discount */
        $undiscounted_total_price = $this->sales_invoice_items->sum(function ($sales_invoice_item) {
            if ($sales_invoice_item->discount != 0) {
                return 0;
            }
            else {
                return $sales_invoice_item->price * $sales_invoice_item->quantity;
            }
        });

        return $this->special_discount * $undiscounted_total_price;
    }

    public function getArchivedTaxAttribute()
    {
        $this->loadMissing("outlet");
        return  $this->pajak_pertambahan_nilai * $this->archived_prediscount_total_price;
    }

    public function getArchivedServiceChargeAttribute()
    {
        $this->loadMissing("outlet");
        return $this->service_charge * $this->archived_prediscount_total_price;
    }

    /* Archived rounding of the total price */
    public function getArchivedRoundingAttribute()
    {
        $this->loadMissing("sales_invoice_items", "outlet");
        $total = $this->archived_total_price + $this->archived_tax + $this->archived_service_charge - $this->archived_special_discount;
        return round($total / 100) * 100;
    }
}