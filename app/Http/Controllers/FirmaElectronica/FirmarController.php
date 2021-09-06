<?php

namespace App\Http\Controllers\FirmaElectronica;


use setasign\Fpdi\Fpdi;
use \setasign\Fpdi\PdfParser\StreamReader;
use Illuminate\Http\Request;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Vyuldashev\XmlToArray\XmlToArray;
use App\Models\FirmaElectronica\DocumentosFirmar;

class FirmarController extends Controller {
    
    public function index() {
        $email = Auth::user()->email;
        $docsFirmar = DocumentosFirmar::where('status','!=','CANCELADO')
                        ->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x 
                            WHERE x->'_attributes'->>'email_firmante' IN ('".$email."') 
                            AND x->'_attributes'->>'firma_firmante' is null)")->orderBy('id', 'desc')->get();
        $docsFirmados = DocumentosFirmar::where('status', 'EnFirma')
                        ->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x 
                        WHERE x->'_attributes'->>'email_firmante' IN ('".$email."') 
                        AND x->'_attributes'->>'firma_firmante' <> '')")
                        ->orWhere(function($query) use ($email) {
                            $query->where('obj_documento_interno->emisor->_attributes->email', $email)
                                    ->where('status', 'EnFirma');
                        })->orderBy('id', 'desc')->get();

        $docsValidados = DocumentosFirmar::where('status', '=', 'VALIDADO')
                        ->whereRaw("EXISTS(SELECT TRUE FROM jsonb_array_elements(obj_documento->'firmantes'->'firmante'->0) x 
                        WHERE x->'_attributes'->>'email_firmante' IN ('".$email."'))")
                        ->orWhere(function($query) use ($email) {
                            $query->where('obj_documento_interno->emisor->_attributes->email', $email)
                                    ->where('status', 'VALIDADO');
                        })->orderBy('id', 'desc')->get();
        
        foreach ($docsFirmar as $value) {
            $value->base64xml = base64_encode($value->documento);
        }

        return view('layouts.FirmaElectronica.firmaElectronica', compact('email', 'docsFirmar', 'docsFirmados', 'docsValidados'));
    }

    public function update(Request $request) {
        $documento = DocumentosFirmar::where('id', $request->idFile)->first();

        $obj_documento = json_decode($documento->obj_documento, true);
        $obj_documento_interno = json_decode($documento->obj_documento_interno, true);
        foreach ($obj_documento['firmantes']['firmante'][0] as $key => $value) {
            if ($value['_attributes']['curp_firmante'] == $request->curp) {
                $value['_attributes']['fecha_firmado_firmante'] = $request->fechaFirmado;
                $value['_attributes']['no_serie_firmante'] = $request->serieFirmante; 
                $value['_attributes']['firma_firmante'] = $request->firma;
                $obj_documento['firmantes']['firmante'][0][$key] = $value;
            }
        }
        foreach ($obj_documento_interno['firmantes']['firmante'][0] as $key => $value) {
            if ($value['_attributes']['curp_firmante'] == $request->curp) {
                $value['_attributes']['fecha_firmado_firmante'] = $request->fechaFirmado;
                $value['_attributes']['no_serie_firmante'] = $request->serieFirmante; 
                $value['_attributes']['firma_firmante'] = $request->firma;
                $obj_documento_interno['firmantes']['firmante'][0][$key] = $value;
            }
        }

        $array = XmlToArray::convert($documento->documento);
        $array2 = XmlToArray::convert($documento->documento_interno);
        $array['DocumentoChis']['firmantes'] = $obj_documento['firmantes'];
        $array2['DocumentoChis']['firmantes'] = $obj_documento_interno['firmantes'];

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

        $result2 = ArrayToXml::convert($obj_documento_interno, [
            'rootElementName' => 'DocumentoChis',
            '_attributes' => [
                'version' => $array2['DocumentoChis']['_attributes']['version'],
                'fecha_creacion' => $array2['DocumentoChis']['_attributes']['fecha_creacion'],
                'no_oficio' => $array2['DocumentoChis']['_attributes']['no_oficio'],
                'dependencia_origen' => $array2['DocumentoChis']['_attributes']['dependencia_origen'],
                'asunto_docto' => $array2['DocumentoChis']['_attributes']['asunto_docto'],
                'tipo_docto' => $array2['DocumentoChis']['_attributes']['tipo_docto'],
                'xmlns' => 'http://firmaelectronica.chiapas.gob.mx/GCD/DoctoGCD',
            ],
        ]);

        DocumentosFirmar::where('id', $request->idFile)
            ->update([
                'obj_documento' => json_encode($obj_documento),
                'obj_documento_interno' => json_encode($obj_documento_interno),
                'documento' => $result,
                'documento_interno' => $result2 
            ]);

        return redirect()->route('firma.inicio')->with('warning', 'Documento firmado exitosamente!');
    }

    public function sellar(Request $request) {
        $documento = DocumentosFirmar::where('id', $request->txtIdFirmado)->first();

        // dd($documento);
        $xmlBase64 = base64_encode($documento->documento);
        $response = Http::post('https://interopera.chiapas.gob.mx/FirmadoElectronicoDocumentos/SellarXML', [
            'xml_Final' => $xmlBase64
        ]);

        // dd($response->json());

        if ($response->json()['status'] == 1) { //exitoso
            $decode = base64_decode($response->json()['xml']);
            DocumentosFirmar::where('id', $request->txtIdFirmado)
                ->update([
                    'status' => 'VALIDADO',
                    'uuid_sellado' => $response->json()['uuid'],
                    'fecha_sellado' => $response->json()['fecha_Sellado'],
                    'documento' => $decode
                ]);
            return redirect()->route('firma.inicio')->with('warning', 'Documento validado exitosamente!');
        } else {
            return redirect()->route('firma.inicio')->with('danger', 'Ocurrio un error al sellar el documento, por favor intente de nuevo');
        }
    }

    public function generarPDF(Request $request) {
        $documento = DocumentosFirmar::where('id', $request->txtIdGenerar)->first();
        $objeto = json_decode($documento->obj_documento_interno,true);
        $tipo_archivo = $documento->tipo_archivo;
        $totalFirmantes = $objeto['firmantes']['_attributes']['num_firmantes'];

        $result = $documento->link_pdf;
        $fileContent = file_get_contents($result, 'rb');
        
        $pdf = new Fpdi();
        // $pdf->addPage('L','Letter');
        $pageCount =  $pdf->setSourceFile(StreamReader::createByString($fileContent));
        
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) { 
            $tplId = $pdf->importPage($pageNo);
            if ($tipo_archivo == 'Contrato') {
                $pdf->addPage('P','A4'); //
            } else {
                $pdf->addPage('L','Letter'); //
            }
            $pdf->useTemplate($tplId);
        }

        if ($tipo_archivo == 'Contrato') {
            $pdf->addPage('P','A4');
        } else {
            $pdf->addPage('L','Letter');
        }
        
        // The new content
        $fontSize = '15';
        $fontColor = `255,0,0`;
        $left = 16;
        $top = 20;
        $text = 'Documento';

        $pdf->SetFont("helvetica", "B", 10);
        // $pdf->SetTextColor($fontColor);
        $pdf->Text($left,$top,$text);
        $pdf->Text(16, 60, 'Firmas e informacion identificadora');
        
        $pdf->SetFont("helvetica", '', 7);
        // documento
        $pdf->SetTextColor(98,98,98);
        $pdf->Text(20, 25, 'Nombre del documento:');
        $pdf->Text(20, 30, 'Fecha de constancia:');
        $pdf->Text(20, 35, 'Documento creado por:');
        $pdf->Text(20, 40, 'Numero de paginas:');
        $pdf->Text(20, 45, 'Numero de firmantes:');
        
        $pdf->SetTextColor($fontColor);
        $pdf->Text(80, 25, $objeto['archivo']['_attributes']['nombre_archivo']);
        $pdf->Text(80, 30, $documento->fecha_sellado);
        $pdf->Text(80, 35, $objeto['emisor']['_attributes']['nombre_emisor']);
        $pdf->Text(80, 40, $pageCount);
        $pdf->Text(80, 45, $totalFirmantes);

        // firmas
        $pdf->Text(77, 60, '(Las firmas son unicas para este documento)');
        $x = 20; $y = 65;
        foreach ($objeto['firmantes']['firmante'][0] as $value) {
            $pdf->SetTextColor(98,98,98);
            $pdf->Text($x, $y, 'Nombre del Firmante:');
            if ($tipo_archivo == 'Contrato') {
                $pdf->Text($x + 100, $y, 'Numero de Certificado:');
                $pdf->Text($x, $y + 5, 'Emisor:');
                $pdf->Text($x, $y + 10, 'Firma Electronica:');
                $pdf->Text($x, $y + 27, 'Fecha y hora de Firma:');
                $pdf->Text($x + 80, $y + 27, 'Puesto:');
            } else {
                $pdf->Text($x, $y + 5, 'Numero de Certificado:');
                $pdf->Text($x, $y + 10, 'Emisor:');
                $pdf->Text($x, $y + 15, 'Firma Electronica:');
                $pdf->Text($x, $y + 27, 'Fecha y hora de Firma:');
                $pdf->Text($x, $y + 32, 'Puesto:');
            }
            
            $pdf->SetTextColor($fontColor);
            $pdf->Text($x + 40, $y, $value['_attributes']['nombre_firmante']);
            if ($tipo_archivo == 'Contrato') {
                $pdf->Text($x + 130, $y, $value['_attributes']['no_serie_firmante']);
                $pdf->Text($x + 40, $y + 5, 'SERVICIO DE ADMINISTRACION TRIBUTARIA');
                $pdf->setXY($x + 39, $y + 7);
                $pdf->MultiCell(0, 4, $value['_attributes']['firma_firmante'], 0, 'J', false);
                $pdf->Text($x + 40, $y + 27, $value['_attributes']['fecha_firmado_firmante']);
                $pdf->Text($x + 100, $y + 27, $value['_attributes']['puesto_firmante']);
            } else {
                $pdf->Text($x + 40, $y + 5, $value['_attributes']['no_serie_firmante']);
                $pdf->Text($x + 40, $y + 10, 'SERVICIO DE ADMINISTRACION TRIBUTARIA');
                $pdf->setXY($x + 39, $y + 12);
                $pdf->MultiCell(0, 4, $value['_attributes']['firma_firmante'], 0, 'J', false);
                $pdf->Text($x + 40, $y + 27, $value['_attributes']['fecha_firmado_firmante']);
                $pdf->Text($x + 40, $y + 32, $value['_attributes']['puesto_firmante']);
            }
        }

        $locat = storage_path('app/public/qrcode/qrcode.png');
        $location = str_replace('\\','/', $locat);
        \PHPQRCode\QRcode::png("Test", $location, 'L', 10, 0);

        if ($tipo_archivo == 'Contrato') {
            $pdf->Image($location, 16, 270, 20, 20, "png");
            $pdf->Text(45, 275, 'Para verificar la integridad de este documento, favor de escanear el codigo QR o visitar el enlace:');
            $pdf->Text(45, 280, 'https://ejemplo.chiapas.gob.mx');
        } else {
            $pdf->Image($location, 16, 185, 20, 20, "png");
            $pdf->Text(45, 190, 'Para verificar la integridad de este documento, favor de escanear el codigo QR o visitar el enlace:');
            $pdf->Text(45, 195, 'https://ejemplo.chiapas.gob.mx');
        }
        $pdf->Output('I', $objeto['archivo']['_attributes']['nombre_archivo']);
    }

}
