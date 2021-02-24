<?php

namespace App\Http\Controllers\Grupos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;

class asignarfoliosController extends Controller
{   
    function __construct() {
        session_start();
    }
    
    public function index(Request $request){
        $id_user = Auth::user()->id;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');        
        $_SESSION['unidades'] =  $_SESSION['id_curso'] = $clave = $curso = $alumnos = $message = NULL;
        //var_dump($rol);exit;
        if($rol){ 
            $unidad = Auth::user()->unidad;
            $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad');        
            if(count($unidades)==0) $unidades =[$unidad];       
            $_SESSION['unidades'] = $unidades;              
        }
      
        /** BÚSQUEDA **/        
        if(session('clave')) $clave = session('clave');
        else $clave = $request->clave;
        if($clave) $_SESSION['clave'] = $clave;
        $data = $this->validaCurso($clave);
        list($curso, $acta, $alumnos, $message) = $data; 

        if(session('message')) $message = session('message');

        return  view('grupos.asignarfolios.index', compact('curso','alumnos','message','acta')); 
    } 
    
    public function store() {     
        $clave = $_SESSION['clave'];
        $data = $this->validaCurso($clave);
        list($curso, $acta, $alumnos, $message) = $data; 
        if($acta AND !$message){
            $id_curso = $curso->id;
            $acta->num_inicio;
            $acta->num_fin;
            $num_folio = $acta->contador;
            foreach($alumnos as $a){   
                if($a->calificacion =="NP"){
                    $movimiento = "SIN EXPEDIR";
                    $motivo= "NO ACREDITADO";
                    $folio = 0;
                }else{
                    $movimiento = "EXPEDIDO";
                    $motivo= "ACREDITADO";
                   if($curso->mod=="EXT") $folio = "D";
                   else $folio = "C";
                   $folio .= str_pad($num_folio, 6, "0", STR_PAD_LEFT);   
                }
                             
                $result = DB::table('tbl_folios')->insert(
                    ['unidad' => $curso->unidad, 'id_curso'=>$curso->id,'matricula'=>$a->matricula, 'nombre'=>$a->alumno,
                    'folio' => $folio, 'movimiento'=> $movimiento, 'motivo' => $motivo, 'mod'=> $curso->mod, 'fini' => $acta->finicial, 'ffin' => $acta->ffinal, 'focan' => 0,
                    'fecha_acta' => $acta->facta, 'fecha_expedicion' => date('Y-m-d'), 'id_unidad' => $acta->id_unidad, 'id_afolios' => $acta->id, 'iduser_created' => Auth::user()->id 
                    ]
                
                );
                if($a->calificacion != "NP"){
                    if($result) DB::table('tbl_afolios')->where('id',$acta->id)->increment('contador');                
                    $num_folio++;
                }
            }
            if($result) $message = "Operacion exitosa!!"; 
        }           
        return redirect('grupos/asignarfolios')->with(['message'=>$message, 'clave'=>$clave]);
    } 

    private function validaCurso($clave){
        $curso = $alumnos = $message = $acta = NULL;
        if($clave){  
            //EXISTE EL CURSO
            $curso = DB::table('tbl_cursos')->where('clave',$clave);
                if($_SESSION['unidades'])$curso = $curso->whereIn('unidad',$_SESSION['unidades']);                               
                $curso = $curso->first();
            if($curso){
                ///ACTA CON FOLIOS DISPONIBLES
                $acta =  DB::table('tbl_afolios')->where('unidad',$curso->unidad)->where('mod',$curso->mod)->where('activo',true)->whereColumn('contador','<','num_fin')->first();
                if(!$acta)$message = "No hay Acta con Folios disponibles. ";
                ///NO DEBE TENER FOLIOS ASIGNADOS
                $folios =  DB::table('tbl_folios')->where('id_curso',$curso->id)->where('movimiento','<>','CANCELADO')->where('folio','>','0')->first();
                if($folios)$message = "Curso con folios asignados. ";
                ///ALUMNOS REGISTRADOS
                $alumnos = DB::table('tbl_inscripcion as i')->select('i.id','i.matricula','i.alumno','i.calificacion','f.folio','f.fecha_expedicion','f.motivo')
                    ->leftjoin('tbl_folios as f', function ($join) {                        
                        $join->on('f.id_curso','=','i.id_curso')
                        ->on('f.matricula','=','i.matricula')
                        ->where('f.movimiento','<>','CANCELADO');
                    })->where('i.id_curso',$curso->id)->orderby('i.alumno')->get();                  
               
                if(count($alumnos)==0) $message = "El curso no tiene alumnos registrados. ";
                elseif(count($alumnos)>0) if(!$alumnos[0]->calificacion)$message = "No hay registro de calificaciones, no podrá asignar folios. ";
                if(!$message)  $_SESSION['clave'] = $curso->clave; 
                
            }else $message = "Clave inválida.";
        }

        return $data = [$curso, $acta, $alumnos, $message];

    }
}