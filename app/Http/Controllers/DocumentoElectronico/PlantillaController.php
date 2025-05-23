<?php

namespace App\Http\Controllers\DocumentoElectronico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\DocumentoService;
use App\Services\EFirmaService;
use Illuminate\Support\Facades\Auth;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PlantillaController extends Controller
{
    private $servicioPlantilla;

    public function __construct(DocumentoService $servicioPlantilla)
    {
        $this->servicioPlantilla = $servicioPlantilla;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        // TODO: MEJORAR CONSULTA Y AGREGAR A PDF
        // $plantillas = $this->servicioPlantilla->obtenerPlantillas();
        $plantillas = $this->servicioPlantilla->getPlantilla(5);
        return response()->json([
            'success' => true,
            'data' => $plantillas
        ], 200);
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
        $documento = $this->servicioPlantilla->getPlantilla($id);
        // como llamarias al método de inyección de variables en un servicio dedicado
                //Prueba de inyeccion html
        $uuid = 'prueba uuid'; $cadena_sello = 'prueba de cadena de sellado'; $fecha_sello = '22/04/2025';

        $selloDigital = "Sello Digital: | GUID: $uuid | Sello: $cadena_sello | Fecha: $fecha_sello<br>
                Este documento ha sido Firmado Electrónicamente, teniendo el mismo valor que la firma autógrafa
                de acuerdo a los Artículos 1, 3, 8 y 11 de la Ley de Firma Electrónica Avanzada del Estado de Chiapas";

        $variables = [
            'sello_digital' => $selloDigital,
            'no_contrato' => 'TU/DA/800/0272/2024.',
            'titular_uc' => 'LIC. ILIANA MICHELLE RAMIREZ MOLINA',
            'cargo_titular_uc' => 'TITULAR DE LA DIRECCIÓN DE LA UNIDAD DE CAPACITACION TUXTLA',
            'instructor' => 'ROGELIO MOISES MUELA LOPEZ',
            'director_general' => 'Mtra. Fabiola Lizbeth Astudillo Reyes',
            'cargo_dg' => 'Titular de la Dirección General del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
            'gobernador' => 'Dr. Rutilio Escandón Cadenas',
            'fecha_nom_dg' => '16 de enero de 2019',
            'espe_instructor' => 'ADMINISTRACIÓN',
            'regimen_instructor' => 'SUELDOS Y SALARIOS E INGRESOS ASIMILADOS A SALARIOS',
            'clave_grupo' => '2B-24-ADMI-CAE-0138',
            'tipo_identif_instructor' => 'Credencial Para Votar',
            'folio_identif_instructor' => '1625073024802',
            'rfc_instructor' => 'MULR870429QN2',
            'domicilio_instructor' => 'C SABINO NORTE MZA 139 LT 9, COL PATRIA NUEVA DE SABINES, C.P. 29045, TUXTLA GUTIERREZ, CHIS.',
            'importe_monto' => '12,800.00',
            'importe_monto_letra' => 'DOCE MIL OCHOCIENTOS PESOS 00/100 M.N.',
            'municipio' => 'TUXTLA GUTIERREZ',
            // otras variables...
        ];
        $contenidoProcesado = $this->servicioPlantilla->procesarPlantilla($documento->cuerpo, $variables);
        $newDocument = (new EFirmaService())->setBody($contenidoProcesado);
        return $newDocument;
        //
        // return response()->json([
        //     'success' => true,
        //     'data' => $contenidoProcesado
        // ], 200);
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
        $plantillas = $this->servicioPlantilla->getPlantilla($id);
        return response()->json([
            'success' => true,
            'data' => $plantillas
        ], 200);
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

    public function generarPdf($file)
    {
        return PDF::loadHTML($file);
    }

    public function loadFile($id)
    {
        $objEplantilla = ['id', 'tipo', 'cuerpo', 'vigencia'];
        $dataQry = $this->servicioPlantilla->getPlantilla($id, 'Plantillas\EPlantilla', $objEplantilla, 'id'); // llamada del servicio con el metodo obtener plantilla parametro con el id y el nombre del modelo
        #TODO: preferible pasar el parametro desde el controlador para no procesar en la capa de datos
        switch ($dataQry->tipo) {
            case 'RF001':
                #TODO: cada caso servirá para procesar contenido exclusivo del archivo deseado a cargar
                $arraySelect = [ 'id', 'memorandum', 'estado', 'movimientos', 'id_unidad', 'envia', 'dirigido', 'archivos', 'unidad', 'periodo_inicio', 'periodo_fin',  'realiza', 'movimiento', 'tipo', 'confirmed', 'created_at' ];

                $rfgetData = $this->servicioPlantilla->getPlantilla(22, 'Reportes\Rf001Model', $arraySelect, 'id');
                $organismo = Auth::user()->id_organismo;
                $unidad = $rfgetData->unidad;
                $organismoPublico = \DB::table('organismos_publicos')->select('nombre_titular', 'cargo_fun')->where('id', '=', $organismo)->first();
                $dataunidades = \DB::table('tbl_unidades')->where('unidad', $rfgetData->unidad)->first();
                $fechaActual = $rfgetData->created_at->format('Y-m-d');
                $fechaFormateada = $this->servicioPlantilla->formatoFechaCrearMemo($fechaActual);
                $municipio = mb_strtoupper($dataunidades->municipio, 'UTF-8');
                $dirigido = \DB::table('tbl_funcionarios')->where('id', 114)->first();
                $intervalo = $this->servicioPlantilla->formatoIntervaloFecha($rfgetData->periodo_inicio, $rfgetData->periodo_fin);
                $importeTotal = 0;

                $movimiento = json_decode($rfgetData->movimientos, true);
                $importeMemo = 0;
                $count = 0;
                $ccpHtml = ''; // Aquí se guarda el contenido generado en el foreach
                $validadores = ['DIRECTOR', 'DIRECTORA', 'ENCARGADO DE LA UNIDAD', 'ENCARGADA DE LA UNIDAD'];
                $ccpValidador = '';
                $ccp = $this->servicioPlantilla->setCpp($rfgetData->id_unidad);
                $ccpDelegado = $this->servicioPlantilla->setFuncionarios($rfgetData->id_unidad);
                $instituto = \DB::table('tbl_instituto')->first();
                // Decodificar el campo cuentas_bancarias
                $cuentas_bancarias = json_decode($instituto->cuentas_bancarias, true); // true convierte el JSON en un array asociativo
                $cuenta = $cuentas_bancarias[$dataunidades->ubicacion]['BBVA'];
                $creado = htmlspecialchars(Carbon::parse($rfgetData->created_at)->format('d/m/Y'));
                $fechaInicio = new \DateTime($rfgetData->periodo_inicio);
                $fechaFin = new \DateTime($rfgetData->periodo_fin);
                $periodoTexto = htmlspecialchars($fechaInicio->format('d/m/Y')) . ' AL ' . htmlspecialchars($fechaFin->format('d/m/Y'));

                $dateCreacion = Carbon::parse($rfgetData->created_at);
                $dateCreacion->locale('es'); // Configurar el idioma a español
                $nombreMesCreacion = $dateCreacion->translatedFormat('F');
                $fechaObs = $dateCreacion->day . "/" . Str::upper($nombreMesCreacion) . "/" . $dateCreacion->year;

                $distintivo = \DB::table('tbl_instituto')->value('distintivo'); #texto de encabezado del pdf
                $leyenda = '';

                $bandera = false;
                $elaboroHtml = '';
                $foliosDepositos = [];
                $tbodyHTML = '';
                $counter = 0;
                $direccion = $dataunidades->direccion;
                $direccionHtml = '';

                if (is_array($movimiento)) {
                    foreach ($movimiento as $key) {
                        $importeMemo += $key['importe'] ?? 0;
                    }
                }

                $importeLetra = $this->servicioPlantilla->letras($importeMemo);

                if (!is_array($direccion)) {
                    $direccion = explode('*', $direccion);
                }

                // Ordenar el array $movimiento de menor a mayor en base al número del campo 'folio'
                usort($movimiento, function($a, $b) {
                    // Extraer el número después del prefijo en el campo 'folio'
                    preg_match('/\d+/', $a['folio'], $matchA);
                    preg_match('/\d+/', $b['folio'], $matchB);
                    $numA = isset($matchA[0]) ? (int) $matchA[0] : 0;
                    $numB = isset($matchB[0]) ? (int) $matchB[0] : 0;

                    return $numA <=> $numB;
                });

                // Observaciones
                $recibos = implode(', ', array_map(fn($m) => htmlspecialchars($m['folio']), $movimiento));


                foreach ($ccp as $key => $value) {
                    if ($count === 0) {
                        $ccpHtml .= htmlspecialchars($value->nombre) . '. ' . htmlspecialchars($value->cargo) . '. Para su conocimiento. <br>';
                    } elseif (
                        !str_contains($value->cargo, 'DIRECTOR') &&
                        !str_contains($value->cargo, 'DIRECTORA') &&
                        !str_contains($value->cargo, 'ENCARGADO DE LA UNIDAD') &&
                        !str_contains($value->cargo, 'ENCARGADA DE LA UNIDAD')
                    ) {
                        if ($key == 1) {
                            $ccpHtml .= 'Archivo / Minutario. <br>';
                        }
                        $ccpHtml .= htmlspecialchars($value->nombre) . '. ' . htmlspecialchars($value->cargo) . '. Mismo fin. <br>';
                    }
                    $count++;
                }

                foreach ($ccp as $v) {
                    foreach ($validadores as $validador) {
                        if (str_contains($v->cargo, $validador)) {
                            $ccpValidador .= 'Validó: ' . htmlspecialchars($v->nombre) . '. ' . htmlspecialchars($v->cargo) . '. <br>';
                            break;
                        }
                    }
                }

                foreach ($ccpDelegado as $ke => $val) {
                    if (!$bandera) {
                        if (str_contains($val->cargo, 'DELEGADO') || str_contains($val->cargo, 'DELEGADA')) {
                            $elaboroHtml .= 'Elaboró: '.htmlspecialchars($val->nombre).'. '.htmlspecialchars($val->cargo).'. <br>';
                            $bandera = true;
                        } elseif (
                            str_contains($val->cargo, 'DIRECTOR') ||
                            str_contains($val->cargo, 'DIRECTORA') ||
                            str_contains($val->cargo, 'ENCARGADO DE LA UNIDAD') ||
                            str_contains($val->cargo, 'ENCARGADA DE LA UNIDAD')
                        ) {
                            $elaboroHtml .= 'Elaboró: '.htmlspecialchars($val->nombre).'. '.htmlspecialchars($val->cargo).'. <br>';
                            $bandera = true;
                        }
                    }
                }

                foreach ($movimiento as $v) {
                    $depositos = json_decode($v['depositos'] ?? '[]', true);
                    foreach ($depositos as $j) {
                        $foliosDepositos[] = htmlspecialchars($j['folio']);
                    }
                }

                if (isset($distintivo)) {
                    if (!is_array($distintivo)) {
                        $distintivo = explode('*', $distintivo);
                    }

                    $leyenda .= '<small>';
                    foreach ($distintivo as $keys => $part) {
                        if ($keys != 0) {
                            $leyenda .= '<br>';
                        }
                        $leyenda .= htmlspecialchars($part);
                    }
                    $leyenda .= '</small>';
                }

                $fichas = implode(', ', $foliosDepositos);


                foreach ($movimiento as $item) {
                    $depositos = json_decode($item['depositos'] ?? '[]', true);
                    $foliosDeposito = [];

                    foreach ($depositos as $k) {
                        $counter++;
                        $foliosDeposito[] = $k['folio'];
                    }

                    // Agrupar por cada 3 con salto de línea
                    $foliosAgrupados = array_chunk($foliosDeposito, 3);
                    $foliosHTML = implode('<br>', array_map(function($chunk) {
                        return implode(', ', $chunk);
                    }, $foliosAgrupados));

                    $conceptoTexto = ($item['concepto'] === 'CURSO DE CAPACITACIÓN O CERTIFICACIÓN')
                        ? htmlspecialchars($item['curso'])
                        : htmlspecialchars($item['concepto']);

                    $importeTotal += $item['importe'];
                    $importeTexto = number_format($item['importe'], 2, '.', ',');

                    $tbodyHTML .= '<tr>';
                    $tbodyHTML .= '<td style="text-align: center;">' . $item['folio'] . '</td>';
                    $tbodyHTML .= '<td style="text-align: center;">' . $foliosHTML . '</td>';
                    $tbodyHTML .= '<td style="text-align: left; font-size: 9px;">' . $conceptoTexto . '</td>';
                    $tbodyHTML .= '<td style="text-align: center;">$ ' . $importeTexto . '</td>';
                    $tbodyHTML .= '</tr>';
                }

                foreach ($direccion as $point => $ari) {
                    if ($point != 0) {
                        $direccionHtml .= '<br>';
                    }
                    $direccionHtml .= htmlspecialchars($ari);
                }

                // Total al final
                $totalTexto = number_format($importeTotal, 2, '.', ',');

                $tbodyHTML .= '<tr>';
                $tbodyHTML .= '<td></td>';
                $tbodyHTML .= '<td></td>';
                $tbodyHTML .= '<td style="text-align:right;"><b>TOTAL</b></td>';
                $tbodyHTML .= '<td style="text-align:center;"><b>$ ' . $totalTexto . '</b></td>';
                $tbodyHTML .= '</tr>';

                //TODO: arreglo dinámico, tiene que ajustarce a las necesidades del documento a generar
                $variableArray = [
                    'unidad' => strtoupper($dataunidades->ubicacion),
                    'memo'  => htmlspecialchars($rfgetData->memorandum),
                    'fecha' => $fechaFormateada,
                    'mun' => $municipio,
                    'tit' => htmlspecialchars(strtoupper($dirigido->titulo)),
                    'nom' => htmlspecialchars(strtoupper($dirigido->nombre)),
                    'car' => htmlspecialchars($dirigido->cargo),
                    'intervalo' => $intervalo,
                    'importe' => number_format($importeMemo, 2, '.', ','),
                    'letra' => $importeLetra,
                    'ccpHtml' => $ccpHtml,
                    'ccpValidador' => $ccpValidador,
                    'elaboroHtml' => $elaboroHtml,
                    'cuentaTexto' => htmlspecialchars($cuenta),
                    'elaboracion' => $creado,
                    'periodoTexto' => $periodoTexto,
                    'fObservacion' => $fechaObs,
                    'recibos' => $recibos,
                    'fichas' => $fichas,
                    'dinamico' => $tbodyHTML,
                    'leyenda' => $leyenda,
                    'direccion' => $direccionHtml,
                ];

                $contenidoProcesado = $this->servicioPlantilla->procesarPlantilla($dataQry->cuerpo, $variableArray);
                $pdf = $this->servicioPlantilla->generarPdfDocument(['contenido' => $contenidoProcesado]);
                return $pdf->stream('concentreado_de_ingresos_rf001_'.$rfgetData->memorandum.'.pdf');
                break;
            case 'CONTRATO':
                $id_contrato = 22713;
                $params = ['table' => 'contratos',
                    'select' => [
                        'tbl_unidades.*',
                        'tbl_cursos.clave',
                        'tbl_cursos.nombre',
                        'tbl_cursos.curp',
                        'instructores.correo',
                        'contratos.numero_contrato'
                    ],
                    'joins' => [
                        ['table' => 'folios', 'first' => 'folios.id_folios', 'second' => 'contratos.id_folios'],
                        ['table' => 'tabla_supre', 'first' => 'tabla_supre.id', 'second' => 'folios.id_supre'],
                        ['table' => 'tbl_unidades', 'first' => 'tbl_unidades.unidad', 'second' => 'tabla_supre.unidad_capacitacion'],
                        ['table' => 'tbl_cursos', 'first' => 'tbl_cursos.id', 'second' => 'folios.id_cursos'],
                        ['table' => 'instructores', 'first' => 'instructores.id', 'second' => 'tbl_cursos.id_instructor']
                    ],
                    'where' => [
                        ['column' => 'contratos.id_contrato', 'value' => $id_contrato]
                    ],
                    'first' => true
                ];
                $info = $this->servicioPlantilla->consultaDinamica($params);
                $nameFileOriginal = 'contrato '.$info->clave.'.pdf';
                $numDocsParam = [
                    'where' => [
                        ['column' => 'tipo_archivo', 'value' => 'Contrato'],
                        ['column' => 'numero_o_clave', 'value' => $info->clave]
                    ],
                    'whereIn' => [
                        ['column' => 'status', 'values' => ['CANCELADO', 'CANCELADO ICTI']]
                    ],
                    'count' => true
                ];
                $numDocs = $this->servicioPlantilla->obtenermultiplesCondiciones('DocumentosFirmar', $numDocsParam);
                $numDocs = '0'.($numDocs+1);
                $numOficioBuilder = explode('/',$info->numero_contrato);
                $position = count($numOficioBuilder) - 2;
                array_splice($numOficioBuilder, $position, 0, $numDocs);
                $numOficio = implode('/',$numOficioBuilder);
                break;
            default:
                # code...
                break;
        }
    }
}
