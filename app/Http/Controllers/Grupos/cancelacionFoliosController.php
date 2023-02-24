<?php

namespace App\Http\Controllers\Grupos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class cancelacionfoliosController extends Controller
{   
    function __construct() {
        session_start();
        $this->motivo = ['NO SOLICITADO'=>'NO SOLICITADO','ERROR MECANOGRAFICO'=>'ERROR MECANOGRAFICO','ROBO O EXTRAVIO'=>'ROBO O EXTRAVIO','DETERIORO'=>'DETERIORO'];
        $this->path_pdf = "/UNIDAD/cancelacion_folios/";
        $this->path_files = env("APP_URL").'/storage/uploadFiles';
    }
    
    public function index(Request $request){
        $id_user = Auth::user()->id;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');        
        $_SESSION['unidades'] = $unidades = $message = $data = NULL;
        if(session('message')) $message = session('message');
        if($rol){ 
            $unidad = Auth::user()->unidad;
            $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad');
            if(count($unidades)==0) $unidades =[$unidad];       
            $_SESSION['unidades'] = $unidades;           
        }
        //var_dump($_SESSION['unidades']);exit;
        if(!$unidades ){
            $unidades = DB::table('tbl_unidades')->orderby('unidad','ASC')->pluck('unidad');
            $_SESSION['unidades'] = $unidades;   
        }
        if(session('clave')) $clave = session('clave');
        else $clave = $request->clave;
             
    
        if($clave){
                    
            $data = DB::table('tbl_inscripcion as i')
                ->select('i.id as id_inscripcion','i.matricula','i.alumno','i.reexpedicion',
                'f.id as id_folio','f.folio','f.fecha_expedicion','f.movimiento','f.motivo','c.unidad','c.clave','c.curso')
                ->where('i.status','INSCRITO')
                ->leftJoin('tbl_folios as f', function($join){                                        
                        $join->on('f.id_curso', '=', 'i.id_curso');
                        $join->on('f.matricula', '=', 'i.matricula');                            
                    })
                ->LEFTJOIN('tbl_cursos as c', 'c.id', '=', 'i.id_curso')
                ->where('c.clave',$clave);
                if($_SESSION['unidades'])$data = $data->whereIn('c.unidad',$_SESSION['unidades']); 
                if($request->matricula) $data = $data->where('i.matricula',$request->matricula);
                
                $data = $data->orderby('i.alumno','ASC')->get();
            //    var_dump($data);exit;
            if(count($data)==0) $message= "Clave inválida para la Unidad de Capacitación.";
            else $_SESSION['clave'] = $clave;  
           // echo $message; exit;
        }

        $motivo = $this->motivo;
        return view('grupos.cancelacionfolios.index', compact('message','data', 'motivo', 'clave'));
    }  
    
   
    public function store(Request $request){
        $clave = $_SESSION['clave'];
        $ids = $request->ids; //var_dump($ids);exit;
        $message = NULL;
        
        if($ids){
            $num_autorizacion = $request->num_autorizacion;
            if ($request->hasFile('file_autorizacion') AND $num_autorizacion) {
                $name_file = $clave."_".$num_autorizacion;
                $file = $request->file('file_autorizacion');
                $file_result = $this->upload_file($file,$name_file);
                //var_dump($file_result);exit;
                $url_file = $file_result["url_file"];
            }else $message = "Archivo inválido";
            
            $cancelar = DB::table('tbl_inscripcion as i')
            ->select('f.id')
            ->LEFTJOIN('tbl_folios as f', 'f.id', '=', 'i.id_folio')
            ->LEFTJOIN('tbl_cursos as c', 'c.id', '=', 'i.id_curso')
            ->where('i.status','INSCRITO')->where('c.clave',$clave)
            ->wherein('f.id',$ids)->pluck('f.id');
            
            //var_dump($cancelar);exit;
            if(count($cancelar)>0){
                 $result = DB::table('tbl_folios')->wherein('id',$cancelar)->update(
                    ['movimiento' => 'CANCELADO', 'motivo' => $request->motivo,'num_autorizacion'=>$request->num_autorizacion,'file_autorizacion'=>$url_file, 'iduser_updated' => Auth::user()->id, 'realizo'=>Auth::user()->name , 'updated_at'=>date('Y-m-d H:i:s')]
                 );             
                 if($result){
                     if($request->motivo=='ERROR MECANOGRAFICO')
                            $resultIns = DB::table('tbl_inscripcion')->wherein('id_folio',$cancelar)->update(['id_folio' => null]);
                    
                    $message = "Operación exitosa!! el registro ha sido guardado correctamente.";
                 }
            }else $message = "No existen folios que cancelar.";
        }
        return redirect('/grupos/cancelacionfolios')->with(['message'=>$message, 'clave'=>$clave]);
    }
    
    protected function upload_file($file,$name){       
        $ext = $file->getClientOriginalExtension(); // extension de la imagen
        $ext = strtolower($ext);
        $url = $mgs= null;

        if($ext == "pdf"){
            $name = trim($name.".pdf");
            $path = $this->path_pdf.$name;
            Storage::disk('custom_folder_1')->put($path, file_get_contents($file));
            //echo $url = Storage::disk('custom_folder_1')->url($path); exit;
            $msg = "El archivo ha sido cargado o reemplazado correctamente.";            
        }else $msg= "Formato de Archivo no válido, sólo PDF.";
                
        $data_file = ["message"=>$msg, 'url_file'=>$path];
       
        return $data_file;
    }
}