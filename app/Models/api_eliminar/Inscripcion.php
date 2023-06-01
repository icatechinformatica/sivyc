<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    //
    protected $table = 'tbl_inscripcion';

    protected $fillable = [
            'id','unidad','matricula','alumno','id_curso','curso',
            'instructor', 'inicio', 'termino', 'hinicio', 'hfin', 'tinscripcion', 'abrinscri', 'munidad', 'costo'
            , 'motivo', 'status', 'realizo'
        ];

    protected $hidden = ['created_at', 'updated_at'];
}
