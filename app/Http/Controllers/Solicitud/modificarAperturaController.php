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
use Illuminate\Validation\ValidationException;
use App\Utilities\MyCrypt;
use App\Models\cat\catUnidades; 
use App\Models\cat\catApertura;
use App\Models\tbl_curso;

class modificarAperturaController extends Controller
{   
    use catUnidades;
    use catApertura;
    private $validationRules = [
        'nmunidad'=> ['required'],
        'opcion'=> ['required'],
        'observaciones'=> ['required']
    ];
    private $validationMessages = [
        'nmunidad.required' => 'Favor de ingresar el memorandum.',
        'opcion.required' => 'Favor de ingresar el motivo.',
        'observaciones.required' => 'Favor de ingresar las observaciones.'
    ];

    function __construct() {
        //session_start();
        $this->ejercicio = date("y");         
        $this->middleware('auth');
        $this->path_pdf = "/DTA/solicitud_folios/";        
        $this->path_files = env("APP_URL").'/storage/uploadFiles';        
        $this->middleware(function ($request, $next) {
            $this->id_user = Auth::user()->id;
            $this->realizo = Auth::user()->name;  
            $this->id_unidad = Auth::user()->unidad;            
            $this->data = $this->unidades_user('unidad');
            $this->unidades =  $this->data['unidades'];
            //$_SESSION['unidades'] =  $this->data['unidades'];            
            return $next($request); 
        });
    }
    
    public function index(Request $request){     
        $grupo = $alumnos = $message = $tcuota = $motivo = NULL;
        
        if(session('IDE')) $clave = session('IDE'); 
        else $clave = $request->clave;
        if($clave){ 
            $tcuota = $this->tcuota();//catálogo de cuotas
            $motivo = $this->motivo_arc02();//catálogo de movito arc02
            $campo = is_numeric($clave) ? 'id' : 'clave';
            $grupo =  DB::table('tbl_cursos')->where($campo,$clave)->whereIn('status',['NO REPORTADO','RETORNO_UNIDAD'])->where('turnado','UNIDAD');
            if($this->unidades) $grupo = $grupo->whereIn('unidad',$this->unidades);
            $grupo = $grupo->first();
            if($grupo){                
                $grupo->IDE =   MyCrypt::encrypt_($grupo->id);
                $alumnos = DB::table('tbl_inscripcion as i')->select('i.*', DB::raw("'VIEW' as mov"))->where('i.id_curso', $grupo->id)->get();
                $tcuota = $tcuota[$grupo->tipo];
            }else  $message = "Clave de Curso no válido para el usuario.";
        } else  $message = "Ingrese la clave del curso.";     
        
        if(session('message')) $message = session('message');        
        return view('solicitud.modificarApertura.index', compact('message','grupo','alumnos', 'tcuota','motivo'));
    }  
       
  
    public function store(Request $request){ 
        $message = "Clave de apertura inválida, intente de nuevo.";
        if($request->IDE){            
            $id_curso =   MyCrypt::decrypt_($request->IDE);             
            if(is_int($id_curso)){
                //status_curso,opcion,motivo, num_revision_arc02,nmunidad, status_solicitud_arc02,mextemporaneo_arc02,rextemporaneo_arc02,fecha_arc02,file_arc02,mov_arc02
                try {// Validar los datos del formulario
                    $validatedData = $request->validate($this->validationRules); 
                    $result = tbl_curso::where('id',$id_curso)->update(
                        ['nmunidad' => strtoupper($request->nmunidad),
                        'num_revision_arc02' => strtoupper($request->nmunidad),
                        'opcion' => $request->opcion,
                        'motivo' => $request->opcion,
                        'arc' => '02',  
                        'status_solicitud_arc02' => null,  
                        'realizo' => $this->realizo,                           
                        'observaciones' => strtoupper($request->observaciones)
                        ]
                    );                    
                    if($result)$message = 'Operación Exitosa!!';
                    else $message = 'Operación fallida, vuelva a intentar..';                    
                } catch (ValidationException $e) {
                    $message = 'Operación fallida, faltan datos para registrar.';
                }

            }else{                 
                 $id_curso = null;
            }
        }        
        return redirect('solicitud/apertura/modificar')->with('message',$message)->with('IDE',$id_curso);
    }

    public function reverse(Request $request){
        $message = "Clave de apertura inválida, intente de nuevo.";
        if($request->IDE){            
            $id_curso =   MyCrypt::decrypt_($request->IDE);             
            if(is_int($id_curso)){
                //status_curso,opcion,motivo, num_revision_arc02,nmunidad, status_solicitud_arc02,mextemporaneo_arc02,rextemporaneo_arc02,fecha_arc02,file_arc02,mov_arc02
                $result = tbl_curso::where('id',$id_curso)->where('arc','02')->update(
                    ['nmunidad' => 0,
                    'num_revision_arc02' => null,
                    'opcion' => 'NINGUNO',
                    'motivo' => 'NINGUNO',
                    'arc' => '01',                
                    'status_solicitud_arc02' => null,            
                    'observaciones' => 'NINGUNO'                
                    ]
                );
                if($result)$message = 'Operación Exitosa!!';
                else $message = 'Operación fallida, vuelva a intentar..';
            }else{         
                 $id_curso = null;
            }
        }

        return redirect('solicitud/apertura/modificar')->with('message',$message);
        /*
        $message = 'Operación fallida, vuelva a intentar..';
        if($_SESSION['id_curso']){
            $id_curso = $_SESSION['id_curso'];
            $result = tbl_curso::where('id',$id_curso)->where('arc','02')->update(
                ['nmunidad' => 0,
                'num_revision_arc02' => null,
                'opcion' => 'NINGUNO',
                'motivo' => 'NINGUNO',
                'arc' => '01',                
                'status_solicitud_arc02' => null,            
                'observaciones' => 'NINGUNO'                
                ]
            );
            if($result)$message = 'Operación Exitosa!!';
        }
        return redirect('solicitud/apertura/modificar')->with('message',$message);
        */
    }
   
}