<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model
{
    use SoftDeletes;

    protected $table = 'activity_logs';

    protected $fillable = [
        'office_id',
        'user_id',
        'tindakan',
        'tabel_terkait',
        'data_id',
        'data_sebelum',
        'data_sesudah',
        'ip_address',
    ];

    protected $casts = [
        'data_sebelum' => 'array',
        'data_sesudah' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
