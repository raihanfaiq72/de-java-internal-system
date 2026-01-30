<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesAttendance extends Model
{
    use SoftDeletes, ActivityLogs;
    
    protected $table = 'sales_attendances';

    protected $fillable = [
        'office_id',
        'user_id',
        'tgl_presensi',
        'jam_masuk',
        'jam_keluar',
        'lokasi_gps',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}