<?php

namespace App\Models\ModelPat;

use Illuminate\Database\Eloquent\Model;

class Funciones extends Model
{
    protected $table = 'funciones_proced';

    protected $fillable = ['id', 'id_parent', 'id_org', 'fun_proc', 'json_meta_mes', 'activo', 'iduser_created', 'iduser_updated', 'id_area'];

    protected $hidden = ['created_at', 'updated_at'];

    protected function scopeBusqueda($query, $buscar){
            if (!empty(trim($buscar))) {
                return $query->where('funciones_proced.fun_proc', 'iLIKE', "%$buscar%");
            }
    }
}
