<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesAttendance extends Model
{
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
