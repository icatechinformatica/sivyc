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
use Illuminate\Support\Facades\DB;

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
    public function index(): JsonResponse | null
    {
        // TODO: MEJORAR CONSULTA Y AGREGAR A PDF
        // $plantillas = $this->servicioPlantilla->obtenerPlantillas();
        return null;
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

    public function loadFile($id)
    {
        $objEplantilla = ['id', 'tipo', 'cuerpo', 'vigencia'];
        $dataQry = $this->servicioPlantilla->getPlantilla($id, 'Plantillas\EPlantilla', $objEplantilla, 'id'); // llamada del servicio con el metodo obtener plantilla parametro con el id y el nombre del modelo
        #TODO: preferible pasar el parametro desde el controlador para no procesar en la capa de datos - Modificar procesos
        switch ($dataQry->tipo) {
            case 'RF001':
                #TODO: cada caso servirá para procesar contenido exclusivo del archivo deseado a cargar
                $arraySelect = [ 'id', 'memorandum', 'estado', 'movimientos', 'id_unidad', 'envia', 'dirigido', 'archivos', 'unidad', 'periodo_inicio', 'periodo_fin',  'realiza', 'movimiento', 'tipo', 'confirmed', 'created_at' ];

                $rfgetData = $this->servicioPlantilla->getPlantilla(22, 'Reportes\Rf001Model', $arraySelect, 'id');
               // Obtener datos básicos
                $basicData = $this->getRf001Data(Auth::user(), $rfgetData);

                // Procesar movimientos
                $movimiento = json_decode($rfgetData->movimientos, true);
                $movementData = $this->processMovements($movimiento);

                // Generar CCPs
                $ccpData = $this->generateCcps($rfgetData);

                // Procesar fechas
                $dateData = $this->processDates($rfgetData);

                // Procesar dirección
                $direccion = $basicData['dataunidades']->direccion;
                $direccionHtml = '';
                if (!is_array($direccion)) {
                    $direccion = explode('*', $direccion);
                }
                foreach ($direccion as $point => $ari) {
                    if ($point != 0) {
                        $direccionHtml .= '<br>';
                    }
                    $direccionHtml .= htmlspecialchars($ari);
                }

                // Procesar cuenta bancaria
                $cuentas_bancarias = json_decode($basicData['instituto']->cuentas_bancarias, true);
                $cuenta = $cuentas_bancarias[$basicData['dataunidades']->ubicacion]['BBVA'];

                // Procesar leyenda
                $leyenda = '';
                if (isset($basicData['distintivo'])) {
                    if (!is_array($basicData['distintivo'])) {
                        $distintivo = explode('*', $basicData['distintivo']);
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

                // Importe en letras
                $importeLetra = $this->servicioPlantilla->letras($movementData['importeMemo']);

                // Arreglo de variables para la plantilla
                $variableArray = [
                    'unidad' => strtoupper($basicData['dataunidades']->ubicacion),
                    'memo'  => htmlspecialchars($rfgetData->memorandum),
                    'fecha' => $dateData['fechaFormateada'],
                    'mun' => mb_strtoupper($basicData['dataunidades']->municipio, 'UTF-8'),
                    'tit' => htmlspecialchars(strtoupper($basicData['dirigido']->titulo)),
                    'nom' => htmlspecialchars(strtoupper($basicData['dirigido']->nombre)),
                    'car' => htmlspecialchars($basicData['dirigido']->cargo),
                    'intervalo' => $dateData['intervalo'],
                    'importe' => number_format($movementData['importeMemo'], 2, '.', ','),
                    'letra' => $importeLetra,
                    'ccpHtml' => $ccpData['ccpHtml'],
                    'ccpValidador' => $ccpData['ccpValidador'],
                    'elaboroHtml' => $ccpData['elaboroHtml'],
                    'cuentaTexto' => htmlspecialchars($cuenta),
                    'elaboracion' => $dateData['creado'],
                    'periodoTexto' => $dateData['periodoTexto'],
                    'fObservacion' => $dateData['fechaObs'],
                    'recibos' => $movementData['recibos'],
                    'fichas' => $movementData['fichas'],
                    'dinamico' => $movementData['tbodyHTML'],
                    'leyenda' => $leyenda,
                    'direccion' => $direccionHtml,
                ];

                $contenidoProcesado = $this->servicioPlantilla->procesarPlantilla($dataQry->cuerpo, $variableArray);
                $pdf = $this->servicioPlantilla->generarPdfDocument(['contenido' => $contenidoProcesado]);
                $filename = 'concentreado_de_ingresos_rf001_'. $rfgetData->memorandum .'.pdf';
                $filename = str_replace(['/', '\\'], '_', $filename);

                return $pdf->stream($filename);
            break;
            case 'CONTRATO':
                $id_contrato = 20912;
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
                $contratoParam = [
                    'table' => 'contratos',
                    'select' => [
                        'id_contrato','numero_contrato','cantidad_letras1','fecha_firma','municipio',
                        'id_folios','instructor_perfilid','unidad_capacitacion','docs','observacion','cantidad_numero','arch_factura','arch_factura_xml',
                        'fecha_status','chk_rechazado','fecha_rechazo','arch_contrato','folio_fiscal','id_curso'
                    ],
                    'where' => [
                        ['column' => 'contratos.id_contrato', 'value' => $id_contrato]
                    ],
                    'first' => true
                ];
                $dataContrato = $this->servicioPlantilla->consultaDinamica($contratoParam);
                $paramsData = [
                    'table' => 'contratos',
                    'select' => [
                        'folios.id_folios','folios.importe_total','tbl_cursos.id','tbl_cursos.horas','tbl_cursos.fecha_apertura','tbl_cursos.soportes_instructor',
                        'tbl_cursos.tipo_curso','tbl_cursos.espe', 'tbl_cursos.clave','instructores.nombre','instructores.apellidoPaterno',
                        'instructores.apellidoMaterno','tbl_cursos.instructor_tipo_identificacion','tbl_cursos.instructor_folio_identificacion','instructores.rfc','tbl_cursos.modinstructor',
                        'instructores.curp','instructores.domicilio','tabla_supre.fecha_validacion'
                    ],
                    'joins' => [
                        ['table' => 'folios', 'first' => 'folios.id_folios', 'second' => 'contratos.id_folios'],
                        ['table' => 'tabla_supre', 'first' => 'tabla_supre.id', 'second' => 'folios.id_supre'],
                        ['table' => 'tbl_cursos', 'first' => 'tbl_cursos.id', 'second' => 'folios.id_cursos'],
                        ['table' => 'instructores', 'first' => 'instructores.id', 'second' => 'tbl_cursos.id_instructor']
                    ],
                    'where' => [
                        ['column' => 'folios.id_folios', 'value' => $dataContrato->id_folios]
                    ],
                    'first' => true
                ];
                $dataQuery = $this->servicioPlantilla->consultaDinamica($paramsData);
                // $especialidadParam = [
                //     'table' => 'especialidad_instructores',
                //     'select' => [
                //         'especialidades.nombre'
                //     ],
                //     'joins' => [
                //         ['table' => 'especialidades', 'first' => 'especialidades.id', 'second' => 'especialidad_instructores.especialidad_id'],
                //     ],
                //     'where' => [
                //         ['column' => 'especialidad_instructores.id', 'value' => $dataContrato->instructor_perfilid]
                //     ],
                //     'first' => true
                // ];
                // $especialidad = $this->servicioPlantilla->consultaDinamica($especialidadParam);
                $fecha_act = new Carbon('23-06-2022');
                $fecha_fir = new Carbon($dataContrato->fecha_firma);
                $nombreInstructor = $dataQuery->nombre . ' ' . $dataQuery->apellidoPaterno . ' ' . $dataQuery->apellidoMaterno;
                $date = strtotime($dataContrato->fecha_firma);
                $D = date('d', $date);
                $M = $this->servicioPlantilla->paraMes(date('m', $date));
                $Y = date("Y", $date);
                $direccion_instituto = DB::Table('tbl_instituto')->Where('id',1)->Value('direccion');
                $direccion_instituto = str_replace('*',' ',$direccion_instituto);
                $direccion_instituto = mb_strtoupper(str_replace('. Tuxtla',', en la ciudad de Tuxtla',$direccion_instituto), 'UTF-8');
                $cantidad = $this->servicioPlantilla->formatoNumero($dataContrato->cantidad_numero);
                $monto = explode(".",strval($dataContrato->cantidad_numero));
                // obtencion de tipo de identificaion y folio dependiendo si esta en el json o en el capo a parte
                $dataQuery->soportes_instructor = json_decode($dataQuery->soportes_instructor);
                if(isset($dataQuery->soportes_instructor->tipo_identificacion)) {
                    $tipo_identificacion =$dataQuery->soportes_instructor->tipo_identificacion;
                    $folio_identificacion = $dataQuery->soportes_instructor->folio_identificacion;
                } else {
                    $tipo_identificacion = $dataQuery->instructor_tipo_identificacion;
                    $folio_identificacion = $dataQuery->instructor_folio_identificacion;
                }
                $loadArray = [
                    'no_contrato' => $dataContrato->numero_contrato,
                    'titular_uc' => $info->dunidad,
                    'cargo_titular_uc' => $info->pdunidad,
                    'instructor' => $nombreInstructor,
                    'cargo_dg' => $info->pdgeneral,
                    'director_general' => $info->dgeneral,
                    'gobernador' => 'DR. EDUARDO RAMÍREZ AGUILAR',
                    'fecha_nom_dg' => '16 de enero de 2019',
                    'espe_instructor' => $dataQuery->espe,
                    'regimen_instructor' => 'SUELDOS Y SALARIOS E INGRESOS '.$dataQuery->modinstructor,
                    'clave_grupo' => $dataQuery->clave,
                    'tipo_identif_instructor' => $dataQuery->instructor_tipo_identificacion,
                    'folio_identif_instructor' => $dataQuery->instructor_folio_identificacion,
                    'rfc_instructor' => $dataQuery->rfc,
                    'domicilio_instructor' => $dataQuery->domicilio,
                    'importe_monto' => $cantidad,
                    'importeMontoLetra' => $dataContrato->cantidad_letras1.' '. $monto[1].'/100 M.N.',
                    'municipio' => $info->municipio,
                ];
                $contenidoProcesado = $this->servicioPlantilla->procesarPlantilla($dataQry->cuerpo, $loadArray);
                $pdf = $this->servicioPlantilla->generarPdfDocument(['contenido' => $contenidoProcesado]);
                $filename = 'contrato_instrcutor_externo_' . $dataContrato->numero_contrato . '.pdf';

                // Reemplaza cualquier / o \ por guion bajo (o espacio u otro carácter válido)
                $filename = str_replace(['/', '\\'], '_', $filename);

                return $pdf->stream($filename);
                break;
            case 'SUPRE':
                // Simulación de $data_supre (objeto con propiedades)
                $data_supre = (object)[
                    'id' => 1,
                    'no_memo' => 'MEMO-2025-001',
                    'fecha' => '2025-07-16',
                    'unidad_capacitacion' => 'TUXTLA',
                ];

                // Simulación de $data (arreglo de objetos)
                $data = [
                    (object)[
                        'fecha' => '2025-07-10',
                        'folio_validacion' => 'FOLIO-2025-001',
                        'importe_hora' => 250.00,
                        'iva' => 40.00,
                        'importe_total' => 290.00,
                        'comentario' => 'Sin observaciones',
                        'nombre' => 'Juan',
                        'apellidoPaterno' => 'Pérez',
                        'apellidoMaterno' => 'García',
                        'unidad' => 'TUXTLA',
                        'curso_nombre' => 'Excel Básico',
                        'clave' => 'EXC-001',
                        'ze' => 'I',
                        'dura' => 20,
                        'tipo_curso' => 'CURSO',
                        'modinstructor' => 'HONORARIOS',
                        'fecha_apertura' => '2025-07-01',
                        'cp' => 6
                    ],
                    (object)[
                        'fecha' => '2025-07-12',
                        'folio_validacion' => 'FOLIO-2025-002',
                        'importe_hora' => 300.00,
                        'iva' => 48.00,
                        'importe_total' => 348.00,
                        'comentario' => 'Pago pendiente',
                        'nombre' => 'María',
                        'apellidoPaterno' => 'López',
                        'apellidoMaterno' => 'Hernández',
                        'unidad' => 'TUXTLA',
                        'curso_nombre' => 'Word Avanzado',
                        'clave' => 'WRD-002',
                        'ze' => 'II',
                        'dura' => 30,
                        'tipo_curso' => 'CERTIFICACION',
                        'modinstructor' => 'ASIMILADOS',
                        'fecha_apertura' => '2025-07-05',
                        'cp' => 7
                    ],
                ];

                // Simulación de otros datos necesarios
                $unidad = (object)[
                    'ubicacion' => 'TUXTLA',
                    'cct' => '07EI'
                ];

                $funcionarios = [
                    'destino' => 'LIC. ALGUIEN IMPORTANTE',
                    'destinop' => 'JEFE DE DEPARTAMENTO DE FINANZAS'
                ];

                $numOficio = null;
                $D = '16';
                $M = 'julio';
                $Y = '2025';

                // Generar las filas de folio
                $data_folio = [
                    (object)[ 'folio_validacion' => 'FOLIO-2025-001' ],
                    (object)[ 'folio_validacion' => 'FOLIO-2025-002' ],
                    (object)[ 'folio_validacion' => 'FOLIO-2025-003' ],
                    (object)[ 'folio_validacion' => 'FOLIO-2025-004' ],
                ];

                $filas_folio = '';
                foreach ($data_folio as $value) {
                    $filas_folio .= '<tr><td>' . $value->folio_validacion . '</td></tr>';
                }

                // Generar las filas de la tabla principal
                $filas_tabla = '';
                foreach ($data as $item) {
                    $filas_tabla .= '<tr>
                        <td class="text-center"><small style="font-size: 10px;">' . $item->folio_validacion . '</small></td>
                        <td class="text-center"><small style="font-size: 10px;">' . $item->fecha . '</small></td>
                        <td class="text-center"><small style="font-size: 10px;">' . $item->nombre . ' ' . $item->apellidoPaterno . ' ' . $item->apellidoMaterno . '</small></td>
                        <td class="text-center"><small style="font-size: 10px;">' . $item->unidad . '</small></td>';

                    $filas_tabla .= '<td><small style="font-size: 10px;">' . ($item->tipo_curso == 'CERTIFICACION' ? 'CERTIFICACIÓN' : 'CURSO') . '</small></td>';

                    $filas_tabla .= '<td class="text-center"><small style="font-size: 10px;">' . $item->curso_nombre . '</small></td>
                        <td class="text-center"><small style="font-size: 10px;">' . $item->clave . '</small></td>
                        <td class="text-center"><small style="font-size: 10px;">' . $item->ze . '</small></td>
                        <td class="text-center"><small style="font-size: 10px;">' . $item->dura . '</small></td>';

                    if ($data[0]->fecha_apertura < '2023-10-12') {
                        $filas_tabla .= '<td class="text-center"><small style="font-size: 10px;">' . number_format($item->importe_hora, 2, '.', ',') . '</small></td>';
                        if ($item->modinstructor == 'HONORARIOS') {
                            $filas_tabla .= '<td class="text-center"><small style="font-size: 10px;">' . number_format($item->iva, 2, '.', ',') . '</small></td>';
                        }
                        $filas_tabla .= '<td class="text-center"><small style="font-size: 10px;">' .
                            ($item->modinstructor == 'HONORARIOS' || $item->modinstructor == 'HONORARIOS Y ASIMILADOS A SALARIOS'
                                ? '12101 HONORARIOS'
                                : '12101 ASIMILADOS A SALARIOS') .
                            '</small></td>';
                        $filas_tabla .= '<td class="text-center"><small style="font-size: 10px;">' . number_format($item->importe_total, 2, '.', ',') . '</small></td>';
                    } else {
                        $criterio = (object)['monto' => $item->importe_hora]; // Ajusta si hay otro origen
                        $filas_tabla .= '<td class="text-center"><small style="font-size: 10px;">' . number_format($criterio->monto, 2, '.', ',') . '</small></td>';
                        $filas_tabla .= '<td class="text-center"><small style="font-size: 10px;">' . number_format($item->importe_total, 2, '.', ',') . '</small></td>';
                        $filas_tabla .= '<td class="text-center"><small style="font-size: 10px;">' .
                            ($item->modinstructor == 'HONORARIOS' || $item->modinstructor == 'HONORARIOS Y ASIMILADOS A SALARIOS'
                                ? '12101 HONORARIOS'
                                : '12101 ASIMILADOS A SALARIOS') .
                            '</small></td>';
                    }

                    $filas_tabla .= '<td class="text-center"><small style="font-size: 10px;">' . $item->comentario . '</small></td>
                    </tr>';
                }

                $ubicacion = $unidad->ubicacion;
                $numOficio = $data_supre->no_memo;
                $unidadCapacitacion = $data_supre->unidad_capacitacion;
                $fechaFormato = $D . ' de ' . $M . ' del ' . $Y;
                $capacitacionAccionMovil = $unidad->cct == '07EI'
                    ? 'Unidad de Capacitación <b>' . $unidad->ubicacion . '</b>,'
                    : 'Acción Móvil <b>' . $data_supre->unidad_capacitacion . '</b>,';
                $modinstructor = $data[0]->modinstructor;
                $tipoCursoTexto = $data[0]->tipo_curso == 'CERTIFICACION' ? ' certificación extraordinaria' : ' curso';
                $fechaApertura = $data[0]->fecha_apertura;

                $columnas = '';

                if ($data[0]->fecha_apertura < '2023-10-12') {
                    $columnas .= '<td><small style="font-size: 10px;">IMPORTE POR HORA</small></td>';
                    if ($data[0]->modinstructor == 'HONORARIOS'){
                        $columnas .= '<td><small style="font-size: 10px;">IVA 16%</small></td>';
                    }
                    $columnas .= '<td><small style="font-size: 10px;">PARTIDA/ CONCEPTO</small></td>
                    <td><small style="font-size: 10px;">IMPORTE</small></td>';
                } else {
                    $columnas .= '<td><small style="font-size: 10px;">COSTO POR HORA</small></td>
                    <td><small style="font-size: 10px;">TOTAL IMPORTE</small></td>
                    <td><small style="font-size: 10px;">PARTIDA/ CONCEPTO</small></td>';
                }

                $unidad = DB::table('tbl_unidades')->SELECT('tbl_unidades.unidad', 'tbl_unidades.cct','tbl_unidades.ubicacion','direccion')
                            ->WHERE('unidad', '=', $data_supre->unidad_capacitacion)
                            ->FIRST();
                $unidad->cct = substr($unidad->cct, 0, 4);
                $direccion = explode("*", $unidad->direccion);

                $direccionFormateada = collect(is_array($direccion) ? $direccion : explode('*', $direccion))->implode('<br>');
                $distintivo = DB::table('tbl_instituto')->value('distintivo'); // más claro que pluck()->first()

                $textoFormateado = '';

                $contenido = null;

                if (isset($leyenda)) {
                    $contenido = is_array($leyenda) ? $leyenda : explode('*', $leyenda);
                } elseif (isset($distintivo)) {
                    $contenido = is_array($distintivo) ? $distintivo : explode('*', $distintivo);
                }

                if ($contenido) {
                    foreach ($contenido as $linea) {
                        $textoFormateado .= '<div>' . htmlspecialchars(trim($linea), ENT_QUOTES, 'UTF-8') . '</div>';
                    }
                }



                $loadArray = [
                    'distintivo' => $distintivo,
                    'ubicacion' => $unidad->ubicacion,
                    'numOficio' => $numOficio,
                    'unidadCapacitacion' => $data_supre->unidad_capacitacion,
                    'fechaFormato' => $fechaFormato,
                    'destino' => $funcionarios['destino'],
                    'destinop' => $funcionarios['destinop'],
                    'modinstructor' => $modinstructor,
                    'tipoCursoTexto' => $tipoCursoTexto,
                    'capacitacionAccionMovil' => $capacitacionAccionMovil,
                    'folios' => $filas_folio,
                    'filasTabla' => $filas_tabla,
                    'columnas' => $columnas,
                    'title' => 'Solicitud de Suficiencia Presupuestal',
                    'direccionFormateada' => $direccionFormateada,
                    'contenido_html' => $textoFormateado,
                ];

                $contenidoProcesado = $this->servicioPlantilla->procesarPlantilla($dataQry->cuerpo, $loadArray);
                $pdf = $this->servicioPlantilla->generarPdfDocument(['contenido' => $contenidoProcesado]);
                $filename = 'solicitud_de_suficiencia_presupuestal' . $numOficio . '.pdf';

                // Reemplaza cualquier / o \ por guion bajo (o espacio u otro carácter válido)
                $filename = str_replace(['/', '\\'], '_', $filename);

                return $pdf->stream($filename);
                break;
            default:
                # code...
                break;
        }
    }

    // tentativamente se cambiaran a otros archivos y cambios de variables
    private function getRf001Data($auth, $rfgetData){
        $organismo = $auth->id_organismo;
        $dataunidades = DB::table('tbl_unidades')
            ->where('unidad', $rfgetData->unidad)
            ->first();

        $dirigido = DB::table('tbl_funcionarios')
            ->where('id', 114)
            ->first();

        $instituto = DB::table('tbl_instituto')->first();
        $distintivo = DB::table('tbl_instituto')->value('distintivo');

        return [
            'dataunidades' => $dataunidades,
            'dirigido' => $dirigido,
            'instituto' => $instituto,
            'distintivo' => $distintivo,
            'organismo' => $organismo
        ];
    }

    private function processMovements($movimiento){
        $importeMemo = 0;
        $foliosDepositos = [];
        $tbodyHTML = '';
        $importeTotal = 0;
        $counter = 0;
        $recibos = [];

        if (is_array($movimiento)) {
            foreach ($movimiento as $key) {
                $importeMemo += $key['importe'] ?? 0;
            }

            // Ordenar movimientos
            usort($movimiento, function($a, $b) {
                preg_match('/\d+/', $a['folio'], $matchA);
                preg_match('/\d+/', $b['folio'], $matchB);
                $numA = isset($matchA[0]) ? (int) $matchA[0] : 0;
                $numB = isset($matchB[0]) ? (int) $matchB[0] : 0;
                return $numA <=> $numB;
            });

            // Procesar cada movimiento
            foreach ($movimiento as $item) {
                $depositos = json_decode($item['depositos'] ?? '[]', true);
                $foliosDeposito = [];
                $recibos[] = htmlspecialchars($item['folio']);

                foreach ($depositos as $k) {
                    $counter++;
                    $foliosDeposito[] = $k['folio'];
                    $foliosDepositos[] = htmlspecialchars($k['folio']);
                }

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

            // Agregar total
            $totalTexto = number_format($importeTotal, 2, '.', ',');
            $tbodyHTML .= '<tr>';
            $tbodyHTML .= '<td></td>';
            $tbodyHTML .= '<td></td>';
            $tbodyHTML .= '<td style="text-align:right;"><b>TOTAL</b></td>';
            $tbodyHTML .= '<td style="text-align:center;"><b>$ ' . $totalTexto . '</b></td>';
            $tbodyHTML .= '</tr>';
        }

        return [
            'importeMemo' => $importeMemo,
            'foliosDepositos' => $foliosDepositos,
            'tbodyHTML' => $tbodyHTML,
            'recibos' => implode(', ', $recibos),
            'fichas' => implode(', ', $foliosDepositos)
        ];
    }

    private function generateCcps($rfgetData){
        $ccp = $this->servicioPlantilla->setCpp($rfgetData->id_unidad);
        $ccpDelegado = $this->servicioPlantilla->setFuncionarios($rfgetData->id_unidad);

        $ccpHtml = '';
        $ccpValidador = '';
        $elaboroHtml = '';
        $count = 0;
        $bandera = false;
        $validadores = ['DIRECTOR', 'DIRECTORA', 'ENCARGADO DE LA UNIDAD', 'ENCARGADA DE LA UNIDAD'];

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

        return [
            'ccpHtml' => $ccpHtml,
            'ccpValidador' => $ccpValidador,
            'elaboroHtml' => $elaboroHtml
        ];
    }

    private function processDates($rfgetData){
        $fechaActual = $rfgetData->created_at->format('Y-m-d');
        $fechaFormateada = $this->servicioPlantilla->formatoFechaCrearMemo($fechaActual);
        $intervalo = $this->servicioPlantilla->formatoIntervaloFecha($rfgetData->periodo_inicio, $rfgetData->periodo_fin);

        $creado = htmlspecialchars(Carbon::parse($rfgetData->created_at)->format('d/m/Y'));

        $fechaInicio = new \DateTime($rfgetData->periodo_inicio);
        $fechaFin = new \DateTime($rfgetData->periodo_fin);
        $periodoTexto = htmlspecialchars($fechaInicio->format('d/m/Y')) . ' AL ' . htmlspecialchars($fechaFin->format('d/m/Y'));

        $dateCreacion = Carbon::parse($rfgetData->created_at);
        $dateCreacion->locale('es');
        $nombreMesCreacion = $dateCreacion->translatedFormat('F');
        $fechaObs = $dateCreacion->day . "/" . Str::upper($nombreMesCreacion) . "/" . $dateCreacion->year;

        return [
            'fechaFormateada' => $fechaFormateada,
            'intervalo' => $intervalo,
            'creado' => $creado,
            'periodoTexto' => $periodoTexto,
            'fechaObs' => $fechaObs
        ];
    }
}
