<?php

namespace App\Http\Controllers\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Reporterf001Interface;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Filters\ReportFilter;
use App\Filters\FolioFilter;
use App\Filters\StatusFilter;
use App\Filters\RangeDateFilter;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use PDF;
use App\Services\ReportService;
use App\Http\Requests\rf001ComentariosRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Tokens_icti;
use App\Models\Reportes\Rf001Model;
use setasign\Fpdi\Fpdi;
use App\Http\Requests\Rf001StoreRequest;

class Rf001Controller extends Controller
{
    private $path_files;
    private Reporterf001Interface $rfoo1Repository;
    protected $path_files_cancelled;
    public function __construct(Reporterf001Interface $rfoo1Repository)
    {
        $this->rfoo1Repository = $rfoo1Repository;
        $this->path_files = env("APP_URL").'/storage/';
        $this->path_files_cancelled = env("APP_URL").'/grupos/recibo/descargar?folio_recibo=';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $concentrado = null)
    {
        // Crear una instancia de Carbon para la fecha actual
        $fechaActual = Carbon::now();
        $periodo = $this->obtenerPrimerYUltimoDiaHabil($fechaActual);
        if ($concentrado) {
            $getConcentrado = $this->rfoo1Repository->getDetailRF001Format($concentrado);
            $idRf001 = $concentrado;

            // Decodificar el JSON
            $data = json_decode($getConcentrado, true);

            // Obtener los movimientos como un array PHP
            $movimientos = json_decode($data['movimientos'], true);

            // Extraer los folios de los movimientos
            $foliosMovimientos = array_column($movimientos, 'folio');

            $periodoInicio = ($request->get('fechaInicio') !== null && $request->get('fechaInicio') !== '') ? $request->get('fechaInicio') : $getConcentrado->periodo_inicio;
            $periodoFin = ($request->get('fechaFin') !== null && $request->get('fechaFin') !== '')? $request->get('fechaFin') : $getConcentrado->periodo_fin;

            $fechaInicio = ($request->get('fechaInicio') !== null && $request->get('fechaInicio') !== '') ? $request->get('fechaInicio') : $getConcentrado->periodo_inicio;
            $fechaFin = ($request->get('fechaFin') !== null && $request->get('fechaFin') !== '')? $request->get('fechaFin') : $getConcentrado->periodo_fin;
        }
        else {
            $getConcentrado = null;
            $foliosMovimientos = null;
            $idRf001 = 0;

            $periodoInicio = ($request->get('fechaInicio') !== null && $request->get('fechaInicio') !== '') ? $request->get('fechaInicio') : $periodo[0];
            $periodoFin = ($request->get('fechaFin') !== null && $request->get('fechaFin') !== '')? $request->get('fechaFin') : $periodo[4];

            $fechaInicio = $request->get('fechaInicio');
            $fechaFin = $request->get('fechaFin');
        }

        // Recuperar los checkboxes seleccionados de los parámetros de consulta
        $selectedCheckboxes = $request->input('seleccionados', []);

        $idUnidad = Auth::user()->unidad;
        $obtenerUnidad = \DB::table('tbl_unidades')->where('id', $idUnidad)->first();
        $unidad = $obtenerUnidad->unidad;
        // Obtener la URL actual sin los parámetros especificados
        $filteredUrl = $request->except(['_token', 'ID', 'filtrar', 'idconcepto']);
        $folioGrupo = $request->get('folio_grupo');
        $getUnidad = $request->get('unidad');
        $statusRecibo = $request->get('estado');
        $user = Auth::user();
        $datos = $this->rfoo1Repository->index($user);
        $filters = [];
        // Formatear la fecha al formato deseado

        $data = $this->rfoo1Repository->getReciboQry($obtenerUnidad->unidad);
        #nuevo fechas del periodo que se obtiene la información
        $tipoSolicitud = 'CONCENTRADO'; //declarado vacio para fines prácticos


        $data->when(!empty($fechaInicio) && !empty($fechaFin), function ($query) use ($fechaInicio, $fechaFin) {
            return $query->whereRaw("EXISTS (
                        SELECT 1
                        FROM jsonb_array_elements(tbl_recibos.depositos) AS deposito
                        WHERE (deposito->>'fecha')::date BETWEEN ? AND ?
                    ) ", [$fechaInicio, $fechaFin]);
        });

        $data->when($request->filled('unidad'), function ($query) use ($request) {
            return $query->where('tbl_unidades.unidad', $request->get('unidad'));
        });

        // Filtrado por folio de grupo
        $data->when(isset($folioGrupo) && $folioGrupo !== '', function ($query) use ($folioGrupo) {
            return $query->where('tbl_recibos.folio_recibo', '=', trim($folioGrupo));
        });

        // Filtrado por estado del recibo
        $data->when(isset($statusRecibo) && $statusRecibo !== '', function ($query) use ($statusRecibo, &$tipoSolicitud) {
            if ($statusRecibo == 'CANCELADO') {
                $tipoSolicitud = 'CANCELADO';
            }
            return $query->where('tbl_recibos.status_folio', '=', trim($statusRecibo));
        });

        $query = $data->orderBy('tbl_recibos.id', 'ASC')->paginate(25);

        $currentYear = date('Y');
        $path_files = $this->path_files;

        return view('reportes.rf001.index', compact('datos', 'currentYear', 'query', 'idUnidad', 'unidad', 'path_files', 'getConcentrado', 'foliosMovimientos', 'selectedCheckboxes', 'periodoInicio', 'periodoFin', 'fechaInicio', 'fechaFin', 'idRf001', 'tipoSolicitud'))->render();
    }

