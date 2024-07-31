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

        $docsFirmar1 = DocumentosFirmar::where('documentos_firmar.status','!=','CANCELADO')
            ->Select('documentos_firmar.*','tbl_cursos.id as idcursos','contratos.id_contrato', 'tbl_cursos.folio_grupo','folios.id_folios','folios.id_supre')
            ->Join('tbl_cursos','tbl_cursos.clave','documentos_firmar.numero_o_clave')
            ->LeftJoin('folios','folios.id_cursos','tbl_cursos.id')
            ->LeftJoin('contratos','contratos.id_folios','folios.id_folios')
            ->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x
                WHERE x->'_attributes'->>'curp_firmante' IN ('".$curpUser->curp."')
                AND x->'_attributes'->>'firma_firmante' is null)");
            // ->orderBy('id', 'desc')->get();


        $docsFirmados1 = DocumentosFirmar::where('documentos_firmar.status', 'EnFirma')
            ->Select('documentos_firmar.*','tbl_cursos.id as idcursos','contratos.id_contrato', 'tbl_cursos.folio_grupo','folios.id_folios','folios.id_supre')
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
            ->Select('documentos_firmar.*','tbl_cursos.id as idcursos','contratos.id_contrato', 'tbl_cursos.folio_grupo','folios.id_folios','folios.id_supre')
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

        $getToken = Tokens_icti::Where('sistema', 'sivyc')->First();

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

        $getToken = Tokens_icti::Where('sistema', 'sivyc')->First();
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
        $respuesta = DB::Table('contratos')->Select('documentos_firmar.tipo_archivo','contratos.id_contrato','tbl_cursos.id AS id_curso','folios.id_folios','folios.id_supre AS id_supre_64')
            ->Join('folios','folios.id_folios','contratos.id_folios')
            ->Join('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->Join('documentos_firmar','documentos_firmar.numero_o_clave','tbl_cursos.clave')
            ->Where('contratos.id_contrato',$request->valor)
            ->Where('documentos_firmar.status', 'VALIDADO')
            ->Get();
        foreach($respuesta as $key => $kcd) {
            $respuesta[$key]->id_supre_64 = base64_encode($kcd->id_supre_64);
        }
        $json=json_encode($respuesta);
        return $json;
    }

    public function generarToken(Request $request) {
        //Token de producción
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
        Tokens_icti::Where('sistema','sivyc')->update([
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
        // return $response1;
    }

    public function obtener_xml($uuid)
    {
        $getToken = Tokens_icti::Where('sistema', 'sivyc')->First();
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

