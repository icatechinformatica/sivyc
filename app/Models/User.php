<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use Notifiable;
    //
    protected $table = 'tblz_usuarios';

    protected $fillable = [
        'id','nombre', 'email', 'unidad', 'puesto', 'token_movil','correo_institucional','activo'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
