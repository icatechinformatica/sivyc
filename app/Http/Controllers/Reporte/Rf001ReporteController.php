<?php

namespace App\Http\Controllers\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Interfaces\Reporterf001Interface;
use Illuminate\Support\Facades\DB;
use Vyuldashev\XmlToArray\XmlToArray;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Crypt;
use App\Models\tbl_unidades;
use App\Models\DocumentosFirmar;
use Carbon\Carbon;
use App\Models\Reportes\Rf001Model;

class Rf001ReporteController extends Controller
{
    private Reporterf001Interface $rfoo1Repository;
    public function __construct(Reporterf001Interface $rfoo1Repository)
    {
        $this->rfoo1Repository = $rfoo1Repository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //
        $estado = '';
        $documento = DocumentosFirmar::WHERE('id', $request->getIdFile)->first();
        $date = Carbon::now();

        $obj_documento = json_decode($documento->obj_documento, true);

        if (empty($obj_documento['archivo']['_attributes']['md5_archivo'])) {
            $obj_documento['archivo']['_attributes']['md5_archivo'] = $documento->md5_file;
        }

        foreach ($obj_documento['firmantes']['firmante'][0] as $key => $value) {
            if ($value['_attributes']['curp_firmante'] == $request->curpObtenido) {
                $value['_attributes']['fecha_firmado_firmante'] = $request->fechaFirmado;
                $value['_attributes']['no_serie_firmante'] = $request->serieFirmante;
                $value['_attributes']['firma_firmante'] = $request->firma;
                $value['_attributes']['certificado'] = $request->certificado;
                $obj_documento['firmantes']['firmante'][0][$key] = $value;
            }
        }

        $array = XmlToArray::convert($documento->documento);
        $array['DocumentoChis']['firmantes'] = $obj_documento['firmantes'];

        // if (isset($obj_documento['anexos'])) {
        //     $ArrayXml = [
        //         "emisor" => $obj_documento['emisor'],
        //         "archivo" => $obj_documento['archivo'],
        //         "anexos" =>  $obj_documento['anexos'],
        //         "firmantes" => $obj_documento['firmantes'],
        //     ];
        //     $obj_documento = $ArrayXml;
        // }

        $result = ArrayToXml::convert($obj_documento, [
            'rootElementName' => 'DocumentoChis',
            '_attributes' => [
                'version' => $array['DocumentoChis']['_attributes']['version'],
                'fecha_creacion' => $array['DocumentoChis']['_attributes']['fecha_creacion'],
                'no_oficio' => $array['DocumentoChis']['_attributes']['no_oficio'],
                'dependencia_origen' => $array['DocumentoChis']['_attributes']['dependencia_origen'],
                'asunto_docto' => $array['DocumentoChis']['_attributes']['asunto_docto'],
                'tipo_docto' =>  $array['DocumentoChis']['_attributes']['tipo_docto'],
                'xmlns' => 'http://firmaelectronica.chiapas.gob.mx/GCD/DoctoGCD',
            ],
        ]);


        DocumentosFirmar::where('id', $request->getIdFile)
            ->update([
                'obj_documento' => json_encode($obj_documento),
                'documento' => $result,
            ]);

        $bandera = Crypt::encrypt('solicitud');
        $encrypted = base64_encode($bandera);
        $encrypted = str_replace(['+', '/', '='], ['-', '_', ''], $encrypted);

        // actualizar firma electronica
        $rf001 = (new Rf001Model())->findOrFail($request->idRf);
        $fechaUnica = $this->rfoo1Repository->getDate($date);

        if ($request->duplicidad == 1) {
            $estado = 'FIRMADO';
        } else {
            switch ($rf001->estado) {
                case 'APROBADO':
                    $estado = 'ENFIRMA';
                    break;
                case 'FIRMADO':
                    $estado = 'SELLADO';
                    break;
                case 'ENFIRMA':
                    $estado = 'FIRMADO';
                    break;
            }
        }

        // Obtener el campo JSON y decodificarlo
        $datosExistentes  = json_decode($rf001->movimiento, true);
        $curpExistente = json_decode($rf001->firmante, true);
        // obtener revision
        $revisionLocal = collect(json_decode($rf001->movimiento, true))->first(function ($item) {
            return isset($item['tipo']) && ($item['tipo'] === 'REVISION_LOCAL');
        });

        if ($revisionLocal) {
            # si hay registros
            $revision = 'REVISION_GENERAL';
        } else {
            #no hay registros
            $revision = 'REVISION_LOCAL';
        }
        //GUARDAMOS INFORMACIÓN DEL MOVIMIENTO EN LA TABLA dateFormat
        $jsonObject = [
            'fecha' => $fechaUnica,
            'usuario' => Auth::user()->email,
            'unidad' => Auth::user()->unidad,
            'tipo' => $revision
        ];

        $jsonCurpFirmante = [
            'curp' => $request->curpObtenido,
        ];

        $datosExistentes[] = $jsonObject;
        $curpExistente[] = $jsonCurpFirmante;

        (new Rf001Model())->where('id', $request->idRf)->update([
            'estado' => $estado,
            'movimiento' => json_encode($datosExistentes, JSON_UNESCAPED_UNICODE),
            'contador_firma' => \DB::raw('contador_firma + 1'),
            'firmante' => json_encode($curpExistente, JSON_UNESCAPED_UNICODE),
        ]);

        return redirect()->route('reporte.rf001.sent', ['generado' => $encrypted])->with('message', 'Documento firmado exitosamente!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // datos de información!!!
        $organismo = Auth::user()->id_organismo;
        $unidad = Auth::user()->unidad;
        return (new ReportService())->xmlFormat($id, $organismo, $unidad, Auth::user());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $solicitud)
    {
        $idFormato = $id;
        return view('reportes.rf001.formatofirma', compact('idFormato', 'solicitud'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function generate_report($id)
    {
        $organismo = Auth::user()->id_organismo;
        $unidad = Auth::user()->unidad;
        $data = (new ReportService())->xmlFormat($id, $organismo, $unidad, Auth::user());
        return response()->json([
            'resp' => $data
         ], Response::HTTP_CREATED);
    }

    public function efirma()
    {
        return view('reportes.rf001.child.firma')->render();
    }

    public function getTokenFirma(Request $request)
    {
        return (new ReportService())->generarToken();
    }

    public function getForma($id)
    {
        // realizar consulta para enviar y generar documento pdf
        $unidad = tbl_unidades::where('id', Auth::user()->unidad)->first();
        $instituto = DB::table('tbl_instituto')->first();
        // Decodificar el campo cuentas_bancarias
        $cuentas_bancarias = json_decode($instituto->cuentas_bancarias, true); // true convierte el JSON en un array asociativo
        $cuentaBancaria = $cuentas_bancarias[$unidad->unidad]['BBVA'];
        $getFormatoRf = $this->rfoo1Repository->getDetailRF001Format($id);
        return (new ReportService())->renderHtmlForma($getFormatoRf, $cuentaBancaria, 'hola');
    }

    public function reporte_cancelado($id)
    {
        //genXmlFormato
        $organismo = Auth::user()->id_organismo;
        $unidad = Auth::user()->unidad;
        $data = (new ReportService())->genXmlFormato($id, $organismo, $unidad, Auth::user());
        return response()->json([
            'resp' => $data
         ], Response::HTTP_CREATED);
    }
}
