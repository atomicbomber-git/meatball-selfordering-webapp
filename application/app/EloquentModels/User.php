<?php

namespace App\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use App\Enums\UserLevel;
use App\Traits\HasRelatedEntitiesCount;

class User extends Model
{
    use HasRelatedEntitiesCount;

    public $fillable = [
        "name", "username", "nik", "password", "level"
    ];

    public $hidden = [
        "password"
    ];

    const RELATED_ENTITIES = [
        "supervised_outlet",
        "outlet_user",
        "waited_sales_invoices",
        "cashiered_sales_invoices",
    ];

    public $timestamps = false;

    public function supervised_outlet()
    {
        return $this->hasOne(Outlet::class, "supervisor_id");
    }

    public function outlet_user()
    {
        return $this->hasOne(OutletUser::class);
    }

    public function waited_sales_invoices()
    {
        return $this->hasMany(SalesInvoice::class, "waiter_id");
    }

    public function cashiered_sales_invoices()
    {
        return $this->hasMany(SalesInvoice::class, "cashier_id");
    }

    public function scopeIsNotAdmin($query)
    {
        return $query->where("level", "<>", UserLevel::ADMIN);
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

    public function getFormattedLevelAttribute()
    {
        return UserLevel::LEVELS[$this->level] ?? '-';
    }
}