<?php

namespace App\Http\Controllers\Grupos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Models\Unidad;

class asignarfoliosController extends Controller
{   
    function __construct() {
        session_start();        
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {   
            $this->user = Auth::user();
            $this->ubicacion = Unidad::where('id',$this->user->unidad)->value('ubicacion'); //si fallas camabiar ubicacion=> unidad
           if($this->user->roles[0]->slug =="admin")
                $this->unidades = Unidad::orderby('unidad')->pluck('unidad','unidad');
            else
                $this->unidades = Unidad::where('ubicacion',$this->ubicacion)->orderby('unidad')->pluck('unidad','unidad');
            return $next($request);
        });
    }
    
    public function index(Request $request){
        $curso = $alumnos = $message = $acta = $matricula = $efirma = $clave = null;
        
        if(session('clave')) $clave = session('clave');
        else $clave = $request->clave;

        if(session('matricula')) $matricula = session('matricula');
        else $matricula = $request->matricula;
        
        if(session('efirma')) $efirma = session('efirma');
        else $efirma = $request->efirma;
        
        if($clave){ 
            $data = $this->validaCurso($clave, $matricula, NULL, $request);
            list($curso, $acta, $alumnos, $message) = $data;
        }
        return view('grupos.asignarfolios.index', compact('curso','alumnos','message','acta', 'matricula','efirma','clave')); 
    } 
    
    public function store(Request $request) {     
        $id_afolio = $request->id_afolio*1;
        $clave =  $request->clave;
        $matricula = $request->matricula;
        $data = $this->validaCurso($clave, $matricula, $id_afolio, $request);
        list($curso, $acta, $alumnos_out, $message) = $data; //var_dump($acta);exit;      
        
        if($acta AND !$message){
            $id_curso = $curso->id;  
            $num_folio = $acta->num_inicio+$acta->contador; //echo $num_folio;exit;
            $fecha_expedicion = $curso->termino;
            
            $alumnos = DB::table('tbl_inscripcion as i')->select('i.id','i.matricula','i.alumno','i.calificacion','i.reexpedicion','f.folio','f.fecha_expedicion','f.movimiento','f.motivo',
            DB::raw('(select count(id) from tbl_folios where i.id_curso = tbl_folios.id_curso and i.matricula = tbl_folios.matricula) as total_expedidos'))
                    ->where('i.status','INSCRITO')->leftjoin('tbl_folios as f','f.id','i.id_folio');
                    if($matricula)$alumnos = $alumnos->where('i.matricula',$matricula);                             
                    $alumnos = $alumnos->where('i.id_curso',$id_curso)->orderby('i.alumno')->get();
            
                   // var_dump($alumnos);exit;
            foreach($alumnos as $a){  //var_dump($a);exit;
                if($num_folio<=$acta->num_fin){
                    if((!$a->folio AND $a->calificacion !="NP") OR ($a->movimiento=="CANCELADO" AND $a->reexpedicion==false))  {
                        
                        $motivo= "ACREDITADO";
                        if($a->total_expedidos>=1){
                            $reexpedicion=true;
                            if($a->motivo=='ROBO O EXTRAVIO' OR $a->motivo=='NO SOLICITADO')$movimiento='DUPLICADO';
                            else $movimiento='REEXPEDIDO';                            
                        }else{
                            $reexpedicion=false;                            
                             $movimiento = "EXPEDIDO";
                        }
                        
                        if($acta->mod=="EXT") $prefijo = "D";
                        elseif($acta->mod=="CAE") $prefijo = "C";
                        elseif($acta->mod=="EFIRMA"){
                             $prefijo = substr($this->ubicacion, 0, 3);
                        }else $prefijo = "A";

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
        $efirma = $request->efirma;      
        return redirect('grupos/asignarfolios')->with(['message'=>$message, 'clave'=>$clave, 'matricula'=>$matricula, 'efirma' => $efirma]);
    } 

    private function validaCurso($clave, $matricula, $id_afolio, $request){
        $curso = $alumnos = $message = $acta = NULL;       
        if($clave){  
            //EXISTE EL CURSO
            $curso = DB::table('tbl_cursos')->where('clave',$clave);                
                $curso = $curso->whereIn('unidad',$this->unidades);          
                $curso = $curso->first();
           

            if($curso){

                ///ACTA CON FOLIOS DISPONIBLES
                if( $request->efirma){
                    $unidad = $this->ubicacion;
                    $mod[] = "EFIRMA";
                }else{
                    if($curso->mod=="EXT" OR $curso->mod=="CAE" ) $mod[] = $curso->mod;
                    $mod[] = "GRAL";
                    $unidad = $curso->unidad;
                }
                
                //dd($unidad);
                $acta =  DB::table('tbl_banco_folios')
                    ->select('*',DB::RAW("CONCAT(substr(finicial,1,1),lpad((num_inicio+contador)::text, 6, '0')) as folio_disponible"))
                    ->where('unidad',$unidad)->wherein('mod',$mod)
                    ->where('activo',true)->whereColumn('contador','<','total');
                                 
                    if($id_afolio){
                        $acta =  $acta->where('id',$id_afolio)->first(); //solo un folio
                        if(!$acta) $message = "No hay Acta con Folios disponibles, realice su solicitud a la DTA. ";
                    }else{
                        $acta =  $acta->orderby('id')->get(); //todos los folios
                        if(count($acta)==0) $message = "No hay Acta con Folios disponibles, realice su solicitud a la DTA. ";
                    }
               
                ///ALUMNOS REGISTRADOS
                $alumnos = DB::table('tbl_inscripcion as i')->select('i.id','i.matricula','i.alumno','i.calificacion','i.reexpedicion','i.id_folio as id_folioi','f.folio','f.fecha_expedicion','f.movimiento','f.motivo','f.id as id_foliof',
                    DB::raw('(select count(id) from tbl_folios where i.id_curso = tbl_folios.id_curso and i.matricula = tbl_folios.matricula) as total_expedidos'))
                    ->where('i.status','INSCRITO');
                    
                    if($matricula)$alumnos = $alumnos->where('i.matricula',$matricula);                    
                    $alumnos = $alumnos->leftJoin('tbl_folios as f', function($join){                                        
                        $join->on('f.id_curso', '=', 'i.id_curso');
                        $join->on('f.matricula', '=', 'i.matricula');
                    });                     
                    $alumnos = $alumnos->where('i.id_curso',$curso->id)->orderby('i.alumno')->orderby('f.folio','DESC')->get();                    
                                      
               //var_dump($alumnos);exit;
                if(count($alumnos)==0) $message = "El curso no tiene alumnos registrados. ";
                elseif(count($alumnos)>0) if(!$alumnos[0]->calificacion)$message = "No hay registro de calificaciones, no podrá asignar folios. ";
                //elseif(count($alumnos)>0) if($alumnos[0]->folio)$message = "Curso con folios expedidos. ";
                /*
                if(!$message){
                    $_SESSION['clave'] = $curso->clave;
                    $_SESSION['matricula'] = $matricula;
                } 
                */
                
            }else $message = "Clave inválida.";
        }
        return $data = [$curso, $acta, $alumnos, $message];

    }
}