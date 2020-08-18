<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    //
    protected $table = 'directorio';

    protected $fillable = [
        'id', 'name', 'apellidoPaterno', 'apellidoMaterno', 'puesto', 'numero_enlace', 'categoria' , 'area_adscripcion_id',
        'activo', 'qr_generado'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function adscripcion()
    {
        return $this->belongsTo(AreaAdscripcion::class);
    }
}
