<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermisosRol extends Model
{
    // permission rol
    protected $table = 'permission_role';

    protected $fillable = [
        'id', 'permission_id', 'role_id'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
