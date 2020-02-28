<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumnopre extends Model
{
    //
    protected $table = 'alumnos_pre';

    protected $fillable = ['id','nombre','correo','telefono','curso', 'horario', 'especialidad_que_desea_inscribirse', 'modo_entero_del_sistema',
    'motivos_eleccion_sistema_capacitacion'];

    protected $hidden = ['created_at', 'updated_at'];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class);
    }
}
