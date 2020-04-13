<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class CatalogoCurso extends Model
{
    // generamos el modelo
    protected $table = 'cursos';

    protected $fillable = [
        'id','especialidad','nombre_curso','modalidad','horas','clasificacion','costo','duracion',
        'objetivo','perfil','solicitud_autorizacion','fecha_validacion','memo_validacion','memo_actualizacion','fecha_actualizacion',
        'unidad_amovil','descripcion','no_convenio','id_especialidad'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
