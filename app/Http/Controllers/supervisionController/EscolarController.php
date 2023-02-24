<?php

namespace App\Http\Controllers\supervisionController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Models\Tbl_curso;

class EscolarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    function __construct() {      
       
    }
    public function index(Request $request)
    {   
        $user = Auth::user();
        $tipo = $request->get('tipo_busqueda');
        $valor = $request->get('valor_busqueda');        
        $unidades = $user->unidades;
        $id_user = $user->id;        
        $anio = date("Y");        
               
        if($request->get('fecha')) $fecha = $request->get('fecha');
<<<<<<< HEAD
        else $fecha = date("d/m/Y"); 
=======
        else $fecha = date("d/m/Y");
>>>>>>> c693001952b3a9dcb6987fb59509e3dead0d12b5
        if($unidades) {
            $unidades = explode(',',$unidades);
            $ubicacion =  DB::table('tbl_unidades as u')->whereIn('u.ubicacion',$unidades)->pluck('u.unidad');
            //var_dump($ubicacion);exit;
<<<<<<< HEAD
        } 
        
=======
        }

>>>>>>> c693001952b3a9dcb6987fb59509e3dead0d12b5
        $query = DB::table('tbl_cursos')->select('tbl_cursos.id','tbl_cursos.id_curso','tbl_cursos.id_instructor',
        'tbl_cursos.nombre','tbl_cursos.clave','tbl_cursos.curso','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.hini',
        'tbl_cursos.hfin','tbl_cursos.unidad',DB::raw('COUNT(DISTINCT(i.id)) as total'),DB::raw('COUNT(DISTINCT(a.id)) as total_alumnos'),
        'token_i.id as token_instructor','token_i.ttl as ttl_instructor','token_a.id_curso as token_alumno',
        'tbl_cursos.json_supervision',DB::raw('COUNT(DISTINCT(ins.id)) as ins_alumnos'));
<<<<<<< HEAD
        
        $query = $query->where('tbl_cursos.clave', '>', '0');
        
        if($fecha)$query = $query->where('tbl_cursos.inicio','<=',$fecha)->where('tbl_cursos.termino','>=',$fecha);
        if($unidades) {
            $query = $query->whereIn('tbl_cursos.unidad',$ubicacion);    
        }
        
        $query = $query->leftJoin('tbl_inscripcion as ins', function($join)use($id_user){
            $join->on('ins.id_curso', '=', 'tbl_cursos.id');
            $join->where('ins.status','INSCRITO');
            $join->groupBy('ins.id_curso');                
        });
        if (!empty($tipo) AND !empty(trim($valor))) {                     
=======

        $query = $query->where('tbl_cursos.clave', '>', '0');

        if($fecha)$query = $query->where('tbl_cursos.inicio','<=',$fecha)->where('tbl_cursos.termino','>=',$fecha);
        if($unidades) {
            $query = $query->whereIn('tbl_cursos.unidad',$ubicacion);
        }

        $query = $query->leftJoin('tbl_inscripcion as ins', function($join)use($id_user){
            $join->on('ins.id_curso', '=', 'tbl_cursos.id');
            $join->where('ins.status','INSCRITO');
            $join->groupBy('ins.id_curso');
        });
        if (!empty($tipo) AND !empty(trim($valor))) {
>>>>>>> c693001952b3a9dcb6987fb59509e3dead0d12b5
            switch ($tipo) {
                case 'nombre_instructor':                        
                    $query = $query->where('tbl_cursos.nombre', 'like', '%'.$valor.'%');
                    break;
                case 'clave_curso':                        
                    $query = $query->where('tbl_cursos.clave',$valor);
                    break;
                case 'nombre_curso':                        
                    $query = $query->where('tbl_cursos.curso', 'LIKE', '%'.$valor.'%');
                    break;                    
            }
        }
        $query = $query->leftJoin('supervision_instructores as i', function($join)use($id_user){
                $join->on('i.id_tbl_cursos', '=', 'tbl_cursos.id');                
                $join->where('i.id_user',$id_user);
                $join->groupBy('i.id_tbl_cursos');
                
            });
        $query = $query->leftJoin('supervision_alumnos as a', function($join)use($id_user){
                $join->on('a.id_tbl_cursos', '=', 'tbl_cursos.id');                
                $join->where('a.id_user',$id_user);
                $join->groupBy('a.id_tbl_cursos');
               
            });
            
        $query = $query->leftJoin('supervision_tokens as token_i' ,function($join)use($id_user){
                $join->on('tbl_cursos.id', '=', 'token_i.id_curso');
                $join->on('token_i.id_instructor','=','tbl_cursos.id_instructor'); 
                $join->where('token_i.id_supervisor',$id_user);
                $join->where('token_i.id_instructor','>','0');
        });
        
        $query = $query->leftJoin('supervision_tokens as token_a' ,function($join)use($id_user){
                $join->on('tbl_cursos.id', '=', 'token_a.id_curso'); 
                $join->where('token_a.id_supervisor',$id_user);     
                $join->where('token_a.id_alumno','>','0');      
        });
          
        $query = $query->groupby('tbl_cursos.id','tbl_cursos.id_curso','tbl_cursos.id_instructor',
        'tbl_cursos.nombre','tbl_cursos.clave','tbl_cursos.curso','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.hini',
        'tbl_cursos.hfin','tbl_cursos.unidad','i.id_tbl_cursos','a.id_tbl_cursos','token_i.id','token_i.ttl','token_a.id_curso');
                
        $data =  $query->orderBy('tbl_cursos.inicio', 'DESC')->paginate(15);
        //var_dump($data);exit;
        
         
        return view('supervision.escolar.index', compact('data','fecha'));
    }    
    
    public function updateCurso(Request $request)
    {
        $id_supervisor = Auth::user()->id;
        $id_curso = $request->input('id_curso');
        $fecha = date("dmy");
        $anio = date("Y");
        $archivo = "#";
        if($id_curso AND $request->input('status_supervision') AND $request->input('obs_supervision') AND $request->file('file_soporte')){
            $status = $request->input('status_supervision');
            if ($request->file('file_soporte')) {
                $ext = $request->file('file_soporte')->extension();
                $file_name =  $status."-".$id_curso."-".$fecha.".".$ext;
                $path_file = '/supervisiones/'.$anio.'/cursos';
                $archivo =  'storage'.$path_file.'/'.$file_name;    
            }
            $json_supervision = response()->json([
                'status' => $request->input('status_supervision'),
                'id_supervisor' => $id_supervisor,
                'fecha' => date('Y-m-d'),
                'obs' => $request->input('obs_supervision'),
                'archivo' =>  $archivo
            ]);
<<<<<<< HEAD
                             
            $c = Tbl_curso::find($id_curso);            
            $c->json_supervision = $json_supervision;
            if($c->save()) {
                if ($request->file('file_soporte')) 
                        $request->file('file_soporte')->storeAs($path_file, $file_name);
                return 1; 
            }            
            return 0;               
        }
        return 0;        
    }
    
    public function curso(Request $request, $clave){
        $mensaje="";
        $curso = $instructor = $alumnos = NULL;
        $consec = 1;
        if($clave){
            $curso = DB::table('tbl_cursos')->select('tbl_cursos.*',DB::raw('right(clave,4) as grupo'),
                DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
                'u.plantel',DB::raw('EXTRACT(MONTH FROM inicio)  as mes_inicio'),DB::raw('EXTRACT(YEAR FROM inicio)  as anio_inicio') )
                ->where('clave',$clave);                
                $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')            
                ->first();                 
            if($curso){
                $instructor = DB::table('instructores')->select('telefono','correo')->where('id',$curso->id_instructor)->first();
                $alumnos = DB::table('tbl_inscripcion as i')
                    ->select('i.matricula','i.alumno','a_pre.telefono','a_pre.correo')
                    ->where('i.id_curso',$curso->id)->where('i.status','INSCRITO')
                    ->Join('alumnos_registro as a_reg', function($join){                                        
                        $join->on('a_reg.no_control', '=', 'i.matricula');                    
                    }) 
                    ->Join('alumnos_pre as a_pre', function($join){
                        $join->on('a_pre.id', '=', 'a_reg.id_pre');
                    });                
                $alumnos = $alumnos->groupby('i.matricula','i.alumno','a_pre.telefono','a_pre.correo')->orderby('i.alumno')->get();  
            }else $mensaje="NO EXISTE EL CURSO ESPECIFICADO";
           
        }else $mensaje="CLAVE INVALIDA";
        return view('supervision.escolar.curso',compact('curso','instructor','alumnos','mensaje','consec')); 
    }
