<?php

namespace App\Http\Controllers\efirma;


// use QrCode;
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
use PDF;
use PHPQRCode\QRcode;
use Carbon\Carbon;

// use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FirmaController extends Controller {

    // php artisan serve --port=8001
    public function index(Request $request) {

        $seleccion = request('section'); ##Paginacion
        $seleccion2 = $request->seccion; ##Busqueda

        if($seleccion == null){$seleccion = $seleccion2;}

        if ($seleccion != null) {session(['seccion' => $seleccion]);}
        else if ($seleccion2 != null) {session(['seccion' => $seleccion2]);}
        $seleccion = session('seccion');

        $docsVistobueno2 = array();
        $email = Auth::user()->email;
        $rol = DB::Table('role_user')->Select('role_id')->Where('user_id', Auth::user()->id)->First();
        $unidad_user = DB::Table('tbl_unidades')->Where('id',Auth::user()->unidad)->Value('ubicacion');
        $curpUser = DB::Table('users')->Select('tbl_funcionarios.curp')
            ->Join('tbl_funcionarios','tbl_funcionarios.correo','users.email')
            ->Where('users.id', Auth::user()->id)
            ->First();

        if($rol->role_id == '31' || $rol->role_id == '47'){
            $curpUser = DB::Table('users')->Select('tbl_funcionarios.curp')
                ->Join('tbl_funcionarios','tbl_funcionarios.id_org','users.id_organismo')
                ->Where('users.id', Auth::user()->id)
                ->First();
            }
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

            // $docsVistoBueno2 = tbl_curso::select(
            //     'tbl_cursos.id',
            //     'tbl_cursos.nombre',
            //     'tbl_cursos.asis_finalizado',
            //     'tbl_cursos.calif_finalizado',
            //     'tbl_cursos.clave',
            //     DB::raw("
            //         CASE
            //             WHEN tipo_archivo = 'Lista de asistencia' THEN
            //                 CASE WHEN 'Lista de calificaciones' IS NULL THEN 'Ambos' ELSE 'Lista de calificaciones' END
            //             WHEN tipo_archivo = 'Lista de calificaciones' THEN
            //                 CASE WHEN 'Lista de asistencia' IS NULL THEN 'Ambos' ELSE 'Lista de asistencia' END
            //             ELSE 'NA'
            //         END AS tipo_archivo_faltante"
            //     ),
            //     DB::raw("
            //     CASE
            //     WHEN tipo_archivo = 'Lista de asistencia' AND documentos_firmar.status = 'CANCELADO' THEN 'asistencia cancelada'
            //     WHEN tipo_archivo = 'Lista de calificaciones' AND documentos_firmar.status = 'CANCELADO' THEN
            //         CASE
            //             WHEN EXISTS (
            //                 SELECT 1
            //                 FROM documentos_firmar df2
            //                 WHERE df2.numero_o_clave = documentos_firmar.numero_o_clave
            //                 AND df2.tipo_archivo = 'Lista de asistencia'
            //                 AND df2.status = 'CANCELADO'
            //             ) THEN 'ambos'
            //             ELSE 'calificaciones canceladas'
            //         END
            //     ELSE 'NA'
            //     END AS archivo_cancelado"
            //     )
            // )
            // ->leftJoin('documentos_firmar', 'documentos_firmar.numero_o_clave', 'tbl_cursos.clave')
            // ->Join('tbl_unidades','tbl_unidades.unidad','tbl_cursos.unidad')
            // ->Where('tbl_unidades.ubicacion',$unidad_user)
            // ->where(function ($query) {
            //     $query->where('asis_finalizado', true)
            //         ->orWhere('calif_finalizado', true);
            // })
            // ->whereNotIn('tbl_cursos.clave', function ($subquery) {
            //     $subquery->select('numero_o_clave')
            //         ->from('documentos_firmar')
            //         ->whereIn('tipo_archivo', ['Lista de asistencia', 'Lista de calificaciones'])
            //         ->WhereIn('status', ['CANCELADO','VALIDADO','EnFirma'])
            //         // ->Where('status', '=', 'VALIDADO')
            //         ->groupBy('numero_o_clave')
            //         ->havingRaw('COUNT(DISTINCT tipo_archivo) > 1');
            // })
            // ->orderByDesc('tbl_cursos.clave')
            // ->get();
            // dd($docsVistoBueno2);
        // }
        $docsFirmar1 = DocumentosFirmar::where('documentos_firmar.status','!=','CANCELADO')
            ->Select('documentos_firmar.*','tbl_cursos.id as idcursos','contratos.id_contrato', 'tbl_cursos.folio_grupo')
            ->Join('tbl_cursos','tbl_cursos.clave','documentos_firmar.numero_o_clave')
            ->LeftJoin('folios','folios.id_cursos','tbl_cursos.id')
            ->LeftJoin('contratos','contratos.id_folios','folios.id_folios')
            ->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x
                WHERE x->'_attributes'->>'curp_firmante' IN ('".$curpUser->curp."')
                AND x->'_attributes'->>'firma_firmante' is null)");
            // ->orderBy('id', 'desc')->get();


        $docsFirmados1 = DocumentosFirmar::where('documentos_firmar.status', 'EnFirma')
            ->Select('documentos_firmar.*','tbl_cursos.id as idcursos','contratos.id_contrato', 'tbl_cursos.folio_grupo')
            ->Join('tbl_cursos','tbl_cursos.clave','documentos_firmar.numero_o_clave')
            ->LeftJoin('folios','folios.id_cursos','tbl_cursos.id')
            ->LeftJoin('contratos','contratos.id_folios','folios.id_folios')
            ->where(function ($query) use ($email,$curpUser) {
                $query->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x
                    WHERE x->'_attributes'->>'curp_firmante' IN ('".$curpUser->curp."')
                    AND x->'_attributes'->>'firma_firmante' <> '')")
                ->orWhere(function($query1) use ($email) {
                    $query1->where('obj_documento_interno->emisor->_attributes->email', $email)
                            ->where('documentos_firmar.status', 'EnFirma');
                });
            });
            // ->orderBy('id', 'desc')->get();

        $docsValidados1 = DocumentosFirmar::where('documentos_firmar.status', 'VALIDADO')
            ->Select('documentos_firmar.*','tbl_cursos.id as idcursos','contratos.id_contrato', 'tbl_cursos.folio_grupo')
            ->Join('tbl_cursos','tbl_cursos.clave','documentos_firmar.numero_o_clave')
            ->LeftJoin('folios','folios.id_cursos','tbl_cursos.id')
            ->LeftJoin('contratos','contratos.id_folios','folios.id_folios')
            ->where(function ($query) use ($email, $curpUser) {
                $query->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x
                    WHERE x->'_attributes'->>'curp_firmante' IN ('".$curpUser->curp."'))")
                ->orWhere(function($query1) use ($email) {
                    $query1->where('obj_documento_interno->emisor->_attributes->email', $email)
                            ->where('documentos_firmar.status', 'VALIDADO');
                });
            });
            // ->orderBy('id', 'desc')->get();

        // $docsCancelados1 = DocumentosFirmar::where('status', 'CANCELADO')
        //     ->where(function ($query) use ($email) {
        //         $query->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x
        //             WHERE x->'_attributes'->>'email_firmante' IN ('".$email."'))")
        //         ->orWhere(function($query1) use ($email) {
        //             $query1->where('obj_documento_interno->emisor->_attributes->email', $email)
        //                     ->where('status', 'CANCELADO');
        //         });
        //     });
        ##Cancelados
        $docsCancelados1 = DocumentosFirmar::where('documentos_firmar.status', 'like', 'CANCELADO%')
            ->Select('documentos_firmar.*','tbl_cursos.id as idcursos', 'tbl_cursos.folio_grupo', 'pa.status_recepcion')
            ->Join('tbl_cursos','tbl_cursos.clave','documentos_firmar.numero_o_clave')
            ->leftjoin('tbl_cursos as tc', 'tc.clave', 'documentos_firmar.numero_o_clave')
            ->leftjoin('pagos as pa', 'pa.id_curso', 'tc.id')
            ->where(function ($query) use ($email, $curpUser) {
                $query->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x
                    WHERE x->'_attributes'->>'curp_firmante' IN ('".$curpUser->curp."'))")
                ->orWhere(function($query1) use ($email) {
                    $query1->where('obj_documento_interno->emisor->_attributes->email', $email)
                            ->where('documentos_firmar.status', 'like', 'CANCELADO%');
                });
        });
            // ->orderBy('id', 'desc')->get();

        ##BUSQUEDA POR CLAVE
        $busqueda_clave = null;
        if($request->txtBusqueda != null || $request->txtBusqueda != ""){
            $busqueda_clave = $request->txtBusqueda;
        }

        if($busqueda_clave == null) {
            // $docsFirmar = $docsFirmar1->orderBy('id', 'desc')->get();
            // $docsFirmados = $docsFirmados1->orderBy('id', 'desc')->get();
            // $docsValidados = $docsValidados1->orderBy('id', 'desc')->get();
            // $docsCancelados = $docsCancelados1->orderBy('id', 'desc')->get();

            $docsFirmar = $docsFirmar1->orderBy('id', 'desc')->paginate(15, ['documentos_firmar.*']);
            $docsFirmados = $docsFirmados1->orderBy('id', 'desc')->paginate(15, ['documentos_firmar.*']);
            $docsValidados = $docsValidados1->orderBy('id', 'desc')->paginate(15, ['documentos_firmar.*']);
            $docsCancelados = $docsCancelados1->orderBy('id', 'desc')->paginate(15, ['documentos_firmar.*']);
        } else {
            // $docsFirmar = $docsFirmar1->where('tipo_archivo', $tipo_documento)->orderBy('id', 'desc')->get();
            // $docsFirmados = $docsFirmados1->where('tipo_archivo', $tipo_documento)->orderBy('id', 'desc')->get();
            // $docsValidados = $docsValidados1->where('tipo_archivo', $tipo_documento)->orderBy('id', 'desc')->get();
            // $docsCancelados = $docsCancelados1->where('tipo_archivo', $tipo_documento)->orderBy('id', 'desc')->get();
            $docsFirmar = $docsFirmar1->where('numero_o_clave', $busqueda_clave)->orderBy('id', 'desc')->paginate(15, ['documentos_firmar.*']);
            $docsFirmados = $docsFirmados1->where('numero_o_clave', $busqueda_clave)->orderBy('id', 'desc')->paginate(15, ['documentos_firmar.*']);
            $docsValidados = $docsValidados1->where('numero_o_clave', $busqueda_clave)->orderBy('id', 'desc')->paginate(15, ['documentos_firmar.*']);
            $docsCancelados = $docsCancelados1->where('numero_o_clave', $busqueda_clave)->orderBy('id', 'desc')->paginate(15, ['documentos_firmar.*']);
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
        return view('layouts.FirmaElectronica.firmaElectronica', compact('docsFirmar', 'email', 'docsFirmados', 'docsValidados', 'docsCancelados', 'busqueda_clave', 'token','rol','curpUser','seleccion'));
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

        ##By Jose Luis Moreno/ Creamos nuevo array para ordenar el xml
        if(isset($obj_documento['anexos'])){
            $ArrayXml = [
                "emisor" => $obj_documento['emisor'],
                "archivo" => $obj_documento['archivo'],
                "anexos" => $obj_documento['anexos'],
                "firmantes" => $obj_documento['firmantes'],
            ];
            $obj_documento = $ArrayXml;
        }

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
            $test = DocumentosFirmar::where('id', $documento->id)
                ->update([
                    'status' => 'VALIDADO',
                    'uuid_sellado' => $response->json()['uuid'],
                    'fecha_sellado' => $response->json()['fecha_Sellado'],
                    'documento' => $decode,
                    'cadena_sello' => $response->json()['cadenaSello']
                ]);

            // dd($tes)
            return redirect()->route('firma.inicio')->with('warning', 'Documento validado exitosamente!');
        } else {
            $respuesta_icti = ['uuid' => $response->json()['uuid'], 'descripcion' => $response->json()['descripcionError']];
            return redirect()->route('firma.inicio')->with('danger', $respuesta_icti);
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
            $DF = DocumentosFirmar::where('id', $request->txtIdCancel)->First();

            $data = [
                'usuario' => Auth::user()->name,
                'id' => Auth::user()->id,
                'motivo' => $request->motivo,
                'fecha' => $date,
                'correo' => Auth::user()->email
            ];

            if ($DF->status == 'VALIDADO') {
                $nuevo_status = 'CANCELADO ICTI';
            } else {
                $nuevo_status = 'CANCELADO';
            }

            DocumentosFirmar::where('id', $request->txtIdCancel)
                ->update([
                    'status' => $nuevo_status,
                    'cancelacion' => $data
                ]);
            if ($nuevo_status == 'CANCELADO') {
                tbl_curso::where('clave', $request->txtClave)
                    ->update(
                        $request->txtTipo == 'Lista de asistencia'
                            ? ['asis_finalizado' => false,
                                'observacion_asistencia_rechazo' => $request->motivo]
                            : ($request->txtTipo == 'Lista de calificaciones'
                                ?  ['calif_finalizado' => false,
                                    'observacion_calificacion_rechazo' => $request->motivo]
                                : [])
                    );
            }

            if($request->txtTipo == 'Contrato' && $nuevo_status == 'CANCELADO') {
                $folio = folio::Join('tbl_cursos','tbl_cursos.id','folios.id_cursos')
                ->Where('clave',$request->txtClave)
                ->Value('folios.id_folios');

                folio::Where('id_folios', $folio)
                ->update(
                        ['status' => 'Capturando']
                );
            }

            //By jose luis Actualizar json reporte foto en tbl_cursos
            if($request->txtTipo == 'Reporte fotografico' && $nuevo_status == 'CANCELADO'){
                try {
                    $curso = tbl_curso::where('clave', $request->txtClave)->first();
                    if ($curso) {
                        $json = $curso->evidencia_fotografica;
                        $json['status_validacion'] = 'RETORNADO';
                        $json['observacion_reporte'] = $request->motivo;
                        $curso->evidencia_fotografica = $json;
                        $curso->save();
                    }
                } catch (\Throwable $th) {
                    return redirect()->route('firma.inicio')->with('warning', 'Error al actualizar reporte fotografico!');
                }
            }

            return redirect()->route('firma.inicio')->with('warning', 'Documento cancelado exitosamente!');
        } else {
            return redirect()->route('firma.inicio')->with('danger', 'Debe ingresar el motivo de cancelación');
        }
    }

    ##Deshacer anulado by Jose Luis
    public function deshacer_anulado(Request $request) {

        $id_efirma = $request->id_efirma;
        if ($id_efirma != null) {
            DocumentosFirmar::where('id', $id_efirma)
            ->update(['status' => 'VALIDADO']);
        }
        return response()->json([
            'status' => 200,
            'id_efirma' => $id_efirma,
            'mensaje' => 'se realizo exitosamente'
        ]);
    }

    protected function getdocumentos(Request $request)
    {
        $respuesta = DB::Table('contratos')->Select('documentos_firmar.tipo_archivo','contratos.id_contrato','tbl_cursos.id AS id_curso')
            ->Join('folios','folios.id_folios','contratos.id_folios')
            ->Join('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->Join('documentos_firmar','documentos_firmar.numero_o_clave','tbl_cursos.clave')
            ->Where('contratos.id_contrato',$request->valor)
            ->Where('documentos_firmar.status', 'VALIDADO')
            ->Get();
        $json=json_encode($respuesta);
        return $json;
    }

    public function generarToken(Request $request) {
        //Token de producción
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
        Tokens_icti::create([
            'token' => $token
        ]);

        return $token;
    }

    public function sellarFile($xml, $token) {
        //Sellado de producción
        $response1 = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/NotariaXML/sellarXML', [
            'xml_Firmado' => $xml
        ]);

        // Sellado de prueba
        // $response1 = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer '.$token
        // ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/NotariaXML/sellarXML', [
        //     'xml_Firmado' => $xml
        // ]);
        return $response1;
    }

    public function obtener_xml($uuid)
    {
        $getToken = Tokens_icti::all()->last();
        $response = $this->xml_recovery($uuid, $getToken->token);
        if ($response->json() == null) {
            $request = new Request();
            $token = $this->generarToken($request);
            $response = $this->xml_recovery($uuid, $token);
        }
        dd($response);

    }

    public function xml_recovery($uuid, $token)
    {
        $response1 = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/NotariaXML/obtenerXML', [
            'uuid' => $uuid,
            'idsistema' => 87
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

