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
use App\Utilities\MyUtility;
use App\Models\cat\catUnidades;
use App\Models\Permission;
use App\Models\tbl_curso;
use App\User;
use PDF;
use App\Services\ValidacionServicioVb;

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
            session(['unidades' =>  $this->data['unidades']]);
            return $next($request);
        });
    }

    public function index(Request $request){
        $opt = $memo = $message = $file = $status_solicitud = $extemporaneo = $motivo_soporte = NULL;

        if($request->memo)  $memo = $request->memo;
        elseif(session()->has('memo')) $memo = session('memo');

        if($request->opt)  $opt = $request->opt;
        elseif(session()->has('opt')) $opt = session('opt');
        else $opt = 'ARC01';

        session(['grupos' => NULL]);
        $grupos = $movimientos = [];
        //echo $memo;
        $path = $this->path_files;
        if($memo){
            $grupos = DB::table('tbl_cursos as tc')->select('convenios.fecha_vigencia','tc.*',DB::raw("'$opt' as option"),'tc.turnado as turnado_solicitud',
                'tc.comprobante_pago','e.memo_soporte_dependencia as soporte_exo','e.nrevision as rev_exo','tr.file_pdf','tr.status_folio','tr.motivo','tc.fecha_arc01',
                DB::raw("COALESCE(tc.status_curso, tc.status_solicitud) as status_curso"),
                DB::raw("COALESCE(tc.movimientos->'VoBo'->0->>'motivo', null) as motivo_vobo"),
                DB::raw("COALESCE(tc.clave, '0') as clave"),///NUEVO VOBO
                DB::raw('COALESCE(tc.vb_dg, false) as vb_dg')//NUEVO VOBO
                )
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
                //dd($grupos);
            if(count($grupos)>0){
                session(['grupos' => $grupos]);
                session(['memo' => $memo]);
                session(['opt' => $opt]);
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
                    if ($value->status_folio=='SOPORTE'){
                         $motivo_soporte = true;
                         $movimientos = ['' => '- SELECCIONAR -', 'ACEPTADO'=>'AUTORIZAR CAMBIO DE RECIBO DE PAGO','DENEGADO'=>'DENEGAR REEMPLAZO DE RECIBO DE PAGO'];
                    }
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

                 if($status_solicitud =='TURNADO'){ //TURNADO PRELIMINAR
                    $movimientos += ['' => '- SELECCIONAR -'];
                    if($grupos[0]->arc == '02')  $movimientos += ['EDICION' =>'AUTORIZAR EDICION'];
                     $movimientos += ['PRETORNADO'=>'RETORNAR A UNIDAD','VALIDADO'=>'VALIDAR PRELIMINAR'];
                }

                /*
                if($status_solicitud =='TURNADO' and $grupos[0]->motivo_vobo and $grupos[0]->vb_dg==false){ //RECHAZADO VoBo
                    $movimientos = ['' => '- SELECCIONAR -', 'PRETORNADO'=>'RETORNAR A UNIDAD'];
                }elseif($status_solicitud =='TURNADO' and $grupos[0]->turnado!='VoBo' and $grupos[0]->vb_dg==true){ //TURNADO PRELIMINAR
                    $movimientos += ['' => '- SELECCIONAR -'];
                    if($grupos[0]->arc == '02'){
                        $movimientos += ['EDICION' =>'AUTORIZAR EDICION', 'PRETORNADO'=>'RETORNAR A UNIDAD','VALIDADO'=>'VALIDAR PRELIMINAR','VoBo'=>'VALIDAR Y SOLICITAR VoBo'];
                    }else{
                        $movimientos += ['PRETORNADO'=>'RETORNAR A UNIDAD','VoBo'=>'VALIDAR Y SOLICITAR VoBo'];
                    }
                }elseif($status_solicitud =='TURNADO' and $grupos[0]->turnado=='VoBo' and $grupos[0]->vb_dg==false){ //DESHACER ENVIO voBo
                    $movimientos = ['' => '- SELECCIONAR -', 'DESHACER'=>'DESHACER MOVIMIENTO'];
                }elseif($status_solicitud =='TURNADO' and $grupos[0]->vb_dg==true){ //TURNADO Y AUTORIZADO DG
                    $movimientos = ['' => '- SELECCIONAR -', 'PRETORNADO'=>'RETORNAR A UNIDAD', 'VALIDADO'=>'VALIDAR PRELIMINAR']; //INHABILITAR VOBO DG
                }
                */

            }else $message = "No se encuentran registros que mostrar.";
        }
//dd($grupos);
        if(session('message')) $message = session('message');

        return view('solicitudes.aperturas.index', compact('message','grupos','memo', 'file','opt', 'movimientos', 'path','status_solicitud','extemporaneo','motivo_soporte'));
    }

    public function search(Request $request){
        $_SESSION = $ejercicio = null;
        if($request->ejercicio)$ejercicio = $request->ejercicio;
        else  $ejercicio = date('Y');
        $aperturas = DB::table('tbl_cursos as tc')
            ->select('tc.unidad','tc.num_revision','tc.munidad','tc.file_arc01','tc.turnado','tc.status_curso','tc.status_solicitud','tc.status','tc.pdf_curso','tc.fecha_apertura',
                DB::raw("
                    (
                    CASE
                        WHEN BOOL_AND(tc.status_curso IS NOT NULL) THEN tc.status_curso
                        WHEN BOOL_AND(tc.turnado = 'VoBo') THEN 'TURNADO VoBo'
                        WHEN BOOL_AND(tc.turnado = 'DGA' AND tc.vb_dg = true) THEN 'AUTORIZADO DG'
                        WHEN BOOL_AND(tc.turnado = 'DGA' AND tc.vb_dg = false) THEN 'RECHAZADO DG'
                        ELSE 'PREVALIDACION'
                        END
                    ) as status_solicitud
                ")
            )
            ->leftJoin('alumnos_registro as a','tc.folio_grupo','=','a.folio_grupo')
            ->where('a.turnado','!=','VINCULACION')
            ->whereYear('tc.created_at', $ejercicio)
            ->where(function($query) {
                $query->where('status_curso','!=',null)
                      ->orWhere('status_solicitud','=','TURNADO')
                      ->orWhere('tc.turnado','=','DGA');
            });
        if ($request->valor) {
            $aperturas = $aperturas->where('tc.munidad',$request->valor)
                ->orWhere('tc.num_revision',$request->valor);
        }
        $aperturas = $aperturas->groupBy('tc.unidad','tc.num_revision','tc.munidad','tc.file_arc01','tc.turnado','tc.status_curso','tc.status_solicitud','tc.status',
            'tc.pdf_curso','tc.fecha_apertura')
            ->orderBy('tc.fecha_apertura','desc')
            ->paginate(50)->appends(['ejercicio' => $ejercicio]);
        $anios = MyUtility::ejercicios(); //dd($aperturas);
        return view('solicitudes.aperturas.buzon',compact('aperturas','anios','ejercicio'));
    }

    public function autorizar(Request $request){ //ENVIAR PDF DE AUTORIZACIÓN Y CAMBIAR ESTATUS A AUTORIZADO
        $result = NULL;
        $titulo = ''; $cuerpo = '';
        $message = 'Operación fallida, vuelva a intentar..';

        if(session('memo') AND session('opt') ){
            if ($request->hasFile('file_autorizacion')) {
                $name_file = str_replace('/','-',session('memo'))."_".date('ymdHis')."_".$this->id_user;
                $file = $request->file('file_autorizacion');
                $file_result = $this->upload_file($file,$name_file);
                $url_file = "https://www.sivyc.icatech.gob.mx/storage/uploadFiles/".$file_result["url_file"];

                if($file_result){
                    switch(session('opt')){
                        case "ARC01":
                            $titulo = 'Autorización de Apertura';
                            $cuerpo = 'La autorización de clave de apertura del memo '.session('memo').' ha sido procesada';
                            $result = DB::table('tbl_cursos')->where('munidad',session('memo'))
                            ->where('clave','<>','0')
                            //->where('turnado','UNIDAD')
                            ->where('status_curso','EN FIRMA')
                            //->where('status','NO REPORTADO')
                            ->update(['status_curso' => 'AUTORIZADO', 'updated_at'=>date('Y-m-d H:i:s'), 'pdf_curso' => $url_file]);
                        break;
                        case "ARC02":
                            if($request->movimiento=='CANCELADO'){
                                //var_dump(session('grupos') );

                                $folio_grupo = session('grupos')[0]->folio_grupo;

                                //exit;
                                $titulo = 'Cancelación de apertura';
                                $cuerpo = 'La cancelación de la apertura del memo '.session('memo').' ha sido procesada';
                                $result = DB::table('tbl_cursos')->where('nmunidad',session('memo'))
                                ->where('clave','<>','0')
                                ->where('turnado','UNIDAD')
                                ->where('status_curso','EN FIRMA')
                                ->whereIn('status',['NO REPORTADO','RETORNO_UNIDAD'])
                                ->update(['status_curso' => 'CANCELADO','status'=>'CANCELADO','arc'=>'02','fecha_modificacion'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s'), 'pdf_curso' => $url_file]);

                                DB::table('alumnos_registro')->where('folio_grupo',$folio_grupo)->update(['eliminado' => TRUE,'updated_at'=>date('Y-m-d H:i:s')]);
                                DB::table('tbl_inscripcion')->where('folio_grupo',$folio_grupo)->update(['activo' => FALSE,'updated_at'=>date('Y-m-d H:i:s')]);

                            }else{
                                $titulo = 'Autorización de modificación de apertura';
                                $cuerpo = 'La autorización de modificación de apertura del memo '.session('memo').' ha sido procesada';
                                $result = DB::table('tbl_cursos')->where('nmunidad',session('memo'))
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
                        $notification = $this->getDataNotification(session('opt'), session('memo'), $titulo, $cuerpo, $url);
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
        if(session('memo') AND session('opt') AND $request->mrespuesta AND $request->fecha ){
            $mrespuesta = $request->mrespuesta;
            $fecha = $request->fecha;
            switch(session('opt')){
                case "ARC01":
                        $result = DB::table('tbl_cursos')->where('munidad',session('memo'))
                        ->where('clave','<>','0')
                        ->where('turnado','UNIDAD')
                        ->where('status_curso','EN FIRMA')
                        ->where('status','NO REPORTADO')
                        ->update([ 'mvalida' => $mrespuesta, 'fecha_apertura' => $fecha, 'valido' => $this->realizo]);
                        if($result)$message = "OPERACIÓN EXITOSA!!";
                break;
                case "ARC02":
                    $result = DB::table('tbl_cursos')->where('nmunidad',session('memo'))
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
        if(session('memo') AND session('opt')){
            switch(session('opt')){
                case "ARC01":
                   $memo = session('memo');
                    if($this->son_mayores ($memo)){
                        $result = DB::table('tbl_cursos')->where('munidad',session('memo'))
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
        if(session('memo') AND $request->mrespuesta AND $request->fecha AND session('opt')){
            $mrespuesta = $request->mrespuesta;
            $fecha = $request->fecha;
            switch(session('opt') ){
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
                        ->where('tc.munidad',session('memo'))->orderby('termino','ASC')->orderby('hfin','ASC')
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
                    $rest = DB::table('tbl_cursos')->where('nmunidad',session('memo'))->update(['status_curso' => 'EN FIRMA', 'nmacademico' => $mrespuesta,'fecha_modificacion' => $fecha,'valido' => $this->realizo]);
                    if($rest)$message = "Operación Exitosa!!";

                break;
            }
        } else $message = "NO OLVIDE INGRESAR NÚMERO Y FECHA DE MEMORÁNDUM";
        return redirect('solicitudes/aperturas')->with('message',$message);
    }

    public function retornar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if(session('memo')){
            switch(session('opt') ){
                case "ARC01":
                    $titulo = 'Apertura retornada';
                    $cuerpo = 'La apertura con número de memo '.session('memo').' fue retornada a su unidad';
                    $rev = DB::table('tbl_cursos')->where('munidad',session('memo'))->value('num_revision');
                    $result = DB::table('tbl_cursos')
                    ->where('clave','0')
                    ->where('turnado','UNIDAD')
                    ->where('status_curso','SOLICITADO')
                    ->where('status','NO REPORTADO')
                    ->where('munidad',session('memo'))->update(['status_curso' => null,'updated_at'=>date('Y-m-d H:i:s'), 'munidad' => $rev,
                                                                'fecha_arc01'=>null,'file_arc01' => null,'status_solicitud'=>null]);
                    if($result){
                        $folios = DB::table('tbl_cursos')->where('munidad',$rev)->pluck('folio_grupo');
                        //var_dump($folios);exit;
                        $rest = DB::table('alumnos_registro')->whereIn('folio_grupo',$folios)->update(['turnado' => "UNIDAD",'fecha_turnado' => date('Y-m-d')]);
                        if($rest)$message = "SOLICITUD ARC-01 RETORNADA EXITOSAMENTE.";
                        session()->forget('memo');
                     }
                break;
                case "ARC02":
                    $titulo = 'Modificación de Apertura';
                    $cuerpo = 'La modificación de apertura del memo '.session('memo').' ha sido retornada a su unidad';
                    $rev = DB::table('tbl_cursos')->where('nmunidad',session('memo'))->value('num_revision_arc02');
                    $result = DB::table('tbl_cursos')
                    ->where('arc','02')
                    ->where('turnado','UNIDAD')
                    ->where('status_curso','SOLICITADO')
                    ->wherein('status',['NO REPORTADO','RETORNO_UNIDAD'])
                    ->where('nmunidad',session('memo'))->update(['status_curso' => 'AUTORIZADO','updated_at'=>date('Y-m-d H:i:s'),'status_solicitud_arc02'=>null,
                                                                    'nmunidad' => $rev]);
                   // echo "pasa";exit;
                    if($result)$message = "SOLICITUD ARC-02 RETORNADA EXITOSAMENTE.";
                    //unset(session('memo'));
                break;
            }

            /*if ($result) {
                $url = 'https://sivyc.icatech.gob.mx/solicitud/turnar solicitud';
                $notification = $this->getDataNotification(session('opt'), session('memo'), $titulo, $cuerpo, $url);
                event(new NotificationEvent($notification['users'], $notification['data']));
            }*/
        }
        return redirect('solicitudes/aperturas')->with('message',$message);
   }

   public function soporte_pago(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if(session('memo') AND session('opt')){
            switch(session('opt')){
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
                        $memo = session('memo');
                        $ids = DB::table('tbl_cursos as tc')->where('tc.munidad',session('memo'))
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
                            if($result) $message = "Operación exitosa!";
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
            $reg_cursos = DB::table('tbl_cursos as tc')->SELECT('id','unidad','nombre','mvalida','mod','espe','curso','inicio','termino','dia','dura',
                DB::raw("concat(hini,' A ',hfin) AS horario"),'horas','plantel','depen','muni','nota','munidad','nmunidad','efisico','hombre','mujer','tipo','opcion',
                'motivo','cp','ze','tcapacitacion','tipo_curso','fecha_apertura','fecha_modificacion','observaciones','valido','realizo','mvalida','nmacademico',
                'status_solicitud','status_solicitud_arc02','cct','tc.folio_grupo','tc.instructor_mespecialidad','folio_grupo',
                DB::raw("COALESCE(clave, '0') as clave"), //NUEVO VOBO
                DB::raw('COALESCE(vb_dg, false) as vb_dg'),//NUEVO VOBO
                DB::raw("
                          (
                            SELECT string_agg(
                            CASE
                                WHEN DATE(\"start\") = DATE(\"end\") THEN TO_CHAR(DATE(\"end\"), 'DD/MM/YYYY')
                                ELSE TO_CHAR(DATE(\"start\"), 'DD/MM/YYYY') || ' - ' || TO_CHAR(DATE(\"end\"), 'DD/MM/YYYY')
                            END

                            || ' ' ||
                            CASE
                                WHEN TO_CHAR(\"start\", 'MI') = '00' THEN TO_CHAR(\"start\", 'HH24')
                                ELSE TO_CHAR(\"start\", 'HH24:MI')
                            END || '-' ||
                            CASE
                                WHEN TO_CHAR(\"end\", 'MI') = '00' THEN TO_CHAR(\"end\", 'HH24')
                                ELSE TO_CHAR(\"end\", 'HH24:MI')
                            END || 'h.(' ||
                            TO_CHAR(
                                (EXTRACT(EPOCH FROM ((CAST(\"end\" AS time) - CAST(\"start\" AS time)))) / 3600) *
                                ((DATE_TRUNC('day', \"end\")::date - DATE_TRUNC('day', \"start\")::date) + 1),
                                'FM999990.##'
                            ) || 'h)',
                            E'\n'
                            ORDER BY DATE(start)
                            ) AS agenda_texto
                            FROM agenda
                            WHERE id_curso = tc.folio_grupo
                        )::text AS agenda
                        "),

                        DB::raw("
                        (
                         CASE 
                         WHEN  tc.arc='01'  AND tc.nota ILIKE '%INSTRUCTOR%' THEN tc.nota                         
                         WHEN tc.arc='01' THEN(

                            CASE
                                WHEN (tc.vb_dg = true OR tc.clave!='0') AND tc.modinstructor = 'ASIMILADOS A SALARIOS' THEN 'INSTRUCTOR POR HONORARIOS ' || tc.modinstructor || ', '
                                WHEN (tc.vb_dg = true  OR tc.clave !='0') AND tc.modinstructor = 'HONORARIOS' THEN 'INSTRUCTOR POR ' || tc.modinstructor || ', '
                                ELSE ''
                            END
                            ||
                            CASE
                                WHEN tc.tipo = 'EXO' THEN 'MEMORÁNDUM DE EXONERACIÓN No. ' || tc.mexoneracion || ', '
                                WHEN tc.tipo = 'EPAR' THEN 'MEMORÁNDUM DE REDUCIÓN DE CUOTA No. ' || tc.mexoneracion || ', '
                                ELSE ''
                            END
                            ||
                            CASE
                                WHEN tc.tipo != 'EXO' THEN
                                    'CUOTA DE RECUPERACIÓN $' || ROUND((tc.costo)/(tc.hombre+tc.mujer),2) || ' POR PERSONA, ' ||
                                    'TOTAL CURSO $' || TO_CHAR(ROUND(tc.costo, 2), 'FM999,999,999.00')
                                ELSE ''
                            END
                            || '<div >MEMORÁNDUM DE VALIDACIÓN DEL INSTRUCTOR ' || tc.instructor_mespecialidad ||'.</div>'
                            || ' ' || COALESCE(tc.nota, '')
                           )
                        ELSE
                            tc.observaciones
                        END

                        ) AS observaciones
                    ")
            );
            if(session('unidades'))$reg_cursos = $reg_cursos->whereIn('unidad',session('unidades'));
            switch(session('opt') ){
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
                // $direccion = $direccion."Teléfono (961)6121621 Email: dtecnicaacademica@gmail.com";
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
            switch($request->movimiento){
                case "DESHACER":
                     $result = DB::table('tbl_cursos')->where('munidad',$memo)->whereIn('status',['NO REPORTADO','RETORNO_UNIDAD'])->where('turnado','VoBo')->update(['turnado' => 'UNIDAD']);
                    if($result)$message = "SOLICITUD RETORNADA DE VoBo.";
                break;
                case "EDICION":
                    $result = DB::table('tbl_cursos')->where('nmunidad',$memo)->whereIn('status',['NO REPORTADO','RETORNO_UNIDAD'])->update(['status_curso' => 'EDICION']);
                    if($result)$message = "SOLICITUD ENVIADA PARA EDICION.";
                break;
                case "RETORNO-VoBo":
                    $result = DB::statement("
                        UPDATE tbl_cursos
                        SET
                            turnado = ?,
                            vb_dg = ?,
                            movimientos = COALESCE(movimientos, '[]'::jsonb)
                                || jsonb_build_array(
                                    jsonb_build_object(
                                        'DTA-GA', jsonb_build_array(
                                            jsonb_build_object(
                                                'fecha',     ?::timestamp,
                                                'usuario',   ?::text,
                                                'operacion', 'RETORNO A DG',
                                                'motivo solicitud', 'SOLICITADO POR LA DG.'
                                            )
                                        )
                                    )
                                )
                        WHERE munidad = ? AND status IN ('NO REPORTADO','RETORNO_UNIDAD')",
                    [
                        'VoBo',
                        false,
                        date('Y-m-d H:i:s'),
                        Auth::user()->name,
                        $memo,
                    ]);
                    if($result)$message = "SOLICITUD RETORNADA A DG.";
                break;
                case "VoBo":
                    $result = DB::table('tbl_cursos')->where('munidad',$memo)->whereIn('status',['NO REPORTADO','RETORNO_UNIDAD'])->update(['turnado' => 'VoBo','vb_dg' => false]);
                    if($result)$message = "SOLICITUD ENVIADA PARA VoBo.";
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
                    $result = DB::table('tbl_cursos')->where($llave,$memo)->wherein('id',$ids)->update([$status => 'VALIDADO', 'turnado'=>'UNIDAD', 'obspreliminar' => null]);
                    if($result)$message = "SOLICITUD TURNADA A UNIDAD.";
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
                    ->update([$status => 'RETORNO', 'obspreliminar' => $value ,'turnado'=>'UNIDAD']);
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
                $message = "SOLICITUD RETORNADA EXITOSAMENTE.";
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

    public function guardar_fecha(Request $request){
        $message = "Operación fallida, por favor intente de nuevo.";

        if ($request->fecha && $request->memo && $request->memo_arc) {
            $campo_fecha = $request->opt == 'ARC02' ? 'fecha_arc02' : 'fecha_arc01';
            $campo_memo  = $request->opt == 'ARC02' ? 'nmunidad' : 'munidad';
            $operacion   = $request->opt == 'ARC02' ? 'CAMBIO LA FECHA Y MEMO DEL ARC02' : 'CAMBIO LA FECHA Y MEMO DEL ARC01';

            $result = DB::table('tbl_cursos')
                ->where($campo_memo, $request->memo_arc)
                ->whereNotNull($campo_fecha)
                ->where(function ($query) {
                    $query->whereNotIn('status_curso', ['CANCELADO'])
                        ->orWhereNull('status_curso');
                })
                ->update([
                    $campo_fecha => $request->fecha,
                    $campo_memo  => $request->memo,
                    'movimientos' => DB::raw("
                        COALESCE(movimientos, '[]'::jsonb) || jsonb_build_array(
                            jsonb_build_object(
                                'fecha', '".now()."',
                                'usuario', '".addslashes(Auth::user()->name)."',
                                'operacion', '".addslashes($operacion)."',
                                'motivo solicitud', 'SOLICITADO POR LA UNIDAD DE CAPACITACIÓN.'
                            )
                        )
                    ")
                ]);

            if ($result) $message = "Operación Exitosa!";
        } else {
            $message = "Por favor, ingrese una fecha válida.";
        }

        return $message;
    }

    ##Funcion para mostrar la lista de instructores validados
    public function modal_instructores(Request $request) {

        $folio_grupo = $request->folio_grupo;
        $totalInstruc = 0;
        $agenda = DB::Table('agenda')->Where('id_curso', $folio_grupo)->get();
        $grupo = DB::table('tbl_cursos')->select('id_curso','inicio', 'tbl_cursos.id_especialidad', 'termino', 'folio_grupo', 'programa', 'id_instructor', 'tbl_unidades.unidad', 'cursos.curso_alfa')
            ->JOIN('tbl_unidades', 'tbl_unidades.id', '=', 'tbl_cursos.id_unidad')
            ->JOIN('cursos', 'cursos.id', '=' ,'tbl_cursos.id_curso')
            ->where('folio_grupo', $folio_grupo)->first();

        // list($instructores, $mensaje) = $this->data_instructores($grupo, $agenda);

         #### Llamamos la validacion de instructor desde el servicio
        $servicio = (new ValidacionServicioVb());
        // $instructores = $servicio->consulta_general_instructores($data, $this->ejercicio);

        list($instructores, $mensaje) = $servicio->data_validacion_instructores($grupo, $agenda, $this->ejercicio);

        // Ordenar por nombre y unidad
        if (!empty($grupo->unidad)) {
            ##Otro ordenamiento por total de cursos y unidad
            try {
                $unidad_prioritaria = $grupo->unidad;

                $instructores = collect($instructores)->sort(function ($a, $b) use ($unidad_prioritaria) {
                    $a_es_prioritario = $a->unidad === $unidad_prioritaria;
                    $b_es_prioritario = $b->unidad === $unidad_prioritaria;

                    if ($a_es_prioritario && !$b_es_prioritario) {
                        return -1;
                    }
                    if (!$a_es_prioritario && $b_es_prioritario) {
                        return 1;
                    }

                    if ($a->unidad === $b->unidad) {
                        return $a->total_cursos <=> $b->total_cursos;
                    }
                    return strcmp($a->unidad, $b->unidad);
                })->values();

            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 500,
                    'mensaje' => 'Error al ordenar la lista de instructores: ' . $th->getMessage()
                ]);
            }
        }

        //Validar si el array instructores esta vacio
        if (count($instructores) === 0) {
            return response()->json([
                'status' => 500,
                'mensaje' => $mensaje,
                'totalInstruc' => count($instructores)
            ]);
        }

        return response()->json([
            'status' => 200,
            'instructores' => $instructores,
            'mensaje' => $mensaje,
            'totalInstruc' => count($instructores)
        ]);
    }

    public function pdfAgendaAnexo(Request $request){
        if($request->memo){
            $data = DB::table('tbl_cursos as tc')->select('tc.folio_grupo','tc.unidad','tc.curso',
               DB::raw("
                    (
                        SELECT json_agg(
                            json_build_object(
                                'fecha',
                                CASE
                                    WHEN DATE(start) = DATE(\"end\") THEN
                                        TO_CHAR(DATE(start), 'DD/MM/YYYY')
                                    ELSE
                                        TO_CHAR(DATE(start), 'DD/MM/YYYY') || ' AL ' || TO_CHAR(DATE(\"end\"), 'DD/MM/YYYY')
                                END,
                                'horario',
                                'DE ' || TO_CHAR(start, 'HH24:MI') || ' A ' || TO_CHAR(\"end\", 'HH24:MI') || ' HRS.',
                                'horas',
                                (EXTRACT(EPOCH FROM ((CAST(\"end\" AS time) - CAST(\"start\" AS time)))) / 3600)*((DATE_TRUNC('day', \"end\")::date - DATE_TRUNC('day', \"start\")::date) + 1)

                            )
                            ORDER BY DATE(start)
                        )
                        FROM agenda
                        WHERE id_curso = tc.folio_grupo
                    ) AS agenda
                "),
                DB::raw("
                    (
                        SELECT SUM(
                            (EXTRACT(EPOCH FROM ((CAST(\"end\" AS time) - CAST(\"start\" AS time)))) / 3600)*((DATE_TRUNC('day', \"end\")::date - DATE_TRUNC('day', \"start\")::date) + 1)
                        )
                        FROM agenda
                        WHERE id_curso = tc.folio_grupo
                    ) AS total_horas
                ")
            );

            if($request->opt=='ARC01'){
                $data = $data->where('tc.munidad', $request->memo);
            }elseif($request->opt=='ARC02'){
                $data = $data->where('tc.nmunidad', $request->memo);
            }
            $data = $data->get(); //dd($data);
        }

        if($data){
            $direccion = null;
            $pdf = PDF::loadView('solicitudes.aperturas.pdfAgendaAnexo',compact('data','direccion'));
            $pdf->setpaper('letter','landscape');
            return $pdf->stream('Agenda-Anexo.pdf');
        }else return "MEMORÁNDUM NO VÁLIDO";

    }

}
