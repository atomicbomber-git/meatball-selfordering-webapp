<?php

namespace App\Helpers;

use Carbon\Carbon;

class Formatter {
    public static function currency($value)
    {
        return number_format($value, 2, ",", ".");
    }

    public static function salesInvoiceNumber($value)
    {
        return sprintf("%04d", $value);
    }

    public static function datetime($value)
    {
        return (new Carbon($value))->format("m/d/Y H:i:s");
    }
}