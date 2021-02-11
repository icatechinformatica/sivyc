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
        'activo', 'sector', 'unidades', 'correo_institucion', 'correo_enlace', 'id_estado'
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

    protected function scopeBusqueda($query, $tipo, $buscar){
        // dd($buscar);
        if (!empty($tipo)) {
            if (!empty(trim($buscar))) {
                switch ($tipo) {
                    case 'no_convenio':
                        return $query->where('convenios.no_convenio', '=', $buscar);
                        break;
                    case 'institucion':
                        return $query->where('convenios.institucion', '=', $buscar);
                        break;
                    case 'tipo_convenio':
                        return $query->where('convenios.tipo_convenio', '=', $buscar);
                        break;
                    case 'sector':
                        return $query->where('convenios.sector', '=', $buscar);
                        break;
                }
            }
        }
    }
}
