<?php

namespace App\Helpers;

use App\EloquentModels\User;

class Auth
{
    public static function check()
    {
        return self::user() !== null;
    }

    public static function user()
    {
        $session_user_id = get_instance()->session->user->id;
        return User::find($session_user_id);
    }
}