<?php

namespace App\Models\ModelExpe;

use Illuminate\Database\Eloquent\Model;

class ExpeUnico extends Model
{
    protected $table = 'tbl_cursos_expedientes';

    protected $fillable = ['id', 'id_curso', 'folio_grupo', 'iduser_created', 'iduser_updated'];

    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = ['vinculacion' => 'json', 'academico' => 'json', 'administrativo' => 'json', 'sop_constancias' => 'json', 'movimientos' => 'json'];

    protected function scopeBusqueda($query, $buscar){
    if (!empty(trim($buscar))) {
        return $query->where('tbl_cursos_expedientes.folio_grupo', 'iLIKE', "%$buscar%");
    }
}
}
