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
use Maatwebsite\Excel\Facades\Excel;
use App\Excel\xlsTransferencia;
use App\Utilities\MyUtility;
use App\Models\Permission;
use App\User;


class transferenciaController extends Controller
{
    
    function __construct() {       
        session_start();
    }

    public function index(Request $request){        
        $data =  $cuentas_retiro = [];
        $unidades = DB::table('tbl_unidades')->where('cct', 'LIKE', '%07EI%')->orderby('unidad')->pluck('unidad','unidad');
        $anios = MyUtility::ejercicios();
        if(!$request->ejercicio) $request->ejercicio = date('Y');
        
        if($request->unidad OR $request->status_transferencia OR $request->valor){
            $cuentasretiro = DB::table('tbl_instituto')->select("cuentas_bancarias->pago_instructor as cuentas")->get();
            $cuentasretiro = json_decode($cuentasretiro[0]->cuentas, true);
            $n=1;
            foreach($cuentasretiro as $k => $v){
                $json_cuentas[$k] =  $v;                
                $cuentas_retiro['{"'.$k.'":"'.$v.'"}'] = "$k: $v";
            }
            $data = $this->data($request);           
        }    

        
                
        if(session('message')) $message = session('message');
        elseif(!isset($message)) $message = null;
            
        return view('solicitudes.transferencia.index',compact('message','data','unidades', 'cuentas_retiro', 'request','anios'));
    }

    public function excel(Request $request){ 
        if($request->ejercicio OR $request->unidad OR $request->status OR $request->valor){
            $fecha = date("dMy");
            //$data = json_decode(json_encode($this->data($request,'excel')),true);            
            $data = $this->data($request, 'excel');
            /*$data = array_column($data, 'unidad_capacitacion','numero_contrato','fecha_pago','no_memo','solicitud_fecha','clave','curso',
            'instructor','rfc','folio_fiscal','banco','cuenta','clabe','importe_neto','status_layout'
        );*/
            //dd($data);
            $nombreLayout = "REPORTE_TRANSFERENCIAS_".$request->status_transferencia."_".$fecha.'.xlsx';  
            //return  $nombreLayout;
            return (new xlsTransferencia('xlsTransferencia', $data))->download($nombreLayout);
        }

        //return $request->valor;
    }

