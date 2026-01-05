<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use SoftDeletes;
    
    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'produk_id',
        'nama_produk_manual',
        'deskripsi_produk',
        'qty',
        'harga_satuan',
        'diskon_nilai',
        'total_harga_item',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'produk_id');
    }

    public function taxes()
    {
        return $this->hasMany(InvoiceItemTaxe::class, 'invoice_item_id');
    }
}
