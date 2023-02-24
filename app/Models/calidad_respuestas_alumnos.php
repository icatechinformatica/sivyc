<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class calidad_respuestas_alumnos extends Model
{
    //
    protected $table = 'calidad_respuestas_alumnos';

    protected $fillable = [
        'id','id_inscripcion','matricula','nombre','id_tbl_cursos','id_curso','id_encuesta'
    ];

    protected $casts = [
        'respuestas' => 'array'
    ];

    protected $hidden = ['created_at', 'updated_at'];

}
