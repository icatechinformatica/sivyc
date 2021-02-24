<?php

namespace App\Http\Controllers\Grupos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;

class calificacionesController extends Controller
{   
    function __construct() {
        session_start();
    }
    
    public function index(Request $request){
        $id_user = Auth::user()->id;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');        
        $_SESSION['unidades']=NULL;
        //var_dump($rol);exit;
        if($rol){ 
            $unidad = Auth::user()->unidad;
            $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad');        
            if(count($unidades)==0) $unidades =[$unidad];       
            $_SESSION['unidades'] = $unidades;              
        }
        //var_dump($_SESSION['unidades']);exit;
        $message = NULL;
        return view('grupos.calificaciones.index', compact('message'));     
    }  
    
    public function search(Request $request){
        $clave = $request->clave;
        $curso = $alumnos = $message = NULL;
        if(session('clave')) $clave = session('clave');
        if(session('message')) $message = session('message');

        if($clave){          
            $curso = DB::table('tbl_cursos')->where('clave',$clave);
                if($_SESSION['unidades'])$curso = $curso->whereIn('unidad',$_SESSION['unidades']);                               
                $curso = $curso->first();
                
           if($curso->turnado == "UNIDAD") $alumnos = DB::table('tbl_inscripcion')->select('id','matricula','alumno','calificacion')->where('id_curso',$curso->id)->orderby('alumno')->get();
           elseif($curso) $message = "Información del Curso fué turnado a $curso->turnado y con estatus de $curso->status .";// .$curso->turnado;
           else $message = "Clave inválida.";
           if(count($alumnos)==0 AND !$message) $message = "El curso no tiene alumnos registrados. ";
        }
        
        //var_dump($alumnos); exit;    
       
        return  view('grupos.calificaciones.index', compact('curso','alumnos','message')); 
    } 
    
      public function update(Request $request) {
        //var_dump($request->calificacion);exit;
        $message = NULL;
        $clave = $request->clave;
        if($request->calificacion ){
            foreach($request->calificacion as $key=>$val){
                if(!is_numeric($val) OR $val<6 )  $val = "NP";
            $result = DB::table('tbl_inscripcion')->where('id', $key)->update(['calificacion' => $val]);
            }
            if($result) $message = "Operacion exitosa!!";        
        }else $message = "No existen cambios que guardar.";
        //echo $message; exit;
        return redirect('grupos/calificaciones/buscar')->with(['message'=>$message, 'clave'=>$clave]);
    } 
}