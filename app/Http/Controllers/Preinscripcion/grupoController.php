<?php

namespace App\Http\Controllers\Preinscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tbl_grupos;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Models\cat\catUnidades;
use App\Models\cat\catApertura;
use App\Models\Alumno;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\Storage;
use PDF;
use App\Agenda; 
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\QueryException;

use function PHPSTORM_META\type;

class grupoController extends Controller
{
    use catUnidades;
    use catApertura;
    function __construct()
    {
        session_start();
        $this->ejercicio = date("y");
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->id_user = Auth::user()->id;
            $this->realizo = Auth::user()->name;
            $this->id_unidad = Auth::user()->unidad;
            $this->path_files = env("APP_URL").'/storage/uploadFiles';

            $this->data = $this->unidades_user('vincula');  //vincula
            $_SESSION['unidades'] =  $this->data['unidades'];

            return $next($request);
        });
    }

    public function index(Request $request)
    {

        $curso = $cursos = $localidad  = $alumnos = $instructores = $instructor = [];
        $es_vulnerable = $edicion = false;
        $unidades = $this->data['unidades'];
        $unidad = $uni = $this->data['unidad'];
        $message = $comprobante = $folio_pago = $fecha_pago = $grupo = NULL;
        if (isset($_SESSION['folio_grupo'])) {  //echo $_SESSION['folio_grupo'];exit;
            $anio_hoy = date('y');  //dd($_SESSION);
            $alumnos = DB::table('alumnos_registro as ar')
            ->select('ar.id as id_reg','ar.no_control','ar.turnado','ar.nombre','ar.apellido_paterno','ar.apellido_materno','ar.id_curso','ar.mod','ar.tipo_curso','ar.id_cerss',
                'ar.horario','ar.inicio','ar.termino','ar.costo','ar.id_muni','ar.clave_localidad','ar.organismo_publico','ar.id_organismo','ar.grupo_vulnerable','ar.id_vulnerable',
                'ap.ultimo_grado_estudios','ar.tinscripcion','ar.unidad','ar.folio_grupo','ar.curp','ar.comprobante_pago','ar.folio_pago','ar.fecha_pago','ap.requisitos',
                'ap.documento_curp','ap.id_gvulnerable',DB::raw("substring(ar.curp,11,1) as sex"),DB::raw("CASE WHEN substring(ar.curp,5,2) <='" . $anio_hoy . "'
                THEN CONCAT('20',substring(ar.curp,5,2),'-',substring(ar.curp,7,2),'-',substring(ar.curp,9,2))
                ELSE CONCAT('19',substring(ar.curp,5,2),'-',substring(ar.curp,7,2),'-',substring(ar.curp,9,2))
                END AS fnacimiento"),'ar.id_especialidad','ar.id_instructor','ar.efisico','ar.escolaridad','ar.servicio','ar.medio_virtual','ar.link_virtual','ar.cespecifico',
                'ar.fcespe','ar.observaciones','ar.mpreapertura','ar.depen_repre','ar.depen_telrepre')
            ->join('alumnos_pre as ap', 'ap.id', 'ar.id_pre')->where('ar.folio_grupo', $_SESSION['folio_grupo'])->where('ar.eliminado', false)
            ->orderBy('apellido_paterno','ASC')->orderby('apellido_materno','ASC')->orderby('nombre','ASC')->get();
            //var_dump($alumnos);exit;
            if (count($alumnos) > 0) {
                foreach ($alumnos as $value) {
                    if ($value->id_gvulnerable != '[]') {
                        $es_vulnerable = true;
                    }
                }
                $tipo = $alumnos[0]->tipo_curso;
                $mod = $alumnos[0]->mod;
                $folio_pago = $alumnos[0]->folio_pago;
                $fecha_pago = $alumnos[0]->fecha_pago;
                $uni = $alumnos[0]->unidad;
                if($alumnos[0]->comprobante_pago)$comprobante = $this->path_files.$alumnos[0]->comprobante_pago;
                if ($alumnos[0]->turnado == 'VINCULACION' and isset($this->data['cct_unidad'])) $this->activar = true;
                else $this->activar = false;

                $curso = DB::table('cursos')->where('id', $alumnos[0]->id_curso)->where('cursos.estado', true)->first();
                $clave = DB::table('tbl_municipios')->where('id', $alumnos[0]->id_muni)->value('clave');
                $localidad = DB::table('tbl_localidades')->where('clave_municipio', '=', $clave)->pluck('localidad', 'clave');
                $cursos = DB::table('cursos')
                    ->where('tipo_curso','like',"%$tipo%")
                    ->where('cursos.estado', true)
                    ->where('modalidad','like',"%$mod%")
                    ->whereJsonContains('unidades_disponible', [$alumnos[0]->unidad])->orderby('cursos.nombre_curso')->pluck('nombre_curso', 'cursos.id');
                $edicion = DB::table('exoneraciones')->where('folio_grupo',$_SESSION['folio_grupo'])->where('status','EDICION')->exists();
                $instructores = DB::table(DB::raw('(select id_instructor, id_curso from agenda group by id_instructor, id_curso) as t'))
                    ->select(DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'),'instructores.id', DB::raw('count(id_curso) as total'))
                    ->rightJoin('instructores','t.id_instructor','=','instructores.id')
                    ->JOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
                    ->JOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
                    ->JOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
                    //->join('especialidad_instructor_curso','especialidad_instructor_curso.id_especialidad_instructor','=','especialidad_instructores.id')
                    ->WHERE('estado',true)
                    ->WHERE('instructores.status', '=', 'VALIDADO')->where('instructores.nombre','!=','')
                    ->WHERE('especialidad_instructores.especialidad_id',$alumnos[0]->id_especialidad)
                    //->where('especialidad_instructor_curso.curso_id',$grupo->id_curso)
                    //->where('especialidad_instructor_curso.activo', true)
                    ->WHERE('fecha_validacion','<',$alumnos[0]->inicio)
                    ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',$alumnos[0]->termino)
                    ->groupBy('t.id_instructor','instructores.id')
                    ->orderBy('instructor')
                    ->get();
                $instructor = DB::table('instructores')->select('id',DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'))->where('id',$alumnos[0]->id_instructor)->first();
                $grupo = DB::table('tbl_cursos')->where('folio_grupo',$_SESSION['folio_grupo'])->first();
            } else {
                $message = "No hay registro qwue mostrar para Grupo No." . $_SESSION['folio_grupo'];
                $_SESSION['folio_grupo'] = NULL;
                $this->activar = true;
            }
        } else {
            $_SESSION['folio_grupo'] = NULL;
            $this->activar = true;
        }

        $cerss = DB::table('cerss');
        if ($unidad) $cerss = $cerss->where('id_unidad', $this->id_unidad)->where('activo', true);
        $cerss = $cerss->orderby('nombre', 'ASC')->pluck('nombre', 'id');
        $folio_grupo =  $_SESSION['folio_grupo'];
        $activar = $this->activar;
        $municipio = DB::table('tbl_municipios')->where('id_estado', '7')->whereJsonContains('unidad_disponible',$uni)->orderby('muni')->pluck('muni', 'id');
        $dependencia = DB::table('organismos_publicos')
            ->where('activo', true)
            ->orderby('organismo')
            ->pluck('organismo', 'organismo');
        $grupo_vulnerable = DB::table('grupos_vulnerables')->orderBy('grupo')->pluck('grupo','id');
        $medio_virtual = $this->medio_virtual();
        if (session('message')) $message = session('message');
        $tinscripcion = $this->tinscripcion();
        return view('preinscripcion.index', compact('cursos', 'alumnos', 'unidades', 'cerss', 'unidad', 'folio_grupo', 'curso', 'activar', 'folio_pago', 'fecha_pago',
            'es_vulnerable', 'message', 'tinscripcion', 'municipio', 'dependencia', 'localidad','grupo_vulnerable','comprobante','edicion','instructores','instructor',
            'medio_virtual','grupo'));
    }


    public function cmbcursos(Request $request)
    {
        //$request->unidad = 'TUXTLA';
        if (isset($request->tipo) and isset($request->unidad) and isset($request->modalidad)) {
            $cursos = DB::table('cursos')->select('cursos.id', 'nombre_curso')
                ->where('tipo_curso','like',"%$request->tipo%")
                ->where('modalidad','like',"%$request->modalidad%")
                ->where('cursos.estado', true)
                ->whereJsonContains('unidades_disponible', [$request->unidad])->orderby('cursos.nombre_curso')->get();
            $json = json_encode($cursos);
            //var_dump($json);exit;
        } else {
            $json = json_encode(["No hay registros que mostrar."]);
        }

        return $json;
    }

    public function cmbmuni(Request $request){
        if (isset($request->uni)) {
            $municipio = DB::table('tbl_municipios')->select('muni','id')->where('id_estado', '7')->whereJsonContains('unidad_disponible',$request->uni)->orderby('muni')->get();
            $json = json_encode($municipio);
        } else {
            $json = json_encode(["No hay registros que mostrar!"]);
        }
        return $json;
    }

    public function cmbrepre(Request $request){
        if (isset($request->depen)) {
            $depen = DB::table('organismos_publicos')->select(DB::raw("REPLACE(UPPER(nombre_titular),'ñ','Ñ') as nombre_titular"),'telefono')->where('organismo', $request->depen)->where('activo', true)->first();
            $json = json_encode($depen);
        } else {
            $json = json_encode(["No hay registros que mostrar!"]);
        }
        return $json;
    }

    public function save(Request $request)
    {
        $curp = $request->busqueda;    //dd($request->all());
        $matricula = $message = NULL;
        $horas = round((strtotime($request->hfin) - strtotime($request->hini)) / 3600, 2);
        
        //VALIDACIÓN DE INSTRUCTOR EN OBSERVACIÓN 
        $instructor_valido = $this->valida_instructor($request->instructor);
        if(!$instructor_valido['valido'])  return redirect()->route('preinscripcion.grupo')->with(['message' => $instructor_valido['message']]); 

        if ($request->tcurso == "CERTIFICACION" and $horas == 10 or $request->tcurso == "CURSO") {
            if ($curp) {
                $date = date('d-m-Y');
                $alumno = DB::table('alumnos_pre')
                    ->select('id as id_pre', 'matricula', DB::raw("cast(EXTRACT(year from(age('$date', fecha_nacimiento))) as integer) as edad"),'ultimo_grado_estudios as escolaridad',
                    'nombre','apellido_paterno','apellido_materno')
                    ->where('curp', $curp)->where('activo', true)->first(); //dd($alumno);
                if ($alumno) {
                    if ($alumno->escolaridad AND ($alumno->escolaridad != ' ')) {
                        if ($alumno->edad >= 15) {
                            $cursos = DB::table(DB::raw("(select a.id_curso as curso from alumnos_registro as a
                                                            inner join alumnos_pre as ap on a.id_pre = ap.id
                                                            where ap.curp = '$curp'
                                                               and a.eliminado = false
                                                            and extract(year from a.inicio) = extract(year from current_date)) as t"))
                                ->select(DB::raw("count(curso) as total"), DB::raw("count(case when curso = '$request->id_curso' then curso end) as igual"))
                                ->first(); //dd($cursos);
                            if ($cursos->igual < 2 && $cursos->total < 15) {
                                if($_SESSION['folio_grupo'] AND DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->where('turnado','<>','VINCULACION')->exists() == true) $_SESSION['folio_grupo'] = NULL;
                                if(!$_SESSION['folio_grupo'] AND $alumno) $_SESSION['folio_grupo'] =$this->genera_folio();
                                //EXTRAER MATRICULA Y GUARDAR
                                $matricula_sice = DB::table('registro_alumnos_sice')->where('eliminado', false)->where('curp', $curp)->value('no_control');
        
                                if ($matricula_sice) {
                                    $matricula = $matricula_sice;
                                    DB::table('registro_alumnos_sice')->where('curp', $curp)->update(['eliminado' => true]);
                                } elseif (isset($alumno->matricula)) $matricula  =  $alumno->matricula;
                                //FIN MATRICULA
                                if (DB::table('exoneraciones')->where('folio_grupo',$_SESSION['folio_grupo'])->where('status','!=', 'CAPTURA')->where('status','!=','CANCELADO')->exists()) {
                                    $message = "Solicitud de Exoneración o Reducción de couta en Proceso..";
                                    return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
                                }
                                $a_reg = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->where('eliminado',false)->first();
                                if ($a_reg) {
                                    $id_especialidad = $a_reg->id_especialidad;
                                    $id_unidad = $a_reg->id_unidad;
                                    $unidad = $a_reg->unidad;
                                    $id_curso = $a_reg->id_curso;
                                    $horario = $a_reg->horario;
                                    $inicio = $a_reg->inicio;
                                    $termino = $a_reg->termino;
                                    $tipo = $a_reg->tipo_curso;
                                    $id_cerss = $a_reg->id_cerss;
                                    $id_muni = $a_reg->id_muni;
                                    $clave_localidad = $a_reg->clave_localidad;
                                    $organismo = $a_reg->organismo_publico;
                                    $id_organismo = $a_reg->id_organismo;
                                    $grupo_vulnerable = $a_reg->grupo_vulnerable;
                                    $id_vulnerable = $a_reg->id_vulnerable;
                                    $comprobante_pago = $a_reg->comprobante_pago;
                                    $modalidad = $a_reg->mod;
                                    $folio_pago = $a_reg->folio_pago;
                                    $fecha_pago =  $a_reg->fecha_pago;
                                    $instructor = $a_reg->id_instructor;
                                    $efisico = $a_reg->efisico;
                                    $medio_virtual = $a_reg->medio_virtual;
                                    $link_virtual = $a_reg->link_virtual;
                                    $servicio = $a_reg->servicio;
                                    $cespecifico = $a_reg->cespecifico;
                                    $fcespe = $a_reg->fcespe;
                                    $observaciones = $a_reg->observaciones;
                                    $depen_repre = $a_reg->depen_repre;
                                    $depen_telrepre = $a_reg->depen_telrepre;
                                } else {
                                    $id_especialidad = DB::table('cursos')->where('estado', true)->where('id', $request->id_curso)->value('id_especialidad');
                                    $id_unidad = DB::table('tbl_unidades')->select('id', 'plantel')->where('unidad', $request->unidad)->value('id');
                                    $unidad = $request->unidad;
                                    $id_curso = $request->id_curso;
                                    $horario= $request->hini.' A '.$request->hfin;
                                    $inicio = $request->inicio;
                                    $termino = $request->termino;
                                    $tipo = $request->tipo;
                                    $id_cerss = $request->cerss;
                                    $id_muni = $request->id_municipio;
                                    $clave_localidad = $request->localidad;
                                    $organismo = $request->dependencia;
                                    $id_organismo = DB::table('organismos_publicos')->where('organismo',$request->dependencia)->where('activo', true)->value('id');
                                    $grupo_vulnerable = DB::table('grupos_vulnerables')->where('id',$request->grupo_vulnerable)->value('grupo');
                                    $id_vulnerable = $request->grupo_vulnerable;
                                    $comprobante_pago = null;
                                    $modalidad = $request->modalidad;
                                    $folio_pago = $request->folio_pago;
                                    $fecha_pago =  $request->fecha_pago;
                                    $instructor = $request->instructor;
                                    $efisico = str_replace('ñ','Ñ',strtoupper($request->efisico));
                                    $medio_virtual = $request->medio_virtual;
                                    $link_virtual = $request->link_virtual;
                                    $servicio = $request->tcurso;
                                    $cespecifico = $request->cespecifico;
                                    $fcespe = $request->fcespe;
                                    $observaciones = str_replace('ñ','Ñ',strtoupper($request->observaciones));
                                    if (($id_organismo == 358) OR ($modalidad=='EXT')) {
                                        $depen_repre = $request->repre_depen;
                                        $depen_telrepre = $request->repre_tel;
                                    } else {
                                        $depen_repre = DB::table('organismos_publicos')->where('organismo',$request->dependencia)->where('activo', true)->value('nombre_titular');
                                        $depen_telrepre = DB::table('organismos_publicos')->where('organismo',$request->dependencia)->where('activo', true)->value('telefono');
                                    }
                                }
                                if ($id_cerss) $cerrs = true;
                                else $cerrs = NULL;
                                if ($_SESSION['folio_grupo']) {
                                    if ((((explode('-',$inicio))[0]) == date('Y')) AND ((explode('-',$termino))[0]) == date('Y')) {
                                        if ($inicio <= $termino) {
                                                $result = DB::table('alumnos_registro')->UpdateOrInsert(
                                                    ['id_pre' => $alumno->id_pre, 'folio_grupo' => $_SESSION['folio_grupo']],
                                                    [
                                                        'id_unidad' =>  $id_unidad, 'id_curso' => $id_curso, 'id_especialidad' =>  $id_especialidad, 'organismo_publico' => $organismo, 'id_organismo'=>$id_organismo,
                                                        'horario'=>$horario, 'inicio' => $inicio, 'termino' => $termino, 'unidad' => $unidad, 'tipo_curso' => $tipo, 'clave_localidad' => $clave_localidad,
                                                        'cct' => $this->data['cct_unidad'], 'realizo' => $this->realizo, 'no_control' => $matricula, 'ejercicio' => $this->ejercicio, 'id_muni' => $id_muni,
                                                        'folio_grupo' => $_SESSION['folio_grupo'], 'iduser_created' => $this->id_user, 'comprobante_pago' => $comprobante_pago,
                                                        'created_at' => date('Y-m-d H:i:s'), 'fecha' => date('Y-m-d'), 'id_cerss' => $id_cerss, 'cerrs' => $cerrs, 'mod' => $modalidad,
                                                        'grupo' => $_SESSION['folio_grupo'], 'eliminado' => false, 'grupo_vulnerable' => $grupo_vulnerable, 'id_vulnerable' => $id_vulnerable,
                                                        'folio_pago'=>$folio_pago, 'fecha_pago'=>$fecha_pago, 'nombre'=>$alumno->nombre, 'apellido_paterno'=>$alumno->apellido_paterno, 
                                                        'apellido_materno'=>$alumno->apellido_materno,'curp'=>$curp,'escolaridad'=>$alumno->escolaridad,
                                                        'id_instructor'=>$instructor,'efisico'=>$efisico,'medio_virtual'=>$medio_virtual,'link_virtual'=>$link_virtual,'servicio'=>$servicio,'cespecifico'=>$cespecifico,
                                                        'fcespe'=>$fcespe, 'observaciones'=>$observaciones, 'depen_repre'=>$depen_repre, 'depen_telrepre'=>$depen_telrepre
                                                    ]
                                                );
                                                if ($result) $message = "Operación Exitosa!!";
                                        } else {
                                            $message = 'La fecha de termino no puede ser menor a la de inicio';
                                        }
                                    } else {
                                        $message = 'El año de la fecha de inicio o de termino no coincide con el actual';
                                    }
                                } else $message = "Operación no permitida!";
                            } else {
                                $message = "El alumno excede con el limte de cursos que puede tomar";
                            }
                        } else {
                            $message = "La edad del alumno no es valida";
                        }
                    } else {
                        $message = "Ingrese la escolaridad al Alumno " . $curp . ".";
                    }
                    
                } else {
                    $message = "Alumno no registrado " . $curp . ".";
                }
            } else $message = "Ingrese la CURP";
        } else {
            $message  = "Si es una CERTIFICACIÓN, corrobore que cubra 10 horas.";
        }
        //dd($_SESSION['folio_grupo']);
        return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
    }

    public function update(Request $request)
    {
        //dd($request->all());
        if ($_SESSION['folio_grupo']) {
            ///VALIDA INSTRUCTOR
            $instructor_valido = $this->valida_instructor($request->instructor);
            if(!$instructor_valido['valido'])  return redirect()->route('preinscripcion.grupo')->with(['message' => $instructor_valido['message']]); ;


            $horas = round((strtotime($request->hfin) - strtotime($request->hini)) / 3600, 2);
            if ($request->tcurso == "CERTIFICACION" and $horas == 10 or $request->tcurso == "CURSO") {
                if ((((explode('-',$request->inicio))[0]) == date('Y')) AND ((explode('-',$request->termino))[0]) == date('Y')) {
                    if ($request->inicio <= $request->termino) {
                        $folio = $_SESSION['folio_grupo'];
                        $mapertura = $request->mapertura;
                        if ($mapertura AND (DB::table('alumnos_registro')->where('mpreapertura',$mapertura)->where('turnado','<>','VINCULACION')->exists())) {
                            $message = 'Número de memorándum de apertura ocupado..';
                        } else {
                            $file =  $request->customFile;
                            $url_comprobante = DB::table('alumnos_registro')->select('comprobante_pago')->where('folio_grupo', $folio)->where('eliminado',false)->first();
                            if ($file) {
                                $url_comprobante = $this->uploaded_file($file, $folio, 'comprobante_pago');
                            } elseif ($url_comprobante->comprobante_pago != null) {
                                $url_comprobante = $url_comprobante->comprobante_pago;
                            } else {
                                $url_comprobante = null;
                            }
                            $id_especialidad = DB::table('cursos')->where('estado', true)->where('id', $request->id_curso)->value('id_especialidad');
                            $costo_individual = DB::table('cursos')->where('estado', true)->where('id', $request->id_curso)->value('costo');
                            $horario = $request->hini . ' A ' . $request->hfin;
                            $id_organismo = DB::table('organismos_publicos')->where('organismo', $request->dependencia)->where('activo', true)->value('id');
                            if (($id_organismo == 358) OR ($request->modalidad=='EXT')) {
                                $depen_repre = $request->repre_depen;
                                $depen_telrepre = $request->repre_tel;
                            } else {
                                $depen_repre = DB::table('organismos_publicos')->where('organismo',$request->dependencia)->where('activo', true)->value('nombre_titular');
                                $depen_telrepre = DB::table('organismos_publicos')->where('organismo',$request->dependencia)->where('activo', true)->value('telefono');
                            }
                            $convenio = null;
                            if($request->dependencia AND $request->modalidad=='CAE'){
                                $organismo = DB::table('organismos_publicos')->where('id',$id_organismo)->value('organismo');
                                $convenio_t = DB::table('convenios')
                                    ->select('no_convenio',db::raw("to_char(DATE (fecha_firma)::date, 'YYYY-MM-DD') as fecha_firma"))
                                    ->where(db::raw("to_char(DATE (fecha_vigencia)::date, 'YYYY-MM-DD')"),'>=',$request->termino)
                                    ->where('institucion',$organismo)
                                    ->where('activo','true')->first();
                                $convenio = [];
                                if ($convenio_t) {
                                    foreach ($convenio_t as $key=>$value) {
                                        $convenio[$key] = $value;
                                    }
                                }else {
                                    $convenio['no_convenio'] = '0';
                                    $convenio['fecha_firma'] = null;
                                    $convenio['sector'] = null;
                                }
                            }
                            if(!$convenio){
                                $convenio['no_convenio'] = '0';
                                $convenio['fecha_firma'] = null;
                                $convenio['sector'] = null;
                            }
                            $sector = DB::table('organismos_publicos')->where('id',$id_organismo)->value('sector');
                            $grupo_vulnerable = DB::table('grupos_vulnerables')->where('id', $request->grupo_vulnerable)->value('grupo');
                            if ($request->cerss) $cerrs = true;
                            else $cerrs = NULL;
                            //novo
                            $unidad = DB::table('tbl_unidades')->select('id','cct', 'plantel')->where('unidad', $request->unidad)->first();
                            $municipio = DB::table('tbl_municipios')->select('id','muni','ze')->where('id', $request->id_municipio)->first();
                            $curso = DB::table('cursos as c')->select('c.id','c.nombre_curso','c.horas','c.rango_criterio_pago_maximo as cp','c.costo','e.nombre as espe',
                                'a.formacion_profesional as area','c.memo_validacion as mpaqueteria','e.clave as clave_especialidad')
                                ->join('especialidades as e','e.id','c.id_especialidad') ->join('area as a','a.id','c.area')
                                ->where('c.id',$request->id_curso)->first();
                            $hini = date("h:i a", strtotime($request->hini));
                            $hfin = date("h:i a", strtotime($request->hfin));
                            $hini = str_replace(['am', 'pm'], ['a.m.', 'p.m.'], $hini);
                            $hfin = str_replace(['am', 'pm'], ['a.m.', 'p.m.'], $hfin);
                            $instructor = DB::table('instructores')->select(
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
                                    'folio_ine')
                                ->WHERE('estado', true)
                                ->WHERE('instructores.status', '=', 'VALIDADO')->where('instructores.nombre', '!=', '')->where('instructores.id', $request->instructor)
                                //->whereJsonContains('unidades_disponible', [$grupo->unidad])
                                ->WHERE('especialidad_instructores.especialidad_id', $id_especialidad)
                                ->WHERE('especialidad_instructores.activo', 'true')
                                ->WHERE('fecha_validacion','<',$request->inicio)
                                ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',$request->termino)
                                ->LEFTJOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
                                ->LEFTJOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
                                ->LEFTJOIN('criterio_pago', 'criterio_pago.id', '=', 'especialidad_instructores.criterio_pago_id')
                                ->ORDERBY('fecha_validacion','DESC')
                                ->first();
                            if ($instructor) {
                                /** CRITERIO DE PAGO */
                                if ($instructor->cp > $curso->cp) {
                                    $cp = $curso->cp;
                                } else {
                                    $cp = $instructor->cp;
                                }
                                /*CALCULANDO CICLO*/
                                $mes_dia1 = date("m-d", strtotime(date("Y-m-d")));
                                $mes_dia2 = date("m-d", strtotime(date("Y") . "-07-01"));

                                if ($mes_dia1 >= $mes_dia2)  $ciclo = date("Y") . "-" . date("Y", strtotime(date("Y") . "+ 1 year")); //sumas año
                                else $ciclo = date("Y", strtotime(date("Y") . "- 1 year")) . "-" . date("Y"); //restar año

                                /*REGISTRANDO COSTO Y TIPO DE INSCRIPCION*/
                                $total_pago = 0;
                                $sx = DB::table('alumnos_registro')->select(DB::raw("COUNT(curp) as total"),DB::raw("SUM(CASE WHEN substring(curp,11,1) ='H' THEN 1 ELSE 0 END) as hombre"),DB::raw("SUM(CASE WHEN substring(curp,11,1) ='M' THEN 1 ELSE 0 END) as mujer"))->where('folio_grupo',$_SESSION['folio_grupo'])->first();
                                foreach ($request->costo as $key => $pago) {
                                    if (!$pago) {
                                        $pago = 0;
                                    }
                                    $diferencia = $costo_individual - $pago;
                                    if ($pago == 0) {
                                        $tinscripcion = "EXONERACION";
                                        $abrins = 'ET';
                                    } elseif ($diferencia > 0) {
                                        $tinscripcion = "REDUCCION DE CUOTA";
                                        $abrins = 'EP';
                                    } else {
                                        $tinscripcion = "PAGO ORDINARIO";
                                        $abrins = 'PI';
                                    }
                                    if (!(DB::table('exoneraciones')->where('folio_grupo',$_SESSION['folio_grupo'])->where('status','!=', 'CAPTURA')->where('status','!=','CANCELADO')->exists())) {
                                        Alumno::where('id', $key)->update(['costo' => $pago, 'tinscripcion' => $tinscripcion, 'abrinscri' => $abrins]);
                                    }
                                    $total_pago += $pago * 1;
                                }
                                $costo_total = $curso->costo * $sx->total;
                                $ctotal = $costo_total - $total_pago;
                                if ($total_pago == 0) {
                                    $tipo_pago = "EXO";
                                    if ($cp > 7) $cp = 7; //EXONERACION Criterio de Pago Máximo 7
                                } elseif ($ctotal > 0) $tipo_pago = "EPAR";
                                else $tipo_pago = "PINS";
                                /*ID DEL CURSO DE 10 DIGITOS*/
                                $PRE = date("y") . $unidad->plantel;
                                $ID = DB::table('tbl_cursos')->where('unidad', $request->unidad)->where('folio_grupo', $_SESSION['folio_grupo'])->value('id');
                                if (!$ID) $ID = DB::table('tbl_cursos')->where('unidad', $request->unidad)->where('id', 'like', $PRE . '%')->value(DB::raw('max(id)+1'));
                                if (!$ID) $ID = $PRE . '0001';
                                if ($request->tcurso == "CERTIFICACION") {
                                    $horas = $dura = 10;
                                    $termino =  $request->inicio;
                                } else {
                                    $dura = $curso->horas;
                                    $termino =  $request->termino;
                                }
                                $created_at = DB::table('tbl_cursos')->where('unidad', $request->unidad)->where('folio_grupo', $_SESSION['folio_grupo'])->value('created_at');
                                if ($created_at) {
                                    $updated_at = date('Y-m-d H:i:s');
                                } else {
                                    $created_at = date('Y-m-d H:i:s');
                                    $updated_at = date('Y-m-d H:i:s');
                                }
                                if ($instructor->tipo_honorario == 'ASIMILADOS A SALARIOS') {
                                    $tipo_honorario = 'ASIMILADOS A SALARIOS';
                                } else {
                                    $tipo_honorario = 'HONORARIOS';
                                }
                                if (DB::table('exoneraciones')->where('folio_grupo',$_SESSION['folio_grupo'])->where('status','!=', 'CAPTURA')->where('status','!=','CANCELADO')->exists()) {
                                    $result = DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->where('turnado','VINCULACION')->update(
                                        ['observaciones'=>$request->observaciones,'updated_at' => date('Y-m-d H:i:s'), 'iduser_updated' => $this->id_user, 'comprobante_pago' => $url_comprobante, 
                                        'folio_pago'=>$request->folio_pago, 'fecha_pago'=>$request->fecha_pago,'mpreapertura'=>$mapertura,'depen_repre'=>$depen_repre, 'depen_telrepre'=>$depen_telrepre,
                                        'cespecifico'=>$request->cespecifico,'fcespe'=>$request->fcespe,'medio_virtual' => $request->medio_virtual,'link_virtual' => $request->link_virtual]);
                                    if ($result) {
                                        $message = "Operación Exitosa!!";
                                        DB::table('tbl_cursos')->where('folio_grupo',$_SESSION['folio_grupo'])->where('id',$ID)->update(['comprobante_pago' => $url_comprobante,
                                            'folio_pago' => $request->folio_pago,'fecha_pago' => $request->fecha_pago, 'updated_at' => date('Y-m-d H:i:s'),
                                            'depen_representante'=>$depen_repre,'depen_telrepre'=>$depen_telrepre,'cespecifico' => $request->cespecifico,'fcespe' => $request->fcespe,
                                            'medio_virtual' => $request->medio_virtual,'link_virtual' => $request->link_virtual]);
                                    }
                                } else {
                                    $alus = DB::table('alumnos_registro')->where('folio_grupo',$folio)->first();
                                    $result = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->where('turnado','VINCULACION')->Update(
                                        [
                                            'id_unidad' =>  $unidad->id, 'id_curso' => $request->id_curso, 'clave_localidad' => $request->localidad, 'organismo_publico' => $request->dependencia,
                                            'id_especialidad' =>  $id_especialidad, 'horario' => $horario, 'unidad' => $request->unidad, 'tipo_curso' => $request->tipo, 'mod' => $request->modalidad,
                                            'iduser_updated' => $this->id_user, 'updated_at' => date('Y-m-d H:i:s'), 'fecha' => date('Y-m-d'), 'id_muni' => $municipio->id,
                                            'inicio' => $request->inicio, 'termino' => $termino, 'id_organismo' => $id_organismo, 'id_vulnerable' => $request->grupo_vulnerable,
                                            'id_cerss' => $request->cerss, 'cerrs' => $cerrs, 'id_muni' => $municipio->id, 'grupo_vulnerable' => $grupo_vulnerable, 'comprobante_pago' => $url_comprobante,
                                            'folio_pago'=>$request->folio_pago, 'fecha_pago'=>$request->fecha_pago, 'servicio'=>$request->tcurso, 'medio_virtual' => $request->medio_virtual,
                                            'link_virtual' => $request->link_virtual, 'efisico'=>str_replace('ñ','Ñ',strtoupper($request->efisico)),'id_instructor'=>$instructor->id,'cespecifico'=>$request->cespecifico,'fcespe'=>$request->fcespe,
                                            'observaciones'=>$request->observaciones, 'mpreapertura'=>$mapertura, 'depen_repre'=>$depen_repre, 'depen_telrepre'=>$depen_telrepre
                                        ]
                                    );
                                    if ($result) {
                                        $message = "Operación Exitosa!!";
                                        //Si hay cambios y esta registrado en tbl_cursos se elimina el instructor para validarlo nuevamente
                                        // DB::table('tbl_cursos')->where('folio_grupo', $_SESSION['folio_grupo'])->where('clave', '0')->update(['nombre' => null, 'curp' => null, 'rfc' => null]);
                                        $result2 = DB::table('tbl_cursos')->where('clave', '0')->updateOrInsert(['folio_grupo' => $_SESSION['folio_grupo']],
                                            ['id' => $ID, 'cct' => $unidad->cct,'unidad' => $request->unidad,'nombre' => $instructor->instructor,'curp' => $instructor->curp,
                                            'rfc' => $instructor->rfc,'clave' => '0','mvalida' => '0','mod' => $request->modalidad,'area' => $curso->area,'espe' => $curso->espe,'curso' => $curso->nombre_curso,
                                            'inicio' => $request->inicio,'termino' => $termino,'dura' => $dura,'hini' => $hini,'hfin' => $hfin,'horas' => $horas,'ciclo' => $ciclo,
                                            'plantel' => null,'depen' => $request->dependencia,'muni' => $municipio->muni,'sector' => $sector,'programa' => null,'nota' => null,'munidad' => null,
                                            'efisico' => str_replace('ñ','Ñ',strtoupper($request->efisico)),'cespecifico' => $request->cespecifico,'mpaqueteria' => $curso->mpaqueteria,'mexoneracion' => null,'hombre' => $sx->hombre,
                                            'mujer' => $sx->mujer,'tipo' => $tipo_pago,'fcespe' => $request->fcespe,'cgeneral' => $convenio['no_convenio'],'fcgen' => $convenio['fecha_firma'],'opcion' => 'NINGUNO','motivo' => 'NINGUNO',
                                            'cp' => $cp,'ze' => $municipio->ze,'id_curso' => $curso->id,'id_instructor' => $instructor->id,'modinstructor' => $tipo_honorario,
                                            'nmunidad' => '0','nmacademico' => '0','observaciones' => 'NINGUNO','status' => "NO REPORTADO",'realizo' => strtoupper($this->realizo),
                                            'valido' => 'SIN VALIDAR','arc' => '01','tcapacitacion' => $request->tipo,'status_curso' => null,'fecha_apertura' => null,
                                            'fecha_modificacion' => null,'costo' => $total_pago,'motivo_correccion' => null,'pdf_curso' => null,'turnado' => "UNIDAD",
                                            'fecha_turnado' => null,'tipo_curso' => $request->tcurso,'clave_especialidad' => $curso->clave_especialidad,'id_especialidad' => $id_especialidad,
                                            'instructor_escolaridad' => $instructor->escolaridad,'instructor_titulo' => $instructor->titulo,'instructor_sexo' => $instructor->sexo,
                                            'instructor_mespecialidad' => $instructor->mespecialidad,'medio_virtual' => $request->medio_virtual,'link_virtual' => $request->link_virtual,
                                            'id_municipio' => $municipio->id,'clave_localidad' => $request->localidad,'id_gvulnerable' => $request->grupo_vulnerable,
                                            'id_cerss' => $request->cerss,'created_at' => $created_at,'updated_at' => $updated_at,'num_revision' => null,
                                            'instructor_tipo_identificacion' => $instructor->tipo_identificacion,'instructor_folio_identificacion' => $instructor->folio_ine,
                                            'comprobante_pago' => $url_comprobante,'folio_pago' => $request->folio_pago,'fecha_pago' => $request->fecha_pago,'depen_representante'=>$depen_repre,
                                            'depen_telrepre'=>$depen_telrepre,'nplantel'=>$unidad->plantel
                                            ]
                                        );
                                        if (($horario <> $alus->horario) OR ($request->id_curso <> $alus->id_curso) OR ($instructor->id <> $alus->id_instructor) OR 
                                            ($request->inicio <> $alus->inicio) OR ($termino <> $alus->termino) OR ($id_especialidad <> $alus->id_especialidad)) {
                                            DB::table('agenda')->where('id_curso', $folio)->delete();
                                            DB::table('tbl_cursos')->where('folio_grupo',$folio)->update(['dia' => '', 'tdias' => 0]);
                                        }
                                    }
                                }
                            } else {
                                $message = 'Instructor no valido..';
                            }
                        }
                    } else {
                        $message = 'La fecha de termino no puede ser menor a la de inicio';
                    }
                } else {
                    $message = 'El año de la fecha de inicio o de termino no coincide con el actual';
                }
            } else {
                $message  = "Si es una CERTIFICACIÓN, corrobore que cubra 10 horas.";
            }
        } else $message = "La acción no se ejecuto correctamente";
        return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
    }

    public function genera_folio()
    {
        //$consec = DB::table('alumnos_registro')->where('ejercicio', $this->ejercicio)->where('cct', $this->data['cct_unidad'])->where('eliminado', false)->value(DB::RAW('max(cast(substring(folio_grupo,7,4) as int))')) + 1;
        $consec = DB::table('alumnos_registro')->where('ejercicio', $this->ejercicio)->where('cct', $this->data['cct_unidad'])->where('eliminado', false)->value(DB::RAW("cast(substring(max(folio_grupo) from '.{4}$') as int)")) + 1;
        $consec = str_pad($consec, 4, "0", STR_PAD_LEFT);
        $folio = $this->data['cct_unidad'] . "-" . $this->ejercicio . $consec;

        return $folio;
    }

    public function nuevo()
    {
        $_SESSION['folio_grupo'] = NULL;
        return redirect()->route('preinscripcion.grupo');
    }
    public function turnar()
    {
        if ($_SESSION['folio_grupo']) {
            if (DB::table('exoneraciones')->where('folio_grupo',$_SESSION['folio_grupo'])->where('status','!=', 'CAPTURA')->where('status','!=','CANCELADO')->where('status','!=','AUTORIZADO')->exists()) {
                $message = "Solicitud de Exoneración o Reducción de couta en Proceso..";
                return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
            } else {
                $g = DB::table('tbl_cursos')->where('folio_grupo',$_SESSION['folio_grupo'])->first();
                if ($g) {
                    if ($g->tipo!='PINS' AND ($g->mexoneracion=='NINGUNO' OR $g->mexoneracion==null OR $g->mexoneracion=='0') AND ($g->depen!='INSTITUTO DE CAPACITACION Y VINCULACION TECNOLOGICA DEL ESTADO DE CHIAPAS')) {
                        $message = "MEMORÁNDUM DE EXONERACIÓN REQUERIDO..";
                        return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
                    } else {
                        $alumnos = DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->get();
                        $comprobante = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->value('comprobante_pago');
                        $horas_agenda = DB::table('agenda')
                            ->select(DB::raw("SUM( (( EXTRACT(EPOCH FROM cast(agenda.end as time))-EXTRACT(EPOCH FROM cast(start as time)))/3600)*
                            ( (extract(days from ((agenda.end - agenda.start)) ) ) + (case when extract(hours from ((agenda.end - agenda.start)) ) > 0 then 1 else 0 end)) ) as horas"))
                            ->where('id_curso',$_SESSION['folio_grupo'])->value('horas');
                        if ($horas_agenda == $g->dura) {
                            $costo = 0;
                            $conteo = 0;
                            foreach ($alumnos as $a) {
                                if (!$a->costo) {
                                    $message = "Ingrese la cuota del alumno " .$a->curp. ".";
                                    return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
                                }
                                $costo += $a->costo;
                                if ($a->costo) {
                                    $conteo += 1;
                                }
                            }
                            // foreach ($alumnos as $a) {
                            //     if ($a->mod=='CAE' AND $a->abrinscri!='PI') {
                            //         $exoneraciones = DB::table('alumnos_registro')
                            //             ->where('id_pre',$a->id_pre)
                            //             ->where('eliminado',false)
                            //             ->where('ejercicio',date('y'))
                            //             ->where('abrinscri','!=','PI')
                            //             ->where('mod','CAE')
                            //             ->where('turnado','!=','VINCULACION')
                            //             ->value(DB::raw('count(id)'));
                            //         if ($exoneraciones > 2) {
                            //             if (DB::table('alumnos_pre')->where('id',$a->id_pre)->value('permiso_exoneracion')==true) {
                            //                 $quitar_permiso = DB::table('alumnos_pre')->where('id',$a->id_pre)->update(['permiso_exoneracion'=>false]);
                            //             } else {
                            //                 $message = "El alumno excede el limite de exoneraciones permitidas " .$a->curp. ".";
                            //                 return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
                            //             }
                            //         }
                            //     }
                            // }
                            
                            $instructor_valido = $this->valida_instructor($alumnos[0]->id_instructor);
                            if($instructor_valido['valido']){
                                $result = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->update(['turnado' => 'UNIDAD', 'fecha_turnado' => date('Y-m-d')]);
                                if($result) DB::table('instructores')->where('id',$alumnos[0]->id_instructor)->where('curso_extra',true)->update(['curso_extra'=>false]);
                                else return redirect()->route('preinscripcion.grupo')->with(['message' => 'El curso no fue turnado correctamente. Por favor de intente de nuevo']); 
                            }else return redirect()->route('preinscripcion.grupo')->with(['message' => $instructor_valido['message']]);
                        } else {
                            $message = "Las horas agendadas no corresponden a la duración del curso..";
                            return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
                        }
                        
                    }
                } else {
                    $message = "Guarde los cambios ejecutados..";
                    return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
                }
            }
        }
        return redirect()->route('preinscripcion.grupo');
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        if ($id) {
            if (DB::table('exoneraciones')->where('folio_grupo',$_SESSION['folio_grupo'])->where('status','!=', 'CAPTURA')->where('status','!=','CANCELADO')->exists()) {
                $result = false;
            } else {
                //$result = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->where('id',$id)->update(['eliminado'=>true,'iduser_updated'=>$this->id_user]);
                if (count(DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->where('eliminado',false)->get())>1) {
                    $result = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->where('id', $id)->delete();
                } else {
                    $result = false;
                } 
            } 
        } else $result = false;
        //echo $result; exit;
        return $result;
    }

    public function subir_comprobante(Request $request)
    {
        $file =  $request->customFile;  //dd($file);
        $id = $_SESSION['folio_grupo'];
        if ($file) {
            $url_comprobante = $this->uploaded_file($file, $id, 'comprobante_pago');
            $opss = DB::table('alumnos_registro')->where('folio_grupo', $id)->update(['comprobante_pago' => $url_comprobante]);
            $message = "Operación Exitosa!!";
        } else {
            $message = 'El documento no fue cargado correctamente';
        }
        return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
    }

    public function uploaded_file($file, $id, $name)
    {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($name . "_" . $id . "_" . date('YmdHis') . "." . $extensionFile);
        $path_pdf = "/UNIDAD/comprobantes_pagos/";
        $path = $path_pdf . $documentFile;
        Storage::disk('custom_folder_1')->put($path, file_get_contents($file)); // guardamos el archivo en la carpeta storage
        //$documentUrl = storage::url($path); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        $documentUrl = $path;
        return $documentUrl;
    }

    public function showlm(Request $request)
    {
        if ($request->ajax()) {
            $clave = DB::table('tbl_municipios')
                ->select('id_estado', 'clave')
                ->where('id', $request->estado_id)
                ->first();
            $localidadArray = DB::table('tbl_localidades')->select('localidad', 'clave')
                ->where('id_estado', $clave->id_estado)
                ->where('clave_municipio', '=', $clave->clave)
                ->orderBy('localidad')->get();
            return response()->json($localidadArray);
        }
    }

    public function remplazar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if ($_SESSION['folio_grupo']) {
            if ($request->busqueda1 AND $request->curpo) {
                $id = DB::table('alumnos_registro as ar')
                    ->leftJoin('alumnos_pre as ap','ar.id_pre','=','ap.id')
                    ->where('ar.folio_grupo',$_SESSION['folio_grupo'])
                    ->where('ap.curp',$request->curpo)
                    ->value('ar.id');
                if ($id) {
                    $date = date('d-m-Y');
                    $alumno = DB::table('alumnos_pre')
                        ->select('id as id_pre','curp', 'matricula', DB::raw("cast(EXTRACT(year from(age('$date', fecha_nacimiento))) as integer) as edad"),
                         DB::raw("cast(EXTRACT(year from(age('$date', fecha_nacimiento))) as integer) as edad"),'ultimo_grado_estudios as escolaridad','nombre','apellido_paterno','apellido_materno'
                        )
                        ->where('curp', $request->busqueda1)
                        ->where('activo', true)
                        ->first();
                    if ($alumno) {
                        if ($alumno->edad >= 15) {
                            if (substr($request->curpo, 10, 1) == substr($request->busqueda1, 10, 1)) {
                                $result = DB::table('alumnos_registro')
                                    ->where('folio_grupo', $_SESSION['folio_grupo'])
                                    ->where('id', $id)
                                    ->update([
                                        'id_pre' => $alumno->id_pre, 'no_control' => $alumno->matricula,                                         
                                        'nombre'=>$alumno->nombre, 'apellido_paterno'=>$alumno->apellido_paterno,
                                        'apellido_materno'=>$alumno->apellido_materno,'curp'=>$alumno->curp,
                                        'escolaridad'=>$alumno->escolaridad,
                                        'iduser_updated' => Auth::user()->id,
                                        'updated_at' => date('Y-m-d H:i')
                                    ]);
                                if ($result) {
                                    $message = "Operación exitosa !!..";
                                }
                            } else {
                                $message = "Los generos no coiniciden..";
                            }

                        } else {
                            $message ="La edad del alumno invalida..";
                        }

                    } else {
                        $message = "Alumno no registrado " . $request->busqueda1 . ".";
                    }
                } else {
                    $message = "Alumno no registrado " . $request->curpo . ".";
                }
            }else {
                $message = "Ingrese la CURP del alumno..";
            }
        }
        return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
    }

    public function generar(){
        if ($_SESSION['folio_grupo']) {
            $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first(); 
            $alumnos = DB::table('alumnos_registro as ar')
                ->select('ar.apellido_paterno','ar.apellido_materno','ar.nombre','ap.sexo', 'ap.correo',
                        DB::raw("CONCAT(ar.apellido_paterno,' ', ar.apellido_materno,' ',ar.nombre) as alumno"),
                        DB::raw("extract(year from (age(ar.inicio,ap.fecha_nacimiento))) as edad"),'c.nombre_curso','c.horas',
                        DB::raw("to_char(DATE (ar.inicio)::date, 'DD-MM-YYYY') as inicio"),
                        DB::raw("to_char(DATE (ar.termino)::date, 'DD-MM-YYYY') as termino"),
                        'ar.horario', 'ar.mod', 'ar.costo','ar.tipo_curso','ar.organismo_publico as depe')
                ->leftJoin('alumnos_pre as ap','ar.id_pre','ap.id')
                ->leftJoin('cursos as c','ar.id_curso','c.id')
                ->where('ar.folio_grupo',$_SESSION['folio_grupo'])
                ->orderBy('alumno')
                ->get();//dd($alumnos);
            if (count($alumnos)>0) {
                $folio_grupo = $_SESSION['folio_grupo'];                
                $reg_unidad = DB::table('tbl_unidades')->where('id', $this->id_unidad)->first(); 
                $direccion = $reg_unidad->direccion;

                $pdf = PDF::loadView('preinscripcion.listaAlumnos',compact('alumnos','distintivo','folio_grupo','direccion'));
                $pdf->setpaper('letter','landscape');
                return $pdf->stream('LISTA.pdf');
            }else {
                return "No se encuentran alumnos registrados, volver a intentar.";exit;
            }
        }else{
            return "ACCIÓN INVÁlIDA";exit;
        }
    }

    public function generarApertura(Request $request){
        if ($_SESSION['folio_grupo']) {
            $data = $cursos = []; $unidad = '';
            $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first();
            $memo = DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->where('eliminado',false)->value('mpreapertura');
            $date = date('Y-m-d');
            if (DB::table('alumnos_registro')->where('mpreapertura',$memo)->value('fmpreapertura')) {
                $date = DB::table('alumnos_registro')->where('mpreapertura',$memo)->value('fmpreapertura');
            }
            if ($memo AND ($memo!='')) {
                $result = DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->where('turnado','=','VINCULACION')->update(['fmpreapertura'=>$date]);
                $cursos = DB::table('tbl_cursos as tc')
                    ->select(
                        'tc.folio_grupo','tc.tipo_curso','tc.espe','tc.curso','tc.mod','tc.tcapacitacion','tc.dura','tc.inicio','tc.termino','ar.horario','tc.dia','tc.horas',
                        'tc.costo',DB::raw("(tc.hombre + tc.mujer) as tpar"),'tc.hombre','tc.mujer','tc.mexoneracion','tc.cgeneral','tc.cespecifico','tc.depen','tc.depen_representante as depen_repre',
                        'tc.depen_telrepre as tel_repre','tc.nombre','ar.realizo as vincu','ar.observaciones as nota_vincu','ar.efisico','tc.unidad'
                    )
                    ->leftJoin('alumnos_registro as ar', 'tc.folio_grupo', 'ar.folio_grupo')
                    ->where('ar.mpreapertura', $memo)
                    ->where('ar.eliminado', false)
                    ->groupBy('tc.folio_grupo','tc.tipo_curso','tc.espe','tc.curso','tc.mod','tc.tcapacitacion','tc.dura','tc.inicio','tc.termino','ar.horario','tc.dia','tc.horas',
                    'tc.costo','tc.hombre','tc.mujer','tc.mexoneracion','tc.cgeneral','tc.cespecifico','tc.depen','tc.depen_representante','tc.depen_telrepre','tc.nombre','ar.realizo',
                    'ar.observaciones','ar.efisico','tc.unidad')
                    ->orderBy('folio_grupo')
                    ->get(); //dd($cursos);
                if (count($cursos) > 0) {
                    $unidad = $cursos[0]->unidad;
                    foreach ($cursos as $key => $value) {
                        $costos =  DB::table('alumnos_registro')->select(DB::raw("concat(count(id),' DE ',costo) as costos"),'costo as cuota')
                            ->where('folio_grupo', $value->folio_grupo)->where('eliminado', false)->orderby('costo','ASC')->groupby('costo')->get();
                        $costo_string = "";
                        if(count($costos)>1){
                            foreach($costos as $c){
                                if(!$costo_string)
                                    $costo_string = $costo_string." ".$c->costos.", ";
                                else $costo_string = $costo_string." ".$c->costos;
                            }
                        }elseif(count($costos)==1) $costo_string = $costos[0]->cuota;

                        $data[$key]['folio_grupo'] = $value->folio_grupo;
                        $data[$key]['tipo_curso'] = $value->tipo_curso;
                        $data[$key]['espe'] = $value->espe;
                        $data[$key]['curso'] = $value->curso;
                        $data[$key]['mod'] = $value->mod;
                        $data[$key]['tcapacitacion'] = $value->tcapacitacion;
                        $data[$key]['dura'] = $value->dura;
                        $data[$key]['inicio'] = $value->inicio;
                        $data[$key]['termino'] = $value->termino;
                        $data[$key]['horario'] = $value->horario;
                        $data[$key]['dia'] = $value->dia;
                        $data[$key]['horas'] = $value->horas;
                        $data[$key]['costos'] = $costo_string;
                        $data[$key]['costo'] = $value->costo;
                        $data[$key]['tpar'] = $value->tpar;
                        $data[$key]['hombre'] = $value->hombre;
                        $data[$key]['mujer'] = $value->mujer;
                        $data[$key]['mexoneracion'] = $value->mexoneracion;
                        $data[$key]['cgeneral'] = $value->cgeneral;
                        $data[$key]['cespecifico'] = $value->cespecifico;
                        $data[$key]['depen'] = $value->depen;
                        $data[$key]['depen_repre'] = $value->depen_repre;
                        $data[$key]['tel_repre'] = $value->tel_repre;
                        $data[$key]['instructor'] = $value->nombre;
                        $data[$key]['vincu'] = $value->vincu;
                        $data[$key]['observaciones'] = $value->nota_vincu;
                        $data[$key]['efisico'] = $value->efisico;
                        $data[$key]['unidad'] = $value->unidad;
                    }
                }
                if (count($data) > 0) {
                    $meses = ['01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre'];
                    $mes = $meses[date('m',strtotime($date))];
                    $date = date('d',strtotime($date)).' de '.$mes.' del '.date('Y',strtotime($date));
                    $reg_unidad = DB::table('tbl_unidades')->where('unidad', $unidad)->first(); //dd($reg_unidad);
                    $direccion = $reg_unidad->direccion; 
                    $pdf = PDF::loadView('preinscripcion.solicitudApertura', compact('distintivo', 'data', 'reg_unidad', 'date', 'memo','direccion'));
                    $pdf->setpaper('letter', 'landscape');
                    return $pdf->stream('SOLICITUD.pdf');
                } else {
                    return "NO SE ENCONTRO REGISTROS, VUELVA A INTENTAR..";
                    exit;
                }
            } else {
                return "GUARDE EL NÚMERO DE MEMORÁNDUM..";
                exit;
            } 
        }else{
            return "ACCIÓN INVÁlIDA";exit;
        }
    }

    public function cmbinstructor(Request $request)
    {
        if (isset($request->id) and isset($request->inicio) and isset($request->termino)) {
            $internos = DB::table('instructores as i')->select('i.id')->join('tbl_cursos as c','c.id_instructor','i.id')
            ->where('i.tipo_instructor', 'INTERNO')->where('curso_extra',false)
            ->where(DB::raw("EXTRACT(YEAR FROM c.inicio)"),date('Y'))
            ->where(DB::raw("EXTRACT(MONTH FROM c.inicio)"),date('m'))
            ->havingRaw('count(*) >= 2')
            ->groupby('i.id');
            
            $id_especialidad = DB::table('cursos')->where('id',$request->id)->value('id_especialidad');
            $instructores = DB::table(DB::raw('(select id_instructor, id_curso from agenda group by id_instructor, id_curso) as t'))
                ->select(DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'),'instructores.id', DB::raw('count(id_curso) as total'))
                ->rightJoin('instructores','t.id_instructor','=','instructores.id')
                ->JOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
                ->JOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
                ->JOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
                //->join('especialidad_instructor_curso','especialidad_instructor_curso.id_especialidad_instructor','=','especialidad_instructores.id')
                ->WHERE('estado',true)          
                ->WHERE('instructores.tipo_honorario', 'like', '%HONORARIOS%')
                ->WHERE('instructores.status', '=', 'VALIDADO')->where('instructores.nombre','!=','')
                ->WHERE('especialidad_instructores.especialidad_id',$id_especialidad)
                //->where('especialidad_instructor_curso.curso_id',$grupo->id_curso)
                //->where('especialidad_instructor_curso.activo', true)            
                ->WHERE('fecha_validacion','<',$request->inicio)
                ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',$request->termino)
                ->whereNotIn('instructores.id', $internos)
                ->groupBy('t.id_instructor','instructores.id')
                ->orderBy('instructor')
                ->get();
            $json = json_encode($instructores);
            //var_dump($json);exit;
        } else {
            $json = json_encode(["No hay registros que mostrar."]);
        }
        return $json;
    }

    public function showCalendar($id){
        $folio = $id;
        $data['agenda'] =  Agenda::where('id_curso','=',$folio)->get();
        return response()->json($data['agenda']);
    }

    public function deleteCalendar(Request $request){
        $id = $request->id;
        $id_curso = DB::table('agenda')->where('id',$id)->value('id_curso');
        if (DB::table('exoneraciones')->where('folio_grupo',$id_curso)->where('status','!=', 'CAPTURA')->where('status','!=','CANCELADO')->exists()) {
            $message = "Solicitud de Exoneración o Reducción de couta en Proceso..";
            return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
        } else {
            Agenda::destroy($id);
            $dias = $this->dias($id_curso);
            $result = DB::table('tbl_cursos')->where('folio_grupo',$id_curso)->update(['dia' => $dias['nombre'], 'tdias' => $dias['total']]);
            return response()->json($id);
        }
    }

    public function storeCalendar(Request $request){
        set_time_limit(0);
        $fechaInicio = date("Y-m-d", strtotime($request->start));
        $fechaTermino = date("Y-m-d", strtotime($request->end));
        $horaInicio = date("H:i", strtotime($request->start));
        $horaTermino = date("H:i", strtotime($request->end));
        $id_instructor = $request->id_instructor;
        $id_curso = $request->id_curso;
        $grupo = DB::table('tbl_cursos')->where('folio_grupo',$id_curso)->first();
        $period = CarbonPeriod::create($fechaInicio,$fechaTermino);
        $minutos_curso = Carbon::parse($horaTermino)->diffInMinutes($horaInicio);
        $es_lunes= Carbon::parse($fechaInicio)->is('monday');
        $sumaMesInicio = 0;
        $sumaMesFin = 0;
        $id_unidad = DB::table('tbl_unidades')->where('unidad','=',$grupo->unidad)->value('id');
        $id_municipio = $grupo->id_municipio;
        $clave_localidad = $grupo->clave_localidad;
        if (DB::table('exoneraciones')->where('folio_grupo',$id_curso)->where('status','!=', 'CAPTURA')->where('status','!=','CANCELADO')->exists()) {
            return "Solicitud de Exoneración o Reducción de couta en Proceso..";
        }
        //VALIDACIÓN DEL HORARIO
        if (($horaInicio < date('H:i',strtotime(str_replace(['a.m.', 'p.m.'], ['am', 'pm'], $grupo->hini)))) OR ($horaInicio > date('H:i',strtotime(str_replace(['a.m.', 'p.m.'], ['am', 'pm'], $grupo->hfin)))) OR 
        ($horaTermino < date('H:i',strtotime(str_replace(['a.m.', 'p.m.'], ['am', 'pm'], $grupo->hini)))) OR ($horaTermino > date('H:i',strtotime(str_replace(['a.m.', 'p.m.'], ['am', 'pm'], $grupo->hfin))))) {
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

        // INSTRUCTORES INTERNOS,MÁXIMO 2 CURSOS EN EL MES y 5 MESES DE ACTIVIDAD
        $instructor_valido = $this->valida_instructor($id_instructor);
        if(!$instructor_valido['valido'])  return $instructor_valido['message'];

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
        
        try {
            $titulo = $request->title;
            $agenda = new Agenda();
            $agenda->title = $titulo;
            $agenda->start = $request->start;
            $agenda->end = $request->end;
            $agenda->textColor = $request->textColor;
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
        $result = DB::table('tbl_cursos')->where('folio_grupo',$id_curso)->update(['dia' => $dias_curso['nombre'], 'tdias' => $dias_curso['total']]);
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
                    if ((($temp + 1) == $value->dia) && ($temp2==null)) {
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

    private function valida_instructor($id_instructor)
    {
        //echo $id_instructor;
        $valido = false;
        $message = null;

        ///VALIDACION DE INSTRUCTORES INTERNOS
        $internos = DB::table('instructores as i')->select('i.id')->join('tbl_cursos as c','c.id_instructor','i.id') ->where('i.id',$id_instructor)
            ->where('i.tipo_instructor', 'INTERNO')->where('curso_extra',false)
            ->where(DB::raw("EXTRACT(YEAR FROM c.inicio)"),date('Y'))
            ->where(DB::raw("EXTRACT(MONTH FROM c.inicio)"),date('m'))
            ->havingRaw('count(*) > 2')
            ->groupby('i.id')->first();
            //var_dump($internos);exit;
        if($internos) $message = "El instructor interno ha excedido el número de cursos a impartir (máximo 2 cursos al mes). Favor de verificar.";
        else $valido = true;
        
        
        ///VALIDACIÓN 5 meses de actividad y 30 días naturales de RECESO
        $receso =  DB::table('tbl_cursos as c')->where('id_instructor',$id_instructor)
        ->where('c.inicio','>=',DB::raw("date_trunc('month', (SELECT max(tbl_cursos.termino) from tbl_cursos where tbl_cursos.id_instructor = c.id_instructor)::timestamp) - interval '5 month'"))
        ->value(DB::raw("count( distinct(date_trunc('month', c.inicio)))"));
       
        //dd($receso);
        if($receso>5){
            $receso = DB::table('tbl_cursos as c')//->select('inicio','termino',DB::raw("COALESCE((select DATE_PART('day', tc.inicio::timestamp - c.termino::timestamp ) from tbl_cursos as tc where tc.id_instructor= c.id_instructor and tc.inicio>c.inicio order by tc.inicio ASC limit 1  )-1,0) as dias"))
            ->where('c.id_instructor',$id_instructor)        
            ->where('c.inicio', '>=', DB::raw("date_trunc('month', (SELECT max(termino) from tbl_cursos where tbl_cursos.id_instructor = c.id_instructor)::timestamp) - interval '5 month'"))        
            
            ->where(DB::raw("COALESCE((select DATE_PART('day', tc.inicio::timestamp - c.termino::timestamp ) from tbl_cursos as tc where tc.id_instructor=c.id_instructor and tc.inicio>c.inicio order by tc.inicio ASC limit 1  )-1,0)::int"),'>',30)
        
            ->where(function($query){
                $query->where('c.status_curso','<>','CANCELADO')->orWherenull('c.status_curso');
            }) 
            ->orderby('c.inicio','ASC')//->get();
            ->first();//value(DB::raw("COALESCE((select DATE_PART('day', tc.inicio::timestamp - c.termino::timestamp ) from tbl_cursos as tc where tc.id_instructor= c.id_instructor and tc.inicio>c.inicio order by tc.inicio ASC limit 1  )-1,0)"));
            
            if($receso) $valido = true;
            else{
                $valido = false;
                $message = "La actividad del instructor supera el límite de 5 meses continuos. Deberá tomar un receso de 30 días naturales.";
            }
        }
        return ['valido' => $valido, 'message' => $message];
    }
}
