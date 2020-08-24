<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogoCurso extends Model
{
    // generamos el modelo
    protected $table = 'cursos';

    protected $fillable = [
        'id','nombre_curso','modalidad','horas','clasificacion','costo','duracion',
        'objetivo','perfil','solicitud_autorizacion','fecha_validacion','memo_validacion',
        'memo_actualizacion','fecha_actualizacion','unidad_amovil','descripcion','no_convenio','id_especialidad',
        'area', 'cambios_especialidad', 'nivel_estudio', 'categoria', 'tipo_curso', 'rango_criterio_pago_minimo', 'rango_criterio_pago_maximo'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * obtener el instructor que pertenece al perfil
     */
    public function curso_validado()
    {
        return $this->hasMany(CursoValidado::class);
    }

    public function area() {
        return $this->belongsTo(Area::class, 'id');
    }
}
