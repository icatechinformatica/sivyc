<?php

namespace App\Models;
use App\User;
use App\Models\Permission;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    //
    protected $table = 'tblz_roles';

    protected $fillable = [
        'id','name','slug', 'description', 'special'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }
}
