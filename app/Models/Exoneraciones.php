<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exoneraciones extends Model
{

    protected $table = 'exoneraciones';

    protected $fillable = [
        'id', 'id_unidad_capacitacion', 'no_memorandum', 'id_estado', 'id_municipio', 'localidad', 'fecha_memorandum',
        'tipo_exoneracion', 'porcentaje', 'razon_exoneracion', 'grupo_beneficiado', 'observaciones', 'no_convenio',
        'memo_soporte_dependencia', 'status', 'activo'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected function scopeBusqueda($query, $tipo, $buscar)
    {
        if (!empty($tipo)) {
            if (!empty(trim($buscar))) {
                switch ($tipo) {
                    case 'id_unidad_capacitacion':
                        $idUnidad = tbl_unidades::where('unidad', '=', $buscar)->get();
                        return $query->where('exoneraciones.id_unidad_capacitacion', '=', $idUnidad[0]->id);
                        break;
                    case 'no_memorandum':
                        return $query->where('exoneraciones.no_memorandum', '=', $buscar);
                        break;
                    case 'no_convenio':
                        return $query->where('exoneraciones.no_convenio', '=', $buscar);
                        break;
                    case 'tipo_exoneracion':
                        return $query->where('exoneraciones.tipo_exoneracion', '=', $buscar);
                        break;
                }
            }
        }
    }
}
