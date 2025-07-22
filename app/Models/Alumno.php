<?php

namespace App\Models;

use App\Models\GrupoVulnerable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    //
    protected $table = 'tbl_alumnos';

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'curp',
        'matricula',
        'fecha_nacimiento',
        'correo',
        'telefono_celular',
        'telefono_casa',
        'domicilio',
        'colonia',
        'cp',
        'clave_localidad',
        'facebook',
        'empresa_trabaja',
        'antiguedad',
        'direccion_empresa',
        'sistema_capacitacion_especificar',
        'medio_entero',
        'medio_confirmacion',
        'archivos_documentos',
        'cerss',
        'vulnerable',
        'datos_alfa',
        'datos_incorporacion',
        'movimientos',
        'recibir_publicaciones',
        'empleado',
        'curso_extra',
        'servidor_publico',
        'check_bolsa',
        'esta_activo',
        'id_sexo',
        'id_municipio',
        'id_estado',
        'id_estado_civil',
        'id_discapacidad',
        'id_ultimo_grado_estudios',
        'id_nacionalidad',
        'id_funcionario_realizo',
    ];

    public $timestamps = false;

    public function alumnospre()
    {
        return $this->belongsTo(Alumnopre::class, 'id');
    }

    public function gruposVulnerables()
    {
        return $this->belongsToMany(GrupoVulnerable::class, 'tbl_alumno_grupo_vulnerable
        ', 'alumno_id', 'grupo_vulnerable_id');
    }
    
}
