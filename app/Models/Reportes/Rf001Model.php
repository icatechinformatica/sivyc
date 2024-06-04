<?php

namespace App\Models\Reportes;

use Illuminate\Database\Eloquent\Model;

class Rf001Model extends Model
{
    //
    protected $table = 'tbl_rf001';

    protected $fillable = [
        'id',
        'memorandum',
        'estado',
        'movimientos',
        'id_unidad',
        'envia',
        'dirigido',
        'archivos',
        'unidad',
        'periodo_inicio',
        'periodo_fin',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
