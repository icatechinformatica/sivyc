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
        'id_usuario_realizo',
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

    public function nacionalidad()
    {
        return $this->belongsTo(Nacionalidad::class, 'id_nacionalidad');
    }

    public function sexo()
    {
        return $this->belongsTo(Sexo::class, 'id_sexo');
    }

    public function estadoCivil()
    {
        return $this->belongsTo(estado_civil::class, 'id_estado_civil');
    }

    // Relación N:M con Estatus
    public function estatus()
    {
        return $this->belongsToMany(Estatus::class, 'tbl_alumno_estatus', 'id_alumno', 'id_estatus');
    }

    public function pais()
    {
        return $this->belongsTo(pais::class, 'id_pais');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }

    public function gradoEstudio()
    {
        return $this->belongsTo(GradoEstudio::class, 'id_grado_estudio');
    }

    public function usuarioRealizo()
    {
        return $this->belongsTo(User::class, 'id_usuario_realizo');
    }
}
