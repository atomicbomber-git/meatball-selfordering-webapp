<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class OutletUser extends Model
{
    public $fillable = [
        "outlet_id", "user_id"
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}