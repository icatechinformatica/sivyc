<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Alumnopre extends Model
{
    //
    protected $table = 'alumnos_pre';

    protected $fillable = ['id','nombre','apellidoPaterno','apellidoMaterno','telefono', 'curp', 'sexo', 'fecha_nacimiento',
    'domicilio', 'colonia', 'cp', 'municipio', 'estado', 'estado_civil', 'discapacidad'];

    protected $hidden = ['created_at', 'updated_at'];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_pre');
    }

    /**
     * mutator en laravel
     */

     public function setFechaNacAttribute($value) {
        return $this->attributes['fecha_nacimiento'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
     }
}
