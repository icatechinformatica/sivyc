<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    //
    protected $table = 'tbl_inscripcion';

    protected $fillable = [
        'id','unidad','matricula','alumno','id_curso','curso',
        'instructor', 'inicio', 'termino', 'hinicio', 'hfin', 'tinscripcion', 'abrinscri', 'munidad', 'costo'
        ,'motivo', 'status', 'realizo','folio_grupo','id_pre','id_cerss','fecha_nacimiento','estado_civil','discapacidad',
        'escolaridad','nacionalidad','etnia','indigena','inmigrante','madre_soltera','familia_migrante','calificacion',
        'iduser_created','iduser_updated','activo','id_folio','reexpedicion','sexo','curp', 'empleado', 'asistencias','lgbt','id_gvulnerable'
    ];
    protected $hidden = ['created_at', 'updated_at'];
}
