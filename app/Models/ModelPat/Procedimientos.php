<?php

namespace App\Models\ModelPat;

use Illuminate\Database\Eloquent\Model;

class Procedimientos extends Model
{
    protected $table = 'funciones_proced';

    protected $fillable = ['id', 'id_parent', 'id_org', 'id_unidadm', 'orden', 'fun_proc', 'activo', 'iduser_created', 'iduser_updated'];

    protected $hidden = ['created_at', 'updated_at'];
}
