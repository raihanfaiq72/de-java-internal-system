<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierBrand extends Model
{
    use SoftDeletes, ActivityLogs;

    protected $table = 'supplier_brands';

    protected $fillable = [
        'office_id',
        'supplier_id',
        'brand_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Partner::class, 'supplier_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}