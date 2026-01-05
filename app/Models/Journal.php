<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    use SoftDeletes;
    
    protected $table = 'journals';

    protected $fillable = [
        'tgl_jurnal',
        'nomor_referensi',
        'keterangan',
    ];

    public function details()
    {
        return $this->hasMany(JournalDetail::class);
    }
}
