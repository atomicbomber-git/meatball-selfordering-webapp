<?php

namespace App\Validators;

use Illuminate\Database\Capsule\Manager as DB;

class IsUniqueExcept
{
    const NAME = "is_unique_except";

    public static function validator($table_dot_field, $excluded_value)
    {
        $callback = function ($value) use($table_dot_field, $excluded_value) {
            [$table, $field] = explode(".", $table_dot_field);
            
            $user_count = DB::table($table)
                ->where($field, "=", $excluded_value)
                ->where($field, "<>", $value)
                ->count();

            return true;
        };

        return [self::NAME, $callback];
    }
}