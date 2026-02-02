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
        'liter_price',
    ];

    protected $casts = [
        'km_per_liter' => 'decimal:2',
        'liter_price' => 'decimal:2',
    ];

    public function deliveryOrders()
    {
        return $this->belongsToMany(DeliveryOrder::class, 'delivery_order_fleets')
            ->withPivot([
                'driver_name',
                'fuel_start_liter',
                'fuel_end_liter',
                'total_distance_km',
                'fuel_used_liter',
                'extra_cost',
                'notes'
            ])
            ->withTimestamps();
    }

    public function deliveryOrderFleets()
    {
        return $this->hasMany(DeliveryOrderFleet::class);
    }
}