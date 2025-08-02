<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoVulnerable extends Model
{
    use HasFactory;

    protected $table = 'grupos_vulnerables';

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'tbl_alumno_grupo_vulnerable', 'grupo_vulnerable_id', 'alumno_id');
    }
}
