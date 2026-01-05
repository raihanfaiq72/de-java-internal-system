<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;
    
    protected $table = 'units';

    protected $fillable = [
        'unit_category_id',
        'nama_unit',
        'simbol',
        'keterangan',
    ];

    public function category()
    {
        return $this->belongsTo(UnitCategorie::class, 'unit_category_id');
    }
}
