<?php

namespace App\Http\Controllers\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Reporterf001Interface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Reportes\Rf001Model;
use App\Models\Tokens_icti;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Http;
use setasign\Fpdi\PdfParser\StreamReader;
use App\Extensions\FPDIWithRotation;

class Rf001AdministrativoController extends Controller
{
    private Reporterf001Interface $rf001Repository;
    private $path_files;
    protected $path_files_cancelled;
    public function __construct(Reporterf001Interface $rf001Repository)
    {
        $this->rf001Repository = $rf001Repository;
        $this->path_files = env("APP_URL").'/storage/';
        $this->path_files_cancelled = env("APP_URL").'/grupos/recibo/descargar?folio_recibo=';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $datos = $this->rf001Repository->getFirmadoFormat($request);
        return view('reportes.rf001.formatofirma', compact('datos'))->render();
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
        //
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
        $getConcentrado = $this->rf001Repository->getDetailRF001Format($id);
        $getFirmante = $this->rf001Repository->getSigner(Auth::user()->id);
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
            return isset($item['tipo']) && $item['tipo'] === 'REVISION_GENERAL';
        });
        $pathFile = $this->path_files;
        $curpFirmante = $getFirmante->curp;
        return view('reportes.rf001.revision.detalle_revision', compact('getConcentrado', 'pathFile', 'id', 'data', 'token', 'curpFirmante', 'revisionLocal', 'pathCancelado'))->render();
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function sendBack(Request $request)
    {
        $id = $request->get('idRf001');
        $this->rf001Repository->regresarEstadoRecibo($id);
        return response()->json([
            'resp' => $this->rf001Repository->reenviarSolicitud($request),
            'message' => 'Documento Regresado para Corrección!',
         ], Response::HTTP_CREATED);
    }

    public function firmar(Request $request)
    {
        $firma = $this->rf001Repository->firmarDocumento($request);
        if ($firma == 1) {
            # TODO : - listo a mostrar el resultado
            return redirect()->route('administrativo.index')->with('message', 'Documento Firmando y espera de Sello!');
        }
    }

    public function sellado(Request $request)
    {
        try {
            $sello = $this->rf001Repository->sellarDocumento($request);
            if ($sello == 1) {
                return redirect()->route('administrativo.index')->with('message', 'Documento sellado exitosamente!');
            } else {
                return redirect()->back()->with('error', $sello);
            }

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Ocurrió un error al momento de sellar: '.$th);
        }
    }

    public function aprobar(Request $request)
    {
        $estado = 'APROBADO';
        $id = $request->get('idRf001');
        $this->rf001Repository->actualizarRecibo($id);
        return response()->json([
            'resp' => $this->rf001Repository->actualizarEstado($id, $estado),
            'message' => 'Documento Aprobado para proceso de efirma!',
        ], Response::HTTP_CREATED);
    }

    public function generarMasivo($id)
    {
        try {
            $data = $this->rf001Repository->generarPdfMasivo($id);
            $pdf = new FPDIWithRotation();
             // Configuración de la marca de agua
            $marcaDeAguaTexto = "SIVyC";    // Texto de la marca de agua
            $marcaDeAguaColor = [200, 200, 200]; // Color gris claro para emular transparencia
            $marcaDeAguaAngulo = 45;            // Ángulo de la marca de agua
            $marcaDeAguaTamaño = 250;            // Tamaño de la fuente


             // Dimensiones de la página en milímetros (A4: 210 x 297)
            $pageWidth = 210;
            $pageHeight = 297;

            foreach ($data as $key) {

                $response = Http::get($key);

                if ($response->ok()) {
                    $pdfContent = $response->body();

                    // Cargar el contenido PDF en FPDI usando StreamReader
                    $totalPaginas = $pdf->setSourceFile(StreamReader::createByString($pdfContent));

                    // Importar cada página del PDF
                    for ($i = 1; $i <= $totalPaginas; $i++) {
                        $pdf->AddPage();
                        $paginaId = $pdf->importPage($i);
                        $pdf->useTemplate($paginaId, 0, 0, $pageWidth, $pageHeight); // Ajusta la posición y tamaño si es necesario

                         // Configurar la fuente y color para la marca de agua
                        $pdf->SetFont('Arial', 'B', $marcaDeAguaTamaño);
                        $pdf->SetTextColor($marcaDeAguaColor[0], $marcaDeAguaColor[1], $marcaDeAguaColor[2], 3);

                        // Calcular la posición central para el texto de la marca de agua
                        $xPos = $pageWidth / 2;
                        $yPos = $pageHeight - 30;

                        // Aplicar rotación y posicionar el texto de la marca de agua en el centro
                        $pdf->SetAlpha(0.3);
                        $pdf->Text(230, $yPos, $marcaDeAguaTexto); // Ajusta la posición si es necesario

                    }
                } else {
                    return response()->json(['error' => "No se pudo cargar el archivo desde la URL: " . $key], 404);
                }
            }

             // Salida del PDF combinado
            return response()->make($pdf->Output('S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="documento_concentrado_recibos_Rf001.pdf"',
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Ocurrió un error al generar el documento masivo: '.$th->getMessage());
        }
    }

    public function getFuncionarios($unidad)
    {
        $getUnidad = (new ReportService())->getFirmantes($unidad);
        return response()->json([
            'resp' => $getUnidad,
            'message' => 'DATOS DE FIRMANTES FIRMA ELECTRONICA POR UNIDAD',
        ], Response::HTTP_CREATED);
    }
}
