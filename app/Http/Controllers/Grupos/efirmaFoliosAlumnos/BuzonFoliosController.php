<?php

namespace App\Http\Controllers\Grupos\efirmaFoliosAlumnos;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Unidad;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\FirmaElectronica\EfoliosAlumnos;
use App\Models\FirmaElectronica\Tokens_icti;
use Vyuldashev\XmlToArray\XmlToArray;
use Spatie\ArrayToXml\ArrayToXml;
use PHPQRCode\QRcode;
use PDF;

class BuzonFoliosController extends Controller
{
    function __construct() {
        session_start();
    }

    public function index(Request $request)
    {
        ## Buzon de folios
        //Debemos obtener la unidad a la que pertenece el usuario para la visualizacion, firmado y sellado
        $data = $ids = $cad_original = $array_firm = [];
        $token = '';
        $existcurp = $existmail = $existfirma = false;
        $curpf = $emailf = '';

        ## Obtenemos la unidad y el rol del usuario
        $unidad = Auth::user()->unidad;
        $ubicacion = Unidad::where('id', $unidad)->value('ubicacion');
        $slug = Auth::user()->roles()->first()->slug;
        // $aceptados = ['admin', 'titular_unidad', 'director_unidad'];

        ##Obtenemos curp, email del firmante para validar si le pertenece firmar el documento
        $curpUser = DB::Table('users')->Select('tbl_funcionarios.curp', 'tbl_funcionarios.correo')
        ->Join('tbl_funcionarios','tbl_funcionarios.correo','users.email')
        ->Where('users.id', Auth::user()->id)
        ->First();

        if($curpUser != null){$curpf = $curpUser->curp;$emailf = $curpUser->correo;}

        ## Estados del documento
        $estados = ['EnFirma' => 'POR FIRMAR','firmado' => 'FIRMADO','sellado' => 'SELLADO','cancelado' => 'CANCELADO'];

        ### Recopilamos los datos del request
        if(session('ejercicio_e')) $ejercicio_e = session('ejercicio_e');
        else $ejercicio_e = $request->anio;
        if($ejercicio_e) $_SESSION['ejercicio_e'] = $ejercicio_e;

        if(session('filtro_e')) $filtro_e = session('filtro_e');
        else $filtro_e = $request->status;
        if($filtro_e) $_SESSION['filtro_e'] = $filtro_e;

        if(session('clave_e')) $clave_e = session('clave_e');
        else $clave_e = $request->txtclave;
        if($clave_e) $_SESSION['clave_e'] = $clave_e;

        if(session('matricula')) $matricula = session('matricula');
        else $matricula = $request->txtmatricula;
        if($matricula) $_SESSION['matricula'] = $matricula;

        ##Realizamos la busqueda en la base de datos de efolios_alumnos
        if(!is_null($ejercicio_e) && !is_null($filtro_e) && !is_null($clave_e)){
            try {
                $data = DB::table('efolios_alumnos as ef')
                ->select('ef.id','ef.matricula','ef.efolio','ef.fecha_creacion','ef.status_doc','tf.nombre','tf.motivo','tf.movimiento','ef.obj_documento', 'ef.cadena_original')
                ->join('tbl_cursos as tc', 'tc.id', '=', 'ef.id_curso')
                ->join('tbl_folios as tf', 'tf.folio', '=', 'ef.efolio')
                ->whereYear('ef.fecha_creacion', $ejercicio_e);
                if($matricula)$data = $data->where('ef.matricula',$matricula);
                // ->where('ef.status_doc', $filtro_e);

                if ($filtro_e == 'EnFirma') {
                    // $data = $data->where('ef.status_doc', 'EnFirma')->orWhere('ef.status_doc', 'EnFirmaUno');
                    $data = $data->where(function($query) {
                        $query->where('ef.status_doc', 'EnFirma')
                              ->orWhere('ef.status_doc', 'EnFirmaUno');
                    });
                }else if($filtro_e == 'firmado'){
                    $data = $data->where('ef.status_doc', 'firmado');
                }else if($filtro_e == 'sellado'){
                    $data = $data->where('ef.status_doc', 'sellado');
                }
                else if($filtro_e == 'cancelado'){
                    // $data = $data->where('ef.status_doc', 'cancelado')->orWhere('ef.status_doc', 'cancelado_icti');
                    $data = $data->where(function($query) {
                        $query->where('ef.status_doc', 'cancelado')
                              ->orWhere('ef.status_doc', 'cancelado_icti');
                    });
                }

                $data = $data->whereRaw("CONCAT(tc.clave, ' ', tc.folio_grupo) LIKE ?", ['%'.$clave_e.'%'])->get();

                if($data == null || count($data) == 0){
                    return back()->with(['message' => 'No se encontraron registros', 'clave_e' => $clave_e, 'matricula' => $matricula]);
                }
            } catch (\Throwable $th) {
                return back()->with('message', '¡ERROR AL REALIZAR LA BUSQUEDA DE REGISTROS! '.$th->getMessage());
            }
            ##Obtenemos los id
            if($data) $ids = $data->pluck('id')->toArray();
            if($data) $cad_original = $data->pluck('cadena_original', 'id')->toArray();

            ##Obtenemos token para enviarlos a la vista
            $getToken = Tokens_icti::latest()->first();
            if ($getToken) {$token = $getToken->token;}

            ##Validamos los firmantes del documento
            // dd(isset($obj['firmantes']['firmante'][0][0]['_attributes']['firma_firmante']));
            foreach ($data as $key => $datos) {
                $obj = json_decode($datos->obj_documento, true);
                $firmantes = $obj['firmantes']['firmante'][0];
                foreach ($firmantes as $value) {
                    $curp = $value['_attributes']['curp_firmante'];
                    $email = $value['_attributes']['email_firmante'];
                    if($curpf == $curp){
                        $existcurp = true;
                        if(isset($value['_attributes']['firma_firmante'])){$existfirma = true;}
                    }
                    if($emailf == $email){$existmail = true;}
                }
                // Terminamos
                if ($existcurp == true && $existmail == true) {
                    break;
                }
            }
        }

        // $token =$this->generarToken();
        // dd($token);

        return view('grupos.efirmafolios.efirmabuzon_folios', compact('ubicacion','estados','ejercicio_e','filtro_e','clave_e',
        'data','ids','matricula','token', 'cad_original', 'array_firm', 'curpf','existcurp','existmail','slug', 'existfirma'));
    }

