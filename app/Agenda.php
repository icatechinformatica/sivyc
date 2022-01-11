<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agenda';

    protected $fillable = ['id','title', 'start', 'end', 'textColor', 'observaciones', 'id_curso', 'id_instructor',
    'id_unidad', 'id_municipio', 'iduser_created', 'iduser_updated'];

    protected $hidden = ['created_at', 'updated_at'];
}
