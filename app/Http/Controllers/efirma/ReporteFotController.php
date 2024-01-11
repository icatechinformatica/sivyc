<?php

namespace App\Http\Controllers\efirma;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Spatie\ArrayToXml\ArrayToXml;
use App\Models\DocumentosFirmar;
use App\Models\Tokens_icti;
use App\Models\tbl_curso;
use PHPQRCode\QRcode;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use PDF;

class ReporteFotController extends Controller
{
    function __construct() {
        $this->mes = ["01" => "ENERO", "02" => "FEBRERO", "03" => "MARZO", "04" => "ABRIL", "05" => "MAYO", "06" => "JUNIO", "07" => "JULIO", "08" => "AGOSTO", "09" => "SEPTIEMBRE", "10" => "OCTUBRE", "11" => "NOVIEMBRE", "12" => "DICIEMBRE"];
        $this->path_files = env("APP_URL").'/storage/uploadFiles';
    }

    public function generar_xml(Request $request) {
        $info = DB::Table('tbl_cursos')->Select('tbl_unidades.*','tbl_cursos.clave','tbl_cursos.nombre','tbl_cursos.curp','instructores.correo')
                ->Join('tbl_unidades','tbl_unidades.unidad','tbl_cursos.unidad')
                ->join('instructores','instructores.id','tbl_cursos.id_instructor')
                ->Where('tbl_cursos.id',$request->txtIdValidado)
                ->First();

        $body = $this->create_body($request->txtIdValidado,$info); //creacion de body
        $body = str_replace(["\r", "\n", "\f"], ' ', $body);

        $nameFileOriginal = 'Reporte fotografico '.$info->clave.'.pdf';
        $numOficio = 'REPORTE-'.$info->clave;
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

        // $temp_anex = ['_attributes' =>
        //     [
        //         'nombre_anexo' => "evidencias-fotografias-23K.pdf",
        //         // 'md5_anexo' => $dataFirmante->funcionario,
        //     ]
        // ];

        // dd($arrayFirmantes);
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
            // 'anexos' => [
            //     '_attributes' => [
            //         'num_anexos' => "1"
            //         // 'md5_archivo' => $md5
            //         // 'checksum_archivo' => utf8_encode($text)
            //     ],
            //     // 'cuerpo' => ['Por medio de la presente me permito solicitar el archivo '.$nameFile]
            //     'anexo' => [
            //         $temp_anex
            //     ]
            // ],
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
                'asunto_docto' => 'Reporte fotografico',
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

            $dataInsert = DocumentosFirmar::Where('numero_o_clave',$info->clave)->Where('tipo_archivo','Reporte fotografico')->First();
            if(is_null($dataInsert)) {
                $dataInsert = new DocumentosFirmar();
            }
            $dataInsert->obj_documento = json_encode($ArrayXml);
            $dataInsert->obj_documento_interno = json_encode($ArrayXml);
            $dataInsert->status = 'EnFirma';
            // $dataInsert->link_pdf = $urlFile;
            $dataInsert->cadena_original = $response->json()['cadenaOriginal'];
            $dataInsert->tipo_archivo = 'Reporte fotografico';
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


    // Crear Cuerpo
    private function create_body($id, $firmantes) {
        #Distintivo
        $leyenda = DB::table('tbl_instituto')->value('distintivo');

        $curso = DB::Table('tbl_cursos')->select(
            'tbl_cursos.*',
            'inicio',
            'termino',
            DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),
            DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
            'u.dunidad',
            'u.municipio',
            'u.unidad',
            'u.ubicacion'
            )->where('tbl_cursos.id',$id);
        $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')->first();

        $valid_accionmovil = ($curso->unidad != $curso->ubicacion) ? ', Accion Movil '.$curso->unidad : ' ';

        $body = $leyenda.' '.
        'REPORTE FOTOGRÁFICO DEL INSTRUCTOR '.
        'Unidad de Capacitación '.$curso->municipio. $valid_accionmovil.
        $curso->municipio.', Chiapas. A 08 de septiembre de 2023. ';


        $body = $body. 'CURSO: '. $curso->curso.' TIPO: '. $curso->tcapacitacion. ' FECHA DE INICIO: '. $curso->fechaini. ' FECHA DE TÉRMINO: '. $curso->fechafin.
        ' CLAVE: '. $curso->clave. ' HORARIO: '. $curso->espe. ' CURSO: '. $curso->curso. ' CLAVE: '. $curso->clave.
        ' FECHA INICIO: '. $curso->fechaini. ' FECHA TERMINO: '. $curso->fechafin. ' HORARIO: '. $curso->hini. ' A '. $curso->hfin. ' NOMBRE DE LA TITULAR DE LA U.C: '. $curso->dunidad.
        ' NOMBRE DEL INSTRUCTOR: '. $curso->nombre;

        return $body;
    }


    #Rechazar documento pdf
    public function rechazo(Request $request) {
        try {
            $curso = tbl_curso::find($request->txtIdRechazo);
            $json = $curso->evidencia_fotografica;
            $json['status_validacion'] = 'RETORNADO';
            $json['observacion_reporte'] = $request->motivoRechazo;
            $curso->evidencia_fotografica = $json;
            $curso->save();
        } catch (\Throwable $th) {
            return redirect()->route('firma.inicio')->with('danger', 'Error al rechazar documento!');
        }

        return redirect()->route('firma.inicio')->with('warning', 'Documento Rechazado Exitosamente!');
    }

