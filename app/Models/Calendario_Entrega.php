<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendario_Entrega extends Model
{
    protected $table = 'calendario_entrega';

    protected $fillable = ['id','mes_informar','fecha_entrega','tipo_entrega','activo'];

    protected $casts = [
        'tipo_entrega' => 'array'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
