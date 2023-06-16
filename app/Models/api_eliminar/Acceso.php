<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Acceso extends Model
{
    //
    protected $table = 'tbl_acceso';

    protected $fillable = [
        'id','nombrecompreto', 'id_categoria', 'numeroenlace', 'usuario', 'contrasena', 'correo',
        'email_verified_at', 'remember_token', 'unidad', 'puesto'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
