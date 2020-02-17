<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class instructor extends Model
{
    protected $fillable = ['curriculum', 'certificado_estudios', 'constancia_cursos', 'acta_nacimiento', 'ine', 'comprobante_domicilio',
    'constancia_agente', 'seleccion_firmada', 'formato_entrevista', 'curp', 'nombre', 'apellido_paterno', 'apellido_materno', 'correo', 'especialidad', 'observacion'];
}

