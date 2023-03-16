<?php

namespace App\Models\ModelPat;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    protected $table = 'unidades_medida';

    protected $fillable = ['id','numero', 'unidadm', 'tipo_unidadm', 'status', 'iduser_created', 'iduser_updated'];

    protected $hidden = ['created_at', 'updated_at'];

    protected function scopeBusqueda($query, $buscar){
            if (!empty(trim($buscar))) {
                return $query->where('unidades_medida.unidadm', 'LIKE', "%$buscar%");
            }
    }
}
