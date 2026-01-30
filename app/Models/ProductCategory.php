<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use SoftDeletes, ActivityLogs;

    protected $table = 'product_categories';

    protected $fillable = [
        'office_id',
        'nama_kategori',
        'deskripsi',
    ];
}