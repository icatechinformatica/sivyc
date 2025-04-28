<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">UNIDAD</th>
                <th scope="col">FOL.GRUPO</th>
                <th scope="col">CLAVE</th>
                <th scope="col">CURSO</th>
                <th scope="col">INSTRUCTOR</th>
                <th scope="col" width="86px">FECHAS</th>                
                <th scope="col" width="7%">HORARIO</th>
                <th scope="col" width="7%">FORMATOT</th>
                <th scope="col" width="7%">TURNADOA</th>
                <th width="5%">ARC-01</th>
                <th width="5%">CALIFIC</th>
                <th width="5%">FOLIOS</th>
                <th width="6%">CANCELAR</th>
            </tr>
        </thead>
        <tbody>            
            @foreach ($data as $item)
                @php 
                    if($item->turnado == 'PENDIENTE') $style = 'style = "color: #AE192D"';
                    elseif($item->turnado == 'APROBADO') $style = 'style = "color: #009887"';
                    else  $style = null;                    
                @endphp
                <tr>
                     <td>{{ $item->unidad }}</td>
                     <td>{{ $item->folio_grupo }}</td>
                     <td>{{ $item->clave }}</td>
                     <td>{{ $item->curso }}</td>
                     <td>{{ $item->nombre }}</td>
                     <td>{{ $item->inicio }} AL </br>{{ $item->termino }}</td>                     
                     <td>{{ $item->hini }} A </br>{{ $item->hfin }}</td>
                     <td>{{ $item->status}}</span></td>
                     <td class="text-center"><span @php echo $style @endphp> <b>{{ $item->turnado}}</b></span></td>
                     <td class="text-center">
                        <a class="nav-link" alt="Tramitar ARC-01" onclick="arc01('{{ $item->folio_grupo }}')">
                            <i  class="fa fa-eye  fa-2x fa-lg text-dark"></i>
                        </a>
                    </td>
                     <td class="text-center">
                        @if($item->clave AND $item->status=="NO REPORTADO")
                            <a class="nav-link" alt="Registrar Calificaciones" onclick="editar('{{ $item->clave }}')">
                                <i  class="fa fa-edit  fa-2x fa-lg text-success"></i>
                            </a>
                        @else 
                            <i  class="fa fa-edit  fa-2x fa-lg text-muted"></i>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item->clave)
                            <a class="nav-link" onclick="signar('{{ $item->clave }}')">
                                <i  class="fa fa-edit  fa-2x fa-lg text-primary"></i>
                            </a>
                        @else 
                            <i  class="fa fa-edit  fa-2x fa-lg text-muted"></i>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item->clave)
                            <a class="nav-link" onclick="cancelar('{{ $item->clave }}')">
                                <i  class="fa fa-edit  fa-2x fa-lg text-danger"></i>
                            </a>
                        @else 
                            <i  class="fa fa-edit  fa-2x fa-lg text-muted"></i>
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
