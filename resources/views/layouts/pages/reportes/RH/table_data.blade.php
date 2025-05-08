{{-- tabla_resultados.blade.php --}}
<caption>Catalogo de Funcionarios</caption>
<thead>
    <tr>
        <th scope="col" width="110px">No. DE ENLACE</th>
        <th scope="col" width="250px">UNIDAD DE CAPACITACIÓN</th>
        <th scope="col" width="250px">NOMBRE</th>
        <th scope="col" width="90px">FECHA</th>
        <th scope="col">ENTRADA - SALIDA</th>
        <th scope="col" width="120px">RETARDO / FALTA</th>
        <th scope="col">JUSTIFICANTE</th>
        <th scope="col">OBSERVACIÓN</th>
        {{-- <th width="80px">ACCION</th> --}}
    </tr>
</thead>
<tbody>
    @foreach($data as $key => $registro)
        <tr>
            <td style="text-align: center;">{{$registro->numero_enlace}}</td> <!-- Muestra el número de enlace -->
            <td>{{$registro->nombre_adscripcion}}</td> <!-- Muestra la unidad de capacitación -->
            <td>{{$registro->nombre_trabajador}}</td> <!-- Muestra el nombre del trabajador -->
            <td>{{$registro->fecha}}</td> <!-- Muestra la fecha -->
            <td>{{$registro->entrada}} - {{$registro->salida}}</td> <!-- Muestra la entrada y salida -->
            <td>
                @if(is_null($registro->salida) || $registro->inasistencia)
                    Inasistencia
                @elseif($registro->retardo)
                    Retardo
                @endif
            </td> <!-- Muestra Inasistencia o Retardo dependiendo de las condiciones -->
            <td>{{$registro->justificante}}</td> <!-- Muestra el justificante -->
            <td>{{$registro->observaciones}}</td> <!-- Muestra las observaciones -->
            {{-- <td></td> <!-- Enlace para ver detalles --> --}}
        </tr>
    @endforeach
</tbody>
<tfoot>
    <tr>
        <td colspan="8">
            {{ $data->appends(request()->query())->links() }} <!-- Paginación -->
        </td>
    </tr>
</tfoot>
