<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class COA extends Model
{
    use SoftDeletes;

    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'office_id',
        'kode_akun',
        'nama_akun',
        'kelompok_akun',
        'is_kas_bank',
    ];
}