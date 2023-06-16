<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    //
    protected $table = 'tbl_unidades';

    protected $fillable = [
            'id', 'unidad', 'cct', 'dunidad', 'dgeneral', 'plantel', 'academico', 'vinculacion',
            'dacademico', 'pdgeneral', 'pdacademico', 'pdunidad', 'pacademico', 'pvinculacion'
        ];

    protected $hidden = ['created_at', 'updated_at'];
}
