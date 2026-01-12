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
use App\Models\User;
use Vyuldashev\XmlToArray\XmlToArray;
use Spatie\ArrayToXml\ArrayToXml;
use App\Models\Tokens_icti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return Recibo::where('tbl_recibos.status_recibo', 'PAGADO')
            ->where('tbl_recibos.status_folio', 'ENVIADO')
            ->where('tbl_unidades.unidad', $unidad)
            ->where(function($query) {
                $query->whereNull('tbl_recibos.estado_reportado')
                    ->orWhere('tbl_recibos.estado_reportado', 'GENERADO');
            })
            ->where(function($query) {
                $query->where('tbl_cursos.status_curso', 'AUTORIZADO')
                    ->orWhere('tbl_cursos.status_curso', 'CANCELADO');
            })
            ->with('concepto:id,concepto')
            ->select(
                'tbl_recibos.*',
                'cat_conceptos.concepto',
                'tbl_recibos.id as id_recibo',
                'tbl_unidades.clave_contrato'
            )
            ->addSelect(DB::raw("
                CASE
                    WHEN tbl_cursos.comprobante_pago <> 'null'
                        THEN concat('uploadFiles', tbl_cursos.comprobante_pago)
                    WHEN tbl_recibos.file_pdf <> 'null'
                        THEN tbl_recibos.file_pdf
                END as file_pdf
            "))
            ->join('cat_conceptos', 'cat_conceptos.id', '=', 'tbl_recibos.id_concepto')
            ->join('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_recibos.unidad')
            ->leftJoin('tbl_cursos', 'tbl_cursos.folio_grupo', '=', 'tbl_recibos.folio_grupo');

    }

    public function applyFilters($query, array $filters)
    {
        // Filtro por fechas dentro de JSONB depositos
        if (!empty($filters['fechaInicio']) && !empty($filters['fechaFin'])) {
            $query->whereRaw("
                EXISTS (
                    SELECT 1
                    FROM jsonb_array_elements(tbl_recibos.depositos) AS deposito
                    WHERE (deposito->>'fecha')::date BETWEEN ? AND ?
                )
            ", [$filters['fechaInicio'], $filters['fechaFin']]);
        }

        // Filtro por unidad
        if (!empty($filters['unidad'])) {
            $query->where('tbl_unidades.unidad', $filters['unidad']);
        }

        // Filtro por folio de recibo
        if (!empty($filters['folio'])) {
            $query->where('tbl_recibos.folio_recibo', trim($filters['folio']));
        }

        return $query;
    }

    public function generateRF001Format($request, $usuario)
    {
        $seleccionados = $request->input('seleccionados', []);
        $dataAdd = [];
        $movimientoAdd = [];

        foreach ($seleccionados as $seleccionado) {
            list($contrato, $numRecibo, $id) = explode("_", $seleccionado);
            # code...
            $query = DB::table('tbl_recibos')
            ->leftjoin('tbl_cursos', 'tbl_recibos.id_curso', '=', 'tbl_cursos.id')
            ->leftjoin('cat_conceptos', 'cat_conceptos.id', '=', 'tbl_recibos.id_concepto')
            ->select('tbl_recibos.folio_recibo', 'tbl_recibos.unidad as unidad', 'tbl_recibos.importe', 'tbl_recibos.status_folio', 'tbl_recibos.status_recibo', 'cat_conceptos.concepto', 'tbl_recibos.depositos', 'tbl_recibos.importe', 'tbl_recibos.importe_letra', 'tbl_cursos.curso', 'tbl_recibos.descripcion', 'tbl_recibos.num_recibo', 'tbl_recibos.file_pdf')
            ->when($id, function ($query, $id) {
                return $query->where('tbl_recibos.id', '=', $id);
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
            ];
            $dataAdd[] = $JsonObj;

            //actualizar tbl recibos
            Recibo::where('num_recibo', '=', $numRecibo)
                ->update([
                    'estado_reportado' => 'GENERADO'
                ]);
        }

        // agregar movimiento - historial del movimiento del formato RF001 -- Creacion de estampa de hora exacta de creacion
       $date = Carbon::now();
       $month = $date->month < 10 ? '0'.$date->month : $date->month;
       $day = $date->day < 10 ? '0'.$date->day : $date->day;
       $hour = $date->hour < 10 ? '0'.$date->hour : $date->hour;
       $minute = $date->minute < 10 ? '0'.$date->minute : $date->minute;
       $second = $date->second < 10 ? '0'.$date->second : $date->second;
       $dateFormat = $date->year.'-'.$month.'-'.$day.'T'.$hour.':'.$minute.':'.$second;

        // crear objeto -- pero se hara en un método
       $ObjetoJson = [
        'fecha' => $dateFormat,
        'usuario' => $usuario->email,
        'unidad' => $usuario->unidad,
        'tipo' => 'GENERADO'
       ];

       $movimientoAdd[] = $ObjetoJson;

       return Rf001Model::create([
            'memorandum' => trim($request->get('consecutivo')),
            'estado' => trim('GENERADO'),
            'movimientos' => json_encode($dataAdd, JSON_UNESCAPED_UNICODE),
            'id_unidad' => $request->get('id_unidad'),
            'unidad' => $request->get('unidad'),
            'periodo_inicio' => $request->get('periodoInicio'),
            'periodo_fin' => $request->get('periodoFIn'),
            'movimiento' => json_encode($movimientoAdd, JSON_UNESCAPED_UNICODE),
            'tipo' => trim($request->get('tipoSolicitud')),
            'envia' => trim($usuario->name),
            'contador_firma' => 0,
        ]);
    }

    public function sentRF001Format($unidad, $memorandum = null)
    {
        $query = (new Rf001Model())->where('id_unidad', '=', $unidad);

        if (!empty($memorandum)) {
            $query->where(function($q) use ($memorandum) {
                $q->where('memorandum', 'LIKE', "%$memorandum%");
            });
        }

        return $query->orderBy('id', 'DESC')->paginate(10 ?? 5);
    }

    public function getDetailRF001Format($concentrado)
    {
        $rf001Detalle = new Rf001Model();
        return $rf001Detalle::findOrFail($concentrado);
    }

    // public function storeData(array $request)
    // {
    //     list($claveContrado, $numeroRecibo, $id, $idConcentrado, $numFolio) = explode("_", $request['elemento']);
    //     $registro = Rf001Model::findOrFail($idConcentrado);
    //     if (!$registro) {
    //         return false;
    //     }

    //     if ($request['details'] === 'true') {
    //         $qry = DB::table('tbl_recibos')
    //             ->leftJoin('tbl_cursos', 'tbl_recibos.id_curso', '=', 'tbl_cursos.id')
    //             ->leftJoin('cat_conceptos', 'cat_conceptos.id', '=', 'tbl_recibos.id_concepto')
    //             ->select(
    //                 'tbl_recibos.folio_recibo', 'tbl_recibos.unidad as unidad', 'tbl_recibos.importe',
    //                 'tbl_recibos.status_folio', 'tbl_recibos.status_recibo', 'cat_conceptos.concepto',
    //                 'tbl_recibos.depositos', 'tbl_recibos.importe', 'tbl_recibos.importe_letra',
    //                 'tbl_cursos.curso', 'tbl_recibos.descripcion', 'tbl_recibos.num_recibo', 'tbl_recibos.file_pdf'
    //             )
    //             ->where('tbl_recibos.id', $id)
    //             ->first();

    //         if (!$qry) {
    //             return false;
    //         }

    //         $datosExistentes = json_decode($registro->movimientos, true) ?: [];
    //         $datosExistentes[] = [
    //             'descripcion' => $qry->descripcion,
    //             'folio' => $qry->folio_recibo,
    //             'curso' => $qry->curso,
    //             'concepto' => $qry->concepto,
    //             'documento' => $qry->file_pdf,
    //             'importe' => $qry->importe,
    //             'importe_letra' => $qry->importe_letra,
    //             'depositos' => $qry->depositos
    //         ];

    //         return Rf001Model::where('id', $idConcentrado)
    //             ->update(['movimientos' => json_encode($datosExistentes, JSON_UNESCAPED_UNICODE)]);
    //     }

    //     // Eliminar movimiento
    //     $arrayDatos = json_decode($registro->movimientos, true) ?: [];
    //     $nuevoArray = [];
    //     $folioEliminado = null;

    //     foreach ($arrayDatos as $item) {
    //         if ($item['folio'] == $numFolio) {
    //             $folioEliminado = $item['folio'];
    //             continue; // No lo agregues al nuevo array
    //         }
    //         $nuevoArray[] = $item;
    //     }

    //     if ($folioEliminado) {
    //         Recibo::where('folio_recibo', $folioEliminado)
    //             ->update(['estado_reportado' => null]);
    //     }

    //     return Rf001Model::where('id', $idConcentrado)
    //         ->update(['movimientos' => json_encode($nuevoArray, JSON_UNESCAPED_UNICODE)]);
    // }

    public function storeData(array $request)
    {
        return DB::transaction(function () use ($request) {
            // 'elemento' viene como string: claveContrato_numRecibo_idRecibo_idConcentrado_folio
            list($claveContrado, $numeroRecibo, $id, $idConcentrado, $numFolio) = explode("_", $request['elemento']);

            // Bloqueamos el registro del concentrado mientras dura la transacción
            $registro = Rf001Model::lockForUpdate()->findOrFail($idConcentrado);

            // AGREGAR movimiento
            if ($request['details'] === 'true') {

                // Puedes quedarte con DB::table si no tienes modelos relacionados
                $qry = DB::table('tbl_recibos')
                    ->leftJoin('tbl_cursos', 'tbl_recibos.id_curso', '=', 'tbl_cursos.id')
                    ->leftJoin('cat_conceptos', 'cat_conceptos.id', '=', 'tbl_recibos.id_concepto')
                    ->select(
                        'tbl_recibos.folio_recibo',
                        'tbl_recibos.unidad as unidad',
                        'tbl_recibos.importe',
                        'tbl_recibos.status_folio',
                        'tbl_recibos.status_recibo',
                        'cat_conceptos.concepto',
                        'tbl_recibos.depositos',
                        'tbl_recibos.importe',
                        'tbl_recibos.importe_letra',
                        'tbl_cursos.curso',
                        'tbl_recibos.descripcion',
                        'tbl_recibos.num_recibo',
                        'tbl_recibos.file_pdf'
                    )
                    ->where('tbl_recibos.id', $id)
                    ->first();

                if (!$qry) {
                    // Lanzamos una excepción para que la transacción haga rollback
                    throw new \RuntimeException('No se encontró el recibo para agregar movimiento.');
                }

                // Decodificamos movimientos actuales
                $datosExistentes = json_decode($registro->movimientos, true) ?: [];

                // Agregamos el nuevo movimiento
                $datosExistentes[] = [
                    'descripcion'    => $qry->descripcion,
                    'folio'          => $qry->folio_recibo,
                    'curso'          => $qry->curso,
                    'concepto'       => $qry->concepto,
                    'documento'      => $qry->file_pdf,
                    'importe'        => $qry->importe,
                    'importe_letra'  => $qry->importe_letra,
                    'depositos'      => $qry->depositos,
                ];

                // Actualizamos el concentrado
                Rf001Model::where('id', $idConcentrado)->update([
                    'movimientos' => json_encode($datosExistentes, JSON_UNESCAPED_UNICODE),
                ]);

                // Actualizamos el estado del recibo a "GENERADO" (o lo que uses)
                Recibo::where('id', $id)->update([
                    'estado_reportado' => 'GENERADO',
                ]);

                // Si todo llega aquí sin excepción, la transacción hace commit automáticamente
                return true;
            }

            // ELIMINAR movimiento
            $arrayDatos     = json_decode($registro->movimientos, true) ?: [];
            $nuevoArray     = [];
            $folioEliminado = null;

            foreach ($arrayDatos as $item) {
                if ($item['folio'] == $numFolio) {
                    $folioEliminado = $item['folio'];
                    continue; // Lo saltamos, es el que queremos eliminar
                }

                $nuevoArray[] = $item;
            }

            // Si realmente eliminamos uno
            if ($folioEliminado) {
                // Actualizamos el recibo, limpiando el estado_reportado
                Recibo::where('folio_recibo', $folioEliminado)->update([
                    'estado_reportado' => null,
                ]);
            }

            // Actualizamos el JSON de movimientos en el concentrado
            Rf001Model::where('id', $idConcentrado)->update([
                'movimientos' => json_encode($nuevoArray, JSON_UNESCAPED_UNICODE),
            ]);

            return true;
        });
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
            'fecha' => $fecha,
            'generado' => Auth::user()->name,
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

    public function firmarDocumento($request)
    {
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

        $rf001 = (new Rf001Model())->findOrFail($request->idRf);
        $fechaUnica = $this->getDate($date);

        // Obtener el campo JSON y decodificarlo
        $datosExistentes  = json_decode($rf001->movimiento, true);
        $curpExistente = json_decode($rf001->firmante, true);

        $jsonObject = [
            'fecha' => $fechaUnica,
            'usuario' => Auth::user()->email,
            'unidad' => Auth::user()->unidad,
            'tipo' => 'ESPERA_SELLADO'
        ];
        $datosExistentes[] = $jsonObject;

        $jsonCurpFirmante = [
            'curp' => $request->curpObtenido,
        ];

        $curpExistente[] = $jsonCurpFirmante;

        return (new Rf001Model())->where('id', $request->idRf)->update([
            'estado' => 'ENSELLADO',
            'movimiento' => json_encode($datosExistentes, JSON_UNESCAPED_UNICODE),
            'contador_firma' => DB::raw('contador_firma + 1'),
            'firmante' => json_encode($curpExistente, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function generarDocumentoPdf($id, $unidad, $organismo): array
    {
        $uuid = $objeto = $qrCodeBase64 = null;
        $puestos = array();
        $id = base64_decode($id);
        $dataRf = (new Rf001Model())->findOrFail($id); // obtener RF001 por id

        $documentoFirma = (new DocumentosFirmar())->where('numero_o_clave', $dataRf->memorandum)
            ->WhereNotIn('status',['CANCELADO','CANCELADO ICTI'])
            ->first();

        $fechaSello = $dataRf->periodo_fin;
        $organismoPublico = DB::table('organismos_publicos')->select('nombre_titular', 'cargo_fun')->where('id', '=', $organismo)->first();

        // checa si el documento está vacio
        if (is_null($documentoFirma)) {
            # está vacio
            $bodyHtml = (new ReportService())->createBodyToXml($dataRf, $unidad, $organismoPublico);
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
            $folio = $documentoFirma->nombre_archivo;
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

            // fin de la generación
            foreach ($objeto['firmantes']['firmante'][0] as $key=>$moist) {

                $puesto = DB::table('tbl_funcionarios as funcionarios')
                ->select('funcionarios.cargo')
                ->join('tbl_organismos as tblOrganismo', 'funcionarios.id_org', '=', 'tblOrganismo.id')
                ->where(function ($query) {
                    $query->where('funcionarios.cargo', 'LIKE', '%DELEGA%')
                        ->orWhere('tblOrganismo.id_parent', 1);
                })
                ->where('funcionarios.curp', $moist['_attributes']['curp_firmante'])
                ->distinct()
                ->first();
                if(!is_null($puesto)) {
                    array_push($puestos,$puesto->cargo);
                } else {
                    array_push($puestos,'ENCARGADO');
                }
            }
        } else
        {
            // si no está sellado tendré que obtener un valor por defecto

        }

        return [$bodyMemo, $bodyRf001, $uuid, $objeto, $puestos, $qrCodeBase64, $fechaSello];
    }

    public function getDate($date)
    {
        //Creacion de estampa de hora exacta de creacion
        $month = $date->month < 10 ? '0'.$date->month : $date->month;
        $day = $date->day < 10 ? '0'.$date->day : $date->day;
        $hour = $date->hour < 10 ? '0'.$date->hour : $date->hour;
        $minute = $date->minute < 10 ? '0'.$date->minute : $date->minute;
        $second = $date->second < 10 ? '0'.$date->second : $date->second;
        $dateFormat = $date->year.'-'.$month.'-'.$day.'T'.$hour.':'.$minute.':'.$second;
        return $dateFormat;
    }

    public function getSigner($idUser)
    {
        // obtener firmante
        return User::select('tbl_funcionarios.curp', 'tbl_funcionarios.correo')
            ->join('tbl_funcionarios', 'tbl_funcionarios.correo', '=', 'users.email')
            ->where('users.id', $idUser)
            ->where('tbl_funcionarios.activo', '=' ,'true')
            ->first();
    }

    public function getFirmadoFormat($request)
    {

        return (new Rf001Model())
            ->whereIn('estado', ['REVISION', 'PARASELLAR', 'ENSELLADO', 'SELLADO'])
            ->whereBetween('periodo_fin', [
                Carbon::create(2025, 1, 1)->startOfDay(),
                Carbon::create(2026, 12, 31)->endOfDay(),
            ])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        // return (new Rf001Model())->whereIn('estado', ['REVISION', 'PARASELLAR', 'ENSELLADO', 'SELLADO'])->whereYear('periodo_fin', Carbon::now()->year)->orderBy('updated_at', 'desc')->paginate(10 ?? 5);
    }

    public function reenviarSolicitud($request)
    {
        $movimientoAdd = [];
        $rf001Id = $request->get('idRf001');
        $rf001 = (new Rf001Model())->findOrFail($rf001Id);

        $date = Carbon::now();
        $month = $date->month < 10 ? '0'.$date->month : $date->month;
        $day = $date->day < 10 ? '0'.$date->day : $date->day;
        $hour = $date->hour < 10 ? '0'.$date->hour : $date->hour;
        $minute = $date->minute < 10 ? '0'.$date->minute : $date->minute;
        $second = $date->second < 10 ? '0'.$date->second : $date->second;
        $dateFormat = $date->year.'-'.$month.'-'.$day.'T'.$hour.':'.$minute.':'.$second;

        $datosExistentes  = json_decode($rf001->movimiento, true);

        // crear objeto -- pero se hara en un método
        $ObjetoJson = [
            'fecha' => $dateFormat,
            'usuario' => Auth::user()->email,
            'unidad' => Auth::user()->unidad,
            'tipo' => 'RETORNADO'
        ];

        $datosExistentes[] = $ObjetoJson;

        return (new Rf001Model())->where('id', $rf001Id)->update([
            'estado' => 'RETORNADO',
            'movimiento' => json_encode($datosExistentes, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function sellarDocumento($request)
    {
        $date = Carbon::now();
        $formatoRf001 = $this->getDetailRF001Format($request->get('rf001Id'));
        $documento = DocumentosFirmar::WHERE('numero_o_clave', $formatoRf001->memorandum)->first();
        $xmlBase64 = base64_encode($documento->documento);
        $getToken = Tokens_icti::latest()->first();


        if (!isset($getToken)) {
            // no hay registros
            $token = (new ReportService())->generarToken();
        } else {
            $token = $getToken->token;
        }

        $response = (new ReportService())->sellarDocumento($xmlBase64, $token);

        if ($response->json() === null) {
            # generar y sellar
            $request = new Request();
            $token = (new ReportService())->generarToken();
            $response = (new ReportService())->sellarDocumento($xmlBase64, $token);
        }
        if ($response->json()['status'] == 1) {
            $decode = base64_decode($response->json()['xml']);

            // Obtener el campo JSON y decodificarlo
            $datosExistentes  = json_decode($formatoRf001->movimiento, true);
            $fechaUnica = $this->getDate($date);

            $jsonObject = [
                'fecha' => $fechaUnica,
                'usuario' => Auth::user()->email,
                'unidad' => Auth::user()->unidad,
                'tipo' => 'SELLADO'
            ];
            $datosExistentes[] = $jsonObject;

            (new Rf001Model())->where('id', $formatoRf001->id)->update([
                'estado' => 'SELLADO',
                'movimiento' => json_encode($datosExistentes, JSON_UNESCAPED_UNICODE),
            ]);

            $dataRf001 = (new Rf001Model())->findOrFail($formatoRf001->id);
            $movimiento = json_decode($dataRf001->movimientos, true);
            foreach ($movimiento as $item) {
                Recibo::where('folio_recibo', '=', $item['folio'])
                    ->update([
                        'estado_reportado' => 'SELLADO'
                    ]);
            }

            return DocumentosFirmar::where('id', $documento->id)
                ->update([
                    'status' => 'VALIDADO',
                    'uuid_sellado' => $response->json()['uuid'],
                    'fecha_sellado' => $response->json()['fecha_Sellado'],
                    'documento' => $decode,
                    'cadena_sello' => $response->json()['cadenaSello']
                ]);
        } else {
            $respuesta_icti = ['uuid' => $response->json()['uuid'], 'descripcion' => $response->json()['descripcionError']];
            return $respuesta_icti;
        }
    }

    public function getQueryCancelado($unidad)
    {
        return Recibo::where('tbl_recibos.status_recibo', 'PAGADO')
            ->where('tbl_unidades.unidad', $unidad)
            ->with('concepto:id,concepto')
            ->select('tbl_recibos.*', 'cat_conceptos.concepto', 'tbl_recibos.id as id_recibo', 'tbl_unidades.clave_contrato')
            ->addSelect(DB::raw("
                    CASE
                        WHEN tbl_cursos.comprobante_pago <> 'null' THEN concat('uploadFiles',tbl_cursos.comprobante_pago)
                        WHEN tbl_recibos.file_pdf <> 'null' THEN tbl_recibos.file_pdf
                    END as file_pdf"))
            ->join('cat_conceptos', 'cat_conceptos.id', '=', 'tbl_recibos.id_concepto')
            ->join('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_recibos.unidad')
            ->leftJoin('tbl_cursos','tbl_cursos.folio_grupo','=', 'tbl_recibos.folio_grupo');
    }

    public function actualizarEstado($id, $estado)
    {
        return (new Rf001Model())->where('id', $id)->update([
            'estado' => $estado,
        ]);
    }

    public function generarDoctoCancelado($id, $unidad, $organismo): array
    {
        $uuid = $objeto = $qrCodeBase64 = null;
        $puestos = array();
        $id = base64_decode($id);
        $dataRf = (new Rf001Model())->findOrFail($id); // obtener RF001 por id

        $documentoFirma = (new DocumentosFirmar())->where('numero_o_clave', $dataRf->memorandum)
            ->WhereNotIn('status',['CANCELADO','CANCELADO ICTI'])
            ->first();


        $organismoPublico = DB::table('organismos_publicos')->select('nombre_titular', 'cargo_fun')->where('id', '=', $organismo)->first();

        // checa si el documento está vacio
        if (is_null($documentoFirma)) {
            # está vacio
            $bodyHtml = (new ReportService())->htmlToXml($dataRf, $unidad, $organismoPublico);
            $bodyMemo = $bodyHtml['memorandum'];
        } else {
            // no está vacio
            $bodyHtml = json_decode($documentoFirma->body_html);
            $bodyMemo = $bodyHtml->memorandum;
        }

        if (isset($documentoFirma->uuid_sellado)) {
            # está o no sellado
            $objeto = json_decode($documentoFirma->obj_documento,true);
            $noOficio = json_decode(json_encode(simplexml_load_string($documentoFirma->documento_interno, "SimpleXMLElement", LIBXML_NOCDATA),true));
            $noOficio = $noOficio->{'@attributes'}->no_oficio;
            $uuid = $documentoFirma->uuid_sellado;
            $cadenaSellado = $documentoFirma->cadena_sello;
            $fechaSellado = $documentoFirma->fecha_sellado;
            $folio = $documentoFirma->nombre_archivo;
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

            // fin de la generación
            foreach ($objeto['firmantes']['firmante'][0] as $key=>$moist) {
                $puesto = DB::Table('tbl_funcionarios')->Select('cargo')->Where('curp',$moist['_attributes']['curp_firmante'])->First();
                if(!is_null($puesto)) {
                    array_push($puestos,$puesto->cargo);
                    // <td height="25px;">{{$search_puesto->cargo}}</td>
                } else {
                    array_push($puestos,'ENCARGADO');
                }
            }
        }

        return [$bodyMemo, $uuid, $objeto, $puestos, $qrCodeBase64];
    }

    public function updateAndValidateFormatRf001($id, $request) : array
    {
        $consecutivo = trim($request->get('consecutivo'));
        $checkDocumento = (new DocumentosFirmar())->where('numero_o_clave', $consecutivo)->first();
        if ($checkDocumento) {
            // Eliminar el registro en DocumentosFirmar antes de actualizar
            $checkDocumento->delete();
        }
        $qry = (new Rf001Model())->where('id', $id)
            ->update([
                'memorandum' => $request->get('consecutivo'),
                'periodo_inicio' => $request->get('periodoInicio'),
                'periodo_fin' => $request->get('periodoFIn'),
                'created_at' => Carbon::now(),
            ]);
        return ['code' => 1, 'message' => $qry];

    }

    public function setCcp($idUnidad)
    {
        return DB::table('tbl_funcionarios as funcionario')
                ->join('tbl_organismos as organismos', 'funcionario.id_org', '=', 'organismos.id')
                ->select('funcionario.nombre', 'funcionario.id_org', 'organismos.id_parent', 'funcionario.cargo')
                ->where('funcionario.activo', '=', 'true')
                ->Where('funcionario.titular', true)
                ->where(function($query) use ($idUnidad) {
                    $query->where('organismos.id_unidad', $idUnidad)
                        ->where(function($moist) use ($idUnidad) {
                            $moist->where('funcionario.cargo', 'like', 'DELEG%')
                            ->orWhere('organismos.id_parent',1);
                        })
                        ->orWhere('organismos.id_parent', 0)
                        ->orWhere('funcionario.id_org', 13);
                })
                ->orderBy('funcionario.id_org', 'asc')
                ->get();

    }

    public function actualizarRecibo($id)
    {
        try {
            $rf001 = (new Rf001Model())->findOrFail($id);
            $movimiento = json_decode($rf001->movimientos, true);
            foreach ($movimiento as $item) {
                Recibo::where('folio_recibo', '=', $item['folio'])
                    ->update([
                        'estado_reportado' => 'VALIDADO'
                    ]);
            }
            return true;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function regresarEstadoRecibo($id)
    {
        $rf001 = (new Rf001Model())->findOrFail($id);
        $movimiento = json_decode($rf001->movimientos, true);
        foreach ($movimiento as $item) {
            Recibo::where('folio_recibo', '=', $item['folio'])
                ->update([
                    'estado_reportado' => 'GENERADO'
                ]);
        }
    }

    public function generarPdfMasivo($id)
    {
        $newId = base64_decode($id);
        $referenciaRf001 = (new Rf001Model())->findOrFail($newId); // obtener RF001 por id
        $movimiento = json_decode($referenciaRf001->movimientos, true);
        $path = config('app.url') . '/storage/';
        $documentos = [];

        usort($movimiento, function($a, $b) {
            // Extraer el número después del prefijo en el campo 'folio'
            preg_match('/\d+/', $a['folio'], $matchA);
            preg_match('/\d+/', $b['folio'], $matchB);
            $numA = isset($matchA[0]) ? (int) $matchA[0] : 0;
            $numB = isset($matchB[0]) ? (int) $matchB[0] : 0;

            return $numA <=> $numB;
        });

        foreach ($movimiento as $item) {
            array_push($documentos, $path.$item['documento']);
        }

        return $documentos;
    }

    public function retornarFinanciero($id, $estado)
    {
        $id = $request->firstWhere('name', 'idRf001')['value'];
        $observaciones = $request->firstWhere('name', 'observacion')['value'];
        $registro = (new Rf001Model())->find($id);
        $comentario = json_decode($registro->observacion, true);
        $comentarioObjet = [
            'comentario' => e($observaciones)
        ];
        // Agrega el nuevo comentario
        $comentario[] = $comentarioObjet;

        return (new Rf001Model())->where('id', $id)->update([
            'confirmed' => $estado,
            'estado' => 'REVISION',
            'observacion' =>  json_encode($comentario, JSON_UNESCAPED_UNICODE),
        ]);
    }
}
