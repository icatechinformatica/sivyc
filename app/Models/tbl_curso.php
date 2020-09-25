<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\supervision\SupervisionInstructor;

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
    
    // scope TABLERO DE CONTROL .- Romelia Pérez Nangüelu
    public function scopeBusquedaTablero($query, $ubicacion, $fecha_inicio, $fecha_termino){
        if(!$fecha_inicio AND !$fecha_termino)$fecha_hoy = "2020-09-01"; //date("Y-m-d");
        else $fecha_hoy="";
        
        if($ubicacion)  $query->where('tbl_cursos.unidad',$ubicacion);        
        
        if($fecha_hoy){ 
            $query->where('tbl_cursos.fecha_apertura',$fecha_hoy);
            $fecha_inicio = $fecha_hoy;
        }elseif($fecha_inicio AND $fecha_termino){
            if($fecha_inicio > $fecha_termino)
                $query->where('tbl_cursos.fecha_apertura','>=',$fecha_termino)->where('tbl_cursos.fecha_apertura','<=',$fecha_inicio);
            else 
                $query->where('tbl_cursos.fecha_apertura','>=',$fecha_inicio)->where('tbl_cursos.fecha_apertura','<=',$fecha_termino);
        }elseif($fecha_inicio){            
            $query->where('tbl_cursos.fecha_apertura',$fecha_inicio);
        }elseif($fecha_termino){                        
            $query->where('tbl_cursos.fecha_apertura',$fecha_termino);
        } 
        return $query;
    }
   
    public function scopeBusquedaSupervisor($query, $tipo, $valor, $fecha, $unidades){   
                   
        if($fecha)$query = $query->where('inicio','<=',$fecha)->where('termino','>=',$fecha);
        if($unidades) {
            $unidades = explode(',',$unidades);
            $query = $query->whereIn('unidad',$unidades);
        }
        if (!empty($tipo) AND !empty(trim($valor))) {                     
            switch ($tipo) {
                case 'nombre_instructor':                        
                    $query = $query->where('nombre', 'like', '%'.$valor.'%');
                    break;
                case 'clave_curso':                        
                    $query = $query->where('clave',$valor);
                    break;
                case 'nombre_curso':                        
                    $query = $query->where('curso', 'LIKE', '%'.$valor.'%');
                    break;                    
            }
    
            return $query->orderBy('inicio', 'DESC');
        }
    }
}
