<?php

namespace App\Models;

use App\Traits\ActivityLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialAccount extends Model
{
    use ActivityLogs, SoftDeletes;

    protected $guarded = ['id'];

    // Additional properties if needed
}
