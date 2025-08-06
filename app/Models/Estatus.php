<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estatus extends Model
{
    use HasFactory;

    protected $table = 'tbl_aux_estatus';

    protected $fillable = [
        'estatus'
    ];

    // Relación N:M con Alumno
    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'tbl_alumno_estatus', 'id_estatus', 'id_alumno');
    }

    // Relación N:M con Grupo
    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'tbl_grupo_estatus', 'id_estatus', 'id_grupo');
    }
}
