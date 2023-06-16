<?php

namespace App\Models\ModelPat;

use Illuminate\Database\Eloquent\Model;

class Organismos extends Model
{
    protected $table = 'tbl_organismos';

    protected $fillable = ['id', 'id_parent', 'dif_dpto', 'nombre', 'siglas', 'clave', 'activo', 'created_at', 'updated_at', 'iduser_created', 'iduser_updated'];

    protected $hidden = ['created_at', 'updated_at'];
}
