<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">MATRICULA</th>
                <th scope="col">ALUMNO</th>
                <th scope="col">UNIDAD</th>
                <th scope="col">CLAVE</th>
                <th scope="col">CURSO</th>                
                <th scope="col">FOLIO</th>
                <th scope="col">ESTATUS</th>                
                <th scope="col">CANCELAR</th>
        </thead>
        <tbody>
        @if($data)
            <?php $i = 1; ?>
            @foreach ($data as $item)
                <tr>     
                     <td>{{ $i++ }}</td>          
                     <td>{{ $item->matricula }}</td>
                     <td>{{ $item->alumno }}</td>
                     <td>{{ $item->unidad }}</td>
                     <td>{{ $item->clave }}</td>
                     <td>{{ $item->curso }}</td>                     
                     <td>{{ $item->folio }}</td>
                     <td>{{ $item->movimiento }}</td>                     
                     <td class='text-center'>    
                        @if( $item->movimiento == "EXPEDIDO" AND $item->reexpedicion==false)                 
                            <div class="form-check">
                                <input class="form-check-input custom-checkbox checkbox-lg" type="checkbox" value="{{$item->id_folio}}"  name="ids[]" id="check + {{ $item->id_folio }}">
                            </div>                     
                        @endif
                     </td>
                </tr>
            @endforeach              
        @endif
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>
