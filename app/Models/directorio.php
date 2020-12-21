<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class directorio extends Model
{
    protected $table = 'directorio';

    protected $fillable = [
        'id','nombre','apellidoPaterno','apellidoMaterno','puesto','numero_enlace','categoria','area_adscripcion_id'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
