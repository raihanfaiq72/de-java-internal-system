<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalDetail extends Model
{
    use SoftDeletes, ActivityLogs;
    
    protected $table = 'journal_details';

    protected $fillable = [
        'journal_id',
        'akun_id',
        'debit',
        'kredit',
    ];
}