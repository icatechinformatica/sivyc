<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    // cargando el nombre y atributos del objeto
    protected $table = 'tbl_municipios';

    protected $fillable = [
        'id','muni','ze'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
