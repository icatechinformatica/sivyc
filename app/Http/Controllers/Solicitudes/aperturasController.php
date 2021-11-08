<?php

namespace App\Http\Controllers\Solicitudes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use App\Models\cat\catUnidades;
use PDF;

class aperturasController extends Controller
{   
    use catUnidades;
    function __construct() {
        session_start();
        $this->ejercicio = date("y");         
        $this->middleware('auth');
        $this->path_pdf = "DTA/autorizado_arc01/";
        $this->path_files = env("APP_URL").'/storage/uploadFiles';

        $this->movARC01 = ['RETORNADO'=>'RETORNAR A UNIDAD'/*,'EN FIRMA'=>'ASIGNAR CLAVES','AUTORIZADO'=>'ENVIAR AUTORIZACION'*/];
        $this->movARC02 = ['RETORNADO'=>'RETORNAR A UNIDAD'/*"CANCELADO"=>"CANCELAR APERTURA", "EN CORRECCION"=>"EN CORRECCION" ,"AUTORIZADO" => "ENVIAR AUTORIZACION"*/];

        $this->middleware(function ($request, $next) {
            $this->id_user = Auth::user()->id;
            $this->realizo = mb_strtoupper(Auth::user()->name,'utf-8');
            $this->puesto = mb_strtoupper(Auth::user()->puesto,'utf-8');

            $this->id_unidad = Auth::user()->unidad;
            
            $this->data = $this->unidades_user('unidad');
            $_SESSION['unidades'] =  $this->data['unidades'];            
            return $next($request); 
        });
    }
    
    public function index(Request $request){
        $opt = $memo = $message = $file = NULL;
        //$memo = $request->memo; 
        //$opt = $request->opt; 
        
        if($request->memo)  $memo = $request->memo; 
        elseif(isset($_SESSION['memo'])) $memo = $_SESSION['memo'];
  
        if($request->opt)  $opt = $request->opt; 
        elseif(isset($_SESSION['opt'])) $opt = $_SESSION['opt'];
        
        $_SESSION['grupos'] = NULL;        
        $grupos = $movimientos = [];
        //echo $memo;
        if($memo){            
            $grupos = DB::table('tbl_cursos as tc')->select('tc.*',DB::raw("'$opt' as option"),'ar.turnado as turnado_solicitud')
                ->leftjoin('alumnos_registro as ar','ar.folio_grupo','tc.folio_grupo');
               if($opt == 'ARC01') $grupos = $grupos->where('tc.munidad',$memo);
               else $grupos = $grupos->where('tc.nmunidad',$memo);
               $grupos = $grupos->groupby('tc.id','ar.turnado')->get();           
            //var_dump($grupos);exit;
            
            if(count($grupos)>0){
                $_SESSION['grupos'] = $grupos;
                $_SESSION['memo'] = $memo;
                $_SESSION['opt'] = $opt;
                $estatus = DB::table('tbl_cursos')->wherein('status_curso', ['SOLICITADO','EN FIRMA','AUTORIZADO']);
                if($opt == 'ARC01') $estatus = $estatus->where('munidad',$memo);
                else $estatus = $estatus->where('nmunidad',$memo);
                $estatus = $estatus->value('status_curso');
                //var_dump($estatus);exit;

                switch($opt){
                    case 'ARC01':
                        if($grupos[0]->file_arc01) $file =  $this->path_files.$grupos[0]->file_arc01;
                        switch($estatus){
                            case 'SOLICITADO':
                                $movimientos = ['' => '- SELECCIONAR -', 'RETORNADO'=>'RETORNAR A UNIDAD','EN FIRMA'=>'ASIGNAR CLAVES'];
                            break;
                            case 'EN FIRMA':
                                $movimientos = ['' => '- SELECCIONAR -', 'AUTORIZADO'=>'ENVIAR AUTORIZACION','CAMBIAR' => 'CAMBIAR MEMORÁNDUM','DESHACER'=>'DESHACER CLAVES'];
                            break;

                        }
                    break;
                    case 'ARC02':
                        if($grupos[0]->file_arc02) $file =  $this->path_files.$grupos[0]->file_arc02;                        
                        switch($estatus){
                            case 'SOLICITADO':
                                $movimientos = ['RETORNADO'=>'RETORNAR A UNIDAD','EN FIRMA'=>'EN FIRMA'];
                            break;
                            case 'EN FIRMA':
                                $movimientos = ['' => '- SELECCIONAR -', 'AUTORIZADO'=>'ENVIAR AUTORIZACION','CAMBIAR' => 'CAMBIAR MEMORÁNDUM'];
                            break;

                        }                        
                    break;
                }
            }else $message = "No se encuentran registros que mostrar.";
           
        }
        if(session('message')) $message = session('message');
        //var_dump($grupos);exit;
        return view('solicitudes.aperturas.index', compact('message','grupos','memo', 'file','opt', 'movimientos'));
    }  

