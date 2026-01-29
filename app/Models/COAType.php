<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class COAType extends Model
{
    use SoftDeletes;

    protected $table = 'coa_type';

    protected $fillable = ['kelompok_id', 'nama_tipe'];

    public function group()
    {
        return $this->belongsTo(Coagroup::class, 'kelompok_id');
    }

    public function coas()
    {
        return $this->hasMany(COA::class, 'tipe_id');
    }
}