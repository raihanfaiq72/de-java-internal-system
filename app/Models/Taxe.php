<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxe extends Model
{
    protected $table = 'taxes';

    protected $fillable = [
        'nama_pajak',
        'persentase',
        'tipe_pajak',
    ];
}