    ##Generacion de PDF en caso de que haya firma, mostralas.
    public function repofotoPdf($id){
        $path_files = $this->path_files;
        $array_fotos = [];
        $id_curso = $id;
        $fechapdf = "";
        $objeto = $dataFirmante = $uuid = $cadena_sello = $fecha_sello = $qrCodeBase64 =  null;

        #Distintivo
        $leyenda = DB::connection('pgsql')->table('tbl_instituto')->value('distintivo');

        #Unidad de capacitacion
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        // $dia = date('d'); $mes = date('m'); $anio = date('Y');
        // // $dia = ($dia) < 10 ? $dia : $dia;
        // $fecha_gen = $dia.' de '.$meses[$mes-1].' de '.$anio;

        $cursopdf = tbl_curso::select('nombre', 'curso', 'tcapacitacion', 'inicio', 'termino', 'evidencia_fotografica',
        'clave', 'hini', 'hfin', 'tbl_cursos.unidad', 'uni.dunidad', 'uni.ubicacion', 'uni.direccion', 'uni.municipio')
        ->join('tbl_unidades as uni', 'uni.unidad', 'tbl_cursos.unidad')
        ->where('tbl_cursos.id', '=', $id_curso)->first();

        if (isset($cursopdf->evidencia_fotografica['url_fotos'])){
            $array_fotos = $cursopdf->evidencia_fotografica['url_fotos'];
            if (isset($cursopdf->evidencia_fotografica["fecha_envio"])) {
                $fechapdf = $cursopdf->evidencia_fotografica["fecha_envio"];
                $fechaCarbon = Carbon::createFromFormat('Y-m-d', $fechapdf);
                $dia = ($fechaCarbon->day) < 10 ? '0'.$fechaCarbon->day : $fechaCarbon->day;
                $fechapdf = $dia.' de '.$meses[$fechaCarbon->month].' de '.$fechaCarbon->year;
            }
        }

        $base64Images = [];
        foreach ($array_fotos as $url) {
            $imageContent = file_get_contents(storage_path("app/public/uploadFiles".$url));
            $base64 = base64_encode($imageContent);
            $base64Images[] = $base64;
        }

        // $unidad = DB::connection('pgsql')->table('tbl_unidades')->select('dunidad')
        //     ->where('unidad', $curso->unidad)->first();
        if($cursopdf){
            //firmas electronicas
            $documento = DocumentosFirmar::where('numero_o_clave', $cursopdf->clave)
            ->Where('tipo_archivo','Reporte fotografico')
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
                    ->Where('org.nombre', 'LIKE', 'DELEGACIÓN ADMINISTRATIVA%')
                    ->OrWhere('org.id_parent', Auth::user()->id_organismo)
                    // ->Where('org.nombre', 'NOT LIKE', 'CENTRO%')
                    ->Where('org.nombre', 'LIKE', 'DELEGACIÓN ADMINISTRATIVA%')
                    ->First();

                // $dataFirmante = DB::Table('tbl_organismos AS org')->Select('org.id', 'fun.nombre AS funcionario','fun.curp',
                // 'fun.cargo','fun.correo', 'us.name', 'us.puesto')
                //     ->join('tbl_funcionarios AS fun', 'fun.id','org.id')
                //     ->join('users as us', 'us.email','fun.correo')
                //     ->where('org.nombre', 'ILIKE', 'DELEGACIÓN ADMINISTRATIVA UC '.$info->ubicacion.'%')
                //     ->first();
                // if($dataFirmante == null){
                //     return "NO SE ENCONTRON DATOS DEL FIRMANTE";
                // }

                //Generacion de QR
                $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumentoPrueba/consulta/Certificado3?guid=$uuid&no_folio=$no_oficio";
                ob_start();
                QRcode::png($verificacion);
                $qrCodeData = ob_get_contents();
                ob_end_clean();
                $qrCodeBase64 = base64_encode($qrCodeData);

            }
        }

        $pdf = PDF::loadView('layouts.FirmaElectronica.reporteFotografico', compact('cursopdf', 'leyenda', 'fechapdf', 'objeto','dataFirmante','uuid','cadena_sello','fecha_sello','qrCodeBase64', 'base64Images'));
        $pdf->setPaper('Letter', 'portrait');
        $file = "ASISTENCIA_$id_curso.PDF";
        return $pdf->stream($file);
    }

    public function generarToken() {
        $resToken = Http::withHeaders([
            'Accept' => 'application/json'
        ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
            // 'nombre' => 'SISTEM_IVINCAP',
            'nombre' => 'FirmaElectronica',
            'key' => '19106D6F-E91F-4C20-83F1-1700B9EBD553'
            // 'key' => 'B8F169E9-C9F6-482A-84D8-F5CB788BC306'
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
        ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
            'xml_OriginalBase64' => $xmlBase64
        ]);

        return $response1;
    }

}
