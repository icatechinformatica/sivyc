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

class CalificacionController extends Controller
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
        // dd($body);
        // $body = str_replace(["\r", "\n", "\f"], ' ', $body);

        $nameFileOriginal = 'Lista de calificaciones '.$info->clave.'.pdf';
        $numOficio = 'RESD-05-'.$info->clave;
        $numFirmantes = '2';

        $arrayFirmantes = [];

        $dataFirmante = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre')
                            ->Join('tbl_funcionarios AS fun','fun.id','org.id')
                            ->Where('org.id', Auth::user()->id_organismo)
                            ->Where('org.nombre', 'LIKE', 'DEPARTAMENTO ACADEMICO%')
                            ->OrWhere('org.id_parent', Auth::user()->id_organismo)
                            // ->Where('org.nombre', 'NOT LIKE', 'CENTRO%')
                            ->Where('org.nombre', 'LIKE', 'DEPARTAMENTO ACADEMICO%')
                            ->First();
        if($dataFirmante->curp == null)
        {
            return redirect()->route('firma.inicio')->with('Danger', 'Error: La curp de un firmante no se encuentra');
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
                'asunto_docto' => 'Registro de evalucación por subobjetivos RESD-05',
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

            $dataInsert = DocumentosFirmar::Where('numero_o_clave',$info->clave)->Where('tipo_archivo','Lista de calificaciones')->First();
            if(is_null($dataInsert)) {
                $dataInsert = new DocumentosFirmar();
            }
            $dataInsert->obj_documento = json_encode($ArrayXml);
            $dataInsert->obj_documento_interno = json_encode($ArrayXml);
            $dataInsert->status = 'EnFirma';
            // $dataInsert->link_pdf = $urlFile;
            $dataInsert->cadena_original = $response->json()['cadenaOriginal'];
            $dataInsert->tipo_archivo = 'Lista de calificaciones';
            $dataInsert->numero_o_clave = $info->clave;
            $dataInsert->nombre_archivo = $nameFileOriginal;
            $dataInsert->documento = $result;
            $dataInsert->documento_interno = $result;
            // $dataInsert->md5_file = $md5;
            $dataInsert->save();

            return redirect()->route('firma.inicio')->with('success', 'Lista de Calificaciones Validado Exitosamente!');
        } else {
            return redirect()->route('firma.inicio')->with('danger', 'Hubo un Error al Validar. Intente Nuevamente en unos Minutos.');
        }
    }

    public function rechazo(Request $request) {
        $curso = tbl_curso::Where('id', $request->txtIdRechazo)->First();
        $curso->observacion_calificacion_rechazo = $request->motivoRechazo;
        $curso->calif_finalizado = FALSE;
        $curso->save();

        return redirect()->route('firma.inicio')->with('success', 'Documento Rechazado Exitosamente!');
    }

    public function calificacion_pdf($id) {
        $objeto = $dataFirmante = $uuid = $cadena_sello = $fecha_sello = $qrCodeBase64 = null;
        if($id) {
            $curso = DB::table('tbl_cursos')->select(
                'tbl_cursos.*',
                DB::raw('right(clave,4) as grupo'),
                DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),
                DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
                'u.plantel'
            )->where('tbl_cursos.id',$id);
            // if($_SESSION['unidades']) $curso = $curso->whereIn('u.ubicacion',$_SESSION['unidades']);
            $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')->first();
            if($curso) {
                $consec_curso = $curso->id_curso;
                $fecha_termino = $curso->inicio;
                $alumnos = DB::table('tbl_inscripcion as i')->select(
                        'i.matricula',
                        'i.alumno',
                        'i.calificacion'
                    )->where('i.id_curso',$curso->id)
                    ->where('i.status','INSCRITO')
                    ->groupby('i.matricula','i.alumno','i.calificacion')
                    ->orderby('i.alumno')
                    ->get();
                if(count($alumnos)==0){
                    return "NO HAY ALUMNOS INSCRITOS";
                    exit;
                }

                //firmas electronicas
                $documento = DocumentosFirmar::where('numero_o_clave', $curso->clave)
                ->Where('tipo_archivo','Lista de calificaciones')
                ->Where('status','VALIDADO')
                ->first();
            if(isset($documento->uuid_sellado)){
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

                $dataFirmante = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre')
                        ->Join('tbl_funcionarios AS fun','fun.id','org.id')
                        ->Where('org.id', Auth::user()->id_organismo)
                        ->Where('org.nombre', 'LIKE', 'DEPARTAMENTO ACADEMICO%')
                        ->OrWhere('org.id_parent', Auth::user()->id_organismo)
                        // ->Where('org.nombre', 'NOT LIKE', 'CENTRO%')
                        ->Where('org.nombre', 'LIKE', 'DEPARTAMENTO ACADEMICO%')
                        ->First();

                //Generacion de QR
                //Verifica si existe link de verificiacion, de lo contrario lo crea y lo guarda
                if(isset($documento->link_verificacion)) {
                    $verificacion = $documento->link_verificacion;
                } else {
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

                $consec = 1;
                $pdf = PDF::loadView('layouts.FirmaElectronica.pdfCalificaciones', compact('curso','alumnos','consec','objeto','dataFirmante','uuid','cadena_sello','fecha_sello','qrCodeBase64'));
                $pdf->setPaper('Letter', 'landscape');
                $file = "CALIFICACIONES_$curso->clave.PDF";
                return $pdf->stream($file);
            } else return "Curso no v&aacute;lido para esta Unidad";
        }
        return "Clave no v&aacute;lida";
    }

    private function create_body($id, $firmantes) {
        $curso = DB::table('tbl_cursos')->select(
            'tbl_cursos.*',
            DB::raw('right(clave,4) as grupo'),
            DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),
            DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
            'u.plantel'
        )->where('tbl_cursos.id',$id);
        // if($_SESSION['unidades']) $curso = $curso->whereIn('u.ubicacion',$_SESSION['unidades']);
        $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')->first();
        if($curso) {
            $consec_curso = $curso->id_curso;
            $fecha_termino = $curso->inicio;
            $alumnos = DB::table('tbl_inscripcion as i')->select(
                    'i.matricula',
                    'i.alumno',
                    'i.calificacion'
                )->where('i.id_curso',$curso->id)
                ->where('i.status','INSCRITO')
                ->groupby('i.matricula','i.alumno','i.calificacion')
                ->orderby('i.alumno')
                ->get();
            $consec = 1;
            $body = "SUBSECRETARÍA DE EDUCACIÓN E INVESTIGACIÓN TECNOLÓGICAS \n".
            "DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO \n".
            "REGISTRO DE EVALUACIÓN POR SUBOBJETIVOS \n".
            "(RESD-05) ".
            "UNIDAD DE CAPACITACIÓN: ". $curso->plantel. ' '.   $curso->unidad. ' CLAVE CCT: '. $curso->cct. ' AREA: '. $curso->area. ' ESPECIALIDAD: '. $curso->espe.
            "\n CURSO: ". $curso->curso. ' CLAVE: '. $curso->clave. ' CICLO ESCOLAR: '. $curso->ciclo. ' FECHA INICIO: '. $curso->fechaini. ' FECHA TERMINO: '. $curso->fechafin.
            "\n GRUPO: ". $curso->grupo. ' HORARIO: '. $curso->dia. ' DE '. $curso->hini. ' A '. $curso->hfin. ' CURP: '. $curso->curp.
            "\n NUM NúMERO DE CONTROL NOMBRE DEL ALUMNO PRIMER APELLIDO/SEGUNDO APELLIDO/NOMBRE(S) CLAVE DE CADA SUBOBJETIVO RESULTADO RESULTADO FINAL";
                    foreach ($alumnos as $a) {
                        $body = $body. "\n". ($consec++). ' '. $a->matricula. ' '. $a->alumno. ' '. $a->calificacion;
                    }
            return $body;
        } else return "Curso no válido para esta Unidad";
    }

    public function generarToken() {

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

    // obtener la cadena original
    public function getCadenaOriginal($xmlBase64, $token) {
        // dd(config('app.cadena'));
        // dd(Config::get('app.cadena', 'default'));
        $response1 = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
            'xml_OriginalBase64' => $xmlBase64
        ]);

        //api prueba
        // $response1 = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer '.$token,
        // ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
        //     'xml_OriginalBase64' => $xmlBase64
        // ]);

        return $response1;
    }
}
