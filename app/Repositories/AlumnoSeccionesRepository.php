<?php

namespace App\Repositories;

use App\Models\Alumno;
use Illuminate\Support\Facades\Log;

class AlumnoSeccionesRepository implements AlumnoSeccionesRepositoryInterface
{
    /**
     * Crea o actualiza un alumno por CURP.
     *
     * @param array $datos
     * @return Alumno
     */
    public function actualizarOrCrearPorCURP(array $datos)
    {
        try {
            $resultado = Alumno::updateOrCreate(
                ['curp' => $datos['curp']],
                $datos
            );
            return $resultado;
        } catch (\Exception $e) {
            Log::error('Error al actualizar o crear alumno: ' . $e->getMessage());
            throw new \Exception('Error al guardar los datos del alumno');
        }
    }

    public function obtenerArchivosDocumentos($curp)
    {
        try {
            $alumno = Alumno::where('curp', $curp)->first();
            if (!$alumno) {
                throw new \Exception('Alumno no encontrado');
            }
            // Decodificar el JSON si es necesario
            $archivos = $alumno->archivos_documentos;
            if (is_string($archivos)) {
                $archivos = json_decode($archivos, true) ?? [];
            }
            return is_array($archivos) ? $archivos : [];
        } catch (\Exception $e) {
            Log::error('Error al obtener archivos documentos: ' . $e->getMessage());
            throw new \Exception('Error al obtener los archivos documentos del alumno');
        }
    }
}
