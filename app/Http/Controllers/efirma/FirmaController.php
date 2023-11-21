<?php

namespace App\Http\Controllers\efirma;


// use QrCode;
use setasign\Fpdi\Fpdi;
use App\Models\DocumentosFirmar;
use Illuminate\Http\Request;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\tbl_curso;
use App\Models\Tokens_icti;
use App\Models\contratos;
use App\Models\contrato_directorio;
use App\Models\directorio;
use App\Models\folio;
use App\Models\especialidad_instructor;
// use BaconQrCode\Encoder\QrCode;
use Illuminate\Support\Facades\Http;
use Vyuldashev\XmlToArray\XmlToArray;
use Illuminate\Support\Facades\Storage;
use \setasign\Fpdi\PdfParser\StreamReader;
use PDF;
use PHPQRCode\QRcode;
use Carbon\Carbon;

// use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FirmaController extends Controller {

    // php artisan serve --port=8001
    public function index(Request $request) {
        $docsVistobueno2 = array();
        $email = Auth::user()->email;
        $rol = DB::Table('role_user')->Select('role_id')->Where('user_id', Auth::user()->id)->First();
        $unidad_user = DB::Table('tbl_unidades')->Where('id',Auth::user()->unidad)->Value('ubicacion');
        // if($rol->role_id == 30 || $rol->role_id == 31) {
            // $docsVistoBueno2 = tbl_curso::select('tbl_cursos.id', 'tbl_cursos.nombre', 'tbl_cursos.asis_finalizado', 'tbl_cursos.calif_finalizado')
            //     ->leftJoin('documentos_firmar', 'documentos_firmar.numero_o_clave', 'tbl_cursos.clave')
            //     // ->whereIn('tipo_archivo', ['Lista de asistencia', 'Lista de calificaciones'])
            //     ->where(function ($query) {
            //         $query->where('asis_finalizado', true)
            //             ->orWhere('calif_finalizado', true);
            //     })
            //     ->orderByDesc('clave')
            //     ->get();

            $docsVistoBueno2 = tbl_curso::select(
                'tbl_cursos.id',
                'tbl_cursos.nombre',
                'tbl_cursos.asis_finalizado',
                'tbl_cursos.calif_finalizado',
                'tbl_cursos.clave',
                \DB::raw("
                    CASE
                        WHEN tipo_archivo = 'Lista de asistencia' THEN
                            CASE WHEN 'Lista de calificaciones' IS NULL THEN 'Ambos' ELSE 'Lista de calificaciones' END
                        WHEN tipo_archivo = 'Lista de calificaciones' THEN
                            CASE WHEN 'Lista de asistencia' IS NULL THEN 'Ambos' ELSE 'Lista de asistencia' END
                        ELSE 'NA'
                    END AS tipo_archivo_faltante"
                ),
                \DB::raw("
                CASE
                WHEN tipo_archivo = 'Lista de asistencia' AND documentos_firmar.status = 'CANCELADO' THEN 'asistencia cancelada'
                WHEN tipo_archivo = 'Lista de calificaciones' AND documentos_firmar.status = 'CANCELADO' THEN
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM documentos_firmar df2
                            WHERE df2.numero_o_clave = documentos_firmar.numero_o_clave
                            AND df2.tipo_archivo = 'Lista de asistencia'
                            AND df2.status = 'CANCELADO'
                        ) THEN 'ambos'
                        ELSE 'calificaciones canceladas'
                    END
                ELSE 'NA'
                END AS archivo_cancelado"
                )
            )
            ->leftJoin('documentos_firmar', 'documentos_firmar.numero_o_clave', 'tbl_cursos.clave')
            ->Join('tbl_unidades','tbl_unidades.unidad','tbl_cursos.unidad')
            ->Where('tbl_unidades.ubicacion',$unidad_user)
            ->where(function ($query) {
                $query->where('asis_finalizado', true)
                    ->orWhere('calif_finalizado', true);
            })
            ->whereNotIn('tbl_cursos.clave', function ($subquery) {
                $subquery->select('numero_o_clave')
                    ->from('documentos_firmar')
                    ->whereIn('tipo_archivo', ['Lista de asistencia', 'Lista de calificaciones'])
                    ->Where('status', '!=', 'CANCELADO')
                    ->Where('status', '!=', 'VALIDADO')
                    ->groupBy('numero_o_clave')
                    ->havingRaw('COUNT(DISTINCT tipo_archivo) > 1');
            })
            ->orderByDesc('tbl_cursos.clave')
            ->get();
            // dd($docsVistoBueno2);
        // }
        $docsFirmar1 = DocumentosFirmar::where('documentos_firmar.status','!=','CANCELADO')
            ->Select('documentos_firmar.*','tbl_cursos.id as idcursos','contratos.id_contrato')
            ->Join('tbl_cursos','tbl_cursos.clave','documentos_firmar.numero_o_clave')
            ->LeftJoin('folios','folios.id_cursos','tbl_cursos.id')
            ->LeftJoin('contratos','contratos.id_folios','folios.id_folios')
            ->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x
                WHERE x->'_attributes'->>'email_firmante' IN ('".$email."')
                AND x->'_attributes'->>'firma_firmante' is null)");
            // ->orderBy('id', 'desc')->get();


        $docsFirmados1 = DocumentosFirmar::where('documentos_firmar.status', 'EnFirma')
            ->Select('documentos_firmar.*','tbl_cursos.id as idcursos','contratos.id_contrato')
            ->Join('tbl_cursos','tbl_cursos.clave','documentos_firmar.numero_o_clave')
            ->LeftJoin('folios','folios.id_cursos','tbl_cursos.id')
            ->LeftJoin('contratos','contratos.id_folios','folios.id_folios')
            ->where(function ($query) use ($email) {
                $query->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x
                    WHERE x->'_attributes'->>'email_firmante' IN ('".$email."')
                    AND x->'_attributes'->>'firma_firmante' <> '')")
                ->orWhere(function($query1) use ($email) {
                    $query1->where('obj_documento_interno->emisor->_attributes->email', $email)
                            ->where('documentos_firmar.status', 'EnFirma');
                });
            });
            // ->orderBy('id', 'desc')->get();

        $docsValidados1 = DocumentosFirmar::where('documentos_firmar.status', 'VALIDADO')
            ->Select('documentos_firmar.*','tbl_cursos.id as idcursos','contratos.id_contrato')
            ->Join('tbl_cursos','tbl_cursos.clave','documentos_firmar.numero_o_clave')
            ->LeftJoin('folios','folios.id_cursos','tbl_cursos.id')
            ->LeftJoin('contratos','contratos.id_folios','folios.id_folios')
            ->where(function ($query) use ($email) {
                $query->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x
                    WHERE x->'_attributes'->>'email_firmante' IN ('".$email."'))")
                ->orWhere(function($query1) use ($email) {
                    $query1->where('obj_documento_interno->emisor->_attributes->email', $email)
                            ->where('documentos_firmar.status', 'VALIDADO');
                });
            });
            // ->orderBy('id', 'desc')->get();

        $docsCancelados1 = DocumentosFirmar::where('status', 'CANCELADO')
            ->where(function ($query) use ($email) {
                $query->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x
                    WHERE x->'_attributes'->>'email_firmante' IN ('".$email."'))")
                ->orWhere(function($query1) use ($email) {
                    $query1->where('obj_documento_interno->emisor->_attributes->email', $email)
                            ->where('status', 'CANCELADO');
                });
            });
            // ->orderBy('id', 'desc')->get();

        $tipo_documento = $request->tipo_documento;
        // if ($tipo_documento != null) {
            session(['tipo' => $tipo_documento]);
        // }
        $tipo_documento = session('tipo');

        if($tipo_documento == null) {
            $docsFirmar = $docsFirmar1->orderBy('id', 'desc')->get();
            $docsFirmados = $docsFirmados1->orderBy('id', 'desc')->get();
            $docsValidados = $docsValidados1->orderBy('id', 'desc')->get();
            $docsCancelados = $docsCancelados1->orderBy('id', 'desc')->get();
        } else {
            $docsFirmar = $docsFirmar1->where('tipo_archivo', $tipo_documento)->orderBy('id', 'desc')->get();
            $docsFirmados = $docsFirmados1->where('tipo_archivo', $tipo_documento)->orderBy('id', 'desc')->get();
            $docsValidados = $docsValidados1->where('tipo_archivo', $tipo_documento)->orderBy('id', 'desc')->get();
            $docsCancelados = $docsCancelados1->where('tipo_archivo', $tipo_documento)->orderBy('id', 'desc')->get();
        }

        foreach ($docsFirmar as $value) {
            $value->base64xml = base64_encode($value->documento);
        }

        $getToken = Tokens_icti::all()->last();

        if (!isset($token)) {// no hay registros
            $token = $this->generarToken($request);
        } else
        {
            $token = $getToken->token;
        }
        // dd($docsFirmados);
        return view('layouts.firmaElectronica.firmaElectronica', compact('docsFirmar', 'email', 'docsFirmados', 'docsValidados', 'docsCancelados', 'tipo_documento', 'token','docsVistoBueno2','rol'));
    }

    public function update(Request $request) {
        $documento = DocumentosFirmar::where('id', $request->idFile)->first();
        // dd($documento);

        $obj_documento = json_decode($documento->obj_documento, true);
        $obj_documento_interno = json_decode($documento->obj_documento_interno, true);

        if (empty($obj_documento['archivo']['_attributes']['md5_archivo'])) {
            $obj_documento['archivo']['_attributes']['md5_archivo'] = $documento->md5_file;
        }

        foreach ($obj_documento['firmantes']['firmante'][0] as $key => $value) {
            if ($value['_attributes']['curp_firmante'] == $request->curp) {
                $value['_attributes']['fecha_firmado_firmante'] = $request->fechaFirmado;
                $value['_attributes']['no_serie_firmante'] = $request->serieFirmante;
                $value['_attributes']['firma_firmante'] = $request->firma;
                $value['_attributes']['certificado'] = $request->certificado;
                $obj_documento['firmantes']['firmante'][0][$key] = $value;
            }
        }
        // foreach ($obj_documento_interno['firmantes']['firmante'][0] as $key => $value) {
        //     if ($value['_attributes']['curp_firmante'] == $request->curp) {
        //         $value['_attributes']['fecha_firmado_firmante'] = $request->fechaFirmado;
        //         $value['_attributes']['no_serie_firmante'] = $request->serieFirmante;
        //         $value['_attributes']['firma_firmante'] = $request->firma;
        //         $value['_attributes']['certificado'] = $request->certificado;
        //         $obj_documento_interno['firmantes']['firmante'][0][$key] = $value;
        //     }
        // }

        $array = XmlToArray::convert($documento->documento);
        // $array2 = XmlToArray::convert($documento->documento_interno);
        $array['DocumentoChis']['firmantes'] = $obj_documento['firmantes'];
        // $array2['DocumentoChis']['firmantes'] = $obj_documento_interno['firmantes'];

        $result = ArrayToXml::convert($obj_documento, [
            'rootElementName' => 'DocumentoChis',
            '_attributes' => [
                'version' => $array['DocumentoChis']['_attributes']['version'],
                'fecha_creacion' => $array['DocumentoChis']['_attributes']['fecha_creacion'],
                'no_oficio' => $array['DocumentoChis']['_attributes']['no_oficio'],
                'dependencia_origen' => $array['DocumentoChis']['_attributes']['dependencia_origen'],
                'asunto_docto' => $array['DocumentoChis']['_attributes']['asunto_docto'],
                'tipo_docto' => $array['DocumentoChis']['_attributes']['tipo_docto'],
                'xmlns' => 'http://firmaelectronica.chiapas.gob.mx/GCD/DoctoGCD',
            ],
        ]);

        // $result2 = ArrayToXml::convert($obj_documento_interno, [
        //     'rootElementName' => 'DocumentoChis',
        //     '_attributes' => [
        //         'version' => $array2['DocumentoChis']['_attributes']['version'],
        //         'fecha_creacion' => $array2['DocumentoChis']['_attributes']['fecha_creacion'],
        //         'no_oficio' => $array2['DocumentoChis']['_attributes']['no_oficio'],
        //         'dependencia_origen' => $array2['DocumentoChis']['_attributes']['dependencia_origen'],
        //         'asunto_docto' => $array2['DocumentoChis']['_attributes']['asunto_docto'],
        //         'tipo_docto' => $array2['DocumentoChis']['_attributes']['tipo_docto'],
        //         'xmlns' => 'http://firmaelectronica.chiapas.gob.mx/GCD/DoctoGCD',
        //     ],
        // ]);

        DocumentosFirmar::where('id', $request->idFile)
            ->update([
                'obj_documento' => json_encode($obj_documento),
                // 'obj_documento_interno' => json_encode($obj_documento_interno),
                'documento' => $result,
                // 'documento_interno' => $result2
            ]);

        return redirect()->route('firma.inicio')->with('warning', 'Documento firmado exitosamente!');
    }

    public function sellar(Request $request) {
        $documento = DocumentosFirmar::where('id', $request->txtIdFirmado)->first();
        $xmlBase64 = base64_encode($documento->documento);

        $getToken = Tokens_icti::all()->last();
        $response = $this->sellarFile($xmlBase64, $getToken->token);
        if ($response->json() == null) {
            $request = new Request();
            $token = $this->generarToken($request);
            $response = $this->sellarFile($xmlBase64, $token);
        }

        if ($response->json()['status'] == 1) { //exitoso
            $decode = base64_decode($response->json()['xml']);
            DocumentosFirmar::where('id', $request->txtIdFirmado)
                ->update([
                    'status' => 'VALIDADO',
                    'uuid_sellado' => $response->json()['uuid'],
                    'fecha_sellado' => $response->json()['fecha_Sellado'],
                    'documento' => $decode,
                    'cadena_sello' => $response->json()['cadenaSello']
                ]);
            return redirect()->route('firma.inicio')->with('warning', 'Documento validado exitosamente!');
        } else {
            return redirect()->route('firma.inicio')->with('danger', 'Ocurrio un error al sellar el documento, por favor intente de nuevo');
        }
    }

    public function generarPDF(Request $request) {
        $documento = DocumentosFirmar::where('id', $request->txtIdGenerar)->first();
        $objeto = json_decode($documento->obj_documento_interno,true);
        $no_oficio = json_decode(json_encode(simplexml_load_string($documento['documento_interno'], "SimpleXMLElement", LIBXML_NOCDATA),true));
        // dd($no_oficio);
        $no_oficio = $no_oficio->{'@attributes'}->no_oficio;
        $uuid = $documento->uuid_sellado;
        $cadena_sello = $documento->cadena_sello;
        $fecha_sello = $documento->fecha_sellado;
        $folio = $documento->nombre_archivo;
        $tipo_archivo = $documento->tipo_archivo;
        $totalFirmantes = $objeto['firmantes']['_attributes']['num_firmantes'];

        if ($documento->tipo_archivo == 'Contrato') {
            $contrato = new contratos();
            $data_contrato = contratos::SELECT('contratos.*')
                            ->JOIN('folios', 'folios.id_folios', 'contratos.id_folios')
                            ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
                            ->WHERE('tbl_cursos.clave', '=', $documento->numero_o_clave)
                            ->FIRST();

            $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $data_contrato->id_contrato)->FIRST();
            $director = directorio::WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
            $testigo1 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
            $testigo2 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
            $testigo3 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

            $data = $contrato::SELECT('folios.id_folios','folios.importe_total','tbl_cursos.id','tbl_cursos.horas',
                                    'tbl_cursos.tipo_curso','tbl_cursos.espe', 'tbl_cursos.clave','instructores.nombre','instructores.apellidoPaterno',
                                    'instructores.apellidoMaterno','tbl_cursos.instructor_tipo_identificacion','tbl_cursos.instructor_folio_identificacion','instructores.rfc','tbl_cursos.modinstructor',
                                    'instructores.curp','instructores.domicilio')
                            ->WHERE('folios.id_folios', '=', $data_contrato->id_folios)
                            ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                            ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                            ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                            ->FIRST();
                            //nomes especialidad
            $especialidad = especialidad_instructor::SELECT('especialidades.nombre')
                                                    ->WHERE('especialidad_instructores.id', '=', $data_contrato->instructor_perfilid)
                                                    ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
                                                    ->FIRST();

            $fecha_act = new Carbon('23-06-2022');
            $fecha_fir = new Carbon($data_contrato->fecha_firma);
            $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
            $date = strtotime($data_contrato->fecha_firma);
            $D = date('d', $date);
            $M = $this->toMonth(date('m', $date));
            $Y = date("Y", $date);

            $cantidad = $this->numberFormat($data_contrato->cantidad_numero);
            $monto = explode(".",strval($data_contrato->cantidad_numero));

            //Generacion de QR
            $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumentoPrueba/consulta/Certificado3?guid=$uuid&no_folio=$no_oficio";
            ob_start();
            QRcode::png($verificacion);
            $qrCodeData = ob_get_contents();
            ob_end_clean();
            $qrCodeBase64 = base64_encode($qrCodeData);
            // Fin de Generacion

            if($data->tipo_curso == 'CURSO')
            {
                if ($data->modinstructor == 'HONORARIOS') {
                    $pdf = PDF::loadView('layouts.firmaElectronica.contratohonorarios', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad','fecha_act','fecha_fir','no_oficio','objeto','uuid','qrCodeBase64','verificacion','cadena_sello','fecha_sello'));
                }else {
                    $pdf = PDF::loadView('layouts.firmaElectronica.contratohasimilados', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad','fecha_act','fecha_fir','no_oficio','objeto','uuid','qrCodeBase64','verificacion','cadena_sello','fecha_sello'));
                }
            }
            else
            {
                $pdf = PDF::loadView('layouts.firmaElectronica.contratocertificacion', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto','especialidad','cantidad','fecha_act','fecha_fir','no_oficio','objeto','uuid','qrCodeBase64','verificacion','cadena_sello','fecha_sello'));
            }
            return $pdf->stream("Contrato-Instructor-$data_contrato->numero_contrato.pdf");

        } else {
            $url = $documento->link_pdf;
            $unity = explode('/', $url);
            $path = storage_path('app/public/uploadFiles/DocumentosFirmas/'.$unity[6].'/'.$documento->nombre_archivo);
            $result = str_replace('\\','/', $path);
            $pageCount =  $pdf->setSourceFile($result);
        }
    }

    public function cancelarDocumento(Request $request) {
        // dd($request);
        $date = date('Y-m-d H:i:s');

        if ($request->motivo != null) {
            $data = [
                'usuario' => Auth::user()->name,
                'id' => Auth::user()->id,
                'motivo' => $request->motivo,
                'fecha' => $date,
                'correo' => Auth::user()->email
            ];

            DocumentosFirmar::where('id', $request->txtIdCancel)
                ->update([
                    'status' => 'CANCELADO',
                    'cancelacion' => $data
                ]);
            tbl_curso::where('clave', $request->txtClave)
                ->update(
                    $request->txtTipo == 'Lista de asistencia'
                        ? ['asis_finalizado' => false]
                        : ($request->txtTipo == 'Lista de calificaciones'
                            ?  ['calif_finalizado' => false]
                            : [])
                );

            if($request->txtTipo == 'Contrato') {
                $folio = folio::Join('tbl_cursos','tbl_cursos.id','folios.id_cursos')
                ->Where('clave',$request->txtClave)
                ->Value('folios.id_folios');

                folio::Where('id_folios', $folio)
                ->update(
                        ['status' => 'Capturando']
                );
            }
            return redirect()->route('firma.inicio')->with('warning', 'Documento cancelado exitosamente!');
        } else {
            return redirect()->route('firma.inicio')->with('danger', 'Debe ingresar el motivo de cancelación');
        }
    }

    public function generarToken(Request $request) {
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

    public function sellarFile($xml, $token) {
        $response1 = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/NotariaXML/sellarXML', [
            'xml_Firmado' => $xml
        ]);
        return $response1;
    }

    protected function toMonth($m)
    {
        switch ($m) {
            case 1:
                return "Enero";
            break;
            case 2:
                return "Febrero";
            break;
            case 3:
                return "Marzo";
            break;
            case 4:
                return "Abril";
            break;
            case 5:
                return "Mayo";
            break;
            case 6:
                return "Junio";
            break;
            case 7:
                return "Julio";
            break;
            case 8:
                return "Agosto";
            break;
            case 9:
                return "Septiembre";
            break;
            case 10:
                return "Octubre";
            break;
            case 11:
                return "Noviembre";
            break;
            case 12:
                return "Diciembre";
            break;


        }
    }

    protected function numberFormat($numero)
    {
        $part = explode(".", $numero);
        $part[0] = number_format($part['0']);
        $cadwell = implode(".", $part);
        return ($cadwell);
    }


}
