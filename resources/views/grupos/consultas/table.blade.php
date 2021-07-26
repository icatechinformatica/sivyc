<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">UNIDAD</th>
                <th scope="col">CLAVE</th>
                <th scope="col">CURSO</th>
                <th scope="col">INSTRUCTOR</th>
                <th scope="col" width="86px">INICIO</th>
                <th scope="col" width="86px">TERMINO</th>
                <th scope="col" width="7%">HORARIO</th>
                <th scope="col" width="7%">ESTATUS</th>
                <th scope="col" width="7%">TURNADO A</th>                
                <th width="5%">CALIFICACIONES</th>
                <th width="5%">FOLIOS</th>
                <th width="6%">CANCELAR</th>
            </tr>
        </thead>
        <tbody>        
            @foreach ($data as $item)           
                <tr>                
                     <td>{{ $item->unidad }}</td>
                     <td>{{ $item->clave }}</td>
                     <td>{{ $item->curso }}</td>
                     <td>{{ $item->nombre }}</td>
                     <td>{{ $item->inicio }}</td>
                     <td>{{ $item->termino }}</td>
                     <td>{{ $item->hini }}</td>
                     <td>{{ $item->status}}</td>
                     <td>{{ $item->turnado}}</td>
                     <td class="text-center">                        
                            <a class="nav-link" alt="Registrar Calificaciones" onclick="editar('{{ $item->clave }}')">
                                <i  class="fa fa-edit  fa-2x fa-lg text-dark"></i>
                            </a>                        
                    </td>
                    <td class="text-center">
                        
                            <a class="nav-link" onclick="signar('{{ $item->clave }}')">
                                <i  class="fa fa-edit  fa-2x fa-lg text-dark"></i>
                            </a>
                        
                    </td>
                    <td class="text-center">                        
                        <a class="nav-link" onclick="cancelar('{{ $item->clave }}')">
                            <i  class="fa fa-edit  fa-2x fa-lg text-danger"></i>
                        </a>
                    
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
