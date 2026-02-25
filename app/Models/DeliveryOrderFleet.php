<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderFleet extends Model
{
    use ActivityLogs;

    protected $fillable = [
        'delivery_order_id',
        'fleet_id',
        'driver_id',
        'fuel_start_liters',
        'fuel_end_liters',
        'distance_traveled_km',
        'fuel_used_liters',
        'toll_cost',
        'parking_cost',
        'other_cost',
        'notes',
        'route_data',
        'estimated_distance_km',
        'estimated_fuel_cost',
        'additional_costs',
        'start_latitude',
        'start_longitude',
        'last_latitude',
        'last_longitude',
        'status',
        'odo_start',
        'odo_end',
        'cash_amount',
        'gas_leftover',
    ];

    protected $casts = [
        'fuel_start_liters' => 'decimal:2',
        'fuel_end_liters' => 'decimal:2',
        'distance_traveled_km' => 'decimal:2',
        'fuel_used_liters' => 'decimal:2',
        'toll_cost' => 'decimal:2',
        'parking_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'estimated_distance_km' => 'decimal:2',
        'estimated_fuel_cost' => 'decimal:2',
        'additional_costs' => 'array',
        'start_latitude' => 'decimal:8',
        'start_longitude' => 'decimal:8',
        'last_latitude' => 'decimal:8',
        'last_longitude' => 'decimal:8',
        'odo_start' => 'decimal:2',
        'odo_end' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'gas_leftover' => 'decimal:2',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // 🔥 Helper: total additional operational cost
    public function getTotalAdditionalCostAttribute()
    {
        return $this->toll_cost + $this->parking_cost + $this->other_cost;
    }

    // 🔥 Helper: auto calculate fuel used
    public function calculateFuelUsage()
    {
        if ($this->fuel_start_liters && $this->fuel_end_liters) {
            return $this->fuel_start_liters - $this->fuel_end_liters;
        }

        if ($this->distance_traveled_km && $this->fleet?->km_per_liter) {
            return $this->distance_traveled_km / $this->fleet->km_per_liter;
        }

        return null;
    }
}
