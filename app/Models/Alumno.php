<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    //
    protected $table = 'alumnos_registro';

    protected $fillable = ['no_control','fecha','numero_solicitud','sexo','curp', 'fecha_nacimiento', 'domicilio', 'colonia',
    'codigo_postal', 'municipio', 'estado', 'estado_civil', 'discapacidad_presente', 'ultimo_grado_estudios', 'empresa_trabaja', 'antiguedad',
    'direccion_empresa', 'cerrs', 'etnia', 'indigena', 'migrante', 'id_pre'];

    protected $hidden = ['created_at', 'updated_at'];

    public function alumnospre() {
        return $this->belongsTo(Alumnopre::class, 'id');
    }
}
