<?php
/* Creador: Orlando Chavez */
namespace App\Http\Controllers\webController;

use App\Models\pago;
use App\Models\instructor;
use App\Models\contratos;
use App\Models\folio;
use App\Models\directorio;
use App\Models\especialidad_instructor;
use App\Models\contrato_directorio;
use App\Models\Calendario_Entrega;
use Illuminate\Http\Request;
use Redirect,Response;
use App\Http\Controllers\Controller;
use PDF;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;
use App\Models\tbl_unidades;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportExcel;
use ZipArchive;

class PagoController extends Controller
{
    public function fill(Request $request)
    {
        $instructor = new instructor();
        $input = $request->numero_contrato;
        $newsAll = $instructor::where('id', $input)->first();
        return response()->json($newsAll, 200);
    }

    public function index(Request $request)
    {
        $array_ejercicio =[];
        $año_pointer = CARBON::now()->format('Y');
        /**
         * busqueda de pago
         */
        $tipoPago = $request->get('tipo_pago');
        $busqueda_pago = $request->get('busquedaPorPago');
        $tipoStatus = $request->get('tipo_status');
        $unidad = $request->get('unidad');
        $mes = $request->get('mes');

        if($request->ejercicio == NULL)
        {
            $año_referencia = '01-01-' . CARBON::now()->format('Y');
            $año_referencia2 = '31-12-' . CARBON::now()->format('Y');
        }
        else
        {
            $año_referencia = '01-01-' . $request->ejercicio;
            $año_referencia2 = '31-12-' . $request->ejercicio;
            $año_pointer = $request->ejercicio;
        }

        for($x = 2020; $x <= intval(CARBON::now()->format('Y')); $x++)
        {
            array_push($array_ejercicio, $x);
        }

        $contrato = new contratos();
        // obtener el usuario y su unidad
        $unidadUser = Auth::user()->unidad;

        // obtener el id
        $userId = Auth::user()->id;

        $roles = DB::table('role_user')
            ->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
            ->SELECT('roles.slug AS role_name')
            ->WHERE('role_user.user_id', '=', $userId)
            ->GET();

        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->WHERE('cct','LIKE','07EI%')->GET();

        //dd($roles[0]->role_name);

        $contratos_folios = $contrato::busquedaporpagos($tipoPago, $busqueda_pago, $tipoStatus, $unidad, $mes)
        ->WHEREIN('folios.status', ['Contrato_Validado','Verificando_Pago','Pago_Verificado','Pago_Rechazado',
                    'Finalizado'])
        ->WHERE('tbl_cursos.inicio', '>=', $año_referencia)
        ->WHERE('tbl_cursos.inicio', '<=', $año_referencia2)
        ->LEFTJOIN('folios','folios.id_folios', '=', 'contratos.id_folios')
        ->LEFTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
        ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
        ->LEFTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
        ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
        ->JOIN('instructores','instructores.id', '=', 'tbl_cursos.id_instructor')
        ->orderBy('pagos.created_at', 'desc')
        ->PAGINATE(50, [
            'contratos.id_contrato', 'contratos.numero_contrato', 'contratos.cantidad_letras1', 'contratos.arch_contrato',
            'contratos.unidad_capacitacion', 'contratos.municipio', 'contratos.fecha_firma','contratos.fecha_status', 'contratos.docs',
            'contratos.observacion', 'contratos.arch_factura', 'contratos.arch_factura_xml','folios.permiso_editar',
            'folios.status','pagos.recepcion', 'folios.id_folios', 'folios.id_supre','pagos.status_recepcion','pagos.created_at','pagos.arch_solicitud_pago',
            'pagos.arch_asistencia','pagos.arch_evidencia','pagos.fecha_agenda','pagos.arch_solicitud_pago','pagos.agendado_extemporaneo',
            'pagos.observacion_rechazo_recepcion','pagos.arch_calificaciones','pagos.arch_evidencia','tbl_cursos.id_instructor','tbl_cursos.soportes_instructor',
            'tbl_cursos.instructor_mespecialidad','tbl_cursos.tipo_curso', 'tbl_cursos.pdf_curso','tabla_supre.doc_validado',
            'instructores.archivo_alta','instructores.archivo_bancario','instructores.archivo_ine', 'tbl_cursos.nombre','pagos.fecha_envio',
            'pagos.updated_at','pagos.status_transferencia',
            DB::raw('(DATE_PART(\'day\', CURRENT_DATE - contratos.fecha_status::timestamp)) >= 7 as alerta'),
            DB::raw('(DATE_PART(\'day\', CURRENT_DATE - pagos.updated_at::timestamp)) >= 7 as alerta_financieros'),
            // DB::raw('(DATE_PART(\'day\', CURRENT_DATE - contratos.fecha_status::timestamp)) >= 30 as bloqueo')
        ]);
        switch ($roles[0]->role_name) {
            case 'unidad.ejecutiva':
                # code...
                $contratos_folios = $contratos_folios;
                break;
            case 'admin':
                # code...
                $contratos_folios = $contratos_folios;

            break;
            case 'direccion.general':
                # code...
                $contratos_folios = $contratos_folios;
                break;
            case 'planeacion':
                # code...
                $contratos_folios = $contratos_folios;
                break;
            case 'financiero_verificador':
                # code...
                $contratos_folios = $contratos_folios;
                break;
            case 'financiero_pago':
                # code...
                $contratos_folios = $contratos_folios;
                break;
            case 'dta':
                # code...
                $contratos_folios = $contratos_folios;
                break;
            default:
                # code...
                // obtener unidades
                $unidadPorUsuario = DB::table('tbl_unidades')->WHERE('id', $unidadUser)->FIRST();

                $contratos_folios = $contrato::busquedaporpagos($tipoPago, $busqueda_pago, $tipoStatus, $unidad, $mes)
                ->WHERE('tbl_unidades.ubicacion', '=', $unidadPorUsuario->ubicacion)
                ->WHEREIN('folios.status', ['Verificando_Pago','Pago_Verificado','Pago_Rechazado','Finalizado'])
                ->WHERE('tbl_cursos.inicio', '>=', $año_referencia)
                ->WHERE('tbl_cursos.inicio', '<=', $año_referencia2)
                ->LEFTJOIN('folios','folios.id_folios', '=', 'contratos.id_folios')
                ->LEFTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
                ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                ->LEFTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                ->JOIN('instructores','instructores.id', '=', 'tbl_cursos.id_instructor')
                ->orderBy('pagos.created_at', 'desc')
                // ->orderBy('contratos.fecha_firma', 'desc')
                ->PAGINATE(50, [
                    'contratos.id_contrato', 'contratos.numero_contrato', 'contratos.cantidad_letras1','contratos.fecha_status','contratos.arch_contrato',
                    'contratos.unidad_capacitacion', 'contratos.municipio', 'contratos.fecha_firma','contratos.docs','contratos.observacion',
                    'contratos.arch_factura', 'contratos.arch_factura_xml','folios.status','folios.id_folios','folios.id_supre','pagos.recepcion',
                    'folios.permiso_editar','pagos.status_recepcion','pagos.arch_solicitud_pago','pagos.fecha_agenda','pagos.created_at','pagos.arch_asistencia','pagos.arch_evidencia',
                    'pagos.arch_calificaciones','pagos.arch_evidencia','pagos.agendado_extemporaneo','pagos.observacion_rechazo_recepcion',
                    'tbl_cursos.id_instructor','tbl_cursos.instructor_mespecialidad','tbl_cursos.tipo_curso','tbl_cursos.pdf_curso','tbl_cursos.soportes_instructor',
                    'tabla_supre.doc_validado','instructores.archivo_alta','instructores.archivo_bancario','instructores.archivo_ine',
                    'tbl_cursos.nombre','pagos.fecha_envio','pagos.updated_at','pagos.status_transferencia',
                    DB::raw('(DATE_PART(\'day\', CURRENT_DATE - contratos.fecha_status::timestamp)) >= 7 as alerta'),
                    DB::raw('(DATE_PART(\'day\', CURRENT_DATE - pagos.updated_at::timestamp)) >= 7 as alerta_financieros'),
                    // DB::raw('(DATE_PART(\'day\', CURRENT_DATE - contratos.fecha_status::timestamp)) >= 30 as bloqueo')
                ]);
                break;
        }

        foreach($contratos_folios as $pointer => $ari)
        {
            $memoval = especialidad_instructor::WHERE('id_instructor',$ari->id_instructor) // obtiene la validacion del instructor
            ->whereJsonContains('hvalidacion', [['memo_val' => $ari->instructor_mespecialidad]])->value('hvalidacion');
            if(isset($memoval))
            {
                foreach($memoval as $me)
                {
                    if($me['memo_val'] == $ari->instructor_mespecialidad)
                    {
                        $contratos_folios[$pointer]->arch_mespecialidad = $me['arch_val'];
                        break;
                    }
                }
            }
            else
            {
                $contratos_folios[$pointer]->arch_mespecialidad = $ari->archivo_alta;
            }

        }

        $calendario_entrega = Calendario_Entrega::whereDate('fecha_entrega', '>=', Carbon::now()->toDateString())
            ->whereJsonContains('tipo_entrega', 'DOCUMENTACION_PAGO')
            ->orderBy('fecha_entrega', 'asc')
            ->value('fecha_entrega');


        return view('layouts.pages.vstapago', compact('contratos_folios','unidades','año_pointer','array_ejercicio','tipoPago','unidad','calendario_entrega'));
    }

