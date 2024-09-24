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
        // return view('reportes.rf001.reporterf001', compact('distintivo', 'organismo', 'data', 'unidad', 'rf001', 'municipio', 'fecha_comp', 'dirigido', 'direccion', 'conocimiento', 'nombreElaboro', 'puestoElaboro', 'delegado'))->render();
    }

    public function xmlFormat($id, $organismo, $unidad, $usuario)
    {
        $rf001 = (new Rf001Model())->findOrFail($id); // obtener RF001 por id

        // $body = $this->createBody($id, $rf001);
        $htmlContent = $this->renderHtmlForma($rf001, $unidad);
        $contWithoutHtml = strip_tags($htmlContent); //contenido sin html
        // limpiar cadena
        $clnHtml = preg_replace('/@page\s*\{.*?\}\s*\/\*.*?\*\/|\.tb\s*\{.*?\}|\#titulo\s*\{.*?\}|\.tablaf\s*\{.*?\}|\.showlast\s*\{.*?\}|\.showborders\s*\{.*?\}|\.prueba\s*\{.*?\}|\.direccion\s*\{.*?\}|\.mielemento\s*\{.*?\}|p\s*\{.*?\}|body\s*\{.*?\}|header\s*\{.*?\}|footer\s*\{.*?\}|if\s*\(\s*isset\(\$pdf\)\s*\)\s*\{.*?\}/s', '', $contWithoutHtml);
        // Eliminar líneas en blanco o espacios innecesarios.
        $clnEspacios = preg_replace('/\s+/', ' ', $clnHtml);
        //eliminar elementos restantes css
        $body = preg_replace('/[.#][\w\s-]+[\w\s,.]*\{[^}]*\}\s*/', '', $clnEspacios);

        $ubicacion = Unidad::where('id', $unidad)->value('ubicacion');

        $dataFirmantes = \DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre','fun.incapacidad')
            ->Join('tbl_funcionarios AS fun','fun.id_org','org.id')
            ->Join('tbl_unidades AS u', 'u.id', 'org.id_unidad')
            ->Where('org.id_parent',1)
            ->Where('fun.activo', 'true')
            ->Where('u.unidad', $ubicacion)
            ->First();

        // $body = $this->createBody($id, $dataFirmantes);
        $arrayFirmantes = [];
        $temp = ['_attributes' =>
            [
                'curp_firmante' => $dataFirmantes->curp,
                'nombre_firmante' => $dataFirmantes->funcionario,
                'email_firmante' => $dataFirmantes->correo,
                'tipo_firmante' => 'FM',
            ]
        ];
        $nameFileOriginal = 'concentrado '.$rf001->memorandum.'.pdf';
        $numOficio = "concentrado-".$rf001->memorandum;
        $numFirmantes = '1'; // 1 o 2
        array_push($arrayFirmantes, $temp);

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
                'cuerpo' => [$body],
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

            $dataInsert = DocumentosFirmar::Where('numero_o_clave', $rf001->memorandum)->Where('tipo_archivo','Reporte fotografico')->First();
            if (is_null($dataInsert)) {
                $dataInsert = new DocumentosFirmar();
            }
            // $dataInsert->obj_documento_interno = json_encode($htmlContent);
            $dataInsert->body_html = $htmlContent;
            $dataInsert->obj_documento = json_encode($ArrayXml);
            $dataInsert->status = 'EnFirma';
            $dataInsert->cadena_original = $response->json()['cadenaOriginal'];
            $dataInsert->tipo_archivo = 'Concentrado de Ingresos Propios';
            $dataInsert->numero_o_clave = $rf001->memorandum;
            $dataInsert->nombre_archivo = $nameFileOriginal;
            $dataInsert->documento = $resultado;
            $dataInsert->documento_interno = $resultado;
            $dataInsert->save();

            (new Rf001Model())->where('id', $id)->update([
                'estado' => 'ENFIRMA'
            ]);

            return TRUE;
        } else {
            return FALSE;
        }

    }

    protected function createBody($id, $firmante)
    {

        try {
            #Distintivo
            $distintivo = \DB::connection('pgsql')->table('tbl_instituto')->value('distintivo');
            $rf001 = (new Rf001Model())->findOrFail($id);

            $data = \DB::table('tbl_unidades')->where('unidad', $rf001->unidad)->first();
            $unidad = strtoupper($data->ubicacion);
            $municipio = mb_strtoupper($data->municipio, 'UTF-8');

            #OBTENEMOS LA FECHA ACTUAL

            $meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];

            $fechaActual = Carbon::now();
            $dia = $fechaActual->day;
            $mes = $fechaActual->month;
            $anio = $fechaActual->year;

            $dia = ($dia) <= 9 ? '0'.$dia : $dia;
            $fecha_comp = $dia.' DE '.$meses[$mes-1].' DE '.$anio;

            $dirigido = \DB::table('tbl_funcionarios')->where('id', 12)->first();

            $datoJson = json_decode($rf001->movimientos, true);
            $startDate = Carbon::parse($rf001->periodo_inicio);
            $endDate = Carbon::parse($rf001->periodo_fin);
            $formattedStartDate = $startDate->format('d');
            $formattedEndDate = $endDate->format('d');
            $mes = $startDate->translatedFormat('F');
            $anio = $startDate->format('Y');

            $bodyHtml = null;

            // $bodyXml = null;

            // #fecha del envio de documento
            // $meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];

            // $bodyXml = "INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN".
            // "\n TECNOLÓGICA DEL ESTADO DE CHIAPAS \n".
            // "\n". $distintivo. "\n".
            // "\n UNIDAD DE CAPACITACIÓN ".$unidad.
            // "\n OFICIO NÚM. ". $rf001->memorandum .
            // "\n ".$municipio.", CHIAPAS. A ".$fecha_comp.".\n";

            // $bodyXml .= "\n ". strtoupper($dirigido->titulo) . " " . strtoupper($dirigido->nombre) .
            // "\n ". $dirigido->cargo .
            // "\n PRESENTE \n";

            // $bodyXml .= "\n Por medio del presente, envío a usted Original del formato de concentrado de ingresos propios (RF-001), original, \n".
            // "\n copias de fichas de depósito y recibos oficiales correspondientes a los cursos generados en la unidad de \n".
            // "\n Capacitación , con los siguientes movimientos. \n";

            // $bodyXml .= "";


            $bodyHtml = '<div align=center><b>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS</b></div>
            <div>
                <b>'.$distintivo.'</b>
            </div> <br>
            <div align=right>
                <b>UNIDAD DE CAPACITACIÓN '.$unidad.'</b> <br>
                <b>OFICIO NÚM. '. $rf001->memorandum . '</b> <br>
                <b>'. $municipio .', CHIAPAS. A '. $fecha_comp. '</b>
            </div> <br>';

            $bodyHtml .= '<div align=right>
                '. strtoupper($dirigido->titulo) .' '. strtoupper($dirigido->nombre) .' <br>
                '.$dirigido->cargo.' <br>
                PRESENTE
            </div><br>';

            $bodyHtml .= '
            <div align=left>
                <p>
                Por medio del presente, envío a usted Original del formato de concentrado de ingresos propios (RF-001), original, copias de fichas de depósito y recibos oficiales correspondientes a los cursos generados en la unidad de Capacitación '.$unidad.', con los siguientes movimientos:
                </p>
            </div><br><br>';

            $bodyHtml .= '
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    <p>PROGRESIVO</p>
                                </th>
                                <th>
                                    <p>N° FOLIO</p>
                                </th>
                                <th>
                                    <p>CURSO / MOTIVO</p>
                                </th>
                                <th>
                                    <p>MOVIMIENTO</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>';
                foreach ($datoJson as $key => $value)
                {
                    $depositos = isset($value['depositos'])
                    ? json_decode($value['depositos'], true)
                    : [];

                    $bodyHtml .= '<tr>';
                    $bodyHtml .= '<td>' . ($key < 9 ? '0' : '') . ($key + 1) . '</td>'; // Aquí suponemos que $key es el PROGRESIVO
                    $bodyHtml .= '<td>' . $value['folio'] . '</td>'; // Aquí accedes al número de folio
                    $bodyHtml .= '<td>' . $value['curso'] == null ? $value['descripcion'] : $value['curso'] . '</td>'; // Aquí accedes al curso/motivo
                    $bodyHtml .= '<td>';
                    foreach ($depositos as $k) {
                        $bodyHtml.=  $k['folio']; // Aquí accedes al movimiento
                    }
                    $bodyHtml .= '</td>';
                    $bodyHtml .= '</tr>';
                }

            $bodyHtml.='</tbody>
                    </table><br>';

            $bodyHtml .= '<div align=justify>
                <p>Correspondientes al periodo comprendido del '.$formattedStartDate.' al '.$formattedEndDate.' de '.$mes.' del '.$anio.', lo anterior, para contabilización
                respectiva.</p>
                <p>Sin otro particular aprovecho la ocasión para saludarlo.</p>
            </div>';
            return $bodyHtml; // retorno del cuerpo del xml
        } catch (\Throwable $th) {
            return "ERROR AL CREAR EL CUERPO DEL DOCUMENTO ".$th->getMessage();
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
        // return Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer '.$token,
        // ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
        //     'xml_OriginalBase64' => $xmlBase64
        // ]);

        // api prueba
        return Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
            'xml_OriginalBase64' => $xmlBase64
        ]);
    }

    // obtener el token
    public function generarToken()
    {
        // $resToken = Http::withHeaders([
        //     'Accept' => 'application/json'
        // ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
        //     'nombre' => 'SISTEM_IVINCAP',
        //     'key' => 'B8F169E9-C9F6-482A-84D8-F5CB788BC306'
        // ]);

        // Token Prueba
        $resToken = Http::withHeaders([
            'Accept' => 'application/json'
        ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
            'nombre' => 'FirmaElectronica',
            'key' => '19106D6F-E91F-4C20-83F1-1700B9EBD553'
        ]);

        $token = $resToken->json();

        Tokens_icti::create([
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
            if (isset($dataArray['fecha_inicio']) && isset($dataArray['fecha_termino'])
                && isset($dataArray['id_firmante']) && isset($dataArray['historial'])) {
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

    private function datos_firmantes($organismo)
    {
        $firmanteUno = $firmanteDos = array();
        try {
            //área del usuario
            $area_org = DB::table('tbl_organismos as o')->select('o.id', 'o.nombre as area_org', 'id_parent', 'fun.cargo', 'fun.titulo',
            'fun.nombre as funcionario', 'fun.correo', 'fun.curp')
            ->Join('tbl_funcionarios as fun', 'fun.id_org', '=', 'o.id')
            ->where('o.id', $organismo)->first();

            // ORGANISMO DEL USUARIO / (DIRECCION)
            $org = DB::table('tbl_organismos as o')->select('o.id', 'o.nombre as org', 'fun.cargo', 'fun.titulo', 'fun.nombre as funcionario',
            'fun.correo', 'fun.curp')
            ->Join('tbl_funcionarios as fun', 'fun.id_org', '=', 'o.id')
            ->where('o.id', $area_org->id_parent)->first();

            if (!$area_org || !$org) {
                return "Error en la busqueda de firmantes";
            }

            if($area_org->id_parent == 1) {
                // $firmanteUno = array('funcionario'=>Auth::user()->name, 'puesto'=>Auth::user()->puesto, 'curp'=>Auth::user()->curp, 'correo'=>Auth::user()->email);
                // $firmanteDos = array('funcionario'=>$area_org->funcionario, 'puesto'=>$area_org->cargo, 'correo'=>$area_org->correo, 'curp'=>$area_org->curp);
                return "Usuario no disponible para realizar el firmado electronico";
            }else{
                $firmanteUno = array('funcionario'=>$area_org->funcionario, 'puesto'=>$area_org->cargo, 'correo'=>$area_org->correo, 'curp'=>$area_org->curp);
                $firmanteDos = array('funcionario'=>$org->funcionario, 'puesto'=>$org->cargo, 'correo'=>$org->correo, 'curp'=>$org->curp);
            }

            return [$area_org, $org, $firmanteUno, $firmanteDos];
        } catch (\Throwable $th) {
            return "Error: ".$th->getMessage();
        }
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
        // return view('reportes.rf001.vista_concentrado.formarf001', compact('distintivo','data', 'cuenta', 'direccion'))->render();
        return PDF::loadView('reportes.rf001.vista_concentrado.formarf001', compact('distintivo','data', 'cuenta', 'direccion'))->setPaper('a4', 'portrait')->output();
    }
}
