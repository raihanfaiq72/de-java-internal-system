<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes, ActivityLogs;

    protected $table = 'brands';

    protected $fillable = [
        'office_id',
        'nama_brand',
    ];

    public function suppliers()
    {
        return $this->belongsToMany(
            Partner::class,
            'supplier_brands',
            'brand_id',
            'supplier_id'
        )
            ->withPivot(['id', 'office_id', 'deleted_at'])
            ->withTimestamps()
            ->wherePivotNull('deleted_at');
    }
}