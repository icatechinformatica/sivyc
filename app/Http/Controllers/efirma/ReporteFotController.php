<?php

namespace App\Http\Controllers\efirma;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\DocumentosFirmar;
use App\Models\tbl_curso;
use PHPQRCode\QRcode;
use Carbon\Carbon;
use PDF;

class ReporteFotController extends Controller
{
    function __construct() {
        $this->mes = ["01" => "ENERO", "02" => "FEBRERO", "03" => "MARZO", "04" => "ABRIL", "05" => "MAYO", "06" => "JUNIO", "07" => "JULIO", "08" => "AGOSTO", "09" => "SEPTIEMBRE", "10" => "OCTUBRE", "11" => "NOVIEMBRE", "12" => "DICIEMBRE"];
        $this->path_files = env("APP_URL").'/storage/uploadFiles';
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
        $firmantes = null;
        $id_curso = $id;
        $cursopdf = tbl_curso::select('nombre', 'curso', 'tcapacitacion', 'inicio', 'termino', 'evidencia_fotografica',
        'clave', 'hini', 'hfin', 'tbl_cursos.unidad', 'uni.dunidad', 'uni.ubicacion', 'uni.direccion', 'uni.municipio')
        ->join('tbl_unidades as uni', 'uni.unidad', 'tbl_cursos.unidad')
        ->where('tbl_cursos.id', '=', $id_curso)->first();

        $direccion = $cursopdf->direccion;

        $documento = DocumentosFirmar::where('numero_o_clave', $cursopdf->clave)
            ->WhereNotIn('status',['CANCELADO','CANCELADO ICTI'])
            ->Where('tipo_archivo','Reporte fotografico')
            ->first();

        if(is_null($documento)) {
            $body_html = $this->create_body($id);
            $body['header'] = $body_html['header'];
            $body['footer'] = $body_html['footer'];
            $body['body'] = $body_html['body'];
        } else {
            $body_html = json_decode($documento->obj_documento_interno);
            $body['header'] = $body_html->header;
            $body['footer'] = $body_html->footer;
            $body['body'] = $body_html->body;
            if(isset($body_html->firmantes)) {
                $firmantes = $body_html->firmantes;
            }
        }

        // $path_files = $this->path_files;
        $path_files = 'https://www.sivyc.icatech.gob.mx/storage/uploadFiles';
        $array_fotos = [];
        $fechapdf = "";
        $objeto = $dataFirmante = $uuid = $cadena_sello = $fecha_sello = $qrCodeBase64 = $EFolio =  null;

        #Distintivo
        // $leyenda = DB::table('tbl_instituto')->value('distintivo');

        #Unidad de capacitacion
        // $meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];

        // if ($cursopdf == null) {
        //     return redirect()->route('firma.inicio')->with('danger', 'Error al consultar el curso de este documento!');
        // }


        // if (isset($cursopdf->evidencia_fotografica["fecha_envio"])) {
        //     $fechapdf = $cursopdf->evidencia_fotografica["fecha_envio"];
        //     $fechaCarbon = Carbon::createFromFormat('Y-m-d', $fechapdf);
        //     $dia = ($fechaCarbon->day) < 10 ? '0'.$fechaCarbon->day : $fechaCarbon->day;
        //     $fechapdf = $dia.' DE '.$meses[$fechaCarbon->month-1].' DE '.$fechaCarbon->year;
        // }else{
        //     $fechapdf = '';
        // }

        ##Procesar fotos
        if (isset($cursopdf->evidencia_fotografica['url_fotos'])){
            $array_fotos = $cursopdf->evidencia_fotografica['url_fotos'];
        }

        ##Procesar imagenes
        $base64Images = [];
        $environment = config('app.env');

        if ($environment === 'local') {
            ##Local
            foreach ($array_fotos as $url) {
                $imageContent = file_get_contents("https://www.sivyc.icatech.gob.mx/storage/uploadFiles{$url}");
                $base64 = base64_encode($imageContent);
                $base64Images[] = $base64;
            }
        } else {
            ##Produccion
            foreach ($array_fotos as $url) {
                $imageContent = file_get_contents(storage_path("app/public/uploadFiles".$url));
                $base64 = base64_encode($imageContent);
                $base64Images[] = $base64;
            }
        }

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

                // $dataFirmante = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre')
                //     ->Join('tbl_funcionarios AS fun','fun.id','org.id')
                //     ->Where('org.id', Auth::user()->id_organismo)
                //     ->Where('org.nombre', 'LIKE', '%ACADEMICO%')
                //     ->First();

                // $dataFirmante = DB::Table('tbl_organismos AS org')
                // ->Select('org.id', 'fun.nombre AS funcionario','fun.curp',
                // 'fun.cargo','fun.correo', 'org.nombre')
                //     ->join('tbl_funcionarios AS fun', 'fun.id','org.id')
                //     ->where('org.nombre', 'LIKE', '%ACADEMICO%')
                //     ->where('org.nombre', 'LIKE', '%'.$cursopdf->ubicacion.'%')
                //     ->first();
                // if($dataFirmante == null){
                //     dd("No se encontraron datos del academico");
                // }

                $puestoUsuario = $objeto['firmantes']['firmante'][0][1]['_attributes']['curp_firmante'];

                $dataFirmante = DB::Table('tbl_organismos AS org')
                ->Select('org.id', 'fun.nombre AS funcionario','fun.curp',
                'fun.cargo','fun.correo', 'org.nombre', 'fun.incapacidad')
                    ->join('tbl_funcionarios AS fun', 'fun.id_org','org.id')
                    ->where('fun.curp', '=', $puestoUsuario)
                    ->first();
                if($dataFirmante == null){return "No se encontraron datos del servidor publico";}


                //Generacion de QR
                //Verificacion de prueba
                // $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumentoPrueba/consulta/Certificado3?guid=$uuid&no_folio=$no_oficio";
                $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumento/consulta/Certificado3?guid=$uuid&no_folio=$no_oficio";
                ob_start();
                QRcode::png($verificacion);
                $qrCodeData = ob_get_contents();
                ob_end_clean();
                $qrCodeBase64 = base64_encode($qrCodeData);

            }
        }

        // if(!is_null($documento)){
        //     $EFolio = $documento->num_oficio;
        // }

        $pdf = PDF::loadView('layouts.FirmaElectronica.reporteFotografico', compact('body', 'objeto','dataFirmante','direccion',
        'uuid','cadena_sello','fecha_sello','qrCodeBase64', 'base64Images', 'array_fotos','firmantes'));
        $pdf->setPaper('Letter', 'portrait');
        $file = "REPORTE_FOTOGRAFICO_$id_curso.PDF";
        return $pdf->stream($file);
    }

    ##By Jose Luis Moreno/ Consulta de evidencias fotograficas
    protected function getreportefoto(Request $request){
        ##231920485  2B-23-ADMI-CAE-0192
        ##contrato 15682
        $id_contrato = $request->id;
        $id_curso = "";

        $result = DB::Table('contratos')->Select('tbl_cursos.id AS id_curso')
            ->Join('folios','folios.id_folios','contratos.id_folios')
            ->Join('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->Join('documentos_firmar','documentos_firmar.numero_o_clave','tbl_cursos.clave')
            ->Where('contratos.id_contrato',$id_contrato)
            ->where('documentos_firmar.tipo_archivo', 'Reporte fotografico')
            ->where('documentos_firmar.status', 'VALIDADO')
            ->first();
        if($result != null){
            $id_curso = $result->id_curso;
        }

        return response()->json([
            'status' => 200,
            'id_curso' => $id_curso
        ]);
    }

    #Crear Cuerpo
    private function create_body($id, $firmantes = null) {

        $path_files = 'https://www.sivyc.icatech.gob.mx/storage/uploadFiles';
        $array_fotos = [];
        $id_curso = $id;
        $fechapdf = "";

        #Distintivo
        $leyenda = DB::Connection('pgsql')->table('tbl_instituto')->value('distintivo');

        #Unidad de capacitacion
        $meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];

        $cursopdf = tbl_curso::select('nombre', 'curso', 'tcapacitacion', 'inicio', 'termino', 'evidencia_fotografica',
        'clave', 'hini', 'hfin', 'tbl_cursos.unidad', 'uni.dunidad', 'uni.ubicacion', 'uni.direccion', 'uni.municipio')
        ->join('tbl_unidades as uni', 'uni.unidad', 'tbl_cursos.unidad')
        ->where('tbl_cursos.id', '=', $id_curso)->first();

        if (isset($cursopdf->evidencia_fotografica["fecha_envio"])) {
            $fechapdf = $cursopdf->evidencia_fotografica["fecha_envio"];
            $fechaCarbon = Carbon::createFromFormat('Y-m-d', $fechapdf);
            $dia = ($fechaCarbon->day) < 10 ? '0'.$fechaCarbon->day : $fechaCarbon->day;
            $fechapdf = $dia.' DE '.$meses[$fechaCarbon->month-1].' DE '.$fechaCarbon->year;
        }else{
            $fechapdf = '';
        }

        if(isset($leyenda)) { $leyenda = explode("*",$leyenda); }

        $body['header'] = '<header>
            <img src="img/instituto_oficial.png" alt="Logo Izquierdo" width="30%" style="position:fixed; left:0; top:0;" />
            <img src="img/chiapas.png" alt="Logo Derecho" width="25%" style="position:fixed; right:0; top:0;" />
        </header>';

        $body['footer'] = '<footer>
            <div style="position: absolute; top: 5px;">
                <img style="" src="img/formatos/footer_vertical.jpeg" width="100%">';
                if ($cursopdf) {
                    $direccion = explode("*", $cursopdf->direccion);
                    $body['footer'] = $body['footer']. '<p class="direccion"><b>';
                    foreach($direccion as $point => $ari) {
                        if($point != 0) { $body['footer'] = $body['footer']. '<br>'; } $body['footer'] = $body['footer']. $ari;
                    }
                    $body['footer'] = $body['footer']. '</b></p>';
                }
                $body['footer'] = $body['footer']. '</div>
        </footer>';

        $body['body'] = '<div style="margin-top: -22%; margin-bottom: 4%;">
            <h6 style="text-align: center;">'; if (isset($leyenda)) {
                foreach($leyenda as $keys => $part){ if($keys != 0) { $body['body'] = $body['body'].'<br>'; } $body['body'] = $body['body'].$part; }
            }
                $body['body'] = $body['body']. '</h6>
        </div>
        <div style="text-align:center;">
            <span style="text-align: center;">REPORTE FOTOGRÁFICO DE INSTRUCTOR EXTERNO</span>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 14px; margin-bottom: 5px;">';
            if ($cursopdf->ubicacion != $cursopdf->unidad) {
                $body['body'] = $body['body']. 'UNIDAD DE CAPACITACIÓN '. $cursopdf->ubicacion. ', CENTRO DE TRABAJO ACCIÓN MÓVIL '. $cursopdf->unidad. '.';
            } else {
                $body['body'] = $body['body']. 'UNIDAD DE CAPACITACIÓN '. $cursopdf->ubicacion. '.';
            }
            $body['body'] = $body['body']. '</p>
            <p style="font-size: 14px; margin-top: 0px; margin-bottom: 25px;">'. mb_strtoupper($cursopdf->municipio, 'UTF-8'). ', CHIAPAS. A '. $fechapdf. '</p>
        </div>
        <table border="1" class="estilo_tabla" width="100%">
            <tbody>
                <tr class="estilo_colum">
                    <td class="estilo_colum" colspan="2"><b>CURSO: </b>'. $cursopdf->curso. '</td>
                </tr>
                <tr class="estilo_colum">
                    <td class="estilo_colum" colspan="2"><b>TIPO: </b>'. $cursopdf->tcapacitacion. '</td>
                </tr>
                <tr class="estilo_colum">
                    <td class="estilo_colum"><b>FECHA DE INICIO: </b>'. $cursopdf->inicio. '</td>
                    <td class="estilo_colum"><b>FECHA DE TÉRMINO: </b>'. $cursopdf->termino. '</td>
                </tr>
                <tr class="estilo_colum">
                    <td class="estilo_colum"><b>CLAVE: </b>'. $cursopdf->clave. '</td>
                    <td class="estilo_colum"><b>HORARIO: </b>'. $cursopdf->hini. ' A '. $cursopdf->hfin. '</td>
                </tr>
                <tr class="estilo_colum">
                    <td class="estilo_colum"><b>NOMBRE DEL TITULAR DE LA U.C: </b>'. $cursopdf->dunidad. '</td>
                    <td class="estilo_colum"><b>NOMBRE DEL INSTRUCTOR: </b>'. $cursopdf->nombre. '</td>
                </tr>
            </tbody>
        </table>';

        return $body;
    }

}
