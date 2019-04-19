<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function managed_outlet()
    {
        return $this->hasOne(Outlet::class, "outlet_administrator_id");
    }
}