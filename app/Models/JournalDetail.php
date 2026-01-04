<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalDetail extends Model
{
    protected $table = 'journal_details';

    protected $fillable = [
        'journal_id',
        'akun_id',
        'debit',
        'kredit',
    ];
}
