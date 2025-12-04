<?php

if (!function_exists('archivos_url')) {
    /**
     * Genera la URL completa para acceder a un archivo
     * 
     * Soporta múltiples escenarios para compatibilidad con datos legacy:
     * 1. URL legacy con /storage/: http://localhost/storage/uploadFiles/... → http://localhost:8000/storage/uploadFiles/...
     * 2. URL completa válida: https://archivos.icatech.com.mx/... → se retorna tal cual
     * 3. Ruta relativa + Local: uploadFiles/... → http://localhost:8000/storage/uploadFiles/...
     * 4. Ruta relativa + S3: uploadFiles/... → https://archivos.icatech.com.mx/uploadFiles/...
     * 
     * @param string $pathOrUrl - Puede ser URL completa o ruta relativa
     *                            Ej: "http://localhost/storage/uploadFiles/..." (legacy)
     *                            Ej: "uploadFiles/alumnos/123/requisitos.pdf" (nuevo)
     * @return string URL completa
     */
    function archivos_url(string $pathOrUrl): string
    {
        // Detectar URLs legacy con formato: http(s)://dominio/storage/ruta/archivo
        // Extraer solo la parte después de /storage/
        if (preg_match('#^https?://[^/]+/storage/(.+)$#', $pathOrUrl, $matches)) {
            $pathOrUrl = $matches[1]; // Extraer: uploadFiles/alumnos/123/file.pdf
        }
        // Si es otra URL completa válida (que comience con http:// o https://), retornarla tal cual
        elseif (preg_match('#^https?://#', $pathOrUrl)) {
            return $pathOrUrl;
        }
        
        // Limpiar la ruta (quitar slashes iniciales/finales)
        $relativePath = trim($pathOrUrl, '/');
        
        // Obtener la URL base de archivos desde .env
        $archivosBaseUrl = env('ARCHIVOS_BASE_URL', null);
        
        // Si está configurada la URL de S3/CloudFront, usarla
        if (!empty($archivosBaseUrl)) {
            return rtrim($archivosBaseUrl, '/') . '/' . $relativePath;
        }
        
        // Si no, usar URL local con /storage/
        // Laravel storage:link crea: public/storage -> storage/app/public
        // Entonces la URL es: APP_URL/storage/{relativePath}
        return url('/storage/' . $relativePath);
    }
}