    public function crear_pago($id)
    {
        $data = contratos::SELECT('instructores.numero_control','instructores.id AS idins','tbl_cursos.nombre','tbl_cursos.curso','tbl_cursos.clave',
                                    'contratos.unidad_capacitacion','folios.id_folios','folios.importe_total','folios.iva','pagos.id AS id_pago', 'pagos.fecha')
                                    ->WHERE('contratos.id_contrato', '=', $id)
                                    ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                                    ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', 'folios.id_cursos')
                                    ->LEFTJOIN('instructores', 'instructores.id', 'tbl_cursos.id_instructor')
                                    ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                                    ->FIRST();

        $importe = round($data->importe_total-$data->iva, 2);
        return view('layouts.pages.frmpago', compact('data','importe'));
    }

    public function modificar_pago()
    {
        return view('layouts.pages.modpago');
    }

    public function verificar_pago($idfolios)
    {
        $contrato = contratos::WHERE('id_folios', '=', $idfolios)->FIRST();

        pago::where('id_contrato', '=', $contrato->id)
        ->update(['fecha_status' => carbon::now()]);

        $pago = pago::WHERE('id_contrato', '=', $contrato->id);

        $folio = folio::findOrfail($idfolios);
        $folio->status = 'Pago_Verificado';
        $folio->save();

        //Notificacion!!
        $letter = [
            'titulo' => 'Solicitud de Pago Validada',
            'cuerpo' => 'La solicitud de pago ' . $pago->no_memo . ' ha sido validada',
            'memo' => $pago->no_memo,
            'unidad' => Auth::user()->unidad,
            'url' => '/pago/verificar_pago/' . $idfolios,
        ];
        //$users = User::where('id', 1)->get();
        // dd($users);
        //event((new NotificationEvent($users, $letter)));

        return redirect()->route('pago-inicio');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $contrato = new contratos();

        $contratos = $contrato::SELECT('contratos.id_contrato', 'contratos.numero_contrato', 'contratos.cantidad_numero',
        'contratos.unidad_capacitacion', 'contratos.municipio', 'contratos.fecha_firma','contratos.arch_factura',
        'folios.status', 'folios.id_folios','tbl_cursos.id_instructor','instructores.id AS idins','instructores.archivo_bancario')
        ->WHERE('contratos.id_contrato', '=', $id)
        ->LEFTJOIN('folios','folios.id_folios', '=', 'contratos.id_folios')
        ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
        ->LEFTJOIN('instructores','instructores.id', '=', 'tbl_cursos.id_instructor')
        ->FIRST();

        $datapago = pago::WHERE('id_contrato', '=', $id)->FIRST();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $contratos->id_contrato)->FIRST();
        $director = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','id')->WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();

