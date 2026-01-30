<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class COAGroup extends Model
{
    use SoftDeletes, ActivityLogs;

    protected $table = 'coa_group';

    protected $fillable = ['office_id', 'kode_kelompok', 'nama_kelompok'];

    public function type()
    {
        return $this->hasMany(CoaType::class, 'kelompok_id');
    }
}