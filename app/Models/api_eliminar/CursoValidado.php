<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CursoValidado extends Model
{
    //
    protected $table = 'curso_validado';

    protected $fillable = [
        'id', 'id_instructor', 'id_curso', 'clave_curso','fecha_inicio','fecha_termino'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * obtener el instructor que pertenece al perfil
     */
    public function instructor_val()
    {
        return $this->belongsTo(Instructor::class);
    }
    public function curso()
    {
        return $this->belongsTo(curso::class);
    }
    public function Folio()
    {
        return $this->hasMany(tablaFolio::class);
    }
}
