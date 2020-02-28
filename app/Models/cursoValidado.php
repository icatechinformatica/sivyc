<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class cursoValidado extends Model
{
    //
    protected $table = 'curso_validado';

    protected $fillable = [
        'id', 'clave_curso', 'id_curso', 'numero_control'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * obtener el instructor que pertenece al perfil
     */
    public function instructor_val()
    {
        return $this->belongsTo(instructor::class);
    }
    public function curso()
    {
        return $this->belongsTo(curso::class);
    }
}
