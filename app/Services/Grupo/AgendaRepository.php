<?php

namespace App\Services\Grupo;

interface AgendaRepository
{
    /**
     * Compatibilidad legacy: validar horas específicas existentes para un grupo.
     */
    public function validarHoras(int $grupoId, array $horas);

    /** Obtener todos los eventos de agenda por grupo */
    public function obtenerEventosPorGrupo(int $grupoId);

    /** Obtener un evento por id */
    public function obtenerEventoPorId(int $agendaId);

    /** Existe traslape entre [start,end) para el grupo (opcionalmente excluyendo un id) */
    public function existeTraslape(int $grupoId, \DateTimeInterface $start, \DateTimeInterface $end, ?int $excluirId = null): bool;

    /** Crear evento */
    public function crearEvento(int $grupoId, \DateTimeInterface $start, \DateTimeInterface $end, bool $horaAlimentos);

    /** Actualizar evento */
    public function actualizarEvento(int $agendaId, \DateTimeInterface $start, \DateTimeInterface $end, bool $horaAlimentos);

    /** Eliminar evento */
    public function eliminarEvento(int $agendaId): bool;
}
