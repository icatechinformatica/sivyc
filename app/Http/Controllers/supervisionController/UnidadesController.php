<?php
// Elaboró Romelia Pérez Nangüelú 
// rpnanguelu@gmail.com

namespace App\Http\Controllers\supervisionController;
use App\Http\Controllers\Controller;
//use App\Models\supervision\unidades;
//use App\Models\tbl_curso;
use App\Models\SupervisionInstructor;
//use App\Models\instructor;
use App\Models\curso;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UnidadesController extends Controller
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
        $fecha = date("Y-m-d");         
        if($request->get('fecha'))$fecha = $request->get('fecha');        
        
        $query = DB::table('tbl_cursos')->select('tbl_cursos.unidad', DB::raw('count(*) as cursos'),'dunidad',
            DB::raw('count(supervision_instructores.id_tbl_cursos) as supervisiones'),
            DB::raw('COALESCE(sum(
            CAST(NOT(ok_nombre) as int)+
            CAST(NOT(ok_fecha_contrato) as int)+
            CAST(NOT(ok_fecha_padron) as int)+
            CAST(NOT(ok_honorarios) as int)+
            CAST(NOT(ok_modalidad) as int)+
            CAST(NOT(ok_horario) as int)+
            CAST(NOT(ok_horas_diarias) as int)+
            CAST(NOT(ok_horas_curso) as int)+
            CAST(NOT(ok_fecha_inicio) as int)+
            CAST(NOT(ok_fecha_termino) as int)+            
            CAST(NOT(ok_mujeres) as int)+
            CAST(NOT(ok_hombres) as int)+
            CAST(NOT(ok_tipo) as int)+
            CAST(NOT(ok_lugar) as int)
            ),0) as incidencias'))            
            ->groupBy('tbl_cursos.unidad','dunidad');
        if($fecha)$query = $query->where('tbl_cursos.inicio','<=',$fecha)->where('tbl_cursos.termino','>=',$fecha);
        $query = $query->leftJoin('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
            ->LeftJoin('supervision_instructores',function($join){
                $join->on('supervision_instructores.id_tbl_cursos', '=', 'tbl_cursos.id')
                ->where('enviado','1');
            });
        
        $data =  $query->orderBy('tbl_cursos.unidad')->get();
        //var_dump($data);exit;      
        //$fecha2 = date('d/m/Y',strtotime($fecha));
        return view('supervision.unidades.index', compact('data','fecha'));
    }
    
   public function detalle(Request $request,$id)
   {
        $unidad = $id;
        $fecha = $request->get('fecha');
        $data = DB::table('supervision_instructores')->where('unidad',$unidad)->where('enviado','1')->get();
        
        return view('supervision.unidades.detalle_supervisiones', compact('data','fecha'));
    
   } 
   /*
  
   public function cursos(Request $request,$id)
   {
        $unidad = $id;
        $tipo_busqueda = $request->get('tipo_busqueda');
        $valor_busqueda = $request->get('valor_busqueda');
        $fecha = $request->get('fecha');
        
        $data = tbl_curso::BusquedaSupervisor($tipo_busqueda, $valor_busqueda, $fecha,$unidad)
            ->PAGINATE(10, ['id','id_curso','id_instructor','nombre','clave','curso','inicio','termino','hini','hfin','unidad']);
        //var_dump($data); exit;
        
        //return var_dump($data);exit;
        return view('supervision.unidades.index_cursos', compact('data','unidad','fecha'));
    
   } 
 */ 
}