        return view('layouts.pages.vstvalidarpago', compact('contratos','director','datapago'));
    }

    public function guardar_pago(Request $request)
    {
        $doc = $request->file('arch_pago'); # obtenemos el archivo
        $urldoc = $this->pdf_upload($doc, $request->id_pago, $request->id_instructor, 'pago_autorizado'); # invocamos el método

        pago::where('id', '=', $request->id_pago)
        ->update(['no_pago' => $request->numero_pago,
                  'fecha' => $request->fecha_pago,
                  'descripcion' => $request->concepto,
                  'fecha_status' => carbon::now(),
                  'arch_pago' => $urldoc]);

        folio::WHERE('id_folios', '=', $request->id_folio)
        ->update(['status' => 'Finalizado']);

        return redirect()->route('pago-inicio');
    }

    public function rechazar_pago(Request $request)
    {
        folio::WHERE('id_folios', '=', $request->idfolios)
        ->update(['status' => 'Pago_Rechazado',
                  'fecha_rechazado' => carbon::now()]);

        $pago = pago::find($request->idPago);
        if($pago->fecha_rechazo == NULL)
        {
            $old = array(array('fecha' => carbon::now()->toDateString(), 'observacion' => $request->observaciones));
        }
        else
        {
            $new = array('fecha' => carbon::now()->toDateString(), 'observacion' => $request->observaciones);
            $old = $pago->fecha_rechazo;
            // dd($new);
            array_push($old, $new);
        }
        pago::where('id', '=', $request->idPago)
        ->update(['observacion' => $request->observaciones,
                  'fecha_rechazo' => $old,
                  'chk_rechazado' => TRUE]);

        //Notificacion!!
        $letter = [
            'titulo' => 'Solicitud de Pago Rechazada',
            'cuerpo' => 'La solicitud de pago ' . $pago->no_memo . ' ha sido rechazada',
            'memo' => $pago->no_memo,
            'unidad' => Auth::user()->unidad,
            'url' => '/pago/solicitud/modificar/' . $request->idfolios,
        ];
        //$users = User::where('id', 1)->get();
        // dd($users);
        //event((new NotificationEvent($users, $letter)));

        return redirect()->route('pago-inicio');
    }

    public function pago_validar($idfolio)
    {
        $folio = folio::findOrfail($idfolio);
        $folio->status = 'Pago_Verificado';
        $folio->save();

        $pago = DB::table('folios')->SELECT('pagos.id')->WHERE('folios.id_folios', '=', $idfolio)
                ->JOIN('contratos', 'contratos.id_folios', '=', 'folios.id_folios')
                ->JOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                ->FIRST();

        pago::where('id', '=', $pago->id)->update(['fecha_validado' => carbon::now()]);
        return redirect()->route('pago-inicio')->with('info', 'El pago ha sido verificado exitosamente.');
    }

    public function pagoRestart($id)
    {
        $affecttbl_inscripcion = DB::table("folios")->WHERE('id_folios', $id)->update(['status' => 'Pago_Rechazado']);

        return redirect()->route('pago-inicio')
                        ->with('success','Solicitud de Pago Reiniciado');
    }

    public function historial_validacion($id)
    {
        //
        $contrato = new contratos();

        $contratos = $contrato::SELECT('contratos.id_contrato', 'contratos.numero_contrato', 'contratos.cantidad_numero',
        'contratos.unidad_capacitacion', 'contratos.municipio', 'contratos.fecha_firma','contratos.arch_factura',
        'folios.status', 'folios.id_folios','tbl_cursos.id_instructor','instructores.id AS idins','instructores.archivo_bancario')
        ->WHERE('contratos.id_contrato', '=', $id)
        ->LEFTJOIN('folios','folios.id_folios', '=', 'contratos.id_folios')
        ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
        ->LEFTJOIN('instructores','instructores.id', '=', 'tbl_cursos.id_instructor')
        ->FIRST();

        $datapago = pago::WHERE('id_contrato', '=', $id)->FIRST();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $contratos->id_contrato)->FIRST();
        $director = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','id')->WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();

        return view('layouts.pages.vsthistorialvalidarpago', compact('contratos','director','datapago'));
    }

    public function documentospago_reporte()
    {
        $unidades = tbl_unidades::SELECT('ubicacion')->WHERE('id', '!=', '0')->ORDERBY('ubicacion','asc')
                                ->GROUPBY('ubicacion')
                                ->GET();

        return view('layouts.pages.vstareportedocumentospago', compact('unidades'));
    }

    public function upload_pago_autorizado(Request $request)
    {
        $idcontrato = DB::Table('contratos')->SELECT('id_contrato')->WHERE('id_folios', $request->idfolpa)->FIRST();
        $pago = pago::WHERE('id_contrato', $idcontrato->id_contrato)->FIRST();
        $doc = $request->file('doc_validado'); # obtenemos el archivo
        $urldoc = $this->pdf_upload($doc, $pago->id, 'pago_autorizado'); # invocamos el método
        $pago->arch_pago = $urldoc;
        $pago->save();

        return redirect()->route('pago-inicio');
    }

    public function tramitesrecepcionados_pdf(Request $request)
    {
        // dd($request);
        $data = contratos::SELECT('contratos.fecha_status', 'contratos.numero_contrato', 'contratos.fecha_firma',
            'contratos.chk_rechazado', 'contratos.fecha_rechazo', 'pagos.status_recepcion', 'pagos.recepcion','pagos.historial', 'tbl_cursos.clave',
		    'tbl_cursos.inicio', 'tbl_cursos.nombre','folios.status')
            ->JOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
            ->JOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
            ->JOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
            ->LEFTJOIN('pagos','pagos.id_contrato','=', 'contratos.id_contrato')
            // ->WHERE('contratos.id_contrato', '=', '4228')
            ->WHERE('contratos.unidad_capacitacion', '=', $request->unidad)
            // ->WHERE('tbl_cursos.tipo_curso', '=', $request->tipo)
            // ->WHERE('tbl_cursos.tcapacitacion', '=', $request->modalidad)
            // ->WHERE('pagos.recepcion', '!=', NULL)
            ->WHERE('pagos.status_recepcion', '!=', 'Rechazado')
            // ->WHERE('pagos.status_recepcion', '!=', 'En Espera')
            ->WHEREBETWEEN('contratos.fecha_status', [$request->fecha1, $request->fecha2])
            // ->WHERE('pagos.historial','!=',null)
            ->ORDERBY('tbl_cursos.inicio', 'ASC')
            ->GET();
            // dd(json_decode($data[7]->historial));
        $head = ['FECHA','NUM.','CLAVE CURSO','ESTATUS'.'FECHA FIRMA DE CONTRATO','NOMBRE DEL INSTRUCTOR'];
        $title = "DOCUMENTOS RECEPCIONADOS";
        $name = $title."_".date('Ymd').".xlsx";
        $view = 'layouts.pages.reportes.excel_contratos_recepcionados';
        return Excel::download(new ExportExcel($data,$head, $title,$view), $name);
        // dd($data[1]->fecha_rechazo);
    }

    public function  reporte_validados_recepcionados(Request $request)
    {
        $mes1 = null; $mes2 = null; $mes3 = null; $mes4 = null; $mes5 = null; $mes6 = null; $mes7 = null; $mes8 = null;
        $mes9 = null; $mes10 = null; $mes11 = null; $mes12 = null;
        $i = $request->fini;
        $now = Carbon::now();
        $monthnow = $this->monthToString($now->month);
        $mi = Carbon::parse($now->year . '-' . $request->fini . '-01');
        $fi = Carbon::parse($now->year . '-' . $request->ffin . '-01');
        $dym = $fi->daysInMonth;
        $fin = $now->year . '-' . $request->ffin . '-' . $dym;
        $nombremesini = $this->monthToString($request->fini);
        $nombremesfin = $this->monthToString($request->ffin);


        //dd($request);
        do {
            if(substr($i, -2, 1) == '0')
            {
                $nomval = "mes" . substr($i, -1);
            }
            else
            {
                $nomval = "mes" . $i;
            }
            //dd($nomval);
            $inicial = Carbon::parse($now->year . '-' . $i . '-01');
            $dym = $inicial->daysInMonth;
            $inicial00 = $now->year . '-' . $i . '-01';
            //dd($inicial00);
            $final = Carbon::parse($now->year . '-' . $i . '-' . $dym . ' 23:59:59');
            //printf($inicial . ' - ' . $final . ' // ');
            $cab1 = "sivyc";
            $cab2 = "fisico";
            $cab3 = "PorEntregar";
            $query1 = "sum(case when b.status in ('Contratado','Verificando_Pago','Pago_Verificado','Finalizado') then 1 else 0 end) AS " . $cab1;
            $query2 = "sum(case when b.status in ('Pago_Verificado','Finalizado')  then 1 else 0 end) AS " . $cab2;
            $query3 = "sum(case when b.status in ('Contratado','Verificando_Pago')  then 1 else 0 end) AS " . $cab3;
            //dd($inicial);
            $$nomval = $data = db::table(DB::raw("(SELECT * from FOLIOS WHERE folios.created_at >=  '$inicial'  and folios.created_at <= '$final' ) AS B"))->select('tbl_unidades.ubicacion',
                DB::raw($query1),
                DB::raw($query2),
                DB::raw($query3),
                )
                //->WHERE('folios.created_at', '>=', $inicial)
                //->WHERE('folios.created_at', '<=', $final)
                //->WHERE('tbl_unidades.ubicacion', '=', 'TUXTLA')
                ->JOIN('tabla_supre', 'tabla_supre.id', '=', 'b.id_supre')
                ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tabla_supre.unidad_capacitacion')
                ->groupBy('tbl_unidades.ubicacion')
                ->orderBy('tbl_unidades.ubicacion')
                ->GET();

            $i++;
        } while ($i <= $request->ffin);
        $data = db::table(DB::raw("(SELECT * from FOLIOS WHERE folios.created_at >=  '$mi'  and folios.created_at <= '$fin' ) AS B"))->select('tbl_unidades.ubicacion',
            DB::raw("sum(case when b.status in ('Contratado','Verificando_Pago','Pago_Verificado','Finalizado')  then 1 else 0 end) AS Sivyc"),
            DB::raw("sum(case when b.status in ('Pago_Verificado','Finalizado')  then 1 else 0 end) AS Fisico"),
            DB::raw("sum(case when b.status in ('Contratado','Verificando_Pago')  then 1 else 0 end) AS PorEntregar"),
            DB::raw("sum(case when b.status in ('Finalizado')  then 1 else 0 end) AS Pagado"),
            DB::raw("sum(case when b.status in ('Pago_Verificado')  then 1 else 0 end) AS PorPagar"),
            DB::raw("sum(case when b.status in ('Contrato_Rechazado','Pago_Rechazado')  then 1 else 0 end) AS Observados")
            )
            //->WHERE('folios.created_at', '>=', $mi)
            //->WHERE('folios.created_at', '<=', $fin) a
            //->WHERE('tbl_unidades.ubicacion', '=', 'TUXTLA')
            ->JOIN('tabla_supre', 'tabla_supre.id', '=', 'b.id_supre')
            ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tabla_supre.unidad_capacitacion')
            ->groupBy('tbl_unidades.ubicacion')
            ->orderBy('tbl_unidades.ubicacion')
            ->GET();
            //dd($monthnow);

            //return view('layouts.pdfpages.reportescontratosval', compact('mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','data','nombremesini','nombremesfin'));

            $pdf = PDF::loadView('layouts.pdfpages.reportescontratosval', compact('mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','data','nombremesini','nombremesfin','now','monthnow'));
            $pdf->setPaper('legal', 'Landscape');
            return $pdf->stream('medium.pdf');
    }

    public function mostrar_pago($id)
    {
        $data = contratos::SELECT('instructores.numero_control','instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                                  'tbl_cursos.curso','tbl_cursos.clave','contratos.unidad_capacitacion','folios.id_folios','folios.importe_total','folios.iva',
                                  'pagos.id AS id_pago','pagos.no_memo','pagos.fecha','pagos.no_pago','pagos.descripcion','pagos.liquido')
                           ->WHERE('contratos.id_contrato', '=', $id)
                           ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', 'tbl_cursos.id_instructor')
                           ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                           ->FIRST();

        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;

        //return view('layouts.pages.vstapagofinalizado', compact('data', 'nomins'));
        $pdf = PDF::loadView('layouts.pages.vstapagofinalizado', compact('data', 'nomins'));
        return $pdf->download('medium.pdf');
    }

    public function agendar_entrega_pago(Request $request)
    {
        // dd($request);
        $variables = ['factura_pdf','factura_xml','contratof_pdf','solpa_pdf','asistencias_pdf','calificaciones_pdf',
                      'evidencia_fotografica_pdf'];
        if(isset($request->id_contrato_agendac))
        {
            for($i=0;$i<=6;$i++)
            {
                $variables[$i] = $variables[$i].'c';
                $id_contrato = $request->id_contrato_agendac;
            }
        }
        else
        {
            $id_contrato = $request->id_contrato_agenda;
        }
        $curso  = DB::TABLE('contratos')->SELECT('tbl_cursos.id_instructor','tbl_cursos.tipo_curso')
            ->JOIN('folios','folios.id_folios','contratos.id_folios')
            ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->WHERE('contratos.id_contrato', $id_contrato)
            ->FIRST();

        $doc_factura_pdf = $request->file($variables[0]); # obtenemos el archivo
        $doc_factura_xml = $request->file($variables[1]); # obtenemos el archivo
        $doc_contrato = $request->file($variables[2]); # obtenemos el archivo
        $doc_solpa = $request->file($variables[3]); # obtenemos el archivo
        $doc_asistencias = $request->file($variables[4]); # obtenemos el archivo
        $doc_calificaciones = $request->file($variables[5]); # obtenemos el archivo
        $doc_evidencia_fotografica = $request->file($variables[6]);

        $contrato = contratos::find($id_contrato);
        if(isset($doc_factura_pdf))
        {
            $factura_pdf = $this->pdf_upload($doc_factura_pdf, $id_contrato, $curso->id_instructor, 'factura_pdf'); # invocamos el método
            $contrato->arch_factura = $factura_pdf;
        }
        if(isset($doc_factura_xml))
        {
            $factura_xml = $this->xml_upload($doc_factura_xml, $id_contrato, $curso->id_instructor, 'factura_xml'); # invocamos el métododd
            $contrato->arch_factura_xml = $factura_xml;
        }
        if(isset($doc_contrato))
        {
            $contrato_pdf = $this->pdf_upload($doc_contrato, $id_contrato, $curso->id_instructor, 'contrato'); # invocamos el método
            $contrato->arch_contrato = $contrato_pdf;
        }
        if(isset($doc_solpa))
        {
            $solpa_pdf = $this->pdf_upload($doc_solpa, $id_contrato, $curso->id_instructor, 'solicitud_pago'); # invocamos el método
            $pago = pago::where('id_contrato', $id_contrato)
            ->update(['arch_solicitud_pago' => $solpa_pdf]);
        }
        if(isset($doc_asistencias))
        {
            $asistencias_pdf = $this->pdf_upload($doc_asistencias, $id_contrato, $curso->id_instructor, 'lista_asistencia'); # invocamos el método
            $pago = pago::where('id_contrato', $id_contrato)
            ->update(['arch_asistencia' => $asistencias_pdf]);
        }
        if(isset($doc_calificaciones))
        {
            $calificaciones_pdf = $this->pdf_upload($doc_calificaciones, $id_contrato, $curso->id_instructor, 'lista_calificaciones'); # invocamos el método
            $pago = pago::where('id_contrato', $id_contrato)
            ->update(['arch_calificaciones' => $calificaciones_pdf]);
        }
        if(isset($doc_evidencia_fotografica))
        {
            $evidencia_fotografica_pdf = $this->pdf_upload($doc_evidencia_fotografica, $id_contrato, $curso->id_instructor, 'evidencia_fotografica'); # invocamos el método
            $pago = pago::where('id_contrato', $id_contrato)
            ->update(['arch_evidencia' => $evidencia_fotografica_pdf]);
        }

        if($request->tipo_envio == 'guardar_enviar' || $request->tipo_envioc == 'guardar_enviar')
        {
            pago::where('id_contrato', $id_contrato)
            ->update(['status_recepcion' => 'En Espera',
                      'fecha_envio' => carbon::now()->format('d-m-Y')]);
        }

        $contrato->save();

        return redirect()->route('pago-inicio')
                ->with('success', 'Entrega de Documentos Agendada Correctamente');
    }

    public function confirmar_entrega_fisica(Request $request)
    {
        $fecha_actual = carbon::now();
        $folio = folio::find($request->id_folio_entrega)->update(['recepcion' => $fecha_actual->toDateString()]);
        return redirect()->route('pago-inicio')
                ->with('success', 'Entrega de Documentos Confirmada Correctamente');
    }

    public function validar_cita_fisica(Request $request)
    {
        // dd($request);
        $updarray = $arrhistorial = array();
        $update = pago::WHERE('id_contrato',$request->id_contrato_cita)->first();
        $archivos = DB::TABLE('contratos')
            ->SELECT('arch_factura','arch_factura_xml','arch_contrato','doc_validado','archivo_ine','archivo_bancario','pdf_curso',
            'instructor_mespecialidad','espe','archivo_alta','tbl_cursos.id_instructor')
            ->WHERE('contratos.id_contrato', $request->id_contrato_cita)
            ->JOIN('pagos','pagos.id_contrato','contratos.id_contrato')
            ->JOIN('folios','folios.id_folios','contratos.id_folios')
            ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->JOIN('tabla_supre','tabla_supre.id','folios.id_supre')
            ->JOIN('instructores','instructores.id','tbl_cursos.id_instructor')->FIRST();

        $especialidad_seleccionada = DB::Table('especialidad_instructores')
            ->SELECT('especialidad_instructores.id','especialidades.nombre')
            ->WHERE('especialidad_instructores.id_instructor',$archivos->id_instructor)
            ->WHERE('especialidades.nombre', '=', $archivos->espe)
            ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
            ->FIRST();

        $memoval = especialidad_instructor::WHERE('id',$especialidad_seleccionada->id)
            ->whereJsonContains('hvalidacion', [['memo_val' => $archivos->instructor_mespecialidad]])->value('hvalidacion');
        if(isset($memoval))
        {
            foreach($memoval as $me)
            {
                if(isset($me['memo_val']) && $me['memo_val'] == $archivos->instructor_mespecialidad)
                {
                    $archivos->instructor_mespecialidad = $me['arch_val'];
                    break;
                }
            }
        }
        else
        {
            $archivos->instructor_mespecialidad = $archivos->archivo_alta;
        }

        $update->fecha_agenda = $request->fecha_confirmada;
        $update->status_recepcion = 'VALIDADO';
        $updarray = ['status' => 'VALIDADO',
                     'fecha_agenda' => $request->fecha_confirmada,
                     'fecha_validacion' => carbon::now()->format('d-m-Y'),
                     'solicitud_pago' => $update->arch_solicitud_pago,
                     'cuenta_bancaria' => $archivos->archivo_bancario,
                     'validacion_instructor' => $archivos->instructor_mespecialidad,
                     'arc' => $archivos->pdf_curso,
                     'valsupre' => $archivos->doc_validado,
                     'factura_pdf' => $archivos->arch_factura,
                     'factura_xml' => $archivos->arch_factura_xml,
                     'contrato' => $archivos->arch_contrato,
                     'identificacion' => $archivos->archivo_ine,
                     'asistencia' => $update->arch_asistencia,
                     'calificacion' => $update->arch_calificaciones,
                     'evidencia' => $update->arch_evidencia];


        if(!isset($update->historial))
        {
            array_push($arrhistorial,$updarray);
        }
        else
        {
            $arrhistorial = $update->historial;
            array_push($arrhistorial,$updarray);
        }
        $update->historial = $arrhistorial;
        $update->save();
        return redirect()->route('pago-inicio')
                ->with('success', 'Documentación Digital Confirmada Correctamente');
    }

    public function rechazar_entrega_fisica(Request $request)
    {
        // dd($request);
        $updarray = $arrhistorial = array();
        $update = pago::WHERE('id_contrato',$request->id_contrato_entrega_rechazo)->first();
        $archivos = DB::TABLE('contratos')
            ->SELECT('arch_factura','arch_factura_xml','arch_contrato','doc_validado','archivo_ine','archivo_bancario','pdf_curso',
            'instructor_mespecialidad','espe','archivo_alta','tbl_cursos.id_instructor')
            ->WHERE('contratos.id_contrato', $request->id_contrato_entrega_rechazo)
            ->JOIN('pagos','pagos.id_contrato','contratos.id_contrato')
            ->JOIN('folios','folios.id_folios','contratos.id_folios')
            ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->JOIN('tabla_supre','tabla_supre.id','folios.id_supre')
            ->JOIN('instructores','instructores.id','tbl_cursos.id_instructor')->FIRST();

        $especialidad_seleccionada = DB::Table('especialidad_instructores')
            ->SELECT('especialidad_instructores.id','especialidades.nombre')
            ->WHERE('especialidad_instructores.id_instructor',$archivos->id_instructor)
            ->WHERE('especialidades.nombre', '=', $archivos->espe)
            ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
            ->FIRST();
            // dd($especialidad_seleccionada);

        $memoval = especialidad_instructor::WHERE('id',$especialidad_seleccionada->id)
            ->whereJsonContains('hvalidacion', [['memo_val' => $archivos->instructor_mespecialidad]])->value('hvalidacion');
        if(isset($memoval))
        {
            foreach($memoval as $me)
            {
                if($me['memo_val'] == $archivos->instructor_mespecialidad)
                {
                    $archivos->instructor_mespecialidad = $me['arch_val'];
                    break;
                }
            }
        }
        else
        {
            $archivos->instructor_mespecialidad = $archivos->archivo_alta;
        }

        $update->observacion_rechazo_recepcion = $request->observacion_rechazo;
        $update->status_recepcion = 'Rechazado';
        $updarray = ['status' => 'Rechazado',
                     'observacion' => $update->observacion_rechazo_recepcion,
                     'fecha_rechazo' => carbon::now()->format('d-m-Y'),
                     'solicitud_pago' => $update->arch_solicitud_pago,
                     'cuenta_bancaria' => $archivos->archivo_bancario,
                     'validacion_instructor' => $archivos->instructor_mespecialidad,
                     'arc' => $archivos->pdf_curso,
                     'valsupre' => $archivos->doc_validado,
                     'factura_pdf' => $archivos->arch_factura,
                     'factura_xml' => $archivos->arch_factura_xml,
                     'contrato' => $archivos->arch_contrato,
                     'identificacion' => $archivos->archivo_ine,
                     'asistencia' => $update->arch_asistencia,
                     'calificacion' => $update->arch_calificaciones,
                     'evidencia' => $update->arch_evidencia];


        if(!isset($update->historial))
        {
            array_push($arrhistorial,$updarray);
        }
        else
        {
            $arrhistorial = $update->historial;
            array_push($arrhistorial,$updarray);
        }
        $update->historial = $arrhistorial;
        $update->save();
        return redirect()->route('pago-inicio')
                ->with('success', 'Rechazo de entrega de Documentos Correctamente');
    }

    public function recibido_entrega_fisica(Request $request)
    {
        // dd($request);
        $updarray = $arrhistorial = array();
        $update = pago::WHERE('id_contrato',$request->id_contrato_entrega)->first();
        $archivos = DB::TABLE('contratos')
            ->SELECT('arch_factura','arch_factura_xml','arch_contrato','doc_validado','archivo_ine','archivo_bancario','pdf_curso',
            'instructor_mespecialidad','espe','archivo_alta','tbl_cursos.id_instructor')
            ->WHERE('contratos.id_contrato', $request->id_contrato_entrega)
            ->JOIN('pagos','pagos.id_contrato','contratos.id_contrato')
            ->JOIN('folios','folios.id_folios','contratos.id_folios')
            ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->JOIN('tabla_supre','tabla_supre.id','folios.id_supre')
            ->JOIN('instructores','instructores.id','tbl_cursos.id_instructor')->FIRST();

        $especialidad_seleccionada = DB::Table('especialidad_instructores')
            ->SELECT('especialidad_instructores.id','especialidades.nombre')
            ->WHERE('especialidad_instructores.id_instructor',$archivos->id_instructor)
            ->WHERE('especialidades.nombre', '=', $archivos->espe)
            ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
            ->FIRST();

        $memoval = especialidad_instructor::WHERE('id',$especialidad_seleccionada->id)
            ->whereJsonContains('hvalidacion', [['memo_val' => $archivos->instructor_mespecialidad]])->value('hvalidacion');
        if(isset($memoval))
        {
            foreach($memoval as $me)
            {
                if(isset($me['memo_val']) &&$me['memo_val'] == $archivos->instructor_mespecialidad)
                {
                    $archivos->instructor_mespecialidad = $me['arch_val'];
                    break;
                }
            }
        }
        else
        {
            $archivos->instructor_mespecialidad = $archivos->archivo_alta;
        }

        $update->status_recepcion = 'VALIDADO';
        $update->recepcion = carbon::now()->format('d-m-Y');
        $updarray = ['status' => 'VALIDADO Y RECIBIDO',
                     'fecha_recibido' => carbon::now()->format('d-m-Y'),
                     'solicitud_pago' => $update->arch_solicitud_pago,
                     'cuenta_bancaria' => $archivos->archivo_bancario,
                     'validacion_instructor' => $archivos->instructor_mespecialidad,
                     'arc' => $archivos->pdf_curso,
                     'valsupre' => $archivos->doc_validado,
                     'factura_pdf' => $archivos->arch_factura,
                     'factura_xml' => $archivos->arch_factura_xml,
                     'contrato' => $archivos->arch_contrato,
                     'identificacion' => $archivos->archivo_ine,
                     'asistencia' => $update->arch_asistencia,
                     'calificacion' => $update->arch_calificaciones,
                     'evidencia' => $update->arch_evidencia];


        if(!isset($update->historial))
        {
            array_push($arrhistorial,$updarray);
        }
        else
        {
            $arrhistorial = $update->historial;
            array_push($arrhistorial,$updarray);
        }
        $update->historial = $arrhistorial;
        $update->save();
        return redirect()->route('pago-inicio')
                ->with('success', 'Recepción de Documentos Guardado Correctamente');
    }

    public function norecibido_entrega_fisica(Request $request)
    {
        // dd($request);
        $updarray = $arrhistorial = array();
        $update = pago::WHERE('id_contrato',$request->id_contrato_noentrega)->first();
        $archivos = DB::TABLE('contratos')
            ->SELECT('arch_factura','arch_factura_xml','arch_contrato','doc_validado','archivo_ine','archivo_bancario','pdf_curso',
            'instructor_mespecialidad','espe','archivo_alta','tbl_cursos.id_instructor')
            ->WHERE('contratos.id_contrato', $request->id_contrato_noentrega)
            ->JOIN('pagos','pagos.id_contrato','contratos.id_contrato')
            ->JOIN('folios','folios.id_folios','contratos.id_folios')
            ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->JOIN('tabla_supre','tabla_supre.id','folios.id_supre')
            ->JOIN('instructores','instructores.id','tbl_cursos.id_instructor')->FIRST();

        $especialidad_seleccionada = DB::Table('especialidad_instructores')
            ->SELECT('especialidad_instructores.id','especialidades.nombre')
            ->WHERE('especialidad_instructores.id_instructor',$archivos->id_instructor)
            ->WHERE('especialidades.nombre', '=', $archivos->espe)
            ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
            ->FIRST();

        $memoval = especialidad_instructor::WHERE('id',$especialidad_seleccionada->id)
            ->whereJsonContains('hvalidacion', [['memo_val' => $archivos->instructor_mespecialidad]])->value('hvalidacion');
        if(isset($memoval))
        {
            foreach($memoval as $me)
            {
                if($me['memo_val'] == $archivos->instructor_mespecialidad)
                {
                    $archivos->instructor_mespecialidad = $me['arch_val'];
                    break;
                }
            }
        }
        else
        {
            $archivos->instructor_mespecialidad = $archivos->archivo_alta;
        }

        $update->status_recepcion = 'No Recibido';
        $updarray = ['status' => 'No Recibido',
                     'fecha_no_recibido' => carbon::now()->format('d-m-Y'),
                     'solicitud_pago' => $update->arch_solicitud_pago,
                     'cuenta_bancaria' => $archivos->archivo_bancario,
                     'validacion_instructor' => $archivos->instructor_mespecialidad,
                     'arc' => $archivos->pdf_curso,
                     'valsupre' => $archivos->doc_validado,
                     'factura_pdf' => $archivos->arch_factura,
                     'factura_xml' => $archivos->arch_factura_xml,
                     'contrato' => $archivos->arch_contrato,
                     'identificacion' => $archivos->archivo_ine,
                     'asistencia' => $update->arch_asistencia,
                     'calificacion' => $update->arch_calificaciones,
                     'evidencia' => $update->arch_evidencia];


        if(!isset($update->historial))
        {
            array_push($arrhistorial,$updarray);
        }
        else
        {
            $arrhistorial = $update->historial;
            array_push($arrhistorial,$updarray);
        }
        $update->historial = $arrhistorial;
        $update->save();
        return redirect()->route('pago-inicio')
                ->with('success', 'No Recepción de Documentos Guardado Correctamente');
    }

    public function retorno_validacion_entrega_fisica(Request $request)
    {
        // dd($request);

        $updarray = $arrhistorial = array();
        $update = pago::WHERE('id_contrato',$request->id_retorno_recepcion)->first();
        if($update->status_recepcion == 'recepcion tradicional') {
            $contrato_id = DB::TABLE('contratos')->WHERE('contratos.id_contrato', $update->id_contrato)->VALUE('id_folios');
            $folio_rt = folio::FIND($contrato_id);
            $folio_rt->status = 'Capturando';
            $folio_rt->save();

        } else {
            $archivos = DB::TABLE('contratos')
                ->SELECT('arch_factura','arch_factura_xml','arch_contrato','doc_validado','archivo_ine','archivo_bancario','pdf_curso',
                'instructor_mespecialidad','espe','archivo_alta','tbl_cursos.id_instructor')
                ->WHERE('contratos.id_contrato', $request->id_retorno_recepcion)
                ->JOIN('pagos','pagos.id_contrato','contratos.id_contrato')
                ->JOIN('folios','folios.id_folios','contratos.id_folios')
                ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
                ->JOIN('tabla_supre','tabla_supre.id','folios.id_supre')
                ->JOIN('instructores','instructores.id','tbl_cursos.id_instructor')->FIRST();

            $especialidad_seleccionada = DB::Table('especialidad_instructores')
                ->SELECT('especialidad_instructores.id','especialidades.nombre')
                ->WHERE('especialidad_instructores.id_instructor',$archivos->id_instructor)
                ->WHERE('especialidades.nombre', '=', $archivos->espe)
                ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
                ->FIRST();

            $memoval = especialidad_instructor::WHERE('id',$especialidad_seleccionada->id)
                ->whereJsonContains('hvalidacion', [['memo_val' => $archivos->instructor_mespecialidad]])->value('hvalidacion');
            if(isset($memoval))
            {
                foreach($memoval as $me)
                {
                    if($me['memo_val'] == $archivos->instructor_mespecialidad)
                    {
                        $archivos->instructor_mespecialidad = $me['arch_val'];
                        break;
                    }
                }
            }
            else
            {
                $archivos->instructor_mespecialidad = $archivos->archivo_alta;
            }

            $update->status_recepcion = 'Rechazado';
            $update->recepcion = null;
            $updarray = ['status' => 'Retorno de Validacion',
                        'observacion' => $update->observacion_rechazo_recepcion,
                        'fecha_retorno' => carbon::now()->format('d-m-Y'),
                        'solicitud_pago' => $update->arch_solicitud_pago,
                        'cuenta_bancaria' => $archivos->archivo_bancario,
                        'validacion_instructor' => $archivos->instructor_mespecialidad,
                        'arc' => $archivos->pdf_curso,
                        'valsupre' => $archivos->doc_validado,
                        'factura_pdf' => $archivos->arch_factura,
                        'factura_xml' => $archivos->arch_factura_xml,
                        'contrato' => $archivos->arch_contrato,
                        'identificacion' => $archivos->archivo_ine,
                        'asistencia' => $update->arch_asistencia,
                        'calificacion' => $update->arch_calificaciones,
                        'evidencia' => $update->arch_evidencia,
                        'usuario_retorno' => Auth::user()->name,];



            if(!isset($update->historial))
            {
                array_push($arrhistorial,$updarray);
            }
            else
            {
                $arrhistorial = $update->historial;
                array_push($arrhistorial,$updarray);
            }
            $update->historial = $arrhistorial;
            $update->save();
        }
        return redirect()->route('pago-inicio')
                ->with('success', 'Valdiación de Documentos Digitales Retornado Correctamente');
    }


    public function financieros_reporte()
    {
        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();

        return view('layouts.pages.vstareportefinancieros', compact('unidades'));
    }

    public function financieros_reportepdf(Request $request)
    {
        $i = 0;
        set_time_limit(0);
        $count = 0;

        $data = folio::SELECT('folios.folio_validacion as suf','folios.status','tabla_supre.fecha','tabla_supre.no_memo',
                                  'tabla_supre.unidad_capacitacion','tbl_cursos.curso','tbl_cursos.clave',
                                  'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                                  'instructores.numero_control')
                                  ->WHERE('folios.status', '!=', 'En_Proceso')
                                  ->WHERE('folios.status', '!=', 'Rechazado')
                                  ->WHERE('folios.status', '!=', 'Validado')
                                  ->whereDate('tabla_supre.fecha', '>=', $request->fecha1)
                                  ->whereDate('tabla_supre.fecha', '<=', $request->fecha2)
                                  ->LEFTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                                  ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                                  ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                                  //->OrderByRaw('FIELD(folios.status, ' . implode(', ', $x) . ') ASC')
                                  ->GET();

        if ($request->filtro == 'curso')
        {
            $data = $data->WHERE('tbl_cursos.id', '=', $request->id_curso);
        }
        else if ($request->filtro == 'unidad')
        {
            $data = $data->WHERE('tabla_supre.unidad_capacitacion', '=', $request->unidad);
        }

        $data = $data->sortBy(function($item){
            return array_search($item->status, ['Validando_Contrato', 'Contrato_Rechazado', 'Contratado', 'Verificando_Pago', 'Pago_Rechazado', 'Pago_Verificado', 'Finalizado']);
        });

        $pdf = PDF::loadView('layouts.pdfpages.reportefinancieros', compact('data','count'));
        $pdf->setPaper('legal', 'Landscape');
        return $pdf->Download('formato de control '. $request->fecha1 . ' - '. $request->fecha2 .'.pdf');

    }

    public function concentrado_ingresos()
    {
        $roluser = DB::TABLE('roles')->SELECT('name')->JOIN('role_user AS ru','ru.role_id','roles.id')
            ->WHERE('user_id',Auth::user()->id)
            ->FIRST();
        $unidaduser = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->WHERE('id',Auth::user()->unidad)->FIRST();
        $unidades = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->GROUPBY('ubicacion')->ORDERBY('ubicacion','ASC')->GET();
        // dd($roluser);
        return view('layouts.pages.vstarf-001', compact('unidades','unidaduser','roluser'));
    }

    public function concentrado_ingresos_pdf(Request $request)
    {
        // dd($request);
        set_time_limit(0);
        $distintivo = DB::table('tbl_instituto')->pluck('distintivo')->first();
        $realiza = DB::TABLE('users')->SELECT('name','puesto')->WHERE('id',Auth::user()->id)->FIRST();
        $fecha['inicio'] = $request->fecha_inicio;
        $fecha['termino'] = $request->fecha_termino;
        $fecha['hoy'] = carbon::now()->format('d-m-Y');
        $data = DB::TABLE('folios AS f')
            ->SELECT('curso','tc.folio_pago','tc.movimiento_bancario','costo','u.cuenta','u.delegado_administrativo',
                    'u.pdelegado_administrativo','u.dunidad','u.pdunidad', 'u.ubicacion')
            ->RIGHTJOIN('tbl_cursos AS tc', 'tc.id', 'f.id_cursos')
            ->JOIN('tbl_unidades AS u', 'u.cct', 'tc.cct')
            ->WHEREBETWEEN('tc.fecha_movimiento_bancario',[$fecha['inicio'],$fecha['termino']])
            ->WHERE('tc.costo','!=','0.00')
            ->WHERE('tc.movimiento_bancario','!=',NULL)
            ->WHERE('ubicacion', $request->unidades)
            ->ORDERBY('fecha_apertura', 'ASC')
            ->GET();
        // dd($data);

        $pdf = PDF::loadView('layouts.pdfpages.concentradodeingresos', compact('distintivo','fecha','data','realiza'));
        $pdf->setPaper('Letter','portrait');
        return $pdf->stream('RF-001.pdf');
    }

    public function subir_contrato_rezagado(Request $request)
    {
        $contrato = contratos::Find($request->idcontrato_rezagado);
        $id_instructor = DB::Table('tbl_cursos')->WHERE('id', $contrato->id_curso)->Value('id_instructor');

        $doc_contrato = $request->file('contrato_rezagado_doc'); # obtenemos el archivo
        $contrato_pdf = $this->pdf_upload($doc_contrato, $contrato->id_contrato, $id_instructor, 'contrato'); # invocamos el método
        $contrato->arch_contrato = $contrato_pdf;
        $contrato->save();

        return redirect()->route('pago-inicio')
                ->with('success', 'Contrato Firmado Cargado Correctamente');
    }

    protected function pdf_upload($pdf, $id, $idins, $nom)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $pdf->storeAs('/uploadFiles/instructor/'.$idins.'/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadFiles/instructor/'.$idins."/".$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }

    protected function xml_upload($xml, $id, $idins, $nom)
    {
        # nuevo nombre del archivo
        $xmlFile = trim($nom."_".date('YmdHis')."_".$id.".xml");
        $xml->storeAs('/uploadFiles/instructor/'.$idins.'/'.$id, $xmlFile); // guardamos el archivo en la carpeta storage
        $xmlUrl = Storage::url('/uploadFiles/instructor/'.$idins."/".$id."/".$xmlFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $xmlUrl;
    }

    public function downloadRar($id_contrato)
    {
        $archivos = DB::TABLE('pagos')->SELECT('pagos.arch_solicitud_pago','pagos.arch_asistencia','pagos.arch_evidencia','pagos.arch_calificaciones',
        'instructores.archivo_bancario','tbl_cursos.instructor_mespecialidad','tbl_cursos.pdf_curso','tbl_cursos.espe','tabla_supre.doc_validado',
        'contratos.arch_factura','contratos.arch_factura_xml','contratos.arch_contrato','contratos.numero_contrato','instructores.archivo_ine')
        ->JOIN('contratos','contratos.id_contrato','pagos.id_contrato')
        ->JOIN('folios','folios.id_folios','contratos.id_folios')
        ->JOIN('tabla_supre','tabla_supre.id','folios.id_supre')
        ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
        ->JOIN('instructores','instructores.id','tbl_cursos.id_instructor')
        ->WHERE('pagos.id_contrato',$id_contrato)
        ->FIRST();

        $especialidad_seleccionada = DB::Table('especialidad_instructores')
            ->SELECT('especialidad_instructores.id','especialidades.nombre')
            ->WHERE('especialidad_instructores.memorandum_validacion',$archivos->instructor_mespecialidad)
            ->WHERE('especialidades.nombre', '=', $archivos->espe)
            ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
            ->FIRST();

        $memoval = especialidad_instructor::WHERE('id',$especialidad_seleccionada->id)
            ->whereJsonContains('hvalidacion', [['memo_val' => $archivos->instructor_mespecialidad]])->value('hvalidacion');
        if(isset($memoval))
        {
            foreach($memoval as $me)
            {
                if($me['memo_val'] == $archivos->instructor_mespecialidad)
                {
                    $archivos->instructor_mespecialidad = $me['arch_val'];
                    break;
                }
            }
        }
        else
        {
            $archivos->instructor_mespecialidad = $archivos->archivo_alta;
        }

    $zip = new ZipArchive;
    $fileName = 'documentacion_'.$id_contrato.'.rar';
    $zipFileName = public_path('example.zip');
    $filePath = public_path($fileName);

    if ($zip->open($filePath, ZipArchive::CREATE) !== TRUE) {
        // Handle error creating RAR archive
        return response("Failed to create RAR archive", 500);
    }

        // Add files to the RAR archive
        $zip->addFile('C:\prueba.pdf', 'solicitud_pago.pdf');
        $zip->addFile($archivos->arch_solicitud_pago, 'solicitud_pago.pdf');
        $zip->addFile($archivos->archivo_bancario, 'banco.pdf');
        $zip->addFile($archivos->instructor_mespecialidad, 'validacion_instructor.pdf');
        $zip->addFile($archivos->pdf_curso, 'ARC.pdf');
        $zip->addFile($archivos->doc_validado, 'suficiencia_presupuestal.pdf');
        $zip->addFile($archivos->arch_factura, 'factura.pdf');
        $zip->addFile($archivos->arch_factura_xml, 'factura_xml.xml');
        $zip->addFile($archivos->arch_contrato, 'contrato.pdf');
        $zip->addFile($archivos->archivo_ine, 'identificacion pdf');
        dd($zip);
        if(isset($archivos->arch_asistencia))
        {
            $zip->addFile($archivos->arch_asistencia, 'asistencias.pdf');
        }
        if(isset($archivos->arch_evidencia))
        {
            $zip->addFile($archivos->arch_evidencia, 'evidencia_fotografica.pdf');
        }
        if(isset($archivos->arch_calificaciones))
        {
            $zip->addFile($archivos->arch_calificaciones, 'calificaciones.pdf');
        }

        $zip->close();

        if (!file_exists($filePath)) {
            // Handle error: file not found
            return response("File not found", 500);
        }

        $headers = [
            'Content-Type' => 'application/rar',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return response()->download($filePath, $fileName, $headers)->deleteFileAfterSend(true);

    }


    protected function monthToString($month)
    {
        switch ($month)
        {
            case 1:
                return 'ENERO';
            break;

            case 2:
                return 'FEBRERO';
            break;

            case 3:
                return 'MARZO';
            break;

            case 4:
                return 'ABRIL';
            break;

            case 5:
                return 'MAYO';
            break;

            case 6:
                return 'JUNIO';
            break;

            case 7:
                return 'JULIO';
            break;

            case 8:
                return 'AGOSTO';
            break;

            case 9:
                return 'SEPTIEMBRE';
            break;

            case 10:
                return 'OCTUBRE';
            break;

            case 11:
                return 'NOVIEMBRE';
            break;

            case 12:
                return 'DICIEMBRE';
            break;
        }
    }
}
