<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{

    protected $table = 'especialidades';

    protected $fillable = [ 'id', 'clave', 'nombre', 'id_areas', 'activo', 'prefijo', 'unidades_disponibles'];

    protected $hidden = ['created_at', 'updated_at'];
}
