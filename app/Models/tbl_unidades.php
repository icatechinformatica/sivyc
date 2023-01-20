<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tbl_unidades extends Model
{
    //
    protected $table = 'tbl_unidades';

    protected $fillable = [
    'id','unidad','cct','dunidad','dgeneral','plantel','academico','vinculacion','dacademico','pdgeneral','pdacademico',
    'pdunidad','pacademico','pvinculacion','jcyc','pjcyc','ubicacion','created_at','updated_at','ubicacion','cuenta'
];

    protected $hidden = ['created_at', 'updated_at'];
}
