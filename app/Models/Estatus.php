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
}
