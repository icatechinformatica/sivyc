<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class curso extends Model
{
    //
    protected $table = 'cursos';

    protected $fillable = [
            'id','especialidad','nombre_curso','modalidad','horas','clasificacion','costo','duracion',
            'objetivo','perfil','solicitud_autorizacion','fecha_validacion','memo_validacion',
            'memo_actualizacion','fecha_actualizacion','unidad_amovil','descripcion','no_convenio','id_especialidad'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * obtener el instructor que pertenece al perfil
     */
    public function curso_validado()
    {
        return $this->hasMany(cursoValidado::class);
    }

}
