<?php

namespace App\Models;
use App\Models\Rol;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    //
    protected $table = 'permissions';

    protected $fillable = [
        'id', 'name', 'slug', 'description',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'permission_role', 'permission_id', 'role_id');
    }
}
