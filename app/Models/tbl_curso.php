<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tbl_curso extends Model
{
    //
    protected $table = 'tbl_cursos';

    protected $fillable = [
    'id','cct','unidad','nombre','curp','rfc','clave','grupo','mvalida','mod','turno','area','espe','curso',
    'inicio','termino','dia','dia2','pini','pfin','dura','hini','hfin','horas','ciclo','plantel','depen','muni',
    'sector','programa','nota','hini2','hfin2','munidad','efisico','cespecifico','mpaqueteria','mexoneracion',
    'hombre','mujer','tipo','fcespe','cgeneral','fcgen','opcion','motivo','cp','ze','id_curso','id_instructor'
];

    protected $hidden = ['created_at', 'updated_at'];

    public function curso() {
        return $this->belongsTo(curso::class, 'id_curso');
    }
    public function instructor() {
        return $this->belongsTo(instructor::class, 'id_instructor');
    }

    // scope
    public function scopeBusquedaCursoValidado($query, $tipo, $buscar){
        if (!empty($tipo)) {
            # entramos y validamos
            if (!empty(trim($buscar))) {
                # empezamos
                switch ($tipo) {
                    case 'clave':
                        # code...
                        return $query->WHERE('clave', '=', $buscar);
                        break;
                    case 'nombre_curso':
                        # code...
                        return $query->where( 'nombre', 'LIKE', "%$buscar%");
                        break;
                    case 'instructor':
                        # code...
                        return $query->where( \DB::raw('CONCAT(nombre, '."' '".' , "apellidoPaterno", '."' '".' , "apellidoMaterno")'), 'LIKE', "%$buscar%");
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
    }

}
