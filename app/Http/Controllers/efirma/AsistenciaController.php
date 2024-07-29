<?php

namespace App\Http\Controllers\efirma;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Spatie\ArrayToXml\ArrayToXml;
use App\Models\DocumentosFirmar;
use Illuminate\Http\Request;
use App\Models\Tokens_icti;
use App\Models\tbl_curso;
use PHPQRCode\QRcode;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use PDF;

class AsistenciaController extends Controller
{
    function __construct() {
        $this->mes = ["01" => "ENERO", "02" => "FEBRERO", "03" => "MARZO", "04" => "ABRIL", "05" => "MAYO", "06" => "JUNIO", "07" => "JULIO", "08" => "AGOSTO", "09" => "SEPTIEMBRE", "10" => "OCTUBRE", "11" => "NOVIEMBRE", "12" => "DICIEMBRE"];
    }

    public function generar_xml(Request $request) {
        $info = DB::Table('tbl_cursos')->Select('tbl_unidades.*','tbl_cursos.clave','tbl_cursos.nombre','tbl_cursos.curp','instructores.correo')
                ->Join('tbl_unidades','tbl_unidades.unidad','tbl_cursos.unidad')
                ->join('instructores','instructores.id','tbl_cursos.id_instructor')
                ->Where('tbl_cursos.id',$request->txtIdValidado)
                ->First();

        $body = $this->create_body($request->txtIdValidado,$info); //creacion de body
        $body = str_replace(["\r", "\n", "\f"], ' ', $body);

        $nameFileOriginal = 'Lista de asistencia '.$info->clave.'.pdf';
        $numOficio = 'LAD-04-'.$info->clave;
        $numFirmantes = '2';

        $arrayFirmantes = [];

        $dataFirmante = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo',
        'org.nombre', 'fun.incapacidad', 'fun.id as id_fun')
                            ->Join('tbl_funcionarios AS fun','fun.id','org.id')
                            ->Where('org.id', Auth::user()->id_organismo)
                            ->Where('org.nombre', 'LIKE', 'DEPARTAMENTO ACADÉMICO%')
                            ->OrWhere('org.id_parent', Auth::user()->id_organismo)
                            // ->Where('org.nombre', 'NOT LIKE', 'CENTRO%')
                            ->Where('org.nombre', 'LIKE', 'DEPARTAMENTO ACADÉMICO%')
                            ->First();
        if($dataFirmante == null){
            return redirect()->route('firma.inicio')->with('danger', 'NO SE ENCONTRARON DATOS DEL FIRMANTE AL REALIZAR LA CONSULTA');
        }
        ##Incapacidad
        $val_inca = $this->valid_incapacidad($dataFirmante);
        if ($val_inca != null) {
            $dataFirmante = $val_inca;
        }

        //Llenado de funcionarios firmantes
        $temp = ['_attributes' =>
            [
                'curp_firmante' => $info->curp,
                'nombre_firmante' => $info->nombre,
                'email_firmante' => $info->correo,
                'tipo_firmante' => 'FM'
            ]
        ];
        array_push($arrayFirmantes, $temp);

        $temp = ['_attributes' =>
            [
                'curp_firmante' => $dataFirmante->curp,
                'nombre_firmante' => $dataFirmante->funcionario,
                'email_firmante' => $dataFirmante->correo,
                'tipo_firmante' => 'FM'
            ]
        ];
        array_push($arrayFirmantes, $temp);

