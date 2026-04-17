<?php

namespace App\Http\Controllers\Solicitud;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\HerramientasService;
use PDF;

class exoneracionController extends Controller
{
    function __construct(HerramientasService $herramientas)
    {
        session_start();
        $this->path_files = env("APP_URL").'/storage/uploadFiles';
        $this->herramientas = $herramientas;
    }

    public function index(Request $request){// dd(session('valor'));
        $message = $valor = $memo = $activar = $nrevision = $pdf = null;
        $agregar = true;
        $cursos = $movimientos = [];
        if ($request->memo) {
            session(['valor' => $request->memo]);
        }elseif ($request->valor) {
            session(['valor' => $request->valor]);
        }
        //else $valor = session('valor');
        if (session()->has('valor')) {
            $cursos = DB::table('exoneraciones as e')
                        ->select('e.id','e.folio_grupo','e.nrevision','tc.tipo_curso','tc.unidad','tc.curso','c.costo','tc.dura','tc.inicio','tc.termino','e.foficio',
                        'tc.hombre','tc.mujer','e.fini','e.ffin','tc.nombre as instructor','e.tipo_exoneracion','e.noficio','e.razon_exoneracion','e.observaciones',
                        'e.no_memorandum','e.status','e.turnado','e.memo_soporte_dependencia','e.pobservacion','e.no_convenio','tc.depen','e.fecha_memorandum')
                        ->leftJoin('tbl_cursos as tc','e.folio_grupo','=','tc.folio_grupo')
                        ->leftJoin('alumnos_registro as ar','e.folio_grupo','=','ar.folio_grupo')
                        ->leftJoin('cursos as c','ar.id_curso','=','c.id')
                        ->where('e.nrevision',session('valor'))
                        ->orWhere('e.no_memorandum',session('valor'))
                        ->groupBy('e.id','e.folio_grupo','e.nrevision','tc.tipo_curso','tc.unidad','tc.curso','c.costo','tc.dura','tc.inicio','tc.termino','tc.hombre',
                        'tc.mujer','e.fini','e.ffin','tc.nombre','e.tipo_exoneracion','e.noficio','e.foficio','e.razon_exoneracion','e.observaciones','e.no_memorandum',
                        'tc.depen')
                        ->get();

            if (count($cursos)>0) {
                if(in_array($cursos[0]->status, ['CAPTURA','VALIDADO','EDICION'])){
                    $activar = true;
                }
                if ($cursos[0]->status!='CAPTURA') $agregar = false;

                $valor = session('valor');
                session(['revision' => $cursos[0]->nrevision]);
                session(['memo' => $cursos[0]->no_memorandum]);
                if ($cursos[0]->memo_soporte_dependencia) {
                    $pdf = $this->path_files.$cursos[0]->memo_soporte_dependencia;
                }
                if($cursos[0]->status=='VALIDADO' AND  !$cursos[0]->fini){
                    $movimientos = ['REINICIAR'=>'SOLICITAR REINICIAR','CANCELAR'=>'SOLICITAR CANCELAR','GENERAR'=>'GENERAR FOLIOS Y MEMORANDUM'];
                }elseif($cursos[0]->status=='VALIDADO' AND  $cursos[0]->fini){
                    $movimientos = ['CANCELAR'=>'SOLICITAR CANCELAR','GENERAR'=>'CAMBIAR MEMORANDUM','TURNAR'=>'TURNAR A DTA'];
                }elseif($cursos[0]->status=='AUTORIZADO'){
                    $movimientos = ['SOPORTES'=>'SOLICITAR CAMBIO DE SOPORTES','EDITAR'=>'SOLICITAR EDICIÓN DE ALUMNOS','CANCELAR'=>'SOLICITAR CANCELACION'];
                }elseif($cursos[0]->status=='EDICION'){
                    $movimientos = ['GENERAR'=>'GENERAR MEMORANDUM','TURNAR'=>'TURNAR A DTA'];

                }
            }else{
                $message = "No se encuentran registros que mostrar.";
                $valor = null;
                session()->forget('valor');
            }
        }else {
            session()->forget('valor');
            session()->forget('revision');
            session()->forget('memo');
        }
        if(session('message')) $message = session('message');
        $razon = ['MS'=>'MADRES SOLTERAS','AM'=>'ADULTOS MAYORES', 'BR'=>'BAJOS RECURSOS', 'D'=>'DISCAPACITADOS', 'PPL'=>'PERSONAS PRIVADAS DE LA LIBERTAD',
        'GRS'=>'GRUPOS DE REINSERCION SOCIAL', 'O'=>'OTRO'];
        return view('solicitud.exoneracion.index',compact('message','valor','cursos','activar','pdf','agregar','movimientos','razon'));
    }

