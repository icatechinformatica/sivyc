<?php

namespace App\Services;
use PDF;
use App\Models\Reportes\Rf001Model;
use App\Models\Unidad;
use Carbon\Carbon;
use Spatie\ArrayToXml\ArrayToXml;
use App\Models\Tokens_icti;
use Illuminate\Support\Facades\Http;
use App\Models\DocumentosFirmar;
use Illuminate\Support\Facades\View;
use App\Models\tbl_unidades;
use Illuminate\Support\Facades\DB;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Str;
use App\Utilities\MyUtility;
use App\Models\Reportes\Recibo;

class ReportService
{
    public function __construct()
    {
        // setear los datos
    }

    public function getReport($distintivo, $organismo, $id, $nombreElaboro, $puestoElaboro)
    {
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $rf001Detalle = new Rf001Model();
        $rf001 = $rf001Detalle::findOrFail($id);
        $data = \DB::table('tbl_unidades')->where('unidad', $rf001->unidad)->first();
        $unidad = strtoupper($data->ubicacion);
        $municipio = mb_strtoupper($data->municipio, 'UTF-8');
        #OBTENEMOS LA FECHA ACTUAL
        $fechaActual = getdate();
        $anio = $fechaActual['year']; $mes = $fechaActual['mon']; $dia = $fechaActual['mday'];
        $dia = ($dia < 10) ? '0'.$dia : $dia;

        $fecha_comp = $dia.' de '.$meses[$mes-1].' del '.$anio;
        $dirigido = \DB::table('tbl_funcionarios')->where('id', 12)->first();
        $conocimiento = \DB::table('tbl_funcionarios')
            ->leftjoin('tbl_organismos', 'tbl_organismos.id', '=', 'tbl_funcionarios.id_org')
            ->where('tbl_organismos.id', 13)
            ->select('tbl_organismos.nombre', 'tbl_funcionarios.nombre as nombre_funcionario', 'tbl_funcionarios.cargo', 'tbl_funcionarios.titulo')
            ->first();
        $direccion = $data->direccion;

        $delegado = \DB::Table('tbl_organismos AS o')->Select('f.nombre','f.cargo')
            ->Join('tbl_funcionarios AS f', 'f.id_org', 'o.id')
            ->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('f.activo', 'true')
            ->Where('o.nombre','LIKE','DELEG%')
            ->Where('u.unidad', $rf001->unidad)
            ->First();

        return PDF::loadView('reportes.rf001.reporterf001', compact('distintivo', 'organismo', 'data', 'unidad', 'rf001', 'municipio', 'fecha_comp', 'dirigido', 'direccion', 'conocimiento', 'nombreElaboro', 'puestoElaboro', 'delegado'))->setPaper('a4', 'portrait')->output();
    }

