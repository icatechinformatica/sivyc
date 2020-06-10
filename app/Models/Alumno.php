<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    //
    protected $table = 'alumnos_registro';

    protected $fillable = [
        'no_control',
        'fecha',
        'numero_solicitud',
        'id_pre',
        'id_especialidad',
        'id_curso',
        'horario',
        'grupo',
        'unidad',
        'tipo_curso',
        'realizo',
        'cerrs',
        'etnia',
        'estatus_modificacion'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function alumnospre() {
        return $this->belongsTo(Alumnopre::class, 'id');
    }

    // in your model
    public function getMyDateFormat($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }
}
