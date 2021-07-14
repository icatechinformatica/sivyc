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
use App\Models\cat\catUnidades; 
use App\Models\cat\catApertura;
use App\Models\tbl_curso;

class modificarAperturaController extends Controller
{   
    use catUnidades;
    use catApertura;
    
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
            $this->data = $this->unidades_user('unidad');
            $_SESSION['unidades'] =  $this->data['unidades'];            
            return $next($request); 
        });
    }
    
    public function index(Request $request){     
        $clave = $grupo = $alumnos = $message = $tcuota = $motivo = NULL;
        if($request->clave)  $clave = $request->clave; 
        elseif(isset($_SESSION['clave'])) $clave = $_SESSION['clave'];
        $_SESSION['alumnos'] = NULL;        
        if($clave){  
            $tcuota = $this->tcuota();
            $motivo = $this->motivo_arc02();

            $grupo =  DB::table('tbl_cursos')->where('clave',$clave)->where('status','NO REPORTADO')->where('turnado','UNIDAD');
            if($_SESSION['unidades']) $grupo = $grupo->whereIn('unidad',$_SESSION['unidades']);
            $grupo = $grupo->first();
            if($grupo){
                $_SESSION['clave'] = $grupo->clave;
                $_SESSION['id_curso'] = $grupo->id;                
                $alumnos = DB::table('tbl_inscripcion as i')->select('i.*', DB::raw("'VIEW' as mov"))->where('i.id_curso', $_SESSION['id_curso'])->get();
                $tcuota = $tcuota[$grupo->tipo];
            }
        }         
        //var_dump($grupo);exit;
        if(session('message')) $message = session('message');
        return view('solicitud.modificarApertura.index', compact('message','grupo','alumnos', 'tcuota','motivo','clave'));
    }  
       
  
    public function store(Request $request){   
        $message = 'Operación fallida, vuelva a intentar..';
        if($_SESSION['id_curso']){
            $id_curso = $_SESSION['id_curso'];
            $result = tbl_curso::where('id',$id_curso)->update(
                ['nmunidad' => strtoupper($request->nmunidad),
                'opcion' => $request->opcion,
                'arc' => '02',                
                'status_curso' => null,                
                'observaciones' => strtoupper($request->observaciones)
                ]
            );
            if($result)$message = 'Operación Exitosa!!';
        }
        return redirect('solicitud/apertura/modificar')->with('message',$message);
    }

    public function reverse(Request $request){   
        $message = 'Operación fallida, vuelva a intentar..';
        if($_SESSION['id_curso']){
            $id_curso = $_SESSION['id_curso'];
            $result = tbl_curso::where('id',$id_curso)->where('arc','02')->update(
                ['nmunidad' => 0,
                'opcion' => 'NINGUNO',
                'arc' => '01',                
                'status_curso' => 'AUTORIZADO',                
                'observaciones' => 'NINGUNO'                
                ]
            );
            if($result)$message = 'Operación Exitosa!!';
        }
        return redirect('solicitud/apertura/modificar')->with('message',$message);
    }
   
}