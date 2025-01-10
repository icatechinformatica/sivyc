<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class instructor extends Model
{
    protected $table = 'instructores';

    protected $fillable = ['id','numero_control','nombre','apellidoPaterno','apellidoMaterno','tipo_honorario',
    'rfc','curp','sexo','estado_civil','fecha_nacimiento','entidad','municipio','asentamiento','domicilio','telefono',
    'correo','banco','no_cuenta','interbancaria','folio_ine','status','rechazo','clave_unidad','archivo_ine',
    'archivo_domicilio','archivo_curp', 'archivo_alta','archivo_bancario','archivo_fotografia','archivo_estudios',
    'archivo_otraid','id_especialidad','archivo_rfc','estado','lastUserId','stps','conocer','clave_loc','localidad',
    'tipo_identificacion','expiracion_identificacion','solicito','turnado','nacionalidad','entidad_nacimiento',
    'municipio_nacimiento','localidad_nacimiento','clave_loc_nacimiento','codigo_postal','telefono_casa','curriculum',
    'archivo_curriculum_personal','tipo_instructor','curso_extra','instructor_alfa','datos_alfa'];

    protected $casts = [
        'unidades_disponible' => 'array',
        'entrevista' => 'array',
        'exp_laboral' => 'array',
        'exp_docente' => 'array',
        'datos_alfa' => 'array'
    ];

    protected $hidden = ['created_at', 'updated_at'];

            /**
     * método slug
     */
    protected function getSlugAttribute($value): string {
        return Str::slug($value, '-');
    }

    /**
     * obtener los perfiles del instructor
     */
    public function perfil()
    {
        return $this->hasMany(InstructorPerfil::class);
    }

    function curso_Validado()
    {
        return $this->hasMany(cursoValidado::class);
    }

    public function setFechaNacAttribute($value) {
        return $this->attributes['fecha_nacimiento'] = Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
    }

    public function soportesInstructor() {
        return [
            'banco' => $this->banco,
            'domicilio' => $this->domicilio,
            'no_cuenta' => $this->no_cuenta,
            'archivo_ine' => $this->archivo_ine,
            'archivo_rfc' => $this->archivo_rfc,
            'interbancaria' => $this->interbancaria,
            'tipo_honorario' => $this->tipo_honorario,
            'archivo_bancario' => $this->archivo_bancario,
            'archivo_domicilio' => $this->archivo_domicilio,
            'tipo_identificacion'  => $this->tipo_identificacion,
            'folio_identificacion' => $this->folio_ine
        ];
    }


    public function scopeSearchInstructor($query, $tipo, $buscar, $tipo_status, $tipo_especialidad)
    {
        $query->GROUPBY('instructores.id','instructores.nombre');
        $query->ORDERBY('status','DESC');
        $query->ORDERBY('fecha_validacion','ASC');

        if (!empty($tipo)){
            # entramos y validamos
            if (!empty(trim($buscar))){
                # empezamos
                switch ($tipo) {
                    case 'clave_instructor':
                        # code...
                        return $query->where('numero_control', '=', $buscar);
                        break;
                    case 'nombre_instructor':
                        # code...
                        return $query->where( \DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',nombre)'), 'LIKE', "%$buscar%");
                        break;
                    case 'curp':
                            # code...
                            return $query->where('curp', '=', $buscar);
                            break;
                    case 'telefono_instructor':
                        return $query->where( 'telefono', 'LIKE', "%$buscar%");
                        break;
                    default:
                        # code...
                        break;
                }
            }
            if(!empty($tipo_status))
            {
                return $query->where( 'status', '=', $tipo_status);
            }
            if(!empty($tipo_especialidad))
            {
                return $query->where( 'especialidad_instructores.especialidad_id', $tipo_especialidad)->WHERE('especialidad_instructores.status','VALIDADO');
            }
        }
    }

    public function scopeSearchNrevision()
    {

    }


}

