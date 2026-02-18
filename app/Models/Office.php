<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $fillable = ['name', 'code'];

    public function users()
    {
        return $this->hasMany(UserOfficeRole::class, 'office_id');
    }
}