    public function autorizar(Request $request){ //ENVIAR PDF DE AUTORIZACIÓN Y CAMBIAR ESTATUS A AUTORIZADO
        $result = NULL;
        $message = 'Operación fallida, vuelva a intentar..'; 
        if($_SESSION['memo'] AND $_SESSION['opt'] ){
            if ($request->hasFile('file_autorizacion')) {               
                $name_file = str_replace('/','-',$_SESSION['memo'])."_".date('ymdHis')."_".$this->id_user;                      
                $file = $request->file('file_autorizacion');
                $file_result = $this->upload_file($file,$name_file);                
                $url_file = "https://www.sivyc.icatech.gob.mx/storage/uploadFiles/".$file_result["url_file"];

                if($file_result){
                    switch($_SESSION['opt'] ){
                        case "ARC01":                      
                            $result = DB::table('tbl_cursos')->where('munidad',$_SESSION['memo'])
                            ->where('clave','<>','0')
                            ->where('turnado','UNIDAD')
                            ->where('status_curso','EN FIRMA')
                            ->where('status','NO REPORTADO')
                            ->update(['status_curso' => 'AUTORIZADO', 'updated_at'=>date('Y-m-d H:i:s'), 'pdf_curso' => $url_file]);
                        break;
                        case "ARC02": 
                            $result = DB::table('tbl_cursos')->where('nmunidad',$_SESSION['memo'])
                            ->where('clave','<>','0')
                            ->where('turnado','UNIDAD')
                            ->where('status_curso','EN FIRMA')
                            ->whereIn('status',['NO REPORTADO','RETORNO_UNIDAD'])
                            ->update(['status_curso' => 'AUTORIZADO', 'arc'=>'02','updated_at'=>date('Y-m-d H:i:s'), 'pdf_curso' => $url_file]);                                            
                        break;
                    }
                    if($result)$message = "La AUTORIZACIÓN fué enviada correctamente"; 
                    
                }else $message = "Error al subir el archivo, volver a intentar.";
            }else $message = "Archivo inválido";
        }
        return redirect('solicitudes/aperturas')->with('message',$message);        
    }

    public function cambiarmemo(Request $request){ //CAMBIAR NÚMERO DE MEMORÁNDUM Y QUIEN VALIDÓ
        $message = 'Operación fallida, vuelva a intentar..';
        if($_SESSION['memo'] AND $_SESSION['opt'] AND $request->mrespuesta AND $request->fecha ){ 
            $mrespuesta = $request->mrespuesta;
            $fecha = $request->fecha;
            switch($_SESSION['opt']){
                case "ARC01":
                        $result = DB::table('tbl_cursos')->where('munidad',$_SESSION['memo'])
                        ->where('clave','<>','0')
                        ->where('turnado','UNIDAD')
                        ->where('status_curso','EN FIRMA')
                        ->where('status','NO REPORTADO')
                        ->update([ 'mvalida' => $mrespuesta, 'fecha_apertura' => $fecha, 'valido' => $this->realizo]);                             
                        if($result)$message = "OPERACIÓN EXITOSA!!";
                break;
                case "ARC02":    
                    $result = DB::table('tbl_cursos')->where('nmunidad',$_SESSION['memo'])
                        ->where('clave','<>','0')
                        ->where('turnado','UNIDAD')
                        ->where('status_curso','EN FIRMA')
                        ->whereIn('status',['NO REPORTADO','RETORNO_UNIDAD'])
                        ->update([ 'nmacademico' => $mrespuesta, 'fecha_modificacion' => $fecha, 'valido' => $this->realizo]);                             
                        if($result)$message = "OPERACIÓN EXITOSA!!";                       
                break;
            }
        }else $message = "NO OLVIDE INGRESAR NÚMERO Y FECHA DE MEMORÁNDUM";
        return redirect('solicitudes/aperturas')->with('message',$message);        
    }
   
