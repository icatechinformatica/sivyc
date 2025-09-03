<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exoneraciones extends Model
{

    protected $table = 'tbl_exoneraciones';

    protected $fillable = [
        'id', 'tipo_exoneracion', 'corta'
    ];

    // protected $hidden = ['created_at', 'updated_at'];

    // protected function scopeBusqueda($query, $tipo, $buscar)
    // {
    //     if (!empty($tipo)) {
    //         if (!empty(trim($buscar))) {
    //             switch ($tipo) {
    //                 case 'id_unidad_capacitacion':
    //                     $idUnidad = tbl_unidades::where('unidad', '=', $buscar)->get();
    //                     return $query->where('exoneraciones.id_unidad_capacitacion', '=', $idUnidad[0]->id);
    //                     break;
    //                 case 'no_memorandum':
    //                     return $query->where('exoneraciones.no_memorandum', '=', $buscar);
    //                     break;
    //                 case 'no_convenio':
    //                     return $query->where('exoneraciones.no_convenio', '=', $buscar);
    //                     break;
    //                 case 'tipo_exoneracion':
    //                     return $query->where('exoneraciones.tipo_exoneracion', '=', $buscar);
    //                     break;
    //             }
    //         }
    //     }
    // }

    public function grupo()
    {
        return $this->hasMany(Grupo::class, 'id_tipo_exoneracion', 'id');
    }
}
