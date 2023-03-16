<?php

namespace App\Models\ModelPat;

use Illuminate\Database\Eloquent\Model;

class Procedimientos extends Model
{
    protected $table = 'funciones_proced';

    protected $fillable = ['id', 'id_parent', 'id_org', 'fun_proc', 'json_meta_mes', 'activo', 'iduser_created', 'iduser_updated', 'id_area'];

    protected $hidden = ['created_at', 'updated_at'];
}