        $ArrayXml = [
            'emisor' => [
                '_attributes' => [
                    'nombre_emisor' => Auth::user()->name,
                    'cargo_emisor' => Auth::user()->puesto,
                    'dependencia_emisor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas'
                    // 'curp_emisor' => $dataEmisor->curp
                ],
            ],
            'archivo' => [
                '_attributes' => [
                    'nombre_archivo' => $nameFileOriginal
                    // 'md5_archivo' => $md5
                    // 'checksum_archivo' => utf8_encode($text)
                ],
                // 'cuerpo' => ['Por medio de la presente me permito solicitar el archivo '.$nameFile]
                'cuerpo' => [$body]
            ],
            'firmantes' => [
                '_attributes' => [
                    'num_firmantes' => $numFirmantes
                ],
                'firmante' => [
                    $arrayFirmantes
                ]
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

        $result = ArrayToXml::convert($ArrayXml, [
            'rootElementName' => 'DocumentoChis',
            '_attributes' => [
                'version' => '2.0',
                'fecha_creacion' => $dateFormat,
                'no_oficio' => $numOficio,
                'dependencia_origen' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
                'asunto_docto' => 'Lista de asistencia LAD-04',
                'tipo_docto' => 'OFC',
                'xmlns' => 'http://firmaelectronica.chiapas.gob.mx/GCD/DoctoGCD',
            ],
        ]);
        //Generacion de cadena unica mediante el ICTI
        $xmlBase64 = base64_encode($result);
        $getToken = Tokens_icti::all()->last();
        if ($getToken) {
            $response = $this->getCadenaOriginal($xmlBase64, $getToken->token);
            if ($response->json() == null) {
                $token = $this->generarToken();
                $response = $this->getCadenaOriginal($xmlBase64, $token);
            }
        } else {// no hay registros

            $token = $this->generarToken();
            $response = $this->getCadenaOriginal($xmlBase64, $token);
        }

        //Guardado de cadena unica
        if ($response->json()['cadenaOriginal'] != null) {
            // $urlFile = $this->uploadFileServer($request->file('doc'), $nameFileOriginal);
            // $urlFile = $this->uploadFileServer($request->file('doc'), $nameFile);
            // $datas = explode('*',$urlFile);

            $dataInsert = DocumentosFirmar::Where('numero_o_clave',$info->clave)->Where('tipo_archivo','Lista de asistencia')->First();
            if(is_null($dataInsert)) {
                $dataInsert = new DocumentosFirmar();
            }
            $dataInsert->obj_documento = json_encode($ArrayXml);
            $dataInsert->obj_documento_interno = json_encode($ArrayXml);
            $dataInsert->status = 'EnFirma';
            // $dataInsert->link_pdf = $urlFile;
            $dataInsert->cadena_original = $response->json()['cadenaOriginal'];
            $dataInsert->tipo_archivo = 'Lista de asistencia';
            $dataInsert->numero_o_clave = $info->clave;
            $dataInsert->nombre_archivo = $nameFileOriginal;
            $dataInsert->documento = $result;
            $dataInsert->documento_interno = $result;
            // $dataInsert->md5_file = $md5;
            $dataInsert->save();

            return redirect()->route('firma.inicio')->with('success', 'Lista de Asistencia Validado Exitosamente!');
        } else {
            return redirect()->route('firma.inicio')->with('danger', 'Hubo un Error al Validar. Intente Nuevamente en unos Minutos.');
        }
    }

    ### BY JOSE LUIS / VALIDACIÓN DE INCAPACIDAD
    public function valid_incapacidad($dataFirmante){
        $result = null;

        $status_campos = false;
        if($dataFirmante->incapacidad != null){
            $dataArray = json_decode($dataFirmante->incapacidad, true);

            ##Validamos los campos json
            if(isset($dataArray['fecha_inicio']) && isset($dataArray['fecha_termino'])
            && isset($dataArray['id_firmante']) && isset($dataArray['historial'])){

                if($dataArray['fecha_inicio'] != '' && $dataArray['fecha_termino'] != '' && $dataArray['id_firmante'] != ''){
                    $fecha_ini = $dataArray['fecha_inicio'];
                    $fecha_fin = $dataArray['fecha_termino'];
                    $id_firmante = $dataArray['id_firmante'];
                    $historial = $dataArray['historial'];
                    $status_campos = true;
                }
            }else{
                return redirect()->route('firma.inicio')->with('danger', 'LA ESTRUCTURA DEL JSON DE LA INCAPACIDAD NO ES VALIDA!');
            }

            ##Validar si esta vacio
            if($status_campos == true){
                ##Validar las fechas
                $fechaActual = date("Y-m-d");
                $fecha_nowObj = new DateTime($fechaActual);
                $fecha_iniObj = new DateTime($fecha_ini);
                $fecha_finObj = new DateTime($fecha_fin);

                if($fecha_nowObj >= $fecha_iniObj && $fecha_nowObj <= $fecha_finObj){
                    ###Realizamos la consulta del nuevo firmante
                    $dataIncapacidad = DB::Table('tbl_organismos AS org')
                    ->Select('org.id', 'fun.nombre AS funcionario','fun.curp',
                    'fun.cargo','fun.correo', 'org.nombre', 'fun.incapacidad')
                    ->join('tbl_funcionarios AS fun', 'fun.id','org.id')
                    ->where('fun.id', $id_firmante)
                    ->first();

                    if ($dataIncapacidad != null) {$result = $dataIncapacidad;}
                    else{return redirect()->route('firma.inicio')->with('danger', 'NO SE ENCONTRON DATOS DE LA PERSONA QUE TOMARÁ EL LUGAR DEL ACADEMICO!');}

                }else{
                    ##Historial
                    $fecha_busqueda = 'Ini:'. $fecha_ini .'/Fin:'. $fecha_fin .'/IdFun:'. $id_firmante;
                    $clave_ar = array_search($fecha_busqueda, $historial);

                    if($clave_ar === false){ ##No esta en el historial entonces guardamos
                        $historial[] = $fecha_busqueda;
                        ##guardar en la bd el nuevo array en el campo historial del json
                        try {
                            $jsonHistorial = json_encode($historial);
                            DB::update('UPDATE tbl_funcionarios SET incapacidad = jsonb_set(incapacidad, \'{historial}\', ?) WHERE id = ?', [$jsonHistorial, $dataFirmante->id_fun]);
                        } catch (\Throwable $th) {
                            return redirect()->route('firma.inicio')->with('danger', 'Error: ' . $th->getMessage());
                        }

                    }
                }
            }

        }
        return $result;
    }

    public function rechazo(Request $request) {
        $curso = tbl_curso::Where('id', $request->txtIdRechazo)->First();
        $curso->observacion_asistencia_rechazo = $request->motivoRechazo;
        $curso->asis_finalizado = FALSE;
        $curso->save();

        return redirect()->route('firma.inicio')->with('success', 'Documento Rechazado Exitosamente!');
    }

    public function asistencia_pdf($id, $output = null) {
        $objeto = $dataFirmante = $uuid = $cadena_sello = $fecha_sello = $qrCodeBase64 = null;
        if ($id) {
            // $curso = tbl_cursos::where('clave', '=', $clave)->first();
            $curso = DB::Table('tbl_cursos')->select(
                'tbl_cursos.*',
                DB::raw('right(clave,4) as grupo'),
                'inicio',
                'termino',
                DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),
                DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
                'u.plantel',
                )->where('tbl_cursos.id',$id);
            $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')->first();
            if ($curso) {

                if ($curso->status_curso == "AUTORIZADO") {
                    $alumnos = DB::Table('tbl_inscripcion as i')->select(
                        'i.id',
                        'i.matricula',
                        'i.alumno',
                        'i.calificacion',
                        'f.folio',
                        'i.asistencias'
                    )->leftJoin('tbl_folios as f', function ($join) {
                        $join->on('f.id', '=', 'i.id_folio');
                    })->where('i.id_curso', $curso->id)
                        ->where('i.status', 'INSCRITO')
                        ->orderby('i.alumno')->get();
                    if (!$alumnos) return back()->with('warning', 'El curso esta en '.$cursos->status_curso);

                    foreach ($alumnos as $key => $value) {
                        $value->asistencias = json_decode($value->asistencias, true);
                    }
                    $mes = $this->mes;
                    $consec = 1;
                    if ($curso->inicio and $curso->termino) {
                        $inicio = explode('-', $curso->inicio); $inicio[2] = '01';
                        $termino = explode('-', $curso->termino); $termino[2] = '01';
                        $meses = $this->verMeses(array($inicio[0].'-'.$inicio[1].'-'.$inicio[2], $termino[0].'-'.$termino[1].'-'.$termino[2]));

                    } else  return "El Curso no tiene registrado la fecha de inicio y de termino";

                    // tbl_cursos::where('id', $curso->id)->update(['asis_finalizado' => true]);

                    //firmas electronicas
                    $documento = DocumentosFirmar::where('numero_o_clave', $curso->clave)
                        ->Where('tipo_archivo','Lista de asistencia')
                        ->Where('status','VALIDADO')
                        ->first();
                    if(isset($documento->uuid_sellado)){
                        $sellado = TRUE;
                        $objeto = json_decode($documento->obj_documento,true);
                        $no_oficio = json_decode(json_encode(simplexml_load_string($documento['documento_interno'], "SimpleXMLElement", LIBXML_NOCDATA),true));
                        // dd($no_oficio);
                        $no_oficio = $no_oficio->{'@attributes'}->no_oficio;
                        $uuid = $documento->uuid_sellado;
                        $cadena_sello = $documento->cadena_sello;
                        $fecha_sello = $documento->fecha_sellado;
                        $folio = $documento->nombre_archivo;
                        $tipo_archivo = $documento->tipo_archivo;
                        $totalFirmantes = $objeto['firmantes']['_attributes']['num_firmantes'];

                        // $dataFirmante = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre', 'fun.incapacidad')
                        //         ->Join('tbl_funcionarios AS fun','fun.id','org.id')
                        //         ->Where('org.id', Auth::user()->id_organismo)
                        //         ->Where('org.nombre', 'LIKE', 'DEPARTAMENTO ACADEMICO%')
                        //         ->OrWhere('org.id_parent', Auth::user()->id_organismo)
                        //         // ->Where('org.nombre', 'NOT LIKE', 'CENTRO%')
                        //         ->Where('org.nombre', 'LIKE', 'DEPARTAMENTO ACADEMICO%')
                        //         ->First();

                        ###Buscamos al funcionario y el puesto By Jose Luis
                        $puestoUsuario = $objeto['firmantes']['firmante'][0][1]['_attributes']['curp_firmante'];

                        $dataFirmante = DB::Table('tbl_organismos AS org')
                        ->Select('org.id', 'fun.nombre AS funcionario','fun.curp',
                        'fun.cargo','fun.correo', 'org.nombre', 'fun.incapacidad')
                            ->join('tbl_funcionarios AS fun', 'fun.id','org.id')
                            ->where('fun.curp', '=', $puestoUsuario)
                            ->first();
                        if($dataFirmante == null){return "No se encontraron datos del servidor publico";}


                        //Generacion de QR
                        //Verifica si existe link de verificiacion, de lo contrario lo crea y lo guarda
                        if(isset($documento->link_verificacion)) {
                            $verificacion = $documento->link_verificacion;
                        } else {
                            // $documento->link_verificacion = $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumentoPrueba/consulta/Certificado3?guid=$uuid&no_folio=$no_oficio";
                            $documento->link_verificacion = $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumento/consulta/Certificado3?guid=$uuid&no_folio=$no_oficio";
                            $documento->save();
                        }
                        ob_start();
                        QRcode::png($verificacion);
                        $qrCodeData = ob_get_contents();
                        ob_end_clean();
                        $qrCodeBase64 = base64_encode($qrCodeData);
                        // Fin de Generacion
                    }

                    $pdf = PDF::loadView('layouts.FirmaElectronica.reporteAsistencia', compact('curso', 'alumnos', 'mes', 'consec', 'meses','objeto','dataFirmante','uuid','cadena_sello','fecha_sello','qrCodeBase64'));
                    $pdf->setPaper('Letter', 'landscape');
                    $file = "ASISTENCIA_$id.PDF";

                    if($output == TRUE) {
                        if($sellado) {
                            return $pdf->output($file);
                        } else {
                            return null;
                        }
                    } else {
                        return $pdf->stream($file);
                    }

                    // if ($fecha_valida < 0) $message = "No prodece el registro de calificaciones, la fecha de termino del curso es el $curso->termino.";
                } // else $message = "El Curso fué $curso->status y turnado a $curso->turnado.";
            }
        }
    }

