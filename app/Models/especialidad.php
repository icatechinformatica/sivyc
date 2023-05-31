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

    protected $hidden = ['created_at', 'updated_at'];

    public function scopeBusqueda($query, $tipo, $buscar)
    {
        if (!empty($tipo)) {
            if (!empty(trim($buscar))) {
                switch ($tipo) {
                    case 'clave':
                        return $query->where('especialidades.clave', '=', $buscar);
                        break;
                    case 'nombre':
                        return $query->where('especialidades.nombre', '=', $buscar);
                        break;
                    case 'prefijo':
                        return $query->where('especialidades.prefijo', '=', $buscar);
                        break;
                    case 'area':
                        $idArea = DB::table('area')->where('area.formacion_profesional', '=', $buscar)->select('id')->first();
                        return $query->where('especialidades.id_areas', '=', $idArea->id);
                        break;
                }
            }
        }
    }

    /**
     * obtener el instructor que pertenece al perfil
     */

}
