<?php

namespace App\Services\Estatus;

use App\Interfaces\ActualizarEstatusRepositoryInterface;

class ActualizarEstatusService
{
    protected $estatusRepository;

    public function __construct(ActualizarEstatusRepositoryInterface $estatusRepository)
    {
        $this->estatusRepository = $estatusRepository;
    }

    public function actualizarAlumnoEstatus($alumnoId, $nuevoEstatus, $seccion = null)
    {
        $secciones = [
            0 => 'datos_personales',
            1 => 'domicilio',
            2 => 'contacto',
            3 => 'grupo_vulnerable',
            4 => 'capacitacion',
            5 => 'laboral',
            6 => 'cerss',
        ];

        // Buscar el índice de la sección actual
        $seccionIndex = null;
        if ($seccion !== null && in_array($seccion, $secciones)) {
            $seccionIndex = array_search($seccion, $secciones);
        }

        // Obtener el último estatus del alumno
        $ultimoEstatus = $this->estatusRepository->obtenerUltimoEstatus($alumnoId);

        if (!$ultimoEstatus || !$ultimoEstatus->estatus || $ultimoEstatus->estatus->isEmpty()) {
            // Si no hay estatus previo, permitir la actualización
            return $this->estatusRepository->actualizarEstatus($alumnoId, $nuevoEstatus, $seccion);
        }

        // Obtener las secciones del último estatus
        $estatusActual = $ultimoEstatus->estatus->first();
        $seccionesGuardadas = [];

        if ($estatusActual && $estatusActual->pivot && $estatusActual->pivot->secciones) {
            $seccionesGuardadas = json_decode($estatusActual->pivot->secciones, true) ?: [];
        }

        // Verificar si la sección a actualizar tiene un ID menor que las ya finalizadas
        if ($seccionIndex !== null && $seccionesGuardadas) {
            foreach ($seccionesGuardadas as $seccionGuardada => $datos) {
                $seccionGuardadaIndex = array_search($seccionGuardada, $secciones);

                // Si encontramos una sección finalizada con índice mayor, no permitimos actualizar una sección menor
                if (
                    $seccionGuardadaIndex !== false &&
                    isset($datos['finalizada']) &&
                    $datos['finalizada'] === true &&
                    $seccionIndex < $seccionGuardadaIndex
                ) {

                    // No actualizar si el ID de la sección es menor que una ya finalizada
                    return false;
                }
            }
        }
        if ($seccion === 'cerss') {
            $nuevoEstatus = 5;  // Finalizo
            $resultado = $this->estatusRepository->actualizarEstatus($alumnoId, $nuevoEstatus, $seccion);
            return [
                'success' => $resultado,
                'finalizado' => true
            ];
        }

        return $this->estatusRepository->actualizarEstatus($alumnoId, $nuevoEstatus, $seccion);
    }
}
