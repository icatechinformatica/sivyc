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

class lotesController extends Controller
{   
    function __construct() {
        session_start();
        $this->path_pdf = "/DTA/solicitud_folios/";        
        $this->path_files = env("APP_URL").'/storage/uploadFiles';
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
        
       
     //  if($request->unidad){
            $unidad = $request->unidad;
            $mod = $request->mod;
            $data = DB::table('tbl_banco_folios as f');
                if($request->mod) $data = $data->where('f.mod',$request->mod);
                if($request->unidad) $data = $data->where('f.unidad',$request->unidad);                        
                $data = $data->orderby('id','DESC')->paginate(15);
       //}
         $path_file = $this->path_files;   
        return view('consultas.lotes', compact('message','unidades','data','unidad', 'mod', 'finicial', 'ffinal', 'path_file'));      
    }  
    
    public function xls(Request $request){
        
        $unidad = $request->unidad;
            $mod = $request->mod;
            $data = DB::table('tbl_banco_folios')->select('unidad','mod','finicial','ffinal','total','contador','num_acta','facta','activo');
                if($request->mod) $data = $data->where('mod',$request->mod);
                if($request->unidad) $data = $data->where('unidad',$request->unidad);                        
                $data = $data->orderby('finicial','DESC')->get();
            
            if(count($data)==0){ return "NO REGISTROS QUE MOSTRAR";exit;}
                                
            $head = ['UNIDAD','MOD','FOLIO INICIAL','FOLIO FINAL','TOTAL','ASIGNADOS','NUM. ACTA','FECHA ACTA','PUBLICADO'];            
            $name= "ACTAS_".$unidad.".xlsx";
            $title = "ACTAS".$unidad;    
    
            if(count($data)>0)return Excel::download(new xls($data,$head, $title), $name);
             
                
       // }else return "SELECCIONE LA UNIDAD";        
    } 
    
}