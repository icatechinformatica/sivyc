<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->

<div class="table-responsive">
    <table class="table ">
        <thead>
            <tr>
                <th scope="col">GRUPO</th>
                <th scope="col">CURSO</th>
                <th scope="col">UNIDAD</th>
                <th scope="col">TURNADO A</th>
                <th scope="col" class="text-center">OPCIONES</th>
                @can('enviar.grupo.vobo')
                    <th scope="col" class="text-center">VoBo</th>
                @endcan
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item->folio_grupo }}</td>
                    <td>{{ $item->curso }}</td>
                    <td>{{ $item->unidad }}</td>
                    <td>{{ $item->turnado }}</td>
                    <td class="text-center">
                        @if($item->id_instructor)
                            @if($item->turnado == 'VINCULACION')
                                <a class="nav-link" ><i class="fa fa-edit  fa-2x fa-lg text-success" title="Editar" onclick="show('{{$item->folio_grupo}}')"></i></a>
                            @else
                                <a class="nav-link" ><i class="fa fa-search  fa-2x fa-lg text-info" title="Ver" onclick="show('{{$item->folio_grupo}}')"></i></a>
                            @endif
                        @endif
                    </td>
                    @can('preinscripcion.grupovobo')
                        <td class="text-center">
                            @if($item->turnado == 'VINCULACION')
                                <a class="nav-link" ><i class="fa fa-edit  fa-2x fa-lg text-success" title="Editar" onclick="show2('{{$item->folio_grupo}}')"></i></a>
                            @else
                                <a class="nav-link" ><i class="fa fa-search  fa-2x fa-lg text-info" title="Ver" onclick="show2('{{$item->folio_grupo}}')"></i></a>
                            @endif

                        </td>
                    @endcan
                </tr>
            @endforeach
                <tr>
                     <td colspan="10" class="text-center">
                        <strong>Resultados totales: {{ $data->total() }}</strong>
                       {{ $data->appends(request()->query())->links('pagination::bootstrap-5') }}
                     </td>
                </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>