    private function create_body($id, $firmantes) {
        $curso = DB::Table('tbl_cursos')->select(
            'tbl_cursos.*',
            DB::raw('right(clave,4) as grupo'),
            'inicio',
            'termino',
            DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),
            DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
            'u.plantel',
            )->where('tbl_cursos.id',$id);
        $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')->first();
        $alumnos = DB::Table('tbl_inscripcion as i')->select(
            'i.id',
            'i.matricula',
            'i.alumno',
            'i.calificacion',
            'f.folio',
            'i.asistencias'
        )->leftJoin('tbl_folios as f', function ($join) {
            $join->on('f.id', '=', 'i.id_folio');
        })->where('i.id_curso', $curso->id)
            ->where('i.status', 'INSCRITO')
            ->orderby('i.alumno')->get();

        foreach ($alumnos as $key => $value) {
            $value->asistencias = json_decode($value->asistencias, true);
        }

        $mes = $this->mes;
        $consec = 1;
        $inicio = explode('-', $curso->inicio); $inicio[2] = '01';
        $termino = explode('-', $curso->termino); $termino[2] = '01';
        $meses = $this->verMeses(array($inicio[0].'-'.$inicio[1].'-'.$inicio[2], $termino[0].'-'.$termino[1].'-'.$termino[2]));

