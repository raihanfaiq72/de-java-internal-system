<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockLocation extends Model
{
    use SoftDeletes;

    protected $table = 'stock_locations';

    protected $fillable = [
        'name',
        'type',
        'is_active',
    ];

    public function mutations()
    {
        return $this->hasMany(StockMutation::class, 'stock_location_id');
    }
}
