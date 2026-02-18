<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use ActivityLogs, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'office_id',
        'sku_kode',
        'nama_produk',
        'product_category_id',
        'supplier_id',
        'brand_id',
        'kemasan',
        'satuan',
        'harga_beli',
        'harga_jual',
        'track_stock',
        'qty',
        'foto_produk',
        'coa_id',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function stock_mutations()
    {
        return $this->hasMany(StockMutation::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Partner::class, 'supplier_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
