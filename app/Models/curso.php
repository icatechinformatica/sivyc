<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class curso extends Model
{
    //
    protected $table = 'cursos';

    protected $fillable = [
            'id','nombre_curso','modalidad','clasificacion','costo','duracion',
            'objetivo','perfil','solicitud_autorizacion','fecha_validacion','memo_validacion',
            'memo_actualizacion','fecha_actualizacion','unidad_amovil','descripcion','no_convenio','id_especialidad',
            'area', 'cambios_especialidad', 'nivel_estudio', 'categoria'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * obtener el instructor que pertenece al perfil
     */
    public function curso_validado()
    {
        return $this->hasMany(cursoValidado::class);
    }

    public function area() {
        return $this->belongsTo(Area::class, 'id');
    }

    /**
     * mutator en laravel
     */

    public function setFechaAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d');
    }

    // in your model
    public function getMyDateFormat($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

}
