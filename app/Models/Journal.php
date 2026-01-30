<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    use SoftDeletes, ActivityLogs;
    
    protected $table = 'journals';

    protected $fillable = [
        'office_id',
        'tgl_jurnal',
        'nomor_referensi',
        'keterangan',
    ];

    public function details()
    {
        return $this->hasMany(JournalDetail::class);
    }
}