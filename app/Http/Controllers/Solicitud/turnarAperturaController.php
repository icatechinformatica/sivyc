<?php

namespace App\Http\Controllers\Solicitud;

use App\Events\NotificationEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use App\Models\cat\catUnidades;
use App\Models\tbl_curso;
use App\User;
use PDF;

class turnarAperturaController extends Controller
{   
    use catUnidades;
    function __construct() {
        session_start();
        $this->ejercicio = date("y");         
        $this->middleware('auth');
        $this->path_pdf = "/UNIDAD/arc01/";        
        $this->path_files = env("APP_URL").'/storage/uploadFiles';    
        $this->middleware(function ($request, $next) {
            $this->id_user = Auth::user()->id;
            $this->realizo = Auth::user()->name;  
            $this->id_unidad = Auth::user()->unidad;
            
            $this->data = $this->unidades_user('unidad');
            $_SESSION['unidades'] =  $this->data['unidades'];            
            return $next($request); 
        });
    }
    
    public function index(Request $request){
        $opt = $memo = $message = $file = NULL;
        if($request->memo)  $memo = $request->memo; 
        elseif(isset($_SESSION['memo'])) $memo = $_SESSION['memo'];

        if($request->opt)  $opt = $request->opt; 
        elseif(isset($_SESSION['opt'])) $opt = $_SESSION['opt'];

        $_SESSION['grupos'] = NULL;        
        $grupos = [];
        if($memo){            
            $grupos = DB::table('tbl_cursos as tc')->select('tc.*',DB::raw("'$opt' as option"),'ar.turnado as turnado_solicitud')->leftjoin('alumnos_registro as ar','ar.folio_grupo','tc.folio_grupo');
               if($opt == 'ARC01') $grupos = $grupos->where('tc.munidad',$memo);
               else $grupos = $grupos->where('tc.nmunidad',$memo);
               if($_SESSION['unidades']) $grupos = $grupos->whereIn('tc.unidad',$_SESSION['unidades']);
               $grupos = $grupos->groupby('tc.id','ar.turnado')->get(); 

            if(count($grupos)>0){
                if($opt == 'ARC01' AND $grupos[0]->file_arc01) $file =  $this->path_files.$grupos[0]->file_arc01;
                elseif($opt == 'ARC02' AND $grupos[0]->file_arc02) $file =  $this->path_files.$grupos[0]->file_arc02;
            }          
        }
        if(count($grupos)>0){
            $_SESSION['grupos'] = $grupos;
            $_SESSION['memo'] = $memo;
            $_SESSION['opt'] = $opt;
            
        }elseif($memo AND $opt) $message = "No se encuentran registros que mostrar.";
        //echo $file; exit;
        //var_dump($grupos);exit;
        if(session('message')) $message = session('message');
        return view('solicitud.turnar.index', compact('message','grupos','memo', 'file','opt'));
    }  
   
    public function regresar(Request $request){
       $message = 'Operación fallida, vuelva a intentar..';
        if($_SESSION['folio']){
            $result = DB::table('alumnos_registro')->where('status_curso',null)->where('turnado','UNIDAD')->where('status','NO REPORTADO')
            ->where('folio_grupo',$_SESSION['folio'])->update(['turnado' => "VINCULACION",'fecha_turnado' => date('Y-m-d')]);            
            //$_SESSION['folio'] = null;
           // unset($_SESSION['folio']);
           if($result){ 
                $message = "El grupo fué turnado correctamente a VINCULACIÓN";
                unset($_SESSION['folio']);
            }
        }
        return redirect('solicitud/apertura')->with('message',$message);
   }
   
