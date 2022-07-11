<?php

namespace App\Http\Controllers\consultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\instructor;
use App\Models\tbl_curso;
use App\Models\cat\catUnidades;
use App\Models\cat\catApertura;
//use App\Excel\xls;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportExcelPOA;

class poaController extends Controller
{
    use catUnidades;
    use catApertura;
    function __construct(){
        session_start();
        $this->ejercicio = date("y");
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->data = $this->unidad_user();
            $_SESSION['unidades'] =  $this->data['unidades'];
            return $next($request);
        });
    }
    public function index(Request $request){
        $data = [];
        if(!$request->opciones) $request->opciones = "CURSOS";
        $data = $this->data($request);        
        return view('consultas.poa.index',compact('data','request'));   
    }

    public function data($request){                
        $unidades = $_SESSION['unidades'];
        if(!$request->fecha1) $request->fecha1 = date('Y-01-01');
        if(!$request->fecha2) $request->fecha2 = date('Y-m-d');    
        
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        $ejercicio = date('Y',strtotime($fecha1));

        if($fecha1 AND $fecha2){            
            switch($request->opciones){
                case "CUOTA":                  
                    $data = DB::table('tbl_cursos as tc')->select('tc.unidad','ti.costo',
                    DB::raw('count(distinct(tc.id)) as cursos_reportados'),
                    DB::raw("sum(CASE WHEN ti.status ='INSCRITO' THEN 1 ELSE 0 END) as inscritos"),         
                    DB::raw("sum(CASE WHEN ti.calificacion ='NP' AND ti.status ='INSCRITO' THEN 1 ELSE 0 END) as desercion")
                    )
                    ->join('tbl_inscripcion as ti','ti.id_curso','tc.id')                    
                    ->where('tc.proceso_terminado',true)->where('tc.status_curso','AUTORIZADO')
                    ->where('tc.fecha_apertura','>=',$fecha1)
                    ->where('tc.fecha_apertura','<=',$fecha2)
                    ->groupby('tc.unidad','ti.costo')
                    ->orderby('ti.costo','ASC')->orderby(DB::raw('count(distinct(tc.id))'),'DESC')->orderby('tc.unidad','ASC')
                    ->get();                    
                break;                
                default:
                    $desercion = DB::table('tbl_inscripcion as d')
                    ->select('d.id_curso',DB::raw('count(d.id) as desercion'))->where('d.status','INSCRITO')->where('d.calificacion','=','NP')->groupby('d.id_curso');
        
                    $total_autorizado = DB::table('tbl_cursos as c')
                    ->select('tu.ubicacion', DB::raw('count(c.id) as total_autorizado'))
                    ->join('tbl_unidades as tu','tu.ubicacion','=','c.unidad')
                    ->where('c.status_curso','AUTORIZADO')
                    ->where('c.fecha_apertura','>=',$fecha1)
                    ->where('c.fecha_apertura','<=',$fecha2)
                    ->groupby('tu.ubicacion');
                    
                /*TOTALES*/
                    $desercion_total = DB::table('tbl_inscripcion as d')
                    ->select('d.id_curso',DB::raw('count(d.id) as desercion'))->where('d.status','INSCRITO')->where('d.calificacion','=','NP')->groupby('d.id_curso');
        
                    $data_total = DB::table('tbl_unidades as u')
                    ->select('poa.id','u.ubicacion as unidad',DB::raw('MAX(poa.total_cursos) as cursos_programados'),
                    DB::raw('count(tc.*) as cursos_autorizados'),DB::raw('count(ts.*) as suficiencia_autorizada'),
                    DB::raw('count(tc2.*) as cursos_reportados'),DB::raw('MAX(poa.total_horas) as horas_programadas'),
                    DB::raw('SUM(tc2.dura) as horas_impartidas'),DB::raw('SUM(tc2.hombre+tc2.mujer) as inscritos'),
                    DB::raw('SUM(d.desercion) as desercion'), DB::raw('SUM(tc2.hombre+tc2.mujer) - SUM(d.desercion) as egresados'),
                    DB::raw("'-1' as plantel"),
                    DB::raw("'A' as ze"),DB::raw("'A' as poa_ze")
                    )
      
                    ->leftjoin('poa', function ($join) use($ejercicio){
                        $join->on('poa.tbl_unidades_unidad','=','u.ubicacion')
                        ->where('poa.ejercicio',$ejercicio)
                        ->where('poa.id_plantel','=',0);
                        
                    })            
                    ->leftjoin('tbl_cursos as tc', function ($join) use($fecha1, $fecha2){
                        $join->on('tc.unidad','=','u.unidad')
                        ->where('tc.status_curso','AUTORIZADO')                
                        ->where('tc.fecha_apertura','>=',$fecha1)
                        ->where('tc.fecha_apertura','<=',$fecha2)
                        ->leftjoin('folios as f', function ($join) use($fecha1, $fecha2){
                            $join->on('f.id_cursos','=','tc.id')
                            ->leftjoin('tabla_supre as ts','ts.id','=','f.id_supre')                    
                            ->where('ts.status','Validado')
                            ->where('ts.fecha_validacion','>=',$fecha1)
                            ->where('ts.fecha_validacion','<=',$fecha2);                    
                        });                          
                    })         
                    ->leftjoin('tbl_cursos as tc2', function ($join) use($fecha1, $fecha2, $desercion){
                        $join->on('tc2.id','=','tc.id')
                        ->where('tc2.proceso_terminado',true)
                        ->where('tc2.fecha_apertura','>=',$fecha1)
                        ->where('tc2.fecha_apertura','<=',$fecha2);
                       
                    })            
                    ->leftjoin('tbl_cursos as tc3', function ($join) use($desercion_total){
                        $join->on('tc3.id','=','tc2.id')                               
                        ->joinSub($desercion_total, 'd', function($join){
                            $join->on('tc3.id','=','d.id_curso');
                        });
                    })
                    ->wherein('u.ubicacion', $unidades)->orwherein('u.unidad', $unidades)
                    ->groupby('u.ubicacion','poa.id');
        
                /***DETALLE POR UNIDAD */ 
                   $data_unidad = DB::table('tbl_unidades as u')
                   ->select('poa.id','u.unidad','poa.total_cursos as cursos_programados',DB::raw('count(tc.*) as cursos_autorizados'),
                   DB::raw('count(ts.*) as suficiencia_autorizada'),DB::raw('count(tc2.*) as cursos_reportados'),
                   'poa.total_horas as horas_programadas',DB::raw('SUM(tc2.dura) as horas_impartidas'),
                   DB::raw('SUM(tc2.hombre+tc2.mujer) as inscritos'),
                   DB::raw('SUM(d.desercion) as desercion'), DB::raw('SUM(tc2.hombre+tc2.mujer) - SUM(d.desercion) as egresados'),
                   'poa.tbl_unidades_plantel as plantel',DB::raw("'B' as ze"),DB::raw("'B' as poa_ze")
                   )
                   ->leftjoin('poa', function ($join) use($ejercicio){
                       $join->on('poa.tbl_unidades_unidad','=','u.unidad')
                       ->where('poa.ejercicio',$ejercicio)
                       ->where('poa.tbl_unidades_plantel','>',0);
                   })            
                   ->leftjoin('tbl_cursos as tc', function ($join) use($fecha1, $fecha2){
                       $join->on('tc.unidad','=','u.unidad')
                       ->where('tc.status_curso','AUTORIZADO')                
                       ->where('tc.fecha_apertura','>=',$fecha1)
                       ->where('tc.fecha_apertura','<=',$fecha2)
                       ->leftjoin('folios as f', function ($join) use($fecha1, $fecha2){
                           $join->on('f.id_cursos','=','tc.id')
                           ->leftjoin('tabla_supre as ts','ts.id','=','f.id_supre')                    
                           ->where('ts.status','Validado')
                           ->where('ts.fecha_validacion','>=',$fecha1)
                           ->where('ts.fecha_validacion','<=',$fecha2);                    
                       });                         
                   })         
                   ->leftjoin('tbl_cursos as tc2', function ($join) use($fecha1, $fecha2, $desercion){
                       $join->on('tc2.id','=','tc.id')
                       ->where('tc2.proceso_terminado',true)
                       ->where('tc2.fecha_apertura','>=',$fecha1)
                       ->where('tc2.fecha_apertura','<=',$fecha2);
                      
                   })            
                   ->leftjoin('tbl_cursos as tc3', function ($join) use($desercion){
                       $join->on('tc3.id','=','tc2.id')                               
                       ->joinSub($desercion, 'd', function($join){
                           $join->on('tc3.id','=','d.id_curso');
                       });
                   }) 
                   ->wherein('u.ubicacion', $unidades)->orwherein('u.unidad', $unidades)            
                   ->groupby('tc.unidad','u.unidad','poa.total_cursos','u.order_poa','poa.total_horas','tc.cct','poa.id',
                   'poa.tbl_unidades_plantel');               
        
                /***DETALLE POR ZONA***/
                     
                    $data = DB::table('tbl_unidades as u')
                    ->select('poa.id','u.unidad','poa.total_cursos as cursos_programados',DB::raw('count(tc.*) as cursos_autorizados'),
                    DB::raw('count(ts.*) as suficiencia_autorizada'),DB::raw('count(tc2.*) as cursos_reportados'),
                    'poa.total_horas as horas_programadas',DB::raw('SUM(tc2.dura) as horas_impartidas'),
                    DB::raw('SUM(tc2.hombre+tc2.mujer) as inscritos'),
                    DB::raw('SUM(d.desercion) as desercion'), DB::raw('SUM(tc2.hombre+tc2.mujer) - SUM(d.desercion) as egresados'),
                    'poa.tbl_unidades_plantel as plantel','tc.ze','poa.ze as poa_ze'
                    )
                                   
                    ->leftjoin('tbl_cursos as tc', function ($join) use($fecha1, $fecha2,$ejercicio){
                        $join->on('tc.unidad','=','u.unidad')
                        ->where('tc.status_curso','AUTORIZADO')                
                        ->where('tc.fecha_apertura','>=',$fecha1)
                        ->where('tc.fecha_apertura','<=',$fecha2)
                        ->leftjoin('folios as f', function ($join) use($fecha1, $fecha2){
                            $join->on('f.id_cursos','=','tc.id')
                            ->leftjoin('tabla_supre as ts','ts.id','=','f.id_supre')                    
                            ->where('ts.status','Validado')
                            ->where('ts.fecha_validacion','>=',$fecha1)
                            ->where('ts.fecha_validacion','<=',$fecha2);                    
                        }) ;
                                  
                    })    
                    ->leftjoin('poa', function ($join) use($ejercicio){
                        $join->on('poa.tbl_unidades_unidad','=','u.unidad')
                        //->on('poa.ze','=','tc.ze')
                        ->where('poa.ejercicio','=',$ejercicio)               
                        ->where('poa.tbl_unidades_plantel','>',0);
                    })             
                    ->leftjoin('tbl_cursos as tc2', function ($join) use($fecha1, $fecha2, $desercion){
                        $join->on('tc2.id','=','tc.id')
                        ->where('tc2.proceso_terminado',true)
                        ->where('tc2.fecha_apertura','>=',$fecha1)
                        ->where('tc2.fecha_apertura','<=',$fecha2);
                       
                    })            
                    ->leftjoin('tbl_cursos as tc3', function ($join) use($desercion){
                        $join->on('tc3.id','=','tc2.id')                               
                        ->joinSub($desercion, 'd', function($join){
                            $join->on('tc3.id','=','d.id_curso');
                        });
                    })                         
                    ->wherein('u.ubicacion', $unidades)->orwherein('u.unidad', $unidades)            
                    ->groupby('tc.unidad','u.unidad','poa.total_cursos','u.order_poa','poa.total_horas','tc.cct','poa.id',
                    'poa.tbl_unidades_plantel','tc.ze')//->get();
                    ->union($data_unidad)
                    ->union($data_total);
                    $data2 = $data->toSql();
                    $data = DB::table(DB::raw("($data2 order by id,ze asc) as a"))->mergeBindings($data)->get();

                break;                
            }



           

        }       
        return $data;        
    }

    public function xls(Request $request){        
        $data = $this->data($request);        
        if(count($data)>0){
            if($request->opciones){
                switch($request->opciones){
                    case "CURSOS":                        
                        $head = ['UNIDAD/ACCION_MOVIL/ZONA','CURSOS_PROGRAMADOS','CURSOS_AUTORIZADOD','DIFERENCIA','SUFICIENCIA_AUTORIZADA',
                        'HORAS_PROGRAMADAS','HORAS_AUTORIZADAS','DIFERENCIA','CURSOS_REPORTADOS_FT','INSCRITOS','EGRESADOS',
                        'DESERCION'];
                        $title = "POA&".$request->opciones;
                        $name = $title."_".date('Ymd').".xlsx";
                        $view = 'consultas.poa.excel_poa';                        
                    break;
                    case "CUOTA":                        
                        $head = ['UNIDAD/ACCION_MOVIL/ZONA','CURSOS_PROGRAMADOS','CURSOS_AUTORIZADOD','DIFERENCIA','SUFICIENCIA_AUTORIZADA',
                        'HORAS_PROGRAMADAS','HORAS_AUTORIZADAS','DIFERENCIA','CURSOS_REPORTADOS_FT','INSCRITOS','EGRESADOS',
                        'DESERCION'];
                        $title = "POA&".$request->opciones;
                        $name = $title."_".date('Ymd').".xlsx";
                        $view = 'consultas.poa.excel_cuota';                        
                    break;
                }                
                if(count($data)>0)return Excel::download(new ExportExcelPOA($data,$head, $title,$view), $name);
            }
        }else{ return "NO HAY REGISTROS QUE MOSTRAR";exit;}
    }
}
