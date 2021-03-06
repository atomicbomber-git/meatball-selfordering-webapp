<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRelatedEntitiesCount;
use Carbon\Carbon;

class Outlet extends Model
{
    use HasRelatedEntitiesCount;

    public $fillable = [
        "id", "name", "address", "brand", "phone",
        "pajak_pertambahan_nilai", "service_charge", "print_server_url",
        "npwpd", "storage_manager_id", "supervisor_id"
    ];

    public $timestamps = false;

    const RELATED_ENTITIES = [
        "outlet_menu_items",
        "receipt_printers",
        "discounts",
    ];

    public function supervisor()
    {
        return $this->belongsTo(User::class);
    }

    public function outlet_menu_items()
    {
        return $this->hasMany(OutletMenuItem::class);
    }

    public function receipt_printers()
    {
        return $this->hasMany(ReceiptPrinter::class);
    }

    public function outlet_users()
    {
        return $this->hasMany(OutletUser::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    public function cashier_printer()
    {
        return $this->hasOne(ReceiptPrinter::class)
            ->where("type", ReceiptPrinter::CASHIER_TYPE)
            ->where("is_active", 1);
    }

    public function kitchen_printer()
    {
        return $this->hasOne(ReceiptPrinter::class)
            ->where("type", ReceiptPrinter::KITCHEN_TYPE)
            ->where("is_active", 1);
    }

    public function service_printer()
    {
        return $this->hasOne(ReceiptPrinter::class)
            ->where("type", ReceiptPrinter::SERVICE_TYPE)
            ->where("is_active", 1);
    }

    public function getDiscountMapAttribute()
    {
        $time = Carbon::now();

        $this->loadMissing(["discounts" => function ($query) use ($time) {
            $query->whereTime("starts_at", "<", $time);
            $query->whereTime("ends_at", ">", $time);
        }, "discounts.discount_menu_items"]);

        return $this->discounts->reduce(function ($curr, $next) {
            return $curr->merge($next->discount_menu_items);
        }, collect())
        ->keyBy("outlet_menu_item_id");
    }
}