    public function deshacer(Request $request){ //DESHACER CLAVES
        $message = 'Operación fallida, vuelva a intentar..';
        if($_SESSION['memo'] AND $_SESSION['opt']){ 
            switch($_SESSION['opt']){
                case "ARC01":
                        $result = DB::table('tbl_cursos')->where('munidad',$_SESSION['memo'])
                        ->where('clave','<>','0')
                        ->where('turnado','UNIDAD')
                        ->where('status_curso','EN FIRMA')
                        ->where('status','NO REPORTADO')
                        ->update(['clave' => '0', 'status_curso' => 'SOLICITADO', 'mvalida' => '0','valido' => 'SIN VALIDAR']);                             
                        if($result)$message = "OPERACIÓN EXITOSA!!";
                break;
                case "ARC02":                           
                break;
            }
        }
        return redirect('solicitudes/aperturas')->with('message',$message);        
    }

    public function asignar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if($_SESSION['memo'] AND $request->mrespuesta AND $request->fecha AND $_SESSION['opt']){
            $mrespuesta = $request->mrespuesta;
            $fecha = $request->fecha;
            switch($_SESSION['opt'] ){
                case "ARC01":                    
                    $result = DB::table('tbl_cursos as tc')
                        ->select('tc.id',DB::raw("CONCAT(
                            SUBSTRING(tc.cct,LENGTH(tc.cct)-4,4)::INT*1,
                            SUBSTRING(tc.cct,LENGTH(tc.cct),1),'-',
                            SUBSTRING(EXTRACT(YEAR FROM tc.inicio)::TEXT,3,2),'-',
                            e.prefijo,'-',tc.mod
                            ) as cve"))                            
                        ->leftjoin('especialidades as e','e.id','tc.id_especialidad')                        
                        ->where('tc.clave','0')
                        ->where('tc.turnado','UNIDAD')
                        ->where('tc.status_curso','SOLICITADO')
                        ->where('tc.status','NO REPORTADO')
                        ->where('tc.munidad',$_SESSION['memo'])->orderby('termino','ASC')->orderby('hfin','ASC')
                        ->get();
                        // var_dump($result);exit;
                        foreach($result as $r){
                            $clave = DB::table('tbl_cursos')->where('clave','like',$r->cve.'%')->max('clave');
                            if($clave) $clave =  $r->cve.'-'.str_pad(intval(substr($clave,strlen($clave)-4,4))+1, 4, "0", STR_PAD_LEFT);
                            else $clave = $r->cve.'-0001'; 

                            $rest = DB::table('tbl_cursos')->where('id',$r->id)->update(['clave' => $clave, 'status_curso' => 'EN FIRMA', 'mvalida' => $mrespuesta,'fecha_apertura' => $fecha,'valido' => $this->realizo]);  
                            if($rest)$message = "Claves Asignadas Correctamente!!";
                        }
                  
                break;
                case "ARC02": 
                    $rest = DB::table('tbl_cursos')->where('nmunidad',$_SESSION['memo'])->update(['status_curso' => 'EN FIRMA', 'nmacademico' => $mrespuesta,'fecha_modificacion' => $fecha,'valido' => $this->realizo]);  
                    if($rest)$message = "Operación Exitosa!!";
                               
                break;
            }
        } else $message = "NO OLVIDE INGRESAR NÚMERO Y FECHA DE MEMORÁNDUM";
        return redirect('solicitudes/aperturas')->with('message',$message); 
    }

