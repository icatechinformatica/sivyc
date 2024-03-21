<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
@php
    $hestilo = "background-color:#900C3F; color:white;";
@endphp

@if ($data['fecha1'] != '' && $data['fecha2'] != '')
    <h4>REPORTE CORRESPONDIENTE AL PERIODO ( {{$data['fecha1']}} ) - ( {{$data['fecha2']}} )</h4>
@endif
@if ($data['curso'] != '')
    <h4>CURSO:{{$data['curso']}}</h4>
@endif
<table border="1" style="border: medium; border-color: black;">
    <thead>
    <tr>
        <th colspan='3' style="{{$hestilo}}"><b></b></th>
        <th colspan='4' style="{{$hestilo}}"><b></b></th>
        <th colspan='1' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='3' style="{{$hestilo}}"><b></b></th>
        <th colspan='3' style="{{$hestilo}}"><b></b></th>
        <th colspan='3' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='3' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        {{-- <th colspan='2' style="{{$hestilo}}"><b></b></th> --}}
    </tr>
    <tr>
        <th colspan='3' style="{{$hestilo}}" align="left"><b>CURP</b></th>
        <th colspan='4' style="{{$hestilo}}" align="left"><b>ALUMNO</b></th>
        <th colspan='1' style="{{$hestilo}}" align="left"><b>EDAD</b></th>
        <th colspan='2' style="{{$hestilo}}" align="left"><b>NACIONALIDAD</b></th>
        <th colspan='2' style="{{$hestilo}}" align="left"><b>SEXO</b></th>
        <th colspan='3' style="{{$hestilo}}" align="left"><b>DOMICILIO</b></th>
        <th colspan='3' style="{{$hestilo}}" align="left"><b>COLONIA</b></th>
        <th colspan='3' style="{{$hestilo}}" align="left"><b>MUNICIPIO</b></th>
        <th colspan='2' style="{{$hestilo}}" align="left"><b>ESTADO</b></th>
        <th colspan='2' style="{{$hestilo}}" align="left"><b>ESTADO CIVIL</b></th>
        <th colspan='3' style="{{$hestilo}}" align="left"><b>GRADO DE ESTUDIOS</b></th>
        <th colspan='2' style="{{$hestilo}}" align="left"><b>TELÉFONO</b></th>
        <th colspan='2' style="{{$hestilo}}" align="left"><b>CORREO ELECTRONICO</b></th>
        {{-- <th colspan='2' style="{{$hestilo}}" align="left"><b>FECHA ALTA</b></th> --}}

    </tr>
    <tr>
        <th colspan='3' style="{{$hestilo}}"><b></b></th>
        <th colspan='4' style="{{$hestilo}}"><b></b></th>
        <th colspan='1' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='3' style="{{$hestilo}}"><b></b></th>
        <th colspan='3' style="{{$hestilo}}"><b></b></th>
        <th colspan='3' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='3' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        {{-- <th colspan='2' style="{{$hestilo}}"><b></b></th> --}}
    </tr>
    </thead>
        <tbody>
            @php
                $estilo = "background-color:#DEDEDE; color:black;";
                $data = $data['data'];
            @endphp
            @foreach ($data as $i)

                <tr>
                    <td colspan='3' style ="{{$estilo}}" align="left">{{ $i->curp }}</td>
                    <td colspan='4' style ="{{$estilo}}" align="left">{{ $i->alumno }}</td>
                    <td colspan='1' style ="{{$estilo}}" align="left">{{ $i->edad }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->nacionalidad }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->sexo }}</td>
                    <td colspan='3' style ="{{$estilo}}" align="left">{{ $i->domicilio }}</td>
                    <td colspan='3' style ="{{$estilo}}" align="left">{{ $i->colonia}}</td>
                    <td colspan='3' style ="{{$estilo}}" align="left">{{ $i->municipio }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->estado }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->estado_civil }}</td>
                    <td colspan='3' style ="{{$estilo}}" align="left">{{ $i->ultimo_grado_est }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->telefono }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->correo }}</td>
                    {{-- <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->updated_at == '' ? 'SIN FECHA' : $i->updated_at->format('d-m-Y') }}</td> --}}
                </tr>
            @endforeach
    </tbody>
</table>
