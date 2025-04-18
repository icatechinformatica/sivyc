<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class especialidad_instructor extends Model
{
    //
    protected $table = 'especialidad_instructores';

    protected $fillable = [
        'id','especialidad_id','perfilprof_id','unidad_solicita','memorandum_valdidacion',
        'fecha_validacion','memorandum_modificacion','observacion', 'criterio_pago_id','lastUserId','activo',
        'id_instructor','cursos_impartir','memorandum_solicitud','fecha_solicitud','especialidad','status','nrevision',
        'observacion_validacion','memorandum_baja','fecha_baja'];

    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = ['cursos_impartir' => 'array', 'hvalidacion' => 'array'];

    public function cursos()
    {
        return $this->belongsToMany(curso::class, 'especialidad_instructor_curso', 'id_especialidad_instructor', 'curso_id');
    }

}
