<?php

namespace App\Helpers;

class Formatter {
    public static function currency($value)
    {
        return number_format($value, 2, ",", ".");
    }

    public static function salesInvoiceNumber($value)
    {
        return sprintf("%04d", $value);
    }
}