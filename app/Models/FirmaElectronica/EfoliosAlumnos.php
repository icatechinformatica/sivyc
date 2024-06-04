<?php

namespace App\Models\FirmaElectronica;

use Illuminate\Database\Eloquent\Model;

class EfoliosAlumnos extends Model
{
    protected $table = 'efolios_alumnos';

    protected $fillable = [
        'obj_documento', 'status_doc', 'link_verif', 'documento_xml', 'datos_alumno'
    ];

    protected $casts = [
        'obj_documento' => 'json',
        'datos_alumno' => 'json',
        'documento_xml' => 'xml',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
