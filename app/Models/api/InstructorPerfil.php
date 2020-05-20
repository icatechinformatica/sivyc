<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class InstructorPerfil extends Model
{
    // tabla de instructores perfil
    protected $table = "instructor_perfil";

    protected $fillable = [
        'id', 'area_carrera', 'estatus', 'pais_institucion', 'entidad_institucion', 'fecha_expedicion_documento', 'folio_documento',
        'numero_control', 'ciudad_institucion', 'nombre_institucion', 'grado_profesional',
        'experiencia_laboral', 'experiencia_docente', 'cursos_recibidos', 'estandar_conocer',
        'registro_stps', 'capacitador_icatech', 'recibidos_icatech', 'cursos_impartidos'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