=======

            $c = Tbl_curso::find($id_curso);
            $c->json_supervision = $json_supervision;
            if($c->save()) {
                if ($request->file('file_soporte'))
                        $request->file('file_soporte')->storeAs($path_file, $file_name);
                return 1;
            }
            return 0;
        }
        return 0;
    }

    public function curso(Request $request, $clave){
        $mensaje="";
        $curso = $instructor = $alumnos = NULL;
        $consec = 1;
        if($clave){
            $curso = DB::table('tbl_cursos')->select('tbl_cursos.*',DB::raw('right(clave,4) as grupo'),
                DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
                'u.plantel',DB::raw('EXTRACT(MONTH FROM inicio)  as mes_inicio'),DB::raw('EXTRACT(YEAR FROM inicio)  as anio_inicio') )
                ->where('clave',$clave);
                $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')
                ->first();
            if($curso){
                $instructor = DB::table('instructores')->select('telefono','correo')->where('id',$curso->id_instructor)->first();
                $alumnos = DB::table('tbl_inscripcion as i')
                    ->select('i.matricula','i.alumno','a_pre.telefono','a_pre.correo')
                    ->where('i.id_curso',$curso->id)->where('i.status','INSCRITO')
                    ->Join('alumnos_registro as a_reg', function($join){
                        $join->on('a_reg.no_control', '=', 'i.matricula');
                    })
                    ->Join('alumnos_pre as a_pre', function($join){
                        $join->on('a_pre.id', '=', 'a_reg.id_pre');
                    });
                $alumnos = $alumnos->groupby('i.matricula','i.alumno','a_pre.telefono','a_pre.correo')->orderby('i.alumno')->get();
            }else $mensaje="NO EXISTE EL CURSO ESPECIFICADO";

        }else $mensaje="CLAVE INVALIDA";
        return view('supervision.escolar.curso',compact('curso','instructor','alumnos','mensaje','consec'));
    }
>>>>>>> c693001952b3a9dcb6987fb59509e3dead0d12b5
}
