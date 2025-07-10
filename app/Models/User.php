<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use Notifiable;
    //
    protected $table = 'tblz_usuarios';

    protected $with = ['registro'];

    protected $fillable = [
        'id','nombre', 'email', 'unidad', 'puesto', 'token_movil','correo_institucional','activo',
        'registro_id', 'registro_type', 'fecha_caducidad'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    // creando una relación polimórfica con el modelo Registro

    public function registro()
    {
        return $this->morphTo(__FUNCTION__, 'registro_type', 'registro_id');
    }
}
