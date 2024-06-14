<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class especialidad extends Model
{
    //
    protected $table = 'especialidades';

    protected $fillable = [
        'id','clave','nombre','campo_formacion', 'id_areas','activo','prefijo'
    ];

    protected $hidden = ['created_at'];

    public function scopeBusqueda($query, $tipo, $buscar)
    {
        if (!empty(trim($buscar))){
            $query->where(function ($query) use ($buscar) {
                $terminoBusqueda = '%' . $buscar . '%';
                $query->where('especialidades.clave', 'like', $terminoBusqueda)
                      ->orWhere('especialidades.nombre', 'like', $terminoBusqueda)
                      ->orWhere('especialidades.prefijo', 'like', $terminoBusqueda);
            })
            ->orwhere('area.formacion_profesional', 'like', '%' . $buscar . '%');
        }
    }

    /**
     * obtener el instructor que pertenece al perfil
     */

}
