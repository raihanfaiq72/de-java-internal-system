<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockLocation extends Model
{
    use SoftDeletes, ActivityLogs;

    protected $table = 'stock_locations';

    protected $fillable = [
        'office_id',
        'name',
        'type',
        'is_active',
    ];

    public function mutations()
    {
        return $this->hasMany(StockMutation::class, 'stock_location_id');
    }
}