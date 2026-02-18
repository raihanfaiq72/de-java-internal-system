<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prefix extends Model
{
    protected $table = 'prefixes';

    protected $guarded = ['id'];

    protected $fillable = ['name', 'description'];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
