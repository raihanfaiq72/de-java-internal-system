<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryOrder extends Model
{
    use ActivityLogs, SoftDeletes;

    protected $table = 'delivery_orders';

    protected $fillable = [
        'office_id',
        'delivery_order_number',
        'delivery_date',
        'status',
        'scheduled_at',
        'departed_at',
        'returned_at',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'scheduled_at' => 'datetime',
        'departed_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function invoices()
    {
        return $this->hasMany(DeliveryOrderInvoice::class);
    }

    public function fleets()
    {
        return $this->hasMany(DeliveryOrderFleet::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
