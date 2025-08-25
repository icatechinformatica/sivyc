<?php

namespace App\Services\Grupo;

use App\Services\Grupo\AgendaRepository;
use Carbon\Carbon;


class AgendaService
{
    protected $agendaRepository;

    public function __construct(AgendaRepository $agendaRepository)
    {
        $this->agendaRepository = $agendaRepository;
    }

    public function validarHoras(int $grupoId, array $horas)
    {
        return $this->agendaRepository->validarHoras($grupoId, $horas);
    }

    /** Obtener eventos como coleccion Eloquent */
    public function obtenerEventos(int $grupoId)
    {
        return $this->agendaRepository->obtenerEventosPorGrupo($grupoId);
    }

    /** Obtener eventos en formato FullCalendar */
    public function obtenerEventosFullcalendar(int $grupoId)
    {
        return $this->obtenerEventos($grupoId)->map(function ($item) {
            $start = Carbon::parse($item->fecha_inicio . ' ' . $item->hora_inicio);
            $end = Carbon::parse($item->fecha_fin . ' ' . $item->hora_fin);
            return [
                'id' => $item->id,
                'title' => 'Sesión',
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
            ];
        });
    }

    /** Regla: validar traslape [start,end) */
    public function validarTraslape(int $grupoId, Carbon $start, Carbon $end, ?int $excluirId = null): bool
    {
        return $this->agendaRepository->existeTraslape($grupoId, $start, $end, $excluirId);
    }

    /** Crear evento con validación */
    public function crear(int $grupoId, Carbon $start, Carbon $end, bool $horaAlimentos)
    {
        if ($this->validarTraslape($grupoId, $start, $end)) {
            throw new \RuntimeException('El horario seleccionado se traslapa con otro existente.');
        }
        return $this->agendaRepository->crearEvento($grupoId, $start, $end, $horaAlimentos);
    }

    /** Actualizar evento con validación */
    public function actualizar(int $agendaId, int $grupoId, Carbon $start, Carbon $end, bool $horaAlimentos)
    {
        if ($this->validarTraslape($grupoId, $start, $end, $agendaId)) {
            throw new \RuntimeException('El horario seleccionado se traslapa con otro existente.');
        }
        return $this->agendaRepository->actualizarEvento($agendaId, $start, $end, $horaAlimentos);
    }

    public function eliminar(int $agendaId): bool
    {
        return $this->agendaRepository->eliminarEvento($agendaId);
    }
}
