<?php

namespace App\Models\Reportes;

// date_default_timezone_set('Etc/GMT+6');

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
