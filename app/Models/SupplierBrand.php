<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierBrand extends Model
{
    use SoftDeletes;

    protected $table = 'supplier_brands';

    protected $fillable = [
        'office_id',
        'supplier_id',
        'brand_id',
    ];
}