    //  ICATECH/1300/1537/2021
    public function enviar(Request $request){
        $result = NULL;
        $titulo = ''; $cuerpo = '';
        $message = 'Operación fallida, vuelva a intentar..';

        if($_SESSION['memo']){
            if ($request->hasFile('file_autorizacion')) {               
                $name_file = $this->id_unidad."_".str_replace('/','-',$_SESSION['memo'])."_".date('ymdHis')."_".$this->id_user;                                
                $file = $request->file('file_autorizacion');
                $file_result = $this->upload_file($file,$name_file);                
                $url_file = $file_result["url_file"];
                if($file_result){

                    switch($_SESSION['opt']){
                        case "ARC01":
                            $titulo = 'Clave de Apertura';
                            $cuerpo = 'Solicitud de asignación de clave de apertura del memo '.$_SESSION['memo'];
                            $folios = array_column(json_decode(json_encode($_SESSION['grupos']), true), 'folio_grupo');
                            $alumnos = DB::table('alumnos_registro')->whereIn('folio_grupo',$folios)->update(['turnado' => "DTA",'fecha_turnado' => date('Y-m-d')]);
                            if($alumnos){
                                $result = DB::table('tbl_cursos')->where('munidad',$_SESSION['memo'])->where('status_curso',null)->where('turnado','UNIDAD')->where('status','NO REPORTADO')
                                ->update(['status_curso' => 'SOLICITADO', 'updated_at'=>date('Y-m-d H:i:s'), 'file_arc01' => $url_file]);                                
                                              
                            }else $message = "Error al turnar la solictud, volver a intentar.";
                        break;
                        case "ARC02":
                            $titulo = 'Modificación de Apertura'; 
                            $cuerpo = 'Solicitud de corrección o cancelación de apertura del memo '.$_SESSION['memo'];   
                            $result = DB::table('tbl_cursos')->where('nmunidad',$_SESSION['memo'])->where('status_curso','AUTORIZADO')->where('turnado','UNIDAD')->whereIn('status',['NO REPORTADO','RETORNO_UNIDAD'])
                            ->update(['status_curso' => 'SOLICITADO', 'updated_at'=>date('Y-m-d H:i:s'), 'file_arc02' => $url_file]);    
                            //echo $result; exit;                      
                        break;
                    }
                    if($result) {
                        $usersNotification = User::WHEREIN('id', [368,370])->get();
                        $dataCurso = tbl_curso::select('tbl_cursos.*','tbl_unidades.ubicacion as ucapacitacion')
                            ->join('tbl_unidades', 'tbl_cursos.unidad', 'tbl_unidades.unidad')
                            ->where($_SESSION['opt'] == 'ARC01' ? 'munidad' : 'nmunidad', $_SESSION['memo'])->first();
                        foreach ($usersNotification as $key => $value) {
                            $partsUnity = explode(',', $value->unidades);
                            if (!in_array($dataCurso->ucapacitacion, $partsUnity)) {
                                unset($usersNotification[$key]);
                            }
                        }

                        $dataNotificacion = [
                            'titulo' => $titulo,
                            'cuerpo' => $cuerpo,
                            'memo' => $_SESSION['memo'],
                            'unidad' => $dataCurso->unidad,
                            'url' => 'https://sivyc.icatech.gob.mx/solicitudes/aperturas ARC01 Y ARC02',
                        ];
                        event(new NotificationEvent($usersNotification, $dataNotificacion));

                        $message = "La solicitud fué turnada correctamente a la DTA"; 
                    }
                    
                }else $message = "Error al subir el archivo, volver a intentar.";
            }else $message = "Archivo inválido";
        }
        return redirect('solicitud/apertura/turnar')->with('message',$message);   
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
   
    public function pdfARC01(Request $request){
        if($request->fecha AND $request->memo){        
            $fecha_memo =  $request->fecha;
            $memo_apertura =  $request->memo;
            $fecha_memo=date('d-m-Y',strtotime($fecha_memo));

            $reg_cursos = DB::table('tbl_cursos')->SELECT('id','unidad','nombre','clave','mvalida','mod','espe','curso','inicio','termino','dia','dura',
                DB::raw("concat(hini,' A ',hfin) AS horario"),'horas','plantel','depen','muni','nota','munidad','efisico','hombre','mujer','tipo','opcion',
                'motivo','cp','ze','tcapacitacion','tipo_curso');                
            if($_SESSION['unidades'])$reg_cursos = $reg_cursos->whereIn('unidad',$_SESSION['unidades']);                
            $reg_cursos = $reg_cursos->WHERE('munidad', $memo_apertura)->orderby('espe')->get();
                
            if(count($reg_cursos)>0){     
                $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first(); 
                $reg_unidad=DB::table('tbl_unidades')->select('dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico','pvinculacion');
                if($_SESSION['unidades'])$reg_unidad = $reg_unidad->whereIn('unidad',$_SESSION['unidades']);                            
                $reg_unidad = $reg_unidad->first();            
                
                $pdf = PDF::loadView('reportes.arc01',compact('reg_cursos','reg_unidad','fecha_memo','memo_apertura','distintivo'));
                $pdf->setpaper('letter','landscape');
                return $pdf->stream('ARC01.pdf');
            }else return "MEMORANDUM NO VALIDO PARA LA UNIDAD";exit;
        }return "ACCIÓN INVÁlIDA";exit;
    }
    
    public function pdfARC02(Request $request) { 
        if($request->fecha AND $request->memo){      
            $fecha_memo =  $request->fecha;
            $memo_apertura =  $request->memo;
            $fecha_memo=date('d-m-Y',strtotime($fecha_memo));

            $reg_cursos = DB::table('tbl_cursos')->SELECT('id','unidad','nombre','clave','mvalida','mod','curso','inicio','termino','dura',
                'efisico','opcion','motivo','nmunidad','observaciones','realizo','tcapacitacion','tipo_curso');
            if($_SESSION['unidades'])$reg_cursos = $reg_cursos->whereIn('unidad',$_SESSION['unidades']);                
            $reg_cursos = $reg_cursos->WHERE('nmunidad', '=', $memo_apertura)->orderby('espe')->get();
                
            if(count($reg_cursos)>0){
                $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first(); 
               // var_dump($instituto);exit;

                $reg_unidad=DB::table('tbl_unidades')->select('unidad','dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico','pvinculacion');                
                if($_SESSION['unidades'])$reg_unidad = $reg_unidad->whereIn('unidad',$_SESSION['unidades']);           
                $reg_unidad = $reg_unidad->first();                
                    
                $pdf = PDF::loadView('reportes.arc02',compact('reg_cursos','reg_unidad','fecha_memo','memo_apertura','distintivo'));
                $pdf->setpaper('letter','landscape');
                return $pdf->stream('ARC02.pdf');
            }else return "MEMORANDUM NO VALIDO PARA LA UNIDAD";exit;   
        }
    }
   
}