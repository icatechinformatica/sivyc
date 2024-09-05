<?php

namespace App\Http\Controllers\Solicitudes;

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
use App\Models\Permission;
use App\Models\tbl_curso;
use App\User;
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
        $opt = $memo = $message = $file = $status_solicitud = $extemporaneo = $motivo_soporte = NULL;        

        if($request->memo)  $memo = $request->memo;
        elseif(isset($_SESSION['memo'])) $memo = $_SESSION['memo'];

        if($request->opt)  $opt = $request->opt;
        elseif(isset($_SESSION['opt'])) $opt = $_SESSION['opt'];

        $_SESSION['grupos'] = NULL;
        $grupos = $movimientos = [];
        //echo $memo;
        $path = $this->path_files;
        if($memo){
            $grupos = DB::table('tbl_cursos as tc')->select('convenios.fecha_vigencia','tc.*',DB::raw("'$opt' as option"),'ar.turnado as turnado_solicitud',
                'tc.comprobante_pago','e.memo_soporte_dependencia as soporte_exo','e.nrevision as rev_exo','tr.file_pdf','tr.status_folio','tr.motivo')
                ->leftjoin('alumnos_registro as ar','ar.folio_grupo','tc.folio_grupo')
                ->leftjoin('convenios','convenios.no_convenio','=','tc.cgeneral')
                ->leftJoin('exoneraciones as e','tc.mexoneracion','=','e.no_memorandum')
                ->leftJoin('tbl_recibos as tr', function ($join) {
                    $join->on('tc.folio_grupo', '=', 'tr.folio_grupo')
                         ->wherein('tr.status_folio',['ENVIADO','SOPORTE']);                             
                });
                
               if($opt == 'ARC01') $grupos = $grupos->where('tc.munidad',$memo);
               else $grupos = $grupos->where('tc.nmunidad',$memo);
               $grupos = $grupos->groupby('tc.id','ar.turnado', 'tc.comprobante_pago','convenios.fecha_vigencia',
               'e.memo_soporte_dependencia','e.nrevision','tr.file_pdf','tr.status_folio','tr.motivo')->get();

            if(count($grupos)>0){
                $_SESSION['grupos'] = $grupos;
                $_SESSION['memo'] = $memo;
                $_SESSION['opt'] = $opt;
                $estatus = DB::table('tbl_cursos')->wherein('status_curso', ['SOLICITADO','EN FIRMA','AUTORIZADO']);
                if($opt == 'ARC01'){
                    $estatus = $estatus->where('munidad',$memo);
                    $status_solicitud = $grupos[0]->status_solicitud;
                }
                else{
                    $estatus = $estatus->where('nmunidad',$memo);
                    $status_solicitud = $grupos[0]->status_solicitud_arc02;
                }
                $estatus = $estatus->value('status_curso');
                foreach ($grupos as $key => $value) {
                    if (isset($value->mextemporaneo) OR isset($value->mextemporaneo_arc02) ) {
                        $extemporaneo = true;
                    }
                    if ($value->status_folio=='SOPORTE') $motivo_soporte = true;
                }
                //var_dump($estatus);exit;
                if($grupos[0]->status_curso == 'SOPORTE'){
                    $estatus = $grupos[0]->status_curso;
                    $motivo_soporte = $grupos[0]->motivo_mov;
                }
                switch($opt){
                    case 'ARC01':
                        if($grupos[0]->file_arc01) $file =  $this->path_files.$grupos[0]->file_arc01;
                        switch($estatus){
                            case 'SOPORTE': //SOLICITUD DE CAMBIO DE SOPORTE ARC01
                                $movimientos = ['' => '- SELECCIONAR -', 'ACEPTADO ARC'=>'AUTORIZAR CAMBIO DE SOPORTE ARC','DENEGADO ARC'=>'DENEGAR REEMPLAZO DE SOPORTE ARC'];
                            break;
                            case 'SOLICITADO':
                                $movimientos = ['' => '- SELECCIONAR -', 'RETORNADO'=>'RETORNAR A UNIDAD','EN FIRMA'=>'ASIGNAR CLAVES'];
                            break;
                            case 'EN FIRMA':
                                if ($this->son_mayores($memo)) {
                                    $movimientos = ['' => '- SELECCIONAR -', 'AUTORIZADO'=>'ENVIAR AUTORIZACION','CAMBIAR' => 'CAMBIAR MEMORÁNDUM','DESHACER'=>'DESHACER CLAVES'];
                                } else {
                                    $movimientos = ['' => '- SELECCIONAR -', 'AUTORIZADO'=>'ENVIAR AUTORIZACION','CAMBIAR' => 'CAMBIAR MEMORÁNDUM'];
                                }
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
                                $movimientos = ['' => '- SELECCIONAR -', 'AUTORIZADO'=>'ENVIAR AUTORIZACIÓN','CAMBIAR' => 'CAMBIAR MEMORÁNDUM','CANCELADO'=>'ENVIAR CANCELACIÓN'];
                            break;

                        }
                    break;
                }
                if($grupos[0]->status_folio == 'SOPORTE'){  /// SOLICITUD DE CAMBIO DE RECIBO DE PAGO
                    if(!$movimientos) $movimientos []='- SELECCIONAR -';
                     $movimientos['ACEPTADO'] = 'AUTORIZAR REEMPLAZO DE SOPORTE DE PAGO';
                     $movimientos['DENEGADO'] = 'DENEGAR REEMPLAZO DE SOPORTE DE PAGO';
                }
            }else $message = "No se encuentran registros que mostrar.";
        }
       
        if(session('message')) $message = session('message');
        //var_dump($grupos);exit;
        return view('solicitudes.aperturas.index', compact('message','grupos','memo', 'file','opt', 'movimientos', 'path','status_solicitud','extemporaneo','motivo_soporte'));
    }

    public function search(Request $request){
        $_SESSION = null;
        $aperturas = DB::table('tbl_cursos as tc')
            ->select('tc.unidad','tc.num_revision','tc.munidad','tc.file_arc01','tc.turnado','tc.status_curso','tc.status_solicitud','tc.status','tc.pdf_curso','tc.fecha_apertura')
            ->leftJoin('alumnos_registro as a','tc.folio_grupo','=','a.folio_grupo')
            ->where('a.turnado','!=','VINCULACION')
            ->where(function($query) {
                $query->where('status_curso','!=',null)
                      ->orWhere('status_solicitud','=','TURNADO');
            });
        if ($request->valor) {
            $aperturas = $aperturas->where('tc.munidad',$request->valor)
                ->orWhere('tc.num_revision',$request->valor);
        }
        $aperturas = $aperturas->groupBy('tc.unidad','tc.num_revision','tc.munidad','tc.file_arc01','tc.turnado','tc.status_curso','tc.status_solicitud','tc.status','tc.pdf_curso','tc.fecha_apertura')
            ->orderBy('tc.fecha_apertura','desc')
            ->paginate(50);
        return view('solicitudes.aperturas.buzon',compact('aperturas'));
    }

    public function autorizar(Request $request){ //ENVIAR PDF DE AUTORIZACIÓN Y CAMBIAR ESTATUS A AUTORIZADO
        $result = NULL;
        $titulo = ''; $cuerpo = '';
        $message = 'Operación fallida, vuelva a intentar..';

        if($_SESSION['memo'] AND $_SESSION['opt'] ){
            if ($request->hasFile('file_autorizacion')) {
                $name_file = str_replace('/','-',$_SESSION['memo'])."_".date('ymdHis')."_".$this->id_user;
                $file = $request->file('file_autorizacion');
                $file_result = $this->upload_file($file,$name_file);
                $url_file = "https://www.sivyc.icatech.gob.mx/storage/uploadFiles/".$file_result["url_file"];

                if($file_result){
                    switch($_SESSION['opt']){
                        case "ARC01":
                            $titulo = 'Autorización de Apertura';
                            $cuerpo = 'La autorización de clave de apertura del memo '.$_SESSION['memo'].' ha sido procesada';
                            $result = DB::table('tbl_cursos')->where('munidad',$_SESSION['memo'])
                            ->where('clave','<>','0')
                            //->where('turnado','UNIDAD')
                            ->where('status_curso','EN FIRMA')
                            //->where('status','NO REPORTADO')
                            ->update(['status_curso' => 'AUTORIZADO', 'updated_at'=>date('Y-m-d H:i:s'), 'pdf_curso' => $url_file]);
                        break;
                        case "ARC02":
                            if($request->movimiento=='CANCELADO'){
                                //var_dump($_SESSION['grupos'] );

                                $folio_grupo = $_SESSION['grupos'][0]->folio_grupo;

                                //exit;
                                $titulo = 'Cancelación de apertura';
                                $cuerpo = 'La cancelación de la apertura del memo '.$_SESSION['memo'].' ha sido procesada';
                                $result = DB::table('tbl_cursos')->where('nmunidad',$_SESSION['memo'])
                                ->where('clave','<>','0')
                                ->where('turnado','UNIDAD')
                                ->where('status_curso','EN FIRMA')
                                ->whereIn('status',['NO REPORTADO','RETORNO_UNIDAD'])
                                ->update(['status_curso' => 'CANCELADO','status'=>'CANCELADO','arc'=>'02','fecha_modificacion'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s'), 'pdf_curso' => $url_file]);

                                DB::table('alumnos_registro')->where('folio_grupo',$folio_grupo)->update(['eliminado' => TRUE,'updated_at'=>date('Y-m-d H:i:s')]);
                                DB::table('tbl_inscripcion')->where('folio_grupo',$folio_grupo)->update(['activo' => FALSE,'updated_at'=>date('Y-m-d H:i:s')]);

                            }else{
                                $titulo = 'Autorización de modificación de apertura';
                                $cuerpo = 'La autorización de modificación de apertura del memo '.$_SESSION['memo'].' ha sido procesada';
                                $result = DB::table('tbl_cursos')->where('nmunidad',$_SESSION['memo'])
                                ->where('clave','<>','0')
                                ->where('turnado','UNIDAD')
                                ->where('status_curso','EN FIRMA')
                                ->whereIn('status',['NO REPORTADO','RETORNO_UNIDAD'])
                                ->update(['status_curso' => 'AUTORIZADO', 'arc'=>'02','status_solicitud_arc02'=>'AUTORIZADO','updated_at'=>date('Y-m-d H:i:s'), 'pdf_curso' => $url_file]);
                            }
                        break;
                    }
                    if($result) {
                        $url = 'https://sivyc.icatech.gob.mx/solicitud/turnar solicitud';
                        $notification = $this->getDataNotification($_SESSION['opt'], $_SESSION['memo'], $titulo, $cuerpo, $url);
                        event(new NotificationEvent($notification['users'], $notification['data']));
                        $message = "La AUTORIZACIÓN fué enviada correctamente";
                    }
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
                   $memo = $_SESSION['memo'];                    
                    if($this->son_mayores ($memo)){
                        $result = DB::table('tbl_cursos')->where('munidad',$_SESSION['memo'])
                        ->where('clave','<>','0')
                        ->where('turnado','UNIDAD')
                        ->where('status_curso','EN FIRMA')
                        ->where('status','NO REPORTADO')                        
                        ->update(['clave' => '0', 'status_curso' => 'SOLICITADO', 'mvalida' => '0','valido' => 'SIN VALIDAR']);
                        if($result)$message = "OPERACIÓN EXITOSA!!";
                    }else $message = "NO SE PERMITEN DESHACER LAS CLAVES, NO SON LAS ULTIMAS!!";
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
                         //var_dump($result);exit;
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
                    $titulo = 'Apertura retornada';
                    $cuerpo = 'La apertura con número de memo '.$_SESSION['memo'].' fue retornada a su unidad';
                    $rev = DB::table('tbl_cursos')->where('munidad',$_SESSION['memo'])->value('num_revision');
                    $result = DB::table('tbl_cursos')
                    ->where('clave','0')
                    ->where('turnado','UNIDAD')
                    ->where('status_curso','SOLICITADO')
                    ->where('status','NO REPORTADO')
                    ->where('munidad',$_SESSION['memo'])->update(['status_curso' => null,'updated_at'=>date('Y-m-d H:i:s'), 'munidad' => $rev,
                                                                'fecha_arc01'=>null,'file_arc01' => null,'status_solicitud'=>null]);
                    if($result){
                        $folios = DB::table('tbl_cursos')->where('munidad',$rev)->pluck('folio_grupo');
                        //var_dump($folios);exit;
                        $rest = DB::table('alumnos_registro')->whereIn('folio_grupo',$folios)->update(['turnado' => "UNIDAD",'fecha_turnado' => date('Y-m-d')]);
                        if($rest)$message = "La solicitud retonado a la Unidad.";
                        unset($_SESSION['memo']);
                     }
                break;
                case "ARC02":
                    $titulo = 'Modificación de Apertura';
                    $cuerpo = 'La modificación de apertura del memo '.$_SESSION['memo'].' ha sido retornada a su unidad';
                    $rev = DB::table('tbl_cursos')->where('nmunidad',$_SESSION['memo'])->value('num_revision_arc02');
                    $result = DB::table('tbl_cursos')
                    ->where('arc','02')
                    ->where('turnado','UNIDAD')
                    ->where('status_curso','SOLICITADO')
                    ->wherein('status',['NO REPORTADO','RETORNO_UNIDAD'])
                    ->where('nmunidad',$_SESSION['memo'])->update(['status_curso' => 'AUTORIZADO','updated_at'=>date('Y-m-d H:i:s'),'status_solicitud_arc02'=>null,
                                                                    'nmunidad' => $rev]);
                   // echo "pasa";exit;
                    if($result)$message = "La solicitud retonado a la Unidad.";
                    //unset($_SESSION['memo']);
                break;
            }

            /*if ($result) {
                $url = 'https://sivyc.icatech.gob.mx/solicitud/turnar solicitud';
                $notification = $this->getDataNotification($_SESSION['opt'], $_SESSION['memo'], $titulo, $cuerpo, $url);
                event(new NotificationEvent($notification['users'], $notification['data']));
            }*/
        }
        return redirect('solicitudes/aperturas')->with('message',$message);
   }

   public function soporte_pago(Request $request){ 
        $message = 'Operación fallida, vuelva a intentar..';
        if($_SESSION['memo'] AND $_SESSION['opt']){            
            switch($_SESSION['opt']){
                case "ARC01":                    
                    if($request->movimiento=='ACEPTADO ARC' OR $request->movimiento=='DENEGADO ARC'){ //SOPORTE ARC
                        if($request->movimiento=='ACEPTADO ARC'){
                             $status_curso = "ACEPTADO"; // PARA QUE LA UNIDAD PUEDA SUBIR EL ARCHIVO
                             $motivo = "AUTORIZACION CAMBIO DE SOPORTE ARC01";
                        }else{ 
                            $status_curso = "AUTORIZADO"; // SE REGRESA AL ESATO ORIGINAL PARA CERRAR 
                            $motivo = "DENEGADO CAMBIO DE SOPORTE ARC01";
                        }

                        $result = DB::table('tbl_cursos')->where('munidad',$request->memo)->where('status_curso','SOPORTE')
                        ->update(['status_curso' => $status_curso,
                            'movimientos' => DB::raw("
                                COALESCE(movimientos, '[]'::jsonb) || jsonb_build_array(
                                    jsonb_build_object(
                                        'fecha', '".date('Y-m-d H:i:s')."',
                                        'usuario', '".Auth::user()->name."',
                                        'operacion', '".$motivo."',
                                        'motivo solicitud', motivo_mov
                                    )
                                )
                            ")
                        ]); 
                        if($result) $message = "Operación exitosa!";                        
                    }else{ //SOPORTE DE PAGO
                        $memo = $_SESSION['memo'];                                        
                        $ids = DB::table('tbl_cursos as tc')->where('tc.munidad',$_SESSION['memo'])
                            ->leftjoin('tbl_recibos as tr', function ($join) {                    
                            $join->on('tc.folio_grupo','=','tr.folio_grupo')->where('tr.status_folio','SOPORTE'); 
                            })->pluck('tr.id','tr.id');
                        if($ids){
                            if($request->movimiento=='ACEPTADO'){
                                $result = DB::table('tbl_recibos')->whereIn('id',$ids)
                                    ->update(['status_folio'=>'ACEPTADO','fecha_status'=>date('Y-m-d H:i:s'),'iduser_updated'=>$this->id_user]);
                            }elseif($request->movimiento=='DENEGADO'){
                                $result = DB::table('tbl_recibos')->whereIn('id',$ids)
                                    ->update(['status_folio'=>'DENEGADO','observaciones'=>$request->observaciones,'fecha_status'=>date('Y-m-d H:i:s'),'updated_at'=> date('Y-m-d H:m:s'),'iduser_updated'=>$this->id_user]);
                            }
                            if($result)$message = "OPERACIÓN EXITOSA!!";
                            else $message = "NO SE PERMITEN DESHACER LAS CLAVES, NO SON LAS ULTIMAS!!";
                        }  
                    }                  
                break;
                case "ARC02":
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
            $marca = true;
            $reg_cursos = DB::table('tbl_cursos')->SELECT('id','unidad','nombre','clave','mvalida','mod','espe','curso','inicio','termino','dia','dura',
                DB::raw("concat(hini,' A ',hfin) AS horario"),'horas','plantel','depen','muni','nota','munidad','nmunidad','efisico','hombre','mujer','tipo','opcion',
                'motivo','cp','ze','tcapacitacion','tipo_curso','fecha_apertura','fecha_modificacion','observaciones','valido','realizo','mvalida','nmacademico',
                'status_solicitud','status_solicitud_arc02','cct');
            if($_SESSION['unidades'])$reg_cursos = $reg_cursos->whereIn('unidad',$_SESSION['unidades']);
            switch($_SESSION['opt'] ){
                case "ARC01":
                     $reg_cursos = $reg_cursos->WHERE('munidad', $memo_apertura)->orderby('espe')->get();
                     if($reg_cursos[0]->status_solicitud=="VALIDADO") $marca = false;
                break;
                case "ARC02":
                    $reg_cursos = $reg_cursos->WHERE('nmunidad', $memo_apertura)->orderby('espe')->get();
                    if($reg_cursos[0]->status_solicitud_arc02=="VALIDADO") $marca = false;
                break;
            }

           // var_dump($reg_cursos);exit;
            if(count($reg_cursos)>0){
                $unidad = DB::table('tbl_unidades')->where('unidad',$reg_cursos[0]->unidad)->value('ubicacion');

                $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first();
                $reg_unidad=DB::table('tbl_unidades')->select('dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico','pvinculacion','jcyc','dacademico','pjcyc','pdacademico','ubicacion','direccion','unidad','cct')
                ->where('unidad',$unidad)->first();

                if($opt=="ARC01") $opt = "ARC-01";
                else $opt = "ARC-02";
                
                $direccion = DB::table('tbl_instituto')->pluck('direccion')->first();
                $direccion = $direccion."Teléfono (961)6121621 Email: dtecnicaacademica@gmail.com";
                $realizo = $this->realizo;
                $puesto = $this->puesto;
                $pdf = PDF::loadView('solicitudes.aperturas.pdfAutoriza',compact('reg_cursos','reg_unidad','fecha_memo','memo_apertura','opt','distintivo','realizo','puesto','marca','direccion'));
                $pdf->setpaper('letter','landscape');
                return $pdf->stream('AutorizacionARC.pdf');
            }else return "MEMORANDUM NO VALIDO PARA LA UNIDAD";exit;
        }return "ACCIÓN INVÁlIDA";exit;
    }

    public function getDataNotification($opt, $memo, $titulo, $cuerpo, $url) {
        $usersNotification = Permission::select('u.*')->join('permission_role as pr', 'permissions.id','pr.permission_id')
                                ->join('roles as r', 'pr.role_id', 'r.id')
                                ->join('role_user as ru', 'r.id', 'ru.role_id')
                                ->join('users as u', 'ru.user_id', 'u.id')
                                ->where('u.unidades', '!=', 'null')
                                ->where('permissions.slug', 'solicitud.apertura')
                                ->get();
        $dataCurso = tbl_curso::select('tbl_cursos.*','tbl_unidades.ubicacion as ucapacitacion')
                        ->join('tbl_unidades', 'tbl_cursos.unidad', 'tbl_unidades.unidad')
                        ->where($opt == 'ARC01' ? 'munidad' : 'nmunidad', $memo)->first();
        foreach ($usersNotification as $key => $value) {
            $partsUnity = explode(',', $value->unidades);
            if (!in_array($dataCurso->ucapacitacion, $partsUnity)) {
                unset($usersNotification[$key]);
            }
        }
        $ids = [];
        foreach ($usersNotification as $value) {
            array_push($ids, $value->id);
        }
        $usersNotification = User::WHEREIN('id', $ids)->get();

        $dataNotification = [
            'titulo' => $titulo,
            'cuerpo' => $cuerpo,
            'memo' => $memo,
            'unidad' => $dataCurso->unidad,
            'url' => $url,
        ];

        return ['users' => $usersNotification, 'data' => $dataNotification];
    }

    public function validar_preliminar(Request $request){
        $memo = $request->memo;
        $opt = $request->opt;
        $message = 'Operación fallida, vuelva a intentar..';
        if ($memo AND ($opt == 'ARC01' OR $opt == 'ARC02')) {
            switch($request->pmovimiento){
                case "EDICION":
                    $result = DB::table('tbl_cursos')->where('nmunidad',$memo)->whereIn('status',['NO REPORTADO','RETORNO_UNIDAD'])->update(['status_curso' => 'EDICION']);
                    if($result)$message = "SOLICITUD ENVIADA PARA EDICION.";  
                break;
                default:
                    if ($opt == 'ARC01') {
                        $status = 'status_solicitud';
                        $llave = 'munidad';
                    }elseif ($opt == 'ARC02') {
                        $status = 'status_solicitud_arc02';
                        $llave = 'nmunidad';
                    }                    
                    $ids = array_keys($request->prespuesta);                    
                    $result = DB::table('tbl_cursos')->where($llave,$memo)->wherein('id',$ids)->update([$status => 'VALIDADO', 'obspreliminar' => null]);
                    if ($result){
                            $result2 = DB::table('tbl_cursos_history')
                                ->where($llave,$memo)
                                ->where('id_tbl_cursos',$ids)
                                ->orderBy('fenviado_preliminar', 'DESC')
                                ->take(1)
                                ->update(['status_solicitud' => 'VALIDADO', 'obspreliminar' => null, 'frespuesta_preliminar' => date('Y-m-d H:i:s')]);
                    }                    
                    if($result2)$message = "SOLICITUD ENVIADA COMO PREVALIDADA.";                    
                break;
            }
        }
      
        return redirect('solicitudes/aperturas')->with('message',$message);
    }

    public function retornar_preliminar(Request $request){

        $memo = $request->memo;
        $opt = $request->opt;
        $message = 'Operación fallida, vuelva a intentar..';
        if ($memo AND $request->prespuesta AND ($opt == 'ARC01' OR $opt == 'ARC02')) {
            if ($opt == 'ARC01') {
                $status = 'status_solicitud';
                $llave = 'munidad';
            }elseif ($opt == 'ARC02') {
                $status = 'status_solicitud_arc02';
                $llave = 'nmunidad';
            }
            foreach ($request->prespuesta as $key => $value) {
                $result = DB::table('tbl_cursos')
                    ->where($llave,$memo)
                    ->where('id',$key)
                    ->update([$status => 'RETORNO', 'obspreliminar' => $value]);
                if ($result) {
                    $result2 = DB::table('tbl_cursos_history')
                        ->where($llave,$memo)
                        ->where('id_tbl_cursos',$key)
                        ->orderBy('fenviado_preliminar', 'DESC')
                        ->take(1)
                        ->update(['status_solicitud' => 'RETORNO', 'obspreliminar' => $value, 'frespuesta_preliminar' => date('Y-m-d H:i:s')]);
                }
            }
            if ($result2) {
                $message = "La solicitud retonado a la Unidad.";
            }
        }
        return redirect('solicitudes/aperturas')->with('message',$message);
    }

    private function son_mayores($memo){
        
            $maxs = DB::table('tbl_cursos as tc')->select(DB::raw('MAX(tc.clave) as clave'))  
            ->whereIn(DB::raw('left(clave,LENGTH(clave)-4)'),function ($query)use($memo){
                $query->select(DB::raw('left(clave,LENGTH(clave)-4)'))
                    ->from('tbl_cursos')
                    ->where('munidad',$memo)
                    ->groupby(DB::raw('left(clave,LENGTH(clave)-4)'));
            })                    
        ->groupby(DB::raw('left(clave,LENGTH(clave)-4)'))
        ->pluck('clave','clave');         
        if( DB::table('tbl_cursos')->where('munidad', $memo)->whereIn('clave', $maxs)->count() == count($maxs)) return true;
        else return false;

    }

}
