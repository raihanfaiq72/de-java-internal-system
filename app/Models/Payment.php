<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes, ActivityLogs;

    protected $table = 'payments';

    protected $fillable = [
        'office_id',
        'invoice_id',
        'nomor_pembayaran',
        'ref_no',
        'tgl_pembayaran',
        'metode_pembayaran',
        'jumlah_bayar',
        'akun_keuangan_id',
        'catatan',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function akun_keuangan()
    {
        return $this->belongsTo(FinancialAccount::class, 'akun_keuangan_id');
    }
}