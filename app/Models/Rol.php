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
        'id',
        'nombre',
        'ruta_corta',
        'description',
        'especial'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'tblz_permiso_rol', 'rol_id', 'permiso_id');
    }

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'tblz_rol_usuario', 'rol_id', 'usuario_id');
    }
}
