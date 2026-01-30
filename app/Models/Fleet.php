<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fleet extends Model
{

    use SoftDeletes, ActivityLogs;

    protected $fillable = [
        'office_id',
        'fleet_name',
        'fuel_type',
        'license_plate',
        'km_per_liter',
    ];

    protected $casts = [
        'km_per_liter' => 'decimal:2',
    ];

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrderFleet::class);
    }
}