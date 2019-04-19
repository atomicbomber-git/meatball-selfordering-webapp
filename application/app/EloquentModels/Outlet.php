<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    public function outlet_menu_items()
    {
        return $this->hasMany(OutletMenuItem::class);
    }

    public function receipt_printers()
    {
        return $this->hasMany(ReceiptPrinter::class);
    }
}