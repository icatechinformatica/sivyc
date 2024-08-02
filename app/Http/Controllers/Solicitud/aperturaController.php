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
use App\Models\ModelExpe\ExpeUnico;
use App\Models\Alumnopre;

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
        //$this->path_files = env("APP_URL").'/storage/uploadFiles';
        
        $this->path_uploadFiles = env("APP_URL").'/storage/uploadFiles';
        $this->path_files = env("APP_URL").'/storage/';
        $this->path_files_cancelled = env("APP_URL").'/grupos/recibo/descargar?folio_recibo=';
        
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
        $folio_grupo =  $grupo = $alumnos = $message = $medio_virtual = $exoneracion = $instructor = $plantel = $programa = $tcurso = $tcuota =
        //$muni = 
        $instructores = $convenio = $localidad = $exonerado = $num_oficio_sop = $titular_sop = $ValidaInstructorPDF = NULL;
        $recibo =[];
        $url_soporte = '';
        if($request->folio_grupo)  $folio_grupo = $request->folio_grupo;
        elseif(isset($_SESSION['folio_grupo'])) $folio_grupo = $_SESSION['folio_grupo'];
        $_SESSION['alumnos'] = NULL;

        //NUEVO
        if($folio_grupo) list($grupo, $alumnos) = $this->grupo_alumnos($folio_grupo);         
        if($grupo){
            #consultamos registros para generar pdf soporte de constancias
            $sop_expediente = DB::table('tbl_cursos_expedientes')->select('sop_constancias')->where('folio_grupo', '=', $folio_grupo)->first();
            if(isset($sop_expediente->sop_constancias)){
                $sop_constancias = json_decode($sop_expediente->sop_constancias);

                if(isset($sop_constancias->num_oficio) && isset($sop_constancias->titular_depen)
                && isset($sop_constancias->cargo_titular)){
                    $num_oficio_sop = $sop_constancias->num_oficio;
                    $titular_sop = ($sop_constancias->titular_depen != "" && $sop_constancias->cargo_titular != "") ? $sop_constancias->titular_depen.', '.$sop_constancias->cargo_titular : '';

                    $bddoc_soporte = ExpeUnico::select('academico->doc_25->url_documento as url_documento')
                    ->where('sop_constancias->num_oficio', $num_oficio_sop)->where('academico->doc_25->url_documento', '<>', '')->first();

                    $url_soporte = ($bddoc_soporte !== null) ? $this->path_files.$bddoc_soporte->url_documento : '';
                }
            }
           
            
            $_SESSION['folio_grupo'] = $grupo->folio_grupo;
            $anio_hoy = date('y');                
            $localidad = DB::table('tbl_localidades')->where('clave',$grupo->clave_localidad)->pluck('localidad')->first();

            $_SESSION['alumnos'] = $alumnos;
            $_SESSION['grupo'] = $grupo;
            //dd($alumnos);
            $plantel = $this->plantel();
            /*
            if($grupo->depen AND $grupo->mod=='CAE'){
                    $organismo = $grupo->depen; // DB::table('organismos_publicos')->where('id',$grupo->id_organismo)->value('organismo');
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
                */
                $programa = $this->programa();

                $instructor = $this->instructor($grupo->id_instructor);
                $instructores = $this->instructores($grupo);    //dd($instructores);
                $exoneracion = $this->exoneracion($this->id_unidad);
                $exoneracion["NINGUNO"] = "NINGUNO";
                //$efisico = $this->efisico();
                $exonerado = DB::table('exoneraciones')->where('folio_grupo',$grupo->folio_grupo)->where('status','<>','CAPTURA')->where('status','<>','CANCELADO')->exists();

                $medio_virtual = $this->medio_virtual();

                $tcurso = $this->tcurso();
                //var_dump($instructor);exit;
                if($grupo->clave !='0') $message = "Clave de Apertura Asignada";
                elseif($grupo->status_curso) $message = "Estatus: ". $grupo->status_curso;
                if($grupo->tipo) $tcuota = $this->tcuota[$grupo->tipo];
                $recibo = DB::table('tbl_recibos')->where('folio_grupo',$_SESSION['folio_grupo'])->where('status_folio','ENVIADO')->first();
                $grupo_mespecialidad = $grupo->instructor_mespecialidad;
                $ValidaInstructorPDF = DB::table('especialidad_instructores')->where('especialidad_id', $grupo->id_especialidad)
                    ->where('id_instructor', $grupo->id_instructor)
                    ->whereExists(function ($query) use ($grupo_mespecialidad){
                    $query->select(\DB::raw("elem->>'arch_val'"))
                        ->from(\DB::raw("jsonb_array_elements(hvalidacion) AS elem"))
                        ->where(\DB::raw("elem->>'memo_val'"), '=', $grupo_mespecialidad);
                })
                ->value(\DB::raw("(SELECT elem->>'arch_val' FROM jsonb_array_elements(hvalidacion) AS elem WHERE elem->>'memo_val' = '$grupo_mespecialidad') as pdfvalida"));

            
        }else $message = "Grupo número ".$folio_grupo .", no disponible para este usuario.";
        $tinscripcion = $this->tinscripcion();


        if(session('message')) $message = session('message');//dd($grupo);
        return view('solicitud.apertura.index', compact('message','grupo','alumnos','plantel','programa',
        'instructor','exoneracion','medio_virtual','tcurso','tinscripcion','tcuota','instructores','convenio','localidad','exonerado',
        'num_oficio_sop', 'titular_sop','recibo','ValidaInstructorPDF', 'url_soporte'));

        /*return view('solicitud.apertura.index', compact('comprobante','efisico','message','grupo','alumnos','plantel','depen','sector','programa',
            'instructor','exoneracion','medio_virtual','tcurso','tinscripcion','tcuota','instructores','convenio','localidad','exonerado',
            'num_oficio_sop', 'titular_sop','recibo','ValidaInstructorPDF', 'url_soporte'));*/
    }


    //NUEVO
    private function grupo_alumnos($folio_grupo){
        $grupo =  DB::table('tbl_cursos as tc')->where('tc.folio_grupo', $folio_grupo)  
            ->select(//DE LA APERTURA
                'tc.*',
                'c.costo as costo_individual',
                DB::raw('ar.observaciones as obs_vincula'),
                DB::raw("COALESCE( 
                    CASE WHEN tc.hini LIKE '%p%' and SUBSTRING(tc.hini, 1, 2)::integer <> 12 THEN (SUBSTRING(tc.hini, 1, 5)::time+'12:00')::text
                         ELSE SUBSTRING(tc.hini, 1, 5) 
                    END, SUBSTRING(ar.horario, 1, 5)) as hini"),                    
                DB::raw("COALESCE(
                    CASE WHEN tc.hfin LIKE '%p%' and SUBSTRING(tc.hfin, 1, 2)::integer <> 12 THEN (SUBSTRING(tc.hfin, 1, 5)::time+'12:00')::text
                         ELSE SUBSTRING(tc.hfin, 1, 5)
                    END,  SUBSTRING(ar.horario, 9, 5)) as hfin"), 

                DB::raw("CASE
                           WHEN tr.status_folio='CANCELADO' THEN concat('".$this->path_files_cancelled."',tr.folio_recibo)
                            WHEN tc.comprobante_pago <> 'null' THEN concat('".$this->path_uploadFiles."',tc.comprobante_pago)
                            WHEN tr.file_pdf <> 'null' THEN concat('".$this->path_files."',tr.file_pdf)
                        END as comprobante_pago"
                        ),                
                DB::raw('COALESCE(tr.folio_recibo, COALESCE(tc.folio_pago, ar.folio_pago)) as folio_pago'),
                DB::raw('COALESCE(tr.fecha_expedicion, COALESCE(tc.fecha_pago, ar.fecha_pago)) as fecha_pago'),                
                DB::raw("COALESCE(tc.solicita, CONCAT(tu.vinculacion,', ',pvinculacion)) as solicita"),
                DB::raw('COALESCE(tc.tdias, null) as tdias'),
                DB::raw('ar.turnado as  turnado_grupo'),
                DB::raw('ar.observaciones as obs_vincula'),                            
                DB::raw("CASE WHEN tu.vinculacion=tu.dunidad THEN true ELSE false END as editar_solicita"),
                DB::raw("CASE WHEN tr.folio_recibo is not null THEN true ELSE false END as es_recibo_digital")
            )
            ->leftjoin('alumnos_registro as ar','tc.folio_grupo','ar.folio_grupo')
            ->leftJoin('tbl_recibos as tr', function ($join) {
                $join->on('tr.folio_grupo', '=', 'ar.folio_grupo')                        
                     ->where('tr.status_folio','ENVIADO'); 
            })            
            ->leftjoin('cursos as c','c.id','ar.id_curso')
            ->leftjoin('tbl_unidades as tu','ar.unidad','tu.unidad')                     
            ->orderby('ar.id_vulnerable','DESC') 
            ->first();          
//dd($grupo);

        $alumnos = DB::table('alumnos_registro as ar')
            ->select('ar.id as id_reg', 'ar.id_vulnerable as id_gvulnerable',
                //DATOS DE LOS ALUMNOS 
                DB::raw('COALESCE(ti.id_pre, ar.id_pre) as id_pre'),
                DB::raw('COALESCE(ti.id_cerss, ar.id_cerss) as id_cerss'),
                DB::raw('COALESCE(ti.abrinscri, ar.abrinscri) as abrinscri'),
                DB::raw('COALESCE(ti.estado_civil, ap.estado_civil) as estado_civil'),
                DB::raw('COALESCE(ti.discapacidad, ap.discapacidad) as discapacidad'),
                DB::raw('COALESCE(ti.etnia, ap.etnia) as etnia'),
                DB::raw('COALESCE(ti.indigena, ap.indigena) as indigena'),
                DB::raw("'0' as calificacion"),
                

                DB::raw('COALESCE(ti.curp, ar.curp) as curp'),
                DB::raw('COALESCE(ti.matricula, ar.no_control) as matricula'),
                DB::raw("COALESCE(ti.alumno, concat(ar.apellido_paterno,' ', ar.apellido_materno,' ',ar.nombre)) as alumno"),
                DB::raw('COALESCE(substring(ti.curp,11,1), substring(ar.curp,11,1)) as sexo'),
                DB::raw("(CONCAT(
                            CASE 
                                WHEN SUBSTRING( COALESCE(ti.curp, ar.curp), 5, 2) > TO_CHAR(NOW(), 'YY') THEN CONCAT('19', SUBSTRING(COALESCE(ti.curp, ar.curp), 5, 2))
                                ELSE CONCAT('20', SUBSTRING(COALESCE(ti.curp, ar.curp), 5, 2))
                            END,'-', SUBSTRING(COALESCE(ti.curp, ar.curp), 7, 2), '-', SUBSTRING(COALESCE(ti.curp, ar.curp), 9, 2) )

                    ) as fecha_nacimiento"),
                DB::raw('COALESCE(EXTRACT(year from (age(ti.inicio,ap.fecha_nacimiento))) , EXTRACT(year from (age(ar.inicio,ap.fecha_nacimiento))) ) as edad'),
                DB::raw('COALESCE(ti.escolaridad, ar.escolaridad) as escolaridad'),
                DB::raw("COALESCE(
                    CASE WHEN ti.id_gvulnerable IS NULL THEN NULL
                        ELSE ( SELECT STRING_AGG(grupo, ', ') FROM grupos_vulnerables WHERE id IN ( SELECT CAST(jsonb_array_elements_text(ti.id_gvulnerable) AS bigint)))
                    END,
                    CASE WHEN ap.id_gvulnerable IS NULL THEN NULL
                        ELSE ( SELECT STRING_AGG(grupo, ', ') FROM grupos_vulnerables WHERE id IN ( SELECT CAST(jsonb_array_elements_text(ap.id_gvulnerable) AS bigint)))
                    END) as grupos"),            
                DB::raw('COALESCE(ti.inmigrante, ap.inmigrante) as inmigrante'),
                DB::raw('ap.es_cereso'),
                DB::raw('COALESCE(ti.familia_migrante, ap.familia_migrante) as familia_migrante'),
                DB::raw('COALESCE(ti.madre_soltera, ap.madre_soltera) as madre_soltera'),
                DB::raw('COALESCE(ti.lgbt, ap.lgbt) as lgbt'),
                DB::raw('COALESCE(ti.nacionalidad, ap.nacionalidad) as nacionalidad'),
                DB::raw('COALESCE(ti.tinscripcion, ar.tinscripcion) as tinscripcion'),
                DB::raw('COALESCE(ti.costo, ar.costo) as costo'),
                DB::raw("COALESCE(ti.requisitos::jsonb->'documento', COALESCE(ar.requisitos::jsonb->'documento', ap.requisitos::jsonb->'documento')) as doc_requisitos"), 
                DB::raw("CASE WHEN  id_folio is not null and ti.status='EDICION' THEN  'CANCELAR FOLIO' ELSE ti.status END status"),
                DB::raw("CASE WHEN ti.id IS NULL AND '$grupo->clave' !='0' AND '$grupo->status_curso' ='AUTORIZADO' AND '$grupo->status' = 'NO REPORTADO' THEN 'INSERT'
                            ELSE  'VIEW ' END as mov")                
                )
                ->where('ar.folio_grupo',$folio_grupo)
                ->leftJoin('tbl_inscripcion as ti', function ($join) {
                    $join->on('ti.folio_grupo', '=', 'ar.folio_grupo')                        
                        ->on('ti.curp','ar.curp'); 
                })
                ->join('alumnos_pre as ap', 'ap.id', 'ar.id_pre')             
                ->leftjoin('tbl_unidades as tu','ar.unidad','tu.unidad' )   
                ->get();
        
//dd($alumnos);        
        if($grupo and $alumnos )  return [$grupo, $alumnos];
        else return $message = "OPERACION NO VALIDA.";
    }
    //FIN NUEVO
    public function search(Request $request){
        $_SESSION = null;
        $aperturas = DB::table('tbl_cursos as tc')
            ->select('tc.unidad','tc.num_revision','tc.munidad','tc.file_arc01','tc.turnado','tc.status_curso','tc.status_solicitud','tc.status','tc.pdf_curso','tc.fecha_apertura')
            ->leftJoin('alumnos_registro as a','tc.folio_grupo','=','a.folio_grupo')
            ->leftJoin('tbl_unidades as u', 'tc.unidad','=','u.unidad')
            ->where('a.turnado','<>','VINCULACION')
            ->where('u.id','=',Auth::user()->unidad);
        if ($request->folio_grupo) {
            $aperturas = $aperturas->where('tc.munidad','=',$request->folio_grupo)
                ->orWhere('tc.num_revision','=',$request->folio_grupo);
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
        if($_SESSION['folio_grupo'] == $request->folio_grupo){
            $result = DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->update(['turnado' => "VINCULACION",'fecha_turnado' => null,'fmpreapertura'=>null]);
            DB::table('tbl_cursos')->where('folio_grupo', $_SESSION['folio_grupo'])->update(['fecha_arc01'=>null]);
            if($result){
                $message = "El grupo fué turnado correctamente a VINCULACIÓN";
                unset($_SESSION['folio_grupo']);
            }
        }
        return redirect('solicitud/apertura')->with('message',$message);
   }



    public function store(Request $request, \Illuminate\Validation\Factory $validate)
    {
        $message = 'Operación fallida, vuelva a intentar..';
        if ($_SESSION['folio_grupo'] == $request->folio_grupo) {

            $result =  DB::table('tbl_cursos')->where('clave', '0')->updateOrInsert(
                ['folio_grupo' => $_SESSION['folio_grupo']],
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
        if($_SESSION['alumnos'] AND $_SESSION['folio_grupo'] == $request->folio_grupo){
            $grupo = DB::table('tbl_cursos as c')->where('status_curso','AUTORIZADO')->where('status','NO REPORTADO')->where('c.folio_grupo',$_SESSION['folio_grupo'])->first();
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
                        DB::table('alumnos_registro')->where('id_pre', $a->id_pre)->where('no_control',null)->where('folio_grupo',$_SESSION['folio_grupo'])->update(['no_control'=>$matricula]);

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
                        'id_gvulnerable'=>$a->id_gvulnerable,
                        'requisitos'=>json_decode($a->requisitos)
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
        $folio = $_SESSION['folio_grupo'];
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
        $id_curso = $_SESSION['folio_grupo'];
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
        $folio_grupo =  $_SESSION['folio_grupo']; #folio de grupo
        $id_curso = DB::table('tbl_cursos')->where('folio_grupo',$_SESSION['folio_grupo'])->pluck('id')->first(); #id curso
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
        $parte2 = (count($partes_titu)>1) ? $partes_titu[1] : "";

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
        ->selectRaw('(SELECT COUNT(id_curso) FROM public.tbl_folios WHERE id_curso = c.id AND motivo = \'ACREDITADO\') AS cantidad_folios')
        ->selectRaw('(SELECT MIN(folio) FROM public.tbl_folios WHERE id_curso = c.id) AS primer_folio')
        ->selectRaw('(SELECT MAX(folio) FROM public.tbl_folios WHERE id_curso = c.id) AS ultimo_folio')
        // ->selectRaw('(SELECT folio FROM public.tbl_folios WHERE id_curso = c.id) AS all_folios')
        ->selectRaw('(SELECT STRING_AGG(folio, \',\') FROM public.tbl_folios WHERE id_curso = c.id AND motivo = \'ACREDITADO\') AS all_folios')
        ->join('tbl_cursos_expedientes as e', 'c.folio_grupo', '=', 'e.folio_grupo')
        ->whereJsonContains('e.sop_constancias->num_oficio', $numficio)
        ->orderByRaw('EXTRACT(MONTH FROM c.termino)')
        ->get();

        ##Procesar folios
        $rango_folios = [];
        foreach ($tabla_contenido as $cursos){
            $rango = $this->process_folios($cursos->all_folios);
            $rango_folios[] = $rango;
        }

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
        'partes_titu', 'municipio', 'fecha_comp', 'tabla_contenido', 'rango_mes', 'total_cursos', 'total_folios', 'dta_certificacion','rango_folios'));
        return $pdf->stream('Soporte de Entrega');
    }

    ##Procesar rango de folios de alumnos
    protected function process_folios($cadena){
        //$cadena = "A151362,A151363,A151364,A151365,A151366,A151367,A151368,A151369,A151370,A159321,A159322,A159323,A159324,A159325,A159326";

        $elementos = explode(",", $cadena);
        $rangos = [];
        $numeros = [];
        foreach ($elementos as $key => $elemento) {
            $numero = (int)str_replace("A", "", $elemento); // Elimina la letra "A" y convierte a entero
            $numeros[] = $numero;
        }

        $resultado = [];
        $inicio = $numeros[0];
        $anterior = $numeros[0];

        foreach ($numeros as $index => $numero) {
            // Verificar si es el último elemento o si el próximo número no es consecutivo
            if ($index == count($numeros) - 1 || $numeros[$index + 1] != $numero + 1) {
                // Agregar el inicio y este número al resultado si es el final de una serie
                $resultado[] = $inicio;
                $resultado[] = $numero;
                // Actualizar el nuevo inicio si hay más números después
                if ($index != count($numeros) - 1) {
                    $inicio = $numeros[$index + 1];
                }
            }
        }
        return $resultado;
    }


     /** Funcion para subir pdf al servidor by Jose Luis
     * @param string $pdf, $id $nom $anio $folder_destino
     */
    protected function pdf_upload($pdf, $id, $nom, $anio, $fold_destin)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $directorio = '/' . $anio . '/'.$fold_destin.'/'.$pdfFile;
        $pdf->storeAs('/uploadFiles/'.$anio.'/'.$fold_destin, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadFiles'. $directorio); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return [$pdfUrl, $directorio];
    }

    #Se encarga de subir los pdfs
    public function upload_pdfsoporte(Request $request) {
        $folio_grupo =  $_SESSION['folio_grupo'];
        $cursoInfo = DB::table('tbl_cursos')
        ->selectRaw('id as idcurso, EXTRACT(YEAR FROM inicio) as anio')->where('folio_grupo', $folio_grupo)->first();

        #Obtenemos el numero de oficio
        $sop_expediente = ExpeUnico::select('sop_constancias')->where('folio_grupo', '=', $folio_grupo)->first();

        $num_oficio_sop = $sop_expediente->sop_constancias['num_oficio'];

        ##COLOCAMOS LA ESTRUCTURA JSON A LOS CAMPOS NULOS
        $camposNulos = ExpeUnico::where('sop_constancias->num_oficio', $num_oficio_sop)->whereNull('vinculacion')
        ->whereNull('academico')->whereNull('administrativo')->get();
        if($camposNulos->isNotEmpty()){
            //Hacemos la actualización
            $json_vacios = $this->llenar_json_exp();
                DB::table('tbl_cursos_expedientes')->where('sop_constancias->num_oficio', $num_oficio_sop)
                ->whereNull('vinculacion')->whereNull('academico')->whereNull('administrativo')
                ->update([
                    'vinculacion' => $json_vacios[0],
                    'academico' => $json_vacios[1],
                    'administrativo' => $json_vacios[2]
                ]);
        }

        $anio = $cursoInfo->anio;
        $idcurso = $cursoInfo->idcurso;

        $archivo = $request->hasFile('archivoPDF');
        $opcion = $request->opcion;
        $partImg = basename($request->urlImg);

        #Condicion para asignar nombre a los docs
        if ($archivo) {
            if($partImg != ''){
                #Reemplazar
                $filePath = 'uploadFiles/'.$anio.'/soporteconst/'.$partImg;
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
                // else { return response()->json(['mensaje' => "¡ERROR!, DOCUMENTO NO ENCONTRADO"]); }
            }
            #Guardamos en la bd
            try {
                $vincu = ExpeUnico::find($idcurso);
                $doc = $request->file('archivoPDF'); # obtenemos el archivo
                $urldoc = $this->pdf_upload($doc, $idcurso, 'soporte_constancia', $anio, 'soporteconst'); # invocamos el método
                $url = $vincu->academico;
                $url['doc_25']['url_documento'] = $urldoc[1];
                $url['doc_25']['existe_evidencia'] = 'si';
                $url['doc_25']['fecha_subida'] = date('Y-m-d');
                $vincu->academico = $url; # guardamos el path
                $vincu->save();

                #Agregar url a los grupos con el mismo numero de oficio JSONB
                ExpeUnico::whereJsonContains('sop_constancias->num_oficio', $num_oficio_sop)
                ->update(['academico->doc_25->url_documento' => $urldoc[1]]);


            } catch (\Throwable $th) {
                return response()->json(['mensaje' => "¡ERROR AL INTENTAR GUARDAR EL ARCHIVO!"]);
            }

        }else{
            return response()->json([
                'status' => 500,
                'mensaje' => 'EL ARCHIVO A SUBIR NO ES COMPATIBLE'
            ]);
        }

        return response()->json([
            'status' => 200,
            'mensaje' => 'EL ARCHIVO SE HA SUBIDO DE MANERA EXITOSA'
        ]);
    }

    #Llenar array para anexar al json de expedientes
    public function llenar_json_exp(){
        $vinculacion = [
            "doc_1" => [
                "nom_doc" => "Convenio Específico / Acta de acuerdo.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => "",
                "iduser" => "",
                "convenio_firma" => "",
                "convenio_cerss_one" => "",
                "convenio_cerss_two" => "",
                "url_pdf_acta" => "",
                "url_pdf_convenio" => ""
            ],
            "doc_2" => [
                "nom_doc" => "Copia de autorización de Exoneración o Reducción de cuota de recuperación.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_3" => [
                "nom_doc" => "Original  de la  Solicitud de Apertura del curso o certificacion al Depto. Académico.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_4" => [
                "nom_doc" => "SID-01 solicitud de inscripción del interesado.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_5" => [
                "nom_doc" => "CURP actualizada o Copia de Acta de Nacimiento.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_6" => [
                "nom_doc" => "Copia de comprobante de último grado de estudios (en caso de contar con el).",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_7" => [
                "nom_doc" => "Copia del recibo oficial de la cuota de recuperación expedido por la Delegación Administrativa y comprobante de depósito o transferencia Bancaria.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_8" => [
                "nom_doc" => "Soporte de manifiesto de inscripción",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "status_dpto" => "CAPTURA",
            "status_save" => false,
            "fecha_guardado" => "",
            "fecha_envio_dta" => "",
            "fecha_validado" => "",
            "fecha_retornado" => "",
            "id_user_save" => null,
            "id_user_valid" => null,
            "id_user_return" => null,
            "descrip_return" => ""
        ];
        $academico = [
            "doc_8" => [
                "nom_doc" => "Original de memorándum ARC-01, solicitud de Apertura de cursos de Capacitación y/o certificación a la Dirección Técnica Académica.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_9" => [
                "nom_doc" => "Copia de memorándum de autorización de ARC-01, emitido por la Dirección Técnica Académica.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_10" => [
                "nom_doc" => "Original de memorándum ARC-02, solicitud de modificación, reprogramación y/o cancelación de curso a la Dirección Técnica Académica.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_11" => [
                "nom_doc" => "Copia de Memorándum de autorización de ARC-02 emitido por la Dirección Técnica Académica.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_12" => [
                "nom_doc" => "Copia de RIACD-02 Inscripción.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_13" => [
                "nom_doc" => "Copia de RIACD-02 Acreditación.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_14" => [
                "nom_doc" => "Copia de RIACD-02 Certificación.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_15" => [
                "nom_doc" => "Copia de LAD-04 (Lista de Asistencia).",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_16" => [
                "nom_doc" => "Copia de RESD-05 (Registro de Evaluación por Subobjetivos).",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_17" => [
                "nom_doc" => "Originales o Copia de las Evaluaciones y/o Reactivos de aprendizaje del alumno y/o resumen de actividades. en caso de ICATECH virtual.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_18" => [
                "nom_doc" => "Original o Copia de las Evaluaciones al Docente y Evaluación del Curso y/o resumen de actividades en caso de ICATECH virtual.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_19" => [
                "nom_doc" => "Reporte fotográfico, como mínimo 2 dos fotografías.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_25" => [
                "nom_doc" => "Oficio de entrega de constancias",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "status_dpto" => "CAPTURA",
            "status_save" => false,
            "fecha_guardado" => "",
            "fecha_envio_dta" => "",
            "fecha_validado" => "",
            "fecha_retornado" => "",
            "id_user_save" => null,
            "id_user_valid" => null,
            "id_user_return" => null,
            "descrip_return" => ""
        ];
        $administrativa = [
            "doc_20" => [
                "nom_doc" => "Memorandum de solicitud de suficiencia presupuestal.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_21" => [
                "nom_doc" => "Copia memorandum de autorización de Suficiencia Presupuestal.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_22" => [
                "nom_doc" => "Original de Contrato de prestación de servicios profesionales del Instructor externo, con firma autógrafa o Firma Electrónica.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_23" => [
                "nom_doc" => "Copia de solicitud de pago al Instructor.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_24" => [
                "nom_doc" => "Comprobante Fiscal Digital por Internet o comprobante de transferencia bancaria de pagp al instructor externo.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "status_dpto" => "CAPTURA",
            "status_save" => false,
            "fecha_guardado" => "",
            "fecha_envio_dta" => "",
            "fecha_validado" => "",
            "fecha_retornado" => "",
            "id_user_save" => null,
            "id_user_valid" => null,
            "id_user_return" => null,
            "descrip_return" => ""
        ];

        $json_vacios = [$vinculacion, $academico, $administrativa];
        return $json_vacios;
    }



}
