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
use Maatwebsite\Excel\Facades\Excel;

class foliosController extends Controller
{   
    function __construct() {
        session_start();
    }
    
    public function index(Request $request){
        $id_user = Auth::user()->id;
        $message = $folios = $unidad = $mod = $finicial = $ffinal= NULL;
        
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');        
        $_SESSION['unidades'] = $unidades = $message = $data = NULL;
        if(session('message')) $message = session('message');
        if($rol){ 
            $unidad = Auth::user()->unidad;
            $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad','unidad');
            if(count($unidades)==0) $unidades =[$unidad];       
            $_SESSION['unidades'] = $unidades;           
        }
        //var_dump($_SESSION['unidades']);exit;
        if(!$unidades ){
            $unidades = DB::table('tbl_unidades')->orderby('unidad','ASC')->pluck('unidad','unidad');
            $_SESSION['unidades'] = $unidades;   
        }
        
       
       if($request->unidad){
            $unidad = $request->unidad;
            $mod = $request->mod;
            $finicial = $request->finicial;
            $ffinal = $request->ffinal;
                            
            $folios = DB::table('tbl_folios as f')->select('c.unidad','f.folio','f.mod','f.fecha_expedicion','f.movimiento','f.motivo','i.matricula','i.alumno','c.clave','c.curso')->where('f.folio','>','0');
                if($request->mod) $folios = $folios->where('f.mod',$request->mod);
                if($request->finicial) $folios = $folios->where('f.folio','>=',$request->finicial);
                if($request->ffinal) $folios = $folios->where('f.folio','<=',$request->ffinal);
                if($request->unidad) $folios = $folios->where('c.unidad',$request->unidad);                        
                $folios = $folios
                ->Join('tbl_inscripcion as i', function($join){                                        
                $join->on('f.id_curso', '=', 'i.id_curso');
                $join->on('f.matricula', '=', 'i.matricula');                            
                })
                ->join('tbl_cursos as c','c.id','i.id_curso')->orderby('f.folio')->get();
       }
        
        return view('consultas.folios', compact('message','unidades','folios','unidad', 'mod', 'finicial', 'ffinal'));     
    }  
    
    public function xls(Request $request){
        
        $unidad = $request->unidad;
        $mod = $request->mod;
        $finicial = $request->finicial;
        $ffinal = $request->ffinal;
        if($unidad){                    
            $folios = DB::table('tbl_folios as f')->select('f.unidad','f.folio','f.mod','f.fecha_expedicion','f.movimiento','f.motivo','i.matricula','i.alumno','c.clave','c.curso')->where('f.folio','>','0');
                if($request->mod) $folios = $folios->where('f.mod',$request->mod);
                if($request->finicial) $folios = $folios->where('f.folio','>=',$request->finicial);
                if($request->ffinal) $folios = $folios->where('f.folio','<=',$request->ffinal);
                if($request->unidad) $folios = $folios->where('f.unidad',$request->unidad);                        
                $folios = $folios
                ->Join('tbl_inscripcion as i', function($join){                                        
                $join->on('f.id_curso', '=', 'i.id_curso');
                $join->on('f.matricula', '=', 'i.matricula');                            
                })
                ->join('tbl_cursos as c','c.id','i.id_curso')->orderby('f.folio')->get();
            
            if(count($folios)==0){ return "NO REGISTROS QUE MOSTRAR";exit;}
                                
            $head = ['UNIDAD','FOLIO','MOD','EXPEDICION','ESTATUS','MOTIVO','MATRICULA','ALUMNO','CLAVE','CURSO'];            
            $name= "FOLIOS_ASIGNADOS_".$unidad.".xlsx";
            $title = "FOLIOS_ASIGANDOS_".$unidad;    
    
            if(count($folios)>0)return Excel::download(new xlsFoliosAsignados($folios,$head, $title), $name);
             
                
        }else return "SELECCIONE LA UNIDAD";        
    } 
    
}