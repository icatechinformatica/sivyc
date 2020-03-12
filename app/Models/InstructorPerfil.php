<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorPerfil extends Model
{
    //
    protected $table = 'instructor_perfil';

    protected $fillable = [
        'id', 'especialidad', 'clave_especialidad', 'validado_unicamente_impartir', 'perfil_profesional', 'area_carrera', 'carrera',
        'estatus', 'pais_institucion', 'entidad_institucion', 'fecha_expedicion_documento', 'folio_documento', 'numero_control'
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

}
