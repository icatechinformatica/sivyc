<?php

namespace App\Http\Controllers\Solicitud;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Models\cat\catUnidades;
use App\Models\cat\catApertura;
use App\Models\tbl_curso;
use App\Models\Inscripcion;
use App\Models\Alumno;
use App\Agenda;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\QueryException;

class aperturaController extends Controller
{
    use catUnidades;
    use catApertura;
    private $validationRules = [
        'munidad'=> ['required'], 'hini'=> ['required'], 'hfin'=> ['required'],'dia'=> ['required'],'inicio'=> ['required'],'termino'=> ['required'],
        'plantel'=> ['required'], 'sector'=> ['required'],'programa'=> ['required'],'id_municipio'=> ['required'],'depen'=> ['required'],
        'tcurso' => ['required'], 'instructor' => ['required'], 'observaciones' => ['required']
    ];
    private $validationMessages = [
        'munidad.required' => 'Favor de ingresar el memorandum .',
        'hini.required' => 'Favor de ingresar la hora de inicio.',
        'hfin.required' => 'Favor de ingresar la hora final.',
        'dia.required' => 'Favor de ingresar los días.',
        'inicio.required' => 'Favor de ingresar la Fecha Inicial.',
        'termino.required' => 'Favor de ingresar la Fecha Final.',
        'plantel.required' => 'Favor de ingresar el Plantel.',
        'sector.required' => 'Favor de ingresar el Sector.',
        'programa.required' => 'Favor de ingresar el Programa.',
        'id_municipio.required' => 'Favor de ingresar la Municipio.',
        'depen.required' => 'Favor de ingresar la Dependencia.',
        'tcurso.required' => 'Favor de ingresar el Servicio.',
        'instructores.required' => 'Favor de ingresar el Instructor.',
        'observaciones.required' => 'Favor de ingresar las Observaciones.'
    ];

    function __construct() {
        session_start();
        $this->ejercicio = date("y");
        $this->middleware('auth');
        $this->path_pdf = "/DTA/solicitud_folios/";
        $this->path_files = env("APP_URL").'/storage/uploadFiles';
        $this->middleware(function ($request, $next) {
            $this->id_user = Auth::user()->id;
            $this->realizo = Auth::user()->name;
            $this->id_unidad = Auth::user()->unidad;
            $this->tcuota = $this->tcuota();
            $this->data = $this->unidades_user('unidad');
            $_SESSION['unidades'] =  $this->data['unidades'];
            return $next($request);
        });
    }

