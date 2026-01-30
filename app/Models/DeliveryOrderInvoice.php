<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderInvoice extends Model
{
    use ActivityLogs;

    protected $fillable = [
        'delivery_order_id',
        'invoice_id',
        'delivery_sequence',
        'delivery_status',
        'arrived_at',
        'delivery_notes',
        'proof_photo',
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}