<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use ActivityLogs, SoftDeletes;

    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'produk_id',
        'nama_produk_manual',
        'deskripsi_produk',
        'qty',
        'tempo',
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
        return $this->hasMany(InvoiceItemTax::class, 'invoice_item_id');
    }
}
