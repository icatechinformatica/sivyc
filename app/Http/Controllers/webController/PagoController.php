<?php
/* Creador: Orlando Chavez */
namespace App\Http\Controllers\webController;

use App\Models\pago;
use App\Models\instructor;
use App\Models\contratos;
use App\Models\folio;
use App\Models\directorio;
use App\Models\especialidad_instructor;
use App\Models\contrato_directorio;
use App\Models\Calendario_Entrega;
use App\Models\DocumentosFirmar;
use Illuminate\Http\Request;
use Redirect,Response;
use App\Http\Controllers\Controller;
use PDF;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;
use App\Models\tbl_unidades;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportExcel;
use ZipArchive;
use setasign\Fpdi\Fpdi;
use File;
use App\Interfaces\DocumentacionPagoInstructorInterface;
use App\extensions\FPDIWithRotation;
use Illuminate\Support\Facades\Http;
use setasign\Fpdi\PdfParser\StreamReader;

class PagoController extends Controller
{
    private DocumentacionPagoInstructorInterface $docValidadoPagoRepository;
    private $path_files;
    public function __construct(DocumentacionPagoInstructorInterface $docValidadoPagoRepository)
    {
        $this->docValidadoPagoRepository = $docValidadoPagoRepository;
        $this->path_files = env("APP_URL").'/storage/';
    }
    public function expediente_pagos_merge()
    {
        set_time_limit(0);
        $asistencia_pdf = $reporte_pdf = NULL;
        $start = microtime(true);
        $archivosFull = DB::Table('tbl_cursos')->Select('tbl_cursos.id','tbl_cursos.clave','id_instructor','instructor_mespecialidad',
            'contratos.id_contrato','arch_solicitud_pago', DB::raw("soportes_instructor->>'archivo_bancario' as archivo_bancario"),
            'tbl_cursos.pdf_curso','tabla_supre.doc_validado','pagos.arch_asistencia','pagos.arch_evidencia','contratos.arch_contrato',
            'pagos.arch_pago', 'folios.id_supre','folios.id_folios',DB::raw("soportes_instructor->>'archivo_ine' as archivo_ine"))
            ->Join('pagos','pagos.id_curso','tbl_cursos.id')
            ->Join('folios','folios.id_cursos','tbl_cursos.id')
            ->Join('tabla_supre','tabla_supre.id','folios.id_supre')
            ->Join('contratos','contratos.id_contrato','pagos.id_contrato')
            ->Where('status_transferencia','PAGADO')
            ->whereDate('tbl_cursos.inicio', '>=', '2024-01-01')
            ->whereDate('pagos.fecha_transferencia', '>=', '2024-11-29')->whereDate('fecha_transferencia', '<=', '2024-12-31')
            // ->Where('pagos.id_curso', '242260259')
            // ->First();
            ->Get();
            // dd($archivosFull);
            $ghostscriptPath = "C:\\Program Files\\gs\\gs10.04.0\\bin\\gswin64c.exe";
        foreach($archivosFull as $pointer => $archivos)
        {

            if($pointer > 107) {
            // 239, 244
            // if($pointer == 4) {echo 'a';}
            $memoval = especialidad_instructor::WHERE('id_instructor',$archivos->id_instructor) // obtiene la validacion del instructor
                ->whereJsonContains('hvalidacion', [['memo_val' => $archivos->instructor_mespecialidad]])->value('hvalidacion');
            if(isset($memoval)) {
                foreach($memoval as $me) {
                    if(isset($me['memo_val']) && $me['memo_val'] == $archivos->instructor_mespecialidad) {
                        $validacion_ins = $me['arch_val'];
                        break;
                    }
                }
            }

        // }

            // $asistenciaController = new AsistenciaController();
            // $asistencia_pdf = $asistenciaController->asistencia_pdf($archivos->id,true);
            // $reporteController = new ReporteFotController();
            // $reporte_pdf = $reporteController->repofotoPdf($archivos->id,true);

            // if(is_null($asistencia_pdf)) {
            //     $asistencia_pdf = $archivos->arch_asistencia;
            // }
            // if(is_null($reporte_pdf)) {
            //     $reporte_pdf = $archivos->arch_evidencia;
            // }
            $check_contrato_efirma = DB::Table('documentos_firmar')->Where('numero_o_clave',$archivos->clave)->Where('tipo_archivo','Contrato')->Where('status','VALIDADO')->value('id');
            if(is_null($check_contrato_efirma)) {
                $contrato_pdf = $archivos->arch_contrato;
            } else {
                $contratoController = new ContratoController();
                $contrato_pdf = $contratoController->contrato_pdf($archivos->id_contrato);
            }

            $check_valsupre_efirma = DB::Table('documentos_firmar')->Where('numero_o_clave',$archivos->clave)->Where('tipo_archivo','valsupre')->Where('status','VALIDADO')->value('id');
            if(is_null($check_valsupre_efirma)) {
                $valsupre_pdf = $archivos->doc_validado;
            } else {
                $valsupreController = new supreController();
                $valsupre_pdf = $valsupreController->valsupre_pdf(base64_encode($archivos->id_supre));
            }

            $check_solpa_efirma = DB::Table('documentos_firmar')->Where('numero_o_clave',$archivos->clave)->Where('tipo_archivo','Solicitud Pago')->Where('status','VALIDADO')->value('id');
            if(is_null($check_solpa_efirma)) {
                $solpa_pdf = $archivos->arch_solicitud_pago;
            } else {
                $pagoController = new ContratoController();
                $solpa_pdf = $pagoController->solicitudpago_pdf($archivos->id_folios);
            }

            if(!str_contains($archivos->arch_pago, 'sivyc.')){
                $arch_pago = 'https://sivyc.icatech.gob.mx'.$archivos->arch_pago;
            } else {
                $arch_pago = $archivos->arch_pago;
            }

                $pdf = new FPDI();

                $fileUrls = [
                    $arch_pago,
                    $solpa_pdf,
                    $archivos->archivo_bancario,
                    // $validacion_ins,
                    $archivos->pdf_curso,
                    $valsupre_pdf,
                    // $asistencia_pdf,
                    // $reporte_pdf,
                    $contrato_pdf,
                    $archivos->archivo_ine
                ];

                $localFiles = [];

                // Descargar los archivos PDF y guardarlos en archivos temporales
                foreach ($fileUrls as $key => $url) {
                    // Crear un archivo temporal
                    $tempFile = tempnam(sys_get_temp_dir(), 'pdf');

                    // Obtener el contenido del archivo
                    if (filter_var($url, FILTER_VALIDATE_URL)) {
                        $contents = file_get_contents($url);
                    } else {
                        $contents = $url;
                    }

                    // Guardar el contenido en el archivo temporal
                    file_put_contents($tempFile, $contents);

                    $pdfFile = str_replace('.tmp', 'new.tmp', $tempFile);
                    $localFiles[] = $pdfFile;
                    // rename($tempFile, $pdfFile);
                    // echo $pdfFile;
                    // Comando de Ghostscript para convertir a PDF 1.4
                    $command = "gswin64c -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -o \"{$pdfFile}\" \"{$tempFile}\"";
                    // dd($command);
                                // gswin64c -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -o "C:\Users\Tec academica\AppData\Local\Temp\pdfCF1F.pdf" "C:\Users\Tec academica\AppData\Local\Temp\pdfCF1F.pdf"
                                // gswin64c -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -o "C:\Games\complete.pdf" "C:\Games\testing.pdf"


                    // Usar exec para ejecutar el comando
                    exec($command, $output, $return_var); // Captura stderr

                    if ($return_var === 0) {
                        // Conversión exitosa
                        // echo "PDF convertido a versión 1.4 y guardado en: {$pdfFile}\n";
                    } else {
                        // Ocurrió un error
                        echo "Error al convertir el PDF: {$pdfFile}\n";
                        echo implode("\n", $output); // Muestra los mensajes de error
                    }
                }

                // Añadir cada página de los PDFs al nuevo documento
                foreach ($localFiles as $file) {
                    // try {
                        // printf($file . '//');
                        $pageCount = $pdf->setSourceFile($file);
                        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                            $templateId = $pdf->importPage($pageNo);
                            $size = $pdf->getTemplateSize($templateId);
                            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                            $pdf->addPage($orientation, [$size['width'], $size['height']]);
                            $pdf->useTemplate($templateId);
                        }
                    // } catch (\Exception $e) {
                    //     echo "Error al procesar el archivo: {$file}. " . $e->getMessage();
                    // }
                }

                // Guarda el PDF combinado
                $fileName = 'app/public/temporal/'.$archivos->clave.'.pdf';
                $outputPath = storage_path($fileName);
                $pdf->Output($outputPath, 'F');

                foreach ($localFiles as $file) {
                    unlink($file);
                }
            }
        }
            $time_elapsed_secs = microtime(true) - $start;
            printf($time_elapsed_secs);
    }

    public function fill(Request $request)
    {
        $instructor = new instructor();
        $input = $request->numero_contrato;
        $newsAll = $instructor::where('id', $input)->first();
        return response()->json($newsAll, 200);
    }

    public function index(Request $request)
    {
        $array_ejercicio =[];
        $año_pointer = CARBON::now()->format('Y');
        /**
         * busqueda de pago
         */
        $tipoPago = $request->get('tipo_pago');
        $busqueda_pago = $request->get('busquedaPorPago');
        $tipoStatus = $request->get('tipo_status');
        $unidad = $request->get('unidad');
        $mes = $request->get('mes');

        if($request->ejercicio == NULL)
        {
            $año_referencia = '01-01-' . CARBON::now()->format('Y');
            $año_referencia2 = '31-12-' . CARBON::now()->format('Y');
        }
        else
        {
            $año_referencia = '01-01-' . $request->ejercicio;
            $año_referencia2 = '31-12-' . $request->ejercicio;
            $año_pointer = $request->ejercicio;
        }

        for($x = 2020; $x <= intval(CARBON::now()->format('Y')); $x++)
        {
            array_push($array_ejercicio, $x);
        }

        $contrato = new contratos();
        // obtener el usuario y su unidad
        $unidadUser = Auth::user()->unidad;

        // obtener el id
        $userId = Auth::user()->id;

        $roles = DB::table('role_user')
            ->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
            ->SELECT('roles.slug AS role_name')
            ->WHERE('role_user.user_id', '=', $userId)
            ->GET();

        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->WHERE('cct','LIKE','07EI%')->GET();
        $contratos_folios = $contrato::busquedaporpagos($tipoPago, $busqueda_pago, $tipoStatus, $unidad, $mes)
            ->WHEREIN('folios.status', ['Verificando_Pago','Pago_Verificado','Pago_Rechazado','Finalizado'])
            ->WHERE('tbl_cursos.inicio', '>=', $año_referencia)
            ->WHERE('tbl_cursos.inicio', '<=', $año_referencia2)
            ->LEFTJOIN('folios','folios.id_folios', '=', 'contratos.id_folios')
            ->LEFTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
            ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
            ->LEFTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
            ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
            ->LeftJoin('tbl_cursos_expedientes','tbl_cursos_expedientes.id_curso','tbl_cursos.id')
            ->leftJoinSub(
                DB::table('documentos_firmar')
                    ->selectRaw('DISTINCT ON (numero_o_clave) *')
                    ->where('tipo_archivo', 'Contrato')
                    ->orderBy('numero_o_clave')
                    ->orderBy('id', 'desc'), // O puedes usar fecha si tienes un campo 'fecha_creacion'
                'documentos_firmar',
                function ($join) {
                    $join->on('documentos_firmar.numero_o_clave', '=', 'tbl_cursos.clave');
                }
            )
            // ->leftJoin('documentos_firmar', function($join) {
            //     $join->on('documentos_firmar.numero_o_clave', '=', 'tbl_cursos.clave')
            //             ->where('documentos_firmar.tipo_archivo', '=', 'Contrato');
            // })
            ->LEFTJOIN('instructores','instructores.id', '=', 'tbl_cursos.id_instructor')
            ->orderBy('pagos.created_at', 'desc');

        //dd($roles[0]->role_name);
        if (in_array($roles[0]->role_name, ['financiero_verificador','financiero recepcion digital','financiero_pago'])) {
            $contratos_folios = $contrato::busquedaporpagos($tipoPago, $busqueda_pago, $tipoStatus, $unidad, $mes)
                ->WHEREIN('folios.status', ['Contrato_Validado','Verificando_Pago','Pago_Verificado','Pago_Rechazado',
                            'Finalizado'])
                ->WHERE('tbl_cursos.inicio', '>=', $año_referencia)
                ->WHERE('tbl_cursos.inicio', '<=', $año_referencia2)
                ->WHERE('pagos.status_recepcion', '!=', null)
                ->LEFTJOIN('folios','folios.id_folios', '=', 'contratos.id_folios')
                ->LEFTJOIN('tbl_cursos', 'folios.id_cursos', '=', 'tbl_cursos.id')
                ->LEFTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
                ->LEFTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                ->LeftJoin('tbl_cursos_expedientes','tbl_cursos_expedientes.id_curso','tbl_cursos.id')
                ->leftJoin('documentos_firmar', function($join) {
                    $join->on('documentos_firmar.numero_o_clave', '=', 'tbl_cursos.clave')
                            ->where('documentos_firmar.tipo_archivo', '=', 'Contrato');
                        //  ->Where('documentos_firmar.status', '=', 'VALIDADO');
                })
                ->JOIN('instructores','instructores.id', '=', 'tbl_cursos.id_instructor')
                ->GroupBy('contratos.id_contrato','folios.permiso_editar','folios.status','pagos.recepcion','folios.id_folios', 'folios.id_supre','pagos.status_recepcion','pagos.created_at','pagos.arch_solicitud_pago',
                    'pagos.arch_asistencia','pagos.arch_evidencia','pagos.fecha_agenda','pagos.arch_solicitud_pago','pagos.agendado_extemporaneo',
                    'pagos.observacion_rechazo_recepcion','pagos.arch_calificaciones','pagos.arch_evidencia','tbl_cursos.id_instructor','tbl_cursos.soportes_instructor',
                    'tbl_cursos.instructor_mespecialidad','tbl_cursos.tipo_curso', 'tbl_cursos.pdf_curso','tbl_cursos.modinstructor','tabla_supre.doc_validado',
                    'instructores.archivo_alta','instructores.archivo_bancario','instructores.archivo_ine', 'tbl_cursos.nombre','pagos.fecha_envio',
                    'pagos.updated_at','pagos.status_transferencia','arch_pago','edicion_pago','tbl_cursos_expedientes.id','tbl_cursos.clave')
                ->orderBy('pagos.created_at', 'desc');
            } elseif(!in_array($roles[0]->role_name, ['financiero_verificador','financiero recepcion digital','financiero_pago','admin'])) {
                $unidadPorUsuario = DB::table('tbl_unidades')->WHERE('id', $unidadUser)->FIRST();
                $contratos_folios = $contratos_folios->WHERE('tbl_unidades.ubicacion', '=', $unidadPorUsuario->ubicacion);
            }

            $contratos_folios = $contratos_folios->PAGINATE(50, [
                'contratos.id_contrato', 'contratos.numero_contrato', 'contratos.cantidad_letras1','contratos.fecha_status','contratos.arch_contrato',
                'contratos.unidad_capacitacion', 'contratos.municipio', 'contratos.fecha_firma','contratos.docs','contratos.observacion',
                'contratos.arch_factura', 'contratos.arch_factura_xml','folios.status','folios.id_folios','folios.id_supre','pagos.recepcion',
                'folios.permiso_editar','pagos.status_recepcion','pagos.arch_solicitud_pago','pagos.fecha_agenda','pagos.created_at','pagos.arch_asistencia','pagos.arch_evidencia',
                'pagos.arch_calificaciones','pagos.arch_evidencia','pagos.agendado_extemporaneo','pagos.observacion_rechazo_recepcion',
                'tbl_cursos.id_instructor','tbl_cursos.instructor_mespecialidad','tbl_cursos.tipo_curso','tbl_cursos.pdf_curso','tbl_cursos.modinstructor','tbl_cursos.soportes_instructor',
                'tabla_supre.doc_validado','instructores.archivo_alta','instructores.archivo_bancario','instructores.archivo_ine','arch_pago',
                'tbl_cursos.nombre','pagos.fecha_envio','pagos.updated_at','pagos.status_transferencia','edicion_pago',
                DB::raw('(DATE_PART(\'day\', CURRENT_DATE - contratos.fecha_status::timestamp)) >= 7 as alerta'),
                DB::raw('(DATE_PART(\'day\', CURRENT_DATE - pagos.updated_at::timestamp)) >= 7 as alerta_financieros'),
                DB::raw("CASE
                    WHEN tbl_cursos_expedientes.id IS NULL
                    OR (jsonb_extract_path_text(tbl_cursos_expedientes.vinculacion, 'status_dpto') IS NULL
                    AND jsonb_extract_path_text(tbl_cursos_expedientes.academico, 'status_dpto') IS NULL
                    AND jsonb_extract_path_text(tbl_cursos_expedientes.administrativo, 'status_dpto') IS NULL)
                    OR (jsonb_extract_path_text(tbl_cursos_expedientes.vinculacion, 'status_dpto') NOT IN ('VALIDADO', 'ENVIADO')
                    AND jsonb_extract_path_text(tbl_cursos_expedientes.academico, 'status_dpto') NOT IN ('VALIDADO', 'ENVIADO')
                    AND jsonb_extract_path_text(tbl_cursos_expedientes.administrativo, 'status_dpto') NOT IN ('VALIDADO', 'ENVIADO'))
                    THEN TRUE
                    ELSE FALSE
                 END as status_dpto_general"),
                 DB::raw("(SELECT
            CASE
                WHEN (EXISTS (SELECT 1 FROM pagos p WHERE p.arch_solicitud_pago IS NOT NULL AND p.id_contrato = contratos.id_contrato)
                    OR EXISTS (SELECT 1 FROM documentos_firmar df WHERE df.tipo_archivo = 'Solicitud Pago' AND df.status = 'VALIDADO' AND df.numero_o_clave = tbl_cursos.clave))
                    AND (EXISTS (SELECT 1 FROM tabla_supre ts WHERE ts.doc_validado IS NOT NULL AND ts.id = folios.id_supre)
                    OR EXISTS (SELECT 1 FROM documentos_firmar df WHERE df.tipo_archivo = 'valsupre' AND df.status = 'VALIDADO' AND df.numero_o_clave = tbl_cursos.clave))
                    AND (EXISTS (SELECT 1 FROM contratos c WHERE c.arch_contrato IS NOT NULL AND c.id_contrato = contratos.id_contrato)
                    OR EXISTS (SELECT 1 FROM documentos_firmar df WHERE df.tipo_archivo = 'Contrato' AND df.status = 'VALIDADO' AND df.numero_o_clave = tbl_cursos.clave))
                    AND (EXISTS (SELECT 1 FROM pagos p WHERE p.arch_asistencia IS NOT NULL AND p.id_contrato = contratos.id_contrato)
                    OR EXISTS (SELECT 1 FROM documentos_firmar df WHERE df.tipo_archivo = 'Lista de asistencia' AND df.status = 'VALIDADO' AND df.numero_o_clave = tbl_cursos.clave)
                    OR tbl_cursos.tipo_curso != 'CURSO')
                    AND (EXISTS (SELECT 1 FROM pagos p WHERE p.arch_evidencia IS NOT NULL AND p.id_contrato = contratos.id_contrato)
                    OR EXISTS (SELECT 1 FROM documentos_firmar df WHERE df.tipo_archivo = 'Reporte fotografico' AND df.status = 'VALIDADO' AND df.numero_o_clave = tbl_cursos.clave)
                    OR tbl_cursos.tipo_curso != 'CURSO')
                    AND (EXISTS (SELECT 1 FROM pagos p WHERE p.arch_calificaciones IS NOT NULL AND p.id_contrato = contratos.id_contrato)
                    OR EXISTS (SELECT 1 FROM documentos_firmar df WHERE df.tipo_archivo = 'Reporte fotografico' AND df.status = 'VALIDADO' AND df.numero_o_clave = tbl_cursos.clave)
                    OR tbl_cursos.tipo_curso != 'CERTIFICACION')
                    THEN FALSE
                ELSE TRUE
            END
            ) AS resultado")
            ]);

        // Before the loop, get all needed especialidad_instructor records
        $instructorIds = $contratos_folios->pluck('id_instructor')->unique()->toArray();
        $especialidades = especialidad_instructor::whereIn('id_instructor', $instructorIds)->get();

        foreach($contratos_folios as $pointer => $ari)
        {
            $especialidad = $especialidades->where('id_instructor', $ari->id_instructor)
                ->filter(function($item) use ($ari) {
                    // Check if hvalidacion contains the needed memo_val
                    if (is_array($item->hvalidacion)) {
                        foreach ($item->hvalidacion as $me) {
                            if (isset($me['memo_val']) && $me['memo_val'] == $ari->instructor_mespecialidad) {
                                return true;
                            }
                        }
                    }
                    return false;
                })->first();

            if ($especialidad) {
                foreach ($especialidad->hvalidacion as $me) {
                    if (isset($me['memo_val']) && $me['memo_val'] == $ari->instructor_mespecialidad) {
                        $contratos_folios[$pointer]->arch_mespecialidad = $me['arch_val'];
                        break;
                    }
                }
            } else {
                $contratos_folios[$pointer]->arch_mespecialidad = $ari->archivo_alta;
            }

        }

        $calendario_entrega = Calendario_Entrega::whereDate('fecha_entrega', '>=', Carbon::now()->toDateString())
            ->whereJsonContains('tipo_entrega', 'DOCUMENTACION_PAGO')
            ->orderBy('fecha_entrega', 'asc')
            ->value('fecha_entrega');


        // Eager load roles and permissions for the current user
        $user = Auth::user();
        $user->load('roles.permissions');

        return view('layouts.pages.vstapago', compact(
            'contratos_folios','unidades','año_pointer','array_ejercicio','tipoPago','unidad','calendario_entrega'
        ));
    }

    public function crear_pago($id)
    {
        $data = contratos::SELECT('instructores.numero_control','instructores.id AS idins','tbl_cursos.nombre','tbl_cursos.curso','tbl_cursos.clave',
                                    'contratos.unidad_capacitacion','folios.id_folios','folios.importe_total','folios.iva','pagos.id AS id_pago', 'pagos.fecha')
                                    ->WHERE('contratos.id_contrato', '=', $id)
                                    ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                                    ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', 'folios.id_cursos')
                                    ->LEFTJOIN('instructores', 'instructores.id', 'tbl_cursos.id_instructor')
                                    ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                                    ->FIRST();

        $importe = round($data->importe_total-$data->iva, 2);
        return view('layouts.pages.frmpago', compact('data','importe'));
    }

    public function modificar_pago()
    {
        return view('layouts.pages.modpago');
    }

    public function verificar_pago($idfolios)
    {
        $contrato = contratos::WHERE('id_folios', '=', $idfolios)->FIRST();

        pago::where('id_contrato', '=', $contrato->id)
        ->update(['fecha_status' => carbon::now()]);

        $pago = pago::WHERE('id_contrato', '=', $contrato->id);

        $folio = folio::findOrfail($idfolios);
        $folio->status = 'Pago_Verificado';
        $folio->save();

        //Notificacion!!
        $letter = [
            'titulo' => 'Solicitud de Pago Validada',
            'cuerpo' => 'La solicitud de pago ' . $pago->no_memo . ' ha sido validada',
            'memo' => $pago->no_memo,
            'unidad' => Auth::user()->unidad,
            'url' => '/pago/verificar_pago/' . $idfolios,
        ];
        //$users = User::where('id', 1)->get();
        // dd($users);
        //event((new NotificationEvent($users, $letter)));

        return redirect()->route('pago-inicio');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $contrato = new contratos();

        $contratos = $contrato::SELECT('contratos.id_contrato', 'contratos.numero_contrato', 'contratos.cantidad_numero',
        'contratos.unidad_capacitacion', 'contratos.municipio', 'contratos.fecha_firma','contratos.arch_factura',
        'folios.status', 'folios.id_folios','tbl_cursos.id_instructor','instructores.id AS idins','instructores.archivo_bancario')
        ->WHERE('contratos.id_contrato', '=', $id)
        ->LEFTJOIN('folios','folios.id_folios', '=', 'contratos.id_folios')
        ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
        ->LEFTJOIN('instructores','instructores.id', '=', 'tbl_cursos.id_instructor')
        ->FIRST();

        $datapago = pago::WHERE('id_contrato', '=', $id)->FIRST();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $contratos->id_contrato)->FIRST();
        $director = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','id')->WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();

        return view('layouts.pages.vstvalidarpago', compact('contratos','director','datapago'));
    }

    public function guardar_pago(Request $request)
    {
        $doc = $request->file('arch_pago'); # obtenemos el archivo
        $urldoc = $this->pdf_upload($doc, $request->id_pago, $request->id_instructor, 'pago_autorizado'); # invocamos el método

        pago::where('id', '=', $request->id_pago)
        ->update(['no_pago' => $request->numero_pago,
                  'fecha' => $request->fecha_pago,
                  'descripcion' => $request->concepto,
                  'fecha_status' => carbon::now(),
                  'arch_pago' => $urldoc]);

        folio::WHERE('id_folios', '=', $request->id_folio)
        ->update(['status' => 'Finalizado']);

        return redirect()->route('pago-inicio');
    }

    public function rechazar_pago(Request $request)
    {
        folio::WHERE('id_folios', '=', $request->idfolios)
        ->update(['status' => 'Pago_Rechazado',
                  'fecha_rechazado' => carbon::now()]);

        $pago = pago::find($request->idPago);
        if($pago->fecha_rechazo == NULL)
        {
            $old = array(array('fecha' => carbon::now()->toDateString(), 'observacion' => $request->observaciones));
        }
        else
        {
            $new = array('fecha' => carbon::now()->toDateString(), 'observacion' => $request->observaciones);
            $old = $pago->fecha_rechazo;
            // dd($new);
            array_push($old, $new);
        }
        pago::where('id', '=', $request->idPago)
        ->update(['observacion' => $request->observaciones,
                  'fecha_rechazo' => $old,
                  'chk_rechazado' => TRUE]);

        //Notificacion!!
        $letter = [
            'titulo' => 'Solicitud de Pago Rechazada',
            'cuerpo' => 'La solicitud de pago ' . $pago->no_memo . ' ha sido rechazada',
            'memo' => $pago->no_memo,
            'unidad' => Auth::user()->unidad,
            'url' => '/pago/solicitud/modificar/' . $request->idfolios,
        ];
        //$users = User::where('id', 1)->get();
        // dd($users);
        //event((new NotificationEvent($users, $letter)));

        return redirect()->route('pago-inicio');
    }

    public function pago_validar($idfolio)
    {
        $folio = folio::findOrfail($idfolio);
        $folio->status = 'Pago_Verificado';
        $folio->save();

        $pago = DB::table('folios')->SELECT('pagos.id')->WHERE('folios.id_folios', '=', $idfolio)
                ->JOIN('contratos', 'contratos.id_folios', '=', 'folios.id_folios')
                ->JOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                ->FIRST();

        pago::where('id', '=', $pago->id)->update(['fecha_validado' => carbon::now()]);
        return redirect()->route('pago-inicio')->with('info', 'El pago ha sido verificado exitosamente.');
    }

    public function pagoRestart($id)
    {
        $affecttbl_inscripcion = DB::table("folios")->WHERE('id_folios', $id)->update(['status' => 'Pago_Rechazado']);

        return redirect()->route('pago-inicio')
                        ->with('success','Solicitud de Pago Reiniciado');
    }

    public function historial_validacion($id)
    {
        //
        $contrato = new contratos();

        $contratos = $contrato::SELECT('contratos.id_contrato', 'contratos.numero_contrato', 'contratos.cantidad_numero',
        'contratos.unidad_capacitacion', 'contratos.municipio', 'contratos.fecha_firma','contratos.arch_factura',
        'folios.status', 'folios.id_folios','tbl_cursos.id_instructor','instructores.id AS idins','instructores.archivo_bancario')
        ->WHERE('contratos.id_contrato', '=', $id)
        ->LEFTJOIN('folios','folios.id_folios', '=', 'contratos.id_folios')
        ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
        ->LEFTJOIN('instructores','instructores.id', '=', 'tbl_cursos.id_instructor')
        ->FIRST();

        $datapago = pago::WHERE('id_contrato', '=', $id)->FIRST();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $contratos->id_contrato)->FIRST();
        $director = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','id')->WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();

        return view('layouts.pages.vsthistorialvalidarpago', compact('contratos','director','datapago'));
    }

    public function documentospago_reporte()
    {
        $unidades = tbl_unidades::SELECT('ubicacion')->WHERE('id', '!=', '0')->ORDERBY('ubicacion','asc')
                                ->GROUPBY('ubicacion')
                                ->GET();

        return view('layouts.pages.vstareportedocumentospago', compact('unidades'));
    }

    public function upload_pago_autorizado(Request $request)
    {
        $idcontrato = DB::Table('contratos')->SELECT('id_contrato')->WHERE('id_folios', $request->idfolpa)->FIRST();
        $pago = pago::WHERE('id_contrato', $idcontrato->id_contrato)->FIRST();
        $doc = $request->file('doc_validado'); # obtenemos el archivo
        $urldoc = $this->pdf_upload($doc, $pago->id, 'pago_autorizado'); # invocamos el método
        $pago->arch_pago = $urldoc;
        $pago->save();

        return redirect()->route('pago-inicio');
    }

    public function tramitesrecepcionados_pdf(Request $request)
    {
        // dd($request);
        $data = contratos::SELECT('contratos.fecha_status', 'contratos.numero_contrato', 'contratos.fecha_firma',
            'contratos.chk_rechazado', 'contratos.fecha_rechazo', 'pagos.status_recepcion', 'pagos.recepcion','pagos.historial', 'tbl_cursos.clave',
		    'tbl_cursos.inicio', 'tbl_cursos.nombre','folios.status')
            ->JOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
            ->JOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
            ->JOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_cursos.unidad')
            ->LEFTJOIN('pagos','pagos.id_contrato','=', 'contratos.id_contrato')
            // ->WHERE('contratos.id_contrato', '=', '4228')
            ->WHERE('contratos.unidad_capacitacion', '=', $request->unidad)
            // ->WHERE('tbl_cursos.tipo_curso', '=', $request->tipo)
            // ->WHERE('tbl_cursos.tcapacitacion', '=', $request->modalidad)
            // ->WHERE('pagos.recepcion', '!=', NULL)
            ->WHERE('pagos.status_recepcion', '!=', 'Rechazado')
            // ->WHERE('pagos.status_recepcion', '!=', 'En Espera')
            ->WHEREBETWEEN('contratos.fecha_status', [$request->fecha1, $request->fecha2])
            // ->WHERE('pagos.historial','!=',null)
            ->ORDERBY('tbl_cursos.inicio', 'ASC')
            ->GET();
            // dd(json_decode($data[7]->historial));
        $head = ['FECHA','NUM.','CLAVE CURSO','ESTATUS'.'FECHA FIRMA DE CONTRATO','NOMBRE DEL INSTRUCTOR'];
        $title = "DOCUMENTOS RECEPCIONADOS";
        $name = $title."_".date('Ymd').".xlsx";
        $view = 'layouts.pages.reportes.excel_contratos_recepcionados';
        return Excel::download(new ExportExcel($data,$head, $title,$view), $name);
        // dd($data[1]->fecha_rechazo);
    }

    public function  reporte_validados_recepcionados(Request $request)
    {
        $mes1 = null; $mes2 = null; $mes3 = null; $mes4 = null; $mes5 = null; $mes6 = null; $mes7 = null; $mes8 = null;
        $mes9 = null; $mes10 = null; $mes11 = null; $mes12 = null;
        $i = $request->fini;
        $now = Carbon::now();
        $monthnow = $this->monthToString($now->month);
        $mi = Carbon::parse($now->year . '-' . $request->fini . '-01');
        $fi = Carbon::parse($now->year . '-' . $request->ffin . '-01');
        $dym = $fi->daysInMonth;
        $fin = $now->year . '-' . $request->ffin . '-' . $dym;
        $nombremesini = $this->monthToString($request->fini);
        $nombremesfin = $this->monthToString($request->ffin);


        //dd($request);
        do {
            if(substr($i, -2, 1) == '0')
            {
                $nomval = "mes" . substr($i, -1);
            }
            else
            {
                $nomval = "mes" . $i;
            }
            //dd($nomval);
            $inicial = Carbon::parse($now->year . '-' . $i . '-01');
            $dym = $inicial->daysInMonth;
            $inicial00 = $now->year . '-' . $i . '-01';
            //dd($inicial00);
            $final = Carbon::parse($now->year . '-' . $i . '-' . $dym . ' 23:59:59');
            //printf($inicial . ' - ' . $final . ' // ');
            $cab1 = "sivyc";
            $cab2 = "fisico";
            $cab3 = "PorEntregar";
            $query1 = "sum(case when b.status in ('Contratado','Verificando_Pago','Pago_Verificado','Finalizado') then 1 else 0 end) AS " . $cab1;
            $query2 = "sum(case when b.status in ('Pago_Verificado','Finalizado')  then 1 else 0 end) AS " . $cab2;
            $query3 = "sum(case when b.status in ('Contratado','Verificando_Pago')  then 1 else 0 end) AS " . $cab3;
            //dd($inicial);
            $$nomval = $data = db::table(DB::raw("(SELECT * from FOLIOS WHERE folios.created_at >=  '$inicial'  and folios.created_at <= '$final' ) AS B"))->select('tbl_unidades.ubicacion',
                DB::raw($query1),
                DB::raw($query2),
                DB::raw($query3),
                )
                //->WHERE('folios.created_at', '>=', $inicial)
                //->WHERE('folios.created_at', '<=', $final)
                //->WHERE('tbl_unidades.ubicacion', '=', 'TUXTLA')
                ->JOIN('tabla_supre', 'tabla_supre.id', '=', 'b.id_supre')
                ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tabla_supre.unidad_capacitacion')
                ->groupBy('tbl_unidades.ubicacion')
                ->orderBy('tbl_unidades.ubicacion')
                ->GET();

            $i++;
        } while ($i <= $request->ffin);
        $data = db::table(DB::raw("(SELECT * from FOLIOS WHERE folios.created_at >=  '$mi'  and folios.created_at <= '$fin' ) AS B"))->select('tbl_unidades.ubicacion',
            DB::raw("sum(case when b.status in ('Contratado','Verificando_Pago','Pago_Verificado','Finalizado')  then 1 else 0 end) AS Sivyc"),
            DB::raw("sum(case when b.status in ('Pago_Verificado','Finalizado')  then 1 else 0 end) AS Fisico"),
            DB::raw("sum(case when b.status in ('Contratado','Verificando_Pago')  then 1 else 0 end) AS PorEntregar"),
            DB::raw("sum(case when b.status in ('Finalizado')  then 1 else 0 end) AS Pagado"),
            DB::raw("sum(case when b.status in ('Pago_Verificado')  then 1 else 0 end) AS PorPagar"),
            DB::raw("sum(case when b.status in ('Contrato_Rechazado','Pago_Rechazado')  then 1 else 0 end) AS Observados")
            )
            //->WHERE('folios.created_at', '>=', $mi)
            //->WHERE('folios.created_at', '<=', $fin) a
            //->WHERE('tbl_unidades.ubicacion', '=', 'TUXTLA')
            ->JOIN('tabla_supre', 'tabla_supre.id', '=', 'b.id_supre')
            ->RIGHTJOIN('tbl_unidades', 'tbl_unidades.unidad', '=', 'tabla_supre.unidad_capacitacion')
            ->groupBy('tbl_unidades.ubicacion')
            ->orderBy('tbl_unidades.ubicacion')
            ->GET();
            //dd($monthnow);

            //return view('layouts.pdfpages.reportescontratosval', compact('mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','data','nombremesini','nombremesfin'));

            $pdf = PDF::loadView('layouts.pdfpages.reportescontratosval', compact('mes1','mes2','mes3','mes4','mes5','mes6','mes7','mes8','mes9','mes10','mes11','mes12','data','nombremesini','nombremesfin','now','monthnow'));
            $pdf->setPaper('legal', 'Landscape');
            return $pdf->stream('medium.pdf');
    }

    public function mostrar_pago($id)
    {
        $data = contratos::SELECT('instructores.numero_control','instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                                  'tbl_cursos.curso','tbl_cursos.clave','contratos.unidad_capacitacion','folios.id_folios','folios.importe_total','folios.iva',
                                  'pagos.id AS id_pago','pagos.no_memo','pagos.fecha','pagos.no_pago','pagos.descripcion','pagos.liquido')
                           ->WHERE('contratos.id_contrato', '=', $id)
                           ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', 'tbl_cursos.id_instructor')
                           ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                           ->FIRST();

        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;

        return view('layouts.pages.vstapagofinalizado', compact('data', 'nomins'));
        $pdf = PDF::loadView('layouts.pages.vstapagofinalizado', compact('data', 'nomins'));
        return $pdf->stream('medium.pdf');
    }

    public function agendar_entrega_pago(Request $request)
    {
        // dd($request);
        $variables = ['factura_pdf','factura_xml','contratof_pdf','solpa_pdf','asistencias_pdf','calificaciones_pdf',
                      'evidencia_fotografica_pdf'];
        $documentosCompletos = ['Solicitud Pago' => FALSE, 'valsupre' => FALSE, 'Contrato' => FALSE, 'Lista de asistencia' => FALSE,
            'Lista de calificaciones' => FALSE, 'Reporte fotografico' => FALSE];
        if(isset($request->id_contrato_agendac))
        {
            for($i=0;$i<=6;$i++)
            {
                $variables[$i] = $variables[$i].'c';
                $id_contrato = $request->id_contrato_agendac;
            }
        }
        else
        {
            $id_contrato = $request->id_contrato_agenda;
        }
        $curso  = DB::TABLE('contratos')->SELECT('tbl_cursos.id_instructor','tbl_cursos.tipo_curso')
            ->JOIN('folios','folios.id_folios','contratos.id_folios')
            ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->WHERE('contratos.id_contrato', $id_contrato)
            ->FIRST();

        $doc_factura_pdf = $request->file($variables[0]); # obtenemos el archivo
        $doc_factura_xml = $request->file($variables[1]); # obtenemos el archivo
        $doc_contrato = $request->file($variables[2]); # obtenemos el archivo
        $doc_solpa = $request->file($variables[3]); # obtenemos el archivo
        $doc_asistencias = $request->file($variables[4]); # obtenemos el archivo
        $doc_calificaciones = $request->file($variables[5]); # obtenemos el archivo
        $doc_evidencia_fotografica = $request->file($variables[6]);

        $contrato = contratos::find($id_contrato);
        if(isset($doc_factura_pdf))
        {
            $factura_pdf = $this->pdf_upload($doc_factura_pdf, $id_contrato, $curso->id_instructor, 'factura_pdf'); # invocamos el método
            $contrato->arch_factura = $factura_pdf;
        }
        if(isset($doc_factura_xml))
        {
            $factura_xml = $this->xml_upload($doc_factura_xml, $id_contrato, $curso->id_instructor, 'factura_xml'); # invocamos el métododd
            $contrato->arch_factura_xml = $factura_xml;
        }
        if(isset($doc_contrato))
        {
            $contrato_pdf = $this->pdf_upload($doc_contrato, $id_contrato, $curso->id_instructor, 'contrato'); # invocamos el método
            $contrato->arch_contrato = $contrato_pdf;
            $documentosCompletos['Contrato'] = TRUE;
        }
        if(isset($doc_solpa))
        {
            $solpa_pdf = $this->pdf_upload($doc_solpa, $id_contrato, $curso->id_instructor, 'solicitud_pago'); # invocamos el método
            $pago = pago::where('id_contrato', $id_contrato)
                ->update(['arch_solicitud_pago' => $solpa_pdf]);
            $documentosCompletos['Solicitud Pago'] = TRUE;
        }
        if(isset($doc_asistencias))
        {
            $asistencias_pdf = $this->pdf_upload($doc_asistencias, $id_contrato, $curso->id_instructor, 'lista_asistencia'); # invocamos el método
            $pago = pago::where('id_contrato', $id_contrato)
                ->update(['arch_asistencia' => $asistencias_pdf]);
            $documentosCompletos['Lista de asistencia'] = TRUE;
        }
        if(isset($doc_calificaciones))
        {
            $calificaciones_pdf = $this->pdf_upload($doc_calificaciones, $id_contrato, $curso->id_instructor, 'lista_calificaciones'); # invocamos el método
            $pago = pago::where('id_contrato', $id_contrato)
            ->update(['arch_calificaciones' => $calificaciones_pdf]);
            $documentosCompletos['Lista de calificaciones'] = TRUE;
        }
        if(isset($doc_evidencia_fotografica))
        {
            $evidencia_fotografica_pdf = $this->pdf_upload($doc_evidencia_fotografica, $id_contrato, $curso->id_instructor, 'evidencia_fotografica'); # invocamos el método
            $pago = pago::where('id_contrato', $id_contrato)
            ->update(['arch_evidencia' => $evidencia_fotografica_pdf]);
            $documentosCompletos['Reporte fotografico'] = TRUE;
        }

        if($request->tipo_envio == 'guardar_enviar' || $request->tipo_envioc == 'guardar_enviar')
        {
            // inicio de verificación de archivos completos 23092024
            // $documentosSellados = DB::Table('tbl_cursos')->Select('documentos_firmar.tipo_archivo')
            //     ->Join('documentos_firmar', 'documentos_firmar.numero_o_clave', 'tbl_cursos.clave')
            //     ->Join('contratos', 'contratos.id_curso', 'tbl_cursos.id')
            //     ->Where('contratos.id_contrato', $id_contrato)
            //     ->Where('documentos_firmar.status','VALIDADO')
            //     ->Get();
            // $documentosSubidos = DB::Table('contratos')->Select('arch_asistencia as Lista de asistencia','arch_calificaciones as Lista de calificaciones','arch_evidencia as Reporte fotografico','arch_solicitud_pago as Solicitud Pago','doc_validado as valsupre','arch_contrato as Contrato')
            // ->Join('pagos','pagos.id_contrato', 'contratos.id_contrato')
            // ->Join('folios','folios.id_cursos','contratos.id_curso')
            // ->Join('tabla_supre','tabla_supre.id','folios.id_supre')
            // ->Where('contratos.id_contrato', $id_contrato)
            // ->First();

            // foreach($documentosSellados as $moist)
            // {
            //     $documentosCompletos[$moist->tipo_archivo] = TRUE;
            //     if(!is_null($documentosSubidos->{$moist->tipo_archivo})) {
            //         $documentosCompletos[$moist->tipo_archivo] = TRUE;
            //     }
            // }
            // // fin de verificacion de archivos completos
            // if(in_array(false, $documentosCompletos)) { //aqui se define si esta completo o no
            //     $faltantes = null;
            //     foreach($documentosCompletos as $key => $result) {
            //         if(!$result) {
            //             $key = $key == 'valsupre' ? 'Validación de suficiencia presupuestal' : $key;
            //             $faltantes = is_null($faltantes) ? $key : $faltantes . ', ' . $key;
            //         }
            //     }
            //     $type = 'warning';
            //     $message = 'El envío a validación no ha sido posible debido a la falta de documentos pendientes por integrar: '. $faltantes;
            // } else {
                pago::where('id_contrato', $id_contrato)
                ->update(['status_recepcion' => 'En Espera',
                        'fecha_envio' => carbon::now()->format('d-m-Y')]);

                $type = 'success';
                $message = 'Entrega de Documentos Agendado Correctamente';
            // }
        } else {
            $idf = DB::Table('contratos')->Where('id_contrato',$id_contrato)->Value('id_folios');
            $update = folio::Find($idf)
                ->update(['edicion_pago' => FALSE,]);

            $type = 'success';
            $message = 'Documentos Guardados Correctamente';
        }

        $contrato->save();

        return redirect()->route('pago-inicio')
                ->with($type, $message);
    }

    public function confirmar_entrega_fisica(Request $request)
    {
        $fecha_actual = carbon::now();
        $folio = folio::find($request->id_folio_entrega)->update(['recepcion' => $fecha_actual->toDateString()]);
        return redirect()->route('pago-inicio')
                ->with('success', 'Entrega de Documentos Confirmada Correctamente');
    }

    public function validar_cita_fisica(Request $request)
    {
        // dd($request);
        $updarray = $arrhistorial = array();
        $update = pago::WHERE('id_contrato',$request->id_contrato_cita)->first();
        $archivos = DB::TABLE('contratos')
            ->SELECT('arch_factura','arch_factura_xml','arch_contrato','doc_validado','archivo_ine','archivo_bancario','pdf_curso',
            'instructor_mespecialidad','espe','archivo_alta','tbl_cursos.id_instructor')
            ->WHERE('contratos.id_contrato', $request->id_contrato_cita)
            ->JOIN('pagos','pagos.id_contrato','contratos.id_contrato')
            ->JOIN('folios','folios.id_folios','contratos.id_folios')
            ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->JOIN('tabla_supre','tabla_supre.id','folios.id_supre')
            ->JOIN('instructores','instructores.id','tbl_cursos.id_instructor')->FIRST();

        $especialidad_seleccionada = DB::Table('especialidad_instructores')
            ->SELECT('especialidad_instructores.id','especialidades.nombre')
            ->WHERE('especialidad_instructores.id_instructor',$archivos->id_instructor)
            ->WHERE('especialidades.nombre', '=', $archivos->espe)
            ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
            ->FIRST();

        $memoval = especialidad_instructor::WHERE('id',$especialidad_seleccionada->id)
            ->whereJsonContains('hvalidacion', [['memo_val' => $archivos->instructor_mespecialidad]])->value('hvalidacion');
        if(isset($memoval))
        {
            foreach($memoval as $me)
            {
                if(isset($me['memo_val']) && $me['memo_val'] == $archivos->instructor_mespecialidad)
                {
                    $archivos->instructor_mespecialidad = $me['arch_val'];
                    break;
                }
            }
        }
        else
        {
            $archivos->instructor_mespecialidad = $archivos->archivo_alta;
        }

        // $update->fecha_agenda = $request->fecha_confirmada;
        $update->recepcion = carbon::now()->format('d-m-Y');
        $update->status_recepcion = 'VALIDADO';
        $updarray = ['status' => 'VALIDADO',
                    //  'fecha_agenda' => $request->fecha_confirmada,
                     'fecha_validacion' => carbon::now()->format('d-m-Y'),
                     'solicitud_pago' => $update->arch_solicitud_pago,
                     'cuenta_bancaria' => $archivos->archivo_bancario,
                     'validacion_instructor' => $archivos->instructor_mespecialidad,
                     'arc' => $archivos->pdf_curso,
                     'valsupre' => $archivos->doc_validado,
                     'factura_pdf' => $archivos->arch_factura,
                     'factura_xml' => $archivos->arch_factura_xml,
                     'contrato' => $archivos->arch_contrato,
                     'identificacion' => $archivos->archivo_ine,
                     'asistencia' => $update->arch_asistencia,
                     'calificacion' => $update->arch_calificaciones,
                     'evidencia' => $update->arch_evidencia];


        if(!isset($update->historial))
        {
            array_push($arrhistorial,$updarray);
        }
        else
        {
            $arrhistorial = $update->historial;
            array_push($arrhistorial,$updarray);
        }
        $update->historial = $arrhistorial;
        $update->save();
        return redirect()->route('pago-inicio')
                ->with('success', 'Documentación Digital Confirmada Correctamente');
    }

    public function rechazar_entrega_fisica(Request $request)
    {
        // dd($request);
        $updarray = $arrhistorial = array();
        $update = pago::WHERE('id_contrato',$request->id_contrato_entrega_rechazo)->first();
        $archivos = DB::TABLE('contratos')
            ->SELECT('arch_factura','arch_factura_xml','arch_contrato','doc_validado','archivo_ine','archivo_bancario','pdf_curso',
            'instructor_mespecialidad','espe','archivo_alta','tbl_cursos.id_instructor')
            ->WHERE('contratos.id_contrato', $request->id_contrato_entrega_rechazo)
            ->JOIN('pagos','pagos.id_contrato','contratos.id_contrato')
            ->JOIN('folios','folios.id_folios','contratos.id_folios')
            ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->JOIN('tabla_supre','tabla_supre.id','folios.id_supre')
            ->JOIN('instructores','instructores.id','tbl_cursos.id_instructor')->FIRST();

        $especialidad_seleccionada = DB::Table('especialidad_instructores')
            ->SELECT('especialidad_instructores.id','especialidades.nombre')
            ->WHERE('especialidad_instructores.id_instructor',$archivos->id_instructor)
            ->WHERE('especialidades.nombre', '=', $archivos->espe)
            ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
            ->FIRST();
            // dd($especialidad_seleccionada);

        $memoval = especialidad_instructor::WHERE('id',$especialidad_seleccionada->id)
            ->whereJsonContains('hvalidacion', [['memo_val' => $archivos->instructor_mespecialidad]])->value('hvalidacion');
        if(isset($memoval))
        {
            foreach($memoval as $me)
            {
                if(isset($me['memo_val']) && $me['memo_val'] == $archivos->instructor_mespecialidad)
                {
                    $archivos->instructor_mespecialidad = $me['arch_val'];
                    break;
                }
            }
        }
        else
        {
            $archivos->instructor_mespecialidad = $archivos->archivo_alta;
        }

        $update->observacion_rechazo_recepcion = $request->observacion_rechazo;
        $update->status_recepcion = 'Rechazado';
        $updarray = ['status' => 'Rechazado',
                     'observacion' => $update->observacion_rechazo_recepcion,
                     'fecha_rechazo' => carbon::now()->format('d-m-Y'),
                     'solicitud_pago' => $update->arch_solicitud_pago,
                     'cuenta_bancaria' => $archivos->archivo_bancario,
                     'validacion_instructor' => $archivos->instructor_mespecialidad,
                     'arc' => $archivos->pdf_curso,
                     'valsupre' => $archivos->doc_validado,
                     'factura_pdf' => $archivos->arch_factura,
                     'factura_xml' => $archivos->arch_factura_xml,
                     'contrato' => $archivos->arch_contrato,
                     'identificacion' => $archivos->archivo_ine,
                     'asistencia' => $update->arch_asistencia,
                     'calificacion' => $update->arch_calificaciones,
                     'evidencia' => $update->arch_evidencia];


        if(!isset($update->historial))
        {
            array_push($arrhistorial,$updarray);
        }
        else
        {
            $arrhistorial = $update->historial;
            array_push($arrhistorial,$updarray);
        }
        $update->historial = $arrhistorial;
        $update->save();
        return redirect()->route('pago-inicio')
                ->with('success', 'Rechazo de entrega de Documentos Correctamente');
    }

    public function recibido_entrega_fisica(Request $request)
    {
        // dd($request);
        $updarray = $arrhistorial = array();
        $update = pago::WHERE('id_contrato',$request->id_contrato_entrega)->first();
        $archivos = DB::TABLE('contratos')
            ->SELECT('arch_factura','arch_factura_xml','arch_contrato','doc_validado','archivo_ine','archivo_bancario','pdf_curso',
            'instructor_mespecialidad','espe','archivo_alta','tbl_cursos.id_instructor')
            ->WHERE('contratos.id_contrato', $request->id_contrato_entrega)
            ->JOIN('pagos','pagos.id_contrato','contratos.id_contrato')
            ->JOIN('folios','folios.id_folios','contratos.id_folios')
            ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->JOIN('tabla_supre','tabla_supre.id','folios.id_supre')
            ->JOIN('instructores','instructores.id','tbl_cursos.id_instructor')->FIRST();

        $especialidad_seleccionada = DB::Table('especialidad_instructores')
            ->SELECT('especialidad_instructores.id','especialidades.nombre')
            ->WHERE('especialidad_instructores.id_instructor',$archivos->id_instructor)
            ->WHERE('especialidades.nombre', '=', $archivos->espe)
            ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
            ->FIRST();

        $memoval = especialidad_instructor::WHERE('id',$especialidad_seleccionada->id)
            ->whereJsonContains('hvalidacion', [['memo_val' => $archivos->instructor_mespecialidad]])->value('hvalidacion');
        if(isset($memoval))
        {
            foreach($memoval as $me)
            {
                if(isset($me['memo_val']) &&$me['memo_val'] == $archivos->instructor_mespecialidad)
                {
                    $archivos->instructor_mespecialidad = $me['arch_val'];
                    break;
                }
            }
        }
        else
        {
            $archivos->instructor_mespecialidad = $archivos->archivo_alta;
        }

        $update->status_recepcion = 'VALIDADO';
        $update->recepcion = carbon::now()->format('d-m-Y');
        $updarray = ['status' => 'VALIDADO Y RECIBIDO',
                     'fecha_recibido' => carbon::now()->format('d-m-Y'),
                     'solicitud_pago' => $update->arch_solicitud_pago,
                     'cuenta_bancaria' => $archivos->archivo_bancario,
                     'validacion_instructor' => $archivos->instructor_mespecialidad,
                     'arc' => $archivos->pdf_curso,
                     'valsupre' => $archivos->doc_validado,
                     'factura_pdf' => $archivos->arch_factura,
                     'factura_xml' => $archivos->arch_factura_xml,
                     'contrato' => $archivos->arch_contrato,
                     'identificacion' => $archivos->archivo_ine,
                     'asistencia' => $update->arch_asistencia,
                     'calificacion' => $update->arch_calificaciones,
                     'evidencia' => $update->arch_evidencia];


        if(!isset($update->historial))
        {
            array_push($arrhistorial,$updarray);
        }
        else
        {
            $arrhistorial = $update->historial;
            array_push($arrhistorial,$updarray);
        }
        $update->historial = $arrhistorial;
        $update->save();
        return redirect()->route('pago-inicio')
                ->with('success', 'Recepción de Documentos Guardado Correctamente');
    }

    public function norecibido_entrega_fisica(Request $request)
    {
        // dd($request);
        $updarray = $arrhistorial = array();
        $update = pago::WHERE('id_contrato',$request->id_contrato_noentrega)->first();
        $archivos = DB::TABLE('contratos')
            ->SELECT('arch_factura','arch_factura_xml','arch_contrato','doc_validado','archivo_ine','archivo_bancario','pdf_curso',
            'instructor_mespecialidad','espe','archivo_alta','tbl_cursos.id_instructor')
            ->WHERE('contratos.id_contrato', $request->id_contrato_noentrega)
            ->JOIN('pagos','pagos.id_contrato','contratos.id_contrato')
            ->JOIN('folios','folios.id_folios','contratos.id_folios')
            ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
            ->JOIN('tabla_supre','tabla_supre.id','folios.id_supre')
            ->JOIN('instructores','instructores.id','tbl_cursos.id_instructor')->FIRST();

        $especialidad_seleccionada = DB::Table('especialidad_instructores')
            ->SELECT('especialidad_instructores.id','especialidades.nombre')
            ->WHERE('especialidad_instructores.id_instructor',$archivos->id_instructor)
            ->WHERE('especialidades.nombre', '=', $archivos->espe)
            ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
            ->FIRST();

        $memoval = especialidad_instructor::WHERE('id',$especialidad_seleccionada->id)
            ->whereJsonContains('hvalidacion', [['memo_val' => $archivos->instructor_mespecialidad]])->value('hvalidacion');
        if(isset($memoval))
        {
            foreach($memoval as $me)
            {
                if($me['memo_val'] == $archivos->instructor_mespecialidad)
                {
                    $archivos->instructor_mespecialidad = $me['arch_val'];
                    break;
                }
            }
        }
        else
        {
            $archivos->instructor_mespecialidad = $archivos->archivo_alta;
        }

        $update->status_recepcion = 'No Recibido';
        $updarray = ['status' => 'No Recibido',
                     'fecha_no_recibido' => carbon::now()->format('d-m-Y'),
                     'solicitud_pago' => $update->arch_solicitud_pago,
                     'cuenta_bancaria' => $archivos->archivo_bancario,
                     'validacion_instructor' => $archivos->instructor_mespecialidad,
                     'arc' => $archivos->pdf_curso,
                     'valsupre' => $archivos->doc_validado,
                     'factura_pdf' => $archivos->arch_factura,
                     'factura_xml' => $archivos->arch_factura_xml,
                     'contrato' => $archivos->arch_contrato,
                     'identificacion' => $archivos->archivo_ine,
                     'asistencia' => $update->arch_asistencia,
                     'calificacion' => $update->arch_calificaciones,
                     'evidencia' => $update->arch_evidencia];


        if(!isset($update->historial))
        {
            array_push($arrhistorial,$updarray);
        }
        else
        {
            $arrhistorial = $update->historial;
            array_push($arrhistorial,$updarray);
        }
        $update->historial = $arrhistorial;
        $update->save();
        return redirect()->route('pago-inicio')
                ->with('success', 'No Recepción de Documentos Guardado Correctamente');
    }

    public function edicion_validacion_entrega_fisica(Request $request) {
        // dd($request);
        $arrhistorial = array();
        $id_folios = DB::Table('contratos')->Where('id_contrato',$request->id_retorno_recepcion)->Value('id_folios');
        $upd = folio::Find($id_folios);
        $upd->edicion_pago = True;
        $upd->save();

        $update = pago::WHERE('id_contrato',$request->id_retorno_recepcion)->first();
        $updarray = ['status' => 'Permiso de Edicion',
                        'observacion' => $request->observacion_retorno,
                        'fecha_retorno' => carbon::now()->format('d-m-Y'),
                        'usuario_retorno' => Auth::user()->name,];

        if(!isset($update->historial))
        {
            array_push($arrhistorial,$updarray);
        }
        else
        {
            $arrhistorial = $update->historial;
            array_push($arrhistorial,$updarray);
        }
        $update->historial = $arrhistorial;
        $update->save();



        return redirect()->route('pago-inicio')
                ->with('success', 'Permiso de Edición Otorgada Correctamente');
    }

    public function retorno_validacion_entrega_fisica(Request $request)
    {
        $updarray = $arrhistorial = array();
        $update = pago::WHERE('id_contrato',$request->id_retorno_recepcion)->first();
        if($update->status_recepcion == 'recepcion tradicional') {
            $contrato_id = DB::TABLE('contratos')->WHERE('contratos.id_contrato', $update->id_contrato)->VALUE('id_folios');
            $folio_rt = folio::FIND($contrato_id);
            $folio_rt->status = 'Capturando';
            $folio_rt->save();

        } else {
            $archivos = DB::TABLE('contratos')
                ->SELECT('arch_factura','arch_factura_xml','arch_contrato','doc_validado','archivo_ine','archivo_bancario','pdf_curso',
                'instructor_mespecialidad','espe','archivo_alta','tbl_cursos.id_instructor')
                ->WHERE('contratos.id_contrato', $request->id_retorno_recepcion)
                ->JOIN('pagos','pagos.id_contrato','contratos.id_contrato')
                ->JOIN('folios','folios.id_folios','contratos.id_folios')
                ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
                ->JOIN('tabla_supre','tabla_supre.id','folios.id_supre')
                ->JOIN('instructores','instructores.id','tbl_cursos.id_instructor')->FIRST();

            $especialidad_seleccionada = DB::Table('especialidad_instructores')
                ->SELECT('especialidad_instructores.id','especialidades.nombre')
                ->WHERE('especialidad_instructores.id_instructor',$archivos->id_instructor)
                ->WHERE('especialidades.nombre', '=', $archivos->espe)
                ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
                ->FIRST();

            $memoval = especialidad_instructor::WHERE('id',$especialidad_seleccionada->id)
                ->whereJsonContains('hvalidacion', [['memo_val' => $archivos->instructor_mespecialidad]])->value('hvalidacion');
            if(isset($memoval))
            {
                foreach($memoval as $me)
                {
                    if($me['memo_val'] == $archivos->instructor_mespecialidad)
                    {
                        $archivos->instructor_mespecialidad = $me['arch_val'];
                        break;
                    }
                }
            }
            else
            {
                $archivos->instructor_mespecialidad = $archivos->archivo_alta;
            }

            $update->status_recepcion = 'Rechazado';
            $update->recepcion = null;
            $updarray = ['status' => 'Retorno de Validacion',
                        'observacion' => $update->observacion_rechazo_recepcion,
                        'fecha_retorno' => carbon::now()->format('d-m-Y'),
                        'solicitud_pago' => $update->arch_solicitud_pago,
                        'cuenta_bancaria' => $archivos->archivo_bancario,
                        'validacion_instructor' => $archivos->instructor_mespecialidad,
                        'arc' => $archivos->pdf_curso,
                        'valsupre' => $archivos->doc_validado,
                        'factura_pdf' => $archivos->arch_factura,
                        'factura_xml' => $archivos->arch_factura_xml,
                        'contrato' => $archivos->arch_contrato,
                        'identificacion' => $archivos->archivo_ine,
                        'asistencia' => $archivos->arch_asistencia,
                        'calificacion' => $archivos->arch_calificaciones,
                        'evidencia' => $archivos->arch_evidencia,
                        'usuario_retorno' => Auth::user()->name,];



            if(!isset($update->historial))
            {
                array_push($arrhistorial,$updarray);
            }
            else
            {
                $arrhistorial = $update->historial;
                array_push($arrhistorial,$updarray);
            }
            $update->historial = $arrhistorial;
            $update->save();
        }
        return redirect()->route('pago-inicio')
                ->with('success', 'Valdiación de Documentos Digitales Retornado Correctamente');
    }


    public function financieros_reporte()
    {
        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();

        return view('layouts.pages.vstareportefinancieros', compact('unidades'));
    }

    public function financieros_reportepdf(Request $request)
    {
        $i = 0;
        set_time_limit(0);
        $count = 0;

        $data = folio::SELECT('folios.folio_validacion as suf','folios.status','tabla_supre.fecha','tabla_supre.no_memo',
                                  'tabla_supre.unidad_capacitacion','tbl_cursos.curso','tbl_cursos.clave',
                                  'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                                  'instructores.numero_control')
                                  ->WHERE('folios.status', '!=', 'En_Proceso')
                                  ->WHERE('folios.status', '!=', 'Rechazado')
                                  ->WHERE('folios.status', '!=', 'Validado')
                                  ->whereDate('tabla_supre.fecha', '>=', $request->fecha1)
                                  ->whereDate('tabla_supre.fecha', '<=', $request->fecha2)
                                  ->LEFTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                                  ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                                  ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                                  //->OrderByRaw('FIELD(folios.status, ' . implode(', ', $x) . ') ASC')
                                  ->GET();

        if ($request->filtro == 'curso')
        {
            $data = $data->WHERE('tbl_cursos.id', '=', $request->id_curso);
        }
        else if ($request->filtro == 'unidad')
        {
            $data = $data->WHERE('tabla_supre.unidad_capacitacion', '=', $request->unidad);
        }

        $data = $data->sortBy(function($item){
            return array_search($item->status, ['Validando_Contrato', 'Contrato_Rechazado', 'Contratado', 'Verificando_Pago', 'Pago_Rechazado', 'Pago_Verificado', 'Finalizado']);
        });

        $pdf = PDF::loadView('layouts.pdfpages.reportefinancieros', compact('data','count'));
        $pdf->setPaper('legal', 'Landscape');
        return $pdf->Download('formato de control '. $request->fecha1 . ' - '. $request->fecha2 .'.pdf');

    }

    public function verificar_documentacion($idContrato) {
    // Inicializar un array para los documentos faltantes
    $missingDocuments = [];

    // Consultar los documentos y verificar su existencia
    // Realiza una sola consulta para verificar todos los documentos
    $documents = DB::table('contratos')
        ->join('tbl_cursos', 'tbl_cursos.id', '=', 'contratos.id_curso')
        ->leftJoin('documentos_firmar as df1', function ($join) {
            $join->on('df1.numero_o_clave', '=', 'tbl_cursos.clave')
                 ->where('df1.tipo_archivo', '=', 'Solicitud Pago')
                 ->where('df1.status', '=', 'VALIDADO');
        })
        ->leftJoin('documentos_firmar as df2', function ($join) {
            $join->on('df2.numero_o_clave', '=', 'tbl_cursos.clave')
                 ->where('df2.tipo_archivo', '=', 'valsupre')
                 ->where('df2.status', '=', 'VALIDADO');
        })
        ->leftJoin('folios', 'folios.id_cursos', '=', 'tbl_cursos.id')
        ->leftJoin('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
        ->leftJoin('documentos_firmar as df3', function ($join) {
            $join->on('df3.numero_o_clave', '=', 'tbl_cursos.clave')
                 ->where('df3.tipo_archivo', '=', 'Contrato');
        })
        ->leftJoin('documentos_firmar as df4', function ($join) {
            $join->on('df4.numero_o_clave', '=', 'tbl_cursos.clave')
                 ->where('df4.tipo_archivo', '=', 'Lista de asistencia');
        })
        ->leftJoin('documentos_firmar as df5', function ($join) {
            $join->on('df5.numero_o_clave', '=', 'tbl_cursos.clave')
                 ->where('df5.tipo_archivo', '=', 'Lista de calificaciones');
        })
        ->leftJoin('documentos_firmar as df6', function ($join) {
            $join->on('df6.numero_o_clave', '=', 'tbl_cursos.clave')
                 ->where('df6.tipo_archivo', '=', 'Reporte fotografico');
        })
        ->leftJoin('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
        ->select(
            'df1.numero_o_clave as solicitud_pago',
            'df2.numero_o_clave as validacion_suficiencia',
            'tabla_supre.doc_validado as doc_validado_suficiencia',
            'df3.numero_o_clave as contrato',
            'df4.numero_o_clave as lista_asistencia',
            'df5.numero_o_clave as lista_calificaciones',
            'df6.numero_o_clave as reporte_fotografico',
            'pagos.arch_solicitud_pago',
            'contratos.arch_contrato',
            'pagos.arch_asistencia',
            'pagos.arch_calificaciones',
            'pagos.arch_evidencia'
        )
        ->where('contratos.id_contrato', $idContrato)
        ->first();

        // Verifica los documentos y agrega los que faltan a la lista
        if (empty($documents->solicitud_pago) && empty($documents->arch_solicitud_pago)) {
            $missingDocuments[] = 'Solicitud de Pago';
        }

        if (empty($documents->validacion_suficiencia) && empty($documents->doc_validado_suficiencia)) {
            $missingDocuments[] = 'Validación de Suficiencia Presupuestal';
        }

        if (empty($documents->contrato) && empty($documents->arch_contrato)) {
            $missingDocuments[] = 'Contrato';
        }

        if (empty($documents->lista_asistencia) && empty($documents->arch_asistencia)) {
            $missingDocuments[] = 'Lista de Asistencia';
        }

        if (empty($documents->lista_calificaciones) && empty($documents->arch_calificaciones)) {
            $missingDocuments[] = 'Lista de Calificaciones';
        }

        if (empty($documents->reporte_fotografico) && empty($documents->arch_evidencia)) {
            $missingDocuments[] = 'Reporte Fotográfico';
        }

        if(is_null($missingDocuments)) { $missingDocuments[] = 'completo';}
        return response()->json(['missing_documents' => $missingDocuments]);

    }

    public function concentrado_ingresos()
    {
        $roluser = DB::TABLE('roles')->SELECT('name')->JOIN('role_user AS ru','ru.role_id','roles.id')
            ->WHERE('user_id',Auth::user()->id)
            ->FIRST();
        $unidaduser = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->WHERE('id',Auth::user()->unidad)->FIRST();
        $unidades = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->GROUPBY('ubicacion')->ORDERBY('ubicacion','ASC')->GET();
        // dd($roluser);
        return view('layouts.pages.vstarf-001', compact('unidades','unidaduser','roluser'));
    }

    public function concentrado_ingresos_pdf(Request $request)
    {
        // dd($request);
        set_time_limit(0);
        $distintivo = DB::table('tbl_instituto')->pluck('distintivo')->first();
        $realiza = DB::TABLE('users')->SELECT('name','puesto')->WHERE('id',Auth::user()->id)->FIRST();
        $fecha['inicio'] = $request->fecha_inicio;
        $fecha['termino'] = $request->fecha_termino;
        $fecha['hoy'] = carbon::now()->format('d-m-Y');
        $data = DB::TABLE('folios AS f')
            ->SELECT('curso','tc.folio_pago','tc.movimiento_bancario','costo','u.cuenta','u.delegado_administrativo',
                    'u.pdelegado_administrativo','u.dunidad','u.pdunidad', 'u.ubicacion')
            ->RIGHTJOIN('tbl_cursos AS tc', 'tc.id', 'f.id_cursos')
            ->JOIN('tbl_unidades AS u', 'u.cct', 'tc.cct')
            ->WHEREBETWEEN('tc.fecha_movimiento_bancario',[$fecha['inicio'],$fecha['termino']])
            ->WHERE('tc.costo','!=','0.00')
            ->WHERE('tc.movimiento_bancario','!=',NULL)
            ->WHERE('ubicacion', $request->unidades)
            ->ORDERBY('fecha_apertura', 'ASC')
            ->GET();
        // dd($data);

        $pdf = PDF::loadView('layouts.pdfpages.concentradodeingresos', compact('distintivo','fecha','data','realiza'));
        $pdf->setPaper('Letter','portrait');
        return $pdf->stream('RF-001.pdf');
    }

    public function subir_contrato_rezagado(Request $request)
    {
        $contrato = contratos::Find($request->idcontrato_rezagado);
        $id_instructor = DB::Table('tbl_cursos')->WHERE('id', $contrato->id_curso)->Value('id_instructor');

        $doc_contrato = $request->file('contrato_rezagado_doc'); # obtenemos el archivo
        $contrato_pdf = $this->pdf_upload($doc_contrato, $contrato->id_contrato, $id_instructor, 'contrato'); # invocamos el método
        $contrato->arch_contrato = $contrato_pdf;
        $contrato->save();

        return redirect()->route('pago-inicio')
                ->with('success', 'Contrato Firmado Cargado Correctamente');
    }

    protected function pdf_upload($pdf, $id, $idins, $nom)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $pdf->storeAs('/uploadFiles/instructor/'.$idins.'/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadFiles/instructor/'.$idins."/".$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }

    protected function xml_upload($xml, $id, $idins, $nom)
    {
        # nuevo nombre del archivo
        $xmlFile = trim($nom."_".date('YmdHis')."_".$id.".xml");
        $xml->storeAs('/uploadFiles/instructor/'.$idins.'/'.$id, $xmlFile); // guardamos el archivo en la carpeta storage
        $xmlUrl = Storage::url('/uploadFiles/instructor/'.$idins."/".$id."/".$xmlFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $xmlUrl;
    }

    public function downloadRar($id_contrato)
    {
        $archivos = DB::TABLE('pagos')->SELECT('pagos.arch_solicitud_pago','pagos.arch_asistencia','pagos.arch_evidencia','pagos.arch_calificaciones',
        'instructores.archivo_bancario','tbl_cursos.instructor_mespecialidad','tbl_cursos.pdf_curso','tbl_cursos.espe','tabla_supre.doc_validado',
        'contratos.arch_factura','contratos.arch_factura_xml','contratos.arch_contrato','contratos.numero_contrato','instructores.archivo_ine')
        ->JOIN('contratos','contratos.id_contrato','pagos.id_contrato')
        ->JOIN('folios','folios.id_folios','contratos.id_folios')
        ->JOIN('tabla_supre','tabla_supre.id','folios.id_supre')
        ->JOIN('tbl_cursos','tbl_cursos.id','folios.id_cursos')
        ->JOIN('instructores','instructores.id','tbl_cursos.id_instructor')
        ->WHERE('pagos.id_contrato',$id_contrato)
        ->FIRST();

        $especialidad_seleccionada = DB::Table('especialidad_instructores')
            ->SELECT('especialidad_instructores.id','especialidades.nombre')
            ->WHERE('especialidad_instructores.memorandum_validacion',$archivos->instructor_mespecialidad)
            ->WHERE('especialidades.nombre', '=', $archivos->espe)
            ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
            ->FIRST();

        $memoval = especialidad_instructor::WHERE('id',$especialidad_seleccionada->id)
            ->whereJsonContains('hvalidacion', [['memo_val' => $archivos->instructor_mespecialidad]])->value('hvalidacion');
        if(isset($memoval))
        {
            foreach($memoval as $me)
            {
                if($me['memo_val'] == $archivos->instructor_mespecialidad)
                {
                    $archivos->instructor_mespecialidad = $me['arch_val'];
                    break;
                }
            }
        }
        else
        {
            $archivos->instructor_mespecialidad = $archivos->archivo_alta;
        }

        // Nombre del archivo ZIP
        $zipFileName = 'archivos.zip';

        // Directorio temporal para almacenar los archivos antes de comprimirlos
        $tempDir = storage_path('temp_zip');

        // Asegúrate de que el directorio temporal exista, si no, créalo
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir);
        }

        // Agrega archivos al directorio temporal (puedes personalizar esto según tus necesidades)
        $filesToAdd = [
            $archivos->arch_solicitud_pago,
            $archivos->archivo_bancario,
            $archivos->instructor_mespecialidad,
            $archivos->pdf_curso,
            $archivos->doc_validado,
            $archivos->arch_factura,
            $archivos->arch_factura_xml,
            $archivos->arch_contrato,
            $archivos->archivo_ine,
            $archivos->arch_evidencia,
            // Agrega más archivos según sea necesario
        ];

        foreach ($filesToAdd as $file) {
            if(!is_null($file)) {
                $filename = pathinfo($file, PATHINFO_BASENAME);
                copy($file, $tempDir . '/' . $filename);
            }
        }
        // Crea el archivo ZIP
        $zip = new ZipArchive;
        if ($zip->open(storage_path($zipFileName), ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $files = File::allFiles($tempDir);

            foreach ($files as $file) {
                $zip->addFile($file->getRealPath(), $file->getRelativePathname());
            }

            $zip->close();

            // Elimina el directorio temporal
            File::deleteDirectory($tempDir);

            // Descarga el archivo ZIP
            return response()->download(storage_path($zipFileName))->deleteFileAfterSend(true);
        } else {
            return 'No se pudo crear el archivo ZIP';
        }
    }

    public function download_pdf_masivo($id) {
        try {
            $data = $this->docValidadoPagoRepository->generarPdfMasivo($id);
            $pdf = new FPDIWithRotation();
             // Configuración de la marca de agua
             $marcaDeAguaTexto = "SIVyC";    // Texto de la marca de agua
             $marcaDeAguaColor = [200, 200, 200]; // Color gris claro para emular transparencia
             $marcaDeAguaAngulo = 45;            // Ángulo de la marca de agua
             $marcaDeAguaTamaño = 250;            // Tamaño de la fuente
              // Dimensiones de la página en milímetros (A4: 210 x 297)
            $pageWidth = 210;
            $pageHeight = 297;

            foreach ($data as $key) {
                if(!method_exists($key, 'status')){
                    $response = Http::get($key);

                    if ($response->ok()) {
                        $pdfContent = $response->body();
                    } else {
                        return response()->json(['error' => "No se pudo cargar el archivo desde la URL: " . $key], 404);
                    }
                }
                else {
                    $pdfContent = $key->content();
                    // dd($key->content(), $pdfContent);
                }


                // Cargar el contenido PDF en FPDI usando StreamReader
                $totalPaginas = $pdf->setSourceFile(StreamReader::createByString($pdfContent));

                // Importar cada página del PDF
                for ($i = 1; $i <= $totalPaginas; $i++) {
                    $paginaId = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($paginaId);
                    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                    $pdf->AddPage($orientation, [$size['width'], $size['height']]); // Ajusta la posición y tamaño si es necesario
                    $pdf->useTemplate($paginaId);

                    // Configurar la fuente y color para la marca de agua
                    // $pdf->SetFont('Arial', 'B', $marcaDeAguaTamaño);
                    // $pdf->SetTextColor($marcaDeAguaColor[0], $marcaDeAguaColor[1], $marcaDeAguaColor[2], 3);

                    // Calcular la posición central para el texto de la marca de agua
                    // $xPos = $pageWidth / 2;
                    // $yPos = $pageHeight - 30;

                    // Aplicar rotación y posicionar el texto de la marca de agua en el centro
                    // $pdf->SetAlpha(0.3);
                    // $pdf->Text(230, $yPos, $marcaDeAguaTexto); // Ajusta la posición si es necesario

                }
            }

             // Salida del PDF combinado
             return response()->make($pdf->Output('S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="documento_concentrado.pdf"',
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Ocurrió un error al generar el documento masivo: '.$th->getMessage());
        }
    }


    protected function monthToString($month)
    {
        switch ($month)
        {
            case 1:
                return 'ENERO';
            break;

            case 2:
                return 'FEBRERO';
            break;

            case 3:
                return 'MARZO';
            break;

            case 4:
                return 'ABRIL';
            break;

            case 5:
                return 'MAYO';
            break;

            case 6:
                return 'JUNIO';
            break;

            case 7:
                return 'JULIO';
            break;

            case 8:
                return 'AGOSTO';
            break;

            case 9:
                return 'SEPTIEMBRE';
            break;

            case 10:
                return 'OCTUBRE';
            break;

            case 11:
                return 'NOVIEMBRE';
            break;

            case 12:
                return 'DICIEMBRE';
            break;
        }
    }
}