    ##Cancelar documento
    public function cancelar_doc(Request $request)
    {
        $descripcion = $request->descripcion;
        $estado_doc = $request->estado_doc;
        $ids = $request->ids;
        $cancelacion = 'cancelado';
        $objeto_cancelacion = [
            "idUser" => Auth::user()->id,
            "descripcion" => $descripcion,
            "fecha_cancelado" => Carbon::now()->toDateTimeString()
        ];
        if($estado_doc == 'sellado') $cancelacion = 'cancelado_icti';

        if($estado_doc == 'EnFirma' || $estado_doc == 'firmado' || $estado_doc == 'sellado'){
            if(count($ids) > 0){
                try {
                    foreach ($ids as $key => $id) {
                        EfoliosAlumnos::where('id', $id)->update(['status_doc' => $cancelacion,'h_cancelado' => $objeto_cancelacion]);
                    }
                    return response()->json(['status' => 200,'mensaje' => '¡Documentos cancelados correctamente!']);
                } catch (\Throwable $th) {
                    return response()->json(['status' => 500,'mensaje' => 'Error al actualizar los campos '.$th->getMessage()]);
                }
            }else{
                return response()->json([
                    'status' => 200,
                    'mensaje' => 'No existen registros para cancelar',
                ]);
            }
        }else{
            return response()->json(['status' => 400,'mensaje' => '¡Parametro no valido, intente de nuevo!']);
        }

    }

