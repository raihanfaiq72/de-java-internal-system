<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItemTaxe extends Model
{
    protected $table = 'invoice_item_taxes';

    protected $fillable = [
        'invoice_item_id',
        'tax_id',
        'nilai_pajak_diterapkan',
    ];
}
