<?php

namespace App\Models;

use Icatech\PermisoRolMenu\Models\Permiso;

class PermisoExt extends Permiso
{
    public function estatus()
    {
        return $this->belongsToMany(Estatus::class, 'tbl_estatus_permiso', 'permiso_id', 'estatus_id');
    }
}