    public function index(Request $request){
        $valor = $efisico = $grupo = $alumnos = $message = $medio_virtual = $depen = $exoneracion = $instructor = $plantel = $programa = $sector = $tcurso = $tcuota =
        $muni = $instructores = $convenio = $localidad = $comprobante = $exonerado = NULL;
        if($request->valor)  $valor = $request->valor;
        elseif(isset($_SESSION['folio'])) $valor = $_SESSION['folio'];
        $_SESSION['alumnos'] = NULL;
        if($valor){
            $grupo =  DB::table('alumnos_registro as ar')->select('ar.id_curso','ar.unidad','ar.horario','ar.inicio','ar.termino','e.nombre as espe','a.formacion_profesional as area',
                'ar.folio_grupo','ar.tipo_curso as tcapacitacion','c.nombre_curso as curso','ar.mod','ar.horario','c.horas','c.costo as costo_individual','c.id_especialidad','ar.comprobante_pago',
                DB::raw("SUM(CASE WHEN substring(ap.curp,11,1) ='H' THEN 1 ELSE 0 END) as hombre"),DB::raw("SUM(CASE WHEN substring(ap.curp,11,1)='M' THEN 1 ELSE 0 END) as mujer"),'c.memo_validacion as mpaqueteria',
                'tc.nota',DB::raw(" COALESCE(tc.clave, '0') as clave"),'ar.id_muni','ar.clave_localidad','ar.organismo_publico','ar.id_organismo','tc.status_solicitud',
                'tc.id_municipio','tc.status_curso','tc.plantel', 'tc.dia', 'tdias', 'id_vulnerable', 'ar.turnado','tc.instructor_mespecialidad','tc.dura',
                'tc.sector','tc.programa','tc.efisico','tc.depen','tc.cgeneral','tc.fcgen','tc.cespecifico','tc.fcespe','tc.mexoneracion','tc.medio_virtual',
                'tc.id_instructor','tc.tipo','tc.link_virtual','tc.munidad','tc.costo','tc.tipo','tc.status','tc.id','e.clave as clave_especialidad','tc.arc','tc.tipo_curso','ar.id_cerss','c.rango_criterio_pago_maximo as cp',
                'ar.folio_pago','ar.fecha_pago')
                ->join('alumnos_pre as ap','ap.id','ar.id_pre')
                ->join('cursos as c','ar.id_curso','c.id')
                ->join('especialidades as e','e.id','c.id_especialidad') ->join('area as a','a.id','c.area')
                ->leftjoin('tbl_cursos as tc','tc.folio_grupo','ar.folio_grupo')
                ->where('ar.turnado','<>','VINCULACION')
                ->where('ar.folio_grupo',$valor);
            if($_SESSION['unidades']) $grupo = $grupo->whereIn('ar.unidad',$_SESSION['unidades']);
            $grupo = $grupo->groupby('ar.mod','ar.id_curso','ar.unidad','ar.horario', 'ar.folio_grupo','ar.tipo_curso','ar.horario','tc.arc','ar.id_cerss','ar.clave_localidad','ar.organismo_publico','ar.id_organismo',
                'e.id','a.formacion_profesional','tc.id','c.id','ar.inicio','ar.termino','ar.comprobante_pago','ar.id_muni','ar.id_vulnerable','ar.turnado',
                'ar.folio_pago','ar.fecha_pago')->first(); //dd($grupo);

            // var_dump($grupo);exit;
            if($grupo){
                $_SESSION['folio'] = $grupo->folio_grupo;
                $anio_hoy = date('y');
                if ($grupo->comprobante_pago) {
                    $comprobante = $this->path_files.$grupo->comprobante_pago;
                }
                $muni = DB::table('tbl_municipios')->where('id_estado','7')->where('id',$grupo->id_muni)->orderby('muni')->pluck('muni')->first();
                $localidad = DB::table('tbl_localidades')->where('clave',$grupo->clave_localidad)->pluck('localidad')->first();

                $alumnos = DB::table('tbl_inscripcion as i')->select('i.*', DB::raw("'VIEW' as mov"))->where('i.folio_grupo',$valor)->orderby('alumno','ASC')->get();
               // var_dump($alumnos);exit;

                if(count($alumnos)==0){
                    $alumnos = DB::table('alumnos_registro as ar')->select('ar.id as id_reg','ap.curp','ap.nombre','ap.apellido_paterno','ap.apellido_materno','ap.fecha_nacimiento AS FN','ap.sexo AS SEX',
                    DB::raw("CONCAT(ap.apellido_paterno,' ', ap.apellido_materno,' ',ap.nombre) as alumno"),'ar.id_cerss', 'ap.lgbt',
                    'ap.estado_civil','ap.discapacidad','ap.nacionalidad','ap.etnia','ap.indigena','ap.inmigrante','ap.madre_soltera','ap.familia_migrante',
                    'ar.costo','ar.tinscripcion',DB::raw("'0' as calificacion"),'ap.ultimo_grado_estudios as escolaridad','ap.empleado','ar.abrinscri',
                    'ap.matricula', 'ar.id_pre','ar.id', DB::raw("substring(curp,11,1) as sexo"),'ap.id_gvulnerable',
                    DB::raw("substring(curp,5,2) as anio_nac"),
                    DB::raw("CASE WHEN substring(curp,5,2) <='".$anio_hoy."' THEN CONCAT('20',substring(curp,5,2),'-',substring(curp,7,2),'-',substring(curp,9,2))
                        ELSE CONCAT('19',substring(curp,5,2),'-',substring(curp,7,2),'-',substring(curp,9,2)) END AS fecha_nacimiento
                    "),
                    DB::raw("'INSERT' as mov"))
                    ->join('alumnos_pre as ap','ap.id','ar.id_pre')->where('ar.folio_grupo',$valor )
                    ->where('ar.eliminado',false)->orderby('ap.apellido_paterno','ASC')->orderby('ap.apellido_materno','ASC')->orderby('ap.nombre','ASC')->get();
                }
                $_SESSION['alumnos'] = $alumnos;
                $_SESSION['grupo'] = $grupo;
                //var_dump($alumnos);exit;

                $plantel = $this->plantel();

                if($grupo->organismo_publico AND $grupo->mod=='CAE'){
                    $organismo = DB::table('organismos_publicos')->where('id',$grupo->id_organismo)->value('organismo');
                    $convenio_t = DB::table('convenios')
                        ->select('no_convenio',db::raw("to_char(DATE (fecha_firma)::date, 'YYYY-MM-DD') as fecha_firma"))
                        ->where(db::raw("to_char(DATE (fecha_vigencia)::date, 'YYYY-MM-DD')"),'>=',$grupo->termino)
                        ->where('institucion',$organismo)
                        ->where('activo','true')->first();
                    $convenio = [];
                    if ($convenio_t) {
                        foreach ($convenio_t as $key=>$value) {
                            $convenio[$key] = $value;
                        }
                    }else {
                        $convenio['no_convenio'] = '0';
                        $convenio['fecha_firma'] = '';
                        $convenio['sector'] = null;
                    }
                }
                if(!$convenio){
                    $convenio['no_convenio'] = '0';
                    $convenio['fecha_firma'] = '';
                    $convenio['sector'] = null;
                }
                $sector = DB::table('organismos_publicos')->where('id',$grupo->id_organismo)->value('sector');
                $programa = $this->programa();

                $instructor = $this->instructor($grupo->id_instructor);
                $instructores = $this->instructores($grupo);    //dd($convenio);
                $exoneracion = $this->exoneracion($this->id_unidad);
                $exoneracion["NINGUNO"] = "NINGUNO";
                $efisico = $this->efisico();
                $exonerado = DB::table('exoneraciones')->where('folio_grupo',$grupo->folio_grupo)->where('status','<>',null)->where('status','<>','CANCELADO')->exists();

                $medio_virtual = $this->medio_virtual();

                $tcurso = $this->tcurso();
                //var_dump($instructor);exit;
                if($grupo->clave !='0') $message = "Clave de Apertura Asignada";
                elseif($grupo->status_curso) $message = "Estatus: ". $grupo->status_curso;
                if($grupo->tipo) $tcuota = $this->tcuota[$grupo->tipo];
            }else $message = "Grupo número ".$valor .", turnado a VINCULACIÓN.";
        }
        $tinscripcion = $this->tinscripcion();
        if(session('message')) $message = session('message');//dd($grupo);
        return view('solicitud.apertura.index', compact('comprobante','efisico','message','grupo','alumnos','plantel','depen','sector','programa',
            'instructor','exoneracion','medio_virtual','tcurso','tinscripcion','tcuota','muni','instructores','convenio','localidad','exonerado'));
    }

    public function search(Request $request){
        $_SESSION = null;
        $aperturas = DB::table('tbl_cursos as tc')
            ->select('tc.unidad','tc.num_revision','tc.munidad','tc.file_arc01','tc.turnado','tc.status_curso','tc.status_solicitud','tc.status','tc.pdf_curso','tc.fecha_apertura')
            ->leftJoin('alumnos_registro as a','tc.folio_grupo','=','a.folio_grupo')
            ->leftJoin('tbl_unidades as u', 'tc.unidad','=','u.unidad')
            ->where('a.turnado','<>','VINCULACION')
            ->where('u.id','=',Auth::user()->unidad);
        if ($request->valor) {
            $aperturas = $aperturas->where('tc.munidad','=',$request->valor)
                ->orWhere('tc.num_revision','=',$request->valor);
        }
        $aperturas = $aperturas->groupBy('tc.unidad','tc.num_revision','tc.munidad','tc.file_arc01','tc.turnado','tc.status_curso','tc.status_solicitud','tc.status','tc.pdf_curso','tc.fecha_apertura')
            ->orderBy('tc.fecha_apertura','desc')
            ->paginate(50);
        return view('solicitud.apertura.buzon',compact('aperturas'));
    }

    public function cgral(Request $request){
        $convenio = $json = [];
        if($request->id AND $request->mod=='CAE')
            $convenio = DB::table('convenios')->select('no_convenio','fecha_firma')->where('institucion',$request->id)->where('activo','true')->first();
        if(!$convenio){
            $convenio['no_convenio'] = '0';
            $convenio['fecha_firma'] = '';
        }
        $json = json_encode($convenio);
        return $json;
    }


    public function regresar(Request $request){
       $message = 'Operación fallida, vuelva a intentar..';
        if($_SESSION['folio']){
            if (DB::table('exoneraciones')->where('folio_grupo',$_SESSION['folio'])->where('status','!=', null)->where('status','!=','CANCELADO')->exists()) {
                $message = "Solicitud de Exoneración o Reducción de couta en Proceso..";
            } else {
                $result = DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio'])->update(['turnado' => "VINCULACION",'fecha_turnado' => date('Y-m-d')]);
                $agenda = DB::table('agenda')->where('id_curso', $_SESSION['folio'])->delete();
                $curso = DB::table('tbl_cursos')->where('folio_grupo', $_SESSION['folio'])->update(['tdias'=>null,'dia'=>null,'fecha_arc01'=>null,
                                                                                                    'id_instructor'=>0]);
                //$_SESSION['folio'] = null;
                // unset($_SESSION['folio']);
                if($result){
                    $message = "El grupo fué turnado correctamente a VINCULACIÓN";
                    unset($_SESSION['folio']);
                }
            }
        }
        return redirect('solicitud/apertura')->with('message',$message);
   }



    public function store(Request $request, \Illuminate\Validation\Factory $validate)
    {
        $message = 'Operación fallida, vuelva a intentar..';
        /*
        $validator = $validate->make($request->all(), $this->validationRules,$this->validationMessages);
        if ($validator->fails()) {
                $message = 'Operación inválida, vuelva a intentar..';
                return redirect('solicitud/apertura')->with('message',$message)
                    ->withErrors($validator)
                    ->withInput();
        }else
        */

        if ($_SESSION['folio'] and $_SESSION['grupo'] and $_SESSION['alumnos']) {
            $grupo = $_SESSION['grupo'];   //var_dump($grupo);exit;
            $horas = round((strtotime($request->hfin) - strtotime($request->hini)) / 3600, 2);
            if (DB::table('tbl_cursos')->where('folio_grupo', '!=', $_SESSION['folio'])->whereNotNull('status_solicitud')
            ->whereRaw("(munidad = '$request->munidad' or num_revision = '$request->munidad')")->exists()) {
                return redirect('solicitud/apertura')->with('message', 'El numero de revisión ya esta ocupado.');
            }
            if ($request->tcurso == "CERTIFICACION" and $horas == 10 or $request->tcurso == "CURSO") {
                $alumnos = $_SESSION['alumnos'];   //var_dump($alumnos);exit;
                $unidad = DB::table('tbl_unidades')->select('cct', 'plantel')->where('unidad', $grupo->unidad)->first();
                $municipio = $cct = DB::table('tbl_municipios')->select('muni', 'ze')->where('id', $grupo->id_muni)->first();
                $hini = date("h:i a", strtotime($request->hini));
                $hfin = date("h:i a", strtotime($request->hfin));
                $hini = str_replace(['am', 'pm'], ['a.m.', 'p.m.'], $hini);
                $hfin = str_replace(['am', 'pm'], ['a.m.', 'p.m.'], $hfin);

                $instructor = DB::table('instructores')
                ->select(
                    'instructores.id',
                    DB::raw('CONCAT("apellidoPaterno", ' . "' '" . ' ,"apellidoMaterno",' . "' '" . ',instructores.nombre) as instructor'),
                    'curp',
                    'rfc',
                    'sexo',
                    'tipo_honorario',
                    'instructor_perfil.grado_profesional as escolaridad',
                    'instructor_perfil.estatus as titulo',
                    'especialidad_instructores.memorandum_validacion as mespecialidad',
                    'especialidad_instructores.criterio_pago_id as cp',
                    'tipo_identificacion',
                    'folio_ine'
                )
                    ->WHERE('estado', true)
                    ->WHERE('instructores.status', '=', 'Validado')->where('instructores.nombre', '!=', '')->where('instructores.id', $request->instructor)
                    //->whereJsonContains('unidades_disponible', [$grupo->unidad])
                    ->WHERE('especialidad_instructores.especialidad_id', $grupo->id_especialidad)
                    ->WHERE('especialidad_instructores.activo', 'true')
                    ->LEFTJOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
                    ->LEFTJOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
                    ->LEFTJOIN('criterio_pago', 'criterio_pago.id', '=', 'especialidad_instructores.criterio_pago_id')
                    ->first();
                // var_dump($instructor);exit;

                if ($instructor) {
                    //VALIDANDO INSTRUCTOR
                    $existe_instructor = DB::table('tbl_cursos')->where('folio_grupo', '<>', $_SESSION['folio'])->where('curp', $instructor->curp)
                        ->where('inicio', $request->inicio)->where('termino', $request->termino)->where('hini', $hini)->where('hfin', $hfin)
                        ->where('dia', trim($request->dia))->where('status_curso', '<>', 'CANCELADO')
                        ->exists();

                    if (!$existe_instructor) {
                        /** CRITERIO DE PAGO */
                        if ($instructor->cp > $grupo->cp) $cp = $grupo->cp;
                        else $cp = $instructor->cp;

                        /*CALCULANDO CICLO*/
                        $mes_dia1 = date("m-d", strtotime(date("Y-m-d")));
                        $mes_dia2 = date("m-d", strtotime(date("Y") . "-07-01"));

                        if ($mes_dia1 >= $mes_dia2)  $ciclo = date("Y") . "-" . date("Y", strtotime(date("Y") . "+ 1 year")); //sumas año
                        else $ciclo = date("Y", strtotime(date("Y") . "- 1 year")) . "-" . date("Y"); //restar año

                        /*REGISTRANDO COSTO Y TIPO DE INSCRIPCION*/

                        /*CALCULANDO EL TIPO DE PAGO*/
                        $total_pago = 0;
                        foreach ($alumnos as $key => $pago) {

                            $costo = $pago->costo;
                            $total_pago += $costo * 1;
                        }
                        $talumno = $grupo->hombre + $grupo->mujer;
                        $costo_total = $grupo->costo_individual * $talumno;
                        $ctotal = $costo_total - $total_pago;
                        if ($total_pago == 0) {
                            $tipo_pago = "EXO";
                            if ($cp > 7) $cp = 7; //EXONERACION Criterio de Pago Máximo 7
                        } elseif ($ctotal > 0) $tipo_pago = "EPAR";
                        else $tipo_pago = "PINS";

                        /*RECALCULANDO TOTAL HOMBRES Y MUJERES*/
                        $hombres = $mujeres = 0;
                        $alumnos = json_decode(json_encode($alumnos), true);
                        $total_sexo = array_count_values(array_column($alumnos, 'sexo'));
                        if (count($total_sexo) > 0) {
                            if (isset($total_sexo['H'])) $hombres = $total_sexo['H'];
                            if (isset($total_sexo['M'])) $mujeres = $total_sexo['M'];
                        }


                        /*ID DEL CURSO DE 10 DIGITOS*/
                        $PRE = date("y") . $unidad->plantel;
                        $ID = DB::table('tbl_cursos')->where('unidad', $grupo->unidad)->where('folio_grupo', $_SESSION['folio'])->value('id');
                        if (!$ID) $ID = DB::table('tbl_cursos')->where('unidad', $grupo->unidad)->where('id', 'like', $PRE . '%')->value(DB::raw('max(id)+1'));
                        if (!$ID) $ID = $PRE . '0001';
                        if ($request->cespecifico) $cespecifico = strtoupper($request->cespecifico);
                        else $cespecifico = 0;

                        if ($request->tcurso == "CERTIFICACION") {
                            $horas = $dura = 10;
                            $termino =  $request->inicio;
                        } else {
                            $dura = $grupo->horas;
                            $termino =  $request->termino;
                        }

                        if (isset($request->efisico_t) && ($request->efisico == 'OTRO')) {
                            $efisico = strtoupper($request->efisico_t);
                        } else {
                            $efisico = $request->efisico;
                        }

                        $created_at = DB::table('tbl_cursos')->where('unidad', $grupo->unidad)->where('folio_grupo', $_SESSION['folio'])->value('created_at');
                        if ($created_at) {
                            $updated_at = date('Y-m-d H:i:s');
                        } else {
                            $created_at = date('Y-m-d H:i:s');
                            $updated_at = date('Y-m-d H:i:s');
                        }

                        if (!$request->cespecifico) $request->cespecifico = 0;
                        if (!$request->mexoneracion) $request->mexoneracion = 0;
                        if (!$request->cgeneral) $request->cgeneral = 0;
                        //$result = tbl_curso::updateOrCreate(
                        if ($instructor->tipo_honorario == 'ASIMILADOS A SALARIOS') {
                            $tipo_honorario = 'ASIMILADOS A SALARIOS';
                        } else {
                            $tipo_honorario = 'HONORARIOS';
                        }
                        $exonerado = DB::table('exoneraciones')->where('folio_grupo', $grupo->folio_grupo)->where('status', '<>', null)->where('status', '<>', 'CANCELADO')->exists();
                        if ($request->hasFile('file_pago')) {
                            $file = $request->file_pago;
                            $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
                            $ext = $file->getClientOriginalExtension(); // extension de la imagen
                            if ($ext == "pdf") {
                                # nuevo nombre del archivo
                                $documentFile = trim("comprobante_pago" . "_" . $grupo->folio_grupo . "_" . date('YmdHis') . "." . $ext);
                                $path_pdf = "/UNIDAD/comprobantes_pagos/";
                                $path = $path_pdf . $documentFile;
                                Storage::disk('custom_folder_1')->put($path, file_get_contents($file)); // guardamos el archivo en la carpeta storage
                                //$documentUrl = storage::url($path); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
                                $documentUrl = $path;
                                $res = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio'])->update(['comprobante_pago' => $documentUrl]);
                            } else {
                                return redirect('solicitud/apertura')->with('message', "Formato de Archivo no válido, sólo PDF.");
                            }
                        } else {
                            $documentUrl = $grupo->comprobante_pago;
                        }
                        if ($exonerado) {
                            $result = DB::table('tbl_cursos')->where('clave', '0')->updateOrInsert(
                                ['folio_grupo' => $_SESSION['folio']],
                                [
                                    'nota' => $request->observaciones,
                                    'programa' => $request->programa,
                                    'cespecifico' => strtoupper($request->cespecifico),
                                    'fcespe' => $request->fcespe,
                                    'munidad' => $request->munidad,
                                    'plantel' => $request->plantel,
                                    'comprobante_pago' => $documentUrl,
                                    'folio_pago' => $request->folio_pago,
                                    'fecha_pago' => $request->fecha_pago
                                ]
                            );
                            $fpago = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio'])->update([
                                'folio_pago' => $request->folio_pago,
                                'fecha_pago' => $request->fecha_pago
                            ]);
                        } else {
                            $result =  DB::table('tbl_cursos')->where('clave', '0')->updateOrInsert(
                                ['folio_grupo' => $_SESSION['folio']],
                                [
                                    'id' => $ID, 'cct' => $unidad->cct,
                                    'unidad' => $grupo->unidad,
                                    'nombre' => $instructor->instructor,
                                    'curp' => $instructor->curp,
                                    'rfc' => $instructor->rfc,
                                    'clave' => '0',
                                    'mvalida' => '0',
                                    'mod' => $grupo->mod,
                                    'area' => $grupo->area,
                                    'espe' => $grupo->espe,
                                    'curso' => $grupo->curso,
                                    'inicio' => $request->inicio,
                                    'termino' => $termino,
                                    //'tdias' => $request->tdias,
                                    //'dia' => $grupo->dia,
                                    'dura' => $dura,
                                    'hini' => $hini,
                                    'hfin' => $hfin,
                                    'horas' => $horas,
                                    'ciclo' => $ciclo,
                                    'plantel' => $request->plantel,
                                    'depen' => $grupo->organismo_publico,
                                    'muni' => $municipio->muni,
                                    'sector' => $request->sector,
                                    'programa' => $request->programa,
                                    'nota' => $request->observaciones,
                                    'munidad' => $request->munidad,
                                    'efisico' => $efisico,
                                    'cespecifico' => strtoupper($request->cespecifico),
                                    'mpaqueteria' => $grupo->mpaqueteria,
                                    'mexoneracion' => $request->mexoneracion,
                                    'hombre' => $hombres,
                                    'mujer' => $mujeres,
                                    'tipo' => $tipo_pago,
                                    'fcespe' => $request->fcespe,
                                    'cgeneral' => $request->cgeneral,
                                    'fcgen' => $request->fcgen,
                                    'opcion' => 'NINGUNO',
                                    'motivo' => 'NINGUNO',
                                    'cp' => $cp,
                                    'ze' => $municipio->ze,
                                    'id_curso' => $grupo->id_curso,
                                    'id_instructor' => $instructor->id,
                                    'modinstructor' => $tipo_honorario,
                                    'nmunidad' => '0',
                                    'nmacademico' => '0',
                                    'observaciones' => 'NINGUNO',
                                    'status' => "NO REPORTADO",
                                    'realizo' => strtoupper($this->realizo),
                                    'valido' => 'SIN VALIDAR',
                                    'arc' => '01',
                                    'tcapacitacion' => $grupo->tcapacitacion,
                                    'status_curso' => null,
                                    'fecha_apertura' => null,
                                    'fecha_modificacion' => null,
                                    'costo' => $total_pago,
                                    'motivo_correccion' => null,
                                    'pdf_curso' => null,
                                    'turnado' => "UNIDAD",
                                    'fecha_turnado' => null,
                                    'tipo_curso' => $request->tcurso,
                                    'clave_especialidad' => $grupo->clave_especialidad,
                                    'id_especialidad' => $grupo->id_especialidad,
                                    'instructor_escolaridad' => $instructor->escolaridad,
                                    'instructor_titulo' => $instructor->titulo,
                                    'instructor_sexo' => $instructor->sexo,
                                    'instructor_mespecialidad' => $instructor->mespecialidad,
                                    'medio_virtual' => $request->medio_virtual,
                                    'link_virtual' => $request->link_virtual,
                                    'id_municipio' => $grupo->id_muni,
                                    'clave_localidad' => $grupo->clave_localidad,
                                    'id_gvulnerable' => $grupo->id_vulnerable,
                                    'id_cerss' => $grupo->id_cerss,
                                    'created_at' => $created_at,
                                    'updated_at' => $updated_at,
                                    'instructor_tipo_identificacion' => $instructor->tipo_identificacion,
                                    'instructor_folio_identificacion' => $instructor->folio_ine,
                                    'num_revision' => $request->munidad,
                                    'comprobante_pago' => $documentUrl,
                                    'folio_pago' => $request->folio_pago,
                                    'fecha_pago' => $request->fecha_pago
                                ]
                            );
                            $fpago = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio'])->update([
                                'folio_pago' => $request->folio_pago,
                                'fecha_pago' => $request->fecha_pago
                            ]);
                            $agenda = DB::table('agenda')->where('id_curso', $_SESSION['folio'])->update(['id_instructor' => $instructor->id]);
                        }
                        if ($result) $message = 'Operación Exitosa!!';
                    } else $message = "El instructor no se encuentra disponible en el horario y fecha requerido.";
                } else $message = 'Instructor no válido.';
            } else $message  = "Si es una CERTIFICACIÓN, corrobore que cubra 10 horas.";
        }
        return redirect('solicitud/apertura')->with('message', $message);
    }

   public function aperturar(Request $request){///PROCESO DE INSCRIPCION
        $result =  NULL;
        $message = "No hay datos para Aperturar.";
        if($_SESSION['alumnos'] AND $_SESSION['folio']){
            $grupo = DB::table('tbl_cursos as c')->where('status_curso','AUTORIZADO')->where('status','NO REPORTADO')->where('c.folio_grupo',$_SESSION['folio'])->first();
            if($grupo){
                $abrinscri = $this->abrinscri();
                $result = false;
                $bandera=0;
                //var_dump($_SESSION['alumnos']);exit;
                $alumnos = $_SESSION['alumnos'];
                foreach($alumnos as $a){
                    $tinscripcion = $a->tinscripcion;
                    $abrinscriTMP = $a->abrinscri;
                    $matricula = $a->matricula;
                    if(!$matricula AND $a->curp AND $grupo->cct){
                        $matricula = $this->genera_matricula($a->curp, $grupo->cct);
                    }

                    if($matricula){
                            DB::table('alumnos_pre')->where('id', $a->id_pre)->where('matricula',null)->update(['matricula'=>$matricula]);
                            DB::table('alumnos_registro')->where('id_pre', $a->id_pre)->where('no_control',null)->where('folio_grupo',$_SESSION['folio'])->update(['no_control'=>$matricula]);

                        $result = Inscripcion::updateOrCreate(
                        ['matricula' =>  $matricula, 'id_curso' =>  $grupo->id, 'folio_grupo' =>  $grupo->folio_grupo],
                        ['unidad' => $grupo->unidad,
                        'alumno' =>  $a->alumno,
                        'curso' =>  $grupo->curso,
                        'instructor' =>  $grupo->nombre,
                        'inicio' =>  $grupo->inicio,
                        'termino' =>  $grupo->termino,
                        'hinicio' =>  $grupo->hini,
                        'hfin' =>  $grupo->hfin,
                        'tinscripcion' =>  $tinscripcion,
                        'abrinscri' =>  $abrinscriTMP,
                        'munidad' =>  $grupo->munidad,
                        'costo' =>  $a->costo,
                        'motivo' =>  null,
                        'status' =>  'INSCRITO',
                        'realizo' =>  $this->realizo,
                        'id_pre' =>  $a->id_pre,
                        'id_cerss' =>  $a->id_cerss,
                        'fecha_nacimiento' =>  $a->fecha_nacimiento,
                        'estado_civil' =>  $a->estado_civil,
                        'discapacidad' =>  $a->discapacidad,
                        'escolaridad' =>  $a->escolaridad,
                        'nacionalidad' =>  $a->nacionalidad,
                        'etnia' =>  $a->etnia,
                        'indigena' =>  $a->indigena,
                        'inmigrante' =>  $a->inmigrante,
                        'madre_soltera' =>  $a->madre_soltera,
                        'familia_migrante' =>  $a->familia_migrante,
                        'calificacion' =>  $a->calificacion,
                        'iduser_created' =>  $this->id_user,
                        'iduser_updated' =>  null,
                        'activo' =>  true,
                        'id_folio' =>  null,
                        'reexpedicion' =>  false,
                        'sexo'=> $a->sexo,
                        'lgbt' => $a->lgbt,
                        'curp'=> $a->curp,
                        'empleado'=>$a->empleado,
                        'id_gvulnerable'=>$a->id_gvulnerable
                        ]);
                    }
                }
            }
        }

        if($result) $message = "Operación Exitosa.";
        return redirect('solicitud/apertura')->with('message',$message);
   }

   public function genera_matricula($curp, $cct){
        $matricula_sice = DB::table('registro_alumnos_sice')->where('eliminado',false)->where('curp',$curp)->value('no_control');
        $matricula = NULL;
        if(!$matricula_sice){
            $matricula_pre = DB::table('alumnos_pre')->where('curp',$curp)->value('matricula');
            if(!$matricula_pre){
                $anio = date('y');
                $clave = $anio.substr($cct,0,2).substr($cct,5,9);
                $max_sice = DB::table('registro_alumnos_sice')->where('eliminado',false)->where('no_control','like',$clave.'%')->max(DB::raw('no_control'));
                $max_pre = DB::table('alumnos_pre')->where('matricula','like',$clave.'%')->max('matricula');

                if($max_sice > $max_pre) $maX = $max_sice;
                elseif($max_sice < $max_pre) $max = $max_pre;
                else $max = '0';

                $max =  str_pad(intval(substr($max,9,13))+1, 4, "0", STR_PAD_LEFT);
                $matricula = $clave.$max;
            }else $matricula = $matricula_pre;
        }else{
            $matricula = $matricula_sice;
            DB::table('registro_alumnos_sice')->where('curp',$curp)->update(['eliminado'=>true]);
        }
        return $matricula;
    }

    public function showCalendar($id){
        $folio = $_SESSION['folio'];
        $data['agenda'] =  Agenda::where('id_instructor', '=', $id)->where('id_curso','=',$folio)->get();
        return response()->json($data['agenda']);
    }
    public function destroy($id){
        // $agenda = Agenda::findOrfail($id);
        $id_curso = DB::table('agenda')->where('id',$id)->value('id_curso');
        Agenda::destroy($id);
        $dias_agenda = DB::table('agenda')
            ->select(db::raw("extract(dow from (generate_series(agenda.start, agenda.end, '1 day'::interval))) as dia"),
                db::raw("generate_series(agenda.start, agenda.end, '1 day'::interval)::date as fecha"))
            ->where('id_curso',$id_curso)
            ->orderBy('fecha')
            ->get();
            if (count($dias_agenda) > 0) {
                $dias = []; $temp = $dias_agenda[0]->dia; $temp2 = null; $save = false; $conteo = count($dias_agenda); $dias_a = [];
                foreach ($dias_agenda as $key => $value) {
                    if ($key > 0) {
                        if ((($temp+1)==$value->dia) && !$temp2) {
                            $temp2 = $value->dia;
                            $save = false;
                        }elseif ($temp2 && (($temp2+1)==$value->dia)) {
                            $temp2 = $value->dia;
                            $save = false;
                        }elseif ( (($temp == '6')||($temp2 == '6')) &&($value->dia=='0')) {
                            $temp2 = $value->dia;
                            $save = false;
                        }elseif ((($temp == $value->dia)||($temp2 == $value->dia))&&($value->fecha == $dias_agenda[$key-1]->fecha)) {
                            $save = false;
                        }else {
                            $save = true;
                        }
                        if ($save == true) {
                            $dias[] = [$temp,$temp2];
                            $temp = $value->dia;
                            $temp2 = null;
                            $save = false;
                        }
                    };
                    if ($key == ($conteo-1)) {
                        $dias[] = [$temp,$temp2];
                    }
                }
                foreach ($dias as $item) {
                    if (($item[0]+1) < ($item[1])) {
                        $dias_a[] = $this->dia($item[0]).' A '.$this->dia($item[1]);
                    }elseif (($item[0]+1)==($item[1])) {
                        $dias_a[] = $this->dia($item[0]).' Y '.$this->dia($item[1]);
                    }elseif (($item[0]=='6')&&($item[1]=='0')) {
                        $dias_a[] = $this->dia($item[0]).' Y '.$this->dia($item[1]);
                    }elseif((($item[0]) > ($item[1])) && isset($item[1])){
                        $dias_a[] = $this->dia($item[0]).' A '.$this->dia($item[1]);
                    }else {
                        $dias_a[] = $this->dia($item[0]);
                    }
                }
                if ( count(array_unique(array_count_values($dias_a))) == 1 ) {
                    $dias_a = array_unique($dias_a);
                }
                $dias_a = implode(", ", $dias_a);
            }else {
                $dias_a = 0;
            }
            $total_dias = DB::table('agenda')
            ->select(DB::raw("(generate_series(agenda.start, agenda.end, '1 day'::interval))::date as dias"))
            ->where('id_curso',$id_curso)
            ->orderBy('dias')
            ->pluck('dias');//dd($total_dias);
            $tdias = 0;

            foreach ($total_dias as $key => $value) {
                if ($key > 0) {
                    if ($value != $total_dias[$key-1]) {
                        $tdias += 1;
                    }
                }else{
                    $tdias = 1;
                }
            }
        $result = DB::table('tbl_cursos')->where('folio_grupo',$id_curso)->update(['dia' => $dias_a, 'tdias' => $tdias]);
        return response()->json($id);
    }
    public function storeCalendar(Request $request) {
        set_time_limit(0);
        $isEquals = false;
        $isEquals2 = false;
        $isEquals3 = false;
        $isEquals4 = false;
        $grupo = $_SESSION['grupo'];
        $fechaInicio = Carbon::parse($request->start)->format('d-m-Y');
        $fechaTermino = Carbon::parse($request->end)->format('d-m-Y');
        $horaInicio = Carbon::parse($request->start)->format('H:i');
        $horaTermino = Carbon::parse($request->end)->format('H:i');
        $minutos_curso= Carbon::parse($horaTermino)->diffInMinutes($horaInicio);
        $period = CarbonPeriod::create($request->start,$request->end);
        $id_instructor = $request->id_instructor;
        $id_unidad = DB::table('tbl_unidades')->where('unidad','=',$grupo->unidad)->value('id');
        $id_curso = $grupo->folio_grupo;
        $id_municipio = $grupo->id_muni;
        $clave_localidad = $grupo->clave_localidad;
        $tipo_curso = $grupo->tcapacitacion;
        $es_lunes= Carbon::parse($fechaInicio)->is('monday');
        $sumaMesInicio = 0;
        $sumaMesFin = 0;
        //CRITERIO DISPONIBILIDAD FECHA Y HORA
        $fi = Carbon::parse($request->start)->format('Y-m-d');
        $ft = Carbon::parse($request->end)->format('Y-m-d');
        $hi = Carbon::parse($request->start)->format('H:i');
        $ht = Carbon::parse($request->end)->format('H:i');
        $evento = DB::table('agenda')
                    ->select('agenda.id_curso','agenda.start','agenda.end')
                    ->join('tbl_cursos','agenda.id_curso','=','tbl_cursos.folio_grupo')
                    ->where('agenda.id_instructor',$id_instructor)
                    ->where('tbl_cursos.status','!=','CANCELADO')
                    ->whereRaw("((date(agenda.start) >= '$fi' and date(agenda.start) <= '$ft' and cast(agenda.start as time) >= '$hi' and cast(agenda.start as time) < '$ht') OR
                                (date(agenda.end) >= '$fi' and date(agenda.end) <= '$ft' and cast(agenda.end as time) > '$hi' and cast(agenda.end as time) <= '$ht'))")
                    ->get();
        if (count($evento) > 0) {
            $isEquals = true;
        }
        //VALIDACION DISPONIBILIDAD FECHA Y HORA X ALUMNO
        $alumnos = DB::table('alumnos_registro as ar')->select('ar.id_pre','ap.curp')
            ->leftJoin('alumnos_pre as ap','ar.id_pre','=','ap.id')
            ->where('ar.folio_grupo',$id_curso)->where('ar.eliminado',false)->get();
        if (count($alumnos)>0) {
            foreach ($alumnos as $key => $value) {
                $existe_dupli = DB::table('agenda as a')
                    ->select('a.id_curso')
                    ->leftJoin('alumnos_registro as ar','a.id_curso','=','ar.folio_grupo')
                    ->whereRaw("((date(a.start) >= '$fi' and date(a.start) <= '$ft' and cast(a.start as time) >= '$hi' and cast(a.start as time) < '$ht') OR
                    (date(a.end) >= '$fi' and date(a.end) <= '$ft' and cast(a.end as time) > '$hi' and cast(a.end as time) <= '$ht'))")
                    ->where('ar.eliminado',false)
                    ->where('ar.id_pre',$value->id_pre)
                    ->get();
                if (count($existe_dupli)>0) {
                    return "iguales8";
                }
            }
        }
        //CRITERIO 8hrs
        foreach ($period as $value) {
            $total = 0;
            $a= Carbon::parse($value)->format('d-m-Y 22:00');    //print_r($a.'||');
            $b= Carbon::parse($value)->format('d-m-Y 00:00');
            $consulta_fechas8= DB::table('agenda')->select('agenda.start','agenda.end')
                                                 ->join('tbl_cursos','agenda.id_curso','=','tbl_cursos.folio_grupo')
                                                 ->where('tbl_cursos.status','!=','CANCELADO')
                                                 ->where('agenda.id_instructor','=',$id_instructor)
                                                 ->where('agenda.start','<=',$a)
                                                 ->where('agenda.end','>=',$b)
                                                 ->orderByRaw("extract(hour from agenda.start) asc")
                                                 ->get();   //dd($consulta_fechas8);
            $suma= 0;
            foreach($consulta_fechas8 as $key=>$fechas){
                $y= Carbon::parse($fechas->end)->format('H:i'); //dd($fechas);
                $x= Carbon::parse($fechas->start)->format('H:i');   //dd($x.'||'.$y);
                $minutos= Carbon::parse($y)->diffInMinutes($x);
                $suma += $minutos;
                if (($suma + $minutos_curso) > 480) {
                    $isEquals3 = true;
                }
            }
        }
        //CRITERIO 40hrs
        if ($es_lunes) {
            $dateini = Carbon::parse($fechaInicio);
            $datefin= Carbon::parse($fechaInicio)->addDay(6);
            $total=0;
            $array1=[];
            foreach($period as $pan){
                $al = Carbon::parse($pan->format('d-m-Y'));
                $fal = Carbon::parse($datefin->format('d-m-Y'));
                if($al <= $fal){
                    $total += $minutos_curso;
                }else{
                    $array1[]=$al;
                }
            }
            $min_reg = DB::table(DB::raw("
                    (select (generate_series(agenda.start, agenda.end, '1 day'::interval))::date as dias, agenda.id_curso,
                    (cast(agenda.end as time)-cast(agenda.start as time))::time as dif
                    from agenda
                    left join tbl_cursos on agenda.id_curso = tbl_cursos.folio_grupo
                    where agenda.id_instructor = '$id_instructor'
                    and tbl_cursos.status != 'CANCELADO'
                    order by agenda.id_curso) as t
                "))
                ->where('dias','>=',$dateini->format('Y-m-d'))
                ->where('dias','<=',$datefin->format('Y-m-d'))
                ->value(DB::raw('sum((extract(hour from dif)*60)+ extract(minute from dif))'));
            if (($min_reg + $total) > 2400) {
                $isEquals4 = true;
            }
            if(!empty($array1)){
                $dateini = $array1[0];
                $es_lunes= Carbon::parse($dateini)->is('monday');
                if ($es_lunes) {
                    $datefin = Carbon::parse($dateini)->addDay(6);
                    $array2 = [];
                    $total2 = 0;

                    foreach ($array1 as $item) {
                        $al = Carbon::parse($item->format('d-m-Y'));
                        $fal = Carbon::parse($datefin->format('d-m-Y'));
                        if ($al <= $fal) {
                            $total2 += $minutos_curso;
                        } else {
                            $array2[] = $item;
                        }
                    }
                    $min_reg = DB::table(
                        DB::raw("(select (generate_series(agenda.start, agenda.end, '1 day'::interval))::date as dias, agenda.id_curso,
                        (cast(agenda.end as time)-cast(agenda.start as time))::time as dif
                        from agenda
                        left join tbl_cursos on agenda.id_curso = tbl_cursos.folio_grupo
                        where agenda.id_instructor = '$id_instructor'
                        and tbl_cursos.status != 'CANCELADO'
                        order by agenda.id_curso) as t")
                        )
                        ->where('dias', '>=', $dateini->format('Y-m-d'))
                        ->where('dias', '<=', $datefin->format('Y-m-d'))
                        ->value(DB::raw('sum((extract(hour from dif)*60)+ extract(minute from dif))'));
                    if (($min_reg + $total2) > 2400) {
                        $isEquals4 = true;
                    }
                    if (!empty($array2)) {
                        $isEquals4 = true;        //ERROR!!!!!
                    }
                } else {
                    $isEquals4 = true;       //ERROR!!!!!
                }
            }
        } else {
            $date= Carbon::parse($fechaInicio)->startOfWeek();   //dd(gettype($date));   //obtener el primer dia de la semana
            $datefin= Carbon::parse($date)->addDay(6);
            $total=0;   //vamos a contar los minutos que dura el curso a la semana y crear array´s para comprobar si el curso comparte días con otra semana
            $array1=[];
            foreach($period as $pan){
                $al = Carbon::parse($pan->format('d-m-Y'));
                $fal = Carbon::parse($datefin->format('d-m-Y'));
                if($al <= $fal){
                    $total += $minutos_curso;
                }else{
                    $array1[]=$pan;
                }
            }
            $min_reg = DB::table(DB::raw("
                    (select (generate_series(agenda.start, agenda.end, '1 day'::interval))::date as dias, agenda.id_curso,
                    (cast(agenda.end as time)-cast(agenda.start as time))::time as dif
                    from agenda
                    left join tbl_cursos on agenda.id_curso = tbl_cursos.folio_grupo
                    where agenda.id_instructor = '$id_instructor'
                    and tbl_cursos.status != 'CANCELADO'
                    order by agenda.id_curso) as t
                "))
                ->where('dias','>=',$date->format('Y-m-d'))
                ->where('dias','<=',$datefin->format('Y-m-d'))
                ->value(DB::raw('sum((extract(hour from dif)*60)+ extract(minute from dif))'));
            if (($min_reg + $total) > 2400) {
                $isEquals4 = true;
            }
            if(!empty($array1)){
                $date= $array1[0];
                $es_lunes= Carbon::parse($date)->is('monday');
                if($es_lunes){
                    $datefin= Carbon::parse($date)->addDay(6);
                    $array2=[];
                    $total2=0;
                    foreach($array1 as $item){
                        $al = Carbon::parse($item->format('d-m-Y'));
                        $fal = Carbon::parse($datefin->format('d-m-Y'));
                        if($al <= $fal){
                            $total2 += $minutos_curso;
                        }else{
                            $array2= $item;
                        }
                    }
                    $min_reg = DB::table(DB::raw("
                            (select (generate_series(agenda.start, agenda.end, '1 day'::interval))::date as dias, agenda.id_curso,
                            (cast(agenda.end as time)-cast(agenda.start as time))::time as dif
                            from agenda
                            left join tbl_cursos on agenda.id_curso = tbl_cursos.folio_grupo
                            where agenda.id_instructor = '$id_instructor'
                            and tbl_cursos.status != 'CANCELADO'
                            order by agenda.id_curso) as t
                        "))
                        ->where('dias','>=',$date->format('Y-m-d'))
                        ->where('dias','<=',$datefin->format('Y-m-d'))
                        ->value(DB::raw('sum((extract(hour from dif)*60)+ extract(minute from dif))'));
                    if (($min_reg + $total2) > 2400) {
                        $isEquals4 = true;
                    }
                    if(!empty($array2)){
                        $isEquals3=true;
                    }

                }else{
                    $isEquals4=true;
                }
            }
        }
        //CRITERIO 5 MESES
        for ($i=1; $i < 6; $i++) {
            $f = DB::table('tbl_cursos')->where('folio_grupo',$id_curso)->value('inicio');
            $finicio = Carbon::parse($f)->firstOfMonth();
            $mesActivo= Carbon::parse($finicio)->addMonth($i);
            $mes = Carbon::parse($mesActivo)->format('d-m-Y');
            $mesInicio = Carbon::parse($mes)->firstOfMonth();
            $mesFin = Carbon::parse($mes)->endOfMonth();
            $consulta = DB::table('tbl_cursos')->select('id')
                                           ->where('status','!=','CANCELADO')
                                           ->where('id_instructor','=', $id_instructor)
                                           ->where('folio_grupo','!=',$id_curso)
                                           ->where('inicio','>=', $mesInicio)
                                           ->where('inicio','<=', $mesFin)
                                           ->get();
            $conteo = $consulta->count();
            if ($conteo >= 1) {
                $sumaMesInicio += 1;
            } else {
                $sumaMesInicio = 0;
                break;
            }
        }
        for ($i=1; $i < 6; $i++) {
            $f = DB::table('tbl_cursos')->where('folio_grupo',$id_curso)->value('inicio');
            $finicio = Carbon::parse($f)->firstOfMonth();
            $mesActivoSub= Carbon::parse($finicio)->subMonth($i);
            $mes = Carbon::parse($mesActivoSub)->format('d-m-Y');
            $mesInicio = Carbon::parse($mes)->firstOfMonth();
            $mesFin = Carbon::parse($mes)->endOfMonth();
            $consulta = DB::table('tbl_cursos')->select('id')
                                           ->where('status','!=','CANCELADO')
                                           ->where('id_instructor','=', $id_instructor)
                                           ->where('folio_grupo','!=',$id_curso)
                                           ->where('inicio','>=', $mesInicio)
                                           ->where('inicio','<=', $mesFin)
                                           ->get();
            $conteo = $consulta->count();
            if ($conteo >= 1) {
                $sumaMesFin += 1;
            } else {
                $sumaMesFin = 0;
                break;
            }
        }
        if ($sumaMesInicio==5||$sumaMesFin==5) {
            return 'iguales5';
        } else {
            //if ( Carbon::parse($request->stat)->format('m-Y') == Carbon::parse($request->end)->format('m-Y')) {
                $total = ($sumaMesInicio + $sumaMesFin) + 1;
                $total1 = $sumaMesInicio + 1;
                $total2 = $sumaMesFin + 1;
                if ($total > 5||$total1 > 5||$total2 > 5) {
                    return 'iguales5';
                }
            // } else {
            //     $total = ($sumaMesInicio + $sumaMesFin) + 2;
            //     $total1 = $sumaMesInicio + 2;
            //     $total2 = $sumaMesFin +2;
            //     if ($total > 5||$total1 > 5||$total2 > 5) {
            //         return 'iguales5';
            //     }
            // }
        }
        //CRITERIO NO MÁS DE 4 CURSOS EN UN MES
        // $hinimes = Carbon::parse($fechaInicio)->firstOfMonth();
        // $finmes = Carbon::parse($fechaInicio)->endOfMonth();
        // $total_grupos = 0;
        // $consulta_grupos = DB::table('tbl_cursos')->select('id_instructor','folio_grupo')
        //                                    ->where('status','!=','CANCELADO')
        //                                    ->where('id_instructor','=', $id_instructor)
        //                                    ->where('inicio','>=', $hinimes)
        //                                    ->where('inicio','<=', $finmes)
        //                                    ->groupBy('id_instructor','folio_grupo')
        //                                    ->get();
        // foreach ($consulta_grupos as $valuel) {
        //     if ($valuel->folio_grupo != $id_curso) {
        //         $total_grupos += 1;
        //         if ($total_grupos > 3) {
        //             return 'iguales6';
        //         }
        //     }
        // }
        //CRITERIO UNIDADES
        /*if ($tipo_curso != 'A DISTANCIA') {
            foreach ($period as $value) {
                $a= Carbon::parse($value)->format('d-m-Y 22:00');
                $b= Carbon::parse($value)->format('d-m-Y 00:00');
                $consulta_unidad= DB::table('agenda')->select('start','end','id_unidad','id_municipio')
                                                     ->where('id_instructor','=',$id_instructor)
                                                     ->where('start','<=',$a)
                                                     ->where('end','>=',$b)
                                                     ->orderByRaw("extract(hour from start) asc")
                                                     ->get();    //dd($consulta_unidad);
                foreach ($consulta_unidad as $fecha) {
                    if ($fecha->id_municipio != $id_municipio) {
                        $tiempo_distance = 20;  //consulta tabla de tiempos
                        $hini= Carbon::parse($fecha->start)->format('H:i');
                        $hfin= Carbon::parse($fecha->end)->format('H:i');
                        if ($hfin == $horaInicio||$hini == $horaTermino) {
                            return 'iguales7';
                        }
                        if ( carbon::parse($hini)->greaterThan(carbon::parse($horaTermino)) ) {
                            $diferiencia= Carbon::parse($horaTermino)->diffInMinutes($hini);
                            if ($diferiencia < $tiempo_distance) {
                                return 'iguales7';
                            }
                        }
                        if( carbon::parse($hfin)->lessThan(carbon::parse($horaInicio)) ){
                            $diferiencia= Carbon::parse($horaInicio)->diffInMinutes($hfin);
                            if ($diferiencia < $tiempo_distance) {
                                return 'iguales7';
                            }
                        }
                    }else {
                        $tiempo_distance = 30;
                        $hini= Carbon::parse($fecha->start)->format('H:i');
                        $hfin= Carbon::parse($fecha->end)->format('H:i');
                        if ($hfin == $horaInicio||$hini == $horaTermino) {
                            return 'iguales7';
                        }
                        if (carbon::parse($hini)->greaterThan(carbon::parse($horaTermino))) {
                            $diferiencia= Carbon::parse($horaTermino)->diffInMinutes($hini);
                            if ($diferiencia < $tiempo_distance) {
                                return 'iguales7';
                            }
                        }
                        if( carbon::parse($hfin)->lessThan(carbon::parse($horaInicio)) ){
                            $diferiencia= Carbon::parse($horaInicio)->diffInMinutes($hfin);
                            if ($diferiencia < $tiempo_distance) {
                                return 'iguales7';
                            }
                        }
                    }
                }
            }
        }*/
           // dd($isEquals);
        if ($isEquals) {
            return 'iguales';
        } else if ($isEquals2) {
            return 'iguales2';
        } else if ($isEquals3) {
            return 'iguales3';
        } else if ($isEquals4) {
            return 'iguales4';
        }else {
            try {
                //dd($id_curso);
                $titulo = $request->title;

                $agenda = new Agenda();

                $agenda->title = $titulo;
                $agenda->start = $request->start;
                $agenda->end = $request->end;
                $agenda->textColor = $request->textColor;
                $agenda->observaciones = $request->observaciones;
                $agenda->id_curso = $id_curso;
                $agenda->id_instructor = $id_instructor;
                $agenda->id_unidad = $id_unidad;
                $agenda->id_municipio = $id_municipio;
                $agenda->clave_localidad = $clave_localidad;
                $agenda->iduser_created = Auth::user()->id; //dd($agenda);
                $agenda->save();
            } catch(QueryException $ex) {
                //dd($ex);
                return 'duplicado';
            }
        }
        $dias_agenda = DB::table('agenda')
            ->select(db::raw("extract(dow from (generate_series(agenda.start, agenda.end, '1 day'::interval))) as dia"),
                db::raw("generate_series(agenda.start, agenda.end, '1 day'::interval)::date as fecha"))
            ->where('id_curso',$id_curso)
            ->orderBy('fecha')
            ->get();
            if (count($dias_agenda) > 0) {
                $dias = []; $temp = $dias_agenda[0]->dia; $temp2 = null; $save = false; $conteo = count($dias_agenda); $dias_a = [];
                foreach ($dias_agenda as $key => $value) {
                    if ($key > 0) {
                        if ((($temp+1)==$value->dia) && !$temp2) {
                            $temp2 = $value->dia;
                            $save = false;
                        }elseif ($temp2 && (($temp2+1)==$value->dia)) {
                            $temp2 = $value->dia;
                            $save = false;
                        }elseif ( (($temp == '6')||($temp2 == '6')) &&($value->dia=='0')) {
                            $temp2 = $value->dia;
                            $save = false;
                        }elseif ((($temp == $value->dia)||($temp2 == $value->dia))&&($value->fecha == $dias_agenda[$key-1]->fecha)) {
                            $save = false;
                        }else {
                            $save = true;
                        }
                        if ($save == true) {
                            $dias[] = [$temp,$temp2];
                            $temp = $value->dia;
                            $temp2 = null;
                            $save = false;
                        }
                    };
                    if ($key == ($conteo-1)) {
                        $dias[] = [$temp,$temp2];
                    }
                }
                foreach ($dias as $item) {
                    if (($item[0]+1) < ($item[1])) {
                        $dias_a[] = $this->dia($item[0]).' A '.$this->dia($item[1]);
                    }elseif (($item[0]+1)==($item[1])) {
                        $dias_a[] = $this->dia($item[0]).' Y '.$this->dia($item[1]);
                    }elseif (($item[0]=='6')&&($item[1]=='0')) {
                        $dias_a[] = $this->dia($item[0]).' Y '.$this->dia($item[1]);
                    }elseif((($item[0]) > ($item[1])) && isset($item[1])){
                        $dias_a[] = $this->dia($item[0]).' A '.$this->dia($item[1]);
                    }else {
                        $dias_a[] = $this->dia($item[0]);
                    }
                }
                if ( count(array_unique(array_count_values($dias_a))) == 1 ) {
                    $dias_a = array_unique($dias_a);
                }
                $dias_a = implode(", ", $dias_a);
            }else {
                $dias_a = 0;
            }
        $total_dias = DB::table('agenda')
            ->select(DB::raw("(generate_series(agenda.start, agenda.end, '1 day'::interval))::date as dias"))
            ->where('id_curso',$id_curso)
            ->orderBy('dias')
            ->pluck('dias');//dd($total_dias);
            $tdias = 0;

            foreach ($total_dias as $key => $value) {
                if ($key > 0) {
                    if ($value != $total_dias[$key-1]) {
                        $tdias += 1;
                    }
                }else{
                    $tdias = 1;
                }
            }
        $result = DB::table('tbl_cursos')->where('folio_grupo',$id_curso)->update(['dia' => $dias_a, 'tdias' => $tdias]);
    }
}
