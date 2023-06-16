<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Directorio extends Model
{
    //
    protected $table = 'directorio';

    protected $fillable = [
            'id', 'nombre', 'apellidoPaterno', 'apellidoMaterno', 'puesto', 'numero_enlace', 'categoria'
        ];

    protected $hidden = ['created_at', 'updated_at'];
}
