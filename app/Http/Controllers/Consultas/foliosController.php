<?php

namespace App\Http\Controllers\Consultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Excel\xlsFoliosAsignados;
use App\Models\Inscripcion;
use App\Models\tbl_curso;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class foliosController extends Controller {   
    function __construct() {
        session_start();
        $this->path_files = env("APP_URL").'/storage/uploadFiles';
    }
    
    public function index(Request $request){

        $id_user = Auth::user()->id;
        $message = $folios = $unidad = $mod = $finicial = $ffinal= NULL;
        $meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
        
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');        
        $_SESSION['unidades'] = $unidades = $message = $data = NULL;
        if(session('message')) $message = session('message');
       // $rol="unidad";
        if ($rol) { 
            $unidad = Auth::user()->unidad;
            $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad','unidad');
            if(count($unidades)==0) $unidades =[$unidad];       
            $_SESSION['unidades'] = $unidades;           
        }

        if (!$unidades ) {
            $unidades = DB::table('tbl_unidades')->orderby('unidad','ASC')->pluck('unidad','unidad');
            $_SESSION['unidades'] = $unidades;   
        }

        $folios = [];
        $busquedaGeneral = $request->busquedaGeneral;

        $buscarComboC = $request->cursoS;
        $buscarDatoC = $request->datoCurso;
        $buscarDatoClave = $request->claveCurso;

        $buscarCombo = $request->alumnoS;
        $buscarDato = $request->datoAlumno;
        switch ($busquedaGeneral) {
            case '3': //rango de folios
                if($request->unidad) {
                    $unidad = $request->unidad;
                    $mod = $request->mod;
                    $finicial = $request->finicial;
                    $ffinal = $request->ffinal;
                                    
                    $folios = DB::table('tbl_folios as f')
                        ->select(
                            'c.unidad',
                            'f.folio',
                            'f.mod',
                            'f.fecha_expedicion',
                            'f.movimiento',
                            'f.motivo',
                            'i.matricula',
                            'i.alumno',
                            'c.clave',
                            'c.curso',
                            'c.dura',
                            'c.inicio',
                            'c.termino',
                            DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),
                            'c.dia',
                            'c.nombre',
                            'c.muni',
                            'c.depen',
                            'c.efisico',
                            'c.status',
                            'c.tcapacitacion',
                            'c.status_curso',
                            'f.file_autorizacion')
                        ->where('f.folio','>','0');
                        if($request->mod) $folios = $folios->where('f.mod',$request->mod);
                        if($request->finicial) $folios = $folios->where('f.folio','>=',$request->finicial);
                        if($request->ffinal) $folios = $folios->where('f.folio','<=',$request->ffinal);
                        if($request->unidad) $folios = $folios->where('c.unidad',$request->unidad);  
                        if($_SESSION['unidades'])$folios = $folios->whereIn('f.unidad',$_SESSION['unidades']);
                                              
                        $folios = $folios->Join('tbl_inscripcion as i', function($join) {                                        
                            $join->on('f.id_curso', '=', 'i.id_curso');
                            $join->on('f.matricula', '=', 'i.matricula');                            
                        })
                        ->join('tbl_cursos as c','c.id','i.id_curso')
                        ->orderby('f.folio')->get();
                }
                break;
            case '1': // por curso
                $claveCurso = $request->claveCurso;
                $folios = $this->getDataForCurso($claveCurso);
                break;
            case '2': // por alumno
                // $folios = $this->getDataForAlumno($request->alumnoS, $request->datoAlumno);
                $folios = $this->getDataForAlumno($buscarCombo, $buscarDato);
                break;
        }

        foreach ($folios as $value) {
            $value->mes = $meses[Carbon::parse($value->termino)->month - 1];
        }

       $path_file = $this->path_files;         
        return view('consultas.folios', compact('message','unidades','folios','unidad', 'mod', 'finicial', 'ffinal', 'path_file', 'busquedaGeneral', 'buscarComboC', 'buscarDatoC', 'buscarDatoClave', 'buscarCombo', 'buscarDato'));     
    }  
    
    public function xls(Request $request){

        $meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
        
        $unidad = $request->unidad;
        $mod = $request->mod;
        $finicial = $request->finicial;
        $ffinal = $request->ffinal;
        
        $folios = [];
        $busquedaGeneral = $request->busquedaGeneral;
        switch ($busquedaGeneral) {
            case '3':
                $folios = DB::table('tbl_folios as f')
                    ->select(
                        'f.unidad',
                        'c.clave',
                        'c.curso',
                        'f.folio',
                        'f.mod',
                        'f.fecha_expedicion',
                        'f.movimiento',
                        'f.motivo',
                        'i.matricula',
                        'i.alumno',
                        'c.dura',
                        'c.inicio',
                        'c.termino',
                        'c.termino as mes',
                        DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),
                        'c.dia',
                        'c.nombre',
                        'c.muni',
                        'c.depen',
                        'c.efisico',
                        'c.status',
                        'c.tcapacitacion',
                        'c.status_curso')
                    ->where('f.folio','>','0');
                    if($request->mod) $folios = $folios->where('f.mod',$request->mod);
                    if($request->finicial) $folios = $folios->where('f.folio','>=',$request->finicial);
                    if($request->ffinal) $folios = $folios->where('f.folio','<=',$request->ffinal);
                    if($request->unidad) $folios = $folios->where('f.unidad',$request->unidad);
                    if($_SESSION['unidades'])$folios = $folios->whereIn('f.unidad',$_SESSION['unidades']);                        
                    $folios = $folios->Join('tbl_inscripcion as i', function($join){                                        
                        $join->on('f.id_curso', '=', 'i.id_curso');
                        $join->on('f.matricula', '=', 'i.matricula');                            
                    })
                    ->join('tbl_cursos as c','c.id','i.id_curso')
                    ->orderby('f.folio')->get();
                    $name= "FOLIOS_ASIGNADOS_".$unidad.".xlsx";
                    $title = "FOLIOS_ASIGANDOS_".$unidad;    
                break;
            case '1':
                $claveCurso = $request->claveCurso;
                $folios = $this->getDataForCurso($claveCurso);
                $name= "FOLIOS_ASIGNADOS_".$claveCurso.".xlsx";
                $title = "FOLIOS_ASIGANDOS_".$claveCurso;    
                break;
            case '2':
                $folios = $this->getDataForAlumno($request->alumnoS, $request->datoAlumno);
                $name = "FOLIOS_ASIGNADOS_".$request->datoAlumno.".xlsx";
                $title = "FOLIOS_ASIGANDOS_".$request->datoAlumno;   
                break;
        }

        foreach ($folios as $value) {
            if (isset($value->file_autorizacion)) {
                unset($value->file_autorizacion);
            }
            $value->mes = $meses[Carbon::parse($value->termino)->month - 1];
        }
        if(count($folios)==0){ return "NO REGISTROS QUE MOSTRAR";exit;}
                                
        $head = ['UNIDAD', 'CLAVE', 'CURSO', 'FOLIO','MOD','EXPEDICION','ESTATUS','MOTIVO','MATRICULA','ALUMNO', 'DURACION', 'INICIO', 'TERMINO', 'MES TERMINO', 'HORARIO', 'DIAS', 'INSTRUCTOR', 'MUNICIPIO', 'DEPENDENCIA BENEFICIADA', 'ESPACIO', 'ESTATUS FORMATO T', 'CAPACITACION', 'ESTATUS APERTURA'];            
        
        if(count($folios)>0)return Excel::download(new xlsFoliosAsignados($folios,$head, $title), $name);
            

        /* if($unidad){                    
            $folios = DB::table('tbl_folios as f')
                ->select('f.unidad',
                    'c.clave',
                    'c.curso',
                    'f.folio',
                    'f.mod',
                    'f.fecha_expedicion',
                    'f.movimiento',
                    'f.motivo',
                    'i.matricula',
                    'i.alumno',
                    'c.dura',
                    'c.inicio',
                    'c.termino',
                    'c.termino as mes',
                    DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),
                    'c.dia',
                    'c.nombre',
                    'c.muni',
                    'c.depen',
                    'c.efisico',
                    'c.status',
                    'c.tcapacitacion',
                    'c.status_curso')
                ->where('f.folio','>','0');
                if($request->mod) $folios = $folios->where('f.mod',$request->mod);
                if($request->finicial) $folios = $folios->where('f.folio','>=',$request->finicial);
                if($request->ffinal) $folios = $folios->where('f.folio','<=',$request->ffinal);
                if($request->unidad) $folios = $folios->where('f.unidad',$request->unidad);
                 if($_SESSION['unidades'])$folios = $folios->whereIn('f.unidad',$_SESSION['unidades']);                        
                $folios = $folios->Join('tbl_inscripcion as i', function($join){                                        
                    $join->on('f.id_curso', '=', 'i.id_curso');
                    $join->on('f.matricula', '=', 'i.matricula');                            
                })
                ->join('tbl_cursos as c','c.id','i.id_curso')
                ->orderby('f.folio')->get();
            
            foreach ($folios as $value) {
                $value->mes = $meses[Carbon::parse($value->termino)->month - 1];
            }
            
            if(count($folios)==0){ return "NO REGISTROS QUE MOSTRAR";exit;}
                                
            $head = ['UNIDAD', 'CLAVE', 'CURSO', 'FOLIO','MOD','EXPEDICION','ESTATUS','MOTIVO','MATRICULA','ALUMNO', 'DURACION', 'INICIO', 'TERMINO', 'MES TERMINO', 'HORARIO', 'DIAS', 'INSTRUCTOR', 'MUNICIPIO', 'DEPENDENCIA BENEFICIADA', 'ESPACIO', 'ESTATUS FORMATO T', 'CAPACITACION', 'ESTATUS APERTURA'];            
            $name= "FOLIOS_ASIGNADOS_".$unidad.".xlsx";
            $title = "FOLIOS_ASIGANDOS_".$unidad;    
    
            if(count($folios)>0)return Excel::download(new xlsFoliosAsignados($folios,$head, $title), $name);
             
                
        }else return "SELECCIONE LA UNIDAD"; */        
    } 

    public function cursoAutocomplete(Request $request) {
        $search = $request->search;
        $tipoCurso = $request->tipoCurso;

        if ($tipoCurso == '1') {
            if (isset($search)) {
                $curso = tbl_curso::select('id', 'curso', 'clave')
                    ->where('clave', '!=', '0')
                    ->where('curso', 'like', '%'.$search.'%')
                    ->limit(10)->get();
            }
            $response = array();
            foreach ($curso as $value) {
                $response[] = array('value' => $value->clave, 'label' => $value->clave.' - '.$value->curso, 'charge' => $value->curso);
            }
        } else {
            if (isset($search)) {
                $curso = tbl_curso::select('id', 'clave')
                    ->where('clave', '!=', '0')
                    ->where('clave', 'like', '%'.$search.'%')
                    ->limit(10)->get();
            }
            $response = array();
            foreach ($curso as $value) {
                $response[] = array('value' => $value->clave, 'label' =>$value->clave, 'charge' => $value->clave);
            }
        }

        echo json_encode($response);
        exit;
        // return json_encode($response);
    }

    public function alumnoAutocomplete(Request $request) {
        $search = $request->search;
        $tipoBusqueda = $request->tipoAlumno;

        switch ($tipoBusqueda) {
            case '1': //curp
                if (isset($search)) {
                    $alumno = Inscripcion::select('curp')
                        ->where('curp', 'like', '%'.$search.'%')
                        ->groupBy('curp')
                        ->limit(10)
                        ->get();
                }
                $response = array();
                foreach ($alumno as $value) {
                    $response[] = array('value' => $value->curp, 'label' => $value->curp, 'charge' => $value->curp);
                }
                break;
            case '2': //nombre
                if (isset($search)) {
                    $alumno = Inscripcion::select('alumno')
                        ->where('alumno', 'like', '%'.$search.'%')
                        ->groupBy('alumno')
                        ->limit(10)
                        ->get();
                }
                $response = array();
                foreach ($alumno as $value) {
                    $response[] = array('value' =>$value->alumno, 'label' => $value->alumno, 'charge' => $value->alumno);
                }
                break;
            case '3': //matricula
                if (isset($search)) {
                    $alumno = Inscripcion::select('matricula')
                        ->where('matricula', 'like', '%'.$search.'%')
                        ->groupBy('matricula')
                        ->limit(10)
                        ->get();
                }
                $response = array();
                foreach ($alumno as $value) {
                    $response[] = array('value' => $value->matricula, 'label' => $value->matricula, 'charge' => $value->matricula);
                }
                break;
        }
        return json_encode($response);
    }

    public function getDataForCurso($clave) {
        $folios = DB::table('tbl_folios as f')
            ->select(
                'c.unidad',
                'c.clave',
                'c.curso',
                'f.folio',
                'f.mod',
                'f.fecha_expedicion',
                'f.movimiento',
                'f.motivo',
                'i.matricula',
                'i.alumno',
                'c.dura',
                'c.inicio',
                'c.termino',
                'c.termino as mes',
                DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),
                'c.dia',
                'c.nombre',
                'c.muni',
                'c.depen',
                'c.efisico',
                'c.status',
                'c.tcapacitacion',
                'c.status_curso',
                'f.file_autorizacion',
            )->where('f.folio', '>', '0');
            $folios = $folios->join('tbl_inscripcion as i', function($join) {
                $join->on('f.id_curso', '=', 'i.id_curso');
                $join->on('f.matricula', '=', 'i.matricula');
            })->join('tbl_cursos as c', 'c.id', 'i.id_curso')
            ->where('c.clave', $clave)
            ->orderBy('f.folio')->get();
        return $folios;
    }

    public function getDataForAlumno($campo, $dato) {
        $folios = DB::table('tbl_folios as f')
            ->select(
                'c.unidad',
                'c.clave',
                'c.curso',
                'f.folio',
                'f.mod',
                'f.fecha_expedicion',
                'f.movimiento',
                'f.motivo',
                'i.matricula',
                'i.alumno',
                'c.dura',
                'c.inicio',
                'c.termino',
                'c.termino as mes',
                DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),
                'c.dia',
                'c.nombre',
                'c.muni',
                'c.depen',
                'c.efisico',
                'c.status',
                'c.tcapacitacion',
                'c.status_curso',
                'f.file_autorizacion'
            )->where('f.folio', '>', '0');
            $folios = $folios->join('tbl_inscripcion as i', function($join) {
                $join->on('f.id_curso', '=', 'i.id_curso');
                $join->on('f.matricula', '=', 'i.matricula');
            })->join('tbl_cursos as c', 'c.id', 'i.id_curso');

        switch ($campo) {
            case '1':
                $folios = $folios->where('i.curp', $dato)->orderBy('f.folio')->get();
                break;
            case '2':
                $folios = $folios->where('i.alumno', $dato)->orderBy('f.folio')->get();
                break;
            case '3':
                $folios = $folios->where('i.matricula', $dato)->orderBy('f.folio')->get();
                break;
        }
        return $folios;
    }
    
}