        $body = "SUBSECRETARÍA DE EDUCACIÓN MEDIA SUPERIOR\n".
        "DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO \n".
        "LISTA DE ASISTENCIA \n".
        "(LAD-04) \n";

        foreach($meses as $key => $mes) {
            $consec = 1;
            $body = $body. 'UNIDAD DE CAPACITACIÓN: '. $curso->plantel. ' '.   $curso->unidad. ' CLAVE CCT: '. $curso->cct. ' CICLO ESCOLAR: '. $curso->ciclo. ' GRUPO: '. $curso->grupo. ' MES: '. $mes['mes'] . ' AÑO: '. $mes['year'].
            "\n AREA: ". $curso->area. ' ESPECIALIDAD: '. $curso->espe. ' CURSO: '. $curso->curso. ' CLAVE: '. $curso->clave.
            "\n FECHA INICIO: ". $curso->fechaini. ' FECHA TERMINO: '. $curso->fechafin. ' HORARIO: '. $curso->dia. ' DE '. $curso->hini. ' A '. $curso->hfin. ' CURP: '. $curso->curp.
            "NUM NÚMERO DE CONTROL NOMBRE DEL ALUMNO PRIMER APELLIDO/SEGUNDO APELLIDO/NOMBRE(S) \n";
            foreach($mes['dias'] as $keyD => $dia){
                $body = ' '. $body. ' '. ($keyD+1);
            }
            $body = $body. ' TOTAL '. ' A I ';
            foreach($alumnos as $a) {
                $tAsis = 0;
                $tFalta = 0;
                $body = $body . "\n". $consec++. ' '. $a->matricula. ' '. $a->alumno. ' ';
                foreach($mes['dias'] as $dia) {
                    if($a->asistencias != null) {
                        foreach($a->asistencias as $asistencia) {
                            if($asistencia['fecha'] == $dia && $asistencia['asistencia'] == true) {
                                $body = $body. '* ';
                                $tAsis++;
                            } else if($asistencia['fecha'] == $dia && $asistencia['asistencia'] == false) {
                                $body = $body. 'x ';
                                $tFalta++;
                            }
                        }
                    }
                }
                $body = $body. $tAsis. ' '. $tFalta. ' ';
            }
        }

