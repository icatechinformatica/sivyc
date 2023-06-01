<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instructor extends Model
{
    //
    protected $table = 'instructores';

    protected $fillable = [
        'id', 'numero_control', 'nombre', "apellidoPaterno", "apellidoMaterno",
       'rfc', 'curp', 'sexo', 'estado_civil', 'fecha_nacimiento', 'entidad', 'municipio',
       'asentamiento', 'domicilio', 'telefono', 'correo', 'banco', 'no_cuenta',
       'interbancaria', 'folio_ine','id_especialidad',
       'tipo_honorario', 'archivo_ine', 'archivo_domicilio', 'archivo_curp',
       'archivo_alta', 'archivo_bancario', 'archivo_fotografia', 'archivo_estudios',
       'archivo_otraid', 'status', 'rechazo', 'clave_unidad', 'extracurricular'
        ];



    protected $hidden = ['created_at', 'updated_at'];

    public function curso_Validado()
    {
        return $this->hasMany(CursoValidado::class);
    }

    public function instructor_pefil(){
        return $this->hasMany(InstructorPerfil::class, 'numero_control', 'id');
    }
}
