<?php

namespace App\Http\Controllers\Consultas;

use App\Events\NotificationEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Excel\xlsTransferencia;
use App\Utilities\MyUtility;
use App\Models\Permission;
//use App\Models\pago;/**EN PRUEBA */
//use App\Models\folio; /**EN PRUEBA */
//use App\Models\contratos;
use App\User;


class pagosController extends Controller
{
    public function index(Request $request){// dd($request->status);
        $data =  $message = [];
        $subtotal = $total = 0;
        $status = null;
        $unidades = DB::table('tbl_unidades')->where('cct', 'LIKE', '%07EI%')->orderby('unidad')->pluck('unidad','unidad');
        $anios = MyUtility::ejercicios();
        if(session('ejercicio'))$request->ejercicio = session('ejercicio');
        if(!$request->ejercicio) $request->ejercicio = date('Y');

        if(session('status'))$status = session('status');
        elseif($request->status) $status = $request->status;
        elseif(!$status) $status = null; //"PENDIENTE";

        if(session('unidad'))$request->unidad = session('unidad');
        if(session('valor'))$request->valor = session('valor');

        if($request->unidad OR $status OR $request->valor){
            if(!$status) $status = "PENDIENTE";

            $data = $this->data($request);
            $subtotal = $data->sum('importe_neto');
            //$acumulado = $data->take($offset + $perPage)->sum('precio');
        }
        if(session('message')) $message = session('message');

        $estatus =  ['PENDIENTE'=>'PENDIENTES','PAGADO'=>'PAGADOS'];
        return view('consultas.pagos',compact('message','data','unidades', 'request', 'anios','subtotal','total', 'estatus','status'));
    }

    public function excel(Request $request){
        if($request->ejercicio OR $request->unidad OR $request->status OR $request->valor){
            $fecha = date("dMy");
            $data = $this->data($request, 'excel');
            $nombreLayout = "REPORTE_TRANSFERENCIAS_".$request->status."_".$fecha.'.xlsx';
            return (new xlsTransferencia('xlsTransferencia', $data))->download($nombreLayout);
        }
    }

    private function data(Request $request, $opcional = null){ //dd($request->status);
        $data =  [];
        if(!$request->status) $request->status = "PENDIENTE";
        if($request->unidad OR $request->status OR $request->valor){
            $data = DB::table('pagos as p')
            ->join('contratos as c','c.id_contrato','p.id_contrato')
            ->join('folios as f','f.id_folios','c.id_folios')
            ->join('tbl_cursos as tc','tc.id','f.id_cursos')
            ->leftJoin('documentos_firmar as docfirma', function($join) {
                $join->on('tc.clave', '=', 'docfirma.numero_o_clave')
                    ->where('docfirma.tipo_archivo', '=', 'Contrato')
                     ->where('docfirma.status', '=', 'VALIDADO');
            });
            if($request->unidad) $data = $data->where('c.unidad_capacitacion', $request->unidad);

            if($request->status){
                switch($request->status){
                    case "PENDIENTE":
                        $data = $data->where('p.status_recepcion', 'VALIDADO')->where('p.status_transferencia', NULL)->whereYear('p.solicitud_fecha', $request->ejercicio);
                    break;
                    case "FINALIZADO":
                        $data = $data->where('f.status', 'Finalizado')->whereYear('p.solicitud_fecha', $request->ejercicio);
                    break;
                    default:
                        $data = $data->where('p.status_transferencia', $request->status)->whereYear('p.fecha_transferencia', $request->ejercicio);
                    break;
                }
            }


            if($request->valor) $data = $data->where(DB::raw('CONCAT(p.num_layout,c.numero_contrato,p.no_memo,tc.nombre,c.folio_fiscal,tc.clave)'),'ilike', '%'.$request->valor.'%');
            $data = $data->whereYear('p.solicitud_fecha', $request->ejercicio);

            $data = $data->wherein('status_recepcion',['VALIDADO','recepcion tradicional']);

            if($opcional=="excel"){ //USO EXCLUSIVO PARA GENERAR EL REPORTE EN EXCEL
                $data = $data->select('c.unidad_capacitacion','c.numero_contrato','p.no_memo','p.solicitud_fecha','tc.clave',
                    'tc.curso','tc.nombre as instructor','tc.rfc','c.folio_fiscal',
                    'tc.soportes_instructor->banco as banco','tc.soportes_instructor->no_cuenta as cuenta','tc.soportes_instructor->interbancaria as clabe',
                    'p.liquido as importe_neto','p.fecha as fecha_pago','num_layout',
                    DB::raw("CASE
                        WHEN  p.status_transferencia is NULL  AND f.status!='Finalizado' THEN 'PENDIENTE'
                        WHEN  p.status_transferencia  = 'PAGADO'  THEN 'PAGADO'
                        WHEN  f.status='Finalizado' THEN 'PAGADO'
                        ELSE p.status_transferencia END as status
                    ")
                )->orderby('numero_contrato','DESC');
            }else{
                $data = $data->select('p.id','c.unidad_capacitacion','c.numero_contrato','p.no_memo','tc.nombre as instructor','tc.rfc','c.folio_fiscal',
                    'tc.soportes_instructor->banco as banco','tc.soportes_instructor->no_cuenta as cuenta','tc.soportes_instructor->interbancaria as clabe',
                    'c.arch_factura as factura','p.liquido as importe_neto','p.solicitud_fecha','p.fecha as fecha_pago','p.arch_pago','p.no_pago',
                    DB::raw("CASE
                        WHEN  p.status_transferencia is NULL  THEN 'PENDIENTE'
                        WHEN  p.status_transferencia  = 'PAGADO'  THEN 'PAGADO'
                        WHEN  f.status = 'Finalizado' THEN 'PAGADO'
                        ELSE p.status_transferencia END as status
                    "),
                    'p.num_layout','tc.curso','tc.clave',
                    DB::raw("CASE
                        WHEN  docfirma.status='VALIDADO' THEN CONCAT('/contrato/',c.id_contrato::text)
                        ELSE c.arch_contrato END as contrato
                    ")
                    )
                ->paginate(15);
                $data->appends(['ejercicio' => $request->ejercicio, 'unidad' => $request->unidad, 'status' => $request->status,'valor'=>$request->valor]);
            }
            //dd($data);
            if($data){ //session('data') = $data;
                return $data;
            }else $message = "NO SE ENCONTRARON DATOS QUE MOSTRAR, FAVOR DE VOLVER A INTENTAR.";
        } else $message = "POR FAVOR, INGRESE POR LO MENOS UN VALOR DE FILTRADO!!";
    }

}
