<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalDetail extends Model
{
    use ActivityLogs, SoftDeletes;

    protected $table = 'journal_details';

    protected $fillable = [
        'nomor_journal',
        'journal_id',
        'akun_id',
        'debit',
        'kredit',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }
}
