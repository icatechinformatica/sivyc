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
            </tr>
        </thead>
        <tbody>        
            @foreach ($data as $item)
            <?php
                $fecha_hoy = date("Y-m-d");
                $fecha_penul = date("Y-m-d",strtotime($item->termino."- 1 days")); 
                if($fecha_hoy >= $fecha_penul) $boton = true;
                else $boton = false;
            ?>
                <tr>                
                     <td>{{ $item->unidad }}</td>
                     <td>{{ $item->clave }}</td>
                     <td>{{ $item->curso }}</td>
                     <td>{{ $item->nombre }}</td>
                     <td>{{ $item->inicio }}</td>
                     <td>{{ $item->termino }} {{$fecha_hoy}}</td>
                     <td>{{ $item->hini }} - {{ $item->hfin }}</td>
                     <td>{{ $item->status}}</td>
                     <td>{{ $item->turnado}}</td>
                     <td class="text-center">
                        @if($boton AND $item->turnado=="UNIDAD" and ($item->status=="NO REPORTADO" OR $item->status=="NINGUNO") )
                            <a class="nav-link" onclick="editar('{{ $item->clave }}')">
                                <i  class="fa fa-edit  fa-2x fa-lg text-danger"></i>
                            </a>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($boton)
                            <a class="nav-link" onclick="signar('{{ $item->clave }}')">
                                <i  class="fa fa-edit  fa-2x fa-lg text-dark"></i>
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
