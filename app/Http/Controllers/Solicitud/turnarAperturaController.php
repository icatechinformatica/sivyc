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
        $movimientos = [];
        if($request->memo)  $memo = $request->memo; 
        elseif(isset($_SESSION['memo'])) $memo = $_SESSION['memo'];

        if($request->opt)  $opt = $request->opt; 
        elseif(isset($_SESSION['opt'])) $opt = $_SESSION['opt'];

        $_SESSION['grupos'] = NULL;        
        $grupos = $mextemporaneo = [];
        $ids_extemp = []; 
        if($memo){     
            $grupos = DB::table('tbl_cursos as tc')->select(db::raw("(select sum(hours) from 
			    (select ( (( EXTRACT(EPOCH FROM cast(agenda.end as time))-EXTRACT(EPOCH FROM cast(start as time)))/3600)*
			    ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) ) 
			        as hours 
 			        from agenda
			        where id_curso = tc.folio_grupo) as t) as horas_agenda"),
                    'tc.*',DB::raw("'$opt' as option"),'ar.turnado as turnado_solicitud',
                    DB::raw("date(tc.termino + cast('14 days' as interval)) as soltermino"),'tr.status_folio')
                    ->leftjoin('alumnos_registro as ar','ar.folio_grupo','tc.folio_grupo')
                    ->leftJoin('tbl_recibos as tr', function ($join) {
                        $join->on('tc.folio_grupo', '=', 'tr.folio_grupo')
                             ->where('tr.status_folio','ENVIADO');                             
                    });
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
                $grupos = $grupos->groupby('tc.id','ar.turnado', 'tr.status_folio')->get();

            if(count($grupos)>0){             
                              
                if ($opt == 'ARC01') {
                    if($grupos[0]->file_arc01) $file =  $this->path_files.$grupos[0]->file_arc01;
                    $status_solicitud = $grupos[0]->status_solicitud;
                    $num_revision = $grupos[0]->num_revision;
                    if(isset($_SESSION['memo']))$ids_extemp = $this->ids_extemp($_SESSION['memo']);
                                       
                    if(count($ids_extemp)>0){
                        $extemporaneo = true;
                        $mextemporaneo = ['VALIDACION VENCIDA DEL INSTRUCTOR'=>'VALIDACION VENCIDA DEL INSTRUCTOR','REQUISITOS FALTANTES'=>'REQUISITOS FALTANTES',
                             'ERROR MECANOGRAFICO'=>'ERROR MECANOGRAFICO','SOLICITUD DE LA DEPENDENCIA'=>'SOLICITUD DE LA DEPENDENCIA',
                             'ACTUALIZACION DE PAQUETERIA DIDACTICA'=>'ACTUALIZACION DE PAQUETERIA DIDACTICA'];
                    }
                    
                    ///MOVIMIENTO ADICIONALES DESPUES DE AUTORIZADO
                    switch($grupos[0]->status_curso){
                        case 'AUTORIZADO':
                            $movimientos = [ 'SOPORTE' => 'SOLICITUD DE CAMBIO DE SOPORTE'];
                        break;
                        case 'ACEPTADO'://PARA SUBIR ARCHIVO
                            $movimientos = [ 'SUBIR' => 'SUBIR SOPORTE'];
                        break;
                    }
                     
                } elseif ($opt == 'ARC02') {
                    if($grupos[0]->file_arc02) $file =  $this->path_files.$grupos[0]->file_arc02;
                    $status_solicitud = $grupos[0]->status_solicitud_arc02;
                    $num_revision = $grupos[0]->num_revision_arc02;
                    foreach ($grupos as $grupo) {
                        
                        $interval = (Carbon::parse($grupo->termino)->diffInDays($grupo->inicio))/2; 
                        $interval = intval(ceil($interval));
                        $interval = (Carbon::parse($grupo->inicio)->addDay($interval))->format('Y-m-d');
                        $i = date($interval);
                        if ($i < date('Y-m-d')){
                             $extemporaneo = true;                                
                             $ids_extemp[] = $grupo->id;
                        }
                    }
                    if($extemporaneo == true)$mextemporaneo = ['OBSERVACIONES DE FINANCIEROS'=>'OBSERVACIONES DE FINANCIEROS','ERROR MECANOGRAFICO'=>'ERROR MECANOGRAFICO',
                    'TRAMITES ADMINISTRATIVOS'=>'TRAMITES ADMINISTRATIVOS'];
                }          
                $_SESSION['grupos'] = $grupos;
                $_SESSION['memo'] = $memo;
                $_SESSION['opt'] = $opt;

            }else $message = "No se encuentran registros que mostrar.";
        }else $message = "Ingrese el número de revisión o número de memorándum.";

        if(session('message')) $message = session('message');
        return view('solicitud.turnar.index', compact('message','grupos','memo', 'file','opt','extemporaneo','mextemporaneo','status_solicitud','num_revision','ids_extemp','movimientos'));
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
       
    public function enviar(Request $request){ 
        $result = $extemporaneo = NULL;
        $titulo = ''; $cuerpo = '';
        $message = 'Operación fallida, vuelva a intentar..';

        if(isset($request->movimiento) ){ 
            switch($request->movimiento){
                case 'SOPORTE': //CAMBIO DE SOPORTE
                    $result = DB::table('tbl_cursos')->where('munidad',$request->memo)->where('status_curso','AUTORIZADO')
                    ->update(['status_curso' => 'SOPORTE', 'motivo_mov' => $request->motivo]); 
                    if($result) $message = "Operación exitosa!";
                break;
                case 'SUBIR': //CAMBIO DE SOPORTE
                    if ($request->hasFile('file_autorizacion')) { 
                        $name_file = DB::table('tbl_cursos')->where('munidad',$request->memo)->where('status_curso','ACEPTADO')->value(DB::raw("split_part(file_arc01, '/', array_length(string_to_array(file_arc01, '/'), 1))"));
                        $file = $request->file('file_autorizacion'); //dd($name_file);
                        $file_result = $this->upload_file($file,$name_file);                
                        $url_file = $file_result["url_file"]; 
                        if($file_result['up']){
                            $movimientos = 
                            $result = DB::table('tbl_cursos')->where('munidad',$request->memo)->where('status_curso','ACEPTADO')
                            ->update(['status_curso' => 'AUTORIZADO',
                                'movimientos' => DB::raw("
                                    COALESCE(movimientos, '[]'::jsonb) || jsonb_build_array(
                                        jsonb_build_object(
                                            'fecha', '".Carbon::now()->format('Y-m-d H:i:s')."',
                                            'usuario', '".Auth::user()->name."',
                                            'operacion', 'CAMBIO DE SOPORTE ARC01',
                                            'motivo', motivo_mov
                                        )
                                    )
                                "),
                                'motivo_mov' => null
                            ]); 
                            if($result) $message = "Operación exitosa!";
                        }else $message = "Error al subir el archivo, volver a intentar.";
                    }
                break;
            }
        }elseif($_SESSION['memo']==$request->nmemo ){
            if ($request->hasFile('file_autorizacion')) {               
                $name_file = $this->id_unidad."_".str_replace('/','-',$_SESSION['memo'])."_".date('ymdHis')."_".$this->id_user;                                
                $file = $request->file('file_autorizacion');
                $file_result = $this->upload_file($file,$name_file);                
                $url_file = $file_result["url_file"]; 
                if($file_result['up']){                    
                    switch($_SESSION['opt']){
                        case "ARC01":
                            //VALIDACION DE EXTEMPORANEIDAD
                            $ids_extemp = $this->ids_extemp($_SESSION['memo']);

                            foreach($ids_extemp as $t){ //GUARDANDO EL VALIDANDO MOTIVO DE LA EXONERACION                                 

                                if(!$request->mrespuesta[$t]) $message = "Escriba la razón extemporaneo.";                                                                                                    
                                if(!$request->motivo[$t]) $message = "Selecione el motivo extemporaneo.";                                   
                                
                                if(!$request->mrespuesta[$t] OR !$request->motivo[$t])  return redirect('solicitud/apertura/turnar')->with('message',$message);
                                else  $result = DB::table('tbl_cursos')->where('id',$t)
                                ->where('status_curso',null)->where('turnado','UNIDAD')->where('status','NO REPORTADO')
                                ->update(['mextemporaneo' => $request->motivo[$t] , 'rextemporaneo'=>$request->mrespuesta[$t]]); 
                            }                          
                            //FIN VALIDACION DE EXTEMPORANEIDAD*/
                            
                            $titulo = 'Clave de Apertura';
                            $cuerpo = 'Solicitud de asignación de clave de apertura del memo '.$_SESSION['memo'];
                            $folios = array_column(json_decode(json_encode($_SESSION['grupos']), true), 'folio_grupo');                            
                            $result = DB::table('tbl_cursos')->where('munidad',$_SESSION['memo'])->where('status_curso',null)->where('turnado','UNIDAD')->where('status','NO REPORTADO')
                                ->update(['status_curso' => 'SOLICITADO', 'updated_at'=>date('Y-m-d H:i:s'), 'file_arc01' => $url_file]); 

                            if($result) $alumnos = DB::table('alumnos_registro')->whereIn('folio_grupo',$folios)->update(['turnado' => "DTA",'fecha_turnado' => date('Y-m-d')]);
                            else $message = "Error al turnar la solictud, volver a intentar.";                            
                           
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
        }else $message = "Operación inválida!!";
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
                    $file_result = $this->upload_file($file,$name_file);                
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
        $path=$url = $mgs= null;
        $up = false;
        if($ext == "pdf"){
            $name = trim($name.".pdf");
            $path = $this->path_pdf.$name;
            $up = Storage::disk('custom_folder_1')->put($path, file_get_contents($file));            
            $msg = "El archivo ha sido cargado o reemplazado correctamente.";            
        }else $msg= "Formato de Archivo no válido, sólo PDF.";
                
        $data_file = ["message"=>$msg, 'url_file'=>$path, 'up'=>$up];
       
        return $data_file;
    }
   
    public function pdfARC01(Request $request){
        if($request->fecha AND $request->memo){ 
            $marca = true;       
            $fecha_memo =  $request->fecha;
            $memo_apertura =  $request->memo;            

            $reg_cursos = DB::table('tbl_cursos')->SELECT('id','unidad','nombre','clave','mvalida','mod','espe','curso','inicio','termino','dia','dura',
                DB::raw("concat(hini,' A ',hfin) AS horario"),'horas','plantel','depen','muni','nota','munidad','efisico','hombre','mujer','tipo','opcion',
                'motivo','cp','ze','tcapacitacion','tipo_curso','fecha_arc01','status_solicitud');                
            if($_SESSION['unidades'])$reg_cursos = $reg_cursos->whereIn('unidad',$_SESSION['unidades']);                
            $reg_cursos = $reg_cursos->WHERE('munidad', $memo_apertura)->orderby('espe')->get();
                
            if(count($reg_cursos)>0){               
                if(preg_match('/unidad\b/',$this->data['slug'])){
                    $asigna_fecha = DB::table('tbl_cursos')->where('munidad',$memo_apertura)->whereNull('fecha_arc01')->update(['fecha_arc01'=>$request->fecha]);                   
                }
                if( $reg_cursos[0]->fecha_arc01) $fecha_memo = $reg_cursos[0]->fecha_arc01;
                
                
               //CONVERSION DE FECHA                
                $meses = ['01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre'];
                $mes = $meses[date('m',strtotime($fecha_memo))];
                $fecha_memo = date('d',strtotime($fecha_memo)).' de '.$mes.' del '.date('Y',strtotime($fecha_memo));
                

                $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first();                 
                $unidad = $reg_cursos[0]->unidad;
                $reg_unidad = DB::table('tbl_unidades')->where('unidad', $unidad)->first();       
                $direccion = $reg_unidad->direccion;                

                if($reg_cursos[0]->status_solicitud=="VALIDADO") $marca = false;
                $pdf = PDF::loadView('solicitud.apertura.pdfARC01',compact('reg_cursos','reg_unidad','fecha_memo','memo_apertura','distintivo','marca','direccion'));
                $pdf->setpaper('letter','landscape');
                return $pdf->stream('ARC01.pdf');
            }else return "MEMORANDUM NO VALIDO PARA LA UNIDAD";exit;
        }return "ACCIÓN INVÁlIDA";exit;
    }
    
    public function pdfARC02(Request $request) { 
        if($request->fecha AND $request->memo){  
            $marca = true;    
            $fecha_memo =  $request->fecha;
            $memo_apertura =  $request->memo;            

            $reg_cursos = DB::table('tbl_cursos')->SELECT('id','unidad','nombre','clave','mvalida','mod','curso','inicio','termino','dura',
                'efisico','opcion','motivo','nmunidad','observaciones','realizo','tcapacitacion','tipo_curso','fecha_arc02','status_solicitud_arc02');
            if($_SESSION['unidades'])$reg_cursos = $reg_cursos->whereIn('unidad',$_SESSION['unidades']);                
            $reg_cursos = $reg_cursos->WHERE('nmunidad', '=', $memo_apertura)->orderby('espe')->get();
                
            if(count($reg_cursos)>0){
                if(preg_match('/unidad\b/',$this->data['slug'])){
                    $asigna_fecha = DB::table('tbl_cursos')->where('nmunidad',$memo_apertura)->whereNull('fecha_arc02')->update(['fecha_arc02'=>$request->fecha]);
                }
                if( $reg_cursos[0]->fecha_arc02) $fecha_memo = $reg_cursos[0]->fecha_arc02;

                //CONVERSION DE FECHA                
                $meses = ['01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre'];
                $mes = $meses[date('m',strtotime($fecha_memo))];
                $fecha_memo = date('d',strtotime($fecha_memo)).' de '.$mes.' del '.date('Y',strtotime($fecha_memo));


                $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first(); 
                $unidad = $reg_cursos[0]->unidad;
                $reg_unidad = DB::table('tbl_unidades')->where('unidad', $unidad)->first();       
                $direccion = $reg_unidad->direccion;

                if($reg_cursos[0]->status_solicitud_arc02=="VALIDADO") $marca = false;
                $pdf = PDF::loadView('solicitud.apertura.pdfARC02',compact('reg_cursos','reg_unidad','fecha_memo','memo_apertura','distintivo','marca','direccion'));
                $pdf->setpaper('letter','landscape');
                return $pdf->stream('ARC02.pdf');
            }else return "MEMORANDUM NO VALIDO PARA LA UNIDAD";exit;   
        }
    }

    public function cambiar_memorandum(Request $request){
         //dd($request->all());
        $message = "Operación fallida, vuelva a intentar..";        
        if ($request->memo AND $request->nmemo AND $request->opt) {
            if ($request->opt === 'ARC01') {
                if ((DB::table('tbl_cursos')->where('munidad',$request->nmemo)->value('id'))) {
                    $message = "El memorándum ya se encuentra en uso..";
                } else {                    
                    $result = DB::table('tbl_cursos')->where('num_revision',$request->memo)->orWhere('munidad',$request->memo)->update(['munidad' => $request->nmemo]);
                    if($result){
                        $history = DB::table('tbl_cursos_history')->where('num_revision',$request->memo)->orWhere('munidad',$request->memo)->update(['munidad' => $request->nmemo,'num_revision'=>$request->memo]);
                        $_SESSION['memo'] = $request->nmemo;
                        $message = "El Guardado del Memorándum fué exitoso";
                    }
                }
            }else{
                if ((DB::table('tbl_cursos')->where('nmunidad',$request->nmemo)->value('id'))) {
                    $message = "El memorándum ya se encuentra en uso..";
                } else {
                    $r = DB::table('tbl_cursos')->where('num_revision_arc02',$request->memo)->orWhere('nmunidad',$request->memo)->value('num_revision_arc02');
                    if ($r) {
                        $result = DB::table('tbl_cursos')->where('num_revision_arc02',$r)->update(['nmunidad' => $request->nmemo]);
                        if ($result) {
                            $result2 = DB::table('tbl_cursos_history')->where('nmunidad',$r)->update(['nmunidad' => $request->nmemo, 'num_revision'=>$r]);
                            $_SESSION['memo'] = $request->nmemo;
                            $message = "El Guardado del Memorándum fué exitoso";
                        }else{
                            $message = "Operación fallida, vuelva a intentar..";
                        }
                    } else {
                        $message = "Operación fallida, vuelva a intentar..";
                    }
                    
                }
            }
        }else $message = "Ingrese el número de memorándum";
        return redirect('solicitud/apertura/turnar')->with('message',$message); 
    }

    private function ids_extemp($memo){        
            $result = DB::select("SELECT id
                    FROM (
                        SELECT c.id, c.termino, min(generate_series::date) as fecha_extemp                            
                        FROM tbl_cursos c 
                                CROSS JOIN generate_series(
                                    CASE 
                                        WHEN date_part('dow',c.inicio) BETWEEN 1 AND 2 THEN c.inicio+ CAST('3 days' AS INTERVAL)
                                        WHEN date_part('dow',c.inicio) BETWEEN 3 AND 6 THEN c.inicio+ CAST('5 days' AS INTERVAL) 									
                                        ELSE c.inicio+ CAST('4 days' AS INTERVAL) 
                                    
                                    END,
                                    c.termino,
                                    '1 day'::interval
                                )
                        WHERE  munidad = ? 
                        AND EXTRACT(DOW FROM generate_series::date) BETWEEN 1 AND 5
  						AND generate_series::date NOT IN(SELECT fecha FROM dias_inhabiles dh WHERE fecha BETWEEN c.inicio AND c.inicio+ CAST('2 days' AS INTERVAL)) 
                        --AND date_part('dow',generate_series::date) not in(0,6)=true 
                        group by c.id,c.termino                        
                           
                    ) as global WHERE now()::date>=fecha_extemp or now()::date>termino",[$memo]);
        return $resultArray = array_column(json_decode(json_encode($result), true),'id'); 
    }   
}