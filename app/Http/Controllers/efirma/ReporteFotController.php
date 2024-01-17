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
        $path_files = $this->path_files;
        $array_fotos = [];
        $id_curso = $id;
        $fechapdf = "";
        $objeto = $dataFirmante = $uuid = $cadena_sello = $fecha_sello = $qrCodeBase64 =  null;

        #Distintivo
        $leyenda = DB::connection('pgsql')->table('tbl_instituto')->value('distintivo');

        #Unidad de capacitacion
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

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
                $fechapdf = $dia.' de '.$meses[$fechaCarbon->month-1].' de '.$fechaCarbon->year;
            }
        }

        $base64Images = [];
        foreach ($array_fotos as $url) {
            $imageContent = file_get_contents(storage_path("app/public/uploadFiles".$url));
            $base64 = base64_encode($imageContent);
            $base64Images[] = $base64;
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

}
