<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    //
    protected $table = 'especialidades';

    protected $fillable = [
        'id','clave','id_areas','nombre'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
