<?php /**MODULO TEMPORAL POR CAPTURA DE APERTURA EN SICE */

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

class rezagoController extends Controller
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
            if($this->id_user <> 244){ echo "OPERACION INVALIDA!!"; exit;}
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
        return view('solicitud.rezago.index', compact('message','grupo','alumnos','plantel','depen','sector','programa','municipio','instructor','exoneracion','medio_virtual','tcurso','tinscripcion','tcuota'));
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
   
    public function store(Request $request){ 
        $message = 'Operación fallida, vuelva a intentar..';
        if($_SESSION['folio'] AND $_SESSION['grupo'] AND $_SESSION['alumnos']){
                    $grupo = $_SESSION['grupo'];   //var_dump($grupo);exit;
                    $alumnos = $_SESSION['alumnos'];   //var_dump($alumnos);exit;
                                
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
                               $result =  Alumno::where('id',$key)->update(['costo' => $pago, 'tinscripcion' => $tinscripcion, 'abrinscri' => $abrins]);
                               if($result) $message = 'Operación Exitosa!!';
                            }                
            
            
        }
        return redirect('solicitud/rezago')->with('message',$message);
   }
}