<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Especialidad extends Model
{
    //
    protected $table = 'especialidades';

    protected $fillable = [
        'id', 'clave', 'id_areas', 'nombre','activo','prefijo'
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
}
