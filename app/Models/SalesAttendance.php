<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesAttendance extends Model
{
    use SoftDeletes;
    
    protected $table = 'sales_attendances';

    protected $fillable = [
        'user_id',
        'tgl_presensi',
        'jam_masuk',
        'jam_keluar',
        'lokasi_gps',
        'keterangan',
    ];
}
