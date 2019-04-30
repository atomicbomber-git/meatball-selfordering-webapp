<?php

namespace App\Helpers;

class Formatter {
    public static function currency($value)
    {
        return number_format($value, 2, ",", ".");
    }
}