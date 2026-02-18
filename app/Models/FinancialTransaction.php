<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialTransaction extends Model
{
    use ActivityLogs, SoftDeletes;

    protected $table = 'financial_transactions';

    protected $fillable = [
        'office_id',
        'transaction_date',
        'type',
        'from_account_id',
        'to_account_id',
        'amount',
        'status',
        'description',
        'reference_number',
        'lampiran',
    ];

    public function fromAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'to_account_id');
    }
}
