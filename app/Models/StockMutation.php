<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class StockMutation extends Model
{
    use SoftDeletes;

    protected $table = 'stock_mutations';

    protected $fillable = [
        'product_id',
        'type',
        'qty',
        'remaining_qty',
        'cost_price',
        'reference_type',
        'reference_id',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
