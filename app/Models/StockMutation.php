<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class StockMutation extends Model
{
    use SoftDeletes, ActivityLogs;

    protected $table = 'stock_mutations';

    protected $fillable = [
        'office_id',
        'product_id',
        'stock_location_id',
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

    public function location()
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }

    public function stock_location()
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }
}