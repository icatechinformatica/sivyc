<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
@php
    $hestilo = "background-color:#900C3F; color:white;";
@endphp

@if ($data['fecha1'] != '' && $data['fecha2'] != '' && $data['opcionsel'] == 'fechas')
    <h4>REPORTE DE CONVENIOS CORRESPONDIENTE AL PERIODO ( {{$data['fecha1']}} ) - ( {{$data['fecha2']}} )</h4>
@endif
<table border="1" style="border: medium; border-color: black;">
    <thead>
    <tr>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='4' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        {{-- <th colspan='2' style="{{$hestilo}}"><b></b></th> --}}
    </tr>
    <tr>
        <th colspan='2' style="{{$hestilo}}" align="left"><b>NO. DE CONVENIO</b></th>
        <th colspan='4' style="{{$hestilo}}" align="left"><b>INSTITUCIÓN</b></th>
        <th colspan='2' style="{{$hestilo}}" align="left"><b>FECHA DE FIRMA</b></th>
        <th colspan='2' style="{{$hestilo}}" align="left"><b>FECHA DE TERMINO</b></th>
        <th colspan='2' style="{{$hestilo}}" align="left"><b>TIPO DE CONVENIO</b></th>
        <th colspan='2' style="{{$hestilo}}" align="left"><b>SECTOR</b></th>
        <th colspan='2' style="{{$hestilo}}" align="left"><b>STATUS</b></th>
        {{-- <th colspan='2' style="{{$hestilo}}" align="left"><b>FECHA ALTA</b></th> --}}

    </tr>
    <tr>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='4' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
        <th colspan='2' style="{{$hestilo}}"><b></b></th>
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
                    <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->no_convenio }}</td>
                    <td colspan='4' style ="{{$estilo}}" align="left">{{ $i->institucion }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->fecha_firma }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->fecha_vigencia }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->tipo_convenio }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->sector }}</td>
                    <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->activo  == 'false' ? 'NO PUBLICADO' : 'PUBLICADO' }}</td>
                    {{-- <td colspan='2' style ="{{$estilo}}" align="left">{{ $i->updated_at == '' ? 'SIN FECHA' : $i->updated_at->format('d-m-Y') }}</td> --}}
                </tr>
            @endforeach
    </tbody>
</table>
