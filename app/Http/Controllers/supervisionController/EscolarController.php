<?php

namespace App\Http\Controllers\supervisionController;

use App\Http\Controllers\Controller;
use App\Models\supervision\instructor;
//use App\Models\tbl_curso;
//use App\Models\instructor;
//use App\Models\curso;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
                 
        if($request->get('fecha'))$fecha = $request->get('fecha');
        else $fecha = date("Y-m-d"); 
                
        $query = DB::table('tbl_cursos')->select('tbl_cursos.id','tbl_cursos.id_curso','tbl_cursos.id_instructor',
        'tbl_cursos.nombre','tbl_cursos.clave','tbl_cursos.curso','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.hini',
        'tbl_cursos.hfin','tbl_cursos.unidad',DB::raw('COUNT(DISTINCT(i.id)) as total'),DB::raw('COUNT(DISTINCT(a.id)) as total_alumnos')
        
        );
        
        if($fecha)$query = $query->where('tbl_cursos.inicio','<=',$fecha)->where('tbl_cursos.termino','>=',$fecha);
        if($unidades) {
            $unidades = explode(',',$unidades);
            $query = $query->whereIn('tbl_cursos.unidad',$unidades);
        }
        if (!empty($tipo) AND !empty(trim($valor))) {                     
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
        $query = $query->where('tbl_cursos.clave', '>', '0');
        
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
            
            
        $query = $query->groupby('tbl_cursos.id','tbl_cursos.id_curso','tbl_cursos.id_instructor',
        'tbl_cursos.nombre','tbl_cursos.clave','tbl_cursos.curso','tbl_cursos.inicio','tbl_cursos.termino','tbl_cursos.hini',
        'tbl_cursos.hfin','tbl_cursos.unidad','i.id_tbl_cursos','a.id_tbl_cursos');
                
        $data =  $query->orderBy('tbl_cursos.inicio', 'DESC')->paginate(15);
        //var_dump($data);exit;
        return view('supervision.escolar.index', compact('data','fecha'));
    }    
    
   
      
}
