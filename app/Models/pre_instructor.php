<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class pre_instructor extends Model
{
    protected $table = 'instructor';

    protected $fillable = ['id','numero_control','nombre','apellidoPaterno','apellidoMaterno','tipo_honorario',
    'rfc','curp','sexo','estado_civil','fecha_nacimiento','entidad','municipio','asentamiento','domicilio','telefono',
    'correo','banco','no_cuenta','interbancaria','folio_ine','status','rechazo','clave_unidad','archivo_ine',
    'archivo_domicilio','archivo_curp', 'archivo_alta','archivo_bancario','archivo_fotografia','archivo_estudios',
    'archivo_otraid','id_especialidad','archivo_rfc','estado','lastUserId','stps','conocer','clave_loc','localidad',
    'tipo_identificacion','expiracion_identificacion','solicito','turnado','nacionalidad','entidad_nacimiento',
    'municipio_nacimiento','localidad_nacimiento','clave_loc_nacimiento','codigo_postal','telefono_casa','curriculum',
    'id_oficial','registro_activo','archivo_curriculum_personal'];

    protected $casts = [
        'unidades_disponible' => 'array',
        'entrevista' => 'array',
        'exp_laboral' => 'array',
        'exp_docente' => 'array',
        'data_perfil' => 'array',
        'data_especialidad' => 'array'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