    public function dashboard(Request $request)
    {
        // obtener unidad
        $idUnidad = Auth::user()->unidad;
        $obtenerUnidad = \DB::table('tbl_unidades')->where('id', $idUnidad)->first();
        $unidad = $obtenerUnidad->unidad;

        return view('reportes.rf001.dashboard', compact('unidad'))->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Rf001StoreRequest $request)
    {
        // manejar error de consulta con try catch
        try {
            // obtener usuario que realiza la acción
            $logUser = Auth::user();
            //siempre trata de ejecutarse el código
            $response = $this->rfoo1Repository->generateRF001Format($request, $logUser);
            if ($response) {
                # si se ejecutó correctamente lo envíamos a una ruta distinta
                return redirect()->route('reporte.rf001.sent')->with('message', 'Formato de concentrado de ingresos enviado!');
            } else {
                // mandar a una ruta que controle el error
                return back()->withErrors(['sent' => 'Ocurrió un error al enviar la información'])->withInput();
            }
        } catch (\Throwable $th) {
            //lanzar un catch de error ejecución, no sabemos cuál error $th;
            return back()->withErrors(['sistema' => 'Ocurrió un error interno en el sistema '. $th])->withInput();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $getConcentrado = $this->rfoo1Repository->getDetailRF001Format($id);
        $getSigner = $this->rfoo1Repository->getSigner(Auth::user()->id);
        $memorandum = $getConcentrado->memorandum;
        $cadenaOriginal = DB::table('documentos_firmar')->select('cadena_original', 'id', 'documento')->where('numero_o_clave', $memorandum)->first();
        $pathCancelado = $this->path_files_cancelled;
        // crear un arreglo

        if ($cadenaOriginal) {
            $data = [
                'cadenaOriginal' => $cadenaOriginal->cadena_original,
                'indice' => $cadenaOriginal->id,
                'baseXml' => base64_decode($cadenaOriginal->documento)
            ];
        } else {
            $data = [
                'cadenaOriginal' => null,
                'indice' => null,
                'baseXml' => null,
            ];
        }

        $getToken = Tokens_icti::latest()->first();

        if (!isset($token)) {
            // no hay registros
            $token = (new ReportService())->generarToken();
        } else {
            $token = $getToken->token;
        }

        // obtener revision
        $revisionLocal = collect(json_decode($getConcentrado->movimiento, true))->first(function ($item) {
            return isset($item['tipo'], $item['usuario']) &&
                (
                    ($item['tipo'] === 'REVISION_LOCAL' && $item['usuario'] != Auth::user()->email) ||
                    ($item['tipo'] === 'GENERADO' && $item['usuario'] != Auth::user()->email)
                );
        });
        $pathFile = $this->path_files;
        $curpFirmante = $getSigner->curp;
        return view('reportes.rf001.detalles', compact('getConcentrado', 'pathFile', 'id', 'data', 'token', 'curpFirmante', 'revisionLocal', 'pathCancelado'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $getConcentrado = $this->rfoo1Repository->getDetailRF001Format($id);
        $idRf001 = $id;
        $idUnidad = Auth::user()->unidad;
        $obtenerUnidad = \DB::table('tbl_unidades')->where('id', $idUnidad)->first();
        $unidad = $obtenerUnidad->unidad;

        // Decodificar el JSON
        $data = json_decode($getConcentrado, true);

        // Obtener los movimientos como un array PHP
        $movimientos = json_decode($data['movimientos'], true);

        // Extraer los folios de los movimientos
        $foliosMovimientos = array_column($movimientos, 'folio');

        $fechaActual = Carbon::now();
        // Formatear la fecha al formato deseado

        $periodo = $this->obtenerPrimerYUltimoDiaHabil($fechaActual);
        $periodoInicio = $periodo[0];
        $periodoFin = $periodo[4];
        $currentYear = date('Y');

        $getQuery = $this->rfoo1Repository->getQueryCancelado($obtenerUnidad->unidad);
        $getQuery->where('tbl_recibos.status_folio', '=', 'CANCELADO');

        $query = $getQuery->orderBy('tbl_recibos.id', 'ASC')->paginate(25);

        return view('reportes.rf001.index_cancelar', compact('foliosMovimientos', 'movimientos', 'idRf001', 'getConcentrado', 'periodoInicio', 'periodoFin', 'currentYear', 'query', 'idUnidad', 'unidad'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $response = $this->rfoo1Repository->updateAndValidateFormatRf001($id, $request);
            if ($response['code'] == 1)
            {
                # si la respuesta es satisfactoria
                return redirect()->route('reporte.rf001.sent')->with('message', 'Actualización del Memorandum '.$request->get('consecutivo').' correctamente!');
            } else {
                return back()->withErrors(['error' => $response['message']])->withInput();
            }
        } catch (\Throwable $th) {
            //lanzar un catch de error ejecución, no sabemos cuál error $th;
            return back()->withErrors(['sistema' => 'Ocurrió un error interno en el sistema '. $th])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //getReport
    }

    protected function obtenerPrimerYUltimoDiaHabil($startDate)
    {
        // Crear un array para almacenar los días hábiles
        $diasHabiles = [];

        $primerDiaSemana = $startDate->startOfWeek();

        // Iterar sobre los próximos 7 días
        for ($i = 0; $i < 5; $i++) {
            // Obtener el día actual más el número de días de la iteración
            $dia = $primerDiaSemana->copy()->addDays($i);

            // Verificar si el día es hábil (de lunes a viernes)
            if ($dia->isWeekday()) {
                $diasHabiles[] = $dia->toDateString(); // Agregar el día al array de días hábiles
            }
        }
        return $diasHabiles;
    }

    public function getSentFormat(Request $request)
    {
        // si se necesita generar el dato
        // aplicar el filtro sólo para memorandum
        $unidad = Auth::user()->unidad;
        $data = $this->rfoo1Repository->sentRF001Format($unidad);
        return view('reportes.rf001.formatos', compact('data'))->render();
    }

    public function addComment(rf001ComentariosRequest $request): JsonResponse
    {
         // Obtiene los datos validados automáticamente
         $validatedData = $request->validated();
         if (!$validatedData) {
            # retornar información del error de la validación
            return response()->json(['errors' => $validatedData->errors()], 422);
         }

        $storeData = $this->rfoo1Repository->storeComment($request);

         return response()->json([
            'data' => $storeData
         ], Response::HTTP_CREATED);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeData(Request $request): JSONResponse
    {
        $order = $request->only([
            'elemento',
            'details'
        ]);

        return response()->json(
            [
                'data' => $this->rfoo1Repository->storeData($order)
            ],
            Response::HTTP_CREATED
        );
    }

    public function getPdfReport($id)
    {
        $rf001 = (new Rf001Model())->findOrFail($id); // obtener RF001 por id
        $organismo = Auth::user()->id_organismo;
        $idReporte = base64_encode($id);
        $unidad = $rf001->unidad;

        $dataunidades = \DB::table('tbl_unidades')->where('unidad', $rf001->unidad)->first();
        $idUnidad = Auth::user()->unidad;

        $direccion = $dataunidades->direccion;
        // aplicando distructuración
        $distintivo = \DB::table('tbl_instituto')->value('distintivo'); #texto de encabezado del pdf
        list($bodyMemo, $bodyRf001, $uuid, $objeto, $puestos, $qrCodeBase64) = $this->rfoo1Repository->generarDocumentoPdf($idReporte, $dataunidades->id, $organismo);

        $data = [
            'bodyMemo' => $bodyMemo,
            'distintivo' => $distintivo,
            'direccion' => $direccion,
            'uuid' => $uuid,
            'objeto' => $objeto,
            'puestos' => $puestos,
            'qrCodeBase64' => $qrCodeBase64,
            'unidad' => $unidad,
            'bodyRf001' => $bodyRf001,
        ];

        // generar el PDF
        $pdf = PDF::loadview('reportes.rf001.reporterf001', $data)
            ->setPaper('a4', 'portrait'); //Configura el tamaño de papel y la orientación

        $dompdf = $pdf->getDomPDF();
        $options = $dompdf->getOptions();
        $options->setIsHtml5ParserEnabled(true);  // Habilita el parser HTML5
        $options->setIsRemoteEnabled(true);       // Permitir imágenes remotas
        $dompdf->setOptions($options);
        // reenderizar PDF
        return $pdf->stream('concentreado_de_ingresos_rf001_'.$rf001->memorandum.'.pdf');

    }

    public function getReporteCancelado($id)
    {
        $rf001 = (new Rf001Model())->findOrFail($id); // obtener RF001 por id
        $unidad = Auth::user()->unidad;
        $organismo = Auth::user()->id_organismo;
        $idReporte = base64_encode($id);

        $data = \DB::table('tbl_unidades')->where('unidad', $rf001->unidad)->first();
        $direccion = $data->direccion;
        // aplicando distructuración
        $distintivo = \DB::table('tbl_instituto')->value('distintivo'); #texto de encabezado del pdf
        list($bodyMemo, $uuid, $objeto, $puestos, $qrCodeBase64) = $this->rfoo1Repository->generarDoctoCancelado($idReporte, $unidad, $organismo);

        $report = PDF::loadView('reportes.rf001.vista_concentrado.memorf001', compact('bodyMemo', 'distintivo','direccion',  'uuid', 'objeto', 'puestos', 'qrCodeBase64'))->setPaper('a4', 'portrait')->output();

        // return view('reportes.rf001.reporterf001', compact('bodyRf001', 'distintivo', 'direccion'))->render();

        // $pdf = PDF::loadView('reportes.rf001.reporterf001');
        $file1 = tempnam(sys_get_temp_dir(), 'report');

        // escribir los datos del PDF en los archivos temporales
        file_put_contents($file1, $report);

        // cambiar los PDF usando FPDI
        $newPdf = new Fpdi();
        $newPdf->AddPage();
        $pageCount1 = $newPdf->setSourceFile($file1);
        $tpldx1 = $newPdf->importPage(1);
        $newPdf->useTemplate($tpldx1);

        unlink($file1);

        return $newPdf->Output('reporte_de_cancelacion_rf001_'.$rf001->memorandum.'.pdf', 'I');
    }

    public function cambioEstado($id)
    {
        $estado = 'REVISION';
        return response()->json(
            [
                'data' => $this->rfoo1Repository->actualizarEstado($id, $estado)
            ],
            Response::HTTP_CREATED
        );
    }

    public function cambioSello($id)
    {
        $date = Carbon::now();
        $estado = 'PARASELLAR';
        $rf001 = (new Rf001Model())->findOrFail($id);
        $fechaUnica = $this->rfoo1Repository->getDate($date);
        // Obtener el campo JSON y decodificarlo
        $datosExistentes  = json_decode($rf001->movimiento, true);
        // obtener revision
        $revision = 'ENVIAR_A_SELLADO';
        //GUARDAMOS INFORMACIÓN DEL MOVIMIENTO EN LA TABLA dateFormat
        $jsonObject = [
            'fecha' => $fechaUnica,
            'usuario' => Auth::user()->email,
            'unidad' => Auth::user()->unidad,
            'tipo' => $revision
        ];

        $datosExistentes[] = $jsonObject;

        (new Rf001Model())->where('id', $id)->update([
            'movimiento' => json_encode($datosExistentes, JSON_UNESCAPED_UNICODE),
        ]);

        return response()->json(
            [
                'data' => $this->rfoo1Repository->actualizarEstado($id, $estado)
            ],
            Response::HTTP_CREATED
        );
    }

    public function retornarFinanciero(Request $request)
    {
        $estado = true;
        $data = collect($request->all());
        return response()->json(
            [
                'payload' => $this->rfoo1Repository->retornarFinanciero($data, $estado)
            ],
            Response::HTTP_CREATED
        );
    }

}
