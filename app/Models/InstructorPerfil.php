<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorPerfil extends Model
{
    //
    protected $table = 'instructor_perfil';

    protected $fillable = [
        'id','grado_profesional','area_carrera','estatus','pais_institucion','entidad_institucion',
        'ciudad_institucion','nombre_institucion','fecha_expedicion_documento','folio_documento','cursos_recibidos',
        'estandar_conocer','registro_stps','capacitador_icatech','recibidos_icatech','cursos_impartidos','experiencia_laboral',
        'experiencia_docente','numero_control','lastUserId',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * obtener el instructor que pertenece al perfil
     */
    public function instructor()
    {
        return $this->belongsTo(instructor::class);
    }

    public function contrato()
    {
        return $this->belongsTo(contratos::class);
    }

    public function curso()
    {
        return $this->belongsToMany(curso::class, 'perfil_cursos');
    }

}
