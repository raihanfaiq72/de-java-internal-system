<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'office_id',
        'sku_kode',
        'nama_produk',
        'product_category_id',
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
}