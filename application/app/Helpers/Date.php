<?php

namespace App\Helpers;

use Carbon\Carbon;

class Date
{
    public static function today()
    {
        return Carbon::today();
    }
}
