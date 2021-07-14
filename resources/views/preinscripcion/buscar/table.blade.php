<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->

<div class="table-responsive">
    <table class="table ">
        <thead>
            <tr>
                <th scope="col">GRUPO</th>                
                <th scope="col">CURSO</th>                
                <th scope="col">TURNADO A</th>
                <th scope="col" class="text-center">OPCIONES</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>                
                    <td>
                        {{ $item->folio_grupo }}
                    </td>
                    <td>
                        {{ $item->curso }}
                    </td>
                    <td>
                        {{ $item->turnado }}
                    </td>
                    <td class="text-center">
                        @if($item->turnado == 'VINCULACION' OR $activar)                       
                            <a class="nav-link" ><i class="fa fa-edit  fa-2x fa-lg text-success" title="Editar" onclick="show('{{$item->folio_grupo}}')"></i></a>
                        @else 
                            <a class="nav-link" ><i class="fa fa-search  fa-2x fa-lg text-info" title="Ver" onclick="show('{{$item->folio_grupo}}')"></i></a>
                        @endif
                    </td>
                </tr>
            @endforeach
                <tr>
                    <td colspan="10" >
                       {{ $data->render() }}
                     </td>
                </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>