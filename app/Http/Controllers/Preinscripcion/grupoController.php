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
use App\Models\ModelExpe\ExpeUnico;
//use App\Models\Alumnopre;
//use App\Models\Inscripcion;
use App\Models\tbl_inscripcion;
use App\Utilities\Algoritmo35;
use App\Utilities\MyUtility;

use function PHPSTORM_META\type;
use App\Http\Controllers\Solicitudes\vbgruposController;
use App\Services\ValidacionServicioVb;

class grupoController extends Controller
{
    use catUnidades;
    use catApertura;
    function __construct()
    {
        session_start();
        $this->ejercicio = date("y");
        $this->path = "/expedientes/";
        $this->path_uploadFiles = env("APP_URL").'/storage/uploadFiles';
        $this->path_files = env("APP_URL").'/storage/';
        $this->path_files_cancelled = env("APP_URL").'/grupos/recibo/descargar?folio_recibo=';
        $this->key = "XdFeW2";
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->id_user = Auth::user()->id;
            $this->realizo = Auth::user()->name;
            $this->id_unidad = Auth::user()->unidad;
            $this->data = $this->unidades_user('vincula');  //vincula
            $this->admin =  $this->unidades_user('admin'); //admin
            $_SESSION['unidades'] =  $this->data['unidades'];

            return $next($request);
        });
    }

    public function index(Request $request){

        //$digitov = Algoritmo35::digito_verificador('2B25000100004');
        /*
        $inst = (new vbgruposController());
        $instructores = $inst->modal_instructores($request);
        dd($instructores);
*/
        $curso = $cursos = $localidad  = $alumnos = $instructores = $instructor = $recibo =[];
        $message = $comprobante = $folio_pago = $fecha_pago = $grupo = $ValidaInstructorPDF = $folio_grupo = NULL;
        $es_vulnerable = $edicion_exo = false;

        $unidades = $this->data['unidades'];
        $unidad = $uni = $this->data['unidad'];
        if(!$unidad) $unidad = $uni = $request->unidad;

        if (isset($_SESSION['folio_grupo'])) {

            $folio_grupo = $_SESSION['folio_grupo'];

            list($grupo, $alumnos) = $this->grupo_alumnos($folio_grupo);
            if (count($alumnos) > 0) {
                $uni = $grupo->unidad;
                $es_vulnerable = collect($alumnos)->contains(function ($value) {
                    return $value->id_gvulnerable != '[]';
                });


                if (($grupo->turnado_grupo == 'VINCULACION' or  $grupo->status_curso=='EDICION' )and isset($this->data['cct_folio'])) $this->activar = true;
                else $this->activar = false;

                $curso = DB::table('cursos')->where('id', $grupo->id_curso);
                    if($grupo->status_curso!='AUTORIZADO') $curso = $curso->where('cursos.estado', true);
                $curso = $curso->first();
                //dd($curso);

                //CATALOGOS
                $tipo = $grupo->tcapacitacion;
                $mod = $grupo->mod;
                //dd($grupo);
                $clave = DB::table('tbl_municipios')->where('id', $grupo->id_municipio)->value('clave');
                $localidad = DB::table('tbl_localidades')->where('id_estado', '7')->where('clave_municipio', '=', $clave)->pluck('localidad', 'clave');
                $cursos = DB::table('cursos')->where('tipo_curso','like',"%$tipo%");
                    // ->Where('curso_alfa',true); //nueva linea para cursos alfa 08052025
                    if($grupo->status_curso!='AUTORIZADO') $cursos = $cursos->where('cursos.estado', true);
                    $cursos = $cursos->where('modalidad','like',"%$mod%")
                    ->whereJsonContains('unidades_disponible', [$grupo->unidad])->orderby('cursos.nombre_curso')->pluck('nombre_curso', 'cursos.id');

                if($grupo->status_curso =='AUTORIZADO')$cursos->put($grupo->id_curso, $grupo->nombre_curso);

                //dd($grupo);
                $instructores = $this->data_instructores($grupo);
                //FIN CATALOGOS
                $instructor = DB::table('instructores')->select('id',DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'),'tipo_honorario')->where('id',$grupo->id_instructor)->first();


                //$grupo = DB::table('tbl_cursos')->where('folio_grupo',$folio_grupo)->first();
                //dd($instructor_mespecialidad);
                $edicion_exo = DB::table('exoneraciones')->where('folio_grupo',$folio_grupo)->where('status','EDICION')->exists();
                if($grupo->id_especialidad){
                    $instructor_mespecialidad = $grupo->instructor_mespecialidad;
                    $ValidaInstructorPDF = DB::table('especialidad_instructores')->where('especialidad_id', $grupo->id_especialidad)
                        ->where('id_instructor', $grupo->id_instructor)
                        ->whereExists(function ($query) use ($instructor_mespecialidad){
                            $query->select(\DB::raw("elem->>'arch_val'"))
                                ->from(\DB::raw("jsonb_array_elements(hvalidacion) AS elem"))
                                ->where(\DB::raw("elem->>'memo_val'"), '=', $instructor_mespecialidad);
                        })
                    ->value(\DB::raw("(SELECT elem->>'arch_val' FROM jsonb_array_elements(hvalidacion) AS elem WHERE elem->>'memo_val' = '$instructor_mespecialidad') as pdfvalida"));
                }
            } else {
                $message = "No hay registro qwue mostrar para Grupo No." . $folio_grupo;
                $this->activar = true;
                $_SESSION['folio_grupo'] = NULL;
            }
        } else {
            $this->activar = true;
            $_SESSION['folio_grupo'] = NULL;
        }

        $cerss = DB::table('cerss');
            if ($unidad) $cerss = $cerss->where('id_unidad', $this->id_unidad)->where('activo', true);
            $cerss = $cerss->orderby('nombre', 'ASC')->pluck('nombre', 'id');

        $activar = $this->activar;

        if(str_starts_with($this->data['cct'] ?? 0, '07000')) $municipio = DB::table('tbl_municipios')->where('id_estado', '7')->orderby('muni')->pluck('muni', 'id');
        else  $municipio = DB::table('tbl_municipios')->where('id_estado', '7')->whereJsonContains('unidad_disponible',$uni)->orderby('muni')->pluck('muni', 'id');




        $dependencia = DB::table('organismos_publicos')
            ->where('activo', true)
            ->orderby('organismo')
            ->pluck('organismo', 'organismo');
        $grupo_vulnerable = DB::table('grupos_vulnerables')->orderBy('grupo')->pluck('grupo','id');
        $medio_virtual = $this->medio_virtual();
        if (session('message')) $message = session('message');
        $tinscripcion = $this->tinscripcion();

        //By Jose Luis Moreno
        $id_usuario = null;
        if($this->admin['slug']) $id_usuario = $this->id_user;
        $linkPDF = array("acta" => '',"convenio" => '', "soli_ape" => '',"sid" => '', "status_dpto" => 'INVALID');
        try {
            $jsonvincu = ExpeUnico::select('vinculacion')->where('folio_grupo', '=', $folio_grupo)->first();
            if (isset($jsonvincu->vinculacion['doc_1']) && isset($jsonvincu->vinculacion['status_dpto'])) {
                $docs_json = [$jsonvincu->vinculacion['doc_1']['url_pdf_acta'], $jsonvincu->vinculacion['doc_1']['url_pdf_convenio'],
                $jsonvincu->vinculacion['doc_3']['url_documento'], $jsonvincu->vinculacion['doc_4']['url_documento']];
                $linkPDF = array(
                    "acta" => ($docs_json[0] != '') ? $this->path_uploadFiles.$docs_json[0] : "",
                    "convenio" => ($docs_json[1] != '') ? $this->path_uploadFiles.$docs_json[1] : "",
                    "soli_ape" => ($docs_json[2] != '') ? $this->path_uploadFiles.$docs_json[2] : "",
                    "sid" => ($docs_json[3] != '') ? $this->path_uploadFiles.$docs_json[3] : "",
                    "status_dpto" => ($jsonvincu->vinculacion['status_dpto'] != '') ? $jsonvincu->vinculacion['status_dpto'] : "INVALID"
                );
            }else{
                $linkPDF = array("acta" => '',"convenio" => '', "soli_ape" => '',"sid" => '', "status_dpto" => 'INVALID');
            }
        } catch (\Throwable $th) {
            dd("Error al cargar documentos ".$th->getMessage());
        }

        //$recibo = DB::table('tbl_recibos')->where('folio_grupo',$folio_grupo)->where('status_folio','ENVIADO')->first();
        $ubicacion = DB::table('tbl_unidades')->where('id', Auth::user()->unidad)->value('ubicacion');
        $recibo_nulo = DB::table('tbl_recibos')->whereNull('folio_recibo')->where('unidad',$ubicacion)->exists();
        $programas = $this->programa();
        $planteles = $this->plantel();
        return view('preinscripcion.index', compact('cursos', 'alumnos', 'unidades', 'cerss', 'unidad', 'folio_grupo', 'curso', 'activar',
            'es_vulnerable', 'tinscripcion', 'municipio', 'dependencia', 'localidad','grupo_vulnerable','edicion_exo','instructores','instructor',
            'medio_virtual','grupo', 'id_usuario','recibo', 'ValidaInstructorPDF', 'linkPDF', 'recibo_nulo','programas','planteles', 'message'));
    }


    public function referencias($folio, $alumno =null){
        if($alumno){
            $data  = DB::table('alumnos_registro as ar')
            ->select('ar.id_pre','ar.folio_grupo', DB::raw("CONCAT(nombre,' ',apellido_paterno,' ',apellido_materno) as alumno"), 'ar.costo', 'ar.inicio')
            ->where('folio_grupo',$folio)->get();
            $n=0;
            foreach ($data as $fila) {
                $folio = $fila->folio_grupo.$fila->id_pre;
                $f = str_replace('-','', $folio);
                $data[$n++]->referencia =  $f.Algoritmo35::digito_verificador($folio);
            }
        }else{
            $f = $folio.'0000';
            $f = str_replace('-','', $f);
            $referencia =  $f.Algoritmo35::digito_verificador($f);
            $costo = DB::table('alumnos_registro as ar')->where('folio_grupo',$folio)->sum('costo');

            $data[]  = DB::table('alumnos_registro as ar')
            ->select('ar.id_pre','ar.folio_grupo', DB::raw("CONCAT(nombre,' ',apellido_paterno,' ',apellido_materno) as alumno"), DB::raw("$costo as costo"), 'ar.inicio', DB::raw("'".$referencia."' as referencia"))
            ->where('folio_grupo',$folio)->orderby(DB::raw("CONCAT(apellido_paterno,' ',apellido_materno,' ',nombre)"),'ASC')->first();
        }
        if($data ?? 0){
            if($alumno) $nombre = 'Alumno';
            else $nombre = 'Representante';

            $instituto = DB::table('tbl_instituto')->where('id',1)->value('name');
            $instituto = MyUtility::textoAltasBajas($instituto);
            $pdf = PDF::loadView('preinscripcion.grupo.pdfReferencia',compact('data','nombre','instituto'));
            $pdf->setpaper('letter','portrait');
            return $pdf->stream('$folio.pdf');
        }else return "No se encontraron datos que mostrar, por favor intente de nuevo.";

    }


    private function data_instructores($data){
        $internos = DB::table('instructores as i')->select('i.id')->join('tbl_cursos as c','c.id_instructor','i.id')
        ->where('i.tipo_instructor', 'INTERNO')->where('curso_extra',false)
        ->where(DB::raw("EXTRACT(YEAR FROM c.inicio)"), date('Y', strtotime($data->inicio)))
        ->where(DB::raw("EXTRACT(MONTH FROM c.inicio)"), date('m', strtotime($data->inicio)))
        ->havingRaw('count(*) >= 2')
        ->groupby('i.id');

        $instructores = DB::table(DB::raw('(select id_instructor, id_curso from agenda group by id_instructor, id_curso) as t'))
        ->select(DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'),'instructores.id', DB::raw('count(id_curso) as total'))
        ->rightJoin('instructores','t.id_instructor','=','instructores.id')
        ->LEFTJOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
        ->LEFTJOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
        ->LEFTJOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
        //->JOIN('especialidad_instructor_curso','especialidad_instructor_curso.id_especialidad_instructor','=','especialidad_instructores.id')
        //->WHERE('especialidad_instructor_curso.curso_id',$data->id_curso)
        //->WHERE('estado',true)
        //->WHERE('instructores.status', '=', 'VALIDADO')->where('instructores.nombre','!=','')
        ->WHERE('especialidad_instructores.especialidad_id',$data->id_especialidad)
        // ->Where('instructor_alfa', true) // nueva linea para instructores alfa 08/05/2025
        // ->WHERE(DB::raw("datos_alfa->'subproyectos'->>'chiapas puede'"), '=', 'no_voluntario') // nueva linea para instructores alfa 08/05/2025
        //->where('especialidad_instructor_curso.activo', true)
        //->WHERE('fecha_validacion','<',$data->inicio)
        //->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',$data->termino)
        //->whereNotIn('instructores.id', $internos)
        ->groupBy('t.id_instructor','instructores.id')
        ->orderBy('instructor')
        //VOBO ->get();
        ->limit(1)->get(); //NUEVO
        return $instructores;
    }

    public function cmbinstructor(Request $request){
        if (isset($request->id) and isset($request->inicio) and isset($request->termino)) {
            $data = $request;
            $data->id_especialidad = DB::table('cursos')->where('id',$request->id)->value('id_especialidad');
            $data->id_curso = $request->id;
            $instructores = $this->data_instructores($data);
            $json = json_encode($instructores);
            return $json;
        } else $json = json_encode(["No hay registros que mostrar."]);
    }


    private function grupo_alumnos($folio_grupo){
        $grupo =  DB::table('alumnos_registro as ar')->where('ar.folio_grupo', $folio_grupo)
            ->select(//DE LA APERTURA
                DB::raw('COALESCE(tc.folio_grupo, ar.folio_grupo) as folio_grupo'),
                DB::raw("COALESCE(tc.clave, '0') as clave"),
                DB::raw('COALESCE(tc.tdias, null) as tdias'),
                DB::raw('COALESCE(tc.mexoneracion, null) as mexoneracion'),
                DB::raw('COALESCE(tc.dia, null) as dia'),
                DB::raw('COALESCE(tc.cgeneral, null) as cgeneral'),
                DB::raw('COALESCE(tc.fcgen, null) as fcgen'),
                DB::raw('COALESCE(tc.tipo, null) as tipo'),

                //DEL GRUPO
                DB::raw('COALESCE(tc.id_cerss, ar.id_cerss) as id_cerss'),
                DB::raw('COALESCE(tc.inicio, ar.inicio) as inicio'),
                DB::raw('COALESCE(tc.termino, ar.termino) as termino'),
                DB::raw('COALESCE(tc.clave_localidad, ar.clave_localidad) as clave_localidad'),
                DB::raw('COALESCE(tc.unidad, ar.unidad) as unidad'),
                DB::raw('COALESCE(tc.id_gvulnerable, null) as id_gvulnerable'),
                DB::raw('COALESCE(tc.efisico, ar.efisico) as efisico'),
                DB::raw('COALESCE(tc.medio_virtual, ar.medio_virtual) as medio_virtual'),
                DB::raw('COALESCE(tc.link_virtual, ar.link_virtual) as link_virtual'),
                DB::raw('COALESCE(tc.cespecifico, ar.cespecifico) as cespecifico'),
                DB::raw('COALESCE(tc.fcespe, ar.fcespe) as fcespe'),
                DB::raw('COALESCE(tc.depen_representante, ar.depen_repre) as depen_repre'),
                DB::raw('COALESCE(tc.depen_telrepre, ar.depen_telrepre) as depen_telrepre'),
                DB::raw('COALESCE(tc.tcapacitacion, ar.tipo_curso) as tcapacitacion'),
                DB::raw("SUBSTRING( COALESCE(
                    CASE WHEN tc.hini LIKE '%p%' and SUBSTRING(tc.hini, 1, 2)::integer <> 12 THEN (SUBSTRING(tc.hini, 1, 5)::time+'12:00')::text
                         ELSE SUBSTRING(tc.hini, 1, 5)
                    END, SUBSTRING(ar.horario, 1, 5)),1,5) as hini"),
                DB::raw("SUBSTRING(COALESCE(
                    CASE WHEN tc.hfin LIKE '%p%' and SUBSTRING(tc.hfin, 1, 2)::integer <> 12 THEN (SUBSTRING(tc.hfin, 1, 5)::time+'12:00')::text
                         ELSE SUBSTRING(tc.hfin, 1, 5)
                    END,  SUBSTRING(ar.horario, 9, 5)),1,5) as hfin"),

                DB::raw('COALESCE(tc.id_municipio, ar.id_muni) as id_municipio'),
                DB::raw('COALESCE(tc.depen, ar.organismo_publico) as depen'),
                DB::raw('COALESCE(tc.depen_telrepre, ar.depen_telrepre) as depen_telrepre'),
                DB::raw('COALESCE(tc.tipo_curso, ar.servicio) as tipo_curso'),
                DB::raw("COALESCE(tc.id_especialidad, ar.id_especialidad) as id_especialidad"),
                DB::raw("COALESCE(tc.instructor_mespecialidad, '') as instructor_mespecialidad"),
                DB::raw('COALESCE(tc.mod, ar.mod) as mod'),
                DB::raw('COALESCE(tc.status_curso, null) as status_curso'),
                DB::raw('COALESCE(tc.unidad, ar.unidad) as unidad'),
                DB::raw('COALESCE(tc.id_instructor, ar.id_instructor) as id_instructor'),
                DB::raw('COALESCE(tc.plantel, null) as plantel'),
                DB::raw('COALESCE(tc.programa, null) as programa'),
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
                DB::raw('COALESCE(tc.id_curso, ar.id_curso) as id_curso'),
                DB::raw('COALESCE(tc.curso, c.nombre_curso) as nombre_curso'),
                DB::raw('COALESCE(tc.clave_localidad, ar.clave_localidad) as clave_localidad'),
                ///DE OTRAS TABLAS
                DB::raw('ar.mpreapertura'),
                DB::raw('ar.turnado as  turnado_grupo'),
                DB::raw('ar.observaciones as obs_vincula'),
                DB::raw("CASE WHEN tu.vinculacion=tu.dunidad THEN true ELSE false END as editar_solicita"),
                DB::raw("CASE WHEN tr.folio_recibo is not null THEN true ELSE false END as es_recibo_digital"),
                'exo.status as exo_status','exo.nrevision as exo_nrevision',
                DB::raw('COALESCE(tc.vb_dg, false) as vb_dg')//NUEVO VOBO

            )
            ->leftjoin('tbl_cursos as tc','tc.folio_grupo','ar.folio_grupo')
            ->leftJoin('tbl_recibos as tr', function ($join) {
                $join->on('tr.folio_grupo', '=', 'ar.folio_grupo')
                     ->where('tr.status_folio','ENVIADO');
            })
            ->leftJoin('exoneraciones as exo', function ($join) {
                $join->on('exo.folio_grupo', '=', 'ar.folio_grupo')
                     ->where('exo.status','!=','CANCELADO');
            })
            ->leftjoin('cursos as c','c.id','ar.id_curso')
            ->leftjoin('tbl_unidades as tu','ar.unidad','tu.unidad')
            //->orderby('ar.id_vulnerable','DESC')
            ->first();
//dd($grupo);
    $alumnos = Alumno::busqueda($folio_grupo,'grupo')->get();

        if($grupo and $alumnos )  return [$grupo, $alumnos];
        else return $message = "OPERACION NO VALIDA.";
    }



    public function cmbcursos(Request $request)
    {
        //$request->unidad = 'TUXTLA';
        if (isset($request->tipo) and isset($request->unidad) and isset($request->modalidad)) {
            $cursos = DB::table('cursos')->select('cursos.id', 'nombre_curso')
                ->where('tipo_curso','like',"%$request->tipo%")
                ->where('modalidad','like',"%$request->modalidad%")
                ->where('cursos.estado', true)
                // ->Where('curso_alfa', true) // linea nueva para solo cursos alfa
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
            if(str_starts_with($this->data['cct'] ?? 0, '07000')) $municipio = DB::table('tbl_municipios')->select('muni','id')->where('id_estado', '7')->orderby('muni')->get();
            else  $municipio = DB::table('tbl_municipios')->select('muni','id')->where('id_estado', '7')->whereJsonContains('unidad_disponible',$request->uni)->orderby('muni')->get();

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
        // $objeto_curp = array('url' => ''); //Para json doc_soporte
        if ($_SESSION['folio_grupo'] == $request->folio_grupo) {
        $curp = $request->busqueda;    //dd($request->all());
        $matricula = $message = NULL;
        $horas = round((strtotime($request->hfin) - strtotime($request->hini)) / 3600, 2);

        if ($request->tcurso == "CERTIFICACION" and $horas == 10 or $request->tcurso == "CURSO") {
            if ($curp) {
                $a_reg = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->where('eliminado',false)->first();
                if($a_reg) $date = $a_reg->inicio;
                else $date = $request->inicio;
                $alumno = DB::table('alumnos_pre')
                    ->select('id as id_pre', 'matricula', DB::raw("cast(EXTRACT(year from(age('$date', fecha_nacimiento))) as integer) as edad"),'ultimo_grado_estudios as escolaridad',
                    'nombre','apellido_paterno','apellido_materno','requisitos','curso_extra')
                    ->where('curp', $curp)->where('activo', true)->first(); //dd($alumno);
                $valida_alumno = $this->valida_alumno($curp, $request);
                if ($valida_alumno['valido']) {//Validación del alummnos en multiples criterios.
                //if ($alumno) {
                    if ($alumno->escolaridad AND ($alumno->escolaridad != ' ')) {
                        if ($alumno->edad >= 15) {
                            // $alumnoAlfa = json_decode($alumno->datos_alfa);
                            // if($alumnoAlfa->switch_alfa) {
                                $cursos = DB::table(DB::raw("(select a.id_curso as curso from alumnos_registro as a
                                                                inner join alumnos_pre as ap on a.id_pre = ap.id
                                                                where ap.curp = '$curp'
                                                                and a.eliminado = false
                                                                and extract(year from a.inicio) = extract(year from current_date)) as t"))
                                    ->select(DB::raw("count(curso) as total"), DB::raw("count(case when curso = '$request->id_curso' then curso end) as igual"))
                                    ->first(); //dd($cursos);
                                if ($cursos->total < 16 OR $alumno->curso_extra==true) {
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
                                    #Consultar doc url Curp json by Jose Luis Moreno Arcos
                                    // if($curp){
                                    //     try {
                                    //         $resul_alumnos = Alumnopre::where('curp', '=', $curp)->first();
                                    //         if ($resul_alumnos && isset($resul_alumnos->requisitos['documento'])) {
                                    //             $objeto_curp = ['url' => $resul_alumnos->requisitos['documento']];
                                    //         } else {
                                    //             $objeto_curp = ['url' => ''];
                                    //         }
                                    //     } catch (\Throwable $th) {
                                    //         // Manejar la excepción según sea necesario
                                    //     }
                                    // }

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
                                        $instructor = !empty($a_reg->id_instructor) ? $a_reg->id_instructor : 1;
                                        $efisico = $a_reg->efisico;
                                        $medio_virtual = $a_reg->medio_virtual;
                                        $link_virtual = $a_reg->link_virtual;
                                        $servicio = $a_reg->servicio;
                                        $cespecifico = $a_reg->cespecifico;
                                        $fcespe = $a_reg->fcespe;
                                        $observaciones = $a_reg->observaciones;
                                        $depen_repre = $a_reg->depen_repre;
                                        $depen_telrepre = $a_reg->depen_telrepre;
                                        $realizo = $a_reg->realizo;
                                        $iduser_created = $a_reg->iduser_created;
                                        // $jsoncurp = $objeto_curp; //Doc Curp
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
                                        $realizo = $this->realizo;
                                        $iduser_created =  $this->id_user;
                                        // $jsoncurp = $objeto_curp; //Doc Curp
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
                                                            'cct' => $this->data['cct_folio'], 'realizo' => $realizo, 'no_control' => $matricula, 'ejercicio' => $this->ejercicio, 'id_muni' => $id_muni,
                                                            'folio_grupo' => $_SESSION['folio_grupo'], 'iduser_created' => $iduser_created, 'comprobante_pago' => $comprobante_pago,
                                                            'created_at' => date('Y-m-d H:i:s'), 'fecha' => date('Y-m-d'), 'id_cerss' => $id_cerss, 'cerrs' => $cerrs, 'mod' => $modalidad,
                                                            'grupo' => $_SESSION['folio_grupo'], 'eliminado' => false, 'grupo_vulnerable' => $grupo_vulnerable, 'id_vulnerable' => $id_vulnerable,
                                                            'folio_pago'=>$folio_pago, 'fecha_pago'=>$fecha_pago, 'nombre'=>$alumno->nombre, 'apellido_paterno'=>$alumno->apellido_paterno,
                                                            'apellido_materno'=>$alumno->apellido_materno,'curp'=>$curp,'escolaridad'=>$alumno->escolaridad,
                                                            'id_instructor'=>$instructor,'efisico'=>$efisico,'medio_virtual'=>$medio_virtual,'link_virtual'=>$link_virtual,'servicio'=>$servicio,'cespecifico'=>$cespecifico,
                                                            'fcespe'=>$fcespe, 'observaciones'=>$observaciones, 'depen_repre'=>$depen_repre, 'depen_telrepre'=>$depen_telrepre, 'requisitos'=> $alumno->requisitos
                                                        ]
                                                    );
                                                    if ($result){
                                                        $message = "Operación Exitosa!!";
                                                        if($alumno->curso_extra==true) DB::table('alumnos_pre')->where('id',$alumno->id_pre)->where('curso_extra',true)->update(['curso_extra'=>false]);
                                                    }
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
                            // }else {
                            //     $message = "El alumno no es ALFA.";
                            // }
                        } else {
                            $message = "La edad del alumno no es valida";
                        }
                    } else {
                        $message = "Ingrese la escolaridad al Alumno " . $curp . ".";
                    }

                } else {
                    $message = $valida_alumno['message'];
                }
            } else $message = "Ingrese la CURP";
        } else {
            $message  = "Si es una CERTIFICACIÓN, corrobore que cubra 10 horas.";
        }
        }else $message = "La acción no se ejecuto correctamente, favor de intentar de nuevo.";
        return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
    }

    public function update(Request $request)
    {
        //dd($request->all()); dd($request->folio_grupo);
         $message = "Operación fallida, por favor intente de nuevo!!";
        if ($_SESSION['folio_grupo'] == $request->folio_grupo) {

            $horas = round((strtotime($request->hfin) - strtotime($request->hini)) / 3600, 2);
            if ($request->tcurso == "CERTIFICACION" and $horas == 10 or $request->tcurso == "CURSO") {
               // if ((((explode('-',$request->inicio))[0]) == date('Y')) AND ((explode('-',$request->termino))[0]) == date('Y')) {
                    if ($request->inicio <= $request->termino) {
                        $folio = $_SESSION['folio_grupo'];
                        $mapertura = $request->mapertura;
                        $tc_curso = DB::table('tbl_cursos')->where('unidad', $request->unidad)->where('folio_grupo', $_SESSION['folio_grupo'])->select('id','status_curso','created_at','id_instructor','cp','folio_grupo')->first();
                        if(!$tc_curso) { $tc_curso = new \stdClass(); $tc_curso->id = $tc_curso->status_curso = $tc_curso->created_at = $tc_curso->id_instructor = $tc_curso->cp = null;}


                        if ($mapertura AND $tc_curso->status_curso!='EDICION' AND (DB::table('alumnos_registro')->where('mpreapertura',$mapertura)->where('turnado','<>','VINCULACION')->exists())) {
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

                                $unidades_toarray = $_SESSION['unidades']->values()->toArray();
                                $convenio_t = DB::table('convenios')
                                    ->select('no_convenio',db::raw("to_char(DATE (fecha_firma)::date, 'YYYY-MM-DD') as fecha_firma"))
                                    ->where(db::raw("to_char(DATE (fecha_vigencia)::date, 'YYYY-MM-DD')"),'>=',$request->termino)
                                    ->where('tipo_convenio','GENERAL')
                                    ->where(function ($q) use ($unidades_toarray) {
                                        foreach ($unidades_toarray as $unidad) {
                                            $q->orWhereJsonContains('unidades', $unidad);
                                        }
                                    })
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
                            $unidad = DB::table('tbl_unidades')->select('id','cct', 'plantel','ubicacion')->where('unidad', $request->unidad)->first();
                            $id_ubicacion = DB::table('tbl_unidades')->where('unidad', $unidad->ubicacion)->value('id');
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
                                    'folio_ine','domicilio','archivo_domicilio','archivo_ine','archivo_bancario','rfc','archivo_rfc',
                                    'banco','no_cuenta','interbancaria','tipo_honorario'
                                    )
                                //->WHERE('estado', true)
                                //->WHERE('instructores.status', '=', 'VALIDADO')->where('instructores.nombre', '!=', '')
                                ->where('instructores.id', $request->instructor)
                                //->whereJsonContains('unidades_disponible', [$grupo->unidad])
                                //->WHERE('especialidad_instructores.especialidad_id', $id_especialidad)
                                //->WHERE('especialidad_instructores.activo', 'true')
                                //->WHERE('fecha_validacion','<',$request->inicio)
                                //->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',$request->termino)
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
                                if (!(DB::table('exoneraciones')->where('folio_grupo',$_SESSION['folio_grupo'])->where('status','!=', 'CAPTURA')->where('status','!=','CANCELADO')->exists())){
                                    foreach ($request->costo as $key => $pago) {
                                        $pago = $pago ?: 0; // Si $pago es null, asigna 0.
                                        $diferencia = $costo_individual - $pago;
                                        $tinscripcion = "EXONERACION";
                                        $abrins = 'ET';
                                        if ($pago > 0) {
                                            $tinscripcion = ($diferencia > 0) ? "REDUCCION DE CUOTA" : "PAGO ORDINARIO";
                                            $abrins = ($diferencia > 0) ? 'EP' : 'PI';
                                        }
                                        Alumno::where('id', $key)->update(['costo' => $pago, 'tinscripcion' => $tinscripcion, 'abrinscri' => $abrins]);


                                        ///SI CAMBIA DE TIPO DE PAGO Y REDUCCION CANCELADA=> SE ACTUALIZA LOS COSTOS EN tbl_inscriçion
                                        if (($tc_curso->status_curso == 'EDICION') AND $_SESSION['folio_grupo']) {
                                            DB::table('tbl_inscripcion')
                                            ->join('alumnos_registro', 'tbl_inscripcion.matricula', '=', 'alumnos_registro.no_control')
                                            ->where('tbl_inscripcion.folio_grupo', $_SESSION['folio_grupo'])
                                            ->where('alumnos_registro.id', $key)
                                            ->update([
                                                'tbl_inscripcion.costo' => $pago,
                                                'tbl_inscripcion.tinscripcion' => $tinscripcion,
                                                'tbl_inscripcion.abrinscri' => $abrins
                                            ]);
                                        }
                                    }
                                }

                                $sx = DB::table('alumnos_registro')->select(
                                    DB::raw("COUNT(curp) as total"),DB::raw("SUM(CASE WHEN substring(curp,11,1) ='H' THEN 1 ELSE 0 END) as hombre"),
                                    DB::raw("SUM(CASE WHEN substring(curp,11,1) ='M' THEN 1 ELSE 0 END) as mujer"),
                                    DB::raw("SUM(costo) as costo")
                                    )->where('folio_grupo',$_SESSION['folio_grupo'])->first();

                                //TOTAL PAGADO
                                $total_pago = $sx->costo*1;
                                //COSTO TOTAL DEL CURSO
                                $costo_total = $curso->costo * $sx->total;
                                //DIFERENCIA COSTO - PAGADO
                                $ctotal = $costo_total - $total_pago;

                                if ($total_pago == 0) {
                                    $tipo_pago = "EXO";
                                    //if ($cp > 7) $cp = 7; //EXONERACION Criterio de Pago Máximo 7
                                } elseif ($ctotal > 0) $tipo_pago = "EPAR";
                                else $tipo_pago = "PINS";
                                /*ID DEL CURSO DE 10 DIGITOS*/
                                $PRE = date("y") . $unidad->plantel;

                                $ID = $tc_curso->id;
                                if (!$ID) $ID = DB::table('tbl_cursos')->where('unidad', $request->unidad)->where('id', 'like', $PRE . '%')->value(DB::raw('max(id)+1'));
                                if (!$ID) $ID = $PRE . '0001';

                                if ($request->tcurso == "CERTIFICACION") {
                                    $horas = $dura = 10;
                                    $termino =  $request->inicio;
                                } else {
                                    $dura = $curso->horas;
                                    $termino =  $request->termino;
                                }

                                $created_at = $tc_curso->created_at;
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
                                $soportes_instructor = ["domicilio"=>$instructor->domicilio, "archivo_domicilio"=>$instructor->archivo_domicilio,
                                "archivo_ine"=>$instructor->archivo_ine,"archivo_bancario"=>$instructor->archivo_bancario,"archivo_rfc"=>$instructor->archivo_rfc,
                                'banco'=>$instructor->banco,'no_cuenta'=>$instructor->no_cuenta,'interbancaria'=>$instructor->interbancaria,'tipo_honorario'=>$instructor->tipo_honorario];

                                $alus = DB::table('alumnos_registro')->where('folio_grupo',$folio)->first();

                                $result_curso = $result_alumnos = null;
                                if($tc_curso->status_curso=='EDICION'){
                                        $result_curso = DB::table('tbl_cursos')->where('folio_grupo', $_SESSION['folio_grupo'])->Update(
                                            ['inicio' => $request->inicio,'termino' => $termino,'hini' => $hini,'hfin' => $hfin,'horas' => $horas,
                                            'id_organismo' => $id_organismo,
                                            'depen' => $request->dependencia,'muni' => $municipio->muni,'sector' => $sector,
                                            'efisico' => str_replace('ñ','Ñ',strtoupper($request->efisico)),'cespecifico' => $request->cespecifico,
                                            'hombre' => $sx->hombre, 'mujer' => $sx->mujer,'tipo' => $tipo_pago,'fcespe' => $request->fcespe,
                                            'cgeneral' => $convenio['no_convenio'],'fcgen' => $convenio['fecha_firma'],
                                            'ze' => $municipio->ze,'realizo' => strtoupper($this->realizo),'tcapacitacion' => $request->tipo,'costo' => $total_pago,
                                            'medio_virtual' => $request->medio_virtual,'link_virtual' => $request->link_virtual,
                                            'id_municipio' => $municipio->id,'clave_localidad' => $request->localidad,'id_gvulnerable' => $request->grupo_vulnerable,
                                            'id_cerss' => $request->cerss,'created_at' => $created_at,'updated_at' => $updated_at,
                                            'comprobante_pago' => $url_comprobante,'folio_pago' => $request->folio_pago,'fecha_pago' => $request->fecha_pago,
                                            'depen_representante'=>$depen_repre,'depen_telrepre'=>$depen_telrepre,'nplantel'=>$unidad->plantel,
                                            'programa'=>$request->programa, 'plantel'=>$request->plantel
                                            ]);

                                        if($tc_curso->cp == $cp AND $result_curso){
                                            $result_curso = DB::table('tbl_cursos')->where('folio_grupo', $_SESSION['folio_grupo'])->Update(
                                                [
                                                'id_instructor' => $instructor->id,'modinstructor' => $tipo_honorario,
                                                'nombre' => $instructor->instructor,'curp' => $instructor->curp,'rfc' => $instructor->rfc,
                                                'instructor_escolaridad' => $instructor->escolaridad,'instructor_titulo' => $instructor->titulo,'instructor_sexo' => $instructor->sexo,
                                                'instructor_mespecialidad' => $instructor->mespecialidad,'instructor_tipo_identificacion' => $instructor->tipo_identificacion,
                                                'instructor_folio_identificacion' => $instructor->folio_ine,'soportes_instructor'=>json_encode($soportes_instructor)
                                                ]);
                                        }
                                        if($result_curso){
                                            $result_alumnos = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->Update(
                                                [
                                                    'clave_localidad' => $request->localidad, 'organismo_publico' => $request->dependencia,
                                                    'horario' => $horario, 'tipo_curso' => $request->tipo,'iduser_updated' => $this->id_user, 'updated_at' => date('Y-m-d H:i:s'),
                                                    'id_muni' => $municipio->id,'inicio' => $request->inicio, 'termino' => $termino,
                                                    'id_organismo' => $id_organismo, 'id_vulnerable' => $request->grupo_vulnerable,
                                                    'id_cerss' => $request->cerss, 'cerrs' => $cerrs, 'id_muni' => $municipio->id, 'grupo_vulnerable' => $grupo_vulnerable,
                                                    'comprobante_pago' => $url_comprobante,'folio_pago'=>$request->folio_pago, 'fecha_pago'=>$request->fecha_pago,
                                                    'medio_virtual' => $request->medio_virtual,'link_virtual' => $request->link_virtual, 'efisico'=>str_replace('ñ','Ñ',strtoupper($request->efisico)),
                                                    'id_instructor'=>$instructor->id,'cespecifico'=>$request->cespecifico,'fcespe'=>$request->fcespe,
                                                    'depen_repre'=>$depen_repre, 'depen_telrepre'=>$depen_telrepre
                                                ]
                                            );
                                            if($result_alumnos) $message = "Operación Exitosa!!";
                                        }
                                }elseif (DB::table('exoneraciones')->where('folio_grupo',$_SESSION['folio_grupo'])->where('status','!=', 'CAPTURA')->where('status','!=','CANCELADO')->exists()) {
                                    $result_alumnos = DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->where('turnado','VINCULACION')->update(
                                        ['id_instructor'=>$instructor->id, 'observaciones'=>$request->observaciones,'updated_at' => date('Y-m-d H:i:s'), 'iduser_updated' => $this->id_user, 'comprobante_pago' => $url_comprobante,
                                        'folio_pago'=>$request->folio_pago, 'fecha_pago'=>$request->fecha_pago,'mpreapertura'=>$mapertura,'depen_repre'=>$depen_repre, 'depen_telrepre'=>$depen_telrepre,
                                        'cespecifico'=>$request->cespecifico,'fcespe'=>$request->fcespe,'medio_virtual' => $request->medio_virtual,'link_virtual' => $request->link_virtual]);

                                    if ($result_alumnos) {
                                        $result_curso = DB::table('tbl_cursos')->where('folio_grupo',$_SESSION['folio_grupo'])->where('id',$ID)
                                            ->update(['comprobante_pago' => $url_comprobante,
                                            'folio_pago' => $request->folio_pago,'fecha_pago' => $request->fecha_pago, 'updated_at' => date('Y-m-d H:i:s'),
                                            'id_organismo' => $id_organismo,
                                            'depen_representante'=>$depen_repre,'depen_telrepre'=>$depen_telrepre,'cespecifico' => $request->cespecifico,'fcespe' => $request->fcespe,
                                            'medio_virtual' => $request->medio_virtual,'link_virtual' => $request->link_virtual,
                                            'id_instructor' => $instructor->id,'nombre' => $instructor->instructor,'modinstructor' => $tipo_honorario,
                                            'curp' => $instructor->curp,'rfc' => $instructor->rfc,'modinstructor' => $tipo_honorario,'instructor_escolaridad' => $instructor->escolaridad,
                                            'instructor_titulo' => $instructor->titulo,'instructor_sexo' => $instructor->sexo,'instructor_mespecialidad' => $instructor->mespecialidad,
                                            'instructor_tipo_identificacion' => $instructor->tipo_identificacion,'instructor_folio_identificacion' => $instructor->folio_ine,
                                            'soportes_instructor'=>json_encode($soportes_instructor),'cp' => $cp,
                                            'programa'=>$request->programa, 'plantel'=>$request->plantel,'costo' => $total_pago, 'status_solicitud' =>null
                                        ]);
                                      //  dd($instructor);
                                        if ($result_curso) $message = "Operación Exitosa!!";
                                    }

                                } else {

                                    $result_alumnos = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->where('turnado','VINCULACION')->Update(
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
                                    if ($result_alumnos) {
                                        $result_curso = DB::table('tbl_cursos')->where('clave', '0')->updateOrInsert(['folio_grupo' => $_SESSION['folio_grupo']],
                                            ['id' => $ID, 'cct' => $unidad->cct,'unidad' => $request->unidad,'nombre' => $instructor->instructor,'curp' => $instructor->curp,
                                            'rfc' => $instructor->rfc,'clave' => '0','mvalida' => '0','mod' => $request->modalidad,'area' => $curso->area,'espe' => $curso->espe,'curso' => $curso->nombre_curso,
                                            'inicio' => $request->inicio,'termino' => $termino,'dura' => $dura,'hini' => $hini,'hfin' => $hfin,'horas' => $horas,'ciclo' => $ciclo,
                                            'id_organismo' => $id_organismo,'depen' => $request->dependencia,'muni' => $municipio->muni,'sector' => $sector,
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
                                            'id_cerss' => $request->cerss,'created_at' => $created_at,'updated_at' => $updated_at,
                                            'instructor_tipo_identificacion' => $instructor->tipo_identificacion,'instructor_folio_identificacion' => $instructor->folio_ine,
                                            'comprobante_pago' => $url_comprobante,'folio_pago' => $request->folio_pago,'fecha_pago' => $request->fecha_pago,'depen_representante'=>$depen_repre,
                                            'depen_telrepre'=>$depen_telrepre,'nplantel'=>$unidad->plantel, 'soportes_instructor'=>json_encode($soportes_instructor),
                                            'id_unidad'=>$id_ubicacion,'munidad' => null,'num_revision' => null,
                                            'programa'=>$request->programa, 'plantel'=>$request->plantel, 'status_solicitud' =>null
                                            //,'programa' => null,'nota' => null,'plantel' => null
                                            ]
                                        );
                                        if($result_curso)$message = "Operación Exitosa!!";
                                    }
                                }
                                ///AGREGAR PARA TODOS LOS CRITERIOS
                                if ($result_alumnos) {
                                    if (($horario <> $alus->horario) OR ($request->id_curso <> $alus->id_curso) OR ($instructor->id <> $alus->id_instructor) OR
                                    ($request->inicio <> $alus->inicio) OR ($termino <> $alus->termino) OR ($id_especialidad <> $alus->id_especialidad)) {                                        
                                        DB::table('agenda')->where('id_curso', $folio)->delete();
                                        DB::table('tbl_cursos')->where('folio_grupo',$folio)->update(['dia' => '', 'tdias' => 0]);                                        
                                    }
                                }

                            } else {
                                $message = 'Instructor no valido..';
                            }
                        }
                    } else {
                        $message = 'La fecha de termino no puede ser menor a la de inicio';
                    }
                //} else {
                    //$message = 'El año de la fecha de inicio o de termino no coincide con el actual';
                //}
            } else {
                $message  = "Si es una CERTIFICACIÓN, corrobore que cubra 10 horas.";
            }
        } else $message = "La acción no se ejecuto correctamente";
        return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
    }

    public function genera_folio()
    {
        $consec = DB::table('alumnos_registro')->where('ejercicio', $this->ejercicio)->where('cct', $this->data['cct_folio'])->where('eliminado', false)->value(DB::RAW("cast(substring(max(folio_grupo) from '.{4}$') as int)")) + 1;
        $consec = str_pad($consec, 4, "0", STR_PAD_LEFT);
        $folio = $this->data['cct_folio'] . "-" . $this->ejercicio . $consec;
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
                            //VOBO $instructor_valido = $this->valida_instructor($g->id_instructor);
                            //VOBO if($instructor_valido['valido']){
                                if($g->status_curso=="EDICION"){
                                    if($g->clave !='0') $result = DB::table('tbl_cursos')->where('folio_grupo', $_SESSION['folio_grupo'])->where('clave','!=','0')->update(['status_curso' => 'AUTORIZADO']);
                                    else $result = DB::table('tbl_cursos')->where('folio_grupo', $_SESSION['folio_grupo'])->where('clave','0')->update(['status_curso' => null]);

                                    if(!$result)return redirect()->route('preinscripcion.grupo')->with(['message' => 'El curso no fue turnado correctamente. Por favor de intente de nuevo']);

                                }else{
                                    $result = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->update(['turnado' => 'UNIDAD', 'fecha_turnado' => date('Y-m-d')]);
                                    if($result) DB::table('instructores')->where('id',$g->id_instructor)->where('curso_extra',true)->update(['curso_extra'=>false]);
                                    else return redirect()->route('preinscripcion.grupo')->with(['message' => 'El curso no fue turnado correctamente. Por favor de intente de nuevo']);
                                }
                            //VOBO }else return redirect()->route('preinscripcion.grupo')->with(['message' => $instructor_valido['message']]);
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
        if ($id and $_SESSION['folio_grupo']) {
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
        if ($file and $_SESSION['folio_grupo'] == $request->folio_grupo) {
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
        $message = tbl_inscripcion::reemplazar($request->folio_grupo, $request->curp_anterior, $request->curp_nueva);
        return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
    }


/*
    public function remplazar(Request $request){
        $message = 'Operación fallida, vuelva a intentar..';
        if ($_SESSION['folio_grupo'] == $request->folio_grupo) {
            if ($request->busqueda1 AND $request->curpo) {
                $id = DB::table('alumnos_registro as ar')
                    ->leftJoin('alumnos_pre as ap','ar.id_pre','=','ap.id')
                    ->where('ar.folio_grupo',$_SESSION['folio_grupo'])
                    ->where('ap.curp',$request->curpo)
                    ->value('ar.id');
                if ($id) {
                    $date = date('d-m-Y');
                    $alumno_nuevo = DB::table('alumnos_pre')
                        ->select('id as id_pre','curp', 'matricula', DB::raw("cast(EXTRACT(year from(age('$date', fecha_nacimiento))) as integer) as edad"),
                         DB::raw("cast(EXTRACT(year from(age('$date', fecha_nacimiento))) as integer) as edad"),'ultimo_grado_estudios as escolaridad','nombre','apellido_paterno','apellido_materno'
                        )
                        ->where('curp', $request->busqueda1)
                        ->where('activo', true)
                        ->first();
                    if ($alumno_nuevo) {
                        if ($alumno_nuevo->edad >= 15) {
                            if (substr($request->curpo, 10, 1) == substr($request->busqueda1, 10, 1)) {
                                $result = DB::table('alumnos_registro')
                                    ->where('folio_grupo', $_SESSION['folio_grupo'])
                                    ->where('id', $id)
                                    ->update([
                                        'id_pre' => $alumno_nuevo->id_pre, 'no_control' => $alumno_nuevo->matricula,
                                        'nombre'=>$alumno_nuevo->nombre, 'apellido_paterno'=>$alumno_nuevo->apellido_paterno,
                                        'apellido_materno'=>$alumno_nuevo->apellido_materno,'curp'=>$alumno_nuevo->curp,
                                        'escolaridad'=>$alumno_nuevo->escolaridad,
                                        'iduser_updated' => Auth::user()->id,
                                        'updated_at' => date('Y-m-d H:i')
                                    ]);
                                if ($result) {
                                    //ACTUALIZAR tbl_inscripcion



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
*/
    public function generar(){
        if ($_SESSION['folio_grupo']) {
            $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first();
            $alumnos = DB::table('alumnos_registro as ar')
                ->select(
                        DB::raw('ar.apellido_paterno'),
                        DB::raw('ar.apellido_materno'),
                        DB::raw('ar.nombre'),
                        DB::raw('COALESCE(substring(ti.curp,11,1), substring(ar.curp,11,1)) as sexo'),
                        DB::raw("extract(year from (age(ar.inicio,COALESCE(ti.fecha_nacimiento, ap.fecha_nacimiento)))) as edad"),
                        'ap.correo',
                        DB::raw('COALESCE(ti.curp, ar.curp) as curp'),
                        DB::raw('COALESCE(tc.curso, c.nombre_curso) as nombre_curso'),
                        DB::raw('COALESCE(tc.dura, c.horas) as horas'),
                        DB::raw("to_char(DATE (ar.inicio)::date, 'dd/mm/YYYY') as inicio"),
                        DB::raw("to_char(DATE (ar.termino)::date, 'dd/mm/YYYY') as termino"),
                        'ar.horario',
                        'ar.mod', 'ar.costo','ar.tipo_curso','ar.organismo_publico as depe'
                        )
                ->leftJoin('alumnos_pre as ap','ar.id_pre','ap.id')
                ->leftJoin('cursos as c','ar.id_curso','c.id')
                ->leftJoin('tbl_cursos as tc','tc.folio_grupo','ar.folio_grupo')
                ->leftJoin('tbl_inscripcion as ti', function($join) {
                    $join->on('ti.folio_grupo', '=', 'ar.folio_grupo')
                         ->on('ti.curp', '=', 'ar.curp');
                })
                ->where('ar.folio_grupo',$_SESSION['folio_grupo'])
                ->orderBy('apellido_paterno','asc')
                ->orderBy('apellido_materno','asc')
                ->orderBy('nombre','asc')
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
            $alum = DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->where('eliminado',false)->first();


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
                        'tc.depen_telrepre as tel_repre','tc.nombre','ar.realizo as vincu','ar.observaciones as nota_vincu','ar.efisico','tc.unidad','ar.fecha_turnado','tc.solicita',
                        DB::raw('COALESCE(tc.vb_dg, false) as vb_dg'), //NUEVO VOBO
                        DB::raw("COALESCE(tc.clave, '0') as clave") //NUEVO VOBO
                    )
                    ->leftJoin('alumnos_registro as ar', 'tc.folio_grupo', 'ar.folio_grupo')
                    ->where('ar.mpreapertura', $memo)
                    ->where('ar.eliminado', false)
                    ->groupBy('tc.folio_grupo','tc.tipo_curso','tc.espe','tc.curso','tc.mod','tc.tcapacitacion','tc.dura','tc.inicio','tc.termino','ar.horario','tc.dia','tc.horas',
                    'tc.costo','tc.hombre','tc.mujer','tc.mexoneracion','tc.cgeneral','tc.cespecifico','tc.depen','tc.depen_representante','tc.depen_telrepre','tc.nombre','ar.realizo',
                    'ar.observaciones','ar.efisico','tc.unidad','ar.fecha_turnado','tc.solicita',
                    'tc.vb_dg','tc.clave' //NUEVO VOBO
                    )
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
                        $data[$key]['vb_dg'] = $value->vb_dg; //NUEVO VOBO
                        $data[$key]['clave'] = $value->clave; //NUEVO VOBO
                    }
                }// dd($cursos[0]->fecha_turnado);
                if (count($data) > 0) {
                    $meses = ['01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre'];
                    if($cursos[0]->fecha_turnado>0) $date = $cursos[0]->fecha_turnado;
                    $mes = $meses[date('m',strtotime($date))];
                    $date = date('d',strtotime($date)).' de '.$mes.' del '.date('Y',strtotime($date));
                    $reg_unidad = DB::table('tbl_unidades')->where('unidad', $unidad)->first(); //dd($reg_unidad);
                    if($reg_unidad->vinculacion==$reg_unidad->dunidad and $cursos[0]->solicita){
                        $solicitaParts = explode(",", $cursos[0]->solicita);
                        $nombre = isset($solicitaParts[0]) ? $solicitaParts[0] : 'Nombre no disponible';
                        $cargo = isset($solicitaParts[1]) ? $solicitaParts[1] : 'Cargo no disponible';
                        $reg_unidad->vinculacion = mb_strtoupper($nombre, 'UTF-8');
                        $reg_unidad->pvinculacion = mb_strtoupper($cargo, 'UTF-8');
                    }
                    $direccion = $reg_unidad->direccion;
                    //dd($data);
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


    public function showCalendar($id){
        $folio = $id;
        $data['agenda'] =  Agenda::where('id_curso','=',$folio)->get();
        return response()->json($data['agenda']);
    }

    public function deleteCalendar(Request $request){
        $id = $request->id;
        $id_curso = DB::table('agenda')->where('id',$id)->value('id_curso');
        /*
        if (DB::table('exoneraciones')->where('folio_grupo',$id_curso)->where('status','!=', 'CAPTURA')->where('status','!=','CANCELADO')->exists()) {
            $message = "Solicitud de Exoneración o Reducción de couta en Proceso..";
            return redirect()->route('preinscripcion.grupo')->with(['message' => $message]);
        } else {
            */
            Agenda::destroy($id);
            $dias = $this->dias($id_curso);
            $result = DB::table('tbl_cursos')->where('folio_grupo',$id_curso)->update(['dia' => $dias['nombre'], 'tdias' => $dias['total']]);
            return response()->json($id);
       // }
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
        /*
        if (DB::table('exoneraciones')->where('folio_grupo',$id_curso)->where('status','!=', 'CAPTURA')->where('status','!=','CANCELADO')->exists()) {
            return "Solicitud de Exoneración o Reducción de couta en Proceso..";
        }
        */
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

        /* VOBO
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
        FIN VOBO*/
        try {
            $titulo = $request->title;
            $agenda = new Agenda();
            $agenda->title = $titulo;
            $agenda->start = date("Y-m-d H:i:s", strtotime($request->start));
            $agenda->end = date("Y-m-d H:i:s", strtotime($request->end));
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

    public function dias($id)
    {
        $dias_agenda = DB::table('agenda')
            ->select(
                DB::raw("extract(dow from (generate_series(agenda.start, agenda.end, '1 day'::interval))) as dia"),
                DB::raw("generate_series(agenda.start, agenda.end, '1 day'::interval)::date as fecha")
            )
            ->where('id_curso', $id)
            ->orderBy('fecha')
            ->get();

        if (count($dias_agenda) > 0) {
            $grupos = [];
            $inicio = null;
            $fin = null;
            $prev = null;

            foreach ($dias_agenda as $item) {
                $fecha = $item->fecha;
                $dia = $item->dia;

                if ($inicio === null) {
                    $inicio = $fin = $item;
                } else {
                    $prev_fecha = new \DateTime($prev->fecha);
                    $curr_fecha = new \DateTime($fecha);
                    $diff = $prev_fecha->diff($curr_fecha)->days;

                    if ($diff == 1) {
                        // Son consecutivos, extendemos el rango
                        $fin = $item;
                    } else {
                        // No son consecutivos, guardamos el rango anterior
                        $grupos[] = [$inicio, $fin];
                        $inicio = $fin = $item;
                    }
                }
                $prev = $item;
            }
            // Guardar el último rango
            $grupos[] = [$inicio, $fin];

            // Construir la cadena de días
            $dias_a = [];
            foreach ($grupos as $rango) {
                $dia_ini = $this->dia($rango[0]->dia);
                $dia_fin = $this->dia($rango[1]->dia);

                if ($rango[0]->fecha == $rango[1]->fecha) {
                    $dias_a[] = $dia_ini;
                } else {
                    // Si solo son dos días consecutivos, usar "Y"
                    $fecha1 = new \DateTime($rango[0]->fecha);
                    $fecha2 = new \DateTime($rango[1]->fecha);
                    if ($fecha1->diff($fecha2)->days == 1) {
                        $dias_a[] = $dia_ini . " Y " . $dia_fin;
                    } else {
                        $dias_a[] = $dia_ini . " A " . $dia_fin;
                    }
                }
            }
            $dias_str = implode(", ", $dias_a);
        } else {
            $dias_str = 0;
        }

        // Calcular el total de días
        $total_dias = DB::table('agenda')
            ->select(DB::raw("(generate_series(agenda.start, agenda.end, '1 day'::interval))::date as dias"))
            ->where('id_curso', $id)
            ->orderBy('dias')
            ->pluck('dias');
        $tdias = count($total_dias);

        $insert_dias['nombre'] = $dias_str;
        $insert_dias['total'] = $tdias;
        return $insert_dias;
    }

    private function valida_instructor($id_instructor)
    {
        //return ['valido' => true, 'message' => null]; //QUITAR ESTA LINEA EL 01 de JULIO 2024
        //echo $id_instructor;
        $valido = false;
        $message = null; //consultar instructores con id y que devuelva campo extra sea igual a true ya que lo devuelva un if si curso extra es igual a false entra a la validacion y si es true entonces cambie valido a true

        $curso_extra = DB::TABLE('instructores')->WHERE('id',$id_instructor)->value('curso_extra');

        if($curso_extra == false)
        {
            ///VALIDACION DE INSTRUCTORES INTERNOS
            $internos = DB::table('instructores as i')->select('i.id')->join('tbl_cursos as c','c.id_instructor','i.id') ->where('i.id',$id_instructor)
                ->where('i.tipo_instructor', 'INTERNO')->where('curso_extra',false)
                ->where(DB::raw("EXTRACT(YEAR FROM c.inicio)"),date('Y'))
                ->where(DB::raw("EXTRACT(MONTH FROM c.inicio)"),date('m'))
                ->where(function($query){
                    $query->where('c.status_curso','<>','CANCELADO')->orWherenull('c.status_curso');
                })
                ->havingRaw('count(*) > 2')
                ->groupby('i.id')->first();
                //var_dump($internos);exit;
            if($internos) $message = "El instructor interno ha excedido el número de cursos a impartir (máximo 2 cursos al mes). Favor de verificar.";
            else $valido = true;

            ///VALIDACIÓN 5 meses de actividad y 30 días naturales de RECESO
            if($valido==true){
                $receso =  DB::table('tbl_cursos as tc')->where('id_instructor',$id_instructor)
                ->where(function($query){
                    $query->where('tc.status_curso','<>','CANCELADO')->orWherenull('tc.status_curso');
                })
                ->where('tc.inicio','>',DB::raw("
                COALESCE(
                    (select max(inicio) from tbl_cursos as c where c.id_instructor = $id_instructor
                        and COALESCE((select DATE_PART('day', tc.inicio::timestamp - c.termino::timestamp )
                        from tbl_cursos as tc where tc.id_instructor = $id_instructor and tc.inicio>c.inicio order by tc.inicio ASC limit 1  )-1,0)>=30 )
                        , (select min(inicio)::timestamp - interval '1 day' from tbl_cursos where id_instructor = $id_instructor))
                "))
                ->value(DB::raw("DATE_PART('day', max(tc.termino)::timestamp - min(tc.inicio)::timestamp)+1"));
                //dd($receso);
                if($receso>150){
                    $valido = false;
                    $message = "El instructor supera el límite de 150 días de actividad, deberá tomar un receso mínimo 30 días naturales.";
                }
            }
        }
        else
        {
            $valido = true;
        }
        return ['valido' => $valido, 'message' => $message];
    }

    private function valida_alumno($curp,$request)
    {
        $valido = false;
        $message = null;
        if($curp){
            //VALIDACION.- EL ALUMNO PODRÁ TOMAR EL MISMO CURSO DESPÚES DE 6 MESES DE CONCLUIRLO O POR DESERCIÓN LO PODRÁ TOMAR TANTAS VECES LO REQUIERA.
            $seis_meses =  DB::table('alumnos_registro as ar')->where('ar.curp',$curp)->where('ar.id_curso','=',$request->id_curso)
                ->where(DB::raw("COALESCE((select status_curso from tbl_cursos c where ar.folio_grupo = c.folio_grupo and ar.curp='$curp' ),'0')"),'!=','CANCELADO')
                ->where(DB::raw("COALESCE((select calificacion from tbl_inscripcion i where ar.folio_grupo = i.folio_grupo and i.curp='$curp'),'0')"),'!=','NP')
                ->value(DB::raw("max(ar.termino)+'6 month'::interval"));
               // dd($seis_meses);
            if($seis_meses<$request->inicio) $valido = true;
            else{
                $message = "El alumno ya esta registrado en el curso o no ha cumplido 6 meses para volver a tomar el mismo curso.";
            }
        }else  $message = "Por favor, ingrese la curp.";

        return ['valido' => $valido, 'message' => $message];
    }

    /**Jose Luis Generación PDF Convenio Especifico y Acta de acuerdo */

    public function pdf_actaAcuerdo(){
        $folio_grupo =  $_SESSION['folio_grupo'];

        //Busqueda 1,2,3
        $data1 = DB::table('tbl_cursos')->select( 'muni', 'fcespe', 'unidad', 'dia', 'hini', 'hfin', 'tcapacitacion', 'nombre', 'curso', 'cespecifico', 'inicio', 'termino', 'efisico', 'vb_dg', 'clave',
        DB::raw("extract(day from fcespe) as diaes, to_char(fcespe, 'TMmonth') as mes, extract(year from fcespe) as anio"),
        DB::raw("(hombre + mujer) as totalp"),
        DB::raw("extract(day from inicio) as diaini, to_char(inicio, 'TMmonth') as mesini, extract(year from inicio) as anioini"),
        DB::raw("extract(day from termino) as diafin, to_char(termino, 'TMmonth') as mesfin, extract(year from termino) as aniofin"))
        ->where('folio_grupo','=',"$folio_grupo")->first();


        //busqueda 4
        $data2 = DB::table('tbl_unidades as u')->select('dunidad','pdunidad', 'delegado_administrativo', 'pdelegado_administrativo', 'academico', 'pacademico', 'vinculacion', 'pvinculacion', 'direccion')
        ->Join('tbl_cursos as c', 'u.unidad', 'c.unidad')
        ->where('c.folio_grupo', $folio_grupo)->first();

        //Busqueda 6
        $data3 = DB::table('alumnos_registro as ar')->select('ar.nombre', 'ar.apellido_paterno', 'ar.apellido_materno', 'ar.folio_grupo', 'ar.costo', 'ar.curp', 'a.correo', 'a.telefono_personal', 'a.medio_confirmacion')
        ->Join('alumnos_pre as a', 'a.curp', 'ar.curp')
        ->orderBy('ar.nombre')
        ->where('folio_grupo','=',"$folio_grupo")->get();

        $direccion = $data2->direccion;


        $pdf = PDF::loadView('reportes.acta_acuerdo_registro_grupo',compact('data1', 'data2','data3', 'direccion'));
        // $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Acta_Acuerdo');
    }
    public function pdf_convenio(Request $request){
        $folio_grupo =  $_SESSION['folio_grupo'];
        $convenio_esp = DB::table('tbl_cursos')->select('cespecifico', 'firma_user', 'firma_cerss_one', 'firma_cerss_two')->where('folio_grupo','=',"$folio_grupo")->first();
        $conv_especifico = $convenio_esp->cespecifico;

        $bd_firm_user = $convenio_esp->firma_user; $bd_firm_cer1 = $convenio_esp->firma_cerss_one; $bd_firm_cer2 = $convenio_esp->firma_cerss_two;

        #OBTENEMOS VALORES DE FIRMA 1 O EN CASO DE ESTAR ACIVO CERSS SERIA DE LOS DOS CAMPOS EXTRA
        $valid_cerss = $request->valid_cerss;
        $campo_fir = $request->firma;
        $campo_firm1_cer = $request->firmaone;
        $campo_firm2_cer = $request->firmatwo;

        $part_firm_cer1 = $part_firm_cer2 = $part_firm_user = null;

        // dd($request->all());
        if($valid_cerss){
            #validamos los campos si contienen datos
            if($campo_firm1_cer != '' || $campo_firm2_cer != ''){
                #partimos el texto del campo a partir de la coma
                if($campo_firm1_cer != '')$part_firm_cer1 = explode(",", $campo_firm1_cer);
                $part_firm_cer2 = explode(",", $campo_firm2_cer);

                if($campo_firm1_cer != $bd_firm_cer1 || $campo_firm2_cer != $bd_firm_cer2){
                    #guardamos los datos de los campos cerss
                    DB::table('tbl_cursos')->where('cespecifico', $conv_especifico)
                    ->update(['firma_cerss_one' => $campo_firm1_cer, 'firma_cerss_two' => $campo_firm2_cer]);
                }
            }else{
                if($bd_firm_cer1 != null && $bd_firm_cer2 != null){
                    DB::table('tbl_cursos')->where('cespecifico', $conv_especifico)
                    ->update(['firma_cerss_one' => null, 'firma_cerss_two' => null]);
                }
            }

        }else{
            #validamos el campo si contiene dato
            if($campo_fir != ''){
                #partimos el texto a traves de la coma
                $part_firm_user = explode(",", $campo_fir);
                if($campo_fir != $bd_firm_user){
                    #guardamos el datos del campo firma
                    DB::table('tbl_cursos')->where('cespecifico', $conv_especifico)
                    ->update(['firma_user' => $campo_fir]);
                }
            }else{
                if($bd_firm_user != null){
                    DB::table('tbl_cursos')->where('cespecifico', $conv_especifico)
                    ->update(['firma_user' => null]);
                }
            }
        }

        #Consultamos los cursos vinculados con la clave de convenio
        $allcourses = DB::table('tbl_cursos')->select('clave','tcapacitacion','espe','curso', 'costo', 'dura', 'hini', 'hfin',
        'cespecifico', 'observaciones', 'folio_grupo', 'inicio', 'termino','hombre', 'mujer',
        DB::raw("extract(day from fcespe) as diaconvenio, to_char(fcespe, 'TMmonth') as mesconvenio, extract(year from fcespe) as anioconvenio"),
        DB::raw("extract(day from inicio) as diainic, extract(month from inicio) as mesinic, extract(year from inicio) as anioinic"),
        DB::raw("extract(day from termino) as diafinc, extract(month from termino) as mesfinc, extract(year from termino) as aniofinc"))
        ->where('cespecifico','=',"$conv_especifico")->get();

        // dd($allcourses);

        $array_folios = array();
        for ($i=0; $i < count($allcourses); $i++) {
            $folio = '';
            $folio = $allcourses[$i]->folio_grupo;
            $chainstr = substr($folio, 5, 4);
            $intchain = intval($chainstr) * 1;
            array_push($array_folios, $intchain);
        }

        #consulta con cerss y sin cerss
        if($valid_cerss){
            $data1 = DB::table('tbl_cursos as cur')->select( 'cur.muni', 'cur.fcespe', 'cur.fcgen', 'cur.dura', 'cur.unidad', 'cur.dia as letradia',
            'cur.hini', 'cur.hfin', 'cur.tcapacitacion', 'cur.nombre', 'cur.curso', 'cur.cespecifico',
            'cur.depen', 'cur.costo', 'cur.inicio', 'cur.termino', 'cur.observaciones','cur.id_cerss','cer.nombre as cernombre',
            'cer.direccion as cerdirecc', 'cur.instructor_mespecialidad', 'cur.depen_representante',
            DB::raw("extract(day from fcespe) as dia, to_char(fcespe, 'TMmonth') as mes, extract(year from fcespe) as anio"),
            DB::raw("extract(day from fcgen) as diagen, to_char(fcgen, 'TMmonth') as mesgen, extract(year from fcgen) as aniogen"),
            DB::raw("(hombre + mujer) as totalp"),
            DB::raw("extract(day from inicio) as diaini, to_char(inicio, 'TMmonth') as mesini, extract(year from inicio) as anioini"),
            DB::raw("extract(day from termino) as diafin, to_char(termino, 'TMmonth') as mesfin, extract(year from termino) as aniofin"))
            ->Join('cerss as cer', 'cer.id', 'cur.id_cerss')
            ->where('cur.folio_grupo','=',"$folio_grupo")->first();
        }else{
            $data1 = DB::table('tbl_cursos as cur')->select( 'cur.muni', 'cur.fcespe', 'cur.fcgen', 'cur.dura', 'cur.unidad', 'cur.dia as letradia',
            'cur.hini', 'cur.hfin', 'cur.tcapacitacion', 'cur.nombre', 'cur.curso', 'cur.tcapacitacion', 'cur.cespecifico', 'cur.cgeneral', 'cur.fcgen',
            'cur.depen', 'cur.costo', 'cur.inicio', 'cur.termino', 'cur.observaciones','cur.id_cerss', 'cur.instructor_mespecialidad',
            'cur.depen_representante',
            DB::raw("extract(day from fcespe) as dia, to_char(fcespe, 'TMmonth') as mes, extract(year from fcespe) as anio"),
            DB::raw("extract(day from fcgen) as diagen, to_char(fcgen, 'TMmonth') as mesgen, extract(year from fcgen) as aniogen"),
            DB::raw("(hombre + mujer) as totalp"),
            DB::raw("extract(day from inicio) as diaini, to_char(inicio, 'TMmonth') as mesini, extract(year from inicio) as anioini"),
            DB::raw("extract(day from termino) as diafin, to_char(termino, 'TMmonth') as mesfin, extract(year from termino) as aniofin"))
            ->where('cur.folio_grupo','=',"$folio_grupo")->first();
        }

        $data2 = DB::table('tbl_unidades as u')->select('dunidad', 'pdunidad', 'dgeneral', 'direccion',  'academico', 'pacademico', 'vinculacion', 'pvinculacion', 'direccion')
        ->Join('tbl_cursos as c', 'u.unidad', 'c.unidad')
        ->where('c.folio_grupo', $folio_grupo)->first();


        $data3 = DB::table('organismos_publicos as u')->select('nombre_titular', 'direccion', 'logo_instituto', 'siglas_inst', 'cargo_fun', 'poder_pertenece')
        ->Join('tbl_cursos as c', 'u.organismo', 'c.depen')
        ->where('c.folio_grupo', $folio_grupo)->first();

        $direccion = $data2->direccion;

        #validar si la image es de internet o del servidor utilizado
        $diferencia = '';
        $subcadenas = explode("_", $data3->logo_instituto);
        if($data3->logo_instituto != ''){
            if($subcadenas[0] == "/img/organismos/organismo"){
                $diferencia = 'local';
            }else{
                $diferencia = 'web';
            }
        }

        // dd($part_firm_cer1, $part_firm_cer2, $part_firm_user);

        $pdf = PDF::loadView('reportes.conv_esp_reg_grupo',compact('data1', 'data2', 'data3', 'diferencia', 'part_firm_cer1', 'part_firm_cer2', 'part_firm_user', 'allcourses', 'array_folios', 'direccion'));
        // $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Convenio');
    }

    /** Funcion para subir pdf al servidor
     * @param string $pdf, $id (convenio especifico), $nom
     */
    protected function pdf_upload($pdf, $id, $nom, $anio, $fold_destin)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $directorio = '/' . $anio . '/'.$fold_destin.'/' . $id . '/'.$pdfFile;
        $pdf->storeAs('/uploadFiles/'.$anio.'/'.$fold_destin.'/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadFiles'. $directorio); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return [$pdfUrl, $directorio];
    }

    #Se encarga de subir los pdfs
    public function upload_pdfs(Request $request) {
        $folio_grupo =  $_SESSION['folio_grupo'];
        $cursoInfo = DB::table('tbl_cursos')
        ->selectRaw('id as idcurso, EXTRACT(YEAR FROM inicio) as anio')->where('folio_grupo', $folio_grupo)->first();

        $anio = $cursoInfo->anio;
        $idcurso = $cursoInfo->idcurso;

        $archivo = $request->hasFile('archivoPDF');
        $opcion = $request->opcion;
        $partImg = basename($request->urlImg);

        #Validamos si no esta el registro en expedientes unicos.
        $validJson = $this->validar_exp_json($folio_grupo, $idcurso);
        if($validJson != 'ok'){
            return response()->json(['status' => "500",'mensaje' => "¡INTENTE DE NUEVO POR FAVOR!"]);
        }

        #Condicion para asignar nombre a los docs
        $nomdoc = $bddoc = '';
        if($opcion == '1'){ $nomdoc = 'acta_acuerdo'; $bddoc = 'url_pdf_acta'; }
        else if($opcion == '2'){ $nomdoc = 'convenio_espe'; $bddoc = 'url_pdf_convenio';}
        else if($opcion == '3'){ $nomdoc = 'soli_apertura'; $bddoc = 'url_documento';}
        else if($opcion == '4'){ $nomdoc = 'sid01'; $bddoc = 'url_documento';}
        if ($archivo) {
            if($partImg != ''){
                #Reemplazar
                $filePath = 'uploadFiles/'.$anio.'/expedientes/'.$idcurso.'/'.$partImg;
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                } else { return response()->json(['mensaje' => "¡ERROR!, DOCUMENTO NO ENCONTRADO"]); }
            }
            #Guardamos en la bd
            try {
                $vincu = ExpeUnico::find($idcurso);
                $doc = $request->file('archivoPDF'); # obtenemos el archivo
                $urldoc = $this->pdf_upload($doc, $idcurso, $nomdoc, $anio, 'expedientes'); # invocamos el método
                $url = $vincu->vinculacion;
                if($opcion == '1'){
                    $url['doc_1']['url_documento'] = $urldoc[1];
                    $url['doc_1'][$bddoc] = $urldoc[1];
                    $url['doc_1']['existe_evidencia'] = 'si';
                    $url['doc_1']['fecha_subida'] = date('Y-m-d');
                }else if($opcion == '2'){
                    $url['doc_1'][$bddoc] = $urldoc[1];
                    $url['doc_1']['existe_evidencia'] = 'si';
                    $url['doc_1']['fecha_subida'] = date('Y-m-d');
                }else{
                    $url['doc_'.$opcion]['url_documento'] = $urldoc[1];
                    $url['doc_'.$opcion]['existe_evidencia'] = 'si';
                    $url['doc_'.$opcion]['fecha_subida'] = date('Y-m-d');
                }

                $vincu->vinculacion = $url; # guardamos el path
                $vincu->save();
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

    #Validar si el registro en expedientes ya existe
    public function validar_exp_json($folio_grupo, $idcurso){
        $existsExpediente = DB::table('tbl_cursos_expedientes')->where('folio_grupo', $folio_grupo)->exists();
        if (!$existsExpediente){
            #FALSE crear todo desde cero
            $json_vacios = $this->llenar_json_exp(); #llamamos los arrays para mandarlos como json
            try {
                $reg_expedientes = new ExpeUnico;
                $reg_expedientes['id'] = $idcurso;
                $reg_expedientes['id_curso'] = $idcurso;
                $reg_expedientes['folio_grupo'] = $folio_grupo;
                $reg_expedientes['vinculacion'] = $json_vacios[0];
                $reg_expedientes['academico'] = $json_vacios[1];
                $reg_expedientes['administrativo'] = $json_vacios[2];
                $reg_expedientes['created_at'] = date('Y-m-d');
                $reg_expedientes['updated_at'] = date('Y-m-d');
                $reg_expedientes['iduser_created'] = Auth::user()->id;
                $reg_expedientes->save();
            } catch (\Throwable $th) {
                //throw $th;
                return 'error';
            }

        }else{
            #TRUE buscar si los json estan llenos si no deberiamos agregar
            $foundJson = ExpeUnico::where('folio_grupo', $folio_grupo)->whereNotNull('vinculacion')
            ->whereNotNull('academico')->whereNotNull('administrativo')->first();

            if ($foundJson == null) {
                #Actualizamos los campos JSON por que null significa que no estan llenos
                #Mandamos a llamar los arrays asociativos para los JSON
                $json_vacios = $this->llenar_json_exp(); #llamamos los arrays para mandarlos como json
                DB::table('tbl_cursos_expedientes')->where('folio_grupo', $folio_grupo)
                ->update(['id_curso' => $idcurso, 'folio_grupo' => $folio_grupo,
                'vinculacion' => $json_vacios[0], 'academico' => $json_vacios[1], 'administrativo' => $json_vacios[2],
                'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d'), 'iduser_updated' => Auth::user()->id]);
            }else{
                #No hacer nada todo esta correcto.
                return 'ok';
            }

        }
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

    ##Función para la validacion de instructores
    public function consultar_instructores (Request $request){
        $folio_grupo = $request->folio_grupo;
        $agenda = DB::Table('agenda')->Where('id_curso', $folio_grupo)->get();
        $grupo = DB::table('tbl_cursos')->select('id_curso','inicio', 'id_especialidad', 'termino', 'folio_grupo', 'programa', 'id_instructor', 'tbl_unidades.unidad')
        ->JOIN('tbl_unidades', 'tbl_unidades.id', '=', 'tbl_cursos.id_unidad')
        ->where('folio_grupo', $folio_grupo)->first();

        // list($instructores, $mensaje) = $this->data_instructores($grupo, $agenda);

         #### Llamamos la validacion de instructor desde el servicio
        $servicio = (new ValidacionServicioVb());
        // $instructores = $servicio->consulta_general_instructores($data, $this->ejercicio);

        list($instructores, $mensaje) = $servicio->data_validacion_instructores($grupo, $agenda, $this->ejercicio);

        //Validar si el array instructores esta vacio
        if (count($instructores) === 0) {
            return response()->json([
                'status' => 500,
                'mensaje' => $mensaje,
            ]);
        }

        return response()->json([
            'status' => 200,
            'mensaje' => $mensaje,
        ]);

    }


    // public function pdf_acta_firm(Request $request) {
    //     $folio_grupo =  $_SESSION['folio_grupo'];
    //     $convenio_esp = DB::table('tbl_cursos')->select('cespecifico')->where('folio_grupo','=',"$folio_grupo")->first();
    //     $cadena_conv = $convenio_esp->cespecifico;
    //     $cadenaSinGuiones = str_replace("-", "", $cadena_conv);
    //     $mensaje = '';

    //     if($request->hasFile('archivoPDF')){
    //         if($request->acciondoc == 'libre'){}
    //         else if($request->acciondoc == 'reemplazar'){
    //             $filePath = 'uploadFiles/acuerdoconvenios/'.$cadenaSinGuiones.'/'.$request->nomDoc;
    //             if (Storage::exists($filePath)) {
    //                 Storage::delete($filePath);
    //                 $mensaje = "ingreso a eliminar";
    //             } else { return response()->json(['status' => "¡ERROR!, DOCUMENTO NO ENCONTRADO"]); }
    //         }
    //         $doc = $request->file('archivoPDF'); # obtenemos el archivo
    //         $urldoc = $this->pdf_upload($doc, $cadenaSinGuiones, 'actafirmado'); # invocamos el método
    //         DB::table('tbl_cursos')->where('folio_grupo', $folio_grupo)->update(['url_pdf_acta' => $urldoc[0]]);
    //         $mensaje = "ARCHIVO CARGADO CORRECTAMENTE";


    //     }else{ $mensaje = "ERROR AL SUBIR EL DOCUMENTO!"; }
    //     return response()->json(['status' => 200, 'mensaje' => $mensaje]);
    // }

    // public function pdf_conv_firm(Request $request) {
    //     $folio_grupo =  $_SESSION['folio_grupo'];
    //     $convenio_esp = DB::table('tbl_cursos')->select('cespecifico')->where('folio_grupo','=',"$folio_grupo")->first();
    //     $cadena_conv = $convenio_esp->cespecifico;
    //     $cadenaSinGuiones = str_replace("-", "", $cadena_conv);
    //     $mensaje = '';

    //     if($request->hasFile('archivoPDF')){
    //         if($request->acciondoc == 'libre'){}
    //         else if($request->acciondoc == 'reemplazar'){
    //             $filePath = 'uploadFiles/acuerdoconvenios/'.$cadenaSinGuiones.'/'.$request->nomDoc;
    //             if (Storage::exists($filePath)) {
    //                 Storage::delete($filePath);
    //                 $mensaje = "ingreso a eliminar";
    //             } else { return response()->json(['status' => "¡ERROR!, DOCUMENTO NO ENCONTRADO"]); }
    //         }

    //         $doc = $request->file('archivoPDF'); # obtenemos el archivo
    //         $urldoc = $this->pdf_upload($doc, $cadenaSinGuiones, 'conveniofirmado'); # invocamos el método
    //         DB::table('tbl_cursos')->where('cespecifico', $cadena_conv)->update(['url_pdf_conv' => $urldoc[0]]);
    //         $mensaje = "Archivo cargado correctamente";


    //     }else{ $mensaje = "ERROR AL SUBIR EL DOCUMENTO!"; }
    //     return response()->json(['status' => 200, 'mensaje' => $mensaje]);
    // }
}