    public function xmlFormat($id, $organismo, $unidad, $usuario)
    {
        $htmlBody = array();
        $rf001 = (new Rf001Model())->findOrFail($id); // obtener RF001 por id
        // checar si el documento se encuentra en la tabla documentos_firmar
        $documentoFirmar = DocumentosFirmar::Where('numero_o_clave', $rf001->memorandum)->First();
        if ($documentoFirmar) {
            # TODO: se encuentra se tiene que eliminar y por ende volver a generar
            $documentoFirmar->delete();
        }

        $distintivo = \DB::table('tbl_instituto')->value('distintivo'); #texto de encabezado del pdf
        // elaboro y puesto de elaboración
        $nombreElaboro = $usuario->name;
        $puestoElaboro = $usuario->puesto;

        $organismoPublico = \DB::table('organismos_publicos')->select('nombre_titular', 'cargo_fun')->where('id', '=', $organismo)->first();

        $body = $this->createBodyToXml($rf001, $unidad, $organismoPublico);

        if (is_null($body)) {
            $error = ['error' => 1];
            return $error;
        }

        $ubicacion = Unidad::where('id', $unidad)->value('ubicacion');

        $movimiento = json_decode($rf001->movimientos, true);

        foreach ($movimiento as $item) {
            # ciclo para actualizar el estado de todos los registros
            Recibo::where('folio_recibo', '=', $item['folio'])
                ->update([
                    'estado_reportado' => 'CONCENTRADO'
                ]);
        }

        $firmantes = $this->funcionariosUnidades($ubicacion);
        list($firmanteNoUno, $firmanteNoDos) = $firmantes;

        $firmanteFinanciero = $this->getFirmanteFinanciero($rf001->id_unidad);

        $dataFirmantes = \DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre','fun.incapacidad')
            ->Join('tbl_funcionarios AS fun','fun.id_org','org.id')
            ->Join('tbl_unidades AS u', 'u.id', 'org.id_unidad')
            ->Where('org.id_parent',1)
            ->Where('fun.activo', 'true')
            ->Where('u.unidad', $ubicacion)
            ->First();


        $nameFileOriginal = 'concentrado '.$rf001->memorandum.'.pdf';
        $numOficio = "concentrado-".$rf001->memorandum;
        $numFirmantes = '3'; // 1 o 2

        $arrayFirmantes = [];
        // director
        $temp = ['_attributes' =>
            [
                'curp_firmante' => $firmanteNoUno['curp'],
                'nombre_firmante' => $firmanteNoUno['funcionario'],
                'email_firmante' => $firmanteNoUno['correo'],
                'tipo_firmante' => 'FM',
            ]
        ];
        array_push($arrayFirmantes, $temp);

        // delegado
        $temp = ['_attributes' =>
            [
                'curp_firmante' => $firmanteNoDos['curp'],
                'nombre_firmante' => $firmanteNoDos['funcionario'],
                'email_firmante' => $firmanteNoDos['correo'],
                'tipo_firmante' => 'FM'
            ]
        ];

        array_push($arrayFirmantes, $temp);

        $temp = ['_attributes' =>
            [
                'curp_firmante' => $firmanteFinanciero['curp'],
                'nombre_firmante' => $firmanteFinanciero['funcionario'],
                'email_firmante' => $firmanteFinanciero['correo'],
                'tipo_firmante' => 'FM'
            ]
        ];
        array_push($arrayFirmantes, $temp);


        $joinBody = strip_tags($body['memorandum']).'/n'.strip_tags($body['formatoRf001']);

        //Creacion de array para pasarlo a XML
        $ArrayXml = [
            'emisor' => [
                '_attributes' => [
                    'nombre_emisor' => $usuario->name,
                    'cargo_emisor' => $usuario->puesto,
                    'dependencia_emisor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
                ],
            ],
            'archivo' => [
                '_attributes' => [
                    'nombre_archivo' => $nameFileOriginal,
                ],
                'cuerpo' => [$joinBody],
            ],
            'firmantes' => [
                '_attributes' => [
                    'num_firmantes' => $numFirmantes
                ],
                'firmante' => [
                    $arrayFirmantes
                ],
            ],
        ];

        //Creacion de estampa de hora exacta de creacion
        $date = Carbon::now();
        $month = $date->month < 10 ? '0'.$date->month : $date->month;
        $day = $date->day < 10 ? '0'.$date->day : $date->day;
        $hour = $date->hour < 10 ? '0'.$date->hour : $date->hour;
        $minute = $date->minute < 10 ? '0'.$date->minute : $date->minute;
        $second = $date->second < 10 ? '0'.$date->second : $date->second;
        $dateFormat = $date->year.'-'.$month.'-'.$day.'T'.$hour.':'.$minute.':'.$second;

        $resultado = ArrayToXml::convert($ArrayXml, [
            'rootElementName' => 'DocumentoChis',
            '_attributes' => [
                'version' => '2.0',
                'fecha_creacion' => $dateFormat,
                'no_oficio' => $numOficio,
                'dependencia_origen' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
                'asunto_docto' => 'Concentrado de Ingresos Propios',
                'tipo_docto' => 'OFC',
                'xmlns' => 'http://firmaelectronica.chiapas.gob.mx/GCD/DoctoGCD',
            ],
        ]);

        //generación de la cadena única mediante el ICTI
        $xmlBase64 = base64_encode($resultado);
        $getToken = Tokens_icti::all()->last();
        if ($getToken) {
            # registros
            $response = $this->getCadenaOriginal($xmlBase64, $getToken->token);
            if ($response->json() == null) {
                # token
                $token = $this->generarToken();
                $response = $this->getCadenaOriginal($xmlBase64, $token);
            }
        } else {
            # no hay registros
            $token = $this->generarToken();
            $response = $this->getCadenaOriginal($xmlBase64, $token);
        }

        // guardando cadena única
        if ($response->json()['cadenaOriginal'] != null) {

            $dataInsert = DocumentosFirmar::Where('numero_o_clave', $rf001->memorandum)->First();
            if (is_null($dataInsert)) {
                $dataInsert = new DocumentosFirmar();
            }
            $dataInsert->body_html = json_encode($body);
            $dataInsert->obj_documento = json_encode($ArrayXml);
            $dataInsert->status = 'EnFirma';
            $dataInsert->cadena_original = $response->json()['cadenaOriginal'];
            $dataInsert->tipo_archivo = 'Concentrado de Ingresos Propios';
            $dataInsert->numero_o_clave = $rf001->memorandum;
            $dataInsert->nombre_archivo = $nameFileOriginal;
            $dataInsert->documento = $resultado;
            $dataInsert->documento_interno = $resultado;
            $dataInsert->save();

            // actualizar registro en modelo Rf001Model
            (new Rf001Model())->where('id', $id)->update([
                'estado' => 'GENERARDOCUMENTO',
                'dirigido' => $firmanteFinanciero['funcionario']
            ]);

            return TRUE;
        } else {
            return FALSE;
        }

    }

    private function incapacidad($incapacidad, $incapacitado)
    {
        $fechaActual = now();
        if(!is_null($incapacidad->fecha_inicio)) {
            $fechaInicio = \Carbon::parse($incapacidad->fecha_inicio);
            $fechaTermino = \Carbon::parse($incapacidad->fecha_termino)->endOfDay();
            if ($fechaActual->between($fechaInicio, $fechaTermino)) {
                // La fecha de hoy está dentro del rango
                $firmanteIncapacidad = \DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre','fun.incapacidad')
                    ->Join('tbl_funcionarios AS fun','fun.id','org.id')
                    ->Where('fun.id', $incapacidad->id_firmante)
                    ->First();

                return($firmanteIncapacidad);
            } else {
                // La fecha de hoy NO está dentro del rango
                if($fechaTermino->isPast()) {
                    $newIncapacidadHistory = 'Ini:'.$incapacidad->fecha_inicio.'/Fin:'.$incapacidad->fecha_termino.'/IdFun:'.$incapacidad->id_firmante;
                    array_push($incapacidad->historial, $newIncapacidadHistory);
                    $incapacidad->fecha_inicio = $incapacidad->fecha_termino = $incapacidad->id_firmante = null;
                    $incapacidad = json_encode($incapacidad);

                    \DB::Table('tbl_funcionarios')->Where('nombre',$incapacitado)
                        ->Update([
                            'incapacidad' => $incapacidad
                    ]);
                }

                return false;
            }
        }
        return false;
    }

