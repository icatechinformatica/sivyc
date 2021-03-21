<?php

namespace App\Http\Controllers\Grupos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;

class calificacionesController extends Controller
{   
    function __construct() {
        session_start();
        $this->periodo = ["7"=>"1","8"=>"1","9"=>"1","10"=>"2","11"=>"2","12"=>"2","1"=>"3","2"=>"3","3"=>"3","4"=>"4","5"=>"4","6"=>"4"];

    }
    
    public function index(Request $request){
        $id_user = Auth::user()->id;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')
            ->value('roles.slug');        
        $_SESSION['unidades'] = $_SESSION['id_curso'] = NULL;
        //var_dump($rol);exit;
        if($rol){ 
            $unidad = Auth::user()->unidad;
            $unidad = DB::table('tbl_unidades')->where('id',$unidad)->value('unidad');
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$unidad)->pluck('unidad');        
            if(count($unidades)==0) $unidades =[$unidad];       
            $_SESSION['unidades'] = $unidades;              
        }
        //var_dump($_SESSION['unidades']);exit;
        $message = NULL;
        return view('grupos.calificaciones.index', compact('message'));     
    }  
    
    public function search(Request $request){
        $clave = $request->clave;
        $curso = $alumnos = $message = NULL;
        if(session('clave')) $clave = session('clave');
        if(session('message')) $message = session('message');
        $fecha_hoy = date("d-m-Y");
        $fecha_valida = NULL;
        $alumnos = [];
        
        if($clave){          
            $curso = DB::table('tbl_cursos')->where('clave',$clave);
                if($_SESSION['unidades'])$curso = $curso->whereIn('unidad',$_SESSION['unidades']);                               
                $curso = $curso->first();
           if($curso){
                $fecha_penultimo = date("Y-m-d",strtotime($curso->termino."- 1 days"));
                $fecha_valida =  strtotime($fecha_hoy)-strtotime($fecha_penultimo);
                
                if($curso->turnado == "UNIDAD" AND $curso->status!="REPORTADO" AND $curso->status!="CANCELADO"){                     
                     $alumnos = DB::table('tbl_inscripcion as i')->select('i.id','i.matricula','i.alumno','i.calificacion','i.folio','f.folio as ffolio')
                        ->leftJoin('tbl_folios as f', function($join){
                            $join->on('f.id_curso', '=', 'i.id_curso');
                            $join->on('f.matricula', '=', 'i.matricula');
                            $join->where('f.movimiento', 'EXPEDIDO');
                        })
                        ->where('i.id_curso',$curso->id)->where('i.status','INSCRITO')->orderby('i.alumno')->get();
                     
                     if($fecha_valida<0) $message = "No prodece el registro calificaciones, la fecha de termino del curso es el $curso->termino.";                                          
                }else $message = "El Curso fué $curso->status y turnado a $curso->turnado.";// .$curso->turnado;           
                
                if(count($alumnos)==0 AND !$message) $message = "El curso no tiene alumnos registrados. ";                
                else $_SESSION['id_curso'] = $curso->id;
            }else $message = "Clave inválida.";           
        }        
        //var_dump($alumnos); exit;       
        return  view('grupos.calificaciones.index', compact('curso','alumnos','message','fecha_valida')); 
    } 
    
      public function update(Request $request) {
        //var_dump($request->calificacion);exit;
        $message = NULL;
        $clave = $request->clave;
        $id_curso = $_SESSION['id_curso'];
        if($request->calificacion ){
            foreach($request->calificacion as $key=>$val){
                if(!is_numeric($val) OR $val<6 )  $val = "NP";
                $result = DB::table('tbl_inscripcion')->where('id_curso',$id_curso)->where('id', $key)->update(['calificacion' => $val,'iduser_updated'=>Auth::user()->id]);
                //var_dump($result);exit;
                /**REGISTRO TEMPORAL EN SICE tbl_calificaciones **/
                if($val!='NP'){ $acreditado = "X"; $noacreditado = "N";}
                else{ $acreditado = "N"; $noacreditado = "X";}

                $a = DB::table('tbl_inscripcion as i')->select('i.*','c.id as id_curso',DB::raw('right(c.clave,4) as grupo'),'c.area','c.espe','c.curso','c.mod','c.nombre as instructor', 'c.inicio','c.termino','c.hini', 'c.hfin', 'c.dura','c.ciclo', DB::raw('EXTRACT(MONTH FROM c.termino)  as mes_termino'))
                    ->Join('tbl_cursos as c', function($join){
                        $join->on('c.id', '=', 'i.id_curso');                        
                    })->where('i.id',$key)->where('i.id_curso',$id_curso)->where('i.status','INSCRITO')->orderby('i.alumno')->first();
                if($a){
                    $result2 = DB::table('tbl_calificaciones')->updateOrInsert(
                        ['idcurso' => $a->id_curso, 'matricula' => $a->matricula],
                        ['unidad' => $a->unidad, 'matricula'=>$a->matricula, 'alumno'=>$a->alumno,
                        'acreditado'=> $acreditado,'noacreditado' =>$noacreditado,'idcurso' => $a->id_curso,'idgrupo' => $a->grupo,'area'=> $a->area,
                        'espe' => $a->espe,'curso' => $a->curso,'mod' => $a->mod,'instructor' => $a->instructor, 'inicio' => $a->inicio,'termino' => $a->termino,
                        'hini' => $a->hini,'hfin'=> $a->hfin,'dura' => $a->dura,'ciclo' => $a->ciclo,'periodo' => $this->periodo[$a->mes_termino],'calificacion' => $val,'realizo' =>Auth::user()->name,'valido'=> Auth::user()->name                    
                    ]);
                }
                /**fin registro en SICE**/

            }
            if($result) $message = "Operacion exitosa!!";        
        }else $message = "No existen cambios que guardar.";
        //echo $message; exit;
        return redirect('grupos/calificaciones/buscar')->with(['message'=>$message, 'clave'=>$clave]);
    } 
}