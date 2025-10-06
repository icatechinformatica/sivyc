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
            $unidades = $this->data['unidades'];
            unset($unidades["ECE-CONOCER"]);
            session(['unidades' => $unidades]);
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

        $unidades = session('unidades');
        if(!$request->fecha1) $request->fecha1 = date('Y-01-01');
        if(!$request->fecha2) $request->fecha2 = date('Y-m-d');

        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        $ejercicio = date('Y',strtotime($fecha1));

        if($fecha1 AND $fecha2){
            switch($request->opciones){
                case "CUOTA":
                    $desercion = DB::table('tbl_inscripcion as d')
                    ->select('d.id_curso', DB::raw('MIN(d.costo) as icosto'), DB::raw("sum(CASE WHEN d.status= 'INSCRITO' and d.calificacion='NP' THEN 1 ELSE 0 END)  as desercion"))
                    ->groupby('d.id_curso');

                    $data = DB::table('tbl_cursos as tc')
                        ->select('tc.unidad',
                        DB::raw('icosto as costo'),
                        DB::raw('count(tc.id) as cursos_reportados'),
                        DB::raw("sum(hombre+mujer) as inscritos"),
                        DB::raw('sum(d.desercion) as desercion'),
                        DB::raw('sum(c.costo*(hombre+mujer)) as importe')
                        )
                        ->leftjoin('cursos as c','c.id','tc.id_curso')
                        ->leftjoinSub($desercion, 'd', function($join){
                            $join->on('tc.id','=','d.id_curso');
                        })
                        ->where('tc.proceso_terminado',true)->where('tc.status_curso','AUTORIZADO')
                        ->where('tc.fecha_turnado','>=',$fecha1)
                        ->where('tc.fecha_turnado','<=',$fecha2)
                        ->groupby('tc.unidad','icosto')
                        ->orderby('icosto','ASC')->orderby(DB::raw('count(tc.id)'),'DESC')->orderby('tc.unidad','ASC')
                        ->get();
                break;
                default:
                    $desercion = DB::table('tbl_inscripcion as d')
                    ->select('d.id_curso',DB::raw("sum(CASE WHEN d.status= 'INSCRITO' and d.calificacion='NP' THEN 1 ELSE 0 END)  as desercion"))
                    ->groupby('d.id_curso');

                    $inicio_nuevoTab = date('Y-m-d',strtotime('2023-10-12')); //renumeración de id de criterios


                    $query_costos = "SUM(CASE
                        WHEN tc.ze ='II' THEN
                        (SELECT
                                CASE WHEN impuestos = 'false' THEN (monto::numeric * tc.dura)+(monto::numeric * tc.dura*.16)
                                ELSE monto::numeric * tc.dura
                                END
                            FROM ( SELECT id,
                                (jsonb_array_elements(ze2->'vigencias')->>'fecha')::date AS fecha_vigencia,
                                (jsonb_array_elements(ze2->'vigencias')->>'monto')::numeric AS monto,
                                (jsonb_array_elements(ze2->'vigencias')->>'incluye_impuestos') AS impuestos
                                FROM criterio_pago
                            WHERE  id =
                                CASE
                                    WHEN  fecha_apertura<'2023-10-12' and tc.cp>=5  THEN tc.cp-1
                                    WHEN fecha_apertura<'2023-10-12' and tc.cp=5 THEN 55
                                    ELSE tc.cp
                                END
                            ) subquery  WHERE  tc.fecha_apertura >= fecha_vigencia ORDER BY fecha_vigencia DESC LIMIT 1)
                        WHEN tc.ze ='III' THEN
                        (SELECT
                                CASE WHEN impuestos = 'false' THEN (monto::numeric * tc.dura)+(monto::numeric * tc.dura*.16)
                                ELSE monto::numeric * tc.dura
                                END
                            FROM ( SELECT id,
                                (jsonb_array_elements(ze3->'vigencias')->>'fecha')::date AS fecha_vigencia,
                                (jsonb_array_elements(ze3->'vigencias')->>'monto')::numeric AS monto,
                                (jsonb_array_elements(ze3->'vigencias')->>'incluye_impuestos') AS impuestos
                                FROM criterio_pago
                            WHERE id =
                                CASE
                                    WHEN  fecha_apertura<'2023-10-12' and tc.cp>=5  THEN tc.cp-1
                                    WHEN fecha_apertura<'2023-10-12' and tc.cp=5 THEN 55
                                    ELSE tc.cp
                                END
                            ) subquery  WHERE  tc.fecha_apertura >= fecha_vigencia ORDER BY fecha_vigencia DESC LIMIT 1)
                        END) as costo_aperturado";

            /*TOTAL POR UNIDAD*/

                    $data_total = DB::table('tbl_unidades as u')
                    ->select('poa.id_unidad','u.ubicacion as unidad',DB::raw('poa.total_cursos as cursos_programados'),
                    DB::raw('COUNT(DISTINCT tc.id) as cursos_autorizados'),
                    DB::raw('COUNT(DISTINCT f.*) as suficiencia_autorizada'),
                    DB::raw("COUNT(DISTINCT CASE WHEN p.status_recepcion IS NOT NULL AND p.status_recepcion <> 'Rechazado' THEN p.id END) AS recep_financ"),
                    DB::raw("COUNT(DISTINCT CASE WHEN p.status_recepcion='recepcion tradicional' OR p.status_recepcion='VALIDADO' THEN p.id END) AS valid_financ"),

                     DB::raw("COUNT(DISTINCT CASE WHEN tc.proceso_terminado = true THEN tc.id END) as cursos_reportados"),
                    DB::raw('MAX(poa.total_horas) as horas_programadas'),
                    DB::raw("sum(tc.dura)  as horas_impartidas"),

                    /***COSTOS **/
                    DB::raw($query_costos),
                    DB::raw("sum(CASE WHEN supre.status='Validado' THEN  f.importe_total ELSE 0 END)  as costo_supre"),
                    DB::raw("sum(CASE WHEN p.status_transferencia='PAGADO' OR f.status='Finalizado' THEN f.importe_total ELSE 0 END)  as pagado"),

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
                    ->leftjoin('tbl_cursos as tc', function ($join) use($fecha1, $fecha2,$desercion){
                        $join->on('tc.unidad','=','u.unidad')
                        ->where('tc.status_curso','AUTORIZADO')
                        ->whereBetween('fecha_apertura', [$fecha1, $fecha2])

                        ->leftjoinSub($desercion, 'd', function($join){
                            $join->on('tc.id','=','d.id_curso');
                        })
                        ->leftjoin('folios as f', function ($join){
                            $join->on('f.id_cursos','=','tc.id')
                            ->leftjoin('tabla_supre as supre', function ($join){
                                $join->on('f.id_supre','=','supre.id');
                            });
                        })

                        ->leftjoin('pagos as p', function ($join){
                            $join->on('p.id_curso','=','tc.id');
                        });

                    })
                    //->where('u.unidad','=','JIQUIPILAS')
                    ->wherein('u.ubicacion', $unidades)->orwherein('u.unidad', $unidades)
                    ->groupby('poa.id_unidad','u.ubicacion','poa.id');


            /*TOTALES POR ZONA*/
                $data_total_ze = DB::table('tbl_unidades as u')
                 ->select(DB::raw('CASE WHEN COALESCE(poa.id_unidad)>0 THEN poa.id_unidad ELSE  0 END as id_unidad'),'u.ubicacion as unidad',
                /*CURSOS */
                DB::raw("(SELECT sum(p.total_cursos) FROM poa as p, tbl_unidades as tu
                 WHERE p.id_unidad=tu.id  and p.ejercicio='$ejercicio' and p.ze is not null and p.id_unidad=poa.id_unidad and p.ze=poa.ze
                 group by p.id_unidad,tu.unidad,p.ze
                 order by p.id_unidad) AS total_curso"),
                DB::raw("count(tc.*)  as cursos_autorizados"),
                DB::raw('count(f.*) as suficiencia_autorizada'),
                DB::raw("SUM(CASE WHEN p.status_recepcion IS NOT NULL AND p.status_recepcion <> 'Rechazado' THEN 1 ELSE 0 END) AS recep_financ"),
                DB::raw("SUM(CASE WHEN p.status_recepcion='recepcion tradicional' OR p.status_recepcion = 'VALIDADO' THEN 1 ELSE 0 END) AS valid_financ"),

                DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN 1 ELSE 0 END ) as cursos_reportados'),
                /*HORAS */
                DB::raw("(SELECT SUM(total_horas) FROM poa  as tmp WHERE tmp.ze=poa.ze and tmp.id_unidad = poa.id_unidad and tmp.ejercicio = '$ejercicio')"),
                DB::raw("sum(tc.dura)  as horas_impartidas"),

                 /***COSTOS ***/
                DB::raw($query_costos),
                DB::raw("sum(CASE WHEN supre.status='Validado' THEN  f.importe_total ELSE 0 END)  as costo_supre"),
                DB::raw("sum(CASE WHEN p.status_transferencia='PAGADO' OR f.status='Finalizado' THEN f.importe_total ELSE 0 END)  as pagado"),

                /***FORMATO T ***/
                 DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN tc.hombre+tc.mujer ELSE 0 END ) as inscritos'),
                 DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN d.desercion ELSE 0 END ) as desercion'),
                 DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN tc.hombre+tc.mujer ELSE 0 END )- sum(CASE WHEN tc.proceso_terminado=true THEN d.desercion ELSE 0 END ) as egresados'),
                 DB::raw("sum(CASE WHEN tc.proceso_terminado=true THEN tc.dura ELSE 0 END)  as horas_reportadas"),
                 DB::raw("'-1' as plantel"),'poa.ze',DB::raw("'poa.ze' as poa_ze"),DB::raw("
                 CASE WHEN poa.ze='III' THEN 3 ELSE 20 END
                  as orden")
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

                     ->leftjoin('folios as f', function ($join){
                        $join->on('f.id_cursos','=','tc.id')
                        ->leftjoin('tabla_supre as supre', function ($join){
                            $join->on('f.id_supre','=','supre.id');
                        });
                    })

                    ->leftjoin('pagos as p', function ($join){
                        $join->on('p.id_curso','=','tc.id');
                    });
                 })
                //->where('u.unidad','=','REFORMA')
                ->wherein('u.ubicacion', $unidades)->orwherein('u.unidad', $unidades)
                ->WhereNotNull('poa.ze')->WhereNotNull('u.ze')
                 ->groupby('u.ubicacion','poa.id_unidad','u.ze','poa.ze');

                 /*->get();

                 dd($data_total_ze);*/

            /***DETALLE POR UNIDAD/ACCION MOVIL */

                 $data_unidad = DB::table('tbl_unidades as u')
                 ->select(DB::raw('poa.id_unidad'),'u.unidad','poa.total_cursos as cursos_programados',
                  DB::raw("count(tc.*)  as cursos_autorizados"),
                  DB::raw('count(f.*) as suficiencia_autorizada'),
                  DB::raw("COUNT(CASE WHEN p.status_recepcion IS NOT NULL AND p.status_recepcion <> 'Rechazado' THEN 1 ELSE NULL END) AS recep_financ"),
                  DB::raw("count(CASE WHEN p.status_recepcion='recepcion tradicional' OR p.status_recepcion = 'VALIDADO' THEN 1 ELSE NULL END) AS valid_financ"),
                  DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN 1 ELSE 0 END ) as cursos_reportados'),
                  'poa.total_horas as horas_programadas',
                  DB::raw("sum(tc.dura)  as horas_impartidas"),

                  /**COSTOS***/
                  DB::raw($query_costos),
                  DB::raw("sum(CASE WHEN supre.status='Validado' THEN  f.importe_total ELSE 0 END)  as costo_supre"),
                  DB::raw("sum(CASE WHEN p.status_transferencia='PAGADO' OR f.status='Finalizado' THEN f.importe_total ELSE 0 END)  as pagado"),

                  /**FORMATO T **/
                  DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN tc.hombre+tc.mujer ELSE 0 END ) as inscritos'),
                  DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN d.desercion ELSE 0 END ) as desercion'),
                  DB::raw('sum(CASE WHEN tc.proceso_terminado=true THEN tc.hombre+tc.mujer ELSE 0 END )- sum(CASE WHEN tc.proceso_terminado=true THEN d.desercion ELSE 0 END ) as egresados'),
                  DB::raw("sum(CASE WHEN tc.proceso_terminado=true THEN tc.dura ELSE 0 END)  as horas_reportadas"),
                 'poa.tbl_unidades_plantel as plantel',DB::raw("'Z' as ze"),DB::raw("'Z' as poa_ze"),
                 DB::raw("CASE WHEN poa.ze='III' THEN poa.id_plantel+3 ELSE poa.id_plantel+20 END as orden")
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
                    ->leftjoin('folios as f', function ($join){
                        $join->on('f.id_cursos','=','tc.id')

                        ->leftjoin('tabla_supre as supre', function ($join){
                            $join->on('f.id_supre','=','supre.id');
                        });
                    })

                    ->leftjoin('pagos as p', function ($join){
                        $join->on('p.id_curso','=','tc.id');
                    });
                  })
                  //->where('u.unidad','=','JIQUIPILAS')
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
                        'RECEPCION_FINANCIEROS','VALIDADO FINANCIEROS',
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
