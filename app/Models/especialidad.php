<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class especialidad extends Model
{
    //
    protected $table = 'especialidades';

    protected $fillable = [
        'id','clave','nombre','campo_formacion', 'id_areas'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * obtener el instructor que pertenece al perfil
     */

}