    public function retornar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if($_SESSION['memo']){ 
            switch($_SESSION['opt'] ){
                case "ARC01":
                    $result = DB::table('tbl_cursos')
                    ->where('clave','0')
                    ->where('turnado','UNIDAD')
                    ->where('status_curso','SOLICITADO')
                    ->where('status','NO REPORTADO')
                    ->where('munidad',$_SESSION['memo'])->update(['status_curso' => null,'updated_at'=>date('Y-m-d H:i:s')]);                    
                    if($result){ 
                        $folios = DB::table('tbl_cursos')->where('munidad',$_SESSION['memo'])->pluck('folio_grupo');     
                        //var_dump($folios);exit;           
                        $rest = DB::table('alumnos_registro')->whereIn('folio_grupo',$folios)->update(['turnado' => "UNIDAD",'fecha_turnado' => date('Y-m-d')]);   
                        if($rest)$message = "La solicitud retonado a la Unidad.";
                        unset($_SESSION['memo']);
                     }
                break;
                case "ARC02": 
                    $result = DB::table('tbl_cursos')
                    ->where('arc','02')
                    ->where('turnado','UNIDAD')
                    ->where('status_curso','SOLICITADO')
                    ->wherein('status',['NO REPORTADO','RETORNO_UNIDAD'])
                    ->where('nmunidad',$_SESSION['memo'])->update(['status_curso' => 'AUTORIZADO','updated_at'=>date('Y-m-d H:i:s')]); 
                   // echo "pasa";exit;
                    if($result)$message = "La solicitud retonado a la Unidad."; 
                    //unset($_SESSION['memo']);            
                break;
            }
        }
        return redirect('solicitudes/aperturas')->with('message',$message);        
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

    public function pdfAutoriza(Request $request){
        if($request->fecha AND $request->memo){        
            $fecha_memo =  $request->fecha;
            $memo_apertura =  $request->memo;
            $fecha_memo=date('d/M/Y',strtotime($fecha_memo));
            $opt = $request->opt;        

            $reg_cursos = DB::table('tbl_cursos')->SELECT('id','unidad','nombre','clave','mvalida','mod','espe','curso','inicio','termino','dia','dura',
                DB::raw("concat(hini,' A ',hfin) AS horario"),'horas','plantel','depen','muni','nota','munidad','nmunidad','efisico','hombre','mujer','tipo','opcion',
                'motivo','cp','ze','tcapacitacion','tipo_curso','fecha_apertura','fecha_modificacion','observaciones','valido','realizo','mvalida','nmacademico');                
            if($_SESSION['unidades'])$reg_cursos = $reg_cursos->whereIn('unidad',$_SESSION['unidades']);   
            switch($_SESSION['opt'] ){
                case "ARC01":            
                     $reg_cursos = $reg_cursos->WHERE('munidad', $memo_apertura)->orderby('espe')->get();

                break;
                case "ARC02": 
                    $reg_cursos = $reg_cursos->WHERE('nmunidad', $memo_apertura)->orderby('espe')->get();

                break;
            }

           // var_dump($reg_cursos);exit;
            if(count($reg_cursos)>0){     
                $unidad = DB::table('tbl_unidades')->where('unidad',$reg_cursos[0]->unidad)->value('ubicacion');

                $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first(); 
                $reg_unidad=DB::table('tbl_unidades')->select('dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico','pvinculacion','jcyc','dacademico','pjcyc','pdacademico','ubicacion')
                ->where('unidad',$unidad)->first();   

                if($opt=="ARC01") $opt = "ARC-01";
                else $opt = "ARC-02";
                $realizo = $this->realizo;
                $puesto = $this->puesto;
                $pdf = PDF::loadView('solicitudes.aperturas.pdfAutoriza',compact('reg_cursos','reg_unidad','fecha_memo','memo_apertura','opt','distintivo','realizo','puesto'));
                $pdf->setpaper('letter','landscape');
                return $pdf->stream('AutorizacionARC.pdf');
            }else return "MEMORANDUM NO VALIDO PARA LA UNIDAD";exit;
        }return "ACCIÓN INVÁlIDA";exit;
    }
}