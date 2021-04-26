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
        $data = $this->validaCurso($clave, NULL);
        list($curso, $acta, $alumnos, $message) = $data; 

        if(session('message')) $message = session('message');

        return  view('grupos.asignarfolios.index', compact('curso','alumnos','message','acta')); 
    } 
    
    public function store(Request $request) {     
        $id_afolio = $request->id_afolio*1;
        $clave = $_SESSION['clave'];
        $data = $this->validaCurso($clave, $id_afolio);
        list($curso, $acta, $alumnos, $message) = $data; //var_dump($acta);exit;      
        
        if($acta AND !$message){
            $id_curso = $curso->id;  
            $num_folio = $acta->num_inicio+$acta->contador; //echo $num_folio;exit;
            $fecha_expedicion = $curso->termino;
            foreach($alumnos as $a){  //var_dump($a);exit;
                if($num_folio<=$acta->num_fin){
                    if((!$a->folio AND $a->calificacion !="NP") OR ($a->movimiento=="CANCELADO" AND $a->reexpedicion==false))  {
                        
                        $motivo= "ACREDITADO";
                        if($a->movimiento=="CANCELADO"){
                            $reexpedicion=true;
                            if($a->motivo=='ROBO O EXTRAVIO' OR $a->motivo=='NO SOLICITADO')$movimiento='DUPLICADO';
                            else $movimiento='REEXPEDIDO';                            
                        }else{
                            $reexpedicion=false;                            
                             $movimiento = "EXPEDIDO";
                        }
                        
                        if($acta->mod=="EXT") $prefijo = "D";
                        elseif($acta->mod=="CAE") $prefijo = "C";
                        else $prefijo = "A";

                        $folio = $prefijo.str_pad($num_folio, 6, "0", STR_PAD_LEFT);                       
                       
                        $id_folio = DB::table('tbl_folios')->insertGetId(
                            ['unidad' => $curso->unidad, 'id_curso'=>$curso->id,'matricula'=>$a->matricula, 'nombre'=>$a->alumno,
                                'folio' => $folio, 'movimiento'=> $movimiento, 'motivo' => $motivo, 'mod'=> $curso->mod, 'fini' => $acta->finicial, 'ffin' => $acta->ffinal, 'focan' => 0,
                                'fecha_acta' => $acta->facta, 'fecha_expedicion' => $fecha_expedicion, 'id_unidad' => $acta->id_unidad, 'id_banco_folios' => $acta->id, 
                                'iduser_created' => Auth::user()->id, 'realizo'=>Auth::user()->name,'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s')
                                ]                            
                         );
                         
                         $data = ['reexpedicion' => $reexpedicion, 'iduser_updated' => Auth::user()->id];
                         if($movimiento!='DUPLICADO') $data['id_folio']= $id_folio;
                         $resultAlumno = DB::table('tbl_inscripcion')->where('id',$a->id)->update($data);
                  
                                        
                        if($id_folio){
                                DB::table('tbl_banco_folios')->where('id',$acta->id)->increment('contador');
                                $message = "Operacion exitosa!!";
                        }                
                        $num_folio++;
                    }           
                     
                }else $message = "El folio final ha sido asignado!!";
            }
        }           
        return redirect('grupos/asignarfolios')->with(['message'=>$message, 'clave'=>$clave]);
    } 

    private function validaCurso($clave, $id_afolio){
        $curso = $alumnos = $message = $acta = NULL;
        if($clave){  
            //EXISTE EL CURSO
            $curso = DB::table('tbl_cursos')->where('clave',$clave);
                if($_SESSION['unidades'])$curso = $curso->whereIn('unidad',$_SESSION['unidades']);                               
                $curso = $curso->first();
            if($curso){
                ///ACTA CON FOLIOS DISPONIBLES
                if($curso->mod=="EXT" OR $curso->mod=="CAE" ) $mod[] = $curso->mod;
                $mod[] = "GRAL";
                
                $acta =  DB::table('tbl_banco_folios')
                    ->select('*',DB::RAW("CONCAT(substr(finicial,1,1),lpad((num_inicio+contador)::text, 6, '0')) as folio_disponible"))
                    ->where('unidad',$curso->unidad)->wherein('mod',$mod)
                    ->where('activo',true)->whereColumn('contador','<','total');
                                        
                    if($id_afolio){
                        $acta =  $acta->where('id',$id_afolio)->first(); //solo un folio
                        if(!$acta) $message = "No hay Acta con Folios disponibles. ";
                    }else{
                        $acta =  $acta->orderby('id')->get(); //todos los folios
                        if(count($acta)==0) $message = "No hay Acta con Folios disponibles. ";
                    }
               // var_dump($acta);exit;
                ///ALUMNOS REGISTRADOS
                $alumnos = DB::table('tbl_inscripcion as i')->select('i.id','i.matricula','i.alumno','i.calificacion','i.reexpedicion','f.folio','f.fecha_expedicion','f.movimiento','f.motivo')
                    ->where('i.status','INSCRITO')
                    ->leftJoin('tbl_folios as f', function($join){                                        
                        $join->on('f.id_curso', '=', 'i.id_curso');
                        $join->on('f.matricula', '=', 'i.matricula');
                    }) 
                    ->where('i.id_curso',$curso->id)->orderby('i.alumno')->get();                  
               //var_dump($alumnos);exit;
                if(count($alumnos)==0) $message = "El curso no tiene alumnos registrados. ";
                elseif(count($alumnos)>0) if(!$alumnos[0]->calificacion)$message = "No hay registro de calificaciones, no podrá asignar folios. ";
                //elseif(count($alumnos)>0) if($alumnos[0]->folio)$message = "Curso con folios expedidos. ";
                if(!$message)  $_SESSION['clave'] = $curso->clave; 
                
            }else $message = "Clave inválida.";
        }

        return $data = [$curso, $acta, $alumnos, $message];

    }
}