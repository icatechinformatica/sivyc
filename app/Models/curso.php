<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class curso extends Model
{
    //
    protected $table = 'cursos';

    protected $fillable = [
            'id','nombre_curso','modalidad','clasificacion','costo','duracion',
            'objetivo','perfil','solicitud_autorizacion','fecha_validacion','memo_validacion',
            'memo_actualizacion','fecha_actualizacion','unidad_amovil','descripcion','no_convenio','id_especialidad',
            'area', 'cambios_especialidad', 'categoria','tipo_curso', 'rango_criterio_pago_minimo',
            'rango_criterio_pago_maximo','estado','observacion','servicio'
    ];

    protected $casts = [
        'unidades_disponible' => 'array',
        'dependencia' => 'array',
        'grupo_vulnerable' => 'array'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * obtener el instructor que pertenece al perfil
     */
    public function curso_validado()
    {
        return $this->hasMany(cursoValidado::class);
    }

    public function area() {
        return $this->belongsTo(Area::class, 'id');
    }

    /**
     * mutator en laravel
     */

    public function setFechaAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d');
    }

    // in your model
    public function getMyDateFormat($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    /***
     * busqueda por parametros
     * scopes
     */
    public function scopeSearchPorCurso($query, $tipo, $buscar)
    {
        if (!empty($tipo)) {
            # entramos y validamos
            if (!empty(trim($buscar))) {
                # empezamos
                switch ($tipo) {
                    case 'especialidad':
                        # code...
                        return $query->where('especialidades.nombre', 'LIKE', "%$buscar%");
                        break;
                    case 'curso':
                        # code...
                        return $query->where( 'cursos.nombre_curso', 'LIKE', "%$buscar%");
                        break;
                    case 'duracion':
                        return $query->where( 'cursos.horas', '=', "$buscar");
                        break;
                    case 'modalidad':
                        return $query->where( 'cursos.modalidad', 'LIKE', "%$buscar%");
                        break;
                    case 'clasificacion':
                        return $query->where( 'cursos.clasificacion', 'LIKE', "%$buscar%");
                        break;
                    case 'anio':
                        return $query->where(\DB::raw("date_part('year' , fecha_validacion )"), '=', "$buscar");
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
    }

    public function especialidadinstructor()
    {
        return $this->belongsToMany(especialidad_instructor::class, 'especialidad_instructor_curso', 'curso_id', 'id_especialidad_instructor');
    }

}
