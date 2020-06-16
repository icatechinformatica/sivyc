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
        'fecha_acta',
        'matricula',
        'nombre',
        'folio',
        'fecha_expedicion',
        'movimiento',
        'motivo',
        'mod',
        'fini',
        'ffin',
        'focan',
        'realizo'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
