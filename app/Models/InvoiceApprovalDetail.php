<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceApprovalDetail extends Model
{
    use ActivityLogs, SoftDeletes;

    protected $table = 'invoice_approval_details';

    protected $fillable = [
        'invoice_id',
        'office_id',
        'requested_by',
        'processed_by',
        'status',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
