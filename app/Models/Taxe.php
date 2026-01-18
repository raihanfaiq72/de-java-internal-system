<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taxe extends Model
{
    use SoftDeletes;
    
    protected $table = 'taxes';

    protected $fillable = [
        'office_id',
        'nama_pajak',
        'persentase',
        'tipe_pajak',
    ];
}