<?php

namespace App\Models\Documentos;

use Illuminate\Database\Eloquent\Model;

class Eplantillas extends Model
{
    protected $table = 'tbl_eplantillas';

    protected $fillable = [
        'id', 'tipo', 'encabezado', 'cuerpo', 'pie', 'vigencia', 'firmantes'
    ];

    protected $casts = [
        'cuerpo' => 'xml',
        'firmantes' => 'array',
        'vigencia' => 'date',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
