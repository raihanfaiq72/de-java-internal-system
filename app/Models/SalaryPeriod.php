<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryPeriod extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'status',
        'name',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function salarySlips()
    {
        return $this->hasMany(SalarySlip::class);
    }
}