    ##Generar documento PDF
    public function generar_pdf($id)
    {
        if(!is_null($id)){
            // $ids = explode(',', $id);
            ##Haremos una consulta desde el objeto_json para mostrarlos en la constancia
            $firmantes = [];
            $uuid = $cadena_sello = $fecha_sello = $no_oficio = "";


            $consulta = EfoliosAlumnos::select('datos_alumno', 'obj_documento', 'status_doc', 'uuid_sellado',
            'fecha_sellado', 'cadena_sello', 'no_oficio')->where('id', $id)->first();


            #Obtenemos datos de los firmantes
            if(!is_null($consulta->datos_alumno) || !is_null($consulta->uuid_sellado)){
                $data = $consulta->datos_alumno;
                // dd($data['cont_tematico'][0]['hora']);
                $uuid = $cadena_sello = $fecha_sello = $no_oficio = $qrCodeBase64 = null;
                if ($consulta->status_doc == 'sellado') {
                    $uuid = $consulta->uuid_sellado;
                    $cadena_sello = $consulta->cadena_sello;
                    $fecha_sello_form = $consulta->fecha_sellado;
                    $date = Carbon::parse($fecha_sello_form);
                    $fecha_sello = $date->format('d/m/Y');
                    $no_oficio = $consulta->no_oficio;

                    $objeto  = $consulta->obj_documento['firmantes']['firmante'][0];
                    foreach ($objeto as $key => $value) {
                        $nombre = $value['_attributes']['nombre_firmante'];
                        $firma = $value['_attributes']['firma_firmante'];
                        $fechafirm = $value['_attributes']['fecha_firmado_firmante'];
                        $seriefirm = $value['_attributes']['no_serie_firmante'];
                        if($key == 0) $puesto = $data['puesto_acad'];
                        else $puesto = $data['puesto_direc'];
                        $firmantes[] = ['nombre' => $nombre,'firma' => $firma,'fecha_firma' => $fechafirm,'serie' => $seriefirm, 'puesto' => $puesto];
                    }


                    // $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumentoPrueba/consulta/Certificado3?guid=$uuid&no_folio=$no_oficio";
                    // dd($verificacion);
                    $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumento/consulta/Certificado3?guid=$uuid&no_folio=$no_oficio";
                    ob_start();
                    QRcode::png($verificacion);
                    $qrCodeData = ob_get_contents();
                    ob_end_clean();
                    $qrCodeBase64 = base64_encode($qrCodeData);

                }


                $pdf = PDF::loadView('grupos.efirmafolios.pdfconstancia_efolios',compact('data', 'uuid', 'cadena_sello', 'fecha_sello', 'no_oficio', 'qrCodeBase64', 'firmantes'));
                return $pdf->stream('Constancia alumno');
            }else{
                return "Error al realizar la consulta a la base de datos";
            }
        }else{
            return "El identificador del documento no es valido";
        }
    }

    ## Actualizar registro con datos de los firmantes
    public function firmar_documento(Request $request)
    {
        $respuesta = $request->input('respuesta');
        $correctos = $request->input('correctos');
        $errores = $request->input('errores');
        $msnError = $request->input('mensaje');
        $curp = $request->input('curp');
        $clave = $request->input('clave_f');
        $matricula = $request->input('matricula_f');

        $arrayRespuesta = [];
        if($respuesta) $arrayRespuesta = json_decode($respuesta, true);
        if(count($arrayRespuesta) > 0 && $curp != ""){

            foreach ($arrayRespuesta as $indice => $res) {
                ##Consultamos el registro
                try {
                    $documento = EfoliosAlumnos::where('id', $res['idCadena'])->first();
                    // $obj_documento = json_decode($documento->obj_documento, true);
                    $obj_documento = $documento->obj_documento;
                    $status_doc = $documento->status_doc;

                    if (empty($obj_documento['archivo']['_attributes']['md5_archivo'])) {
                        $obj_documento['archivo']['_attributes']['md5_archivo'] = "";
                    }

                    foreach ($obj_documento['firmantes']['firmante'][0] as $key => $value) {
                        if ($value['_attributes']['curp_firmante'] == $curp) {
                            $value['_attributes']['fecha_firmado_firmante'] = $res['fechafirma'];
                            $value['_attributes']['no_serie_firmante'] = $res['no_seriefirmante'];
                            $value['_attributes']['firma_firmante'] = $res['firma_cadena'];
                            $value['_attributes']['certificado'] = $res['certificado'];
                            $obj_documento['firmantes']['firmante'][0][$key] = $value;
                        }
                    }

                    $array = XmlToArray::convert($documento->documento_xml);
                    $array['DocumentoChis']['firmantes'] = $obj_documento['firmantes'];

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
                    if($status_doc == 'EnFirma'){$status_doc = 'EnFirmaUno';}
                    else if($status_doc == 'EnFirmaUno'){$status_doc = 'firmado';}

                    EfoliosAlumnos::where('id', $res['idCadena'])
                    ->update(['obj_documento' => json_encode($obj_documento),'documento_xml' => $result, 'status_doc' => $status_doc]);

                } catch (\Throwable $th) {
                    return redirect('grupos/efirma/buzon')->with(['message'=> 'Error: '.$th->getMessage(), 'clave_e' => $clave, 'matricula' => $matricula]);
                }

            }
            return redirect('grupos/efirma/buzon')->with([
                'message' => 'Documento(s) firmado(s): ' . "\n" . 'Exitoso(s): ' . $correctos . "\n" . 'Detalles: ' . $errores . ' ' . $msnError,
                'clave_e' => $clave, 'matricula' => $matricula
            ]);

        }else{
            return redirect('grupos/efirma/buzon')->with(['message'=>'No hay respuesta para procesar: '."\n". $msnError, 'clave_e' => $clave, 'matricula' => $matricula]);
        }

    }

