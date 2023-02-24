<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class localidad extends Model
{
    //
    protected $table = 'tbl_localidades';

    protected $fillable = [
        'id','localidad','id_estado','clave_municipio','latitud','longitud','lat_decimal','lon_decimal',
        'altitud','poblacion','id_unidad','clave'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
