<?php

namespace App\Http\Controllers\Consultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Excel\xls;
use Maatwebsite\Excel\Facades\Excel;

class cursosfinalizadosController extends Controller
{   
    function __construct() {
        session_start();        
    }
    
    public function index(Request $request){
        $id_user = Auth::user()->id;
        $message = $data = $unidad  = $fecha1 = $fecha2= NULL;
        
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');        
        $_SESSION['unidades'] = $unidades = $message = $data = NULL;
        if(session('message')) $message = session('message');
        // $rol="unidad"; 
        if($rol){ 
            $unidad = Auth::user()->unidad;
            $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad','unidad');
            if(count($unidades)==0) $unidades =[$unidad];       
            $_SESSION['unidades'] = $unidades;           
        }
       // var_dump($_SESSION['unidades']);exit;
        if(!$unidades ){
            $unidades = DB::table('tbl_unidades')->orderby('unidad','ASC')->pluck('unidad','unidad');
            $_SESSION['unidades'] = $unidades;   
        }
       
       $unidad = $request->unidad;         
       $fecha1 = $request->fecha1;
       $fecha2 = $request->fecha2;
       if($unidad OR $fecha1 OR $fecha1){                     
           $data = DB::table('tbl_cursos as c')->where('clave','!=','0');
             if($request->fecha1) $data = $data->where('c.termino','>=',$request->fecha1);
             if($request->fecha2) $data = $data->where('c.termino','<=',$request->fecha2);
             if($request->unidad) $data = $data->where('c.unidad',$request->unidad); 
             if($_SESSION['unidades'])$data = $data->whereIn('c.unidad',$_SESSION['unidades']);                       
                
           $data = $data->orderby('c.unidad')->orderby('c.termino')->get();
       }
        //var_dump($data);exit;
        return view('consultas.cursosfinalizados', compact('message','unidades','data','unidad', 'fecha1', 'fecha2'));     
    }  
    
    public function xls(Request $request){
        
        $unidad = $request->unidad;            
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        if($unidad OR $fecha1 OR $fecha1){                     
            $data = DB::table('tbl_cursos as c')->where('clave','!=','0');
            if($request->fecha1) $data = $data->where('c.termino','>=',$request->fecha1);
            if($request->fecha2) $data = $data->where('c.termino','<=',$request->fecha2);
            if($request->unidad) $data = $data->where('c.unidad',$request->unidad);                        
            if($_SESSION['unidades'])$data = $data->whereIn('c.unidad',$_SESSION['unidades']);
              
            $data = $data->orderby('c.termino')->get();
                
            if(count($data)==0){ return "NO REGISTROS QUE MOSTRAR";exit;}
                                    
            $head = ['UNIDAD','CLAVE','CURSO','MOD','INICIO','TERMINO'];            
            $name= "CURSOS_TERMINADOS_".$unidad.".xlsx";
            $title = "CURSOS_TERMINADOS_".$unidad;    
        
            if(count($data)>0)return Excel::download(new xls($data,$head, $title), $name);
             
        }else echo "Seleccione un rango de fecha";
                
    } 
    
}