    public function sellar_documento(Request $request){
        $clave = $request->input('clave_s');
        $matricula = $request->input('matricula_s');
        $correctos = 0;
        if($request->ids_sellar){
            $arrayIds = json_decode($request->ids_sellar, true);
            if(count($arrayIds) == []){exit("No existen registros para realizar el proceso de sellado");}
                foreach ($arrayIds as $key => $valId) {
                    try {
                        $documento = EfoliosAlumnos::select('documento_xml')->where('id', $valId)->first();
                        // $documento = EfoliosAlumnos::where('id', $valId)->value('documento_xml');
                        $xmlBase64 = base64_encode($documento->documento_xml);

                        $getToken = Tokens_icti::latest()->first();

                        $response = $this->sellarFile($xmlBase64, $getToken->token);
                        if ($response->json() == null) {
                            $request = new Request();
                            $token = $this->generarToken($request);
                            $response = $this->sellarFile($xmlBase64, $token);
                        }
                        if ($response->json()['status'] == 1) { //exitoso
                            $decode = base64_decode($response->json()['xml']);
                            EfoliosAlumnos::where('id', $valId)
                                ->update([
                                    'status_doc' => 'sellado',
                                    'uuid_sellado' => $response->json()['uuid'],
                                    'fecha_sellado' => $response->json()['fecha_Sellado'],
                                    'documento_xml' => $decode,
                                    'cadena_sello' => $response->json()['cadenaSello']
                                ]);
                        } else {
                            // $respuesta_icti = ['uuid' => $response->json()['uuid'], 'descripcion' => $response->json()['descripcionError']];
                            // return redirect()->route('grupo.efirma.index')->with(['message' => $respuesta_icti]);
                            $respuesta_icti = json_encode(['uuid' => $response->json()['uuid'], 'descripcion' => $response->json()['descripcionError']]);
                            return redirect('grupos/efirma/buzon')->with(['message'=>$respuesta_icti, 'clave_e' => $clave, 'matricula' => $matricula]);
                        }
                    } catch (\Throwable $th) {
                        // return redirect()->route('grupo.efirma.index')->with(['message' => $th->getMessage()]);
                        return redirect('grupos/efirma/buzon')->with(['message'=> $th->getMessage(), 'clave_e' => $clave, 'matricula' => $matricula]);
                    }
                    $correctos ++;
                }
                $mensaje = $correctos.' Documento(s) sellado(s) exitosamente. ';
                return redirect('grupos/efirma/buzon')->with(['message'=> $mensaje, 'clave_e' => $clave, 'matricula' => $matricula]);
        }else{
            // return redirect()->route('grupo.efirma.index')->with(['message' => 'No existen datos para procesar el sellado de documentos.']);
            return redirect('grupos/efirma/buzon')->with(['message'=> 'No existen datos para procesar el sellado de documentos.', 'clave_e' => $clave, 'matricula' => $matricula]);
        }
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


    public function generarToken() {

        try {
            ##Producción
            $resToken = Http::withHeaders([
                'Accept' => 'application/json'
            ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
                'nombre' => 'SISTEM_IVINCAP',
                'key' => 'B8F169E9-C9F6-482A-84D8-F5CB788BC306'
            ]);

            ## Prueba
            // $resToken = Http::withHeaders([
            //     'Accept' => 'application/json'
            // ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
            //     'nombre' => 'FirmaElectronica',
            //     'key' => '19106D6F-E91F-4C20-83F1-1700B9EBD553'
            // ]);

            $token = $resToken->json();
            Tokens_icti::create(['token' => $token]);
            return $token;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

    }

}
