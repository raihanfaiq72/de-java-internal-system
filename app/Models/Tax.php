<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use SoftDeletes, ActivityLogs;

    protected $table = 'taxes';

    protected $fillable = [
        'office_id',
        'nama_pajak',
        'persentase',
        'tipe_pajak',
    ];
}