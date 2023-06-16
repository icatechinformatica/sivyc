<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    // cargando el nombre y atributos del objeto
    protected $table = 'estados';

    protected $fillable = [
        'id','nombre'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