        return $body;
    }

    function verMeses($a) {
        $f1 = new DateTime($a[0]);
        $f2 = new DateTime($a[1]);

        // obtener la diferencia de fechas
        $d = $f1->diff($f2);
        $difmes =  $d->format('%m');
        $messs = $this->mes;

        $meses = [];
        $temp = [
            'fecha' => $f1->format('Y-m-d'),
            'ultimoDia' => date("Y-m-t", strtotime($f1->format('Y-m-d'))),
            'mes' => $messs[$f1->format('m')],
            'year' => $f1->format('Y'),
            'dias' => $this->getDays($f1->format('Y-m-d'), date("Y-m-t", strtotime($f1->format('Y-m-d'))))
        ];
        array_push($meses, $temp);

        $impf = $f1;
        for ($i = 1; $i <= $difmes; $i++) {
            // despliega los meses
            $impf->add(new DateInterval('P1M'));
            $temp = [
                'fecha' => $impf->format('Y-m-d'),
                'ultimoDia' => date("Y-m-t", strtotime($impf->format('Y-m-d'))),
                'mes' => $messs[$f1->format('m')],
                'year' => $impf->format('Y'),
                'dias' => $this->getDays($impf->format('Y-m-d'), date("Y-m-t", strtotime($impf->format('Y-m-d'))))
            ];
            array_push($meses, $temp);
        }
        return $meses;
    }

    function getDays($dateInicio, $dateFinal) {
        $dias = [];
        for ($i = $dateInicio; $i <= $dateFinal; $i = date("Y-m-d", strtotime($i . "+ 1 days"))) {
            array_push($dias, $i);
        }
        return $dias;
    }

    public function generarToken() {
        ## Producción
        $resToken = Http::withHeaders([
            'Accept' => 'application/json'
        ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
            'nombre' => 'SISTEM_IVINCAP',
            'key' => 'B8F169E9-C9F6-482A-84D8-F5CB788BC306'
        ]);

        ##Prueba
        // $resToken = Http::withHeaders([
        //     'Accept' => 'application/json'
        // ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
        //     'nombre' => 'FirmaElectronica',
        //     'key' => '19106D6F-E91F-4C20-83F1-1700B9EBD553'
        // ]);

        $token = $resToken->json();

        Tokens_icti::create([
            'token' => $token
        ]);
        return $token;
    }

    // obtener la cadena original
    public function getCadenaOriginal($xmlBase64, $token) {
        // dd(config('app.cadena'));
        // dd(Config::get('app.cadena', 'default'));

        ##Producción
        $response1 = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
            'xml_OriginalBase64' => $xmlBase64
        ]);

        ##Prueba
        // $response1 = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer '.$token,
        // ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
        //     'xml_OriginalBase64' => $xmlBase64
        // ]);

        return $response1;
    }
}
