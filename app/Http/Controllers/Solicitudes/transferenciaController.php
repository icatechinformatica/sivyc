<?php

namespace App\Http\Controllers\Solicitudes;

use App\Events\NotificationEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use App\Models\Permission;
use App\User;


class transferenciaController extends Controller
{
    
    function __construct() {       
    }

    public function index(Request $request){        
        $anio = date('Y');  
        $data =  $cuentas_retiro = [];
        $unidades = DB::table('tbl_unidades')->where('cct', 'LIKE', '%07EI%')->orderby('unidad')->pluck('unidad','unidad');
        $ejercicio = DB::select("SELECT EXTRACT(YEAR FROM ejercicio)  as ejercicio FROM generate_series( ('2023' || '-01-01')::date, current_date, '1 year') as ejercicio");
            $ejercicio = array_column($ejercicio, 'ejercicio');
            $ejercicio = array_combine($ejercicio, $ejercicio);                      
        
        if($request->unidad OR $request->status_transferencia OR $request->valor){
            $cuentasretiro = DB::table('tbl_instituto')->select("cuentas_bancarias->pago_instructor as cuentas")->get();
                $cuentasretiro = json_decode($cuentasretiro[0]->cuentas, true);
            $n=1;
            foreach($cuentasretiro as $k => $v){
                $json_cuentas[$k] =  $v;                
                $cuentas_retiro['{"'.$k.'":"'.$v.'"}'] = "$k: $v";
            }
            //dd($json_cuentas);
            //dd($cuentas_retiro);
            $data = DB::table('pagos as p')
                ->select('p.id','c.unidad_capacitacion','c.numero_contrato','p.no_memo','tc.nombre as instructor','tc.rfc','c.folio_fiscal',
                    'tc.soportes_instructor->banco as banco','tc.soportes_instructor->no_cuenta as cuenta','tc.soportes_instructor->interbancaria as clabe',
                    'c.arch_factura as factura','p.liquido as importe_neto',
                    DB::raw("CASE 
                        WHEN  p.status_transferencia is NULL  AND f.status!='Finalizado' THEN 'PENDIENTE' 
                        WHEN  f.status='Finalizado' THEN 'PAGADO'                     
                        ELSE p.status_transferencia END as status
                    "), 
                    'p.num_layout','tc.curso','tc.clave','c.arch_contrato as contrato'
                )
                ->join('contratos as c','c.id_contrato','p.id_contrato')
                ->join('folios as f','f.id_folios','c.id_folios')
                ->join('tbl_cursos as tc','tc.id','f.id_cursos');
                if($request->unidad) $data = $data->where('c.unidad_capacitacion', $request->unidad);
                
                if($request->status_transferencia){
                    switch($request->status_transferencia){
                        case "PENDIENTE":
                            $data = $data->where('p.status_transferencia', NULL)->where('p.fecha_status', 'like', $request->ejercicio.'%');
                        break;
                        case "PAGADO":
                            $data = $data->where('p.status_transferencia', 'PAGADO')->where('p.fecha_status', 'like', $request->ejercicio.'%');
                        break;
                        case "FINALIZADO":
                            $data = $data->where('f.status', 'Finalizado')->where('p.fecha_status', 'like', $request->ejercicio.'%');
                        break;
                        default:
                            $data = $data->where('p.status_transferencia', $request->status_transferencia)->where('p.fecha_transferencia', 'like', $request->ejercicio.'%');
                        break;
                    }                    
                }else{
                    $data = $data->where('p.fecha_status', 'like', $request->ejercicio.'%');
                }      
                if($request->valor) $data = $data->where(DB::raw('CONCAT(p.num_layout,c.numero_contrato,p.no_memo,tc.nombre)'),'ilike', '%'.$request->valor.'%');
                $data = $data->wherein('status_recepcion',['Citado','Recibido','VALIDADO','recepcion tradicional'])->get();        
            }        
            if(session('message')) $message = session('message');
            elseif(!isset($message)) $message = null;
        return view('solicitudes.transferencia.index',compact('message','data','ejercicio','unidades', 'cuentas_retiro', 'request'));
    }

    public function marcar(Request $request)
    {        
        $msg = "OPERACION NO VALIDA.";        
        if($request->id and $request->check and (($request->num_layout AND $request->estado == "MARCADO") OR $request->estado == "PAGADO")){
            
            if( $request->estado == "MARCADO"){
                if($request->check == "true")
                    $result =  DB::table('pagos')->where('id', '=', $request->id)->update(['status_transferencia' => 'MARCADO','num_layout'=> $request->num_layout, 'fecha_transferencia'=>date('Y-m-d')]);
                else
                    $result =  DB::table('pagos')->where('id', '=', $request->id)->update(['status_transferencia' => NULL,'num_layout'=> NULL, 'fecha_transferencia'=>NULL]);
            }elseif( $request->estado == "PAGADO"){
                if($request->check == "true")
                    $result =  DB::table('pagos')->where('id', '=', $request->id)->whereNull('status_transferencia')->update(['status_transferencia' => 'PAGADO', 'fecha_transferencia'=>date('Y-m-d')]);
                else
                    $result =  DB::table('pagos')->where('id', '=', $request->id)->whereNull('num_layout')->update(['status_transferencia' => NULL,'fecha_transferencia'=>NULL]);
            }

            if($result) $msg = "OPERACION EXITOSA.";
        }
        return $msg;
    }

    public function deshacer(Request $request)
    {        
        $result = false; 
        $message = NULL;
        if($request->num_layout AND $request->movimiento=='DESHACER'){  
            $result =  DB::table('pagos')->where('num_layout', '=', $request->num_layout)->where('status_transferencia', 'GENERADO')->update(['status_transferencia' => 'MARCADO']);
            $request->status_transferencia = "MARCADO";
            $message = "OPERACION EXITOSA!!";   
        }     
        return redirect('/solicitudes/transferencia/index')->with('message',$message);
    }

    public function generar(Request $request)
    {        
        $cuenta = json_decode($request->cuenta_retiro,true); 
        $banco = key($cuenta);
        $cuenta_retiro = $cuenta[$banco];  
        $num_layout = $request->num_layout;
        
        if($banco=="BBVA"){        
            $data = DB::table('pagos as p')
            ->select(             
                DB::raw("CONCAT(
                    LPAD(regexp_replace(tc.soportes_instructor->>'no_cuenta','[^a-zA-Z0-9]', '', 'g'), 18, '0'),
                    LPAD('$cuenta_retiro', 18, '0'),'MXP',
                    LPAD(regexp_replace(p.liquido::TEXT, '[^\d.]',''), 16, '0'),
                    LEFT(regexp_replace(tc.curso,'[^a-zA-Z0-9]', '', 'g'),14), ' FAC', RIGHT(regexp_replace(c.folio_fiscal, '\s', '', 'g') ,5), ' ', LEFT(regexp_replace(c.unidad_capacitacion, '\s', '', 'g'),4)) as reg"
                    )
            )
            ->join('contratos as c','c.id_contrato','p.id_contrato')
            ->join('folios as f','f.id_folios','c.id_folios')
            ->join('tbl_cursos as tc','tc.id','f.id_cursos')
            ->where("tc.soportes_instructor->banco",'ilike','%BBVA%')  
            ->where('p.num_layout',$num_layout)          
            ->where('p.status_transferencia','MARCADO')->pluck('reg');
        }else{
            $data = DB::table('pagos as p')
            ->select(                
                DB::raw("CONCAT(
                    LPAD(regexp_replace(tc.soportes_instructor->>'interbancaria','[^a-zA-Z0-9]', '', 'g'), 18, '0'),
                    LPAD('$cuenta_retiro', 18, '0'),'MXP',
                    LPAD(regexp_replace(p.liquido::TEXT, '[^\d.]',''), 16, '0'),
                    RPAD(TRIM(regexp_replace(tc.nombre,'[^a-zA-Z0-9 ]', '', 'g')),30,' '),
                    CASE
                        WHEN LENGTH(regexp_replace(tc.soportes_instructor->>'interbancaria','[^a-zA-Z0-9]', '', 'g'))>=18 THEN '40'
                        ELSE '30'
                    END,
                    RPAD(regexp_replace(tc.curso,'[^a-zA-Z0-9]', '', 'g'),21,' '), ' FAC', 
                    RIGHT(regexp_replace(c.folio_fiscal, '\s', '', 'g') ,5), ' ', 
                    LEFT(regexp_replace(c.unidad_capacitacion, '\s', '', 'g'),4),
                    LPAD(ROW_NUMBER() OVER (ORDER BY p.id_contrato)::TEXT, 7, '0') ,'H'
                    ) as reg"
                )
            )
            ->join('contratos as c','c.id_contrato','p.id_contrato')
            ->join('folios as f','f.id_folios','c.id_folios')
            ->join('tbl_cursos as tc','tc.id','f.id_cursos')
            ->where("tc.soportes_instructor->banco",'not ilike','%BBVA%')  
            ->where('p.num_layout',$num_layout)          
            ->where('p.status_transferencia','MARCADO')->pluck('reg');
           // dd($data);
        }
        
        if($data){ 
            $result = DB::table('pagos as p')
            ->join('contratos as c','c.id_contrato','p.id_contrato')
            ->join('folios as f','f.id_folios','c.id_folios')
            ->join('tbl_cursos as tc','tc.id','f.id_cursos');            
            if($banco=="BBVA")
                $result->where("tc.soportes_instructor->banco",'ilike','%BBVA%');       
            else
                $result->where("tc.soportes_instructor->banco",'not ilike','%BBVA%');
            
            $result->where('p.status_transferencia','MARCADO')
            ->where('p.num_layout',$num_layout)
            ->update(['status_transferencia' => 'GENERADO', 'fecha_transferencia'=>date('Y-m-d')]);      
            //dd($result);
        }           
        $data = str_replace(['["','"]','","'],['','',"\n"],$data);
        $name_file = $num_layout."_".$banco."_".date('dMY_His').".txt";
        return response($data)
            ->header('Content-Disposition', 'attachment; filename='.$name_file)
            ->header('Content-Type', 'text/plain');
    }
}