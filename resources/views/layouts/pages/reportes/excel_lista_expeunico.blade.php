<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
@php
    $hestilo = "background-color:#900C3F; color:white;";
@endphp

@if ($data['anio'] != '')
    <br>
    <h4>REPORTE EXPEDIENTE UNICO</h4>
    <h4>EJERCICIO: {{$data['anio']}}</h4>
    <br>
@endif
<table border="1" style="border: medium; border-color: black;">
    <thead>
    <tr>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='3' style="{{$hestilo}}"><b></b></th>
        <th colspan='5' style="{{$hestilo}}"><b></b></th>
        <th colspan='4' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
    </tr>
    <tr>
        <th colspan='2' style="{{$hestilo}}" align="center"><b>UNIDAD</b></th>
        <th colspan='2' style="{{$hestilo}}" align="center"><b>FOLIO DE GRUPO</b></th>
        <th colspan='3' style="{{$hestilo}}" align="center"><b>CLAVE</b></th>
        <th colspan='5' style="{{$hestilo}}" align="center"><b>CURSO</b></th>
        <th colspan='4' style="{{$hestilo}}" align="center"><b>INSTRUCTOR</b></th>
        <th colspan='2' style="{{$hestilo}}" align="center"><b>INICIO</b></th>
        <th colspan='2' style="{{$hestilo}}" align="center"><b>TERMINO</b></th>
        <th colspan='2' style="{{$hestilo}}" align="center"><b>HORA DE INICIO</b></th>
        <th colspan='2' style="{{$hestilo}}" align="center"><b>HORA DE TERMINO</b></th>
        <th colspan='2' style="{{$hestilo}}" align="center"><b>FECH ENVIO</b></th>
        <th colspan='2' style="{{$hestilo}}" align="center"><b>FECH RETORNO</b></th>
        <th colspan='2' style="{{$hestilo}}" align="center"><b>FECH VALIDACION</b></th>
        <th colspan='2' style="{{$hestilo}}" align="center"><b>ESTATUS</b></th>
    </tr>
    <tr>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='3' style="{{$hestilo}}"><b></b></th>
        <th colspan='5' style="{{$hestilo}}"><b></b></th>
        <th colspan='4' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
    </tr>
    </thead>
        <tbody>
            @php
                $estilo = "background-color:#DEDEDE; color:black;";
                $data = $data['data'];
            @endphp
            @foreach ($data as $i)
                <tr>
                    <td colspan='2' style ="{{$estilo}}" align="center">{{ $i->unidad }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="center">{{ $i->folio_grupo }}</td>
                    <td colspan='3' style ="{{$estilo}}" align="center">{{ $i->clave }}</td>
                    <td colspan='5' style ="{{$estilo}}" align="left">{{ $i->curso }}</td>
                    <td colspan='4' style ="{{$estilo}}" align="left">{{ $i->nombre }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="center">{{ \Carbon\Carbon::parse($i->inicio)->format('d-m-Y') }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="center">{{ \Carbon\Carbon::parse($i->termino)->format('d-m-Y') }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="center">{{ $i->hini }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="center">{{ $i->hfin }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="center">{{ $i->fec_envio == '' ? 'SIN FECHA' : \Carbon\Carbon::parse($i->fec_envio)->format('d-m-Y H:i') }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="center">{{ $i->fec_return == '' ? 'SIN FECHA' : \Carbon\Carbon::parse($i->fec_return)->format('d-m-Y H:i') }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="center">{{ $i->fec_valid == '' ? 'SIN FECHA' : \Carbon\Carbon::parse($i->fec_valid)->format('d-m-Y H:i') }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="center">{{ $i->st_admin }}</td>
                </tr>
            @endforeach
    </tbody>
</table>
