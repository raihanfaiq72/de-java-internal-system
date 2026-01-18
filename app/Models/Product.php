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
        'deskripsi_produk',
        'product_category_id',
        'unit_category_id',
        'unit_id',
        'harga_beli',
        'harga_jual',
        'track_stock',
        'qty',
        'foto_produk',
        'akun_penjualan_id',
        'akun_pembelian_id',
        'akun_diskon_penjualan_id',
        'akun_diskon_pembelian_id',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategorie::class, 'product_category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function stock_mutations()
    {
        return $this->hasMany(StockMutation::class);
    }
}