<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class tablaFolio extends Model
{
    //
    protected $table = 'curso_validado_tabla_supre';

    protected $fillable = [
        'id','idcurso_validado','id_supre'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * obtener el instructor que pertenece al perfil
     */
    public function supre()
    {
        return $this->belongsTo(supre::class);
    }
    public function cursoValidado()
    {
        return $this->belongsTo(cursoValidado::class);
    }
}
