<?php

namespace App\Http\Controllers\Consultas;

use App\Http\Controllers\Controller;
use App\Models\cat\catUnidades;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\curso;
use App\Excel\xls;
use Maatwebsite\Excel\Facades\Excel;

class gruposaperturadosController extends Controller ///DTA
{
    use catUnidades;
    function __construct(){        
        $this->ejercicio = date("y");
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->data = $this->unidades_user('unidad');
            $this->unidades = $this->data['unidades'];      
            unset($this->unidades["ECE-CONOCER"]);      
            return $next($request);
        });
    }

    public function index(Request $request){
        $message = $data = $unidad  = $fecha1 = $fecha2 =  $valor = NULL;
        $unidades =$this->unidades;
        if(session('message')) $message = session('message');
        $unidad = $request->unidad;
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;
        $opcion = $request->opcion;
        $valor = $request->valor;

        $data = $this->data($request);   
        $values = $request->all();
        return view('consultas.gruposaperturados', compact('message','unidades','data', 'values'));        
    }

    public function xls(Request $request){
        $data = $this->data($request, true);     
        $opcion = $request->opcion;

        if(count($data)>0){
            #CONDICION DEL ENCABEZADO DEL EXCEL
            if($opcion == 'EXONERADOS'){
                $head = ['#','UNIDAD/ACCIÓN MÓVIL','CLAVE','ESPECIALIDAD','CURSO','AGENDA','INSTRUTOR(A)','CRITERIO DE PAGO','HORAS','COSTOXHORA','IMPORTE',
                'CAPACITACIÓN','MODALIDAD','HOMBRES','MUJERES','CUPO','ESTATUS','VINCULADOR(A)',
                'TIPO PAGO','CUOTA','FORMACIÓN','MUNICIPIO','DEPENDENCIA BENEFICIADA',
                'SOLICITUD ARC-01','FECHA ARC-01','OBSERVACIONES_ARC01','AUTORIZACION ARC-01','FECHA AUTORIZACION ARC01','ESTATUS_ARC01',
                'SOLICITUD ARC-02','AUTORIZACIONARC-ARC-02', 'OBSERVACIONES_ARC02',
                'ESPACIO FíSICO','PAGO INSTRUCTOR','PLATAFORMA',
                'MEMO_REDU/EXO', 'FECHA_REDU/EXO','REDU/EXO', 'ESTATUS_REDU/EXO','OBSERVACIONES_EXO', 'NO CONVENIO', 'NO OFICIO', 'FECHA OFICIO'];
            }else{
                $head = ['#','UNIDAD/ACCIÓN MÓVIL','CLAVE','ESPECIALIDAD','CURSO','AGENDA','INSTRUTOR(A)','CRITERIO DE PAGO','HORAS','COSTOXHORA','IMPORTE',
                'CAPACITACIÓN','MODALIDAD','HOMBRES','MUJERES','CUPO','ESTATUS','VINCULADOR(A)',
                'TIPO PAGO','CUOTA','FORMACIÓN','MUNICIPIO','DEPENDENCIA BENEFICIADA',
                'SOLICITUD ARC-01','FECHA ARC-01','OBSERVACIONES_ARC01','AUTORIZACION ARC-01','FECHA AUTORIZACION ARC01','ESTATUS_ARC01',
                'SOLICITUD ARC-02','AUTORIZACIONARC-ARC-02', 'OBSERVACIONES_ARC02',
                'ESPACIO FíSICO','PAGO INSTRUCTOR','PLATAFORMA'];
            }
            
            $title = "GRUPOS_".$opcion;
            $name = "GRUPOS_".$opcion."_".date('Ymd').".xlsx";
            
            return Excel::download(new xls($data,$head, $title), $name);
        }else { return "NO REGISTROS QUE MOSTRAR";exit;}
        

    }

    public function data(Request $request, $xls = false){
        $unidad = $request->unidad; 
        $fecha1 = $request->fecha1;
        if(!$request->fecha2) $fecha2 = $request->fecha1;
        else $fecha2 = $request->fecha2;
        $opcion = $request->opcion;
        $valor = $request->valor;
        $data = null;
        if($unidad OR $fecha1 OR $fecha2 OR $valor){                      
           if (in_array($opcion, ['AUTORIZADOS', 'INICIADOS'])) {
                // Ordena primero por unidad y luego por fecha de inicio
                $ordenColumnas = 'tc.unidad ASC, tc.inicio DESC';
            } else {
                // Ordena primero por unidad y luego por fecha de término
                $ordenColumnas = 'tc.unidad ASC, tc.termino DESC';
            }
            $subquery = DB::table('alumnos_registro')->select('realizo','folio_grupo', DB::raw('ROW_NUMBER() OVER(PARTITION BY folio_grupo ORDER BY id ASC) as rn'));
            $data = DB::table('tbl_cursos as tc')->where('clave','!=','0')     
            ->select(
                DB::raw("ROW_NUMBER() OVER (ORDER BY {$ordenColumnas} ) as consec"),
                'tc.unidad','tc.clave','tc.espe','tc.curso',
                DB::raw("
                        (
                            SELECT string_agg(
                            CASE
                                WHEN DATE(\"start\") = DATE(\"end\") THEN TO_CHAR(DATE(\"end\"), 'DD/MM/YYYY')
                                ELSE TO_CHAR(DATE(\"start\"), 'DD/MM/YYYY') || ' - ' || TO_CHAR(DATE(\"end\"), 'DD/MM/YYYY')
                            END

                            || ' ' ||
                            CASE
                                WHEN TO_CHAR(\"start\", 'MI') = '00' THEN TO_CHAR(\"start\", 'HH24')
                                ELSE TO_CHAR(\"start\", 'HH24:MI')
                            END || '-' ||
                            CASE
                                WHEN TO_CHAR(\"end\", 'MI') = '00' THEN TO_CHAR(\"end\", 'HH24')
                                ELSE TO_CHAR(\"end\", 'HH24:MI')
                            END || 'h. (' ||
                            TO_CHAR(
                                (EXTRACT(EPOCH FROM ((CAST(\"end\" AS time) - CAST(\"start\" AS time)))) / 3600) *
                                ((DATE_TRUNC('day', \"end\")::date - DATE_TRUNC('day', \"start\")::date) + 1),
                                'FM999990.0'
                            ) || 'hrs.)',
                            E'\n'
                            ORDER BY DATE(start)
                            ) AS agenda_texto
                            FROM agenda
                            WHERE id_curso = tc.folio_grupo
                        )::text AS agenda
                    "),
                'tc.nombre','tc.cp','tc.dura','ch.monto as costo',DB::raw("(ch.monto * tc.dura) as importe"),'tc.tcapacitacion','tc.mod',
                'tc.hombre','tc.mujer',DB::raw("(tc.mujer + tc.hombre) as cupo"),'tc.status','ar.realizo as vinculador')
            
            
            ->leftJoinSub($subquery, 'ar', function ($join) {
                $join->on('tc.folio_grupo', '=', 'ar.folio_grupo')
                ->where('ar.rn', '=', 1); 
            })
            ->join('criterio_pago as cp', 'cp.id', '=', 'tc.cp')
            ->leftJoin(DB::raw("LATERAL (
                SELECT (elem->>'monto')::numeric AS monto
                FROM jsonb_array_elements(
                    CASE 
                        WHEN tc.ze = 'II'  THEN ze2->'vigencias'
                        WHEN tc.ze = 'III' THEN ze3->'vigencias'
                    END
                ) elem
                WHERE (elem->>'fecha')::date <= tc.inicio
                ORDER BY (elem->>'fecha')::date DESC
                LIMIT 1
            ) AS ch"), DB::raw("TRUE"), '=', DB::raw("TRUE"));
            

            if($xls){                                      
                $data->addselect(
                    DB::raw("
                            CASE
                            WHEN tipo = 'EXO' THEN 'EXONERACIÓN'
                            WHEN tipo = 'EPAR' THEN 'REDUCCIÓN DE CUOTA'
                            ELSE 'PAGO ORDINARIO'
                            END as tpago"),
                    'costo as cuota','tipo_curso','muni','depen',
                    'munidad','fecha_arc01',
                     DB::raw("
                        (
                        CASE
                            WHEN tc.nota ILIKE '%INSTRUCTOR%' THEN tc.nota
                        ELSE
                            CASE
                                WHEN (tc.vb_dg = true OR tc.clave!='0') AND tc.modinstructor = 'ASIMILADOS A SALARIOS' THEN 'INSTRUCTOR POR HONORARIOS ' || tc.modinstructor || ', '
                                WHEN (tc.vb_dg = true  OR tc.clave !='0') AND tc.modinstructor = 'HONORARIOS' THEN 'INSTRUCTOR POR ' || tc.modinstructor || ', '
                                ELSE ''
                            END
                            ||
                            CASE
                                WHEN tc.tipo = 'EXO' THEN 'MEMORÁNDUM DE EXONERACIÓN No. ' || tc.mexoneracion || ', '
                                WHEN tc.tipo = 'EPAR' THEN 'MEMORÁNDUM DE REDUCCIÓN DE CUOTA No. ' || tc.mexoneracion || ', '
                                ELSE ''
                            END
                            ||
                            CASE
                                WHEN tc.tipo != 'EXO' THEN
                                    'CUOTA DE RECUPERACIÓN $' || ROUND((tc.costo)/(tc.hombre+tc.mujer),2) || ' POR PERSONA, ' ||
                                    'TOTAL CURSO $' || TO_CHAR(ROUND(tc.costo, 2), 'FM999,999,999.00')
                                ELSE ''
                            END
                            || ', MEMORÁNDUM DE VALIDACIÓN DEL INSTRUCTOR ' || tc.instructor_mespecialidad ||'.'
                            || ' ' || COALESCE(tc.nota, '')
                        END
                        ) AS nota
                    "),

                    
                    'mvalida', 'fecha_apertura', 'status_curso', 'nmunidad', 'fecha_arc01', 'tc.observaciones as obs_arc02', 'nmacademico',
                    'efisico', 'modinstructor','medio_virtual');
            }
                if($opcion == "EXONERADOS"){
                    $data->join('exoneraciones as exo', 'exo.folio_grupo', '=', 'tc.folio_grupo');
                    $data->addselect(
                        'exo.no_memorandum', 'exo.fecha_memorandum', 'exo.status',
                        DB::raw("
                            CASE
                            WHEN exo.tipo_exoneracion = 'EXO' THEN 'EXONERACIÓN'
                            WHEN exo.tipo_exoneracion = 'EPAR' THEN 'REDUCCIÓN'
                            END as tpago_exo"),
                        'exo.observaciones','exo.no_convenio','exo.noficio', 'exo.foficio');
                }
            
        
            ##FILTRADOS
            if($valor){
                $data = $data->where(function ($q) use ($valor) {
                    $q->where('tc.clave', 'like', "%$valor%")
                    ->orWhere('tc.munidad', 'like', "%$valor%");
                });
            }else{         
                switch($opcion){
                    case "AUTORIZADOS":
                        $data->whereBetween('tc.fecha_apertura', [$fecha1, $fecha2]);                        
                        $data =  $data->where('tc.status_curso','AUTORIZADO')->orderby('tc.unidad')->orderby('tc.inicio','DESC');
                    break;
                    case "INICIADOS":
                        $data->whereBetween('tc.inicio', [$fecha1, $fecha2]);                         
                        $data =  $data->orderby('tc.unidad')->orderby('tc.inicio','DESC');
                    break;
                    case "TERMINADOS":
                        $data->whereBetween('tc.termino', [$fecha1, $fecha2]);                         
                        $data =  $data->orderby('tc.unidad')->orderby('tc.termino','DESC');
                    break;
                    case "EXONERADOS":
                        $data->whereBetween('tc.termino', [$fecha1, $fecha2]);                         
                        $data = $data->where('exo.status','=', 'AUTORIZADO');
                        $data =  $data->orderby('tc.unidad')->orderby('tc.termino','DESC');
                    break;
                }               
            } 
            if(count($unidad)>0) $data = $data->whereIn('tc.unidad',$unidad);
            else $data = $data->whereIn('tc.unidad',$this->unidades);            
            $data = $data->get(); 
        }
        return $data;
    }

}
