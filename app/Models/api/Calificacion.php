<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    // creación del modelo
    protected $table = 'tbl_calificaciones';

    protected $fillable = [
        'id','unidad', 'matricula', 'acreditado', 'noacreditado', 'idcurso', 'idgrupo', 'area', 'espe', 'alumno',
        'curso', 'mod', 'instructor', 'inicio', 'termino', 'hini', 'hfin', 'dura', 'ciclo', 'periodo', 'calificacion', 'valido'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function curso() {
        return $this->belongsTo(Curso::class, 'id');
    }
}
