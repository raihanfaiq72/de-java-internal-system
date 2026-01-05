<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitCategorie extends Model
{
    use SoftDeletes;
    
    protected $table = 'unit_categories';

    protected $fillable = [
        'nama_kategori',
        'konversi_nilai',
    ];

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
