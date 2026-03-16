<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_name',
        'start_date',
        'end_date',
        'month',
        'year',
        'status',
        'generated_by',
        'file_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'generated_at' => 'datetime',
    ];

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getStatusAttribute($value)
    {
        return ucfirst($value);
    }
}
