<?php

namespace App\Services\Documentacion;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ModelExpe\ExpeUnico;

class DocumentacionService
{
    private $path_uploadFiles;

    public function __construct()
    {
        $this->path_uploadFiles = env("APP_URL") . '/storage/uploadFiles';
    }

    /**
     * Obtiene los documentos de vinculación para un folio de grupo
     */
    public function obtenerDocumentosVinculacion($folio_grupo)
    {
        $linkPDF = [
            "acta" => '',
            "convenio" => '',
            "soli_ape" => '',
            "sid" => '',
            "status_dpto" => 'INVALID'
        ];

        try {
            $jsonvincu = ExpeUnico::select('vinculacion')
                ->where('folio_grupo', '=', $folio_grupo)
                ->first();

            if (isset($jsonvincu->vinculacion['doc_1']) && isset($jsonvincu->vinculacion['status_dpto'])) {
                $docs_json = [
                    $jsonvincu->vinculacion['doc_1']['url_pdf_acta'],
                    $jsonvincu->vinculacion['doc_1']['url_pdf_convenio'],
                    $jsonvincu->vinculacion['doc_3']['url_documento'],
                    $jsonvincu->vinculacion['doc_4']['url_documento']
                ];

                $linkPDF = [
                    "acta" => ($docs_json[0] != '') ? $this->path_uploadFiles . $docs_json[0] : "",
                    "convenio" => ($docs_json[1] != '') ? $this->path_uploadFiles . $docs_json[1] : "",
                    "soli_ape" => ($docs_json[2] != '') ? $this->path_uploadFiles . $docs_json[2] : "",
                    "sid" => ($docs_json[3] != '') ? $this->path_uploadFiles . $docs_json[3] : "",
                    "status_dpto" => ($jsonvincu->vinculacion['status_dpto'] != '') ? 
                        $jsonvincu->vinculacion['status_dpto'] : "INVALID"
                ];
            }
        } catch (\Throwable $th) {
            // Log error if needed
            // Log::error("Error al cargar documentos: " . $th->getMessage());
        }

        return $linkPDF;
    }
}
