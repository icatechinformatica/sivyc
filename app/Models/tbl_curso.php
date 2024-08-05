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
    'hombre','mujer','tipo','fcespe','cgeneral','fcgen','opcion','motivo','cp','ze','id_curso','id_instructor',
    'modinstructor','nmunidad','nmacademico','observaciones','status','realizo','valido','arc',
    'tcapacitacion','status_curso','fecha_apertura','fecha_modificacion','costo','motivo_correccion',
    'pdf_curso','json_supervision','turnado','fecha_turnado','tipo_curso','id_especialidad','instructor_escolaridad',
    'instructor_titulo','instructor_sexo','instructor_mespecialidad','medio_virtual','link_virtual','folio_grupo',
    'id_municipio','clave_especialidad','id_cerss','tdias','movimiento_bancario','fecha_movimeinto_bancario',
    'factura','fecha_factura','mov_bancario','soportes_instructor','firma_user','firma_cerss_one','firma_cerss_two',
    'url_pdf_acta','url_pdf_conv','observacion_asistencia_rechazo','observacion_calificacion_rechazo'
];

    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = ['json_supervision' => 'array',
                        'mov_bancario' => 'array',
                        'soportes_instructor' => 'array',
                        'evidencia_fotografica' => 'json'];

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
                        return $query->WHERE('tbl_cursos.clave', 'LIKE', "%$buscar%");
                        break;
                    case 'nombre_curso':
                        # code...
                        return $query->WHERE( 'cursos.nombre_curso', 'LIKE', "%$buscar%");
                        break;
                    case 'instructor':
                        # code...
                        return $query->WHERE( \DB::raw('CONCAT(instructores."apellidoPaterno", '."' '".' , instructores."apellidoMaterno", '."' '".' , instructores.nombre)'), 'LIKE', "%$buscar%");
                        break;
                    case 'unidad':
                        # retornar una consulta
                        return $query->WHERE( 'tbl_cursos.unidad', 'LIKE', "%$buscar%");
                        break;
                    case 'anio':
                        # retornar consulta por anio
                        return $query->WHERE(\DB::raw("date_part('year' , tbl_cursos.created_at )"), '=', "$buscar");
                        break;
                    case 'arc01':
                        # code...
                        return $query->WHERE('tbl_cursos.munidad', 'LIKE', "%$buscar%");
                    break;
                    default:
                        # code...
                        break;
                }
            }
        }
    }

	// scope TABLERO DE CONTROL .- Romelia Pérez Nangüelu
    public function scopeBusquedaTablero($query, $ubicacion, $fecha_inicio, $fecha_termino){
        if(!$fecha_inicio AND !$fecha_termino)$fecha_hoy = date("Y-m-d");
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

    protected function scopeSearchByData($query, $unidades){
        if ($unidades) {
            # generamos la consulta del scope
            $query->where('u.ubicacion', '=', $unidades);
            return $query;
        }
    }

    protected function scopeSearchByUnidadMes($query, $unidad, $mes){
        /**
         * SCOPE BUSCADOR PARA UNIDAD Y MES .- DISEÑADO POR MIS. DANIEL MÉNDEZ CRUZ
         */
        if (!empty($unidad) AND !empty($mes)) {
            # checamos que no haya vacios
            $query->WHERE(DB::raw("to_char(tbl_cursos.fecha_turnado, 'TMMONTH')"), $mes)->WHERE('tblU.ubicacion', $unidad);
           return $query;
        }
    }

    protected function scopeSearchByMesUnidadAnio($query, $mesoptenido){
        if (!empty($mesoptenido)) {
            # se cumple culquiera de las condiciones
            $query->WHERE(DB::raw("to_char(tbl_cursos.fecha_turnado, 'TMMONTH')"), $mesoptenido);
            return $query;
        }
    }

    #Expedientes Unicos Busqueda
    protected function scopeBusquedaExpediente($query, $sel_status){
        if($sel_status == 'EN CAPTURA'){
            $query->orWhereRaw("ex.vinculacion->>'status_save' = 'false'")
            ->orWhereRaw("ex.academico->>'status_save' = 'false'")->orWhereRaw("ex.administrativo->>'status_save' = 'false'");
            return $query;
        
        }else if(empty($sel_status) || $sel_status == 'PENDIENTE POR ENVIAR') { //Esta vacio traer pendientes por default
            $query->whereRaw("ex.administrativo->>'status_dpto' = 'CAPTURA'")->whereRaw("ex.vinculacion->>'status_save' = 'true'")
            ->whereRaw("ex.academico->>'status_save' = 'true'")->whereRaw("ex.administrativo->>'status_save' = 'true'");            
            return $query;

        }else if($sel_status == 'ENVIADO A DTA'  || $sel_status == 'PENDIENTE'){  //dele : enviado a dta / dta: pendientes
            //$query->whereRaw("ex.vinculacion->>'status_dpto' = 'ENVIADO'")->whereRaw("ex.academico->>'status_dpto' = 'ENVIADO'")
            //->whereRaw("ex.administrativo->>'status_dpto' = 'ENVIADO'")->whereRaw("ex.vinculacion->>'status_save' = 'true'")  
            //->whereRaw("ex.academico->>'status_save' = 'true'")->whereRaw("ex.administrativo->>'status_save' = 'true'");

            $query->whereRaw("ex.administrativo->>'status_dpto' = 'ENVIADO'");
            return $query;

        }else if($sel_status == 'RETORNADO'){
            $query//->whereRaw("ex.vinculacion->>'status_dpto' = 'RETORNADO'")->whereRaw("ex.academico->>'status_dpto' = 'RETORNADO'")
            ->whereRaw("ex.administrativo->>'status_dpto' = 'RETORNADO'");
            return $query;

        }else if($sel_status == 'VALIDADO'){
            //$query->whereRaw("ex.vinculacion->>'status_dpto' = 'VALIDADO'")->whereRaw("ex.academico->>'status_dpto' = 'VALIDADO'")
            $query->whereRaw("ex.administrativo->>'status_dpto' = 'VALIDADO'");
            return $query;
        }
    }
}
