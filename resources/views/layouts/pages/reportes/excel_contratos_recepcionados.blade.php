<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
@php
    $hestilo = "background-color:#DEDEDE;";
    $restilo = "background-color: red;";
    $azestilo = "background-color: blue;";
    $vestilo = "background-color: green;";
@endphp
<table>
    <thead>
    <tr>
        <th rowspan='2' style="{{$hestilo}}" align="center"><b>CONS.</b></th>
        <th colspan='4' style="{{$hestilo}}" align="center"><b>VALIDACIÓN DE CONTRATO</b></th>
        <th rowspan='2' style="{{$restilo}}" align="center"><b>FECHA FIRMA DE CONTRATO</b></th>
        <th rowspan='2' style="{{$hestilo}}" align="center"><b>NOMBRE DEL INSTRUCTOR</b></th>
    </tr>
    <tr>
        <th style="{{$restilo}}"><b>FECHA</b></th>
        <th style="{{$azestilo}}"><b>NÚM.</b></th>
        <th style="{{$vestilo}}"><b>CLAVE CURSO</b></th>
        <th style="{{$vestilo}}"><b>ESTATUS</b></th>
    </tr>
    </thead>
    <tbody>
        @php
        @endphp
        @foreach ($data as $key => $cadwell)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$cadwell->fecha_status}}</td>
                <td>{{$cadwell->numero_contrato}}</td>
                <td>{{$cadwell->clave}}</td>
                <td>{{$cadwell->status}}</td>
                <td>{{$cadwell->fecha_firma}}</td>
                <td>{{$cadwell->nombre}}</td>
            </tr>
        @endforeach
</tbody>
</table>
