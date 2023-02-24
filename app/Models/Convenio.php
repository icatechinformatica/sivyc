<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Convenio extends Model
{
    // tabla de convenios
    protected $table = 'convenios';

    protected $fillable = [
        'id', 'no_convenio', 'tipo_sector', 'institucion', 'fecha_firma', 'fecha_vigencia',
        'archivo_convenio', 'tipo_convenio', 'poblacion', 'municipio', 'nombre_titular', 'nombre_firma', 'nombre_enlace',
        'telefono_enlace', 'direccion', 'telefono', 'status',
        'activo', 'sector', 'unidades', 'correo_institucion', 'correo_enlace', 'id_estado', 'id_organismo'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * mutator en laravel
     */

    // in your model
    public function getMyDateFormat($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    protected function scopeBusqueda($query, $tipo, $datos){
        $buscar = $datos['campo_buscar'];
        $fecha1 = $datos['fecha1'];
        $fecha2 = $datos['fecha2'];
        // dd($buscar);
        if (!empty($tipo)) {
            if (!empty(trim($buscar))) {
                switch ($tipo) {
                    case 'no_convenio':
                        return $query->where('convenios.no_convenio', 'LIKE', "%$buscar%");
                        break;
                    case 'institucion':
                        return $query->where('convenios.institucion', 'LIKE', "%$buscar%");
                        break;
                    case 'tipo_convenio':
                        return $query->where('convenios.tipo_convenio', 'LIKE', "%$buscar%");
                        break;
                    case 'sector':
                        return $query->where('convenios.sector', 'LIKE', "%$buscar%");
                        break;
                    case 'dependencia':
                        return $query->where('convenios.institucion', 'LIKE', "%$buscar%");
                        break;
                }
            }else if(!empty(trim($fecha1)) && !empty(trim($fecha2))){
                return $query->whereBetween('convenios.updated_at', [$fecha1, $fecha2]);
            }

        }

    }

}
