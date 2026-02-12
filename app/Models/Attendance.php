<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime', // or 'timestamp' depending on format, but 'datetime' is safer if storing Y-m-d H:i:s or just H:i:s as string
        'clock_out' => 'datetime',
    ];

    // But wait, migration used 'time' column type.
    // Laravel casts 'time' usually as string or custom cast.
    // Let's stick to string for time or use 'datetime' if we want Carbon instances (but date part will be mock).
    // Better to use 'string' for time columns if they are just H:i:s.
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
