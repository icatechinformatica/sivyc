<?php

namespace App\Interfaces\Services;

interface DocumentacionServiceInterface
{
    /**
     * Obtiene los documentos de vinculación para un folio de grupo
     */
    public function obtenerDocumentosVinculacion($folio_grupo);
}
