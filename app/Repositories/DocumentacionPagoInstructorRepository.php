<?php
namespace App\Repositories;

use App\Http\Controllers\webController\ContratoController;
use App\Http\Controllers\webController\supreController;
use App\Interfaces\DocumentacionPagoInstructorInterface;
use App\Models\especialidad_instructor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ReportService;
use App\Utilities\MyUtility;
use Illuminate\Http\Request;
use App\Models\Unidad;
use App\Models\User;
use Carbon\Carbon;

class DocumentacionPagoInstructorRepository implements DocumentacionPagoInstructorInterface
{
    public function generarPdfMasivo($id)
    {
        $asistencia_pdf = $reporte_pdf = NULL;
        $archivos = DB::Table('tbl_cursos')->Select('tbl_cursos.id','tbl_cursos.clave','id_instructor','instructor_mespecialidad',
            'contratos.id_contrato','arch_solicitud_pago', DB::raw("soportes_instructor->>'archivo_bancario' as archivo_bancario"),
            'tbl_cursos.pdf_curso','tabla_supre.doc_validado','pagos.arch_asistencia','pagos.arch_evidencia','contratos.arch_contrato',
            'pagos.arch_pago', 'folios.id_supre','folios.id_folios',DB::raw("soportes_instructor->>'archivo_ine' as archivo_ine"))
            ->Join('pagos','pagos.id_curso','tbl_cursos.id')
            ->Join('folios','folios.id_cursos','tbl_cursos.id')
            ->Join('tabla_supre','tabla_supre.id','folios.id_supre')
            ->Join('contratos','contratos.id_contrato','pagos.id_contrato')
            ->Where('contratos.id_contrato', $id)
            // ->Where('status_transferencia','PAGADO')
            // ->whereDate('tbl_cursos.inicio', '>=', '2024-01-01')
            // ->whereDate('pagos.fecha_transferencia', '>=', '2024-08-01')->whereDate('fecha_transferencia', '<=', '2024-08-31')
            // ->Where('pagos.id_curso', '242260259')
            ->First();
            // ->Get();

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

        return $fileUrls;
    }
}
