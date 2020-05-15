<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    //
    protected $table = 'tbl_inscripcion';

    protected $fillable = [
            'id','unidad','matricula','nombre','id_curso','curso',
            'instructor', 'inicio', 'termino', 'hinicio', 'hfin', 'tinscripcion', 'abrinscri',
            'hini2', 'hfin2', 'munidad', 'costo'
        ];

    protected $hidden = ['created_at', 'updated_at'];
}
