<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'roles';
    protected $guarded = ['id'];
    protected $fillable = ['name','description'];

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',  
            'role_id',           
            'permission_id'      
        );
    }

}
