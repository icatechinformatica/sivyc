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
    'archivo_otraid','id_especialidad','archivo_rfc','estado','lastUserId'];

    protected $casts = [
        'unidades_disponible' => 'array'
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

    public function scopeSearchInstructor($query, $tipo, $buscar)
    {
        if (!empty($tipo)) {
            # entramos y validamos
            if (!empty(trim($buscar))) {
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
                    case 'telefono_instructor':
                        return $query->where( 'telefono', 'LIKE', "%$buscar%");
                        break;
                    case 'estatus_instructor':
                        return $query->where( 'status', '=', ucwords(strtolower($buscar)));
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
    }


}

