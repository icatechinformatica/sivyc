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
        'id_pre', 'id_especialidad',
        'id_curso',
        'horario',
        'grupo',
        'ultimo_grado_estudios',
        'empresa_trabaja',
        'antiguedad',
        'direccion_empresa',
        'medio_entero',
        'sistema_capacitacion_especificar',
        'chk_acta_nacimiento',
        'acta_nacimiento',
        'chk_curp',
        'curp',
        'chk_comprobante_domicilio',
        'comprobante_domicilio',
        'chk_fotografia',
        'fotografia',
        'chk_ine',
        'ine',
        'chk_pasaporte_licencia',
        'pasaporte_licencia_manejo',
        'chk_comprobante_ultimo_grado',
        'comprobante_ultimo_grado',
        'puesto_empresa'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function alumnospre() {
        return $this->belongsTo(Alumnopre::class, 'id');
    }
}
