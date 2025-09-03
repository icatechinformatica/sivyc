<?php

namespace App\Repositories\Grupo;

use App\Agenda; // usa tabla tbl_grupo_agenda
use App\Services\Grupo\AgendaRepository;
use Carbon\Carbon;

class AgendaEloquentRepository implements AgendaRepository
{
    public function validarHoras(int $grupoId, array $horas)
    {
        // Este método parece legacy; adaptamos a columnas reales si aplica.
        // No hay columna "hora" directa; devolvemos colecciones vacías o ajustar según uso real.
        return collect();
    }

    public function obtenerEventosPorGrupo(int $grupoId)
    {
        return Agenda::where('id_grupo', $grupoId)->get();
    }

    public function obtenerEventoPorId(int $agendaId)
    {
        return Agenda::find($agendaId);
    }

    public function existeTraslape(int $grupoId, \DateTimeInterface $start, \DateTimeInterface $end, ?int $excluirId = null): bool
    {
        $startStr = Carbon::instance(Carbon::parse($start))->format('Y-m-d H:i:s');
        $endStr = Carbon::instance(Carbon::parse($end))->format('Y-m-d H:i:s');

        return Agenda::where('id_grupo', $grupoId)
            ->when($excluirId, function ($q) use ($excluirId) {
                $q->where('id', '<>', $excluirId);
            })
            ->where(function ($q) use ($startStr, $endStr) {
                $q->whereRaw("CONCAT(fecha_inicio, ' ', hora_inicio) < ?", [$endStr])
                  ->whereRaw("CONCAT(fecha_fin, ' ', hora_fin) > ?", [$startStr]);
            })
            ->exists();
    }

    public function crearEvento(int $grupoId, \DateTimeInterface $start, \DateTimeInterface $end, bool $horaAlimentos)
    {
        $startC = Carbon::parse($start);
        $endC = Carbon::parse($end);
        return Agenda::create([
            'id_grupo' => $grupoId,
            'fecha_inicio' => $startC->toDateString(),
            'hora_inicio' => $startC->format('H:i:s'),
            'fecha_fin' => $endC->toDateString(),
            'hora_fin' => $endC->format('H:i:s'),
            'hora_alimentos' => $horaAlimentos,
        ]);
    }

    public function actualizarEvento(int $agendaId, \DateTimeInterface $start, \DateTimeInterface $end, bool $horaAlimentos)
    {
        $agenda = Agenda::findOrFail($agendaId);
        $startC = Carbon::parse($start);
        $endC = Carbon::parse($end);
        $agenda->update([
            'fecha_inicio' => $startC->toDateString(),
            'hora_inicio' => $startC->format('H:i:s'),
            'fecha_fin' => $endC->toDateString(),
            'hora_fin' => $endC->format('H:i:s'),
            'hora_alimentos' => $horaAlimentos,
        ]);
        return $agenda;
    }

    public function eliminarEvento(int $agendaId): bool
    {
        $agenda = Agenda::findOrFail($agendaId);
        return (bool) $agenda->delete();
    }
}
