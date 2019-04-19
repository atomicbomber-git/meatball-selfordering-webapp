<?php

namespace App\Helpers;

use App\EloquentModels\User;

class Auth
{
    public static function check()
    {
        return get_instance()->session->user !== null;
    }

    public static function user()
    {
        if (!self::check()) {
            return null;
        }
        
        $session_user_id = get_instance()->session->user->id;
        return User::find($session_user_id);
    }
}