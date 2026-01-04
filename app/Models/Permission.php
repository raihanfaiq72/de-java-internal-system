<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $guarded = ['id'];
    protected $fillable = ['prefix_id','action','name','description'];

    public function roles() {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public function prefix() {
        return $this->belongsTo(Prefix::class);
    }
}
