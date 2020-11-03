<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class calidad_respuestas extends Model
{
    //
    protected $table = 'calidad_respuestas';

    protected $fillable = [
        'id','id_encuesta','id_tbl_cursos','id_curso','id_instructor','unidad','fecha_aplicacion'
    ];

    protected $casts = [
        'respuestas' => 'array'
    ];

    protected $hidden = ['created_at', 'updated_at'];

}
