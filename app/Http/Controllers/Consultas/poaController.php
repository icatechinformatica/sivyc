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

            /*TOTAL GENERAL*/      
                    $vigencia_CP = date('Y-m-d',strtotime('2022-11-01'));
                    
                    $data_total = DB::table('tbl_unidades as u')
                    ->select('poa.id_unidad','u.ubicacion as unidad',DB::raw('poa.total_cursos as cursos_programados'),
                    DB::raw("count(*)  as cursos_autorizados"),
                    DB::raw('count(f.*) as suficiencia_autorizada'),                    
                    DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN 1 ELSE 0 END ) as cursos_reportados'),                    
                    DB::raw('MAX(poa.total_horas) as horas_programadas'),                    
                    DB::raw("sum(tc.dura)  as horas_impartidas"),

                    /***COSTOS **/
                    DB::raw("sum(
                        CASE WHEN tc.inicio>='$vigencia_CP' THEN
                            CASE WHEN tc.ze ='II' THEN
                                (tc.dura*cp.ze2_2022)+(tc.dura*cp.ze2_2022*.16)
                            WHEN tc.ze ='III' THEN
                              (tc.dura*cp.ze3_2022)+(tc.dura*cp.ze3_2022*.16)
                            END
                        ELSE 
                            CASE WHEN tc.ze ='II' THEN
                            (tc.dura*cp.ze2_2021)+(tc.dura*cp.ze2_2021*.16)
                            WHEN tc.ze ='III' THEN
                            (tc.dura*cp.ze3_2021)+(tc.dura*cp.ze3_2021*.16)
                            END
                        END
                    )  as costo_aperturado"),   
                    
                    DB::raw("sum(f.importe_total)  as costo_supre"),                    
                    DB::raw("sum(CASE WHEN f.status='Finalizado' THEN importe_total ELSE 0 END)  as pagado"),
                                        
                    /***FORMATO T **/
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
                        ->where('tc.status_curso','AUTORIZADO')

                        ->whereBetween('fecha_apertura', [$fecha1, $fecha2])   
                        ->leftjoinSub($desercion, 'd', function($join){
                            $join->on('tc.id','=','d.id_curso');
                        })
                        ->leftjoin('criterio_pago as cp',function($join){
                            $join->on('cp.id','=','tc.cp');
                        })
                        ->leftjoin('folios as f', function ($join) use($fecha1, $fecha2){
                            $join->on('f.id_cursos','=','tc.id')                            
                            ->whereNotIn('f.status',['En_Proceso','Rechazado','Cancelado']);                            
                        });
                        
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
                 DB::raw("count(tc.*)  as cursos_autorizados"),
                 DB::raw('count(f.*) as suficiencia_autorizada'),
                 DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN 1 ELSE 0 END ) as cursos_reportados'),
                 DB::raw('MAX(poa.total_horas) as horas_programadas'),
                 DB::raw("sum(tc.dura)  as horas_impartidas"),
                
                 /***COSTOS ***/                
                 DB::raw("sum(
                    CASE WHEN tc.inicio>='$vigencia_CP' THEN
                        CASE WHEN tc.ze ='II' THEN
                            (tc.dura*cp.ze2_2022)+(tc.dura*cp.ze2_2022*.16)
                        WHEN tc.ze ='III' THEN
                          (tc.dura*cp.ze3_2022)+(tc.dura*cp.ze3_2022*.16)
                        END
                    ELSE 
                        CASE WHEN tc.ze ='II' THEN
                        (tc.dura*cp.ze2_2021)+(tc.dura*cp.ze2_2021*.16)
                        WHEN tc.ze ='III' THEN
                        (tc.dura*cp.ze3_2021)+(tc.dura*cp.ze3_2021*.16)
                        END
                    END
                )  as costo_aperturado"),  

                DB::raw("sum(f.importe_total)  as costo_supre"),
                DB::raw("sum(CASE WHEN f.status='Finalizado' THEN importe_total ELSE 0 END)  as pagado"),
                
                /***FORMATO T ***/
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
                     ->where('tc.status_curso','AUTORIZADO')
                     ->whereBetween('fecha_apertura', [$fecha1, $fecha2])
                     ->leftjoinSub($desercion, 'd', function($join){
                         $join->on('tc.id','=','d.id_curso');
                     })
                     ->leftjoin('criterio_pago as cp',function($join){
                        $join->on('cp.id','=','tc.cp');
                    })
                     ->leftjoin('folios as f', function ($join) use($fecha1, $fecha2){
                         $join->on('f.id_cursos','=','tc.id')                         
                         ->whereNotIn('f.status',['En_Proceso','Rechazado','Cancelado']);                         
                     });
                 })
                  ->wherein('u.ubicacion', $unidades)->orwherein('u.unidad', $unidades)
                 ->groupby('u.ubicacion','poa.id_unidad','u.ze','poa.ze');

            /***DETALLE POR UNIDAD/ACCION MOVIL */
                 
                 $data_unidad = DB::table('tbl_unidades as u')
                 ->select(DB::raw('poa.id_unidad'),'u.unidad','poa.total_cursos as cursos_programados',
                  DB::raw("count(tc.*)  as cursos_autorizados"),
                  DB::raw('count(f.*) as suficiencia_autorizada'),
                  DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN 1 ELSE 0 END ) as cursos_reportados'),                    
                  'poa.total_horas as horas_programadas',
                  DB::raw("sum(tc.dura)  as horas_impartidas"),
                  
                  /**COSTOS***/
                    DB::raw("sum(
                        CASE WHEN tc.inicio>='$vigencia_CP' THEN
                            CASE WHEN tc.ze ='II' THEN
                                (tc.dura*cp.ze2_2022)+(tc.dura*cp.ze2_2022*.16)
                            WHEN tc.ze ='III' THEN
                              (tc.dura*cp.ze3_2022)+(tc.dura*cp.ze3_2022*.16)
                            END
                        ELSE 
                            CASE WHEN tc.ze ='II' THEN
                            (tc.dura*cp.ze2_2021)+(tc.dura*cp.ze2_2021*.16)
                            WHEN tc.ze ='III' THEN
                            (tc.dura*cp.ze3_2021)+(tc.dura*cp.ze3_2021*.16)
                            END
                        END
                    )  as costo_aperturado"),  

                  DB::raw("sum(f.importe_total)  as costo_supre"),
                  DB::raw("sum(CASE WHEN f.status='Finalizado' THEN importe_total ELSE 0 END)  as pagado"),
                 
                  /**FORMATO T **/
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
                      ->where('tc.status_curso','AUTORIZADO')                  
                      ->whereBetween('fecha_apertura', [$fecha1, $fecha2])   
                      ->leftjoinSub($desercion, 'd', function($join){
                          $join->on('tc.id','=','d.id_curso');
                      })
                      ->leftjoin('criterio_pago as cp',function($join){
                        $join->on('cp.id','=','tc.cp');
                        })
                      ->leftjoin('folios as f', function ($join) use($fecha1, $fecha2){
                          $join->on('f.id_cursos','=','tc.id')                          
                          ->whereNotIn('f.status',['En_Proceso','Rechazado','Cancelado']);                          
                      });
                  })                                  
                 ->wherein('u.ubicacion', $unidades)->orwherein('u.unidad', $unidades)
                 ->groupby('tc.unidad','u.unidad','poa.total_cursos','poa.total_horas','tc.cct','poa.id',
                 'poa.tbl_unidades_plantel')
                 ->union($data_total_ze)
                 ->union($data_total);
                 $data2 = $data_unidad->toSql();
                 $data = DB::table(DB::raw("($data2 order by id_unidad,orden ASC) as a"))->mergeBindings($data_unidad)->get();                
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
                        'HORAS_PROGRAMADAS','HORAS_AUTORIZADAS','DIFERENCIA',
                        'COSTO_APERTURADOS','COSTO_SUFICIENCIA_AUTORIZADA','DIFERENCIA','PAGADO',
                        'CURSOS_REPORTADOS_FT','INSCRITOS','EGRESADOS',
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
