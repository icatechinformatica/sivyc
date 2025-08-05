<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoVulnerable extends Model
{
    use HasFactory;

    protected $table = 'tbl_aux_grupo_vulnerable';
    protected $primaryKey = 'id_grupo_vulnerable';
    
    public $timestamps = false;

    protected $fillable = [
        'grupo_vulnerable'
    ];

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'tbl_alumno_grupo_vulnerable', 'grupo_vulnerable_id', 'alumno_id');
    }
}
