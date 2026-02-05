<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use SoftDeletes, ActivityLogs;

    protected $table = 'mitras';

    protected $fillable = [
        'office_id',
        'nomor_mitra',
        'badan_usaha',
        'nama',
        'no_hp',
        'email',
        'ktp_npwp',
        'tipe_mitra',
        'is_cash_customer',
        'payment_terms',
        'identity_card',
        'alamat',
        'kontak_nama',
        'kontak_jabatan',
        'kontak_no_hp',
        'kontak_email',
        'akun_hutang_id',
        'akun_piutang_id',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_cash_customer' => 'boolean',
    ];

    public function akunHutang()
    {
        return $this->belongsTo(COA::class, 'akun_hutang_id');
    }

    public function akunPiutang()
    {
        return $this->belongsTo(COA::class, 'akun_piutang_id');
    }

    public function brands()
    {
        return $this->belongsToMany(
            Brand::class,
            'supplier_brands',
            'supplier_id',
            'brand_id'
        )
            ->withPivot(['id', 'office_id', 'deleted_at'])
            ->withTimestamps()
            ->wherePivotNull('deleted_at');
    }
}
