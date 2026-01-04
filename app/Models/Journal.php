<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
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
