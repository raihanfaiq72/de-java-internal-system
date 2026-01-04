<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitCategorie extends Model
{
    protected $table = 'unit_categories';

    protected $fillable = [
        'nama_kategori',
        'konversi_nilai',
    ];
}
