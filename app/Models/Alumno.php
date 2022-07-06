<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    //
    protected $table = 'alumnos_registro';

    protected $fillable = [
        'no_control',
        'fecha',
        'numero_solicitud',
        'id_pre',
        'id_especialidad',
        'id_curso',
        'horario',
        'grupo',
        'unidad',
        'tipo_curso',
        'realizo',
        'cerrs',
        'etnia',
        'estatus_modificacion',
        'costo',
        'tinscripcion'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function alumnospre() {
        return $this->belongsTo(Alumnopre::class, 'id');
    }

    // in your model
    public function getMyDateFormat($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    // scopes
    public function scopeBusqueda($query, $tipo, $buscar)
    {
        if (!empty($tipo)) {
            # entramos y validamos
            if (!empty(trim($buscar))) {
                # empezamos
                switch ($tipo) {
                    case 'no_control':
                        # code...
                        return $query->where('alumnos_registro.no_control', '=', $buscar);
                        break;
                    case 'folio_grupo':
                        return $query->where('alumnos_registro.folio_grupo', '=', $buscar);
                        break;
                    case 'nombres':
                        # code...
                        return $query->where( \DB::raw("CONCAT(alumnos_pre.apellido_paterno, ' ',alumnos_pre.apellido_materno,' ',alumnos_pre.nombre)"), 'LIKE', "%$buscar%");
                        break;
                    case 'curso':
                        # code...
                        return $query->where('cursos.nombre_curso', 'LIKE', "%$buscar%");
                        break;
                    case 'curp':
                        return $query->where('alumnos_pre.curp', '=', $buscar);
                        break;
                    case 'no_control_busqueda':
                        return $query->where('alumnos_registro.no_control', 'LIKE', "%$buscar%");
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
    }
}
