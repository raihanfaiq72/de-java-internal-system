<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class COA extends Model
{
    use ActivityLogs, SoftDeletes;

    protected $table = 'chart_of_accounts';

    protected $fillable = ['office_id', 'tipe_id', 'kode_akun', 'nama_akun', 'is_kas_bank', 'bank_name',
        'bank_account_number',
        'bank_account_name',
        'bank_branch',
        'bank_city',
        'currency', ];

    public function type()
    {
        return $this->belongsTo(COAType::class, 'tipe_id');
    }
}
