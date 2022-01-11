<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class organismosPublicos extends Model {
    
    protected $table = 'organismos_publicos';

    protected $fillable = [
        'id', 'organismo', 'sector', 'poder_pertenece', 'tipo', 'nombre_titular', 'id_estado', 'id_municipio', 'clave_localidad', 'telefono', 'correo', 'direccion', 'activo'
    ];

    protected $hidden = ['created_at', 'updated_at'];

}
