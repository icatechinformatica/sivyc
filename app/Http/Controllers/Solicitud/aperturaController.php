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
use PDF;

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
        $muni = $instructores = $convenio = $localidad = $comprobante = $exonerado = $num_oficio_sop = $titular_sop = NULL;
        if($request->valor)  $valor = $request->valor;
        elseif(isset($_SESSION['folio'])) $valor = $_SESSION['folio'];
        $_SESSION['alumnos'] = NULL;
        if($valor){
            #consultamos registros para generar pdf soporte de constancias
            $sop_expediente = DB::table('tbl_cursos_expedientes')->select('sop_constancias')->where('folio_grupo', '=', $valor)->first();
            if($sop_expediente){
                $sop_constancias = json_decode($sop_expediente->sop_constancias);
                $num_oficio_sop = $sop_constancias->num_oficio;
                $titular_sop = ($sop_constancias->titular_depen != "" && $sop_constancias->cargo_titular != "") ? $sop_constancias->titular_depen.', '.$sop_constancias->cargo_titular : '';
            }

            $grupo =  DB::table('alumnos_registro as ar')->select('ar.id_curso','ar.unidad','ar.horario','ar.inicio','ar.termino','e.nombre as espe','a.formacion_profesional as area',
                'ar.folio_grupo','ar.tipo_curso as tcapacitacion','c.nombre_curso as curso','ar.mod','ar.horario','c.horas','c.costo as costo_individual','c.id_especialidad','ar.comprobante_pago',
                DB::raw("SUM(CASE WHEN substring(ar.curp,11,1) ='H' THEN 1 ELSE 0 END) as hombre"),DB::raw("SUM(CASE WHEN substring(ar.curp,11,1)='M' THEN 1 ELSE 0 END) as mujer"),'c.memo_validacion as mpaqueteria',
                'tc.nota',DB::raw(" COALESCE(tc.clave, '0') as clave"),'ar.id_muni','ar.clave_localidad','ar.organismo_publico','ar.id_organismo','tc.status_solicitud',
                'tc.id_municipio','tc.status_curso','tc.plantel', 'tc.dia', 'tc.tdias', 'id_vulnerable', 'ar.turnado','tc.instructor_mespecialidad','tc.dura',
                DB::raw("cast(replace(replace(hini,'a.m.','am'),'p.m.','pm') as time) as hini"),
                DB::raw("cast(replace(replace(hfin,'a.m.','am'),'p.m.','pm') as time) as hfin"),
                'tc.sector','tc.programa','tc.efisico','tc.depen','tc.cgeneral','tc.fcgen','tc.cespecifico','tc.fcespe','tc.mexoneracion','tc.medio_virtual',
                'tc.id_instructor','tc.tipo','tc.link_virtual','tc.munidad','tc.costo','tc.tipo','tc.status','tc.id','e.clave as clave_especialidad','tc.arc','tc.tipo_curso','ar.id_cerss','c.rango_criterio_pago_maximo as cp',
                'ar.folio_pago','ar.fecha_pago','ar.observaciones as nota_vincu','tc.mexoneracion')
                ->join('alumnos_pre as ap','ap.id','ar.id_pre')
                ->join('cursos as c','ar.id_curso','c.id')
                ->join('especialidades as e','e.id','c.id_especialidad') ->join('area as a','a.id','c.area')
                ->leftjoin('tbl_cursos as tc','tc.folio_grupo','ar.folio_grupo')
                ->where('ar.turnado','<>','VINCULACION')
                ->where('ar.folio_grupo',$valor);
            if($_SESSION['unidades']) $grupo = $grupo->whereIn('ar.unidad',$_SESSION['unidades']);
            $grupo = $grupo->groupby('ar.mod','ar.id_curso','ar.unidad','ar.horario', 'ar.folio_grupo','ar.tipo_curso','ar.horario','tc.arc','ar.id_cerss','ar.clave_localidad','ar.organismo_publico','ar.id_organismo',
                'e.id','a.formacion_profesional','tc.id','c.id','ar.inicio','ar.termino','ar.comprobante_pago','ar.id_muni','ar.id_vulnerable','ar.turnado',
                'ar.folio_pago','ar.fecha_pago','ar.observaciones')->first(); //dd($grupo);

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
                    $alumnos = DB::table('alumnos_registro as ar')->select('ar.id as id_reg','ar.curp','ar.nombre','ar.apellido_paterno','ar.apellido_materno',
                    'ap.fecha_nacimiento AS FN','ap.sexo AS SEX','ar.id_cerss', 'ap.lgbt',DB::raw("CONCAT(ar.apellido_paterno,' ', ar.apellido_materno,' ',ar.nombre) as alumno"),
                    'ap.estado_civil','ap.discapacidad','ap.nacionalidad','ap.etnia','ap.indigena','ap.inmigrante','ap.madre_soltera','ap.familia_migrante',
                    'ar.costo','ar.tinscripcion',DB::raw("'0' as calificacion"),'ar.escolaridad','ap.empleado','ar.abrinscri',
                    'ap.matricula', 'ar.id_pre','ar.id', DB::raw("substring(ar.curp,11,1) as sexo"),'ap.id_gvulnerable',
                    DB::raw("substring(ar.curp,5,2) as anio_nac"),
                    DB::raw("CASE WHEN substring(ar.curp,5,2) <='".$anio_hoy."' THEN CONCAT('20',substring(ar.curp,5,2),'-',substring(ar.curp,7,2),'-',substring(ar.curp,9,2))
                        ELSE CONCAT('19',substring(ar.curp,5,2),'-',substring(ar.curp,7,2),'-',substring(ar.curp,9,2)) END AS fecha_nacimiento
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
                $exonerado = DB::table('exoneraciones')->where('folio_grupo',$grupo->folio_grupo)->where('status','<>','CAPTURA')->where('status','<>','CANCELADO')->exists();

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
            'instructor','exoneracion','medio_virtual','tcurso','tinscripcion','tcuota','muni','instructores','convenio','localidad','exonerado', 'num_oficio_sop', 'titular_sop'));
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
        if($_SESSION['folio'] == $request->valor){
            $result = DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio'])->update(['turnado' => "VINCULACION",'fecha_turnado' => null,'fmpreapertura'=>null]);
            DB::table('tbl_cursos')->where('folio_grupo', $_SESSION['folio'])->update(['fecha_arc01'=>null]);         
            if($result){
                $message = "El grupo fué turnado correctamente a VINCULACIÓN";
                unset($_SESSION['folio']);
            }
        }
        return redirect('solicitud/apertura')->with('message',$message);
   }



    public function store(Request $request, \Illuminate\Validation\Factory $validate)
    {
        $message = 'Operación fallida, vuelva a intentar..';    
        if ($_SESSION['folio'] == $request->valor) {

            $result =  DB::table('tbl_cursos')->where('clave', '0')->updateOrInsert(
                ['folio_grupo' => $_SESSION['folio']],
                [           
                    'munidad' => $request->munidad,        
                    'plantel' => $request->plantel,
                    'programa' => $request->programa,
                    'nota' => $request->observaciones,                    
                    'realizo' => strtoupper($this->realizo),                    
                    'updated_at' => date('Y-m-d H:m:s'),
                    'num_revision' => $request->munidad              
                ]
            );
            if ($result) $message = 'Operación Exitosa!!';          
        }
        return redirect('solicitud/apertura')->with('message', $message);
    }

   public function aperturar(Request $request){///PROCESO DE INSCRIPCION
        $result =  NULL;
        $message = "No hay datos para Aperturar.";
        if($_SESSION['alumnos'] AND $_SESSION['folio'] == $request->valor){
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
                        'iduser_updated' =>  $this->id_user,
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
        /*
        // $agenda = Agenda::findOrfail($id);
        $id_curso = DB::table('agenda')->where('id',$id)->value('id_curso');
        Agenda::destroy($id);
        $dias = $this->dias($id_curso);
        $result = DB::table('tbl_cursos')->where('folio_grupo',$id_curso)->update(['dia' => $dias['nombre'], 'tdias' => $dias['total']]);
        return response()->json($id);
        */
    }
    public function storeCalendar(Request $request){
        set_time_limit(0);
        $fechaInicio = date("Y-m-d", strtotime($request->start));
        $fechaTermino = date("Y-m-d", strtotime($request->end));
        $horaInicio = date("H:i", strtotime($request->start));
        $horaTermino = date("H:i", strtotime($request->end));
        $id_instructor = $request->id_instructor;
        $id_curso = $_SESSION['folio'];
        $grupo = $_SESSION['grupo'];
        $period = CarbonPeriod::create($fechaInicio,$fechaTermino);
        $minutos_curso = Carbon::parse($horaTermino)->diffInMinutes($horaInicio);
        $es_lunes= Carbon::parse($fechaInicio)->is('monday');
        $sumaMesInicio = 0;
        $sumaMesFin = 0;
        $id_unidad = DB::table('tbl_unidades')->where('unidad','=',$grupo->unidad)->value('id');
        $id_municipio = $grupo->id_muni;
        $clave_localidad = $grupo->clave_localidad;
        if (DB::table('exoneraciones')->where('folio_grupo',$id_curso)->where('status','!=', 'CAPTURA')->where('status','!=','CANCELADO')->exists()) {
            return "Solicitud de Exoneración o Reducción de couta en Proceso..";
        }
        //VALIDACIÓN DEL HORARIO
        if (($horaInicio < date('H:i',strtotime($grupo->hini))) OR ($horaInicio > date('H:i',strtotime($grupo->hfin))) OR
        ($horaTermino < date('H:i',strtotime($grupo->hini))) OR ($horaTermino > date('H:i',strtotime($grupo->hfin)))) {
            return "El horario ingresado no corresponde al registro del curso.";
        }
         if (($minutos_curso > 480 AND $grupo->tipo_curso=='CURSO') OR ($minutos_curso > 600 AND $grupo->tipo_curso=='CERTIFICACION')) {
            return "El instructor no debe de exceder las 8 o 10 hrs impartidas, según sea el caso.";
        }
        // CRITERIO DISPONIBILIDAD FECHA Y HORA ALUMNOS
        $alumnos_ocupados = DB::table('alumnos_registro as ar')
            ->select('ap.curp')
            ->leftJoin('alumnos_pre as ap','ar.id_pre','ap.id')
            ->leftJoin('agenda as a', 'ar.folio_grupo','a.id_curso')
            ->leftJoin('tbl_cursos as tc','ar.folio_grupo','tc.folio_grupo')
            ->where('tc.status','<>','CANCELADO')
            ->where('ar.eliminado',false)
            ->where('ar.folio_grupo','<>',$id_curso)
            ->whereRaw("((date(a.start) <= '$fechaInicio' and date(a.end) >= '$fechaInicio') OR (date(a.start) <= '$fechaTermino' and date(a.end) >= '$fechaTermino'))")
            ->whereRaw("((cast(a.start as time) <= '$horaInicio' and cast(a.end as time) > '$horaInicio') OR (cast(a.start as time) < '$horaTermino' and cast(a.end as time) >= '$horaTermino'))")
            ->whereIn('ar.id_pre', [DB::raw("select id_pre from alumnos_registro where folio_grupo = '$id_curso' and eliminado = false")])
            ->get();
        if (count($alumnos_ocupados) > 0) {
            return "Alumno(s) no disponible en fecha y hora: ".json_encode($alumnos_ocupados);
        }
        //CRITERIOS INSTRUCTOR ::
        //DISPONIBILIDAD FECHA Y HORA
        $duplicado = DB::table('agenda as a')
            ->leftJoin('tbl_cursos as tc','a.id_curso','tc.folio_grupo')
            ->where('a.id_instructor',$id_instructor)
            ->where('tc.status','<>','CANCELADO')
            ->whereRaw("((date(a.start) <= '$fechaInicio' and date(a.end) >= '$fechaInicio') OR (date(a.start) <= '$fechaTermino' and date(a.end) >= '$fechaTermino'))")
            ->whereRaw("((cast(a.start as time) <= '$horaInicio' and cast(a.end as time) > '$horaInicio') OR (cast(a.start as time) < '$horaTermino' and cast(a.end as time) >= '$horaTermino'))")
            ->exists();
        if ($duplicado) {
            return "El instructor no se encuentra disponible en fecha y hora";
        }
        //8HRS DIARIAS
        foreach ($period as $fecha) {
            $f = date($fecha->format('Y-m-d'));
            $suma = 0;
            $horas_dia = DB::table('agenda')->select(DB::raw('cast(agenda.start as time) as hini'),DB::raw('cast(agenda.end as time) as hfin'))
                ->join('tbl_cursos','agenda.id_curso','=','tbl_cursos.folio_grupo')
                ->where('tbl_cursos.status','<>','CANCELADO')
                ->where('agenda.id_instructor','=',$id_instructor)
                ->whereRaw("(date(agenda.start)<='$f' AND date(agenda.end)>='$f')")
                ->get();
            foreach ($horas_dia as $value) {
                $minutos = Carbon::parse($value->hfin)->diffInMinutes($value->hini);
                $suma += $minutos;
                if (($suma + $minutos_curso) > 480) {
                    return "El instructor no debe de exceder las 8hrs impartidas.";
                }
            }
        }
        //40HRS SEMANA
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
                return "El instructor no debe impartir más de 40hrs semanales.";
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
                        return "El instructor no debe impartir más de 40hrs semanales.";
                    }
                    if (!empty($array2)) {
                        return "El instructor no debe impartir más de 40hrs semanales.";        //ERROR!!!!!
                    }
                } else {
                    return "El instructor no debe impartir más de 40hrs semanales.";       //ERROR!!!!!
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
                return "El instructor no debe impartir más de 40hrs semanales.";
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
                        return "El instructor no debe impartir más de 40hrs semanales.";
                    }
                    if(!empty($array2)){
                        return "El instructor no debe impartir más de 40hrs semanales.";
                    }

                }else{
                    return "El instructor no debe impartir más de 40hrs semanales.";
                }
            }
        }
        //5 MESES CONSECUTIVOS
        $inicio_curso = DB::table('tbl_cursos')->where('folio_grupo',$id_curso)->value('inicio');
        $termino_curso = DB::table('tbl_cursos')->where('folio_grupo',$id_curso)->value('termino');
        $imes = Carbon::parse($inicio_curso)->subMonth(4);
        $imes = Carbon::parse($imes)->firstOfMonth();
        $tmes = Carbon::parse($inicio_curso)->addMonth(4);
        $tmes = Carbon::parse($tmes)->firstOfMonth();
        $conteo1 = 1;
        $conteo2 = null;
        $temp =  $temp2 = $mesact = '';
        if (DB::table('tbl_cursos')->where('status','<>','CANCELADO')->where('id_instructor',$id_instructor)->where('folio_grupo','<>',$id_curso)->whereRaw("(((inicio >= cast((cast('$inicio_curso' as date) - cast('30 days' as interval)) as date)) AND (inicio <= '$termino_curso')) OR ((termino >= cast((cast('$inicio_curso' as date) - cast('30 days' as interval)) as date)) AND (termino <= '$termino_curso')))")->exists()) {
            $actinstru = DB::table('tbl_cursos')->select('inicio','termino','folio_grupo')
                ->where('status','<>','CANCELADO')
                ->where('id_instructor',$id_instructor)
                ->where('folio_grupo','<>',$id_curso)
                ->whereRaw("(((inicio >= '$imes') AND (inicio <= '$termino_curso')) OR ((termino >= '$imes') AND (termino <= '$termino_curso')))")
                ->orderBy('termino','desc')->get();//dd($actinstru);
            foreach ($actinstru as $key => $value) {
                if ($conteo1 > 5) {
                   return "La actividad del instructor por mes supera el límite permitido (5 meses) ";
                } elseif ($key == 0) {
                    $temp = $value->inicio;
                    $temp2 = $value->termino;
                    if (date('Y-m',strtotime($value->termino)) < date('Y-m',strtotime($inicio_curso))) {
                        $conteo1 += 1;
                    }
                    if ((date('m',strtotime($value->termino)) <> date('m',strtotime($value->inicio))) AND (date('Y-m',strtotime($value->inicio)) <> date('Y-m',strtotime($inicio_curso)))) {
                        $conteo1 += 1;
                    }
                    $mesact = date('m',strtotime($value->inicio));
                } elseif ($key > 0) {
                    if (($value->termino >= date('Y-m-d',strtotime(Carbon::parse($temp)->subDay(30)->format('Y-m-d')))) AND ($value->termino <= $temp2)) {
                        if (date('m',strtotime($value->termino)) < $mesact) {
                            $conteo1 += 1;
                        }
                        if ((date('m',strtotime($value->termino)) <> date('m',strtotime($value->inicio))) AND (date('Y-m',strtotime($value->inicio)) <> date('Y-m',strtotime($temp)))) {
                            $conteo1 += 1;
                        }
                        $mesact = date('m',strtotime($value->inicio));
                        $temp = $value->inicio;
                        $temp2 = $value->termino;
                    } elseif ($mesact <> date('m',strtotime($value->termino))) {
                        break;
                    }
                }
            }
        }
        if (DB::table('tbl_cursos')->where('status','<>','CANCELADO')->where('id_instructor',$id_instructor)->where('folio_grupo','<>',$id_curso)->whereRaw("((inicio <= cast((cast('$termino_curso' as date) + cast('30 days' as interval)) as date)) AND (inicio >= '$inicio_curso')) OR ((termino <= cast((cast('$termino_curso' as date) + cast('30 days' as interval)) as date)) AND (termino >= '$inicio_curso'))")->exists()) {
            $actinstru = DB::table('tbl_cursos')->select('inicio','termino','folio_grupo')
                ->where('status','<>','CANCELADO')
                ->where('id_instructor',$id_instructor)
                ->where('folio_grupo','<>',$id_curso)
                ->whereRaw("(((inicio <= '$tmes') AND (inicio >= '$inicio_curso')) OR ((termino <= '$tmes') AND (termino >= '$inicio_curso')))")
                ->orderBy('inicio','asc')->get();//dd($actinstru);
                foreach ($actinstru as $key => $value) {
                    if ($conteo2 > 5) {
                       return "La actividad del instructor por mes supera el límite permitido (5 meses) ";
                    } elseif ($key == 0) {
                        $temp = $value->inicio;
                        $temp2 = $value->termino;
                        if (date('Y-m',strtotime($value->inicio)) > date('Y-m',strtotime($termino_curso))) {
                            $conteo2 += 1;
                        }
                        if ((date('m',strtotime($value->inicio)) <> date('m',strtotime($value->termino))) AND (date('Y-m',strtotime($value->inicio)) <> date('Y-m',strtotime($inicio_curso)))) {
                            $conteo2 += 1;
                        }
                        $mesact = date('m',strtotime($value->termino));
                    } elseif ($key > 0) {
                        if (($value->inicio >= $temp) AND ($value->inicio <= date('Y-m-d',strtotime(Carbon::parse($temp2)->addDay(30)->format('Y-m-d'))))) {
                            if (date('m',strtotime($value->termino)) > $mesact) {
                                $conteo2 += 1;
                            }
                            if ((date('m',strtotime($value->termino)) <> date('m',strtotime($value->inicio))) AND (date('Y-m',strtotime($value->inicio)) <> date('Y-m',strtotime($temp)))) {
                                $conteo2 += 1;
                            }
                            $mesact = date('m',strtotime($value->termino));
                            $temp = $value->inicio;
                            $temp2 = $value->termino;
                        } elseif ($mesact <> date('m',strtotime($value->inicio))) {
                            break;
                        }
                    }
                }
        }
        if (($conteo1 + $conteo2) > 5) {
            return "La actividad del instructor por mes supera el límite permitido (5 meses) ";
        }
        //
        try {
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
            $agenda->iduser_created = Auth::user()->id;
            $agenda->save();
        } catch (QueryException $ex) {
            //dd($ex);
            return 'duplicado';
        }
        $dias_curso = $this->dias($id_curso);
        //$result = DB::table('tbl_cursos')->where('folio_grupo',$id_curso)->update(['dia' => $dias_curso['nombre'], 'tdias' => $dias_curso['total']]);
    }
    public function dias($id){
        $dias_agenda = DB::table('agenda')
            ->select(
                db::raw("extract(dow from (generate_series(agenda.start, agenda.end, '1 day'::interval))) as dia"),
                db::raw("generate_series(agenda.start, agenda.end, '1 day'::interval)::date as fecha")
            )
            ->where('id_curso', $id)
            ->orderBy('fecha')
            ->get();
        if (count($dias_agenda) > 0) {
            $dias = [];
            $temp = $dias_agenda[0]->dia;
            $temp2 = null;
            $save = false;
            $conteo = count($dias_agenda);
            $dias_a = [];
            foreach ($dias_agenda as $key => $value) {
                if ($key > 0) {
                    if ((($temp + 1) == $value->dia) && ($temp2 == null)) {
                        $temp2 = $value->dia;
                        $save = false;
                    } elseif ($temp2 && (($temp2 + 1) == $value->dia)) {
                        $temp2 = $value->dia;
                        $save = false;
                    } elseif ((($temp == '6') || ($temp2 == '6')) && ($value->dia == '0')) {
                        $temp2 = $value->dia;
                        $save = false;
                    } elseif ((($temp == $value->dia) || ($temp2 == $value->dia)) && ($value->fecha == $dias_agenda[$key - 1]->fecha)) {
                        $save = false;
                    } else {
                        $save = true;
                    }
                    if ($save == true) {
                        $dias[] = [$temp, $temp2];
                        $temp = $value->dia;
                        $temp2 = null;
                        $save = false;
                    }
                };
                if ($key == ($conteo - 1)) {
                    $dias[] = [$temp, $temp2];
                }
            }
            foreach ($dias as $item) {
                if (($item[0] + 1) < ($item[1])) {
                    $dias_a[] = $this->dia($item[0]) . ' A ' . $this->dia($item[1]);
                } elseif (($item[0] + 1) == ($item[1])) {
                    $dias_a[] = $this->dia($item[0]) . ' Y ' . $this->dia($item[1]);
                } elseif (($item[0] == '6') && ($item[1] == '0')) {
                    $dias_a[] = $this->dia($item[0]) . ' Y ' . $this->dia($item[1]);
                } elseif ((($item[0]) > ($item[1])) && isset($item[1])) {
                    $dias_a[] = $this->dia($item[0]) . ' A ' . $this->dia($item[1]);
                } else {
                    $dias_a[] = $this->dia($item[0]);
                }
            }
            if (count(array_unique(array_count_values($dias_a))) == 1) {
                $dias_a = array_unique($dias_a);
            }
            $dias_a = implode(", ", $dias_a);
        } else {
            $dias_a = 0;
        }
        $total_dias = DB::table('agenda')
            ->select(DB::raw("(generate_series(agenda.start, agenda.end, '1 day'::interval))::date as dias"))
            ->where('id_curso', $id)
            ->orderBy('dias')
            ->pluck('dias');
        $tdias = 0;

        foreach ($total_dias as $key => $value) {
            if ($key > 0) {
                if ($value != $total_dias[$key - 1]) {
                    $tdias += 1;
                }
            } else {
                $tdias = 1;
            }
        }
        $insert_dias ['nombre'] = $dias_a;
        $insert_dias ['total'] = $tdias;
        return $insert_dias;
    }

    # Made by Jose Luis Moreno Arcos / Funcion que genera pdf de soporte de constancias.
    public function genpdf_soporte(Request $request){
        $idorg = $request->idorg; #organismo
        $numficio = $request->num_oficio; #numero de oficio
        $datos_titular = $request->datos_titular; #datos del titular en caso de que no firme el titular del org
        $unidad_sop = $request->unidad_sop; #unidad
        $cgeneral_sop = $request->cgeneral_sop; #convenio general
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $distintivo = DB::table('tbl_instituto')->value('distintivo'); #texto de encabezado del pdf

        #recuperamos el id del curso a traves del folio
        $folio_grupo =  $_SESSION['folio']; #folio de grupo
        $id_curso = DB::table('tbl_cursos')->where('folio_grupo',$_SESSION['folio'])->pluck('id')->first(); #id curso
        $dta_certificacion = DB::table('tbl_funcionarios')->where('id', 18)->pluck('nombre')->first();

         #consulta de organismo o en su caso partimos el texto del campo titular
         $partes_titu = []; $organismo = '';
         if($datos_titular != '') {
            $partes_titu = explode(",", $datos_titular);
         }else{
             $organismo = DB::table('organismos_publicos')->select('nombre_titular', 'cargo_fun')->where('id', '=', $idorg)->first();
         }

        #Insertamos o actualizamos registros en la tabla de expedientes unicos
        $parte1 = (count($partes_titu)>0) ? $partes_titu[0] : "";
        $parte2 = (count($partes_titu)>0) ? $partes_titu[1] : "";

        $soporte_const = [
            "num_oficio" => $numficio,
            "titular_depen" => $parte1,
            "cargo_titular" => $parte2,
            "fecha_gen" => date("Y-m-d"),
            "url_pdf" => "",
        ];
        $soporte_const_json = json_encode($soporte_const);

        $resquery = DB::table('tbl_cursos_expedientes')->updateOrInsert(
            ['folio_grupo' => $folio_grupo, 'id' => $id_curso], // Buscar por el campo 'id' folio_grupo
            ['id_curso' =>$id_curso, 'folio_grupo' => $folio_grupo, 'sop_constancias' => $soporte_const_json,
            'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d'),
            'iduser_created' => Auth::user()->id, 'iduser_updated' => Auth::user()->id]
        );
        if(!$resquery){
            return redirect()->back()->withErrors(['menssage' => 'Hubo un problema al realizar la operación.']);
        }


        #Datos de encabezado y pie de pagina
        $data = DB::table('tbl_cursos as cur')->select('cur.fcespe', 'cur.munidad','tu.unidad', 'tu.dunidad',
        'tu.pdunidad', 'cur.realizo', 'cur.valido', 'tu.municipio', 'tu.direccion', 'tu.ubicacion')
        ->Join('tbl_unidades as tu', 'tu.unidad', 'cur.unidad')
        ->where('cur.folio_grupo','=',"$folio_grupo")->first();


        $direccion = $data->direccion;
        $unidad = strtoupper($data->ubicacion);
        $municipio = mb_strtoupper($data->municipio, 'UTF-8');

        #OBTENEMOS LA LISTA DE CURSOS
        $tabla_contenido = DB::table('tbl_cursos as c')
        ->select('c.id', 'c.curso', 'c.folio_grupo', 'c.clave', 'c.cespecifico', 'c.inicio',
        'c.termino', 'c.tcapacitacion', 'c.mod', 'c.nombre', 'c.hini', 'c.hfin')
        ->selectRaw('(SELECT COUNT(id_curso) FROM public.tbl_folios WHERE id_curso = c.id) AS cantidad_folios')
        ->selectRaw('(SELECT MIN(folio) FROM public.tbl_folios WHERE id_curso = c.id) AS primer_folio')
        ->selectRaw('(SELECT MAX(folio) FROM public.tbl_folios WHERE id_curso = c.id) AS ultimo_folio')
        ->join('tbl_cursos_expedientes as e', 'c.folio_grupo', '=', 'e.folio_grupo')
        ->whereJsonContains('e.sop_constancias->num_oficio', $numficio)
        ->orderByRaw('EXTRACT(MONTH FROM c.termino)')
        ->get();

        #RANGO DE MESES
        $bd_rango_mes = DB::table('tbl_cursos as c')
        ->join('tbl_cursos_expedientes as e', 'c.folio_grupo', '=', 'e.folio_grupo')
        ->whereRaw("jsonb_extract_path_text(e.sop_constancias, 'num_oficio') = ?", [$numficio])
        ->selectRaw("MIN(DATE_PART('month', termino)) as mes_minimo, MAX(DATE_PART('month', termino)) as mes_maximo")
        ->get();
        $mesmin = $bd_rango_mes[0]->mes_minimo;
        $mesmax = $bd_rango_mes[0]->mes_maximo;
        $rango_mes = ($mesmin != $mesmax) ? $meses[$mesmin-1].' - '.$meses[$mesmax-1] : $meses[$mesmin-1];


        #TOTAL DE FOLIOS
        $total_folios = 0;
        for ($i=0; $i < count($tabla_contenido); $i++) {
            $total_folios += $tabla_contenido[$i]->cantidad_folios;
        }
        $total_folios = ($total_folios < 10) ? '0'+ $total_folios : $total_folios;
        $total_cursos = count($tabla_contenido);
        $total_cursos = ($total_cursos < 10) ? '0'+ $total_cursos : $total_cursos;


        #OBTENEMOS LA FECHA ACTUAL
        $fechaActual = getdate();
        $anio = $fechaActual['year']; $mes = $fechaActual['mon']; $dia = $fechaActual['mday'];
        $dia = ($dia < 10) ? '0'.$dia : $dia;

        $fecha_comp = $dia.' de '.$meses[$mes-1].' del '.$anio;

        $pdf = PDF::loadView('reportes.soporte_entrega_constancia',compact('distintivo', 'direccion', 'data', 'unidad', 'organismo', 'numficio',
        'partes_titu', 'municipio', 'fecha_comp', 'tabla_contenido', 'rango_mes', 'total_cursos', 'total_folios', 'dta_certificacion'));
        return $pdf->stream('Soporte de Entrega');
    }
}
