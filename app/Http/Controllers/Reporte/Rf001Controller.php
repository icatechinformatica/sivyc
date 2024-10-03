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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\Tokens_icti;
use App\Models\Reportes\Rf001Model;
use setasign\Fpdi\Fpdi;

class Rf001Controller extends Controller
{
    private $path_files;
    private Reporterf001Interface $rfoo1Repository;
    public function __construct(Reporterf001Interface $rfoo1Repository)
    {
        $this->rfoo1Repository = $rfoo1Repository;
        $this->path_files = env("APP_URL").'/storage/';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $concentrado = null)
    {
        if ($concentrado) {
            $getConcentrado = $this->rfoo1Repository->getDetailRF001Format($concentrado);
            $idRf001 = $concentrado;

            // Decodificar el JSON
            $data = json_decode($getConcentrado, true);

            // Obtener los movimientos como un array PHP
            $movimientos = json_decode($data['movimientos'], true);

            // Extraer los folios de los movimientos
            $foliosMovimientos = array_column($movimientos, 'folio');
        }
        else {
            $getConcentrado = null;
            $foliosMovimientos = null;
            $idRf001 = 0;
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
        // Crear una instancia de Carbon para la fecha actual
        $fechaActual = Carbon::now();
        // Formatear la fecha al formato deseado

        $periodo = $this->obtenerPrimerYUltimoDiaHabil($fechaActual);
        $data = $this->rfoo1Repository->getReciboQry($obtenerUnidad->unidad);
        #nuevo fechas del periodo que se obtiene la información
        $periodoInicio = $periodo[0];
        $periodoFin = $periodo[4];

        if ( !empty($request->get('fechaInicio')) || !empty($request->get('fechaFin'))) {
            $fechaInicio = $request->get('fechaInicio');
            $fechaFin = $request->get('fechaFin');

            $data->whereBetween('tbl_recibos.fecha_expedicion', [$fechaInicio, $fechaFin]);
        } else {
            $fechaInicio = $periodo[0];
            $fechaFin = $periodo[4];
        }

        if ($getUnidad !== '' && isset($getUnidad)) {
            $data->where('tbl_unidades.unidad', $request->get('unidad'));
        }


        if (isset($folioGrupo) && $folioGrupo !== '') {
            $data->where('tbl_recibos.folio_recibo', '=', $folioGrupo);
        }

        if (isset($statusRecibo) && $statusRecibo !== '') {
            $data->where('tbl_recibos.status_folio', '=', $statusRecibo);
        }


        $query = $data->orderBy('tbl_recibos.id', 'ASC')->paginate(25);
        $currentYear = date('Y');
        $path_files = $this->path_files;

        return view('reportes.rf001.index', compact('datos', 'currentYear', 'query', 'idUnidad', 'unidad', 'path_files', 'getConcentrado', 'foliosMovimientos', 'selectedCheckboxes', 'periodoInicio', 'periodoFin', 'fechaInicio', 'fechaFin', 'idRf001'))->render();
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
    public function store(Request $request)
    {
        // manejar error de consulta con try catch
        try {
            // obtener usuario que realiza la acción
            $logUser = Auth::user();
            //siempre trata de ejecutarse el código
            $response = $this->rfoo1Repository->generateRF001Format($request, $logUser);
            if ($response) {
                $bandera = Crypt::encrypt('solicitud');
                $encrypted = base64_encode($bandera);
                $encrypted = str_replace(['+', '/', '='], ['-', '_', ''], $encrypted);
                # si se ejecutó correctamente lo envíamos a una ruta distinta
                return redirect()->route('reporte.rf001.sent', ['generado' => $encrypted])->with('message', 'Formato de concentrado de ingresos enviado!');
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
    public function show($id, $solicitud)
    {
        $getConcentrado = $this->rfoo1Repository->getDetailRF001Format($id);
        $getSigner = $this->rfoo1Repository->getSigner(Auth::user()->id);
        $memorandum = $getConcentrado->memorandum;
        $cadenaOriginal = DB::table('documentos_firmar')->select('cadena_original', 'id', 'documento')->where('numero_o_clave', $memorandum)->first();
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
                    ($item['tipo'] === 'REVISION_LOCAL' && $item['usuario'] === Auth::user()->email) ||
                    ($item['tipo'] === 'REVISION_GENERAL' && $item['usuario'] === Auth::user()->email)
                );
        });
        $pathFile = $this->path_files;
        $curpFirmante = $getSigner->curp;
        return view('reportes.rf001.detalles', compact('getConcentrado', 'pathFile', 'id', 'solicitud', 'data', 'token', 'curpFirmante', 'revisionLocal'))->render();
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
            $order = $request->only([
                'consecutivo',
                'id_unidad',
                'periodoInicio',
                'periodoFIn',
                'unidad'
            ]);
            $response = $this->rfoo1Repository->updateFormatoRf001($order, $id);
            if ($response) {
                # si la respuesta es satisfactoria
                return redirect()->route('reporte.rf001.sent')->with('message', 'Formato de concentrado de ingresos '.$request['consecutivo'].' actualizado correctamente!');
            } else {
                return back()->withErrors(['sent' => 'Ocurrió un error al actualizar la información.'])->withInput();
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
        $encrypted = str_replace(['-', '_'], ['+', '/'], $request->get('generado'));
        $encrypted = base64_decode($encrypted);
        $dato = Crypt::decrypt($encrypted);
        $data = $this->rfoo1Repository->sentRF001Format($request);
        return view('reportes.rf001.formatos', compact('data', 'dato'))->render();
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
        $unidad = Auth::user()->unidad;
        $organismo = Auth::user()->id_organismo;
        $idReporte = base64_encode($id);

        $data = \DB::table('tbl_unidades')->where('unidad', $rf001->unidad)->first();
        $direccion = $data->direccion;
        // aplicando distructuración
        $distintivo = \DB::table('tbl_instituto')->value('distintivo'); #texto de encabezado del pdf
        list($bodyMemo, $bodyRf001, $uuid, $objeto, $puestos, $qrCodeBase64) = $this->rfoo1Repository->generarDocumentoPdf($idReporte, $unidad, $organismo);

        $report = PDF::loadView('reportes.rf001.reporterf001', compact('bodyMemo', 'distintivo','direccion',  'uuid', 'objeto', 'puestos', 'qrCodeBase64'))->setPaper('a4', 'portrait')->output();
        $formatoRF001 = PDF::loadView('reportes.rf001.vista_concentrado.formarf001', compact('bodyRf001', 'distintivo', 'direccion', 'uuid', 'objeto', 'puestos', 'qrCodeBase64'))->setPaper('a4', 'portrait')->output();

        // return view('reportes.rf001.vista_concentrado.formarf001', compact('bodyRf001', 'distintivo', 'direccion'))->render();

        // $pdf = PDF::loadView('reportes.rf001.reporterf001');
        $file1 = tempnam(sys_get_temp_dir(), 'report');
        $file2 = tempnam(sys_get_temp_dir(), 'formatoRF001');

        // escribir los datos del PDF en los archivos temporales
        file_put_contents($file1, $report);
        file_put_contents($file2, $formatoRF001);

        // cambiar los PDF usando FPDI
        $newPdf = new Fpdi();
        $newPdf->AddPage();
        $pageCount1 = $newPdf->setSourceFile($file1);
        $tpldx1 = $newPdf->importPage(1);
        $newPdf->useTemplate($tpldx1);

        $newPdf->AddPage();
        $pageCount2 = $newPdf->setSourceFile($file2);
        $tpldx2 = $newPdf->importPage(1);
        $newPdf->useTemplate($tpldx2);

        unlink($file1);
        unlink($file2);

        return $newPdf->Output('concentreado_de_ingresos_rf001_'.$rf001->memorandum.'.pdf', 'I');

        //generar el PDF
        // $pdf = PDF::loadHTML($combinedConent);
        // return $pdf->stream('combined_documents.pdf');
        // return $report->stream();
        // return view('reportes.rf001.reporterf001', $data)->render();
    }
}
