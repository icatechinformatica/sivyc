<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioCurso extends Model
{
    use HasFactory;
    protected $table = 'tbl_aux_servicios';
    protected $fillable = ['servicio', 'alumnos_min'];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }

}
