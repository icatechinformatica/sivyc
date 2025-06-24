<?php

namespace App\Models;

use App\Models\Rol;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    //
    protected $table = 'tblz_permisos';

    protected $fillable = [
        'id',
        'nombre',
        'ruta_corta',
        'descripcion',
        'icono',
        'activo',
        'clave_orden'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'tblz_permiso_rol', 'permiso_id', 'rol_id');
    }

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'tblz_permiso_usuario', 'permiso_id', 'usuario_id');
    }

    public function esMenu()
    {
        return $this->menu ?? false;
    }

    public function scopeMenus($query)
    {
        return $query->where('menu', true);
    }
}