    protected function getCadenaOriginal($xmlBase64, $token)
    {
        // api producción
        return Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
            'xml_OriginalBase64' => $xmlBase64
        ]);

        // api prueba
        // return Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer '.$token,
        // ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
        //     'xml_OriginalBase64' => $xmlBase64
        // ]);
    }

    // obtener el token
    public function generarToken()
    {
        // Token Producción
        $resToken = Http::withHeaders([
            'Accept' => 'application/json'
        ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
            'nombre' => 'SISTEM_IVINCAP',
            'key' => 'B8F169E9-C9F6-482A-84D8-F5CB788BC306'
        ]);

        // Token Prueba
        // $resToken = Http::withHeaders([
        //     'Accept' => 'application/json'
        // ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
        //     'nombre' => 'FirmaElectronica',
        //     'key' => '19106D6F-E91F-4C20-83F1-1700B9EBD553'
        // ]);

        $token = $resToken->json();

        Tokens_icti::Where('sistema','sivyc')->update([
            'token' => $token
        ]);
        return $token;
    }

    protected function validar_incapacidad($dataFirmante)
    {
        $result = null;
        $status_campos = false;
        if ($dataFirmante->incapacidad != null) {
            # se genera un campo json
            $dataArray = json_decode($dataFirmante->incapacidad, true);
            #validamos los campos json
            if (isset($dataArray['fecha_inicio']) && isset($dataArray['fecha_termino']) && isset($dataArray['id_firmante']) && isset($dataArray['historial'])) {
                # checar datos y mostrar información
                if ($dataArray['fecha_inicio'] != '' && $dataArray['fecha_termino'] != '' && $dataArray['id_firmante'] != '')
                {
                    # code...
                    $fecha_ini = $dataArray['fecha_inicio'];
                    $fecha_fin = $dataArray['fecha_termino'];
                    $id_firmante = $dataArray['id_firmante'];
                    $historial = $dataArray['historial'];
                    $status_campos = true;
                } else {
                    return "LA ESTRUCTURA DEL JSON DE LA INCAPACIDAD NO ES VALIDA!";
                }

                #validar si está vacio
                if ($status_campos == true) {
                    # validar fechas
                    $fechaActual = date('Y-m-d');
                    $fecha_nowObj = new DateTime($fechaActual);
                    $fecha_iniObj = new DateTime($fecha_ini);
                    $fecha_finObj = new DateTime($fecha_fin);

                    if ($fecha_nowObj >= $fecha_iniObj && $fecha_nowObj <= $fecha_finObj)
                    {
                        # realizar la consulta del nuevo firmante
                        $dataIncapacidad = \DB::Table('tbl_organismos AS org')
                        ->SELECT('org.id', 'fun.nombre AS funcionario','fun.curp', 'us.name',
                        'fun.cargo','fun.correo', 'us.puesto', 'fun.incapacidad')
                        ->JOIN('tbl_funcionarios AS fun', 'fun.id','org.id')
                        ->JOIN('users AS us', 'us.email', 'fun.correo')
                        ->WHERE('fun.id', $id_firmante)
                        ->FIRST();

                        if ($dataIncapacidad != null) {
                            $result  = $dataIncapacidad;
                        } else {
                            return "NO SE ENCONTRON DATOS DE LA PERSONA QUE TOMARÁ EL LUGAR DEL ACADEMICO!";
                        }
                    } else {
                        # Historial
                        $fechaBusqueda = 'Ini:'. $fecha_ini .'/Fin:'. $fecha_fin .'/IdFun:'. $id_firmante;
                        $claveAr = array_search($fechaBusqueda, $historial);

                        if ($claveAr === false) {
                            # si no se encuentra en el historial procedemos a gurdar el registro
                            $historial[] = $fechaBusqueda;
                            # guardar en la bd el nuevo array en el campo historial del json
                            try {
                                $jsonHistorial = json_encode($historial);
                                \DB::update('UPDATE tbl_funcionarios SET incapacidad = jsonb_set(incapacidad, \'{historial}\', ?) WHERE id = ?', [$jsonHistorial, $dataFirmante->id_fun]);
                            } catch (\Throwable $th) {
                                return "Error: " . $th->getMessage();
                            }
                        }

                    }
                }
            }
        }
        return $result;
    }

    public function rederHtmlMemorandum($id)
    {
        return View::make('reportes.rf001.vista_concentrado.memorf001')->render();

    }

    public function renderHtmlForma($data, $unidad, $distintivo)
    {
        $unidad = tbl_unidades::where('id', $unidad)->first();
        $instituto = DB::table('tbl_instituto')->first();
        $direccion = $unidad->direccion;
        // Decodificar el campo cuentas_bancarias
        $cuentas_bancarias = json_decode($instituto->cuentas_bancarias, true); // true convierte el JSON en un array asociativo
        $cuenta = $cuentas_bancarias[$unidad->unidad]['BBVA'];
        return View::make('reportes.rf001.vista_concentrado.formarf001', compact('distintivo','data', 'cuenta', 'direccion'))->render();
        // return PDF::loadView('reportes.rf001.vista_concentrado.formarf001', compact('distintivo','data', 'cuenta', 'direccion'))->setPaper('a4', 'portrait')->output();
    }

    public function createBodyToXml($data, $unidad, $organismo)
    {
        $htmlBody = [];
        // memorandum crear primer documento
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $tblUnidades = \DB::table('tbl_unidades')->where('unidad', $data->unidad)->first();
        $unidadUbicacion = strtoupper($tblUnidades->ubicacion);
        $municipio = mb_strtoupper($tblUnidades->municipio, 'UTF-8');
        #OBTENEMOS LA FECHA ACTUAL
        // $fechaActual = getdate();
        $fechaActual = $data->created_at->format('Y-m-d');
        $fechaFormateada = $this->formatoFechaCrearMemo($fechaActual);
        // $anio = $fechaActual['year']; $mes = $fechaActual['mon']; $dia = $fechaActual['mday'];
        // $dia = ($dia < 10) ? '0'.$dia : $dia;

        // $fecha_comp = $dia.' de '.$meses[$mes-1].' del '.$anio;
        $dirigido = \DB::table('tbl_funcionarios')->where('id', 12)->first();
        $conocimiento = \DB::table('tbl_funcionarios')
            ->leftjoin('tbl_organismos', 'tbl_organismos.id', '=', 'tbl_funcionarios.id_org')
            ->where('tbl_organismos.id', 13)
            ->select('tbl_organismos.nombre', 'tbl_funcionarios.nombre as nombre_funcionario', 'tbl_funcionarios.cargo', 'tbl_funcionarios.titulo')
            ->first();
        $direccion = $tblUnidades->direccion;

        $delegado = \DB::Table('tbl_organismos AS o')->Select('f.nombre','f.cargo')
            ->Join('tbl_funcionarios AS f', 'f.id_org', 'o.id')
            ->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('f.activo', 'true')
            ->Where('o.nombre','LIKE','DELEG%')
            ->Where('u.unidad', $data->unidad)
            ->First();

        $nombre_titular = $cargo_fun = 'DATO REQUERIDO';
        if ($organismo) {
            $nombre_titular = $organismo->nombre_titular;
            $cargo_fun = mb_strtoupper($organismo->cargo_fun, 'UTF-8');
        }

        $datoJson = json_decode($data->movimientos, true);
        $startDate = Carbon::parse($data->periodo_inicio);
        $endDate = Carbon::parse($data->periodo_fin);
        $formattedStartDate = $startDate->format('d');
        $formattedEndDate = $endDate->format('d');
        $mes = $startDate->translatedFormat('F');
        $anio = $startDate->format('Y');

        $movimiento = json_decode($data->movimientos, true);
        $importeMemo = 0;
        $importeTotal = 0;
        $periodoInicio = Carbon::parse($data->periodo_inicio);
        $periodoFin = Carbon::parse($data->periodo_fin);
        $dateCreacion = Carbon::parse($data->created_at);
        $dateCreacion->locale('es'); // Configurar el idioma a español
        $nombreMesCreacion = $dateCreacion->translatedFormat('F');

        // documento rf001
        $unidad = tbl_unidades::where('id', $unidad)->first();
        $instituto = DB::table('tbl_instituto')->first();
        $direccion = $unidad->direccion;
        // Decodificar el campo cuentas_bancarias
        $cuentas_bancarias = json_decode($instituto->cuentas_bancarias, true); // true convierte el JSON en un array asociativo
        $cuenta = $cuentas_bancarias[$unidad->unidad]['BBVA'];

        foreach ($movimiento as $key) {
            // Acumular el importe total
            $importeMemo += $key['importe'];
        }

        $importeLetra = (new MyUtility())->letras($importeMemo);

        $htmlBody['memorandum'] = '<div class="contenedor">
            <div class="bloque_dos" align="right" style="font-family: Arial, sans-serif; font-size: 14px;">
                <p class="delet_space_p color_text"><b>UNIDAD DE CAPACITACIÓN ' . htmlspecialchars(strtoupper($unidadUbicacion)) . '</b></p>
                <p class="delet_space_p color_text">MEMORÁNDUM No. ' . htmlspecialchars($data->memorandum) . '</p>
                <p class="delet_space_p color_text">' . htmlspecialchars($municipio) . ', CHIAPAS; <span class="color_text">' . htmlspecialchars($fechaFormateada) . '</span></p>
            </div>
            <br>
            <div class="bloque_dos" align="left" style="font-family: Arial, sans-serif; font-size: 14px;">
                <p class="delet_space_p color_text"><b>' . htmlspecialchars(strtoupper($dirigido->titulo)) . ' ' . htmlspecialchars(strtoupper($dirigido->nombre)) . '</b></p>
                <p class="delet_space_p color_text"><b>' . htmlspecialchars($dirigido->cargo) . '</b></p>
                <p class="delet_space_p color_text"><b>PRESENTE.</b></p>
            </div>
            <div class="contenido" style="font-family: Arial, sans-serif; font-size: 14px; margin-top: 25px" align="justify">
                Por medio del presente, me permito enviar a usted el Concentrado de Ingresos Propios (FORMA RF-001) de la Unidad de Capacitación
                <span class="color_text"> ' .htmlspecialchars($unidadUbicacion). ' </span>, correspondiente a la semana comprendida '. $this->formatoIntervaloFecha($data->periodo_inicio, $data->periodo_fin) .'
                El informe refleja un total de $'.number_format($importeMemo, 2, '.', ',').' ('.$importeLetra.'), mismo que se adjunta para su conocimiento y trámite correspondiente.
                <br>
            </div>
            <br>';

        $htmlBody['memorandum'] .= '<div class="tabla_alumnos">
                   <p style="font-family: Arial, sans-serif; font-size: 14px;">Sin otro particular aprovecho la ocasión para saludarlo. </p>
                    <br>
                </div>
            </div> <br><br>';



        // Inicialización de formato
        $htmlBody['formatoRf001'] = '<div class="contenedor">
        <div style="text-align: center; font-size: 10px;">
            <p>
                FORMA RF-001
                <br>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS
                <br>UNIDAD DE CAPACITACIÓN '.htmlspecialchars(strtoupper($unidadUbicacion)).'
                <br>CONCENTRADO DE INGRESOS PROPIOS
            </p>
        </div>
        <table class="tabla_con_border" style="padding-top: 9px;">
            <tr>
                <td width="200px">FECHA DE ELABORACIÓN</td>
                <td width="750px" style="border-top-style: none; border-bottom-style: none; border-left-style: dotted;" colspan="8"></td>
                <td width="200px" style="text-align:center;">SEMANA</td>
                <td colspan="13" style="border: inset 0pt;"></td>
            </tr>
            <tr>
                <td style="text-align:center;">' . htmlspecialchars(Carbon::parse($data->created_at)->format('d/m/Y')) . '</td>
                <td colspan="8" style="border-top-style: none; border-bottom-style: none; border-left-style: dotted;"></td>
                <td style="text-align:center;">' . htmlspecialchars($periodoInicio->format('d/m/Y')) . ' AL ' . htmlspecialchars($periodoFin->format('d/m/Y')) . '</td>
                <td colspan="13" style="border: inset 0pt;"></td>
            </tr>
            </table>
            <center class="espaciado"></center>';

        // Información de la cuenta bancaria
        $htmlBody['formatoRf001'] .= '<table class="tabla_con_border">
            <tr>
                <td style="text-align: center;">
                    <b>DEPÓSITO(S) EFECTUADO(S) A LA CUENTA BANCARIA:</b>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    NO. CUENTA ' . htmlspecialchars($cuenta) . '
                </td>
            </tr>
            </table>';

        // Tabla de movimientos bancarios
        $htmlBody['formatoRf001'] .= '<table class="tabla_con_border" style="width: 100%; table-layout: fixed; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: center; width: 20%; word-wrap: break-word;"><b>MOVTO BANCARIO Y/O <br> NÚMERO DE FOLIO</b></th>
                    <th style="text-align: center; width: 20%; word-wrap: break-word;" ><b>N°. RECIBO Y/O FACTURA</b></th>
                    <th style="text-align: center; width: 45%; word-wrap: break-word;">CONCEPTO DE COBRO</th>
                    <th style="text-align: center; width: 15%; word-wrap: break-word;">IMPORTE</th>
                </tr>
            </thead>
            <tbody>';

        // Iterar sobre los movimientos
        $counter = 0;
        foreach ($movimiento as $item) {
            $depositos = isset($item['depositos']) ? json_decode($item['depositos'], true) : [];

            $htmlBody['formatoRf001'] .= '<tr>
                <td style="text-align: center;  word-wrap: break-word;">' . htmlspecialchars($item['folio']) . '</td>
                <td style="text-align: center;  word-wrap: break-word;">';

                // Iterar sobre los depósitos
                foreach ($depositos as $k) {
                    $counter++;
                    $htmlBody['formatoRf001'] .= $k['folio'];

                    if ($counter % 3 == 0) {
                        $htmlBody['formatoRf001'] .= '<br>';
                    } else {
                        $htmlBody['formatoRf001'] .= ', ';
                    }
                }

            $htmlBody['formatoRf001'] .= '</td>
                <td style="text-align: left; font-size: 9px;  word-wrap: break-word;">';

            // Mostrar curso o descripción
            if ($item['curso'] != null) {
                $htmlBody['formatoRf001'] .= htmlspecialchars($item['curso']);
            } else {
                $htmlBody['formatoRf001'] .= htmlspecialchars($item['descripcion']);
            }

            $htmlBody['formatoRf001'] .= '</td>
                <td style="text-align: center;  word-wrap: break-word;">$ ' . number_format($item['importe'], 2, '.', ',') . '</td>
            </tr>';

            // Acumular el importe total
            $importeTotal += $item['importe'];
        }

        // Agregar fila de total
        $htmlBody['formatoRf001'] .= '<tr>
            <td></td>
            <td></td>
            <td style="text-align:right;"><b>TOTAL</b></td>
            <td style="text-align:center;">
                <b>$ ' . number_format($importeTotal, 2, '.', ',') . '</b>
            </td>
            </tr>
            </tbody>
            </table>
            <center class="espaciado"></center>';

        $htmlBody['formatoRf001'] .=
        '<table class="tabla_con_border">
            <tr>
                <td colspan="3">OBSERVACIONES:</td>
            </tr>
            <tr>
             <td colspan="3" style=" vertical-align: text-top;"><b>SE ENVIAN FICHAS DE DEPOSITO:</b>';
             foreach ($movimiento as $k) {
                $htmlBody['formatoRf001'] .= htmlspecialchars($k['folio']) . ',';
             }
             $htmlBody['formatoRf001'] .= '<p><b>RECIBO OFICIAL: &nbsp;</b>';
             foreach ($movimiento as $v) {
                $deposito = isset($v['depositos']) ? json_decode($v['depositos'], true) : [];
                foreach ($deposito as $j) {
                    $htmlBody['formatoRf001'] .= htmlspecialchars($j['folio']) . ',';
                }
             }
             $htmlBody['formatoRf001'] .= '&nbsp; <b> '. $dateCreacion->day ."/". Str::upper($nombreMesCreacion)."/". $dateCreacion->year .'</b>';
             $htmlBody['formatoRf001'] .=  '</p></div></td></tr>';
             $htmlBody['formatoRf001'] .= '<tr>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
             </tr></table>
             <p style="font-size: 8px;">DECLARO BAJO PROTESTA DE DECIR VERDAD, QUE LOS DATOS CONTENIDOS EN ESTE CONCENTRADO SON VERÍDICOS Y MANIFIESTO TENER CONOCIMIENTO DE LAS SANCIONES QUE SE APLICARÁN EN CASO CONTRARIO</p>';
        return $htmlBody;
    }

    protected function funcionariosUnidades($unidadObtenida)
    {
        try {
            // arreglo
            $firmanteNoUno = $firmanteNoDos = [];
            // delegado administrativo
            $query = DB::table('tbl_organismos AS tblOrganismo')->Select('funcionarios.nombre', 'funcionarios.correo', 'funcionarios.curp', 'funcionarios.cargo')
                        ->Join('tbl_funcionarios AS funcionarios', 'funcionarios.id_org', 'tblOrganismo.id')
                        ->Join('tbl_unidades AS unidades', 'unidades.id', 'tblOrganismo.id_unidad')
                        ->Where('funcionarios.activo', 'true')
                        ->Where('unidades.unidad', $unidadObtenida);
            //director de la unidad
            $directorQuery = clone $query;
            $director = $directorQuery->Where('tblOrganismo.id_parent', 1)->first();
            // delegado de la unidad
            $delegadoQuery = clone $query;
            $delegado = $delegadoQuery->Where('tblOrganismo.nombre', 'LIKE', 'DELEG%')->first();

            if(!$director || !$delegado){
                return "Error en la busqueda de firmantes";
            }
            // proceso en el cuál se generan los arreglos de los firmantes
            $firmanteNoUno = array('funcionario'=>$director->nombre, 'puesto'=>$director->cargo, 'correo'=>$director->correo, 'curp'=>$director->curp);
            $firmanteNoDos = array('funcionario'=>$delegado->nombre, 'puesto'=>$delegado->cargo, 'correo'=>$delegado->correo, 'curp'=>$delegado->curp);

            return [$firmanteNoUno, $firmanteNoDos];
        } catch (\Throwable $th) {
            return "Error: ".$th->getMessage();
        }
    }

    public function sellarDocumento($xml, $token) {
        //Sellado de producción
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/NotariaXML/sellarXML', [
            'xml_Firmado' => $xml
        ]);

        // Sellado de prueba
        // $response = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer '.$token
        // ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/NotariaXML/sellarXML', [
        //     'xml_Firmado' => $xml
        // ]);
        return $response;
    }

    public function genXmlFormato($id, $organismo, $unidad, $usuario)
    {
        $htmlBody = array();
        $rf001 = (new Rf001Model())->findOrFail($id); // obtener RF001 por id
        $documentoFirmar = DocumentosFirmar::Where('numero_o_clave', $rf001->memorandum)->First();
        if ($documentoFirmar) {
            # TODO: se encuentra se tiene que eliminar y por ende volver a generar
            $documentoFirmar->delete();
        }
        //checa si existe
        $distintivo = \DB::table('tbl_instituto')->value('distintivo'); #texto de encabezado del pdf
        // elaboro y puesto de elaboración
        $nombreElaboro = $usuario->name;
        $puestoElaboro = $usuario->puesto;

        $organismoPublico = \DB::table('organismos_publicos')->select('nombre_titular', 'cargo_fun')->where('id', '=', $organismo)->first();

        $body = $this->htmlToXml($rf001, $unidad, $organismoPublico); //cambiar este formato

        if (is_null($body)) {
            $error = ['error' => 1];
            return $error;
        }

        $ubicacion = Unidad::where('id', $unidad)->value('ubicacion');

        $firmantes = $this->funcionariosUnidades($ubicacion);
        list($firmanteNoUno, $firmanteNoDos) = $firmantes;

        $financieroFirmante = $this->getFirmanteFinanciero($rf001->id_unidad);

        $dataFirmantes = \DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre','fun.incapacidad')
            ->Join('tbl_funcionarios AS fun','fun.id_org','org.id')
            ->Join('tbl_unidades AS u', 'u.id', 'org.id_unidad')
            ->Where('org.id_parent',1)
            ->Where('fun.activo', 'true')
            ->Where('u.unidad', $ubicacion)
            ->First();


        $nameFileOriginal = 'concentrado cancelacion'.$rf001->memorandum.'.pdf';
        $numOficio = "cancelacion-rf001-".$rf001->memorandum;
        $numFirmantes = '2'; // 1 o 2

        $arrayFirmantes = [];

        // TODO: solo firma el delegado

        // delegado
        $temp = ['_attributes' =>
            [
                'curp_firmante' => $firmanteNoDos['curp'],
                'nombre_firmante' => $firmanteNoDos['funcionario'],
                'email_firmante' => $firmanteNoDos['correo'],
                'tipo_firmante' => 'FM'
            ]
        ];

        array_push($arrayFirmantes, $temp);

        $temp = ['_attributes' =>
            [
                'curp_firmante' => $financieroFirmante['curp'],
                'nombre_firmante' => $financieroFirmante['funcionario'],
                'email_firmante' => $financieroFirmante['correo'],
                'tipo_firmante' => 'FM'
            ]
        ];
        array_push($arrayFirmantes, $temp);


        $joinBody = strip_tags($body['memorandum']);

        //Creacion de array para pasarlo a XML
        $ArrayXml = [
            'emisor' => [
                '_attributes' => [
                    'nombre_emisor' => $usuario->name,
                    'cargo_emisor' => $usuario->puesto,
                    'dependencia_emisor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
                ],
            ],
            'archivo' => [
                '_attributes' => [
                    'nombre_archivo' => $nameFileOriginal,
                ],
                'cuerpo' => [$joinBody],
            ],
            'firmantes' => [
                '_attributes' => [
                    'num_firmantes' => $numFirmantes
                ],
                'firmante' => [
                    $arrayFirmantes
                ],
            ],
        ];

        //Creacion de estampa de hora exacta de creacion
        $date = Carbon::now();
        $month = $date->month < 10 ? '0'.$date->month : $date->month;
        $day = $date->day < 10 ? '0'.$date->day : $date->day;
        $hour = $date->hour < 10 ? '0'.$date->hour : $date->hour;
        $minute = $date->minute < 10 ? '0'.$date->minute : $date->minute;
        $second = $date->second < 10 ? '0'.$date->second : $date->second;
        $dateFormat = $date->year.'-'.$month.'-'.$day.'T'.$hour.':'.$minute.':'.$second;

        $resultado = ArrayToXml::convert($ArrayXml, [
            'rootElementName' => 'DocumentoChis',
            '_attributes' => [
                'version' => '2.0',
                'fecha_creacion' => $dateFormat,
                'no_oficio' => $numOficio,
                'dependencia_origen' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
                'asunto_docto' => 'Concentrado de Ingresos Propios',
                'tipo_docto' => 'OFC',
                'xmlns' => 'http://firmaelectronica.chiapas.gob.mx/GCD/DoctoGCD',
            ],
        ]);

        //generación de la cadena única mediante el ICTI
        $xmlBase64 = base64_encode($resultado);
        $getToken = Tokens_icti::all()->last();
        if ($getToken) {
            # registros
            $response = $this->getCadenaOriginal($xmlBase64, $getToken->token);
            if ($response->json() == null) {
                # token
                $token = $this->generarToken();
                $response = $this->getCadenaOriginal($xmlBase64, $token);
            }
        } else {
            # no hay registros
            $token = $this->generarToken();
            $response = $this->getCadenaOriginal($xmlBase64, $token);
        }

        // guardando cadena única
        if ($response->json()['cadenaOriginal'] != null) {

            $dataInsert = DocumentosFirmar::Where('numero_o_clave', $rf001->memorandum)->first();
            if (is_null($dataInsert)) {
                $dataInsert = new DocumentosFirmar();
                $dataInsert->body_html = json_encode($body);
                $dataInsert->obj_documento = json_encode($ArrayXml);
                $dataInsert->status = 'EnFirma';
                $dataInsert->cadena_original = $response->json()['cadenaOriginal'];
                $dataInsert->tipo_archivo = 'Concentrado de Ingresos Propios';
                $dataInsert->numero_o_clave = $rf001->memorandum;
                $dataInsert->nombre_archivo = $nameFileOriginal;
                $dataInsert->documento = $resultado;
                $dataInsert->documento_interno = $resultado;
                $dataInsert->save();
            }

            // actualizar registro en modelo Rf001Model
            (new Rf001Model())->where('id', $id)->update([
                'estado' => 'GENERARDOCUMENTO',
                'dirigido' => $financieroFirmante['funcionario']
            ]);

            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function htmlToXml($data, $unidad, $organismo)
    {
        $htmlBody = [];
        // memorandum crear primer documento
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $tblUnidades = \DB::table('tbl_unidades')->where('unidad', $data->unidad)->first();
        $unidadUbicacion = strtoupper($tblUnidades->ubicacion);
        $municipio = mb_strtoupper($tblUnidades->municipio, 'UTF-8');
        #OBTENEMOS LA FECHA ACTUAL
        $fechaActual = getdate();
        $anio = $fechaActual['year']; $mes = $fechaActual['mon']; $dia = $fechaActual['mday'];
        $dia = ($dia < 10) ? '0'.$dia : $dia;

        $fecha_comp = $dia.' de '.$meses[$mes-1].' del '.$anio;
        $dirigido = \DB::table('tbl_funcionarios')->where('id', 12)->first();
        $conocimiento = \DB::table('tbl_funcionarios')
            ->leftjoin('tbl_organismos', 'tbl_organismos.id', '=', 'tbl_funcionarios.id_org')
            ->where('tbl_organismos.id', 13)
            ->select('tbl_organismos.nombre', 'tbl_funcionarios.nombre as nombre_funcionario', 'tbl_funcionarios.cargo', 'tbl_funcionarios.titulo')
            ->first();
        $direccion = $tblUnidades->direccion;

        $delegado = \DB::Table('tbl_organismos AS o')->Select('f.nombre','f.cargo')
            ->Join('tbl_funcionarios AS f', 'f.id_org', 'o.id')
            ->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('f.activo', 'true')
            ->Where('o.nombre','LIKE','DELEG%')
            ->Where('u.unidad', $data->unidad)
            ->First();

        $nombre_titular = $cargo_fun = 'DATO REQUERIDO';
        if ($organismo) {
            $nombre_titular = $organismo->nombre_titular;
            $cargo_fun = mb_strtoupper($organismo->cargo_fun, 'UTF-8');
        }

        $datoJson = json_decode($data->movimientos, true);
        $startDate = Carbon::parse($data->periodo_inicio);
        $endDate = Carbon::parse($data->periodo_fin);
        $formattedStartDate = $startDate->format('d');
        $formattedEndDate = $endDate->format('d');
        $mes = $startDate->translatedFormat('F');
        $anio = $startDate->format('Y');

        $movimiento = json_decode($data->movimientos, true);
        $importeTotal = 0;
        $periodoInicio = Carbon::parse($data->periodo_inicio);
        $periodoFin = Carbon::parse($data->periodo_fin);
        $dateCreacion = Carbon::parse($data->created_at);
        $dateCreacion->locale('es'); // Configurar el idioma a español
        $nombreMesCreacion = $dateCreacion->translatedFormat('F');

        // documento rf001
        $unidad = tbl_unidades::where('id', $unidad)->first();
        $instituto = DB::table('tbl_instituto')->first();
        $direccion = $unidad->direccion;
        // Decodificar el campo cuentas_bancarias
        $cuentas_bancarias = json_decode($instituto->cuentas_bancarias, true); // true convierte el JSON en un array asociativo
        $cuenta = $cuentas_bancarias[$unidad->unidad]['BBVA'];

        $htmlBody['memorandum'] = '<div class="contenedor">
            <div class="bloque_uno" align="right">
                <p class="delet_space_p color_text">UNIDAD DE CAPACITACIÓN ' . htmlspecialchars(strtoupper($unidadUbicacion)) . '</p>
                <p class="delet_space_p color_text">MEMORÁNDUM NÚM. ' . htmlspecialchars($data->memorandum) . '</p>
                <p class="delet_space_p color_text">' . htmlspecialchars($municipio) . ', CHIAPAS; <span class="color_text">' . htmlspecialchars(strtoupper($fecha_comp)) . '</span></p>
            </div>
            <br><br><br>
            <div class="bloque_dos" align="left">
                <p class="delet_space_p color_text">C. ' . htmlspecialchars(strtoupper($dirigido->titulo)) . ' ' . htmlspecialchars(strtoupper($dirigido->nombre)) . '</p>
                <p class="delet_space_p color_text">' . htmlspecialchars($dirigido->cargo) . '</p>
                <p class="delet_space_p color_text">PRESENTE.</p>
            </div>
            <br>
            <div class="contenido" align="justify">
                Por medio del presente, atentamente me permito informar a usted la cancelación del recibo que a continuación se indica, por la razón especifica:
            </div>
            <br>';

        $htmlBody['memorandum'] .= '<table class="tabla_con_border">
            <thead>
                <tr>
                    <th style="text-align: center;"><b>PROGRESIVO</b></th>
                    <th style="text-align: center;" ><b>NÚMERO DE RECBIBO</b></th>
                    <th style="text-align: center;">MOTIVO</th>
                </tr>
            </thead>
            <tbody>';

        // Iterar sobre los movimientos
        $counter = 1;
            foreach ($datoJson as $key) {
                $curso = isset($key['curso']) && $key['curso'] !== null ? strtolower($key['curso']) : strtolower($key['descripcion']);
                $htmlBody['memorandum'] .= '<tr>
                    <td style="width: 55px; text-align: center;">' . $counter . '</td>
                    <td style="width: 40px; text-align: center; font-size: 9px;">'. htmlspecialchars($key['folio']) .'</td>
                    <td style="width: 160px; text-align: center; font-size: 9px;">';
                    if ($key['curso'] != null) {
                        $htmlBody['memorandum'] .= htmlspecialchars($key['curso']);
                    } else {
                        $htmlBody['memorandum'] .= htmlspecialchars($key['descripcion']);
                    }
                $htmlBody['memorandum'] .=  '</td>
                    </tr>';

                $counter ++;
            }

        $htmlBody['memorandum'] .= '</tbody>
            </table>
            <center class="espaciado"></center>';

        $htmlBody['memorandum'] .= '
                <p style="font-size: 14px">Sin más que agregar, agradezco su atención y le envío un cordial saludo. </p>
                <br>
            </div>';


        return $htmlBody;
    }

    public function formatoIntervaloFecha($fechaIni, $fechaFin)
    {
        // Parsear las fechas usando Carbon
        $dateInit = Carbon::parse($fechaIni);
        $dateEnd = Carbon::parse($fechaFin);

        // Configurar el idioma a español
        $dateInit->locale('es');
        $dateEnd->locale('es');

        // Comparar si los meses de las fechas de inicio y fin son iguales
        if($dateInit->translatedFormat('F') === $dateEnd->translatedFormat('F')) {
            // Mismo mes, formato: "del 7 al 11 de octubre del 2024"
            $formattedDates = 'del ' . $dateInit->translatedFormat('j') . ' al ' . $dateEnd->translatedFormat('j') . ' de ' . $dateEnd->translatedFormat('F') . ' del ' . $dateEnd->translatedFormat('Y');
        } else {
            // Meses diferentes, formato: "del 30 de septiembre al 4 de octubre del 2024"
            $formattedDates = 'del ' . $dateInit->translatedFormat('j') . ' de ' . $dateInit->translatedFormat('F') . ' al ' . $dateEnd->translatedFormat('j') . ' de ' . $dateEnd->translatedFormat('F') . ' del ' . $dateEnd->translatedFormat('Y');
        }

        // Imprimir el resultado
        return $formattedDates;
    }

    public function getFirmanteFinanciero($idRfUnidad)
    {
        $UnidadesAtendidasNarj8 = ['CATAZAJA', 'JIQUIPILAS', 'OCOSINGO', 'TAPACHULA', 'VILLAFLORES', 'YAJALON'];
        $UnidadesAtendidasCucc8 = ['TUXTLA', 'TONALA', 'SAN CRISTOBAL', 'COMITAN', 'REFORMA'];

        $qry = DB::table('tbl_organismos AS tblOrganismo')->Select('funcionarios.nombre', 'funcionarios.correo', 'funcionarios.curp', 'funcionarios.cargo')
        ->Join('tbl_funcionarios AS funcionarios', 'funcionarios.id_org', 'tblOrganismo.id')
        ->Where('funcionarios.titular', 0)
        ->Where('funcionarios.id_org', 12);

        $querygetUnidad = DB::table('tbl_unidades')->select('unidad', 'id', 'cct')->where('id', '=', $idRfUnidad)->first();

        if (in_array($querygetUnidad->unidad, $UnidadesAtendidasNarj8)) {
            # se encuentra en la lista nandayapa
            $qry = $qry->where('funcionarios.correo', '=', 'nandayaparamirez_jj@hotmail.com')->first();

            return array('funcionario'=>$qry->nombre, 'puesto'=>$qry->cargo, 'correo'=>$qry->correo, 'curp'=>$qry->curp);

        } elseif (in_array($querygetUnidad->unidad, $UnidadesAtendidasCucc8)) {
            # se encuentra en lista chatu
            $qry = $qry->where('funcionarios.correo', '=', 'chatucr77@hotmail.com')->first();

            return array('funcionario'=>$qry->nombre, 'puesto'=>$qry->cargo, 'correo'=>$qry->correo, 'curp'=>$qry->curp);
        }
    }

    protected function formatoFechaCrearMemo($fecha)
    {
        //parsear la fecha utilizando Carbon
        $parserDate = Carbon::parse($fecha);
        // configurar al idioma español
        $parserDate->locale('es');

        $formattedDate = $parserDate->translatedFormat('d'). ' DE '. mb_strtoupper($parserDate->translatedFormat('F'), 'UTF-8'). ' DEL '. $parserDate->translatedFormat('Y');
        return $formattedDate;
    }
}
