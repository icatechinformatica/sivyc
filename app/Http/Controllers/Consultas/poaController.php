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
                    ->where('tc.fecha_turnado','>=',$fecha1)
                    ->where('tc.fecha_turnado','<=',$fecha2)
                    ->groupby('tc.unidad','ti.costo')
                    ->orderby('ti.costo','ASC')->orderby(DB::raw('count(distinct(tc.id))'),'DESC')->orderby('tc.unidad','ASC')
                    ->get();
                break;
                default:
                    $desercion = DB::table('tbl_inscripcion as d')
                    ->select('d.id_curso',DB::raw("sum(CASE WHEN d.status= 'INSCRITO' and d.calificacion='NP' THEN 1 ELSE 0 END)  as desercion"))
                    ->groupby('d.id_curso');

                /*TOTALES*/                    
                    $data_total = DB::table('tbl_unidades as u')
                    ->select('poa.id_unidad','u.ubicacion as unidad',DB::raw('poa.total_cursos as cursos_programados'),
                    DB::raw("sum(CASE WHEN tc.status_curso='AUTORIZADO' THEN 1 ELSE 0 END)  as cursos_autorizados"),
                    DB::raw('count(ts.*) as suficiencia_autorizada'),
                    DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN 1 ELSE 0 END ) as cursos_reportados'),                    
                    DB::raw('MAX(poa.total_horas) as horas_programadas'),
                    DB::raw("sum(CASE WHEN tc.status_curso='AUTORIZADO' THEN tc.dura ELSE 0 END)  as horas_impartidas"),
                    DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN tc.hombre+tc.mujer ELSE 0 END ) as inscritos'),
                    DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN d.desercion ELSE 0 END ) as desercion'),
                    DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN tc.hombre+tc.mujer ELSE 0 END )- sum(CASE WHEN tc.proceso_terminado=true THEN d.desercion ELSE 0 END ) as egresados'),
                    DB::raw("sum(CASE WHEN tc.proceso_terminado=true THEN tc.dura ELSE 0 END)  as horas_reportadas"),
                    DB::raw("'-1' as plantel"),
                    DB::raw("'A' as ze"),DB::raw("'A' as poa_ze"),DB::raw("1 as orden")
                    )

                    ->leftjoin('poa', function ($join) use($ejercicio){
                        $join->on('poa.tbl_unidades_unidad','=','u.ubicacion')
                        ->where('poa.ejercicio',$ejercicio)
                        ->where('poa.id_plantel','=',0);

                    })
                    ->leftjoin('tbl_cursos as tc', function ($join) use($fecha1, $fecha2,$ejercicio,$desercion){
                        $join->on('tc.unidad','=','u.unidad')                        
                        ->whereBetween('fecha_apertura', [$fecha1, $fecha2])   
                        ->leftjoinSub($desercion, 'd', function($join){
                            $join->on('tc.id','=','d.id_curso');
                        })
                        ->leftjoin('folios as f', function ($join) use($fecha1, $fecha2){
                            $join->on('f.id_cursos','=','tc.id')
                            ->leftjoin('tabla_supre as ts','ts.id','=','f.id_supre')
                            ->where('ts.status','Validado')
                            ->whereBetween('fecha_validacion', [$fecha1, $fecha2]);                            
                        }) ;
                    })  
                    ->wherein('u.ubicacion', $unidades)->orwherein('u.unidad', $unidades)
                    ->groupby('poa.id_unidad','u.ubicacion','poa.id');               

                
                 /*TOTALES POR ZONA*/  
                 
                 $data_total_ze = DB::table('tbl_unidades as u')
                 ->select(DB::raw('CASE WHEN COALESCE(poa.id_unidad)>0 THEN poa.id_unidad ELSE  0 END as id_unidad'),'u.ubicacion as unidad',
                 
                 DB::raw("(SELECT sum(p.total_cursos) FROM poa as p, tbl_unidades as tu
                 WHERE p.id_unidad=tu.id  and p.ejercicio='2022' and p.ze is not null and p.id_unidad=poa.id_unidad and p.ze=poa.ze
                 group by p.id_unidad,tu.unidad,p.ze
                 order by p.id_unidad) AS total_curso"),                 
                 DB::raw("sum(CASE WHEN tc.status_curso='AUTORIZADO' THEN 1 ELSE 0 END)  as cursos_autorizados"),
                 DB::raw('count(ts.*) as suficiencia_autorizada'),
                 DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN 1 ELSE 0 END ) as cursos_reportados'),
                 DB::raw('MAX(poa.total_horas) as horas_programadas'),
                 DB::raw("sum(CASE WHEN tc.status_curso='AUTORIZADO' THEN tc.dura ELSE 0 END)  as horas_impartidas"),
                 DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN tc.hombre+tc.mujer ELSE 0 END ) as inscritos'),
                 DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN d.desercion ELSE 0 END ) as desercion'),
                 DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN tc.hombre+tc.mujer ELSE 0 END )- sum(CASE WHEN tc.proceso_terminado=true THEN d.desercion ELSE 0 END ) as egresados'),
                 DB::raw("sum(CASE WHEN tc.proceso_terminado=true THEN tc.dura ELSE 0 END)  as horas_reportadas"),
                 DB::raw("'-1' as plantel"),'poa.ze',DB::raw("'poa.ze' as poa_ze"),DB::raw("2 as orden")
                 )
                 ->leftjoin('poa', function ($join) use($ejercicio){
                     $join->on('poa.tbl_unidades_unidad','=','u.unidad')
                     ->where('poa.ejercicio',$ejercicio)
                     ->where('poa.id_plantel','>',0);

                 })               
                 ->leftjoin('tbl_cursos as tc', function ($join) use($fecha1, $fecha2,$ejercicio,$desercion){
                     $join->on('tc.unidad','=','u.unidad')
                     ->whereBetween('fecha_apertura', [$fecha1, $fecha2])
                     ->leftjoinSub($desercion, 'd', function($join){
                         $join->on('tc.id','=','d.id_curso');
                     })
                     ->leftjoin('folios as f', function ($join) use($fecha1, $fecha2){
                         $join->on('f.id_cursos','=','tc.id')
                         ->leftjoin('tabla_supre as ts','ts.id','=','f.id_supre')
                         ->where('ts.status','Validado')
                         ->whereBetween('fecha_validacion', [$fecha1, $fecha2]);
                     }) ;
                 })
                  ->wherein('u.ubicacion', $unidades)->orwherein('u.unidad', $unidades)
                 ->groupby('u.ubicacion','poa.id_unidad','u.ze','poa.ze');

                 /***DETALLE POR UNIDAD */
                 
                 $data_unidad = DB::table('tbl_unidades as u')
                 ->select(DB::raw('poa.id_unidad'),'u.unidad','poa.total_cursos as cursos_programados',
                  DB::raw("sum(CASE WHEN tc.status_curso='AUTORIZADO' THEN 1 ELSE 0 END)  as cursos_autorizados"),
                  DB::raw('count(ts.*) as suficiencia_autorizada'),
                  DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN 1 ELSE 0 END ) as cursos_reportados'),                    
                  'poa.total_horas as horas_programadas',
                  DB::raw("sum(CASE WHEN tc.status_curso='AUTORIZADO' THEN tc.dura ELSE 0 END)  as horas_impartidas"),
                  DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN tc.hombre+tc.mujer ELSE 0 END ) as inscritos'),
                  DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN d.desercion ELSE 0 END ) as desercion'),
                  DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN tc.hombre+tc.mujer ELSE 0 END )- sum(CASE WHEN tc.proceso_terminado=true THEN d.desercion ELSE 0 END ) as egresados'),
                  DB::raw("sum(CASE WHEN tc.proceso_terminado=true THEN tc.dura ELSE 0 END)  as horas_reportadas"),                   
                 'poa.tbl_unidades_plantel as plantel',DB::raw("'Z' as ze"),DB::raw("'Z' as poa_ze"),DB::raw("poa.id as orden")
                 )
                 ->leftjoin('poa', function ($join) use($ejercicio){
                     $join->on('poa.tbl_unidades_unidad','=','u.unidad')
                     ->where('poa.ejercicio',$ejercicio)                     
                     ->where('poa.id_plantel','>',0);
                 })
                 ->leftjoin('tbl_cursos as tc', function ($join) use($fecha1, $fecha2,$ejercicio,$desercion){
                      $join->on('tc.unidad','=','u.unidad')                        
                      ->whereBetween('fecha_apertura', [$fecha1, $fecha2])   
                      ->leftjoinSub($desercion, 'd', function($join){
                          $join->on('tc.id','=','d.id_curso');
                      })
                      ->leftjoin('folios as f', function ($join) use($fecha1, $fecha2){
                          $join->on('f.id_cursos','=','tc.id')
                          ->leftjoin('tabla_supre as ts','ts.id','=','f.id_supre')
                          ->where('ts.status','Validado')
                          ->whereBetween('fecha_validacion', [$fecha1, $fecha2]);                            
                      }) ;
                  })                                  
                 ->wherein('u.ubicacion', $unidades)->orwherein('u.unidad', $unidades)
                 ->groupby('tc.unidad','u.unidad','poa.total_cursos','poa.total_horas','tc.cct','poa.id',
                 'poa.tbl_unidades_plantel')
                 ->union($data_total_ze)
                 ->union($data_total);
                 $data2 = $data_unidad->toSql();
                 $data = DB::table(DB::raw("($data2 order by id_unidad,orden ASC) as a"))->mergeBindings($data_unidad)->get();
                 /*
                 $data_total = $data_total->wherein('u.ubicacion', $unidades)->orwherein('u.unidad', $unidades)
                 ->union($data_total_ze);
                 $data2 = $data_total->toSql();
                 $data = DB::table(DB::raw("($data2 order by id_unidad,orden ASC) as a"))->mergeBindings($data_total)->get();
               */
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
                        'DESERCION','HORAS_REPORTADAS'];
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
