<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
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
}
