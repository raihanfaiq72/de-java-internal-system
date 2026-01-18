<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mitra extends Model
{
    use SoftDeletes;

    protected $table = 'mitras';

    protected $fillable = [
        'office_id',
        'nomor_mitra',
        'badan_usaha',
        'nama',
        'no_hp',
        'email',
        'tipe_mitra',
        'alamat',
        'kontak_nama',
        'kontak_jabatan',
        'kontak_no_hp',
        'kontak_email',
        'akun_hutang_id',
        'akun_piutang_id',
    ];

    public function akunHutang()
    {
        return $this->belongsTo(COA::class, 'akun_hutang_id');
    }

    public function akunPiutang()
    {
        return $this->belongsTo(COA::class, 'akun_piutang_id');
    }
}