<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use App\Enums\UserLevel;

class User extends Model
{
    public function managed_outlet()
    {
        return $this->hasOne(Outlet::class, "outlet_administrator_id");
    }

    public function outlet_user()
    {
        return $this->hasOne(OutletUser::class);
    }

    // This is not an Eloquent relationship method
    public function getOutletAttribute()
    {
        $outlet = null;

        if ($this->level === UserLevel::OUTLET_ADMIN) {
            $outlet = $this->managed_outlet;
        }
        else if ($this->level === UserLevel::WAITER) {
            $outlet = $this->outlet_user->outlet;
        }

        return $outlet;
    }
}