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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Excel\xlsTransferencia;
use App\Utilities\MyUtility;
use App\Models\Permission;
use App\Models\pago;/**EN PRUEBA */
use App\Models\folio; /**EN PRUEBA */
use App\Models\contratos;
use App\User;


class transferenciaController extends Controller
{

    function __construct() {
        $this->path = "/expedientes/";
        $this->path_files = env("APP_URL").'/storage/';
        //session_start();
    }

    public function index(Request $request){// $this->calcula_isr(32000);
        $folio = null;
        $data =  $cuentas_retiro = $numeros_layouts = $message = [];
        $unidades = DB::table('tbl_unidades')->where('cct', 'LIKE', '%07EI%')->orderby('unidad')->pluck('unidad','unidad');
        $anios = MyUtility::ejercicios();
        if(!$request->ejercicio) $request->ejercicio = date('Y');        
        
        if(session('ejercicio'))$request->ejercicio = session('ejercicio');
        if(session('unidad') AND !$request->unidad)$request->unidad = session('unidad');
        if(session('status') AND !$request->status)$request->status = session('status');
        if(session('valor') AND !$request->valor)$request->valor = session('valor');

        if($request->unidad OR $request->status OR $request->valor){
            $cuentasretiro = DB::table('tbl_instituto')->select("cuentas_bancarias->pago_instructor as cuentas")->get();
            $cuentasretiro = json_decode($cuentasretiro[0]->cuentas, true);
            $n=1;
            foreach($cuentasretiro as $k => $v){
                $json_cuentas[$k] =  $v;
                $cuentas_retiro['{"'.$k.'":"'.$v.'"}'] = "$k: $v";
            }
            $data = $this->data($request);
             if(count($data)>0){
               if($data->last()->unicos == 0){
                 $request->status = $data[0]->status;
                 $folio = $data[0]->num_layout;
               }
             }
            
        }
        
        if(session('message')) $message = session('message');

        if($request->status=="GENERADO"){
            $numeros_layouts = DB::table('pagos')->where('status_transferencia','GENERADO')->pluck('num_layout','num_layout');
        }
            //dd($numeros_layouts);
        $folio = $folio ?: $this->folio_layout();
        return view('solicitudes.transferencia.index',compact('message','data','unidades', 'cuentas_retiro', 'request', 'anios', 'numeros_layouts','folio'));
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
            $nombreLayout = "REPORTE_TRANSFERENCIAS_".$request->status."_".$fecha.'.xlsx';
            //return  $nombreLayout;
            return (new xlsTransferencia('xlsTransferencia', $data))->download($nombreLayout);
        }

