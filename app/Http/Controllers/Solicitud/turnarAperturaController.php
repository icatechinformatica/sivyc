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
use Carbon\Carbon;

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
        $opt = $memo = $message = $file = $extemporaneo = $status_solicitud = $num_revision = NULL;
        if($request->memo)  $memo = $request->memo; 
        elseif(isset($_SESSION['memo'])) $memo = $_SESSION['memo'];

        if($request->opt)  $opt = $request->opt; 
        elseif(isset($_SESSION['opt'])) $opt = $_SESSION['opt'];

        $_SESSION['grupos'] = NULL;        
        $grupos = $mextemporaneo = [];
        if($memo){            
            $grupos = DB::table('tbl_cursos as tc')->select(db::raw("(select sum(hours) from 
			(select ( (( EXTRACT(EPOCH FROM cast(agenda.end as time))-EXTRACT(EPOCH FROM cast(start as time)))/3600)*
			 ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) ) 
			 as hours 
 			from agenda
			where id_curso = tc.folio_grupo) as t) as horas_agenda"),
                                                            'tc.*',DB::raw("'$opt' as option"),'ar.turnado as turnado_solicitud',
                                                            DB::raw("date(tc.termino + cast('14 days' as interval)) as soltermino"),
                                                            DB::raw("date(tc.inicio + cast('2 days' as interval)) as extemporaneo"))
                                                            ->leftjoin('alumnos_registro as ar','ar.folio_grupo','tc.folio_grupo');
                if($opt == 'ARC01'){ 
                   $grupos = $grupos->whereRaw("(tc.num_revision = '$memo' OR (tc.munidad = '$memo'))");
                   //->where('tc.munidad',$memo);
                }else{ 
                   $grupos = $grupos->whereRaw("(tc.num_revision_arc02 = '$memo' OR (tc.nmunidad = '$memo'))");
                   //->where('tc.nmunidad',$memo);
                }
                if($_SESSION['unidades']){ 
                   $grupos = $grupos->whereIn('tc.unidad',$_SESSION['unidades']);
                }
                $grupos = $grupos->groupby('tc.id','ar.turnado')->get();

            if(count($grupos)>0){
                if($opt == 'ARC01' AND $grupos[0]->file_arc01) $file =  $this->path_files.$grupos[0]->file_arc01;
                elseif($opt == 'ARC02' AND $grupos[0]->file_arc02) $file =  $this->path_files.$grupos[0]->file_arc02;
                foreach ($grupos as $grupo) {
                    if ($opt == 'ARC01' AND ($grupo->extemporaneo < date('Y-m-d'))) {
                        $extemporaneo = true;
                    } elseif ($opt == 'ARC02') {
                        $interval = (Carbon::parse($grupo->termino)->diffInDays($grupo->inicio))/2; 
                        $interval = intval(ceil($interval));
                        $interval = (Carbon::parse($grupo->inicio)->addDay($interval))->format('Y-m-d');
                        $i = date($interval);
                        if ($i < date('Y-m-d')) {
                            $extemporaneo = true;
                        }
                    }
                }
                if ($opt == 'ARC01') {
                    $mextemporaneo = ['VALIDACION VENCIDA DEL INSTRUCTOR'=>'VALIDACION VENCIDA DEL INSTRUCTOR','REQUISITOS FALTANTES'=>'REQUISITOS FALTANTES',
                                    'ERROR MECANOGRAFICO'=>'ERROR MECANOGRAFICO','SOLICITUD DE LA DEPENDENCIA'=>'SOLICITUD DE LA DEPENDENCIA',
                                    'ACTUALIZACION DE PAQUETERIA DIDACTICA'=>'ACTUALIZACION DE PAQUETERIA DIDACTICA'];
                    $status_solicitud = $grupos[0]->status_solicitud;
                    $num_revision = $grupos[0]->num_revision;
                } else if ($opt == 'ARC02') {
                    $mextemporaneo = ['OBSERVACIONES DE FINANCIEROS'=>'OBSERVACIONES DE FINANCIEROS','ERROR MECANOGRAFICO'=>'ERROR MECANOGRAFICO',
                                        'TRAMITES ADMINISTRATIVOS'=>'TRAMITES ADMINISTRATIVOS'];
                    $status_solicitud = $grupos[0]->status_solicitud_arc02;
                    $num_revision = $grupos[0]->num_revision_arc02;
                }
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
        return view('solicitud.turnar.index', compact('message','grupos','memo', 'file','opt','extemporaneo','mextemporaneo','status_solicitud','num_revision'));
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
        $result = $extemporaneo = NULL;
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
                            $cursos = DB::table('tbl_cursos')->select('tbl_cursos.*',DB::raw("date(tbl_cursos.inicio + cast('2 days' as interval)) as extemporaneo"))->where('munidad',$_SESSION['memo'])->get();
                            foreach ($cursos as $key => $value) {
                                if ($value->fecha_arc01 == null) {
                                    $message = "La fecha del arc 01 no se ha generado, genere el memorandum pdf.";
                                    return redirect('solicitud/apertura/turnar')->with('message',$message);
                                }
                                if ( $value->extemporaneo < date('Y-m-d')) {
                                    foreach ($request->motivo as $m => $motivo) {
                                        if (($value->id == $m) AND ($motivo == null)) {
                                            $message = "Seleccione el motivo extemporaneo.";
                                            return redirect('solicitud/apertura/turnar')->with('message',$message);
                                        }else {
                                            $extemporaneo = true;
                                        }
                                    }
                                }
                            }
                            $titulo = 'Clave de Apertura';
                            $cuerpo = 'Solicitud de asignación de clave de apertura del memo '.$_SESSION['memo'];
                            $folios = array_column(json_decode(json_encode($_SESSION['grupos']), true), 'folio_grupo');
                            $alumnos = DB::table('alumnos_registro')->whereIn('folio_grupo',$folios)->update(['turnado' => "DTA",'fecha_turnado' => date('Y-m-d')]);
                            if($alumnos){
                                $result = DB::table('tbl_cursos')->where('munidad',$_SESSION['memo'])->where('status_curso',null)->where('turnado','UNIDAD')->where('status','NO REPORTADO')
                                ->update(['status_curso' => 'SOLICITADO', 'updated_at'=>date('Y-m-d H:i:s'), 'file_arc01' => $url_file]); 
                                if ($result) {
                                    if ($extemporaneo) {
                                        foreach ($request->motivo as $key => $value) {
                                            if ($value != null) {
                                                $respuesta = null;
                                                foreach ($request->mrespuesta as $i => $x) {
                                                    if ($i == $key) {
                                                        $respuesta = $x;
                                                    }
                                                }
                                                $result2 = DB::table('tbl_cursos')->where('munidad',$_SESSION['memo'])->where('id',$key)->where('turnado','UNIDAD')->where('status','NO REPORTADO')
                                                                ->update(['mextemporaneo' => $value, 'rextemporaneo'=>$respuesta]);
                                            }
                                        }
                                    }
                                }                               
                                              
                            }else $message = "Error al turnar la solictud, volver a intentar.";
                        break;
                        case "ARC02":
                            $cursos = DB::table('tbl_cursos')->select('tbl_cursos.*')->where('nmunidad',$_SESSION['memo'])->get();
                            foreach ($cursos as $key => $value) {
                                $interval = (Carbon::parse($value->termino)->diffInDays($value->inicio))/2;
                                $interval = intval(ceil($interval)); 
                                $interval = (Carbon::parse($value->inicio)->addDay($interval))->format('Y-m-d');
                                $i = date($interval);
                                if ($i < date('Y-m-d')) {
                                    foreach ($request->motivo as $m => $motivo) {
                                        if (($value->id == $m) AND ($motivo == null)) {
                                            $message = "Seleccione el motivo extemporaneo.";
                                            return redirect('solicitud/apertura/turnar')->with('message',$message);
                                        }else {
                                            $extemporaneo = true;
                                        }
                                    }
                                }
                            }
                            $titulo = 'Modificación de Apertura'; 
                            $cuerpo = 'Solicitud de corrección o cancelación de apertura del memo '.$_SESSION['memo'];   
                            $result = DB::table('tbl_cursos')->where('nmunidad',$_SESSION['memo'])->where('status_curso','AUTORIZADO')->where('turnado','UNIDAD')->whereIn('status',['NO REPORTADO','RETORNO_UNIDAD'])
                            ->update(['status_curso' => 'SOLICITADO', 'updated_at'=>date('Y-m-d H:i:s'), 'file_arc02' => $url_file]);    
                            //echo $result; exit; 
                            if ($extemporaneo) {
                                foreach ($request->motivo as $key => $value) {
                                    if ($value != null) {
                                        $respuesta = null;
                                        foreach ($request->mrespuesta as $i => $x) {
                                            if ($i == $key) {
                                                $respuesta = $x;
                                            }
                                        }
                                        $result2 = DB::table('tbl_cursos')->where('nmunidad',$_SESSION['memo'])->where('id',$key)->where('turnado','UNIDAD')->whereIn('status',['NO REPORTADO','RETORNO_UNIDAD'])
                                                        ->update(['mextemporaneo_arc02' => $value, 'rextemporaneo_arc02'=>$respuesta]);
                                    }
                                }
                            }                     
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

   public function preliminar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if ($request->opt) {
            $opt = $request->opt;
            if ($opt == 'ARC01' OR $opt == 'ARC02') {
                if ($opt == 'ARC01') {
                    $status = 'status_solicitud';
                    $memo = 'tc.munidad';
                    $url = 'tc.file_arc01';
                } elseif ($opt == 'ARC02') {
                    $status = 'status_solicitud_arc02';
                    $memo = 'tc.nmunidad';
                    $url = 'tc.file_arc02';
                }
                $url_file = null;
                if ($request->hasFile('file_autorizacion')) {
                    $name_file = $this->id_unidad."_".str_replace('/','-',$request->memo)."_".date('ymdHis')."_".$this->id_user;                                
                    $file = $request->file('file_autorizacion');
                    $file_result = $this->upload_rfile($file,$name_file);                
                    $url_file = $file_result["url_file"];
                }
                $result = DB::table('tbl_cursos as tc')->where($memo,$request->memo)->update([$status=>'TURNADO',$url=>$url_file]);
                $cursos = DB::table('tbl_cursos as tc')
                    ->select('tc.*')
                    ->where($memo,$request->memo)
                    ->get();
                if ($result) {
                    foreach ($cursos as $key => $value) {
                        $update = DB::table('tbl_cursos_history')->insert([
                            'status_solicitud' => 'TURNADO',
                            'fenviado_preliminar' => date('Y-m-d H:i:s'),
                            'num_revision' => $request->nmemo,
                            'id_tbl_cursos' => $value->id,
                            'cct' => $value->cct,
                            'unidad' => $value->unidad,
                            'nombre' => $value->nombre,
                            'curp' => $value->curp,
                            'rfc' => $value->rfc,
                            'clave' => $value->clave,
                            'mvalida' => $value->mvalida,
                            'mod' => $value->mod,
                            'area' => $value->area,
                            'espe' => $value->espe,
                            'curso' =>  $value->curso,
                            'inicio' => $value->inicio,
                            'termino' => $value->termino,
                            'dia' => $value->dia,
                            'dura' => $value->dura,
                            'hini' => $value->hini,
                            'hfin' => $value->hfin,
                            'horas' => $value->horas,
                            'ciclo' => $value->ciclo,
                            'plantel' => $value->plantel,
                            'depen' => $value->depen,
                            'muni' => $value->muni,
                            'sector' => $value->sector,
                            'programa' => $value->programa,
                            'nota' => $value->nota,
                            'munidad' => $value->munidad,
                            'efisico' => $value->efisico,
                            'cespecifico' => $value->cespecifico,
                            'mpaqueteria' => $value->mpaqueteria,
                            'mexoneracion' => $value->mexoneracion,
                            'hombre' => $value->hombre,
                            'mujer' => $value->mujer,
                            'tipo' => $value->tipo,
                            'fcespe' => $value->fcespe,
                            'cgeneral' => $value->cgeneral,
                            'opcion' => $value->opcion,
                            'motivo' => $value->motivo,
                            'cp' => $value->cp,
                            'ze' => $value->ze,
                            'created_at' => $value->created_at,
                            'updated_at' => $value->updated_at,
                            'id_curso' => $value->id_curso,
                            'id_instructor' => $value->id_instructor,
                            'modinstructor' => $value->modinstructor,
                            'nmunidad' => $value->nmunidad,
                            'nmacademico' => $value->nmacademico,
                            'observaciones' => $value->observaciones,
                            'status' => $value->status,
                            'realizo' => $value->realizo,
                            'valido' => $value->valido,
                            'arc' => $value->arc,
                            'tcapacitacion' => $value->tcapacitacion,
                            'status_curso' => $value->status_curso,
                            'fecha_apertura' => $value->fecha_apertura,
                            'fecha_modificacion' => $value->fecha_modificacion,
                            'costo' => $value->costo,
                            'motivo_correccion' => $value->motivo_correccion,
                            'pdf_curso' => $value->pdf_curso,
                            'json_supervision' => $value->json_supervision,
                            'memos' => $value->memos,
                            'observaciones_formato_t' => $value->observaciones_formato_t,
                            'fecha_turnado' => $value->fecha_turnado,
                            'turnado' => $value->turnado,
                            'proceso_terminado' => $value->proceso_terminado,
                            'tipo_curso' => $value->tipo_curso,
                            'fecha_envio' => $value->fecha_envio,
                            'id_especialidad' => $value->id_especialidad,
                            'instructor_escolaridad' => $value->instructor_escolaridad,
                            'instructor_titulo' => $value->instructor_titulo,
                            'instructor_sexo' => $value->instructor_sexo,
                            'instructor_mespecialidad' => $value->instructor_mespecialidad,
                            'medio_virtual' => $value->medio_virtual,
                            'folio_grupo' => $value->folio_grupo,
                            'id_municipio' => $value->id_municipio,
                            'link_virtual' => $value->link_virtual,
                            'clave_especialidad' => $value->clave_especialidad,
                            'file_arc01' => $value->file_arc01,
                            'file_arc02' => $value->file_arc02,
                            'mov_arc02' => $value->mov_arc02,
                            'id_cerss' => $value->id_cerss,
                            'tdias' => $value->tdias,
                            'asis_finalizado' => $value->asis_finalizado,
                            'calif_finalizado' => $value->calif_finalizado,
                            'clave_localidad' => $value->clave_localidad,
                            'id_gvulnerable' => $value->id_gvulnerable,
                            'fecha_arc01' => $value->fecha_arc01,
                            'fecha_arc02' => $value->fecha_arc02,
                            'instructor_tipo_identificacion' => $value->instructor_tipo_identificacion,
                            'instructor_folio_identificacion' => $value->instructor_folio_identificacion
                        ]);
                    }
                    $message = "La solicitud preliminar fué turnada correctamente a la DTA";
                }
            } else {
                $message = "Acción inválida";
            }
            
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

    protected function upload_rfile($file,$name){       
        $ext = $file->getClientOriginalExtension(); // extension de la imagen
        $ext = strtolower($ext);
        $url = $mgs= null;

        if($ext == "pdf"){
            $name = trim($name.".pdf");
            $path = "/UNIDAD/revision_arc01/".$name;
            Storage::disk('custom_folder_1')->put($path, file_get_contents($file));
            //echo $url = Storage::disk('custom_folder_1')->url($path); exit;
            $msg = "El archivo ha sido cargado o reemplazado correctamente.";            
        }else $msg= "Formato de Archivo no válido, sólo PDF.";
                
        $data_file = ["message"=>$msg, 'url_file'=>$path];
       
        return $data_file;
    }
   
    public function pdfARC01(Request $request){
        if($request->fecha AND $request->memo){ 
            $marca = null;       
            //$fecha_memo =  $request->fecha;
            $memo_apertura =  $request->memo;
            //$fecha_memo=date('d-m-Y',strtotime($fecha_memo));

            $reg_cursos = DB::table('tbl_cursos')->SELECT('id','unidad','nombre','clave','mvalida','mod','espe','curso','inicio','termino','dia','dura',
                DB::raw("concat(hini,' A ',hfin) AS horario"),'horas','plantel','depen','muni','nota','munidad','efisico','hombre','mujer','tipo','opcion',
                'motivo','cp','ze','tcapacitacion','tipo_curso','fecha_arc01');                
            if($_SESSION['unidades'])$reg_cursos = $reg_cursos->whereIn('unidad',$_SESSION['unidades']);                
            $reg_cursos = $reg_cursos->WHERE('munidad', $memo_apertura)->orderby('espe')->get();
                
            if(count($reg_cursos)>0){   
                foreach ($reg_cursos as $value) {
                    if (!$value->fecha_arc01) {
                        $result = DB::table('tbl_cursos')->where('munidad',$memo_apertura)->update(['fecha_arc01'=>$request->fecha]);
                    }
                }  
                $fecha_memo = DB::table('tbl_cursos')->where('munidad',$memo_apertura)->pluck('fecha_arc01')->first();
                $fecha_memo=date('d-m-Y',strtotime($fecha_memo));
                $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first(); 
                $reg_unidad=DB::table('tbl_unidades')->select('dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico','pvinculacion');
                if($_SESSION['unidades'])$reg_unidad = $reg_unidad->whereIn('unidad',$_SESSION['unidades']);                            
                $reg_unidad = $reg_unidad->first();            
                
                $pdf = PDF::loadView('reportes.arc01',compact('reg_cursos','reg_unidad','fecha_memo','memo_apertura','distintivo','marca'));
                $pdf->setpaper('letter','landscape');
                return $pdf->stream('ARC01.pdf');
            }else return "MEMORANDUM NO VALIDO PARA LA UNIDAD";exit;
        }return "ACCIÓN INVÁlIDA";exit;
    }
    
    public function pdfARC02(Request $request) { 
        if($request->fecha AND $request->memo){  
            $marca = null;    
            //$fecha_memo =  $request->fecha;
            $memo_apertura =  $request->memo;
            //$fecha_memo=date('d-m-Y',strtotime($fecha_memo));

            $reg_cursos = DB::table('tbl_cursos')->SELECT('id','unidad','nombre','clave','mvalida','mod','curso','inicio','termino','dura',
                'efisico','opcion','motivo','nmunidad','observaciones','realizo','tcapacitacion','tipo_curso','fecha_arc02');
            if($_SESSION['unidades'])$reg_cursos = $reg_cursos->whereIn('unidad',$_SESSION['unidades']);                
            $reg_cursos = $reg_cursos->WHERE('nmunidad', '=', $memo_apertura)->orderby('espe')->get();
                
            if(count($reg_cursos)>0){
                if (!$reg_cursos[0]->fecha_arc02) {
                    $result = DB::table('tbl_cursos')->where('nmunidad',$memo_apertura)->update(['fecha_arc02'=>$request->fecha]);
                }  
                $fecha_memo = DB::table('tbl_cursos')->where('nmunidad',$memo_apertura)->pluck('fecha_arc02')->first();
                $fecha_memo=date('d-m-Y',strtotime($fecha_memo));
                $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first(); 
               // var_dump($instituto);exit;

                $reg_unidad=DB::table('tbl_unidades')->select('unidad','dunidad','academico','vinculacion','dacademico','pdacademico','pdunidad','pacademico','pvinculacion');                
                if($_SESSION['unidades'])$reg_unidad = $reg_unidad->whereIn('unidad',$_SESSION['unidades']);           
                $reg_unidad = $reg_unidad->first();                
                    
                $pdf = PDF::loadView('reportes.arc02',compact('reg_cursos','reg_unidad','fecha_memo','memo_apertura','distintivo','marca'));
                $pdf->setpaper('letter','landscape');
                return $pdf->stream('ARC02.pdf');
            }else return "MEMORANDUM NO VALIDO PARA LA UNIDAD";exit;   
        }
    }

    public function cambiar_memorandum(Request $request){
         //dd($request->all());
        $message = "Ingrese el número de memorándum";
        if ($request->memo AND $request->nmemo AND $request->opt) {
            if ($request->opt === 'ARC01') {
                if ((DB::table('tbl_cursos')->where('munidad',$request->nmemo)->value('id'))) {
                    $message = "El memorándum ya se encuentra en uso..";
                } else {
                    $r = DB::table('tbl_cursos')->where('num_revision',$request->memo)->orWhere('munidad',$request->memo)->value('num_revision');
                    $result = DB::table('tbl_cursos')->where('num_revision',$r)->update(['munidad' => $request->nmemo]);
                    if ($result) {
                        $result2 = DB::table('tbl_cursos_history')->where('num_revision',$request->memo)->update(['munidad' => $request->nmemo]);
                        $_SESSION['memo'] = $request->nmemo;
                        $message = "El Guardado del Memorándum fué exitoso";
                    }else{
                        $message = "Operación fallida, vuelva a intentar..";
                    }
                } 
            }else {
                if ((DB::table('tbl_cursos')->where('nmunidad',$request->nmemo)->value('id'))) {
                    $message = "El memorándum ya se encuentra en uso..";
                } else {
                    $r = DB::table('tbl_cursos')->where('num_revision_arc02',$request->memo)->orWhere('nmunidad',$request->memo)->value('num_revision_arc02');
                    $result = DB::table('tbl_cursos')->where('num_revision_arc02',$r)->update(['nmunidad' => $request->nmemo]);
                    if ($result) {
                        $result2 = DB::table('tbl_cursos_history')->where('num_revision',$request->memo)->update(['nmunidad' => $request->nmemo]);
                        $_SESSION['memo'] = $request->nmemo;
                        $message = "El Guardado del Memorándum fué exitoso";
                    }else{
                        $message = "Operación fallida, vuelva a intentar..";
                    }
                }
            }
        }
        return redirect('solicitud/apertura/turnar')->with('message',$message); 
    }
   
}