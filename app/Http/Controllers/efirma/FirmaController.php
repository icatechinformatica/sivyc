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
use Illuminate\Support\Facades\Validator;

// use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FirmaController extends Controller {

    // php artisan serve --port=8001
    public function index(Request $request) {
        //Prueba de generar token
        // dd($this->generarToken($request));
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
            ->Join('tbl_funcionarios','tbl_funcionarios.id_org','users.id_organismo')
            ->Where('users.id', Auth::user()->id)
            ->Where('tbl_funcionarios.activo', 'true')
            ->Where('tbl_funcionarios.titular', 'true')
            ->First();

        if(is_null($curpUser)) {
            $curpUser = new \stdClass();
            $curpUser->curp = 'N/A';
        }

        if($rol->role_id == '31' || $rol->role_id == '47' || $rol->role_id == '4'){

            $curpUser = DB::Table('users')->Select('tbl_funcionarios.curp')
                ->Join('tbl_funcionarios','tbl_funcionarios.id_org','users.id_organismo')
                ->Where('users.id', Auth::user()->id)
                ->Where('tbl_funcionarios.activo', 'true')
                ->First();
            }

        $docsFirmar1 = DocumentosFirmar::where('documentos_firmar.status','!=','CANCELADO')
            ->Select('documentos_firmar.*','tbl_cursos.id as idcursos','contratos.id_contrato', 'tbl_cursos.folio_grupo','folios.id_folios','folios.id_supre','folios.folio_validacion')
            ->Join('tbl_cursos','tbl_cursos.clave','documentos_firmar.numero_o_clave')
            ->LeftJoin('folios','folios.id_cursos','tbl_cursos.id')
            ->LeftJoin('contratos','contratos.id_folios','folios.id_folios')
            ->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x
                WHERE x->'_attributes'->>'curp_firmante' IN ('".$curpUser->curp."')
                AND x->'_attributes'->>'firma_firmante' is null)");
            // ->orderBy('id', 'desc')->get();


        $docsFirmados1 = DocumentosFirmar::where('documentos_firmar.status', 'EnFirma')
            ->Select('documentos_firmar.*','tbl_cursos.id as idcursos','contratos.id_contrato', 'tbl_cursos.folio_grupo','folios.id_folios','folios.id_supre','folios.folio_validacion')
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
            ->Select('documentos_firmar.*','tbl_cursos.id as idcursos','contratos.id_contrato', 'tbl_cursos.folio_grupo','folios.id_folios','folios.id_supre','folios.folio_validacion')
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
            $docsFirmar = $docsFirmar1->where(function ($query) use ($busqueda_clave) {
                $query->where('numero_o_clave', $busqueda_clave)
                    ->OrWhere('folios.folio_validacion', $busqueda_clave);
            })->orderBy('id', 'desc')->paginate(15, ['documentos_firmar.*']);
            $docsFirmados = $docsFirmados1->where(function ($query) use ($busqueda_clave) {
                $query->where('numero_o_clave', $busqueda_clave)
                    ->OrWhere('folios.folio_validacion', $busqueda_clave);
            })->orderBy('id', 'desc')->paginate(15, ['documentos_firmar.*']);
            $docsValidados = $docsValidados1->where(function ($query) use ($busqueda_clave) {
                $query->where('numero_o_clave', $busqueda_clave)
                    ->OrWhere('folios.folio_validacion', $busqueda_clave);
            })->orderBy('id', 'desc')->paginate(15, ['documentos_firmar.*']);
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
                //CONSULTA PARA VALIDACIÓN
                $valid_finacieros = DB::table('tbl_cursos as tc')
                ->join('pagos as pa', 'pa.id_curso', '=', 'tc.id')
                ->join('folios as fo', 'fo.id_cursos', '=', 'tc.id')
                ->where('pa.status_recepcion','VALIDADO')
                ->where('fo.edicion_pago', false)  // Aquí se compara si es diferente de true
                ->where('tc.clave', '=', $request->txtClave)
                ->exists();

                if($valid_finacieros){ //Si es true entonces mandar mensaje
                        return redirect()->route('firma.inicio')->with('danger', 'No es posible anular el documento debido a que ha sido validado por financieros');
                }else{ //No existe relacion con financieros o si tiene permiso de edición
                    $nuevo_status = 'CANCELADO ICTI';
                }

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

            return redirect()->route('firma.inicio')->with('warning', 'Documento cancelado de manera exitosa!');
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
        //     'nombre' => 'FIRMELEC_DOCGOB',
        //     'key' => '332295ED-CFE9-41F5-BEDB-54618833A7F4'
        // ]);

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
        return $response1;
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

    ##Nueva funcion para obtener cadenas originales
    public function obtener_cadenas(Request $request)
    {
        // Validar que vengan ids como arreglo no vacío
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'mensaje' => 'Parámetros inválidos.',
                'errores' => $validator->errors()
            ], 422);
        }

        $ids = $request->input('ids', []);
        $consulta = DocumentosFirmar::whereIn('id', $ids)->get(['id', 'cadena_original']);
        $cadenas_originales = $consulta->pluck('cadena_original', 'id')->toArray();

        return response()->json([
            'status'           => 200,
            'cadena_original'  => $cadenas_originales,
            'mensaje'          => 'Se realizó exitosamente'
        ]);
    }



    ##Nuevas funciones para el firmado masivo
    public function firmar_documentos(Request $request)
    {
        $respuesta = $request->input('respuesta');
        $correctos = $request->input('correctos');
        $errores   = $request->input('errores');
        $msnError  = $request->input('mensaje');
        $curp      = $request->input('curp');

        if (empty($respuesta) || empty($curp)) {
            return redirect()->route('firma.inicio')->with([
                'warning' => 'No hay respuesta para procesar: ' . "\n" . $msnError,
            ]);
        }

        $arrayRespuesta = json_decode($respuesta, true);
        if (!is_array($arrayRespuesta) || count($arrayRespuesta) === 0) {
            return redirect()->route('firma.inicio')->with([
                'warning' => 'Formato de respuesta inválido.'
            ]);
        }

        try {
            DB::transaction(function () use ($arrayRespuesta, $curp) {

                $ids = collect($arrayRespuesta)->pluck('idCadena')->all();
                $documentos = DocumentosFirmar::whereIn('id', $ids)->get()->keyBy('id');

                foreach ($arrayRespuesta as $res) {
                    if (!isset($documentos[$res['idCadena']])) {
                        continue; // si el documento no existe
                    }

                    $documento = $documentos[$res['idCadena']];
                    $obj_documento = json_decode($documento->obj_documento, true);

                    // Garantizar que md5_archivo exista
                    if (empty($obj_documento['archivo']['_attributes']['md5_archivo'])) {
                        $obj_documento['archivo']['_attributes']['md5_archivo'] = $documento->md5_file;
                    }

                    // Actualizar firmante correspondiente
                    foreach ($obj_documento['firmantes']['firmante'][0] as $key => $value) {
                        if ($value['_attributes']['curp_firmante'] == $curp) {
                            $obj_documento['firmantes']['firmante'][0][$key]['_attributes'] = array_merge(
                                $value['_attributes'],
                                [
                                    'fecha_firmado_firmante' => $res['fechafirma'] ?? null,
                                    'no_serie_firmante'      => $res['no_seriefirmante'] ?? null,
                                    'firma_firmante'         => $res['firma_cadena'] ?? null,
                                    'certificado'            => $res['certificado'] ?? null,
                                ]
                            );
                        }
                    }

                    // Reconstruir XML actualizado
                    $array = XmlToArray::convert($documento->documento);

                    $ArrayXml['emisor'] = $obj_documento['emisor'];

                    if (isset($obj_documento['receptores'])) {
                        $ArrayXml['receptores'] = $obj_documento['receptores'];
                    }

                    $ArrayXml["archivo"] = $obj_documento['archivo'];


                    if (isset($obj_documento['anexos'])) {
                        $ArrayXml["anexos"] = $obj_documento['anexos'];
                    }

                    $ArrayXml["firmantes"] = $obj_documento['firmantes'];

                    // Reemplazamos $obj_documento por el array ordenado
                    $obj_documento = $ArrayXml;

                    $resultXml = ArrayToXml::convert($obj_documento, [
                        'rootElementName' => 'DocumentoChis',
                        '_attributes' => [
                            'version'            => $array['DocumentoChis']['_attributes']['version'] ?? '',
                            'fecha_creacion'     => $array['DocumentoChis']['_attributes']['fecha_creacion'] ?? '',
                            'no_oficio'          => $array['DocumentoChis']['_attributes']['no_oficio'] ?? '',
                            'dependencia_origen' => $array['DocumentoChis']['_attributes']['dependencia_origen'] ?? '',
                            'asunto_docto'       => $array['DocumentoChis']['_attributes']['asunto_docto'] ?? '',
                            'tipo_docto'         => $array['DocumentoChis']['_attributes']['tipo_docto'] ?? '',
                            'xmlns'              => 'http://firmaelectronica.chiapas.gob.mx/GCD/DoctoGCD',
                        ],
                    ]);

                    // Guardar cambios en DB
                    $documento->update([
                        'obj_documento' => json_encode($obj_documento),
                        'documento' => $resultXml,
                    ]);
                }
            });

            return redirect()->route('firma.inicio')->with([
                'success'   => "Documento(s) firmado(s): \nExitoso(s): $correctos \n, Detalles: $errores $msnError"
            ]);

        } catch (\Throwable $th) {
            return redirect()->route('firma.inicio')->with([
                'danger'   => 'Error: ' . $th->getMessage()
            ]);
        }
    }

    #### FUNCION DEL SELLADO MASIVO
    public function sellar_documentos(Request $request)
    {
        // 1) Validar campo recibido
        $raw = $request->input('ids_sellar'); // puede venir como JSON string o arreglo

        if (empty($raw)) {
            return redirect()->route('firma.inicio')
                ->with(['message' => 'No se recibieron IDs para procesar el sellado.']);
        }

        // 2) Decodificar JSON si aplica
        $ids = is_array($raw) ? $raw : json_decode($raw, true);

        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('firma.inicio')
                ->with(['message' => 'El formato de los IDs no es válido.']);
        }

        // 3) Limpiar IDs (solo enteros positivos y únicos)
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), function ($v) {
            return $v > 0;
        })));

        if (empty($ids)) {
            return redirect()->route('firma.inicio')
                ->with(['message' => 'No hay IDs válidos para procesar.']);
        }

        // 4) Obtener documentos en un solo query
        $documentos = DocumentosFirmar::select('id', 'documento', 'status')->whereIn('id', $ids)->get()->keyBy('id');

        if ($documentos->isEmpty()) {
            return redirect()->route('firma.inicio')
                ->with(['message' => 'No se encontraron documentos con esos IDs.']);
        }

        // 5) Intentar obtener token actual
        $tokenRow = Tokens_icti::where('sistema', 'sivyc')->first();
        $token = $tokenRow ? $tokenRow->token : null;

        $exitos = 0;
        $errores = [];

        // 6) Recorrer los documentos
        foreach ($ids as $id) {
            // Validar existencia
            if (!$documentos->has($id)) {
                $errores[$id] = 'Documento no encontrado.';
                continue;
            }

            $doc = $documentos[$id];

            // Omitir documentos ya validados
            if ($doc->status === 'VALIDADO') {
                continue;
            }

            try {
                $xmlBase64 = base64_encode($doc->documento);

                // Intento 1: usar token actual
                $response = $token ? $this->sellarFile($xmlBase64, $token) : null;

                // Si no responde correctamente, generar token nuevo una sola vez
                if (!$response || !$response->json()) {
                    $tokenNuevo = $this->generarToken(new Request());
                    if (!empty($tokenNuevo)) {
                        $token = $tokenNuevo;
                        $response = $this->sellarFile($xmlBase64, $token);
                    }
                }

                // Validar respuesta
                if (!$response || !$response->json()) {
                    $errores[$id] = 'Respuesta del servicio inválida.';
                    continue;
                }

                $body = $response->json();

                if (!isset($body['status'])) {
                    $errores[$id] = 'No se recibió estado del servicio.';
                    continue;
                }

                // Éxito
                if ((int)$body['status'] === 1) {
                    $xmlDecoded = base64_decode(isset($body['xml']) ? $body['xml'] : '', true);
                    if ($xmlDecoded === false) {
                        $errores[$id] = 'XML inválido recibido.';
                        continue;
                    }

                    DocumentosFirmar::where('id', $id)->update([
                        'status'        => 'VALIDADO',
                        'uuid_sellado'  => isset($body['uuid']) ? $body['uuid'] : null,
                        'fecha_sellado' => isset($body['fecha_Sellado']) ? $body['fecha_Sellado'] : now(),
                        'documento'     => $xmlDecoded,
                        'cadena_sello'  => isset($body['cadenaSello']) ? $body['cadenaSello'] : null,
                        'updated_at'    => now(),
                    ]);

                    $exitos++;
                } else {
                    $errores[$id] = isset($body['descripcionError'])
                        ? $body['descripcionError']
                        : 'Error desconocido al sellar.';
                }

            } catch (\Throwable $th) {
                $errores[$id] = $th->getMessage();
            }
        }

        // 7) Preparar mensaje final
        $total = count($ids);
        $fallos = count($errores);
        $msg = $exitos . " Documento(s) sellado(s) de {$total}.";

        if ($fallos > 0) {
            // Mostrar primeros 5 errores
            $detalles = '';
            $contador = 0;
            foreach ($errores as $k => $v) {
                $detalles .= "#{$k}: {$v} | ";
                $contador++;
                if ($contador >= 5) break;
            }
            $msg .= " {$fallos} Documento(s) con error. : " . trim($detalles, ' |');
            if ($fallos > 5) $msg .= ' ...';
        }

        // 8) Redirigir con resultado
        return redirect()->route('firma.inicio')->with(['success' => $msg]);
    }



}

