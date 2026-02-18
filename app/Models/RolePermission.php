<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'role_permissions';

    protected $guarded = ['id'];

    protected $fillable = [
        'role_id',
        'permission_id',
    ];

    public $timestamps = true;

    public function role()
    {
        return $this->belongsTo(Roles::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
