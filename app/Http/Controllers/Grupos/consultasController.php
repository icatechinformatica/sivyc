<?php

namespace App\Http\Controllers\Grupos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;

class consultasController extends Controller
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
        
        $clave = $request->clave;
        $data = $message = NULL;
        $is_string =  preg_match("/^[a-z\s]+$/i", $clave); 

        $data = DB::table('tbl_cursos')->where('clave','>','0');
            if($clave){
                if($is_string) $data = $data->where('nombre','like','%'.$clave.'%');
                else $data = $data->where('clave',$clave);
            }
           if($_SESSION['unidades'])$data = $data->whereIn('unidad',$_SESSION['unidades']);                               
            $data = $data->orderby('created_at','DESC')->paginate(15);

        if(!$data) $message = "Clave inválida.";
        return view('grupos.consultas.index', compact('message','data'));     
    }  
    
    public function calificaciones(Request $request){
        $clave = $request->clave;
        $message = NULL;
        return redirect('grupos/calificaciones/buscar')->with(['message'=>$message, 'clave'=>$clave]);
    }
    
    public function folios(Request $request){
        $clave = $request->clave;        
        return redirect('grupos/asignarfolios')->with(['clave'=>$clave]);
    }
}