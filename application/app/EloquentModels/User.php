<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use App\Enums\UserLevel;

class User extends Model
{
    public $hidden = [
        "password"
    ];

    public function supervised_outlet()
    {
        return $this->hasOne(Outlet::class, "supervisor_id");
    }

    public function outlet_user()
    {
        return $this->hasOne(OutletUser::class);
    }

    // This is not an Eloquent relationship method
    public function getOutletAttribute()
    {
        $outlet = null;

        if ($this->level === UserLevel::SUPERVISOR) {
            $outlet = $this->supervised_outlet;
        }
        else if (in_array($this->level, [UserLevel::WAITER, UserLevel::CASHIER])) {
            $outlet = $this->outlet_user->outlet;
        }

        return $outlet;
    }
}