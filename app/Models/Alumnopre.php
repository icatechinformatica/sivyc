<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Alumnopre extends Model
{
    //
    protected $table = 'alumnos_pre';

    protected $fillable = [
        'id',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'telefono',
        'curp',
        'sexo',
        'fecha_nacimiento',
        'domicilio',
        'colonia',
        'cp',
        'municipio',
        'estado',
        'estado_civil',
        'discapacidad',
        'ultimo_grado_estudios',
        'empresa_trabaja',
        'antiguedad',
        'direccion_empresa',
        'medio_entero',
        'chk_acta_nacimiento',
        'chk_curp',
        'chk_comprobante_domicilio',
        'chk_fotografia',
        'chk_ine',
        'chk_pasaporte_licencia',
        'chk_comprobante_ultimo_grado',
        'chk_comprobante_calidad_migratoria',
        'acta_nacimiento',
        'documento_curp',
        'comprobante_domicilio',
        'fotografia',
        'ine',
        'pasaporte_licencia_manejo',
        'comprobante_ultimo_grado',
        'comprobante_calidad_migratoria',
        'puesto_empresa',
        'sistema_capacitacion_especificar',
        'realizo',
        'tiene_documentacion',
        'es_cereso',
        'titular_cerss',
        'permiso_exoneracion'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_pre');
    }

    /**
     * mutator en laravel
     */

     public function setFechaNacAttribute($value) {
        return $this->attributes['fecha_nacimiento'] = Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
     }

     // scopes
    public function scopeBusquedaPor($query, $tipo, $buscar)
    {
        if (!empty($tipo)) {
            # entramos y validamos
            if (!empty(trim($buscar))) {
                # empezamos
                switch ($tipo) {
                    case 'matricula_aspirante':
                        # code...
                        return $query->where('matricula', '=', $buscar);
                        break;
                    case 'curp_aspirante':
                        # code...
                        return $query->where('curp', '=', $buscar);
                        break;
                    case 'nombre_aspirante':
                        # code...
                        return $query->where( \DB::raw("CONCAT(apellido_paterno, ' ',apellido_materno,' ',nombre)"), 'LIKE', "%$buscar%");
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
    }
}