    private function data(Request $request, $opcional = null){
        $data =  [];
        if($request->unidad OR $request->status_transferencia OR $request->valor){
            $data = DB::table('pagos as p')           
            ->join('contratos as c','c.id_contrato','p.id_contrato')
            ->join('folios as f','f.id_folios','c.id_folios')
            ->join('tbl_cursos as tc','tc.id','f.id_cursos');
            if($request->unidad) $data = $data->where('c.unidad_capacitacion', $request->unidad);
            
            if($request->status_transferencia){
                switch($request->status_transferencia){
                    case "PENDIENTE":
                        $data = $data->where('p.status_transferencia', NULL)->whereYear('p.solicitud_fecha', $request->ejercicio);
                    break;
                    case "PAGADO":
                        $data = $data->where('p.status_transferencia', 'PAGADO')->whereYear('p.solicitud_fecha', $request->ejercicio);
                    break;
                    case "FINALIZADO":
                        $data = $data->where('f.status', 'Finalizado')->whereYear('p.solicitud_fecha', $request->ejercicio);
                    break;
                    default:
                        $data = $data->where('p.status_transferencia', $request->status_transferencia)->whereYear('p.fecha_transferencia', $request->ejercicio);
                    break;
                }                    
            }else{
                $data = $data->whereYear('p.solicitud_fecha', $request->ejercicio);
            }      
            if($request->valor) $data = $data->where(DB::raw('CONCAT(p.num_layout,c.numero_contrato,p.no_memo,tc.nombre)'),'ilike', '%'.$request->valor.'%');
            $data = $data->wherein('status_recepcion',['VALIDADO','recepcion tradicional']);

            if($opcional=="excel"){ //UsO EXCLUSIVO PARA GENERAR EL REPORTE EN EXCEL
                $data = $data->select('c.unidad_capacitacion','c.numero_contrato','p.no_memo','p.solicitud_fecha','tc.clave',                
                    'tc.curso','tc.nombre as instructor','tc.rfc','c.folio_fiscal',
                    'tc.soportes_instructor->banco as banco','tc.soportes_instructor->no_cuenta as cuenta','tc.soportes_instructor->interbancaria as clabe',
                    'p.liquido as importe_neto','p.fecha as fecha_pago','num_layout',
                    DB::raw("CASE 
                        WHEN  p.status_transferencia is NULL  AND f.status!='Finalizado' THEN 'PENDIENTE' 
                        WHEN  p.status_transferencia  = 'PAGADO'  THEN 'PAGADO'
                        WHEN  f.status='Finalizado' THEN 'PAGADO'
                        ELSE p.status_transferencia END as status_layout
                    ")
                )->orderby('numero_contrato','DESC');
            }else{
                $data = $data->select('p.id','c.unidad_capacitacion','c.numero_contrato','p.no_memo','tc.nombre as instructor','tc.rfc','c.folio_fiscal',
                    'tc.soportes_instructor->banco as banco','tc.soportes_instructor->no_cuenta as cuenta','tc.soportes_instructor->interbancaria as clabe',
                    'c.arch_factura as factura','p.liquido as importe_neto','p.solicitud_fecha','p.fecha as fecha_pago',
                    DB::raw("CASE 
                        WHEN  p.status_transferencia is NULL  AND f.status!='Finalizado' THEN 'PENDIENTE' 
                        WHEN  p.status_transferencia  = 'PAGADO'  THEN 'PAGADO'
                        WHEN  f.status = 'Finalizado' THEN 'PAGADO'
                        ELSE p.status_transferencia END as status_layout
                    "),                     
                    DB::raw("CASE 
                        WHEN  p.status_transferencia is NULL  AND f.status!='Finalizado' THEN 'PENDIENTE' 
                        WHEN  p.status_transferencia  = 'PAGADO' THEN 'PAGADO'
                        WHEN  f.status='Finalizado' THEN 'FINALIZADO'
                        ELSE 'EN PROCESO' END as status
                    "), 
                    'p.num_layout','tc.curso','tc.clave','c.arch_contrato as contrato'
                    )
                ->paginate(15); 
                $data->appends(['ejercicio' => $request->ejercicio,'unidad' => $request->unidad, 'status_transferencia' => $request->status_transferencia,'valor'=>$request->valor]);
            }
            //dd($data);
            if($data){ //$_SESSION['data'] = $data;
                return $data;
            }else $message = "NO SE ENCONTRARON DATOS QUE MOSTRAR, FAVOR DE VOLVER A INTENTAR.";
        } else $message = "POR FAVOR, INGRESE POR LO MENOS UN VALOR DE FILTRADO!!";   
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
                    $result =  DB::table('pagos')->where('id', '=', $request->id)->whereNull('status_transferencia')->update(['status_transferencia' => 'PAGADO', 'fecha_transferencia'=>date('Y-m-d'), 'fecha' => $request->fecha_pago]);
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

        $dataBBVA = DB::table('pagos as p')
            ->select(DB::raw("CONCAT(curso,
                    LPAD(regexp_replace(tc.soportes_instructor->>'no_cuenta','[^a-zA-Z0-9]', '', 'g'), 18, '0'),
                    LPAD('$cuenta_retiro', 18, '0'),'MXP',
                    LPAD(regexp_replace(p.liquido::TEXT, '[^\d.]',''), 16, '0'),                            
                    translate(
                        CONCAT_WS(
                            ' ',                            
                            SUBSTRING(SPLIT_PART(regexp_replace(curso, '\m(de|la|los|el|para|en|con|a|y|del|las)\M\\s+', '', 'gi'), ' ', 1), 1, 6),
                            SUBSTRING(SPLIT_PART(regexp_replace(curso, '\m(de|la|los|el|para|en|con|a|y|del|las)\M\\s+', '', 'gi'), ' ', 2), 1, 5),
                            SUBSTRING(SPLIT_PART(regexp_replace(curso, '\m(de|la|los|el|para|en|con|a|y|del|las)\M\\s+', '', 'gi'), ' ', 3), 1, 3)                            
                        ),                            
                    'áéíóúÁÉÍÓÚÑ', 'aeiouAEIOUN'),                     
                    ' FAC', RIGHT(regexp_replace(c.folio_fiscal, '\s', '', 'g') ,5), ' ', LEFT(regexp_replace(c.unidad_capacitacion, '\s', '', 'g'),4)) as reg"
                    )
            )
            ->join('contratos as c','c.id_contrato','p.id_contrato')
            ->join('folios as f','f.id_folios','c.id_folios')
            ->join('tbl_cursos as tc','tc.id','f.id_cursos')
            ->where("tc.soportes_instructor->banco",'ilike','%BBVA%')  
            ->where('p.num_layout',$num_layout)          
            ->whereIn('p.status_transferencia',['MARCADO','GENERADO']);//->pluck('reg');
        $dataINTER = DB::table('pagos as p')
            ->select( DB::raw("CONCAT(
                    LPAD(regexp_replace(tc.soportes_instructor->>'interbancaria','[^a-zA-Z0-9]', '', 'g'), 18, '0'),
                    LPAD('$cuenta_retiro', 18, '0'),'MXP',
                    LPAD(regexp_replace(p.liquido::TEXT, '[^\d.]',''), 16, '0'),
                    RPAD(TRIM(regexp_replace(tc.nombre,'[^a-zA-Z0-9 ]', '', 'g')),30,' '),
                    CASE
                        WHEN LENGTH(regexp_replace(tc.soportes_instructor->>'interbancaria','[^a-zA-Z0-9]', '', 'g'))>=18 THEN '40'
                        ELSE '30'
                    END,
                    translate(
                        CONCAT_WS(
                            ' ',                                                     
                            SUBSTRING(SPLIT_PART(regexp_replace(curso, '\m(de|la|los|el|para|en|con|a|y|del|las)\M\\s+', '', 'gi'), ' ', 1), 1, 6),
                            SUBSTRING(SPLIT_PART(regexp_replace(curso, '\m(de|la|los|el|para|en|con|a|y|del|las)\M\\s+', '', 'gi'), ' ', 2), 1, 5),
                            SUBSTRING(SPLIT_PART(regexp_replace(curso, '\m(de|la|los|el|para|en|con|a|y|del|las)\M\\s+', '', 'gi'), ' ', 3), 1, 3)                            
                        ),                            
                    'áéíóúÁÉÍÓÚÑ', 'aeiouAEIOUN'),                    
                    ' FAC', 
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
            ->whereIn('p.status_transferencia',['MARCADO', 'GENERADO']);//->pluck('reg');
          
        
        $data = $dataBBVA->union($dataINTER)->pluck('reg');
       //$data = $dataBBVA->union($dataINTER)->get();
          //dd($data);
        if($data){ 
            $result = DB::table('pagos as p')
            ->join('contratos as c','c.id_contrato','p.id_contrato')
            ->join('folios as f','f.id_folios','c.id_folios')
            ->join('tbl_cursos as tc','tc.id','f.id_cursos');           /* 
            if($banco=="BBVA")
                $result->where("tc.soportes_instructor->banco",'ilike','%BBVA%');       
            else
                $result->where("tc.soportes_instructor->banco",'not ilike','%BBVA%');
            */
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