<?php

namespace App\Models\Vistas;

use Illuminate\Database\Eloquent\Model;

class CursoView extends Model
{
    protected $table = 'vista_cursos';
    protected $primaryKey = 'id_curso';


    public function grupos()
    {
        return $this->hasMany(\App\Models\Grupo::class, 'id_curso', 'id_curso');
    }
}
