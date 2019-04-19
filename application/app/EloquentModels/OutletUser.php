<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class OutletUser extends Model
{
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}