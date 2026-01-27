<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $table = 'expenses';

    protected $fillable = [
        'office_id',
        'akun_keuangan_id',
        'nama_biaya',
        'nama_vendor',
        'akun_beban_id',
        'tgl_biaya',
        'kategori_biaya',
        'jumlah',
        'keterangan',
        'lampiran',
    ];

    public function akunKeuangan()
    {
        return $this->belongsTo(FinancialAccount::class, 'akun_keuangan_id');
    }

    public function akunBeban()
    {
        return $this->belongsTo(COA::class, 'akun_beban_id');
    }
}
