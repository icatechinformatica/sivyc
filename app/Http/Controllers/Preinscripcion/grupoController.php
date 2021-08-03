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


class grupoController extends Controller
{   
    use catUnidades;
    use catApertura;
    function __construct() {
        session_start();
        $this->ejercicio = date("y");         
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->id_user = Auth::user()->id;
            $this->realizo = Auth::user()->name;  
            $this->id_unidad = Auth::user()->unidad;
            
            $this->data = $this->unidades_user('vincula');
            $_SESSION['unidades'] =  $this->data['unidades'];
                       
            return $next($request); 
        });
        
    }
    
    public function index(Request $request){

        $curso = $grupo = $cursos = $alumnos = [];
        $unidades = $this->data['unidades'];
        $unidad = $this->data['unidad'];
        $message = NULL;
        if(isset($_SESSION['folio_grupo'])){  //echo $_SESSION['folio_grupo'];exit;
            $anio_hoy = date('y');
            $alumnos = DB::table('alumnos_registro as ar')->select('ar.id as id_reg','ar.turnado','ap.nombre','apellido_paterno','apellido_materno',
                'ar.id_curso','ar.tipo_curso','ar.id_cerss','ar.horario','ap.ultimo_grado_estudios','ar.tinscripcion','ar.unidad','ar.folio_grupo','ap.curp',
                DB::raw("substring(curp,11,1) as sex"),                     
                DB::raw("CASE WHEN substring(curp,5,2) <='".$anio_hoy."' 
                THEN CONCAT('20',substring(curp,5,2),'-',substring(curp,7,2),'-',substring(curp,9,2))
                ELSE CONCAT('19',substring(curp,5,2),'-',substring(curp,7,2),'-',substring(curp,9,2)) 
                END AS fnacimiento")
                )->join('alumnos_pre as ap','ap.id','ar.id_pre')->where('ar.folio_grupo',$_SESSION['folio_grupo'] )->where('ar.eliminado',false)->get();
            //var_dump($alumnos);exit;
            if(count($alumnos)>0){
                $id_curso = $alumnos[0]->id_curso; 
                $tipo = $alumnos[0]->tipo_curso;
                if($alumnos[0]->turnado == 'VINCULACION' AND isset($this->data['cct_unidad']))$this->activar = true;
                else $this->activar = false;
                
                if($alumnos) $curso = DB::table('cursos')->where('id',$id_curso)->first();
                
                $cursos= DB::table('cursos')
                 ->where('tipo_curso',$tipo)
                 ->where('cursos.estado',true)
                 ->whereJsonContains('unidades_disponible', [$unidad])->orderby('cursos.nombre_curso')->pluck('nombre_curso','cursos.id');
            
                }else{ 
                    $message = "No hay registro qwue mostrar para Grupo No.".$_SESSION['folio_grupo'];
                    $_SESSION['folio_grupo'] = NULL;
                    $this->activar = true;      
               }
             
        }else{
            $_SESSION['folio_grupo'] = NULL;
            $this->activar = true;      
        }           
        
        $cerss = DB::table('cerss');
        if($unidad) $cerss = $cerss->where('id_unidad',$this->id_unidad)->where('activo',true);
        $cerss = $cerss->orderby('nombre','ASC')->pluck('nombre', 'id');              
        $folio_grupo =  $_SESSION['folio_grupo'];
        $activar = $this->activar;
        if(session('message')) $message = session('message');
        $tinscripcion = $this->tinscripcion();
        return view('preinscripcion.index',compact('cursos', 'alumnos','unidades','cerss','unidad', 'folio_grupo','curso','activar','message','tinscripcion'));        
        
    }
    
       
    public function cmbcursos(Request $request)
    {         
        //$request->unidad = 'TUXTLA';
        if (isset($request->tipo) and isset($request->unidad)){
            $cursos= DB::table('cursos')->select('cursos.id','nombre_curso')
             ->where('tipo_curso',$request->tipo)
             ->where('cursos.estado',true)
             ->whereJsonContains('unidades_disponible', [$request->unidad])->orderby('cursos.nombre_curso')->get();              
            $json=json_encode($cursos);
            //var_dump($json);exit;
        }else{
            $json=json_encode(["No hay registros que mostrar."]);
        }
        
        return $json; 
    }
    
    public function save(Request $request){
        $curp = $request->busqueda;    
        $matricula = $message = NULL;
        if($curp){
            $alumno = DB::table('alumnos_pre')->select('id as id_pre','matricula')->where('curp',$curp)->where('activo',true)->first();
            if(!$_SESSION['folio_grupo'] AND $alumno) $_SESSION['folio_grupo'] =$this->genera_folio();           
          
            if($alumno){                  
                    //EXTRAER MATRICULA Y GUARDAR
                    $matricula_sice = DB::table('registro_alumnos_sice')->where('eliminado',false)->where('curp', $curp)->value('no_control');            
                    
                    if($matricula_sice){
                        $matricula = $matricula_sice;
                        DB::table('registro_alumnos_sice')->where('curp', $curp)->update(['eliminado'=>true]);                                                        
                    }elseif(isset($alumno->matricula)) $matricula  =  $alumno->matricula;  
                    //FIN MATRICULA 
                    
                    $a_reg = DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->first();                
                    if($a_reg){
                        $id_especialidad = $a_reg->id_especialidad;
                        $id_unidad = $a_reg->id_unidad;
                        $unidad = $a_reg->unidad;
                        $id_curso = $a_reg->id_curso;
                        $horario = $a_reg->horario;
                        $tipo = $a_reg->tipo_curso;
                        $id_cerss = $a_reg->id_cerss;
                        
                    }else{
                        $id_especialidad = DB::table('cursos')->where('estado',true)->where('id', $request->id_curso)->value('id_especialidad');
                        $id_unidad = DB::table('tbl_unidades')->select('id','plantel')->where('unidad',$request->unidad)->value('id');
                        $unidad = $request->unidad;
                        $id_curso = $request->id_curso;
                        $horario = $request->horario;
                        $tipo = $request->tipo;
                        $id_cerss = $request->cerss;
                    }
                    if($id_cerss) $cerrs = true;
                    else $cerrs = NULL;
                    if($_SESSION['folio_grupo']){
                        $result = DB::table('alumnos_registro')->UpdateOrInsert(
                                [ 'id_pre' => $alumno->id_pre, 'folio_grupo' => $_SESSION['folio_grupo']],
                                [  'id_unidad' =>  $id_unidad, 'id_curso' => $id_curso, 'id_especialidad' =>  $id_especialidad, 
                                'horario' => $request->horario,'unidad' => $unidad,'tipo_curso' => $tipo, 
                                'cct' => $this->data['cct_unidad'],'realizo' => $this->realizo,'no_control' => $matricula,'ejercicio' => $this->ejercicio,
                                'folio_grupo' => $_SESSION['folio_grupo'],'iduser_created' => $this->id_user, 
                                'created_at' => date('Y-m-d H:i:s'),'fecha' => date('Y-m-d'), 'id_cerss' => $id_cerss, 'cerrs' => $cerrs,
                                'grupo' => $_SESSION['folio_grupo'],'eliminado' => false
                                ]
                            );
                        if($result) $message = "Operación Exitosa!!";
                    }else $message = "Operación no permitida!";
              }else $message = "Alumno no registrado ".$curp.".";
              
                
            }else $message = "Ingrese la CURP";
        
        return redirect()->route('preinscripcion.grupo')->with(['message'=>$message]);
    }
    
    public function update(Request $request){
        if($_SESSION['folio_grupo']){   
            $id_especialidad = DB::table('cursos')->where('estado',true)->where('id', $request->id_curso)->value('id_especialidad');
            $id_unidad = DB::table('tbl_unidades')->select('id','plantel')->where('unidad',$request->unidad)->value('id');
            
            if($request->cerss) $cerrs = true;
            else $cerrs = NULL;
            $result = DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->Update(
                [ 'id_unidad' =>  $id_unidad, 'id_curso' => $request->id_curso,
                        'id_especialidad' =>  $id_especialidad, 'horario' => $request->horario,'unidad' => $request->unidad,'tipo_curso' => $request->tipo,
                        'iduser_updated' => $this->id_user,'updated_at' => date('Y-m-d H:i:s'),'fecha' => date('Y-m-d'), 
                        'id_cerss' => $request->cerss, 'cerrs' => $cerrs
                ]
             );
             //Si hay cambios y esta registrado en tbl_cursos se elimina el instructor para validarlo nuevamente
             DB::table('tbl_cursos')->where('folio_grupo',$_SESSION['folio_grupo'])->where('clave','0')->update(['nombre' =>null, 'curp'=> null, 'rfc'=> null]);

        }else $message = "La acción no se ejecuto correctamente";
        return redirect()->route('preinscripcion.grupo');
        
    }
    
    public function genera_folio(){
         $consec = DB::table('alumnos_registro')->where('ejercicio',$this->ejercicio)->where('cct',$this->data['cct_unidad'])->where('eliminado',false)->value(DB::RAW('count(distinct(folio_grupo))'))+1;
         $consec = str_pad($consec, 4, "0", STR_PAD_LEFT);                            
         $folio = $this->data['cct_unidad']."-".$this->ejercicio.$consec;
         
         return $folio;
    }
    
    public function nuevo(){
        $_SESSION['folio_grupo'] = NULL;
        return redirect()->route('preinscripcion.grupo');
        
    }
    public function turnar(){        
        if($_SESSION['folio_grupo']){
            //echo "pasa"; exit;
            DB::table('alumnos_registro')->where('folio_grupo',$_SESSION['folio_grupo'])->update(['turnado'=>'UNIDAD','fecha_turnado' => date('Y-m-d')]);
            //$_SESSION['folio_grupo']=NULL;
        }
        return redirect()->route('preinscripcion.grupo');
        
    }
    
    public function delete(Request $request){
        $id = $request->id;
        if($id){
           //$result = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->where('id',$id)->update(['eliminado'=>true,'iduser_updated'=>$this->id_user]);
           $result = DB::table('alumnos_registro')->where('folio_grupo', $_SESSION['folio_grupo'])->where('id',$id)->delete();
        }else $result = false;
        //echo $result; exit;
        return $result;
    }   
   
}