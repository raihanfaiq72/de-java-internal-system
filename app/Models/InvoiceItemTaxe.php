<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItemTaxe extends Model
{
    use SoftDeletes;
    
    protected $table = 'invoice_item_taxes';

    protected $fillable = [
        'invoice_item_id',
        'tax_id',
        'nilai_pajak_diterapkan',
    ];

    public function invoiceItem()
    {
        return $this->belongsTo(InvoiceItem::class, 'invoice_item_id');
    }
}
