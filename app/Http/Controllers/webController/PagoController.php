<?php
/* Creador: Orlando Chavez */
namespace App\Http\Controllers\webController;

use App\Models\pago;
use App\Models\instructor;
use App\Models\contratos;
use App\Models\folio;
use App\Models\directorio;
use App\Models\contrato_directorio;
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

        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();

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
        ->orderBy('pagos.created_at', 'desc')
        ->PAGINATE(25, [
            'contratos.id_contrato', 'contratos.numero_contrato', 'contratos.cantidad_letras1',
            'contratos.unidad_capacitacion', 'contratos.municipio', 'contratos.fecha_firma','folios.permiso_editar',
            'contratos.docs', 'contratos.observacion', 'folios.status','folios.recepcion', 'folios.id_folios',
            'folios.id_supre','pagos.created_at','pagos.arch_pago'
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
                ->orderBy('contratos.fecha_firma', 'desc')
                ->PAGINATE(25, [
                    'contratos.id_contrato', 'contratos.numero_contrato', 'contratos.cantidad_letras1',
                    'contratos.unidad_capacitacion', 'contratos.municipio', 'contratos.fecha_firma',
                    'folios.permiso_editar','contratos.docs',
                    'contratos.observacion', 'folios.status', 'folios.id_folios','folios.id_supre','folios.recepcion','pagos.arch_pago'
                ]);
                break;
        }

        return view('layouts.pages.vstapago', compact('contratos_folios','unidades','año_pointer','array_ejercicio'));
    }

    public function crear_pago($id)
    {
        $data = contratos::SELECT('instructores.numero_control','instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                                  'tbl_cursos.curso','tbl_cursos.clave','contratos.unidad_capacitacion','folios.id_folios','folios.importe_total','folios.iva','pagos.id AS id_pago')
                                    ->WHERE('contratos.id_contrato', '=', $id)
                                    ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                                    ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', 'folios.id_cursos')
                                    ->LEFTJOIN('instructores', 'instructores.id', 'tbl_cursos.id_instructor')
                                    ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                                    ->FIRST();

        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        $importe = round($data->importe_total-$data->iva, 2);
        return view('layouts.pages.frmpago', compact('data', 'nomins','importe'));
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
        $urldoc = $this->pdf_upload($doc, $request->id_pago, 'pago_autorizado'); # invocamos el método

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
        $data = contratos::SELECT('contratos.fecha_status', 'contratos.numero_contrato',
            'contratos.chk_rechazado', 'contratos.fecha_rechazo', 'folios.recepcion', 'tbl_cursos.clave',
		    'tbl_cursos.inicio', 'tbl_cursos.nombre','folios.status')
            ->JOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
            ->JOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
            ->JOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
            ->WHERE('contratos.id_contrato', '=', '4228')
            // ->WHERE('tbl_unidades.ubicacion', '=', $request->unidad)
            // ->WHERE('tbl_cursos.tipo_curso', '=', $request->tipo)
            // ->WHERE('tbl_cursos.tcapacitacion', '=', $request->modalidad)
            // ->WHEREBETWEEN('tbl_cursos.inicio', [$request->fecha1, $request->fecha2])
            ->ORDERBY('tbl_cursos.inicio', 'ASC')
            ->GET();
            // dd($data);
        // dd($data[1]->fecha_rechazo);
        if ($request->tipo == 'CURSO')
        {
            $tipo = 'HONORARIOS';
        }
        else
        {
            $tipo = 'CERTIFICACIONES EXTRAORDINARIAS';
        }

        if ($request->modalidad == 'PRESENCIAL')
        {
            $modalidad = 'PRESENCIAL';
        }
        else
        {
            $modalidad = 'A DISTANCIA';
        }
        $unidad = $request->unidad;
        $distintivo = DB::table('tbl_instituto')->pluck('distintivo')->first();

        $pdf = PDF::loadView('layouts.pdfpages.reportetramitesrecepcionados', compact('data','tipo', 'modalidad','unidad','distintivo'));
        $pdf->setPaper('legal', 'Landscape');
        return $pdf->Stream('Tramites recepcionados de la unidad '. $request->unidad . $request->fecha1 . ' - '. $request->fecha2 .'.pdf');
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

    protected function pdf_upload($pdf, $id, $nom)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $pdf->storeAs('/uploadFiles/supre/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadFiles/supre/'.$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
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
