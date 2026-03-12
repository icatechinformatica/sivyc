<table>
    @foreach($recordsByEmployee as $workno => $rows)
        @php
            $first      = $rows->first();
            $func       = $first->funcionario;
            $nombre     = $func->nombre_trabajador ?? trim(($first->first_name ?? '').' '.($first->last_name ?? ''));
            $puesto     = $first->puesto; // accessor
            $unidadNom = $first->unidad;
        @endphp

        {{-- Fila: número de empleado + nombre + adscripción (como en tu Excel) --}}
        <tr>
            <td colspan="2">{{ $workno }}</td>
            <td colspan="4">{{ mb_strtoupper($nombre) }}</td>
            <td colspan="2">{{ mb_strtoupper($puesto) }}</td>
        </tr>

        {{-- Fila título de unidad (COMITAN / DELEGACIÓN / etc.) --}}
        <tr>
            <td colspan="2"></td>
            <td colspan="6">{{ mb_strtoupper($unidadNom) }}</td>
        </tr>

        {{-- Encabezados de tabla para ese empleado --}}
        <tr>
            <td>Fecha</td>
            <td>Semana</td>
            <td colspan="3">Hora</td>
            <td>Veces</td>
            <td>Working Time</td>
            <td></td>
        </tr>

        @php
            // Agrupar por día
            $rowsByDay = $rows->groupBy(function($r) {
                return $r->check_time_local->format('Y-m-d');
            });
        @endphp

        @foreach($rowsByDay as $date => $dayRows)
            @php
                $dateObj   = \Carbon\Carbon::parse($date);
                $diaCorto  = $dateObj->locale('es')->isoFormat('ddd'); // Lun, Mar, etc.

                // Ordenamos por hora
                $sorted    = $dayRows->sortBy('check_time_local');

                // Texto de horas "08:13:55 - 16:00:45  08:32:00 - 16:05:00 ..."
                $horasText = $sorted
                    ->map(function ($r) {
                        return $r->check_time_local->format('H:i:s');
                    })
                    ->chunk(2) // entrada / salida
                    ->map(function ($par) {
                        return $par->implode(' - ');
                    })
                    ->implode('   ');

                $veces      = $dayRows->count();

                $firstIn    = $sorted->first()->check_time_local ?? null;
                $lastOut    = $sorted->last()->check_time_local ?? null;

                $working    = 0;
                if ($firstIn && $lastOut && $lastOut->gt($firstIn)) {
                    $working = $lastOut->diffInMinutes($firstIn) / 60;
                }
            @endphp

            <tr>
                <td>{{ $dateObj->format('d/m/Y') }}</td>
                <td>{{ ucfirst($diaCorto) }}</td>
                <td colspan="3">{{ $horasText }}</td>
                <td>{{ $veces }}</td>
                <td>{{ number_format($working, 2) }}</td>
                <td></td>
            </tr>
        @endforeach

        {{-- Fila en blanco como separador entre empleados --}}
        <tr><td colspan="8"></td></tr>
    @endforeach
</table>
