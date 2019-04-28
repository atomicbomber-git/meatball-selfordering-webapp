<?php

namespace App\Helpers;

class URL {
    public static function has($value)
    {
        return strpos($_SERVER["REQUEST_URI"], $value) !== false;
    }
}