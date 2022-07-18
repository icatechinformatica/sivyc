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
        $message = $valor = $nrevision = $memo = $status = $file = $edicion = null;
        $cursos = $movimientos = [];
        if ($request->valor) {
            $_SESSION['valor'] = $request->valor;
        }
        if (isset($_SESSION['valor'])) {
            $cursos = DB::table('exoneraciones as e')
                        ->select('e.id','e.folio_grupo','e.nrevision','tc.tipo_curso','tc.unidad','tc.curso','c.costo','tc.dura','tc.inicio','tc.termino','e.foficio',
                            'tc.hombre','tc.mujer','e.fini','e.ffin','tc.nombre as instructor','e.tipo_exoneracion','e.noficio','e.razon_exoneracion','e.observaciones',
                            'e.no_memorandum','e.status','e.turnado','e.memo_soporte_dependencia','e.pobservacion','e.no_convenio','tc.depen','e.motivo')
                        ->leftJoin('tbl_cursos as tc','e.folio_grupo','=','tc.folio_grupo')
                        ->leftJoin('alumnos_registro as ar','e.folio_grupo','=','ar.folio_grupo')
                        ->leftJoin('cursos as c','ar.id_curso','=','c.id')
                        ->where('e.nrevision',$_SESSION['valor'])
                        ->orWhere('e.no_memorandum',$_SESSION['valor'])
                        ->groupBy('e.id','tc.tipo_curso','tc.unidad','c.nombre_curso','tc.curso','c.costo','tc.dura','tc.inicio','tc.termino','e.foficio',
                        'tc.hombre','tc.mujer','e.fini','e.ffin','tc.nombre','e.tipo_exoneracion','e.noficio','e.razon_exoneracion','e.observaciones',
                        'e.no_memorandum','e.status','e.turnado','memo_soporte_dependencia','tc.depen','e.motivo')
                        ->get();    //dd($cursos);
            if ( count($cursos) > 0 ) {
                $nrevision = $cursos[0]->nrevision;
                $memo = $cursos[0]->no_memorandum;
                $status = $cursos[0]->status;
                $file = $this->path_files.$cursos[0]->memo_soporte_dependencia;
                $valor = $_SESSION['valor'];
                $_SESSION['revision'] = $nrevision;
                $_SESSION['memo'] = $memo;
                if ($status == 'PREVALIDACION') {
                    $movimientos = ['RETORNAR'=>'RETORNAR', 'VALIDAR'=>'VALIDAR'];
                }else {
                    $movimientos = ['RETORNAR_VALIDADO'=>'RETORNAR','RETORNAR'=>'REINICIAR','CANCELAR'=>'CANCELAR','AUTORIZAR'=>'AUTORIZAR'];
                }
                if (($status == 'SOLICITUD EDITAR') OR ($status == 'SOLICITUD CANCELAR') OR ($status=='ACTUALIZACION SOPORTE')) {
                    $movimientos = ['ACTUALIZACION SOPORTE'=>'ACTUALIZACION DE SOPORTES','SOLICITUD EDITAR'=>'EDITAR','SOLICITUD CANCELAR'=>'CANCELAR'];
                    $edicion = $cursos[0]->motivo;
                }
            } else {
                $message = "No se encuentran registros que mostrar.";
            }
            
        }
        if(session('message')) $message = session('message');
        return view('solicitudes.exoneraciones.index',compact('message','valor','memo','file','cursos','movimientos','status','edicion'));
    }

    public function retornar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if (isset($_SESSION['revision'])) {
            foreach ($request->respuesta as $key => $value) {
                $result = DB::table('exoneraciones')->where('nrevision', $_SESSION['revision'])->where('folio_grupo', $key)
                    ->update([
                        'status' => null, 'frespuesta' => date('Y-m-d H:i:s'), 'pobservacion' => $value, 'turnado' => 'UNIDAD', 'activo'=>null,
                        'no_memorandum'=>null, 'fecha_memorandum'=>null
                    ]);
                if ($result) {
                    $curso = DB::table('exoneraciones')->where('nrevision',$_SESSION['revision'])->where('folio_grupo',$key)->first();
                    $result2 = DB::table('history_exoneraciones')->insert([
                        'id_exoneracion' => $curso->id, 'folio_grupo' => $curso->folio_grupo, 'id_unidad_capacitacion' => $curso->id_unidad_capacitacion,
                        'no_memorandum' => $curso->no_memorandum, 'fecha_memorandum' => $curso->fecha_memorandum, 'tipo_exoneracion' => $curso->tipo_exoneracion,
                        'razon_exoneracion' => $curso->razon_exoneracion, 'observaciones' => $curso->observaciones, 'no_convenio' => $curso->no_convenio,
                        'memo_soporte_dependencia' => $curso->memo_soporte_dependencia, 'iduser_created' => $curso->iduser_created,
                        'iduser_updated' => $curso->iduser_updated, 'created_at' => $curso->created_at, 'updated_at' => $curso->updated_at, 'status' => 'RETORNADO',
                        'nrevision' => $curso->nrevision, 'noficio' => $curso->noficio, 'foficio' => $curso->foficio, 'fini' => $curso->fini, 'ffin' => $curso->ffin,
                        'realizo' => $curso->realizo, 'valido'=>strtoupper(Auth::user()->name), 'fenvio' => $curso->fenvio, 'frespuesta' => $curso->frespuesta,
                        'pobservacion' => $curso->pobservacion, 'cct' => $curso->cct, 'ejercicio' => $curso->ejercicio, 'activo' => $curso->activo,
                        'turnado' => $curso->turnado, 'motivo' => $curso->motivo
                    ]);
                    $message = "La solicitud fue retonado a la Unidad.";
                }
            }
        }
        return redirect()->route('solicitudes.exoneracion')->with(['message' => $message]);
    }

    public function retornar_validado(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if (isset($_SESSION['memo'])) {
            foreach ($request->respuesta as $key => $value) {
                $result = DB::table('exoneraciones')->where('no_memorandum',$_SESSION['memo'])->where('folio_grupo',$key)
                        ->update(['status'=>'VALIDADO', 'frespuesta'=>date('Y-m-d H:i:s'), 'pobservacion'=>$value, 'turnado'=>'UNIDAD']);
                if ($result) {
                    $curso = DB::table('exoneraciones')->where('no_memorandum',$_SESSION['memo'])->where('folio_grupo',$key)->first();
                    $result2 = DB::table('history_exoneraciones')->insert([
                        'id_exoneracion' => $curso->id, 'folio_grupo' => $curso->folio_grupo, 'id_unidad_capacitacion' => $curso->id_unidad_capacitacion,
                        'no_memorandum' => $curso->no_memorandum, 'fecha_memorandum' => $curso->fecha_memorandum, 'tipo_exoneracion' => $curso->tipo_exoneracion,
                        'razon_exoneracion' => $curso->razon_exoneracion, 'observaciones' => $curso->observaciones, 'no_convenio' => $curso->no_convenio,
                        'memo_soporte_dependencia' => $curso->memo_soporte_dependencia, 'iduser_created' => $curso->iduser_created,
                        'iduser_updated' => $curso->iduser_updated, 'created_at' => $curso->created_at, 'updated_at' => $curso->updated_at, 'status' => 'RETORNADO_VALIDADO',
                        'nrevision' => $curso->nrevision, 'noficio' => $curso->noficio, 'foficio' => $curso->foficio, 'fini' => $curso->fini, 'ffin' => $curso->ffin,
                        'realizo' => $curso->realizo, 'valido' => $curso->valido, 'fenvio' => $curso->fenvio, 'frespuesta' => $curso->frespuesta,
                        'pobservacion' => $curso->pobservacion, 'cct' => $curso->cct, 'ejercicio' => $curso->ejercicio, 'activo' => $curso->activo,
                        'turnado' => $curso->turnado, 'motivo' => $curso->motivo
                    ]);
                    $message = "La solicitud fue retonado a la Unidad.";
                }
            }
        }
        return redirect()->route('solicitudes.exoneracion')->with(['message'=>$message]);
    }

    public function validar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if (isset($_SESSION['revision'])) {
            foreach ($request->respuesta as $key => $value) {
                $result = DB::table('exoneraciones')->where('nrevision',$_SESSION['revision'])->where('folio_grupo',$key)
                        ->update(['status'=>'VALIDADO', 'valido'=>strtoupper(Auth::user()->name), 'frespuesta'=>date('Y-m-d H:i:s'), 'pobservacion'=>null, 'turnado'=>'UNIDAD']);
                if ($result) {
                    $curso = DB::table('exoneraciones')->where('nrevision',$_SESSION['revision'])->where('folio_grupo',$key)->first();
                    $result2 = DB::table('history_exoneraciones')->insert([
                        'id_exoneracion' => $curso->id, 'folio_grupo' => $curso->folio_grupo, 'id_unidad_capacitacion' => $curso->id_unidad_capacitacion,
                        'no_memorandum' => $curso->no_memorandum, 'fecha_memorandum' => $curso->fecha_memorandum, 'tipo_exoneracion' => $curso->tipo_exoneracion,
                        'razon_exoneracion' => $curso->razon_exoneracion, 'observaciones' => $curso->observaciones, 'no_convenio' => $curso->no_convenio,
                        'memo_soporte_dependencia' => $curso->memo_soporte_dependencia, 'iduser_created' => $curso->iduser_created,
                        'iduser_updated' => $curso->iduser_updated, 'created_at' => $curso->created_at, 'updated_at' => $curso->updated_at, 'status' => 'VALIDADO',
                        'nrevision' => $curso->nrevision, 'noficio' => $curso->noficio, 'foficio' => $curso->foficio, 'fini' => $curso->fini, 'ffin' => $curso->ffin,
                        'realizo' => $curso->realizo, 'valido' => $curso->valido, 'fenvio' => $curso->fenvio, 'frespuesta' => $curso->frespuesta,
                        'pobservacion' => $curso->pobservacion, 'cct' => $curso->cct, 'ejercicio' => $curso->ejercicio, 'activo' => $curso->activo,
                        'turnado' => $curso->turnado, 'motivo' => $curso->motivo
                    ]);
                    $message = 'Operación Exitosa!!';
                }
            }
        }
        return redirect()->route('solicitudes.exoneracion')->with(['message'=>$message]);
    }

    public function autorizar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if ($_SESSION['memo']) {
            if ($request->hasFile('file_autorizacion')) {
                $name_file = Auth::user()->unidad."_".str_replace('/','-',$_SESSION['memo'])."_".date('ymdHis')."_".Auth::user()->id;                                
                $file = $request->file('file_autorizacion');
                $path = "/UNIDAD/exoneracion/"; 
                $file_result = $this->upload_file($file,$name_file, $path);                
                $url_file = $file_result["url_file"];
                if ($file_result) {
                    $result = DB::table('exoneraciones')->where('no_memorandum',$_SESSION['memo'])
                        ->update(['status'=>'AUTORIZADO', 'frespuesta'=>date('Y-m-d H:i:s'), 'memo_soporte_dependencia'=>$url_file,
                                'valido'=>strtoupper(Auth::user()->name), 'activo'=>'true']);
                    if ($result) {
                        $message = 'Operación Exitosa!!';
                        $c = DB::table('tbl_cursos as c')->join('exoneraciones as e', 'c.folio_grupo', '=', 'e.folio_grupo')
                            ->where('e.no_memorandum', $_SESSION['memo'])
                            ->where('e.status','AUTORIZADO')
                            ->where('e.activo', 'true')
                            ->update(['c.mexoneracion' => $_SESSION['memo']]);
                        $cursos = DB::table('exoneraciones')->where('no_memorandum',$_SESSION['memo'])->get();
                        foreach ($cursos as $key => $value) {
                            $result2 = DB::table('history_exoneraciones')->insert([
                                'id_exoneracion' => $value->id, 'folio_grupo' => $value->folio_grupo, 'id_unidad_capacitacion' => $value->id_unidad_capacitacion,
                                'no_memorandum' => $value->no_memorandum, 'fecha_memorandum' => $value->fecha_memorandum, 'tipo_exoneracion' => $value->tipo_exoneracion,
                                'razon_exoneracion' => $value->razon_exoneracion, 'observaciones' => $value->observaciones, 'no_convenio' => $value->no_convenio,
                                'memo_soporte_dependencia' => $value->memo_soporte_dependencia, 'iduser_created' => $value->iduser_created,
                                'iduser_updated' => $value->iduser_updated, 'created_at' => $value->created_at, 'updated_at' => $value->updated_at, 'status' => 'AUTORIZADO',
                                'nrevision' => $value->nrevision, 'noficio' => $value->noficio, 'foficio' => $value->foficio, 'fini' => $value->fini, 'ffin' => $value->ffin,
                                'realizo' => $value->realizo, 'valido' => $value->valido, 'fenvio' => $value->fenvio, 'frespuesta' => $value->frespuesta,
                                'pobservacion' => $value->pobservacion, 'cct' => $value->cct, 'ejercicio' => $value->ejercicio, 'activo' => $value->activo,
                                'turnado' => $value->turnado, 'motivo' => $value->motivo
                            ]);
                        }  
                    }
                } else {
                    $message = "Error al subir el archivo, volver a intentar.";
                }
            } else {
                $message = "Archivo inválido";
            }  
        }
        return redirect()->route('solicitudes.exoneracion')->with(['message'=>$message]);
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
                                    'e.razon_exoneracion','e.observaciones',
                                    'tc.depen','e.id_unidad_capacitacion','tc.mod','ar.horario','tc.efisico','tc.tcapacitacion','tc.medio_virtual','tc.dia',
                                    'tc.folio_grupo','e.no_memorandum',DB::raw("to_char(DATE (e.fecha_memorandum)::date, 'DD-MM-YYYY') as fecha_memorandum"))
                            ->leftJoin('tbl_cursos as tc','e.folio_grupo','=','tc.folio_grupo')
                            ->leftJoin('alumnos_registro as ar','tc.folio_grupo','=','ar.folio_grupo')
                            ->leftJoin('cursos as c','ar.id_curso','=','c.id')
                            ->where('e.nrevision',$_SESSION['revision'])
                            ->groupBy('tc.tipo_curso','tc.unidad','tc.curso','c.costo','tc.dura','tc.inicio','tc.termino','tc.mujer','tc.hombre','e.fini','e.ffin',
                            'tc.nombre','e.tipo_exoneracion','e.no_convenio','e.noficio','e.foficio','e.razon_exoneracion','e.observaciones',
                            'tc.depen','e.id_unidad_capacitacion','tc.mod','ar.horario','tc.efisico','tc.tcapacitacion','tc.medio_virtual','tc.dia','tc.folio_grupo',
                            'e.no_memorandum','e.fecha_memorandum','e.nrevision')
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
                                ->select('ap.apellido_paterno','ap.apellido_materno','ap.nombre','ap.sexo','ar.costo',
                                    DB::raw("extract(year from (age('$value->inicio',ap.fecha_nacimiento))) as edad"))
                                ->leftJoin('alumnos_pre as ap','ar.id_pre','=','ap.id')
                                ->where('ar.folio_grupo',$value->folio_grupo)
                                ->get();
                $data[$key]['curso'] = $value->curso;
                $data[$key]['mod'] = $value->mod;
                $data[$key]['dura'] = $value->dura;
                $data[$key]['horario'] = $value->horario;
                $data[$key]['inicio'] = $value->inicio;
                $data[$key]['termino'] = $value->termino;
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

    public function cancelar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if ($_SESSION['memo']) {
            $result = DB::table('exoneraciones')->where('no_memorandum', $_SESSION['memo'])->update([
                'status' => 'CANCELADO', 'activo' => 'false', 'motivo' => $request->motivo,
                'frespuesta' => date('Y-m-d H:i:s')
            ]);
            if ($result) {
                $message = 'Operación Exitosa!!';
                $c = DB::table('tbl_cursos as c')->join('exoneraciones as e','c.folio_grupo','=','e.folio_grupo')
                    ->where('e.no_memorandum',$_SESSION['memo'])
                    ->update(['c.mexoneracion'=>'0']);
                $cursos = DB::table('exoneraciones')->where('no_memorandum', $_SESSION['memo'])->get();
                foreach ($cursos as $key => $value) {
                    $result2 = DB::table('history_exoneraciones')->insert([
                        'id_exoneracion' => $value->id, 'folio_grupo' => $value->folio_grupo, 'id_unidad_capacitacion' => $value->id_unidad_capacitacion,
                        'no_memorandum' => $value->no_memorandum, 'fecha_memorandum' => $value->fecha_memorandum, 'tipo_exoneracion' => $value->tipo_exoneracion,
                        'razon_exoneracion' => $value->razon_exoneracion, 'observaciones' => $value->observaciones, 'no_convenio' => $value->no_convenio,
                        'memo_soporte_dependencia' => $value->memo_soporte_dependencia, 'iduser_created' => $value->iduser_created,
                        'iduser_updated' => $value->iduser_updated, 'created_at' => $value->created_at, 'updated_at' => $value->updated_at, 'status' => 'CANCELADO',
                        'nrevision' => $value->nrevision, 'noficio' => $value->noficio, 'foficio' => $value->foficio, 'fini' => $value->fini, 'ffin' => $value->ffin,
                        'realizo' => $value->realizo, 'valido'=>strtoupper(Auth::user()->name), 'fenvio' => $value->fenvio, 'frespuesta' => $value->frespuesta,
                        'pobservacion' => $value->pobservacion, 'cct' => $value->cct, 'ejercicio' => $value->ejercicio, 'activo' => $value->activo,
                        'turnado' => $value->turnado, 'motivo' => $value->motivo
                    ]);
                }
            }
        }
        return redirect()->route('solicitudes.exoneracion')->with(['message' => $message]);
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

    public function editar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if ($_SESSION['memo']) {
            if ($request->movimiento) {
                $result = DB::table('exoneraciones')
                    ->where('no_memorandum', $_SESSION['memo'])
                    ->update([
                        'status' => 'EDICION', 'turnado' => 'UNIDAD', 'frespuesta' => date('Y-m-d H:i:s')
                    ]);
                if ($result) {
                    $message = 'Operación Exitosa!!';
                    $c = DB::table('tbl_cursos as c')->join('exoneraciones as e','c.folio_grupo','=','e.folio_grupo')
                        ->where('e.no_memorandum',$_SESSION['memo'])
                        ->update(['c.mexoneracion'=>'0']);
                    $cursos = DB::table('exoneraciones')->where('no_memorandum', $_SESSION['memo'])->get();
                    foreach ($cursos as $key => $value) {
                        $result2 = DB::table('history_exoneraciones')->insert([
                            'id_exoneracion' => $value->id, 'folio_grupo' => $value->folio_grupo, 'id_unidad_capacitacion' => $value->id_unidad_capacitacion,
                            'no_memorandum' => $value->no_memorandum, 'fecha_memorandum' => $value->fecha_memorandum, 'tipo_exoneracion' => $value->tipo_exoneracion,
                            'razon_exoneracion' => $value->razon_exoneracion, 'observaciones' => $value->observaciones, 'no_convenio' => $value->no_convenio,
                            'memo_soporte_dependencia' => $value->memo_soporte_dependencia, 'iduser_created' => $value->iduser_created,
                            'iduser_updated' => $value->iduser_updated, 'created_at' => $value->created_at, 'updated_at' => $value->updated_at, 'status' => 'EDICION',
                            'nrevision' => $value->nrevision, 'noficio' => $value->noficio, 'foficio' => $value->foficio, 'fini' => $value->fini, 'ffin' => $value->ffin,
                            'realizo' => $value->realizo, 'valido'=>strtoupper(Auth::user()->name), 'fenvio' => $value->fenvio, 'frespuesta' => $value->frespuesta,
                            'pobservacion' => $value->pobservacion, 'cct' => $value->cct, 'ejercicio' => $value->ejercicio, 'activo' => $value->activo,
                            'turnado' => $value->turnado, 'motivo' => $value->motivo
                        ]); 
                    }
                }
            }
        }
        return redirect()->route('solicitudes.exoneracion')->with(['message'=>$message]);
    }

    public function asoporte(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if (isset($_SESSION['memo'])) {
            if ($request->movimiento) {
                $result = DB::table('exoneraciones')->where('no_memorandum',$_SESSION['memo'])
                        ->update(['status'=>'VALIDADO', 'frespuesta'=>date('Y-m-d H:i:s'), 'turnado'=>'UNIDAD']);
                if ($result) {
                    $cursos = DB::table('exoneraciones')->where('no_memorandum', $_SESSION['memo'])->get();
                    foreach ($cursos as $key => $value) {
                        $result2 = DB::table('history_exoneraciones')->insert([
                            'id_exoneracion' => $value->id, 'folio_grupo' => $value->folio_grupo, 'id_unidad_capacitacion' => $value->id_unidad_capacitacion,
                            'no_memorandum' => $value->no_memorandum, 'fecha_memorandum' => $value->fecha_memorandum, 'tipo_exoneracion' => $value->tipo_exoneracion,
                            'razon_exoneracion' => $value->razon_exoneracion, 'observaciones' => $value->observaciones, 'no_convenio' => $value->no_convenio,
                            'memo_soporte_dependencia' => $value->memo_soporte_dependencia, 'iduser_created' => $value->iduser_created,
                            'iduser_updated' => $value->iduser_updated, 'created_at' => $value->created_at, 'updated_at' => $value->updated_at, 'status' => 'ACTUALIZACION SOPORTE',
                            'nrevision' => $value->nrevision, 'noficio' => $value->noficio, 'foficio' => $value->foficio, 'fini' => $value->fini, 'ffin' => $value->ffin,
                            'realizo' => $value->realizo, 'valido'=>strtoupper(Auth::user()->name), 'fenvio' => $value->fenvio, 'frespuesta' => $value->frespuesta,
                            'pobservacion' => $value->pobservacion, 'cct' => $value->cct, 'ejercicio' => $value->ejercicio, 'activo' => $value->activo,
                            'turnado' => $value->turnado, 'motivo' => $value->motivo
                        ]); 
                    }
                    $message = "La solicitud fue retonado a la Unidad.";
                }
            }
        }
        return redirect()->route('solicitudes.exoneracion')->with(['message'=>$message]);
    }
}
