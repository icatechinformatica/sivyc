<?php

namespace App\Http\Controllers\Solicitudes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PDF;

class exoneracionesController extends Controller
{
    function __construct()
    {
        session_start();       
        $this->path_files = env("APP_URL").'/storage/uploadFiles';
    }

    public function index(Request $request){
        $message = $valor = $nrevision = $memo = $status = $file = $motivo = null;
        $cursos = $movimientos = [];
        if ($request->valor) {
            $_SESSION['valor'] = $request->valor;
        }
        if (isset($_SESSION['valor'])) {
            $cursos = DB::table('exoneraciones as e')
                        ->select('e.id','e.folio_grupo','e.nrevision','tc.tipo_curso','tc.unidad','tc.curso','c.costo','tc.dura','tc.inicio','tc.termino','e.foficio',
                            'tc.hombre','tc.mujer','e.fini','e.ffin','tc.nombre as instructor','e.tipo_exoneracion','e.noficio','e.razon_exoneracion','e.observaciones',
                            'e.no_memorandum','e.status','e.turnado','e.memo_soporte_dependencia','e.pobservacion','e.no_convenio','tc.depen','e.motivo','tc.hini','tc.hfin')
                        ->leftJoin('tbl_cursos as tc','e.folio_grupo','=','tc.folio_grupo')
                        ->leftJoin('alumnos_registro as ar','e.folio_grupo','=','ar.folio_grupo')
                        ->leftJoin('cursos as c','ar.id_curso','=','c.id')
                        ->where('e.nrevision',$_SESSION['valor'])
                        ->orWhere('e.no_memorandum',$_SESSION['valor'])
                        ->groupBy('e.id','tc.tipo_curso','tc.unidad','c.nombre_curso','tc.curso','c.costo','tc.dura','tc.inicio','tc.termino','e.foficio',
                        'tc.hombre','tc.mujer','e.fini','e.ffin','tc.nombre','e.tipo_exoneracion','e.noficio','e.razon_exoneracion','e.observaciones',
                        'e.no_memorandum','e.status','e.turnado','memo_soporte_dependencia','tc.depen','e.motivo','tc.hini','tc.hfin')
                        ->get();    //dd($cursos);
            if ( count($cursos) > 0 ) {
                $nrevision = $cursos[0]->nrevision;
                $memo = $cursos[0]->no_memorandum;
                $status = $cursos[0]->status;
                $motivo = $cursos[0]->motivo;
                $file = $this->path_files.$cursos[0]->memo_soporte_dependencia;
                $valor = $_SESSION['valor'];
                $_SESSION['revision'] = $nrevision;
                $_SESSION['memo'] = $memo;                
                if ($status == 'PREVALIDACION') {
                    $movimientos = ['RETORNAR'=>'RETORNAR', 'VALIDAR'=>'VALIDAR'];
                }elseif(in_array($status, ['REINICIAR','CANCELAR','EDITAR','SOPORTES'])){
                    $movimientos = ['REINICIAR'=>'REINICIAR','SOPORTES'=>'CAMBIO DE SOPORTES','EDITAR'=>'EDICION DE ALUMNOS','CANCELAR'=>'CANCELACION'];
                }else {
                    $movimientos = ['SOPORTES'=>'RETORNAR POR SOPORTES','CANCELAR'=>'CANCELAR SOLICITUD','AUTORIZAR'=>'AUTORIZAR SOLICITUD'];
                }
               
            } else {
                $message = "No se encuentran registros que mostrar.";
            }
            
        }
        if(session('message')) $message = session('message');
        $razon = ['MS'=>'MADRES SOLTERAS','AM'=>'ADULTOS MAYORES', 'BR'=>'BAJOS RECURSOS', 'D'=>'DISCAPACITADOS', 'PPL'=>'PERSONAS PRIVADAS DE LA LIBERTAD',
        'GRS'=>'GRUPOS DE REINSERCION SOCIAL', 'O'=>'OTRO'];
        return view('solicitudes.exoneraciones.index',compact('message','valor','memo','file','cursos','movimientos','status','razon','motivo'));
    }
    public function denegar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        $mov = $request->movimiento;
        if($mov){ 
            if(isset($_SESSION['revision']) AND ($request->valor == $_SESSION['revision'] OR $request->valor == $_SESSION['memo'] )){

                $result = DB::table('exoneraciones')->where('nrevision',$_SESSION['revision'])
                ->update(['pobservacion'=>'MSG: MOVIMIENTO DE '.$mov.' DENEGADO']);

                $this->reverse_exo($_SESSION['revision']);  
                if($result)$message = "SOLICITUD DE  $mov DENEGADO.";
            }
        }else $message = "No existe solicitud de movimiento que atender."; 
        return redirect()->route('solicitudes.exoneracion')->with(['message' => $message]);
    }
    public function aceptar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';  
        $mov = $request->movimiento;
        if(isset($_SESSION['revision']) AND ($request->valor == $_SESSION['revision'] OR $request->valor == $_SESSION['memo'] )){
            $status = ['EDITAR'=>'EDICION','EDICION DE ALUMNOS'=>'EDICION','SOPORTES'=>'VALIDADO','CAMBIO DE SOPORTES'=>'VALIDADO'];
            if($mov){                
                $this->history( $_SESSION['revision']);//PARA PODER REVERTIR ESTADO ANTERIOR            
                switch($mov){                    
                    case 'RETORNAR':                        
                        foreach ($request->respuesta as $key => $value) {                            
                            $result = DB::table('exoneraciones')->where('nrevision', $_SESSION['revision'])->where('folio_grupo', $key)
                                ->update([
                                    'status' => 'CAPTURA', 'frespuesta' => date('Y-m-d H:i:s'), 'pobservacion' => $value, 'turnado' => 'UNIDAD', 'activo'=>null,
                                    'no_memorandum'=>null, 'fecha_memorandum'=>null
                                ]);
                            if($result)$message = "La solicitud fue RETORNADA a la Unidad.";
                        }
                    break; 
                    case 'VALIDAR':
                        $result = DB::table('exoneraciones')->where('nrevision',$_SESSION['revision'])
                        ->update(['status'=>'VALIDADO', 'valido'=>strtoupper(Auth::user()->name), 'frespuesta'=>date('Y-m-d H:i:s'), 'pobservacion'=>null, 'turnado'=>'UNIDAD']);
                        if($result)$message = 'Operación Exitosa!!';                    
                    break;                     
                    case 'REINICIAR':                    
                        $result = DB::table('exoneraciones')
                        ->where('nrevision', $_SESSION['revision'])->wherein('status',['REINICIAR'])
                        ->update(['status' => 'CAPTURA', 'turnado' => 'UNIDAD','frespuesta' => date('Y-m-d H:i:s'), 'activo'=>null,'no_memorandum'=>null, 'fecha_memorandum'=>null]);
                        if($result)$message = "Solicitud REINICIADA.";
                    break; 
                    case $mov=='EDITAR' || $mov=='EDICION DE ALUMNOS' || $mov=='SOPORTES' || $mov=='CAMBIO DE SOPORTES':
                        $status = $status[$request->movimiento]; 
                        $result = DB::table('exoneraciones')->where('nrevision', $_SESSION['revision'])
                        ->update(['status' => $status, 'turnado' => 'UNIDAD', 'frespuesta' => date('Y-m-d H:i:s')]);
                        if($result)$message = "SOLICITUD DE ".$mov." ATENDIDA.";
                    break;
                    case $mov=='CANCELAR' || $mov=='CANCELACION': 
                        $result = DB::table('exoneraciones')
                        ->where('nrevision', $_SESSION['revision'])->whereIn('status',['CANCELAR','SOLICITADO'])
                        ->update(['status' => 'CANCELADO', 'activo' => 'false','frespuesta' => date('Y-m-d H:i:s'),'motivo' => $request->motivo]);
                        if($result){
                            $c = DB::table('tbl_cursos as c')->join('exoneraciones as e','c.folio_grupo','=','e.folio_grupo')
                            ->where('e.no_memorandum',$_SESSION['memo'])
                            ->update(['c.mexoneracion'=>'0']);
                            if($c)$message = "Solicitud CANCELADA.";
                        }
                    break;                      
                    case 'AUTORIZAR':
                        if ($request->hasFile('file_autorizacion')) { 
                            $name_file = Auth::user()->unidad."_".str_replace('/','-',$_SESSION['memo'])."_".date('ymdHis')."_".Auth::user()->id;                                
                            $file = $request->file('file_autorizacion');
                            $path = "/UNIDAD/exoneracion/"; 
                            $file_result = $this->upload_file($file,$name_file, $path);                
                            $url_file = $file_result["url_file"];
                            if ($file_result) {                                
                                $result = DB::table('exoneraciones')->where('nrevision',$_SESSION['revision'])->where('status','SOLICITADO')
                                    ->update(['status'=>'AUTORIZADO', 'frespuesta'=>date('Y-m-d H:i:s'), 'memo_soporte_dependencia'=>$url_file,
                                            'valido'=>strtoupper(Auth::user()->name), 'activo'=>'true']);
                                if ($result) {                                    
                                    $c = DB::table('tbl_cursos as c')->join('exoneraciones as e', 'c.folio_grupo', '=', 'e.folio_grupo')
                                        ->where('e.no_memorandum', $_SESSION['memo'])
                                        ->where('e.status','AUTORIZADO')
                                        ->where('e.activo', 'true')
                                        ->update(['c.mexoneracion' => $_SESSION['memo']]);
                                    if($c)$message = 'Operación Exitosa!!';
                                }
                                
                            } else $message = "Archivo inválido";
                        } else $message = "Favor de adjuntar el archivo de Autorización";
                    break;                
                }
            }else $message = "No existe solicitud de movimiento que atender."; 
        }
        return redirect()->route('solicitudes.exoneracion')->with(['message' => $message]);
    }

    public function generar(Request $request){
        if ($_SESSION['revision']) {
            $mexoneracion = $date = $alumnos = null;
            $marca = true;  $data = [];
            $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first(); 
            $cursos =  DB::table('exoneraciones as e')
                            ->select('tc.tipo_curso','tc.unidad','tc.curso','c.costo','tc.dura',
                                    DB::raw("to_char(DATE (tc.inicio)::date, 'DD-MM-YYYY') as inicio"),
                                    DB::raw("to_char(DATE (tc.termino)::date, 'DD-MM-YYYY') as termino"),
                                    'tc.mujer','tc.hombre','e.fini','e.ffin','e.nrevision',
                                    'tc.nombre as instructor','e.tipo_exoneracion','e.no_convenio','e.noficio',DB::raw("to_char(DATE (e.foficio)::date, 'DD-MM-YYYY') as foficio"),
                                    'e.razon_exoneracion','e.observaciones','tc.hini','tc.hfin',
                                    'tc.depen','e.id_unidad_capacitacion','tc.mod','ar.horario','tc.efisico','tc.tcapacitacion','tc.medio_virtual','tc.dia',
                                    'tc.folio_grupo','e.no_memorandum',DB::raw("to_char(DATE (e.fecha_memorandum)::date, 'DD-MM-YYYY') as fecha_memorandum"))
                            ->leftJoin('tbl_cursos as tc','e.folio_grupo','=','tc.folio_grupo')
                            ->leftJoin('alumnos_registro as ar','tc.folio_grupo','=','ar.folio_grupo')
                            ->leftJoin('cursos as c','ar.id_curso','=','c.id')
                            ->where('e.nrevision',$_SESSION['revision'])
                            ->groupBy('tc.tipo_curso','tc.unidad','tc.curso','c.costo','tc.dura','tc.inicio','tc.termino','tc.mujer','tc.hombre','e.fini','e.ffin',
                            'tc.nombre','e.tipo_exoneracion','e.no_convenio','e.noficio','e.foficio','e.razon_exoneracion','e.observaciones',
                            'tc.depen','e.id_unidad_capacitacion','tc.mod','ar.horario','tc.efisico','tc.tcapacitacion','tc.medio_virtual','tc.dia','tc.folio_grupo',
                            'e.no_memorandum','e.fecha_memorandum','e.nrevision','tc.hini','tc.hfin')
                            ->get();    //dd($cursos);
            $reg_unidad = DB::table('tbl_unidades')->select('ubicacion','dgeneral','dunidad','academico','vinculacion','dacademico','pdgeneral','pdacademico',
                                'pdunidad','pacademico','pvinculacion','municipio')
                                ->where('id',$cursos[0]->id_unidad_capacitacion)
                                ->first(); //dd($reg_unidad);
            $depen = $cursos[0]->depen; //ucwords(strtolower($cursos[0]->depen));
            if($cursos[0]->no_memorandum)$mexoneracion = $cursos[0]->no_memorandum;
            else $mexoneracion = $cursos[0]->nrevision;
            foreach ($cursos as $key => $value) {
                $alumnos = DB::table('alumnos_registro as ar')
                                ->select('ar.nombre','ar.costo','ar.apellido_paterno','ar.apellido_materno',
                                    DB::raw("extract(year from (age('$value->inicio',ap.fecha_nacimiento))) as edad"),DB::raw("substring(ar.curp,11,1) as sexo"))
                                ->leftJoin('alumnos_pre as ap','ar.id_pre','=','ap.id')
                                ->where('ar.folio_grupo',$value->folio_grupo)
                                ->get();
                $horario = date('H:i', strtotime(str_replace(['a.m.','p.m.'],['am','pm'],$value->hini))).' A '.date('H:i', strtotime(str_replace(['a.m.','p.m.'],['am','pm'],$value->hfin)));
                $data[$key]['curso'] = $value->curso;
                $data[$key]['mod'] = $value->mod;
                $data[$key]['dura'] = $value->dura;
                $data[$key]['horario'] = $horario;
                $data[$key]['inicio'] = $value->inicio;
                $data[$key]['termino'] = $value->termino;
                $data[$key]['tcapacitacion'] = $value->tcapacitacion;
                if ($value->tcapacitacion=='PRESENCIAL') {
                    $data[$key]['lugar'] = $value->efisico;
                }else {
                    $data[$key]['lugar'] = $value->medio_virtual;
                }
                $data[$key]['dias'] = $value->dia;
                $data[$key]['instructor'] = $value->instructor;
                $data[$key]['alumnos'] = $alumnos;
            }
            $pdf = PDF::loadView('solicitud.exoneracion.Solicitudexoneracion',compact('cursos','mexoneracion','distintivo','date','reg_unidad','depen','marca','data'));
            $pdf->setpaper('letter','landscape');
            return $pdf->stream('EXONERACION.pdf');
        } else {
            return "ACCIÓN INVÁlIDA";exit;
        }
    }

    protected function upload_file($file,$name,$path){       
        $ext = $file->getClientOriginalExtension(); // extension de la imagen
        $ext = strtolower($ext);
        $url = $mgs= null;
        if($ext == "pdf"){
            $name = trim($name.".pdf");
            $path = $path.$name;
            Storage::disk('custom_folder_1')->put($path, file_get_contents($file));
            //echo $url = Storage::disk('custom_folder_1')->url($path); exit;
            $msg = "El archivo ha sido cargado o reemplazado correctamente.";            
        }else $msg= "Formato de Archivo no válido, sólo PDF.";      
        $data_file = ["message"=>$msg, 'url_file'=>$path];
        return $data_file;
    }

    public function search(Request $request){
        $exoneraciones = DB::table('exoneraciones as e')
            ->select('e.nrevision', 'e.no_memorandum', 'u.ubicacion as unidad', 'e.status', 'e.turnado', 'e.memo_soporte_dependencia', 'e.fecha_memorandum')
            ->leftJoin('tbl_unidades as u', 'e.id_unidad_capacitacion', '=', 'u.id')
            ->whereIn('e.turnado', ['DTA', 'DTA_REVISION']);
        if ($request->valor) {
            $exoneraciones = $exoneraciones->where('e.nrevision', $request->valor)
                ->orWhere('e.no_memorandum', $request->valor);
        }
        $exoneraciones = $exoneraciones->groupBy('e.nrevision', 'e.no_memorandum', 'u.ubicacion', 'e.status', 'e.turnado', 'e.memo_soporte_dependencia', 'e.fecha_memorandum')
            ->orderBy('e.status', 'desc')
            ->orderBy('e.fecha_memorandum', 'desc')
            ->paginate(100);
        return view('solicitudes.exoneraciones.table', compact('exoneraciones'));
    }

    private function history($revision){
        $history = DB::insert("insert into history_exoneraciones(
            id_exoneracion,folio_grupo,id_unidad_capacitacion,no_memorandum,
            fecha_memorandum,tipo_exoneracion,razon_exoneracion,observaciones,no_convenio,memo_soporte_dependencia,
            iduser_created,iduser_updated,created_at,updated_at,status,nrevision,noficio,foficio,fini,ffin,
            realizo,valido,fenvio,frespuesta,pobservacion,cct,ejercicio,activo,turnado,motivo
        ) 
        select e.id,e.folio_grupo,e.id_unidad_capacitacion,e.no_memorandum,
        e.fecha_memorandum,e.tipo_exoneracion,e.razon_exoneracion,e.observaciones,e.no_convenio,e.memo_soporte_dependencia,
        e.iduser_created,e.iduser_updated,e.created_at,e.updated_at,e.status,e.nrevision,e.noficio,e.foficio,e.fini,e.ffin,
        e.realizo,e.valido,e.fenvio,e.frespuesta,e.pobservacion,e.cct,e.ejercicio,e.activo,e.turnado,e.motivo
        from exoneraciones as e
        where e.nrevision=?",[$revision]);
    }

    private function reverse_exo($nrevision){
        //RECUPERANDO LOS REGISTROS DEL ULTIMO MOVIMIENTO DE HISTORY PARA REVERTIR
        $ids = DB::table('history_exoneraciones')->where('nrevision',$nrevision)->groupby('folio_grupo')->select(DB::raw('MAX(id) as id'))->pluck('id','id');
        $ids = array_values(json_decode(json_encode ( $ids ) , true));
        $ids = implode(',', $ids);
        $this->history( $nrevision);

        $history = DB::update("update exoneraciones SET
            status = h. status, motivo = h.motivo, updated_at = h.updated_at, iduser_updated = h.iduser_updated,
            activo = h.activo, fenvio = h. fenvio, frespuesta= h.frespuesta, turnado = h.turnado
        from( 
            select status, motivo, updated_at, iduser_updated, activo, fenvio, frespuesta, turnado, folio_grupo, nrevision
            from history_exoneraciones where id in ($ids)
            ) as h
        WHERE exoneraciones.folio_grupo = h.folio_grupo and  exoneraciones.nrevision= '$nrevision'");
    }
}