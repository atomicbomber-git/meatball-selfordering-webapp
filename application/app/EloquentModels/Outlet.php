<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
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
}