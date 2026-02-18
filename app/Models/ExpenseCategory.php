<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use ActivityLogs, SoftDeletes;

    protected $table = 'expense_categories';

    protected $fillable = [
        'office_id',
        'name',
    ];
}
