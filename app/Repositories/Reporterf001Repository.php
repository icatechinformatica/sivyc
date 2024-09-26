<?php
namespace App\Repositories;

use App\Interfaces\Reporterf001Interface;
use App\Utilities\MyUtility;
use App\Models\Unidad;
use Illuminate\Support\Facades\Auth;
use App\Models\Reportes\Recibo;
use Carbon\Carbon;
use App\Models\Reportes\Rf001Model;
use App\Models\DocumentosFirmar;
use App\Services\ReportService;
use PHPQRCode\QRcode;

class Reporterf001Repository implements Reporterf001Interface
{
    public function index($user): Array
    {
        $ubicacion = Unidad::where('id', $user->unidad)->value('ubicacion');
        $unidades = Unidad::where('ubicacion',$ubicacion)->orderby('unidad')->pluck('unidad','unidad');
        $anios = MyUtility::ejercicios();
        return $data = [
            'anios' => $anios,
            'unidades' => $unidades,
        ];
    }

    public function getReciboQry($unidad)
    {
        // $recibo = Recibo::where('id_concepto', '>', 1)
        $recibo = Recibo::where('tbl_recibos.status_recibo', 'PAGADO')
            ->where('tbl_unidades.unidad', $unidad)
            ->with('concepto:id,concepto')
            ->select('tbl_recibos.*', 'cat_conceptos.concepto', 'tbl_recibos.id as id_recibo', 'tbl_unidades.clave_contrato')
            ->addSelect(\DB::raw("
                    CASE
                        WHEN tbl_cursos.comprobante_pago <> 'null' THEN concat('uploadFiles',tbl_cursos.comprobante_pago)
                        WHEN tbl_recibos.file_pdf <> 'null' THEN tbl_recibos.file_pdf
                    END as file_pdf"))
            ->join('cat_conceptos', 'cat_conceptos.id', '=', 'tbl_recibos.id_concepto')
            ->join('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_recibos.unidad')
            ->leftJoin('tbl_cursos','tbl_cursos.folio_grupo','=', 'tbl_recibos.folio_grupo');
        return $recibo;
    }

    public function generateRF001Format($request)
    {
        $seleccionados = $request->input('seleccionados', []);
        $dataAdd = [];

        foreach ($seleccionados as $seleccionado) {
            list($contrato, $numRecibo, $id) = explode("_", $seleccionado);
            # code...
            $query = \DB::table('tbl_recibos')
            ->leftjoin('tbl_cursos', 'tbl_recibos.id_curso', '=', 'tbl_cursos.id')
            ->leftjoin('cat_conceptos', 'cat_conceptos.id', '=', 'tbl_recibos.id_concepto')
            ->select('tbl_recibos.folio_recibo', 'tbl_recibos.unidad as unidad', 'tbl_recibos.importe', 'tbl_recibos.status_folio', 'tbl_recibos.status_recibo', 'cat_conceptos.concepto', 'tbl_recibos.depositos', 'tbl_recibos.importe', 'tbl_recibos.importe_letra', 'tbl_cursos.curso', 'tbl_recibos.descripcion', 'tbl_recibos.num_recibo')
            ->addSelect(\DB::raw("
                    CASE
                        WHEN tbl_cursos.comprobante_pago <> 'null' THEN concat('uploadFiles',tbl_cursos.comprobante_pago)
                        WHEN tbl_recibos.file_pdf <> 'null' THEN tbl_recibos.file_pdf
                    END as file_pdf"))
            ->when($numRecibo, function ($query, $numRecibo) {
                return $query->where('tbl_recibos.num_recibo', '=', $numRecibo);
            })->first();

            $JsonObj = [
                'descripcion' => $query->descripcion,
                'folio' => $query->folio_recibo,
                'curso' => $query->curso,
                'concepto' => $query->concepto,
                'documento' => $query->file_pdf,
                'importe' => $query->importe,
                'importe_letra' => $query->importe_letra,
                'depositos' => $query->depositos,
                'descripcion' => $query->descripcion,
            ];
            $dataAdd[] = $JsonObj;
        }

       return Rf001Model::create([
            'memorandum' => trim($request->get('consecutivo')),
            'estado' => trim('GENERADO'),
            'movimientos' => json_encode($dataAdd, JSON_UNESCAPED_UNICODE),
            'id_unidad' => $request->get('id_unidad'),
            'unidad' => $request->get('unidad'),
            'periodo_inicio' => $request->get('periodoInicio'),
            'periodo_fin' => $request->get('periodoFIn'),
        ]);
    }

    public function sentRF001Format($request)
    {
        $rf001Model = new Rf001Model();

        return $rf001Model::latest()->paginate(10 ?? 5);
    }

    public function getDetailRF001Format($concentrado)
    {
        $rf001Detalle = new Rf001Model();
        return $rf001Detalle::findOrFail($concentrado);
    }

    public function storeData(array $request)
    {
        list($claveContrado, $numeroRecibo, $id, $idConcentrado, $numFolio) = explode("_", $request['elemento']);
        $rf001Detalle = new Rf001Model();

        $registro = $rf001Detalle->findOrFail($idConcentrado);
        $insertData = [];
        $dataAdd = [];

        if ($registro) {

            if ($request['details'] === 'true') {

                $qry = \DB::table('tbl_recibos')
                ->leftjoin('tbl_cursos', 'tbl_recibos.id_curso', '=', 'tbl_cursos.id')
                ->leftjoin('cat_conceptos', 'cat_conceptos.id', '=', 'tbl_recibos.id_concepto')
                ->select('tbl_recibos.folio_recibo', 'tbl_recibos.unidad as unidad', 'tbl_recibos.importe', 'tbl_recibos.status_folio', 'tbl_recibos.status_recibo', 'cat_conceptos.concepto', 'tbl_recibos.depositos', 'tbl_recibos.importe', 'tbl_recibos.importe_letra', 'tbl_cursos.curso', 'tbl_recibos.descripcion', 'tbl_recibos.num_recibo')
                ->addSelect(\DB::raw("
                        CASE
                            WHEN tbl_cursos.comprobante_pago <> 'null' THEN concat('uploadFiles',tbl_cursos.comprobante_pago)
                            WHEN tbl_recibos.file_pdf <> 'null' THEN tbl_recibos.file_pdf
                        END as file_pdf"))
                ->when($numeroRecibo, function ($query, $numeroRecibo) {
                    return $query->where('tbl_recibos.num_recibo', '=', $numeroRecibo);
                })->first();

                # verdadero
                // get the actual JSON records
                 // Obtener el campo JSON y decodificarlo
                $datosExistentes  = json_decode($registro->movimientos, true);
                $JsonObj = [
                    'descripcion' => $qry->descripcion,
                    'folio' => $qry->folio_recibo,
                    'curso' => $qry->curso,
                    'concepto' => $qry->concepto,
                    'documento' => $qry->file_pdf,
                    'importe' => $qry->importe,
                    'importe_letra' => $qry->importe_letra,
                ];
                // Add a new Json Object to existing Array
                $datosExistentes[] = $JsonObj;
                $updateData = [
                    'movimientos' => json_encode($datosExistentes, JSON_UNESCAPED_UNICODE)
                ];
                return Rf001Model::where('id', $idConcentrado)->update($updateData);
            } else {
                # si es falso -- implica que se tiene que quitar del arreglo json
                $jsonObject = $registro->movimientos;
                $arrayDatos = json_decode($jsonObject, true);
                $objetoBorrar = ['folio' => $numFolio];

                for ($i=0; $i < count($arrayDatos); $i++) {
                    if (
                        $arrayDatos[$i]['folio'] == $objetoBorrar['folio']
                    ) {
                        unset($arrayDatos[$i]);
                        $arrayDatos = array_values($arrayDatos);
                        break;
                    }
                }
                $deleteData = [
                    'movimientos' => json_encode($arrayDatos)
                ];
                return Rf001Model::where('id', $idConcentrado)->update($deleteData);
            }

        }
    }

    public function updateFormatoRf001($request, $id)
    {
        $modeloRf001 = new Rf001Model();
        $updateRecord = $modeloRf001->findOrFail($id);
        $updateRecord->memorandum = $request['consecutivo'];
        $updateRecord->id_unidad = $request['id_unidad'];
        $updateRecord->periodo_inicio = $request['periodoInicio'];
        $updateRecord->periodo_fin = $request['periodoFIn'];
        $updateRecord->unidad = $request['unidad'];
        return $updateRecord->save();
    }

    public function storeComment($request)
    {
        #nuevo comentario agregar
        $date = Carbon::now();
        $fecha = $date->format('Y-m-d');

        $newComment = [
            'comentario' => $request['observacion'],
            'fecha' => $fecha
        ];

        $detalleFolio = (new Rf001Model())->where('memorandum', $request['memo'])->first();

        $datosExistentes  = json_decode($detalleFolio->movimientos, true);

        for ($i=0; $i < count($datosExistentes); $i++) {
            if (isset($datosExistentes[$i]['observaciones']) && $datosExistentes[$i]['folio'] == $request['folio']) {
                $observaciones = json_decode($datosExistentes[$i]['observaciones'], true);
                if (is_array($observaciones)) {
                    $observaciones[] = $newComment;
                    $datosExistentes[$i]['observaciones'] = json_encode($observaciones);
                }
                break;
            } elseif($datosExistentes[$i]['folio'] == $request['folio']) {
                # agregar nuevo
                $observaciones[] = $newComment;
                $datosExistentes[$i]['observaciones'] = json_encode($observaciones);
            }
        }

        // Vuelve a codificar a JSON
        $updateComment = [
            'movimientos' => json_encode($datosExistentes)
        ];
        return (new Rf001Model())->where('memorandum', $request['memo'])->update($updateComment);
    }

    public function updateRf001($id)
    {
        return (new Rf001Model())->where('id', $id)->update([
            'estado' => 'FIRMADO',
        ]);
    }

    public function firmarDocumento($Request)
    {
    }

    public function generarDocumentoPdf($id, $unidad, $organismo): array
    {
        $id = base64_decode($id);
        $dataRf = (new Rf001Model())->findOrFail($id); // obtener RF001 por id

        $documentoFirma = (new DocumentosFirmar())->where('numero_o_clave', $dataRf->memorandum)
            ->WhereNotIn('status',['CANCELADO','CANCELADO ICTI'])
            ->Where('tipo_archivo','supre')
            ->first();

        // checa si el documento está vacio
        if (is_null($documentoFirma)) {
            # está vacio
            $bodyHtml = (new ReportService())->createBodyToXml($dataRf, $unidad, $organismo);
            $bodyMemo = $bodyHtml['memorandum'];
            $bodyRf001 = $bodyHtml['formatoRf001'];
        } else {
            // no está vacio
            $bodyHtml = json_decode($documentoFirma->body_html);
            $bodyMemo = $bodyHtml->memorandum;
            $bodyRf001 = $bodyHtml->formatoRf001;
        }

        if (isset($documentoFirma->uuid_sellado)) {
            # está o no sellado
            $objeto = json_decode($documentoFirma->obj_documento,true);
            $noOficio = json_decode(json_encode(simplexml_load_string($documentoFirma->documento_interno, "SimpleXMLElement", LIBXML_NOCDATA),true));
            $noOficio = $noOficio->{'@attributes'}->no_oficio;
            $uuid = $documentoFirma->uuid_sellado;
            $cadenaSellado = $documentoFirma->cadena_sello;
            $fechaSellado = $documentoFirma->fecha_sellado;
            $totalFirmantes = $objeto['firmantes']['_attributes']['num_firmantes'];
            // verificar si existe el enlace de verificacion

            if (isset($documentoFirma->link_verificacion)) {
                // se encuentra
                $verificacion = $documentoFirma->link_verificacion;
            } else {
                // no se encuentra
                $documentoFirma->link_verificacion = $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumento/consulta/Certificado3?guid=$uuid&no_folio=$noOficio";
                $documentoFirma->save();
            }
            ob_start();
            QRcode::png($verificacion);
            $qrCodeData = ob_get_contents();
            ob_end_clean();
            $qrCodeBase64 = base64_encode($qrCodeData);
        }

        return [$bodyMemo, $bodyRf001];
    }
}
