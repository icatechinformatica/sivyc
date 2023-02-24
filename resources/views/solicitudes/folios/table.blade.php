<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">UNIDAD</th>
                <th scope="col">MOD</th>
                <th scope="col">FOLIO INICIAL</th>
                <th scope="col">FOLIO FINAL</th>                
                <th scope="col">TOTAL</th>                
                <th scope="col" class="text-center">PENDIENTES</th>
                <th scope="col" class="text-center">ASIGNADOS</th>
                <th scope="col">NUM. ACTA</th>
                <th scope="col">FECHA ACTA</th>
                <th scope="col">PUBLICADO</th>
                <th scope="col">PDF ACTA</th>
                <th scope="col" class="text-center">EDITAR</th>                
            </tr>
        </thead>
        <tbody>
        <?php $i = 1; ?>
            @foreach ($data as $item)
                <?php $pendientes = $item->total-$item->contador; ?>
                <tr>     
                     <td>{{ $i++ }}</td>          
                     <td>{{ $item->unidad }}</td>
                     <td>{{ $item->mod }}</td>
                     <td>{{ $item->finicial }}</td>
                     <td>{{ $item->ffinal }}</td>
                     <td>{{ $item->total }}</td>                     
                     <td class="text-center">{{ $item->total-$item->contador }}</td>
                     <td class="text-center">{{ $item->contador }}</td>
                     <td>{{ $item->num_acta }}</td>
                     <td>{{ $item->facta }}</td>                 
                     <td class="text-center"> 
                     @if($item->activo==true)
                        <b>SI</b>
                     @else 
                        <p class="text-danger"><b>NO</b></p>            
                     @endif
                     </td>
                     <td>
                        @if($item->file_acta)
                            <a class="nav-link"  href="{{ $path_file.$item->file_acta }}" target="_blank">
                                <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                            </a>  
                        @else 
                            {{ "NO ADJUNTADO"}}
                        @endif
                     </td> 
                     <td class="text-center">
                        @if($item->total>$item->contador)                        
                            <a class="nav-link" onclick="editar('{{ $item->id }}',{{$item->contador}})">
                                <i  class="fa fa-edit  fa-2x fa-lg text-success"></i>
                            </a>
                        @endif                        
                    </td>
                </tr>
            @endforeach
                <tr>
                    <td colspan="13" >
                       {{ $data->render() }}
                     </td>
                </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>
