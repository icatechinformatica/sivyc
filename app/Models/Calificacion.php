<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    //
    protected $table = 'tbl_calificaciones';

    protected $fillable = [
        'id','unidad', 'matricula', 'acreditado', 'noacreditado', 'idcurso', 'idgrupo', 'area', 'espe', 'alumno',
        'curso', 'mod', 'instructor', 'inicio', 'termino', 'hini', 'hfin', 'dura', 'ciclo', 'periodo', 'calificacion', 'valido', 'realizo'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