        //return $request->valor;
    }

    private function data(Request $request, $opcional = null){ //dd($request->status);
        $data =  [];
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

            if($opcional=="excel"){ //UsO EXCLUSIVO PARA GENERAR EL REPORTE EN EXCEL
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
                $sub = DB::table('pagos as p2')
                    ->selectRaw("
                        p2.id,
                        p2.status_transferencia,
                        CASE
                            WHEN ROW_NUMBER() OVER (
                                PARTITION BY p2.status_transferencia
                                ORDER BY p2.id
                            ) = 1
                                THEN 1
                            ELSE 0
                        END AS es_unico
                    ");

                // 2) Tu query principal + joinSub + selects
                $data = $data
                    ->joinSub($sub, 'x', function ($join) {
                        $join->on('x.id', '=', 'p.id');
                    })
                    ->select(
                        'p.id',
                        'c.unidad_capacitacion',
                        'c.numero_contrato',
                        'p.no_memo',
                        'tc.nombre as instructor',
                        'tc.rfc',
                        'c.folio_fiscal',
                        DB::raw("tc.soportes_instructor->>'banco' as banco"),
                        DB::raw("tc.soportes_instructor->>'no_cuenta' as cuenta"),
                        DB::raw("tc.soportes_instructor->>'interbancaria' as clabe"),
                        'c.arch_factura as factura',
                        'p.liquido as importe_neto',
                        'p.solicitud_fecha',
                        'p.fecha as fecha_pago',
                        'p.arch_pago',
                        'p.no_pago',
                        DB::raw("
                            CASE
                                WHEN p.status_transferencia IS NULL THEN 'PENDIENTE'
                                WHEN p.status_transferencia = 'PAGADO' THEN 'PAGADO'
                                WHEN f.status = 'Finalizado' THEN 'PAGADO'
                                ELSE p.status_transferencia
                            END as status
                        "),
                        'p.num_layout',
                        'tc.curso',
                        'tc.clave',
                        DB::raw("
                            CASE
                                WHEN docfirma.status = 'VALIDADO'
                                    THEN CONCAT('/contrato/', c.id_contrato::text)
                                ELSE c.arch_contrato
                            END as contrato
                        "),
                        // columna de conteo acumulado de estados distintos:
                        DB::raw("
                            SUM(x.es_unico) OVER (ORDER BY p.id) AS unicos
                        ")
                    )
                    ->paginate(15);
                $data->appends(['ejercicio' => $request->ejercicio,'unidad' => $request->unidad, 'status_transferencia' => $request->status,'valor'=>$request->valor]);
            }
            //dd($data);
            if($data){
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
            $request->status = "MARCADO";
            $message = "OPERACION EXITOSA!!";
        }
        return redirect('/solicitudes/transferencia/index')->with('message',$message);
    }
    public function folio_layout(){
        $num_default = date('dmy');        
        $max = DB::table('pagos as p')
            ->where('num_layout', 'like', $num_default.'%')
            ->value(DB::raw("
                CASE 
                    WHEN max(num_layout) IS NULL THEN NULL
                    ELSE CONCAT('$num_default', LPAD((CAST(SUBSTRING(max(num_layout), LENGTH(max(num_layout)) - 3, 4) AS INTEGER) + 1)::text,4,'0'))
                END
            "));
        return $max ?? ($num_default . '0001');        
    }

    public function generar(Request $request)
    {
        $cuenta = json_decode($request->cuenta_retiro,true);
        $banco = key($cuenta);
        $cuenta_retiro = $cuenta[$banco];
        $num_layout = $request->num_layout;
        ///ACTUALIZA ESTATUS Y ASIGAN NUMERO DE LAYOUT EN LA TABLA pagos
        DB::statement("
                WITH base AS (
                    SELECT COALESCE(MAX(consec_layout), 0) AS base_consec
                    FROM pagos
                    WHERE num_layout = '$num_layout'
                ),
                marcados AS (
                    SELECT 
                        p.id,
                        ROW_NUMBER() OVER (ORDER BY p.id) AS rn,
                        b.base_consec
                    FROM pagos p
                    CROSS JOIN base b
                    WHERE p.status_transferencia = 'MARCADO'
                    AND p.num_layout = '$num_layout'
                )
                UPDATE pagos p
                SET 
                    status_transferencia = 'GENERADO',
                    fecha_transferencia  = '".date('Y-m-d')."',
                    consec_layout        = marcados.base_consec + marcados.rn
                FROM marcados
                WHERE p.id = marcados.id
            ");
        
        //CREA O ACTUALIZA REGISTROS EN LA TABLA pagos_mensuales
        $instructores_pagos = DB::table('pagos as p')
        ->select('tc.id_instructor','p.num_layout')
        ->selectRaw('sum(p.liquido) as total_bruto')
        ->selectRaw('json_agg(tc.id) as ids_cursos')
        ->selectRaw("tc.soportes_instructor->>'banco'  as banco")
        ->selectRaw("tc.soportes_instructor->>'no_cuenta'  as cuenta")
        ->selectRaw("tc.soportes_instructor->>'interbancaria'  as clabe")
        ->selectRaw("
            CASE 
            WHEN tc.soportes_instructor->>'banco' ILIKE '%BBVA%' THEN
                CONCAT('PTC',
                    LPAD(regexp_replace(tc.soportes_instructor->>'no_cuenta','[^a-zA-Z0-9]', '', 'g'), 18, '0'),
                    LPAD('$cuenta_retiro', 18, '0'),'MXP',
                    '{{LIQUIDO}}',                    
                    RPAD(CONCAT_WS(' ',min(p.num_layout::text),string_agg(p.consec_layout::text, ' '  ORDER BY p.consec_layout ASC)) ,30,' '),'0                  000000000000.00')
            ELSE
                CONCAT('PSC',
                    LPAD(regexp_replace(tc.soportes_instructor->>'interbancaria','[^a-zA-Z0-9]', '', 'g'), 18, '0'),
                    LPAD('$cuenta_retiro', 18, '0'),'MXP',
                    '{{LIQUIDO}}',
                    RPAD(TRIM(regexp_replace(tc.nombre,'[^a-zA-Z0-9 ]', '', 'g')),30,' '),
                    '40',
                    LEFT(regexp_replace(tc.soportes_instructor->>'interbancaria','[^a-zA-Z0-9]', '', 'g'), '3'),
                   RPAD(CONCAT_WS(' ',min(p.num_layout::text),string_agg(p.consec_layout::text, ' '  ORDER BY p.consec_layout ASC)) ,30,' '),'0                  000000000000.00')
                END 
            as cadena_layout"
            )
        ->join('tbl_cursos as tc','tc.id','p.id_curso')
        ->where('p.num_layout',$num_layout)
        ->where('p.status_transferencia','GENERADO')        
        ->groupby('tc.id_instructor','tc.nombre', DB::raw("tc.soportes_instructor->>'banco'"), DB::raw("tc.soportes_instructor->>'interbancaria'"), DB::raw("tc.soportes_instructor->>'no_cuenta'"), 'p.num_layout')->get();
        //LPAD(regexp_replace(sum(p.liquido)::TEXT, '[^\d.]',''), 16, '0'),    
        //LPAD(regexp_replace(sum(p.liquido)::TEXT, '[^\d.]',''), 16, '0'),

        foreach ($instructores_pagos as $ipagos) {
            list($total_liquido, $total_isr) = $this->calcula_isr($ipagos->total_bruto);            
            $liquido_formateado = str_pad(number_format((float)$total_liquido, 2, '.', ''), 16, '0', STR_PAD_LEFT);

            $cadena = $ipagos->cadena_layout; 
            $cadena_layout = str_replace('{{LIQUIDO}}', $liquido_formateado, $cadena);

            DB::table('pagos_mensuales')->updateOrInsert(
                [
                    'id_instructor' => $ipagos->id_instructor,
                    'num_layout' => $ipagos->num_layout
                ],
                [
                    'ids_cursos' => $ipagos->ids_cursos,
                    'importe_bruto' => $ipagos->total_bruto,
                    'total_liquido' => $total_liquido,
                    'total_isr' => $total_isr,
                    'banco' => $ipagos->banco,
                    'cuenta' => $ipagos->cuenta,
                    'clabe' => $ipagos->clabe,
                    'cadena_layout' => $cadena_layout,
                    'updated_at' => now(),
                ]
            );
        }
        $data = DB::table('pagos_mensuales')->where('num_layout',$num_layout)->pluck('cadena_layout');
        
        $data = str_replace(['["','"]','","'],['','',"\n"],$data);
        $name_file = $num_layout."_".$banco."_".date('dMY_His').".txt";
        
        return response($data)
            ->header('Content-Disposition', 'attachment; filename='.$name_file)
            ->header('Content-Type', 'text/plain');
    }

    private function calcula_isr($importe){

        $BG = $importe;
        $tarifa = DB::table('tarifas_isr')->where('limite_inferior','<=',$BG)->where('limite_superior','>=',$BG)->first();
        $LI = $tarifa->limite_inferior;
        $RESTA = $BG-$LI; 
        $PORC_LI = $tarifa->porcentaje;
        $IMPUESTO = $RESTA*$PORC_LI/100;
        $CF = $tarifa->cuota_fija;
        $ISR = $IMPUESTO+$CF;
        $LIQUIDO = $BG-$ISR;        
        return [$LIQUIDO, $ISR];
        
    }
    
    public function layout_xsl(Request $request){
        if($request->ejercicio OR $request->num_layout){
            $fecha = date("dMy");       
            $data = $this->data($request, 'excel');
            $nombreLayout = "LAYOUT_PAGO"."_".$fecha.'.xlsx';            
            return (new xlsTransferencia('xlsLayout', $data))->download($nombreLayout);
        }
    }

    public function pagado(Request $request)
    {
        if($request->ids AND $request->numero_pago AND $request->fecha_pago AND $request->descripcion){
            $ejercicio = DB::table('pagos')->wherein('pagos.id',$request->ids)->join('tbl_cursos','tbl_cursos.id','pagos.id_curso')
            ->select(DB::raw('EXTRACT(YEAR FROM tbl_cursos.inicio) as anio_inicio'))->value('anio_inicio');

            $urldoc = $this->pdf_upload($ejercicio, $request); # invocamos el método
            if($urldoc){
                $result= pago::wherein('id', $request->ids)
                ->update(['no_pago' => $request->numero_pago,
                        'fecha' => $request->fecha_pago,
                        'fecha_transferencia' => $request->fecha_pago,
                        'descripcion' => $request->descripcion,
                        'status_transferencia' => 'PAGADO',
                        'arch_pago' => $urldoc
                        ]);

                $id_cursos = DB::table('pagos')->wherein('id', $request->ids)->pluck('id_curso');
                folio::WHEREIN('id_cursos', $id_cursos)->update(['status' => 'Finalizado']);
                if($request->folio_fiscal){
                        contratos::whereIn('id_curso', $id_cursos)
                            ->where(function ($query) {
                                    $query->whereNull('folio_fiscal')
                                    ->orWhere('fecha_firma', '>', '2023-12-31');
                            })->update(['folio_fiscal' => $request->folio_fiscal]);

                }
                if($result) $message["ALERT"] = "OPERACIÓN EXITOSA!!";
                else $message["ERROR"] = "LA OPERACIÓN NO SE EJECUTADO CORRECTAMENTE, POR FAVOR INTENTE DE NUEVO.";

            }else $message["ERROR"] = "NO SE ADJUNTO EL ARCHIVO, POR FAVOR INTENTE DE NUEVO.";

        }else $message["ERROR"] = "POR FAVOR, COMPLETE LOS DATOS E INTENTE DE NUEVO.";

        return redirect('/solicitudes/transferencia/index')->with(['message'=>$message,'ejercicio'=>$request->ejericicio,'unidad'=>$request->unidad,'status'=>$request->status,'valor'=>$request->valor]);
    }

    protected function pdf_upload($ejercicio, $request)
    {
        if($request->file('arch_pago')){
            $ejercicio = DB::table('pagos')->wherein('pagos.id',$request->ids)->join('tbl_cursos','tbl_cursos.id','pagos.id_curso')
            ->select(DB::raw('EXTRACT(YEAR FROM tbl_cursos.inicio) as anio_inicio'))->value('anio_inicio');
            $result = DB::table('pagos')->wherein('pagos.id',$request->ids)->select(DB::raw("SPLIT_PART(arch_pago, 'storage/', 2) AS archivo"),'arch_pago')->pluck('archivo','arch_pago');

            $arch_pagos = $result->keys()->toArray();
            $archivos = $result->values()->toArray();

            //dd($arch_pagos);

            $file = $request->file('arch_pago');
            $path = $ejercicio.$this->path.'comprobantes_pagos/';
            $name_file = "pago_".trim($request->numero_pago."_".date('YmdHis')."_".Auth::user()->id.".pdf");
            $file_result = MyUtility::upload_file($path,$file,$name_file);

            if($file_result["up"]){
                $no_borrar = DB::table('pagos')->whereNotin('pagos.id',$request->ids)->whereIn('arch_pago', $arch_pagos)->select(DB::raw("SPLIT_PART(arch_pago, 'storage/', 2) AS archivo"))->pluck('archivo');
                $archivos_borrar = array_diff($archivos, $no_borrar->toArray());

                if(count($archivos_borrar)){
                    foreach ($archivos_borrar as $borrar) Storage::delete($borrar);
                }

                $url_file = $this->path_files.$file_result["url_file"].$name_file;

            }
        } else $url_file = false;
        return $url_file;
    }

}
