<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Folio extends Model
{
    //
    protected $table = 'tbl_folios';

    protected $fillable = [
        'id',
        'unidad',
        'id_curso',
        'matricula',
        'nombre',
        'folio',
        'movimiento',
        'motivo',
        'mod',
        'fini',
        'ffin',
        'focan',
        'realizo',
        'fecha_acta',
        'fecha_expedicion',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
