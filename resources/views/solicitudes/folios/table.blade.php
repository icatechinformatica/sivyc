<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">UNIDAD</th>
                <th scope="col">MODALIDAD</th>
                <th scope="col">FOLIO INICIAL</th>
                <th scope="col">FOLIO FINAL</th>                
                <th scope="col">TOTAL</th>
                <th scope="col" class="text-center">ASIGNADOS</th>
                <th scope="col">NUM. ACTA</th>
                <th scope="col">FECHA ACTA</th>
                <th scope="col">PUBLICADO</th>
                <th scope="col" class="text-center">EDITAR</th>                
            </tr>
        </thead>
        <tbody>
        <?php $i = 1; ?>
            @foreach ($data as $item)
                <tr>     
                     <td>{{ $i++ }}</td>          
                     <td>{{ $item->unidad }}</td>
                     <td>{{ $item->mod }}</td>
                     <td>{{ $item->finicial }}</td>
                     <td>{{ $item->ffinal }}</td>
                     <td>{{ $item->total }}</td>
                     <td class="text-center">{{ $item->contador }}</td>
                     <td>{{ $item->num_acta }}</td>
                     <td>{{ $item->facta }}</td>                 
                     <td> @if($item->activo==true){{ 'SI' }}@else {{ 'NO' }} @endif</td> 
                     <td class="text-center">
                        @if($item->contador>0)                        
                            <a class="nav-link" onclick="editar('{{ $item->id }}')">
                                <i  class="fa fa-edit  fa-2x fa-lg text-success"></i>
                            </a>
                        @endif                        
                    </td>
                </tr>
            @endforeach
                <tr>
                    <td colspan="11" >
                       {{ $data->render() }}
                     </td>
                </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>
