<?php

namespace App\Models\ModelPat;

use Illuminate\Database\Eloquent\Model;

class FechasPat extends Model
{
    protected $table = 'fechas_pat';

    protected $fillable = ['id', 'id_org', 'nombre_org', 'periodo', 'status', 'iduser_created', 'iduser_updated'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = ['fecha_meta' => 'json', 'fechas_avance' => 'json', 'status_meta' => 'json', 'status_avance' => 'json'];

    protected function scopeBusqueda($query, $buscar){
            if (!empty(trim($buscar))) {
                return $query->where('fechas_pat.fun_proc', 'iLIKE', "%$buscar%");
            }
    }
}
