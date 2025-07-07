{{-- tabla_resultados.blade.php --}}
<caption>Catalogo de Funcionarios</caption>
<thead>
    <tr>
        <th scope="col" width="110px">No. DE ENLACE</th>
        <th scope="col" width="250px">UNIDAD DE CAPACITACIÓN</th>
        <th scope="col" width="250px">NOMBRE</th>
        <th width="80px">ACCION</th>
    </tr>
</thead>
<tbody>
    @foreach($data as $key => $registro)
        <tr>
            <td style="text-align: center;">{{$registro->numero_enlace}}</td> <!-- Muestra el número de enlace -->
            <td>{{$registro->nombre_adscripcion}}</td> <!-- Muestra la unidad de capacitación -->
            <td>{{$registro->nombre_trabajador}}</td> <!-- Muestra el nombre del trabajador -->
            <td>
                <a data-toggle="modal" data-placement="top" data-target="#ReporteModal">
                    <i class="fa fa-file-pdf fa-2x fa-lg text-danger" title="Generar Reporte"></i>
                </a> &nbsp;&nbsp;&nbsp;&nbsp;
                <a href="{{route('rh.reporte.detalles', ['id' => $registro->numero_enlace])}}">
                    <i class="fa fa-eye fa-2x fa-lg text-success" title="Ver Detalles"></i>
                </a>
            </td> <!-- Enlace para ver detalles -->
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
