<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BulkReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_name',
        'slug',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bulkReport) {
            $bulkReport->slug = $bulkReport->generateSlug();
        });

        static::updating(function ($bulkReport) {
            $bulkReport->slug = $bulkReport->generateSlug();
        });
    }

    public function generateSlug()
    {
        $baseSlug = Str::slug($this->period_name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getStatusAttribute($value)
    {
        return ucfirst($value);
    }
}