    public function store(Request $request){
        $curso = null;
        $message = "Operación fallida, vuelva a intentar..";
        if ($request->grupo) {
            $curso = DB::table('tbl_cursos as tc')
                        ->select(DB::raw("(select sum(hours) from
                         (select ( (( EXTRACT(EPOCH FROM cast(agenda.end as time))-EXTRACT(EPOCH FROM cast(start as time)))/3600)*
                         ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) )
                         as hours
                         from agenda
                         where id_curso = tc.folio_grupo) as t) as horas_agenda"),'ar.id_unidad','ar.cct','tc.tipo','tc.cgeneral','ar.ejercicio','tc.status_solicitud',
                         'tc.dura','tc.status_curso','ar.id_organismo','ar.folio_grupo','tc.status','tc.status_curso','tc.arc','tc.status_solicitud_arc02')
                        ->join('agenda', 'agenda.id_curso','=','tc.folio_grupo')
                        ->leftJoin('alumnos_registro as ar','tc.folio_grupo','=','ar.folio_grupo')
                        ->where('ar.id_organismo','!=',242)
                        ->whereIn('tc.tipo',['EXO','EPAR'])
                        ->where('tc.folio_grupo',$request->grupo)
                        ->whereIn('tc.status',['NO REPORTADO','RETORNO_UNIDAD'])

                        ->where(function($query) {

                            $query->where(function($query) {
                                    $query->where('tc.status_curso','=',null)
                                    ->where(function($query) {
                                        $query->where('tc.status_solicitud','=',null)
                                            ->orWhere('tc.status_solicitud', '=', 'RETORNO');
                                    })

                                    ->where('ar.turnado','=','VINCULACION');

                            })
                            ->orwhere(function($query) {
                                $query->where('tc.status_curso','AUTORIZADO')
                                ->where('tc.arc','02')
                                ->where('tc.status_solicitud_arc02','=',null)
                                ->orwhere('tc.status_solicitud_arc02','<>','AUTORIZADO');
                            });
                        })

                        ->first();// dd($curso);

            if ($curso) {
                if (($curso->tipo != 'EXO') AND (count(DB::table('alumnos_registro')->where('folio_grupo',$curso->folio_grupo)->where('tinscripcion','=','EXONERACION')->get())>0)) {
                    return redirect()->route('solicitud.exoneracion')->with(['message' => 'EL GRUPO NO DEBE TENER EXONERADOS PARA SOLICITUD DE REDUCIÓN DE CUOTA..']);
                }
                if (DB::table('exoneraciones')->where('folio_grupo',$curso->folio_grupo)->where(function($query) { $query->where('status','!=','CANCELADO')->orWhere('status', 'CAPTURA'); })->exists()) {
                    return redirect()->route('solicitud.exoneracion')->with(['message' => 'EL GRUPO SE ENCUENTRA EN USO..']);
                }
                if (($curso->dura == $curso->horas_agenda)) {
                    $organismo = null;
                    if (session('revision')) {
                        if ((DB::table('exoneraciones')->where('nrevision',session('revision'))->where('status','<>','CAPTURA')->exists()==true)) {
                            session()->forget('revision');
                        } elseif ((DB::table('exoneraciones')->where('nrevision',session('revision'))->where('status','=','CAPTURA')->exists()==true)) {
                            $organismo = DB::table('exoneraciones as e')
                                ->select('ar.id_organismo','e.tipo_exoneracion','e.status')
                                ->leftJoin('alumnos_registro as ar','e.folio_grupo','=','ar.folio_grupo')
                                ->whereNotNull('nrevision')
                                ->where('e.nrevision',session('revision'))
                                ->where('e.status','=','CAPTURA')
                                ->first();
                            if (($organismo) AND (($organismo->id_organismo != $curso->id_organismo) OR ($organismo->tipo_exoneracion != $curso->tipo))) {
                                return redirect()->route('solicitud.exoneracion')->with(['message' => 'EL GRUPO NO CORRESPONDE A LA DEPENDENCIA DE LA SOLICITUD O EL TIPO DE EXONERACION ES DIFERENTE..']);
                            }
                        }
                    }
                    if (!session('revision') AND $curso) {
                        $consec = (DB::table('exoneraciones')->where('ejercicio',$curso->ejercicio)->where('cct',$curso->cct)->value(DB::RAW("max(cast(substring(nrevision from '.{4}$') as int))"))) + 1;
                        $consec = str_pad($consec, 4, "0", STR_PAD_LEFT);
                        $revision = "EXO-".$curso->cct.$curso->ejercicio.$consec;
                        session(['revision' => $revision]);
                    }
                    $result = DB::table('exoneraciones')
                                ->UpdateOrInsert(
                                    ['folio_grupo'=>$request->grupo,'nrevision'=>session('revision')],
                                    ['status'=>'CAPTURA','id_unidad_capacitacion'=> $curso->id_unidad, 'tipo_exoneracion'=>$curso->tipo, 'razon_exoneracion'=>$request->opt,
                                    'observaciones'=>$request->observaciones, 'no_convenio'=>$curso->cgeneral, 'iduser_created'=>Auth::user()->id,
                                    'created_at'=>date('Y-m-d H:i:s'),'noficio'=>$request->oficio, 'foficio'=>$request->foficio, 'realizo'=>strtoupper(Auth::user()->name), 'turnado'=>'UNIDAD',
                                    'cct'=>$curso->cct, 'ejercicio'=>$curso->ejercicio]
                                );
                    if ($result) {
                        session(['valor' => session('revision')]);
                        $message = 'Operación Exitosa!!';
                    }
                } else {
                    $message = "Las horas agendadas no corresponden a la duración del curso..";
                }
            }else {
                $message = "No se encontro disponible el grupo ".$request->grupo.", verifique que se haya creado y agendado correctamente.";
            }
        }
        return redirect()->route('solicitud.exoneracion')->with(['message' => $message]);
    }

    public function nuevo(){
        session()->forget('valor');
        session()->forget('revision');
        session()->forget('memo');
        return redirect()->route('solicitud.exoneracion');
    }

    public function delete(Request $request){
        $id = $request->id;
        if ($id) {
            if ((DB::table('exoneraciones')->where('id',$id)->value('fini')) OR
            (DB::table('exoneraciones')->where('id',$id)->value('ffin'))) {
                $result = false;
            } else {
                $result = DB::table('exoneraciones')->where('status','CAPTURA')->where('id',$id)->delete();
            }
        } else {
            $result = false;
        }
        return $result;
    }

    public function preliminar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if (session('revision')) {
            if ($request->hasFile('file_autorizacion')) {
                $name_file = Auth::user()->unidad."_".str_replace('/','-',session('revision'))."_".date('ymdHis')."_".Auth::user()->id;
                $file = $request->file('file_autorizacion');
                $path = "/UNIDAD/revision_exoneracion/";
                $file_result = $this->upload_file($file,$name_file, $path);
                $url_file = $file_result["url_file"];
                if ($file_result) {//dd($file_result);exit;
                    $this->history( session('revision'));
                    $fecha = $request->fecha ?: date('Y-m-d');
                    $result = DB::table('exoneraciones')
                                ->where('nrevision',session('revision'))
                                ->where('status', 'CAPTURA')
                                ->update(['status'=>'PREVALIDACION', 'fenvio'=>date('Y-m-d H:i:s'), 'frespuesta'=>null, 'memo_soporte_dependencia'=>$url_file,
                                        'turnado'=>'DTA',
                                        'fecha_memorandum' => DB::raw("CASE WHEN fecha_memorandum IS NULL THEN '" .$fecha. "' ELSE fecha_memorandum END")
                                    ]);
                    if ($result) {
                        $message = "La PREVALIDACION fué turnada correctamente a la DTA";
                    } else {
                        $message = "Error al turnar la solictud, volver a intentar.";
                    }

                } else {
                    $message = "Error al subir el archivo, volver a intentar.";
                }
            } else {
                $message = "Archivo inválido";
            }
        }
        return redirect()->route('solicitud.exoneracion')->with(['message' => $message]);
    }

    public function enviar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if (session('memo')) {
            if ($request->hasFile('file_autorizacion')) {
                $name_file = Auth::user()->unidad."_".str_replace('/','-',session('memo'))."_".date('ymdHis')."_".Auth::user()->id;
                $file = $request->file('file_autorizacion');
                $path = "/UNIDAD/revision_exoneracion/";
                $file_result = $this->upload_file($file,$name_file, $path);
                $url_file = $file_result["url_file"];
                if ($file_result) {
                    $this->history( session('revision'));
                    $result = DB::table('exoneraciones')->where('no_memorandum',session('memo'))
                        ->update(['status'=>'SOLICITADO', 'turnado'=>'DTA', 'fenvio'=>date('Y-m-d H:i:s'), 'frespuesta'=>null, 'memo_soporte_dependencia'=>$url_file,
                            'pobservacion'=>null]);
                    if ($result) {
                        $message = "La SOLICITUD fué turnada correctamente a la DTA";
                        } else {
                            $message = "Error al turnar la solictud, volver a intentar.";
                        }
                } else {
                    $message = "Error al subir el archivo, volver a intentar.";
                }
            } else {
                $message = "Archivo inválido";
            }
        }
        return redirect()->route('solicitud.exoneracion')->with(['message' => $message]);
    }

    public function generar(Request $request){
        if ($request->valor) {
                $valor = $request->valor;
                $fecha = $request->fecha ?: date('Y-m-d');
                // VALIDA SI EL MEMO NO SE ENCUENTRA EN USO
                $existe_memo = DB::table('exoneraciones')->where('no_memorandum',$request->memo)->where('status', 'AUTORIZADO')->exists();

                if($existe_memo) return "EL NÚMERO DE MEMORÁNDUM INGRESADO YA SE ENCUENTRA ASIGNADO. LE SUGERIMOS VERIFICAR E INGRESAR UN NÚMERO LIBRE.";

                // Actualiza los registros con status VALIDADO
                $result_memo = DB::table('exoneraciones')
                    ->where(function ($query) use ($valor) {
                        $query->where('nrevision', $valor)
                            ->orWhere('no_memorandum', $valor);
                    })->where('status', 'VALIDADO')->update(['no_memorandum' => $request->memo,
                    'fecha_memorandum' => DB::raw("CASE WHEN fecha_memorandum IS NULL THEN '" .$fecha. "' ELSE fecha_memorandum END"),
                ]);

                // Actualiza los registros con status CAPTURA
                $result_fecha = DB::table('exoneraciones')
                    ->where(function ($query) use ($valor) {
                        $query->where('nrevision', $valor)
                            ->orWhere('no_memorandum', $valor);
                    })->where('status', 'CAPTURA')->update(['fecha_memorandum' => $fecha]);

                $marca = $result_memo ? null : true;
                if($result_memo) $valor = $request->memo;

                session(['memo' => $request->memo,'valor'=>$valor]);


            $cursos = DB::table('exoneraciones')
            ->where(function($query) use ($valor) {
                    $query->where('nrevision', $valor)
                        ->orWhere('no_memorandum', $valor);
                })
            ->get();
            foreach ($cursos as $key => $value) {
                $year = date('y');
                if (!($value->fini) OR !($value->ffin)) {
                    $alumnos = count(DB::table('alumnos_registro')
                                ->where('folio_grupo',$value->folio_grupo)
                                ->where('tinscripcion','!=','PAGO ORDINARIO')
                                ->where('eliminado',false)
                                ->get());
                    $ini = (DB::table('exoneraciones')->where('ejercicio',$year)->where('cct',$value->cct)->value(DB::raw('max(ffin)')))+1;
                    $fin = (DB::table('exoneraciones')->where('ejercicio',$year)->where('cct',$value->cct)->value(DB::raw('max(ffin)')))+$alumnos;
                    $result2 = DB::table('exoneraciones')->where('id',$value->id)->update(['fini'=>$ini, 'ffin'=>$fin]);
                }
            }
            if($cursos) {
                $mexoneracion = $date = $alumnos = null;
                $data = [];
                $director= DB::table('tbl_instituto')->select('titular', 'cargo')->first();
                $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first();
                $cursos =  DB::table('exoneraciones as e')
                                ->select('tc.tipo_curso','tc.unidad','tc.curso','c.costo','tc.dura',
                                        DB::raw("to_char(DATE (tc.inicio)::date, 'DD-MM-YYYY') as inicio"),
                                        DB::raw("to_char(DATE (tc.termino)::date, 'DD-MM-YYYY') as termino"),
                                        'tc.mujer','tc.hombre','e.fini','e.ffin',
                                        'tc.nombre as instructor','e.tipo_exoneracion','e.no_convenio','e.noficio',DB::raw("to_char(DATE (e.foficio)::date, 'DD-MM-YYYY') as foficio"),
                                        'e.razon_exoneracion','e.observaciones','tc.hini','tc.hfin',
                                        'tc.depen','e.id_unidad_capacitacion','tc.mod','ar.horario','tc.efisico','tc.tcapacitacion','tc.medio_virtual','tc.dia','e.nrevision',
                                        'tc.folio_grupo','e.no_memorandum',DB::raw("to_char(DATE (e.fecha_memorandum)::date, 'DD-MM-YYYY') as fecha_memorandum"),
                                        DB::raw("
                                            (
                                                SELECT string_agg( '<div>' ||
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
                                                END || 'h. (' ||
                                                TO_CHAR(
                                                    (EXTRACT(EPOCH FROM ((CAST(\"end\" AS time) - CAST(\"start\" AS time)))) / 3600) *
                                                    ((DATE_TRUNC('day', \"end\")::date - DATE_TRUNC('day', \"start\")::date) + 1),
                                                    'FM999990.0'
                                                ) || 'hrs.)'|| '</div>',
                                                E'\n'
                                                ORDER BY DATE(start)
                                                ) AS agenda_texto
                                                FROM agenda
                                                WHERE id_curso = tc.folio_grupo
                                            )::text AS agenda
                                            ")
                            )
                                ->join('tbl_cursos as tc','e.folio_grupo','=','tc.folio_grupo')
                                ->join('agenda','agenda.id_curso','=','e.folio_grupo')

                                ->leftJoin('alumnos_registro as ar','tc.folio_grupo','=','ar.folio_grupo')
                                ->leftJoin('cursos as c','ar.id_curso','=','c.id')
                                ->where(function($query) use ($valor) {
                                    $query->where('nrevision', $valor)
                                        ->orWhere('no_memorandum', $valor);
                                })
                                ->groupBy('tc.tipo_curso','tc.unidad','tc.curso','c.costo','tc.dura','tc.inicio','tc.termino','tc.mujer','tc.hombre','e.fini','e.ffin',
                                'tc.nombre','e.tipo_exoneracion','e.no_convenio','e.noficio','e.foficio','e.razon_exoneracion','e.observaciones',
                                'tc.depen','e.id_unidad_capacitacion','tc.mod','ar.horario','tc.efisico','tc.tcapacitacion','tc.medio_virtual','tc.dia','tc.folio_grupo',
                                'e.no_memorandum','e.fecha_memorandum','tc.hini','tc.hfin','e.nrevision')
                                ->orderBy('e.fini','asc')
                                ->get();    //dd($cursos);
                $reg_unidad = DB::table('tbl_unidades')->select('ubicacion','dgeneral','dunidad','academico','vinculacion','dacademico','pdgeneral','pdacademico',
                                    'pdunidad','pacademico','pvinculacion','municipio','direccion')
                                    ->where('id',$cursos[0]->id_unidad_capacitacion)
                                    ->first(); //dd($reg_unidad);

                $depen = $cursos[0]->depen;
                $date = $cursos[0]->fecha_memorandum;
                if($cursos[0]->no_memorandum)$mexoneracion = $cursos[0]->no_memorandum;
                else $mexoneracion = $cursos[0]->nrevision;
                foreach ($cursos as $key => $value) {
                    $alumnos = DB::table('alumnos_registro as ar')
                                    ->select('ar.nombre','ar.costo','ar.apellido_paterno','ar.apellido_materno',
                                        DB::raw("extract(year from (age('$value->inicio',ap.fecha_nacimiento))) as edad"),DB::raw("substring(ar.curp,11,1) as sexo"))
                                    ->leftJoin('alumnos_pre as ap','ar.id_pre','=','ap.id')
                                    ->where('ar.folio_grupo',$value->folio_grupo)
                                    ->orderBy('ar.apellido_paterno')->orderBy('ar.apellido_materno')->orderBy('ar.nombre')
                                    ->get();
                    $horario = date('H:i', strtotime(str_replace(['a.m.','p.m.'],['am','pm'],$value->hini))).' A '.date('H:i', strtotime(str_replace(['a.m.','p.m.'],['am','pm'],$value->hfin)));
                    $data[$key]['curso'] = $value->curso;
                    $data[$key]['mod'] = $value->mod;
                    $data[$key]['dura'] = $value->dura;
                    $data[$key]['horario'] = $horario;
                    $data[$key]['inicio'] = $value->inicio;
                    $data[$key]['termino'] = $value->termino;
                    $data[$key]['agenda'] = $value->agenda;
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
                $direccion = $reg_unidad->direccion;

                //setlocale(LC_TIME, "spanish");
                //$fecha = strftime("%d de %B del %Y" ,strtotime($date));
                $meses = ['01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre'];
                $mes = $meses[date('m',strtotime($date))];
                $fecha_doc = date('d',strtotime($date)).' de '.$mes.' del '.date('Y',strtotime($date));
                $fecha = '09-12-2024';
                //Seccion para el layout correcto sacando el año;
                $layout_año = $this->herramientas->getPdfLayoutByDate($date);

                $pdf = PDF::loadView('solicitud.exoneracion.Solicitudexoneracion',compact('cursos','mexoneracion','distintivo','fecha_doc','reg_unidad','depen','marca','data','direccion','director','fecha','layout_año'))->setPaper('letter', 'landscape');
                $pdf->setpaper('letter','landscape');
                return $pdf->stream('EXONERACION.pdf');
           // } else {
                return "Error al cargar el memorándum, volver a intentar.";exit;
            }
        } else {
            return "ACCIÓN INVÁLIDA";exit;
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
        $uni = DB::table('tbl_unidades')->where('id',Auth::user()->unidad)->value('ubicacion');
        $exoneraciones = DB::table('exoneraciones as e')
            ->select('e.nrevision','e.no_memorandum','u.ubicacion as unidad','e.status','e.turnado','e.memo_soporte_dependencia','e.fecha_memorandum')
            ->leftJoin('tbl_unidades as u', 'e.id_unidad_capacitacion','=','u.id')
            ->where('u.ubicacion',$uni);
        if ($request->valor) {
            $exoneraciones = $exoneraciones->where('e.nrevision',$request->valor)
                ->orWhere('e.no_memorandum',$request->valor);
        }
        $exoneraciones = $exoneraciones->groupBy('e.nrevision','e.no_memorandum','u.ubicacion','e.status','e.turnado','e.memo_soporte_dependencia','e.fecha_memorandum')
            ->orderBy('e.status','desc')
            ->orderBy('e.fecha_memorandum','desc')
            ->paginate(100);
        return view('solicitud.exoneracion.table',compact('exoneraciones'));
    }

    public function edicion(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if (session('revision')) {
            if ($request->movimiento AND $request->motivo) {
                $history  = $this->history( session('revision'));
                if($history){
                    $result = DB::table('exoneraciones')->where('nrevision',session('revision'))
                    ->update(['status'=>$request->movimiento, 'motivo'=>$request->motivo, 'updated_at'=>date('Y-m-d H:i:s'), 'iduser_updated'=>Auth::user()->id,
                    'activo'=>null, 'fenvio'=>date('Y-m-d H:i:s'), 'frespuesta'=>null, 'turnado'=>'DTA']);
                    if($result)$message = "La SOLICITUD fué turnada correctamente a la DTA";
                }
            } else {
                $message = 'Seleccione el movimiento y escriba el motivo de la solicitud de edición';
            }
        }
        return redirect()->route('solicitud.exoneracion')->with(['message' => $message]);
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
        return $history;
    }

}
