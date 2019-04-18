<?php

namespace App\Helpers;

class Auth
{
    public static function check()
    {
        return get_instance()->session->user !== null;
    }

    public static function user()
    {
        return get_instance()->session->user;
    }
}