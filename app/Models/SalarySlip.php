<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySlip extends Model
{
    protected $fillable = [
        'salary_period_id',
        'employee_id',
        'basic_salary',
        'premi',
        'meal_allowance',
        'late_deduction',
        'other_deduction',
        'total_salary',
        'details',
        'status',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'premi' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'late_deduction' => 'decimal:2',
        'other_deduction' => 'decimal:2',
        'total_salary' => 'decimal:2',
        'details' => 'array',
    ];

    public function salaryPeriod()
    {
        return $this->belongsTo(SalaryPeriod::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
