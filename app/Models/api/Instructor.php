<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    //
    protected $table = 'instructores';

    protected $fillable = [
        'id', 'numero_control', 'nombre', 'apellidoPaterno', 'apellidoMaterno', 'cursos_recibidos', 'capacitados_icatech', 'curso_recibido_icatech',
        'cursos_impartidos', 'rfc', 'curp', 'sexo', 'estado_civil', 'fecha_nacimiento', 'entidad', 'municipio',
        'asentamiento', 'domicilio', 'telefono', 'correo', 'observaciones', 'cursos_conocer', 'banco', 'no_cuenta',
        'interbancaria', 'folio_ine', 'archivo_cv', 'created_at', 'updated_at', 'id_especialidad', 'status', 'rechazo', 'clave_unidad'
        ];



    protected $hidden = ['created_at', 'updated_at'];
}
