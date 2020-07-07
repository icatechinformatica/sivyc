<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumnosSice extends Model
{
    //
    protected $table = 'registro_alumnos_sice';

    protected $fillable = [
        'id', 'no_control', 'curp'

    ];

    protected function scopeBusquedaCurp($query, $buscar){
        if (!empty(trim($buscar))) {
            return $query->where('curp', '=', $buscar);
        }
    }

    protected $hidden = ['created_at', 'updated_at'];
}
