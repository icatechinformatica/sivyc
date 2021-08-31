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
        $valor = $grupo = $alumnos = $message = $municipio = $medio_virtual = $depen = $exoneracion = $instructor = $plantel = $programa = $sector = $tcurso = $tcuota = NULL;
        if($request->valor)  $valor = $request->valor;
        elseif(isset($_SESSION['folio'])) $valor = $_SESSION['folio'];
        $_SESSION['alumnos'] = NULL;
        if($valor){
            $grupo =  DB::table('alumnos_registro as ar')->select('ar.id_curso','ar.unidad','ar.horario','e.nombre as espe','a.formacion_profesional as area',
                'ar.folio_grupo','ar.tipo_curso as tcapacitacion','c.nombre_curso as curso','c.modalidad as mod','ar.horario','c.horas as dura','c.costo as costo_individual','c.id_especialidad',
                DB::raw("SUM(CASE WHEN substring(ap.curp,11,1) ='H' THEN 1 ELSE 0 END) as hombre"),DB::raw("SUM(CASE WHEN substring(ap.curp,11,1)='M' THEN 1 ELSE 0 END) as mujer"),'c.memo_validacion as mpaqueteria',
                'tc.hini','tc.hfin','tc.nota',DB::raw(" COALESCE(tc.clave, '0') as clave"),
                'tc.id_municipio','tc.status_curso','tc.dia','tc.inicio','tc.termino','tc.plantel',
                'tc.sector','tc.programa','tc.efisico','tc.depen','tc.cgeneral','tc.fcgen','tc.cespecifico','tc.fcespe','tc.mexoneracion','tc.medio_virtual',
                'tc.id_instructor','tc.tipo','tc.link_virtual','tc.munidad','tc.costo','tc.tipo','tc.status','tc.id','e.clave as clave_especialidad','tc.arc','tc.tipo_curso','ar.id_cerss','tc.tdias','c.rango_criterio_pago_maximo as cp')
                ->join('alumnos_pre as ap','ap.id','ar.id_pre')
                ->join('cursos as c','ar.id_curso','c.id')
                ->join('especialidades as e','e.id','c.id_especialidad') ->join('area as a','a.id','c.area')
                ->leftjoin('tbl_cursos as tc','tc.folio_grupo','ar.folio_grupo')
                ->where('ar.turnado','<>','VINCULACION')
                ->where('ar.folio_grupo',$valor);
            if($_SESSION['unidades']) $grupo = $grupo->whereIn('ar.unidad',$_SESSION['unidades']);
            $grupo = $grupo->groupby('ar.id_curso','ar.unidad','ar.horario', 'ar.folio_grupo','ar.tipo_curso','ar.horario','tc.arc','ar.id_cerss',
                'e.id','a.formacion_profesional','tc.id','c.id')->first();

            // var_dump($grupo);exit;
            if($grupo){
                $_SESSION['folio'] = $grupo->folio_grupo;
                $anio_hoy = date('y');

                $alumnos = DB::table('tbl_inscripcion as i')->select('i.*', DB::raw("'VIEW' as mov"))->where('i.folio_grupo',$valor)->get();
               // var_dump($alumnos);exit;

                if(count($alumnos)==0){
                    $alumnos = DB::table('alumnos_registro as ar')->select('ar.id as id_reg','ap.curp','ap.nombre','ap.apellido_paterno','ap.apellido_materno','ap.fecha_nacimiento AS FN','ap.sexo AS SEX',
                    DB::raw("CONCAT(ap.apellido_paterno,' ', ap.apellido_materno,' ',ap.nombre) as alumno"),'ar.id_cerss',
                    'ap.estado_civil','ap.discapacidad','ap.nacionalidad','ap.etnia','ap.indigena','ap.inmigrante','ap.madre_soltera','ap.familia_migrante',
                    'ar.costo','ar.tinscripcion',DB::raw("'0' as calificacion"),'ap.ultimo_grado_estudios as escolaridad','ap.empresa_trabaja as empleado',
                    'ap.matricula', 'ar.id_pre','ar.id', DB::raw("substring(curp,11,1) as sexo"),
                    DB::raw("substring(curp,5,2) as anio_nac"),
                    DB::raw("CASE WHEN substring(curp,5,2) <='".$anio_hoy."' THEN CONCAT('20',substring(curp,5,2),'-',substring(curp,7,2),'-',substring(curp,9,2))
                        ELSE CONCAT('19',substring(curp,5,2),'-',substring(curp,7,2),'-',substring(curp,9,2)) END AS fecha_nacimiento
                    "),
                    DB::raw("'INSERT' as mov"))
                    ->join('alumnos_pre as ap','ap.id','ar.id_pre')->where('ar.folio_grupo',$valor )
                    ->where('ar.eliminado',false)->get();
                }
                $_SESSION['alumnos'] = $alumnos;
                $_SESSION['grupo'] = $grupo;
                //var_dump($alumnos);exit;

                $plantel = $this->plantel();
                $depen = $this->dependencia($grupo->unidad);
                $depen["ICATECH"] = "ICATECH";
                $depen["GRUPO INDEPENDIENTE"] = "GRUPO INDEPENDIENTE";

                $sector = $this->sector();
                $programa = $this->programa();

                $municipio = $this->municipio();
                $instructor = $this->instructor($grupo->unidad, $grupo->id_especialidad);
                $exoneracion = $this->exoneracion($this->id_unidad);
                $exoneracion["NINGUNO"] = "NINGUNO";

                $medio_virtual = $this->medio_virtual();

                $tcurso = $this->tcurso();
                //var_dump($instructor);exit;
                if($grupo->clave !='0') $message = "Clave de Apertura Asignada";
                elseif($grupo->status_curso) $message = "Estatus: ". $grupo->status_curso;
                if($grupo->tipo) $tcuota = $this->tcuota[$grupo->tipo];
            }else $message = "Grupo número ".$valor .", turnado a VINCULACIÓN.";
        }
        $tinscripcion = $this->tinscripcion();
        if(session('message')) $message = session('message');
        return view('solicitud.apertura.index', compact('message','grupo','alumnos','plantel','depen','sector','programa','municipio','instructor','exoneracion','medio_virtual','tcurso','tinscripcion','tcuota'));
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
            $result = DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio'])->update(['turnado' => "VINCULACION",'fecha_turnado' => date('Y-m-d')]);
            //$_SESSION['folio'] = null;
           // unset($_SESSION['folio']);
           if($result){
                $message = "El grupo fué turnado correctamente a VINCULACIÓN";
                unset($_SESSION['folio']);
            }
        }
        return redirect('solicitud/apertura')->with('message',$message);
   }



   public function store(Request $request, \Illuminate\Validation\Factory $validate){
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

        if($_SESSION['folio'] AND $_SESSION['grupo'] AND $_SESSION['alumnos']){
                $horas = (strtotime($request->hfin)-strtotime($request->hini))/3600;
                if($request->tcurso == "CERTIFICACION" AND $horas==10 OR $request->tcurso == "CURSO"){
                    $grupo = $_SESSION['grupo'];   //var_dump($grupo);exit;
                    $alumnos = $_SESSION['alumnos'];   //var_dump($alumnos);exit;
                    $unidad = DB::table('tbl_unidades')->select('cct','plantel')->where('unidad',$grupo->unidad)->first();
                    $municipio = $cct = DB::table('tbl_municipios')->select('muni','ze')->where('id',$request->id_municipio)->first();
                    $hini = date("h:i a",strtotime($request->hini));
                    $hfin = date("h:i a",strtotime($request->hfin));
                    $hini = str_replace(['am','pm'],['a.m.','p.m.'],$hini);
                    $hfin = str_replace(['am','pm'],['a.m.','p.m.'],$hfin);

                    $instructor = DB::table('instructores')
                        ->select('instructores.id',DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'),
                        'curp','rfc','sexo','tipo_honorario','instructor_perfil.grado_profesional as escolaridad','instructor_perfil.estatus as titulo',
                        'especialidad_instructores.memorandum_validacion as mespecialidad','especialidad_instructores.criterio_pago_id as cp')
                        ->WHERE('estado',true)
                        ->WHERE('instructores.status', '=', 'Validado')->where('instructores.nombre','!=','')->where('instructores.id',$request->instructor)
                        //->whereJsonContains('unidades_disponible', [$grupo->unidad])
                        ->WHERE('especialidad_instructores.especialidad_id',$grupo->id_especialidad)
                        ->LEFTJOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
                        ->LEFTJOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
                        ->LEFTJOIN('criterio_pago', 'criterio_pago.id', '=', 'especialidad_instructores.criterio_pago_id')
                        ->first();
                   // var_dump($instructor);exit;

                    if($instructor){
                        //VALIDANDO INSTRUCTOR
                       $existe_instructor = DB::table('tbl_cursos')->where('folio_grupo','<>',$_SESSION['folio'])->where('curp', $instructor->curp)
                            ->where('inicio',$request->inicio)->where('termino',$request->termino)->where('hini',$hini)->where('hfin',$hfin)
                            ->where('dia', trim($request->dia))->where('status_curso','<>','CANCELADO')
                            ->exists();

                        if(!$existe_instructor){
                            /** CRITERIO DE PAGO */
                            if($instructor->cp > $grupo->cp)$cp = $grupo->cp;
                            else $cp = $instructor->cp;

                            /*CALCULANDO CICLO*/
                            $mes_dia1 = date("m-d",strtotime(date("Y-m-d")));
                            $mes_dia2 = date("m-d",strtotime(date("Y"). "-07-01"));

                            if($mes_dia1 >= $mes_dia2)  $ciclo = date("Y")."-".date("Y",strtotime(date("Y"). "+ 1 year"));//sumas año
                            else $ciclo = date("Y",strtotime(date("Y"). "- 1 year"))."-".date("Y"); //restar año

                            /*REGISTRANDO COSTO Y TIPO DE INSCRIPCION*/
                            $total_pago = 0;
                            $abrinscri = $this->abrinscri();
                            foreach($request->costo as $key=>$pago){

                                $diferencia = $grupo->costo_individual - $pago;
                                if($pago == 0 ) $tinscripcion = "EXONERACION TOTAL DE PAGO";
                                elseif($diferencia > 0) $tinscripcion = "EXONERACION PARCIAL DE PAGO";
                                else $tinscripcion = "PAGO DE INSCRIPCION";
                                $total_pago += $pago*1;
                                $abrins = $abrinscri[$tinscripcion];
                                Alumno::where('id',$key)->update(['costo' => $pago, 'tinscripcion' => $tinscripcion, 'abrinscri' => $abrins]);
                            }
                            /*CALCULANDO EL TIPO DE PAGO*/
                            $talumno = $grupo->hombre + $grupo->mujer;
                            $costo_total = $grupo->costo_individual * $talumno;
                            $ctotal = $costo_total - $total_pago;
                            if($total_pago == 0)$tipo_pago = "EXO";
                            elseif($ctotal > 0) $tipo_pago = "EPAR";
                            else $tipo_pago = "PINS";

                            /*RECALCULANDO TOTAL HOMBRES Y MUJERES*/
                            $hombres = $mujeres = 0;
                            $alumnos = json_decode(json_encode($alumnos), true);
                            $total_sexo = array_count_values(array_column($alumnos, 'sexo'));
                            if(count($total_sexo)>0){
                                if(isset($total_sexo['H']))$hombres = $total_sexo['H'];
                                if(isset($total_sexo['M']))$mujeres = $total_sexo['M'];
                            }


                            /*ID DEL CURSO DE 10 DIGITOS*/
                            $PRE = date("y").$unidad->plantel;
                            $ID = DB::table('tbl_cursos')->where('unidad',$grupo->unidad)->where('folio_grupo',$_SESSION['folio'])->value('id');
                            if(!$ID )$ID = DB::table('tbl_cursos')->where('unidad',$grupo->unidad)->where('id','like',$PRE.'%')->value(DB::raw('max(id)+1'));
                            if(!$ID) $ID = $PRE.'0001';
                            if($request->cespecifico) $cespecifico = strtoupper($request->cespecifico);
                            else $cespecifico = 0;

                            if($request->tcurso=="CERTIFICACION"){
                                $horas = $dura = 10;
                                $termino =  $request->inicio;
                            }else{
                                $dura = $grupo->dura;
                                $termino =  $request->termino;
                            }

                            if(!$request->cespecifico) $request->cespecifico = 0;
                            if(!$request->mexoneracion) $request->mexoneracion = 0;
                            if(!$request->cgeneral) $request->cgeneral = 0;

                            //$result = tbl_curso::updateOrCreate(
                            $result =  DB::table('tbl_cursos')->where('clave','0')->updateOrInsert(
                                ['folio_grupo' => $_SESSION['folio']],
                                ['id'=>$ID, 'cct' => $unidad->cct,
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
                                'tdias' => $request->tdias,
                                'dia' => $request->dia,
                                'dura' => $dura,
                                'hini' => $hini,
                                'hfin' => $hfin,
                                'horas' => $horas,
                                'ciclo' => $ciclo,
                                'plantel' => $request->plantel,
                                'depen' => $request->depen,
                                'muni' => $municipio->muni,
                                'sector' => $request->sector,
                                'programa' => $request->programa,
                                'nota' => strtoupper($request->observaciones),
                                'munidad' => $request->munidad,
                                'efisico' => strtoupper($request->efisico),
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
                                'modinstructor' => $instructor->tipo_honorario,
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
                                'id_municipio' => $request->id_municipio,
                                'id_cerss' => $grupo->id_cerss,
                                'created_at'=>date('Y-m-d H:i:s')
                            ]
                        );
                        if($result)$message = 'Operación Exitosa!!';
                    }else $message = "El instructor no se encuentra disponible en el horario y fecha requerido.";
                }else $message = 'Instructor no válido.';

            }else $message  = "Si es una CERTIFICACIÓN, corrobore que cubra 10 horas.";

        }
        return redirect('solicitud/apertura')->with('message',$message);
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
                    $abrinscriTMP = $abrinscri[$tinscripcion];
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
                        'curp'=> $a->curp
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
}
