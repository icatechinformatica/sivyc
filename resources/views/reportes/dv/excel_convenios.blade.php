<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
@php
    $hestilo = "background-color:#c90166;  text-align: center; color: white; font-weight: bold; font-size:12;";
    $bestilo = "text-align: center; font-size:11;";
@endphp
@if(count($data['data'])>0)
<span>Rango de fechas: </span>
<table>
    <thead>
        <tr>
            <th rowspan="2" style="{{$hestilo}} width: 120px;">N. Convenio</th>
            <th rowspan="2" style="{{$hestilo}} width: 320px;">Institución</th>
            <th rowspan="2" style="{{$hestilo}} width: 80px;">Tipo</th>
            <th rowspan="2" style="{{$hestilo}} width: 130px;">Inicio <br/> de Vigencia</th>
            <th rowspan="2" style="{{$hestilo}} width: 130px;">Termino <br/> de Vigencia</th>
            <th rowspan="2" style="{{$hestilo}} width: 200px;">Unidad</th>

            @foreach ($data['anios'] as $anio)
                <th rowspan="2" style="{{$hestilo}} width: 100px;">Total <br/> Cursos {{$anio}}</th>
                <th colspan="3" style="{{$hestilo}}">Egresados Acumulados {{$anio}}</th>
            @endforeach

            @foreach ($data['meses'] as $mes)
            @php
                $partes = explode('-', $mes);
                $res_anio = $partes[0];
                $res_mes = $data['array_meses'][$partes[1]];
            @endphp
                <th colspan="5" style="{{$hestilo}}">Mes de {{$res_mes.' '.$res_anio}}</th>
            @endforeach
        </tr>
        <tr>
            @foreach ($data['anios'] as $anio)
                <th style="{{$hestilo}} width: 80px;">M</th>
                <th style="{{$hestilo}} width: 80px;">H</th>
                <th style="{{$hestilo}} width: 80px;">Total</th>
            @endforeach

            @foreach ($data['meses'] as $mes)
                <th style="{{$hestilo}} width: 80px;">No.Cursos</th>
                <th style="{{$hestilo}} width: 80px;">M</th>
                <th style="{{$hestilo}} width: 80px;">H</th>
                <th style="{{$hestilo}} width: 80px;">Total</th>
                <th style="{{$hestilo}} width: 300px;">Cursos</th>
            @endforeach
        </tr>
    </thead>
    @php
        $i = 1;
    @endphp
    <tbody>
        @foreach($data['data'] as $item)
            <tr>
                <td style="{{$bestilo}}">{{ $item->no_convenio }}</td>
                <td style="{{$bestilo}}">{{ $item->institucion}}</td>
                <td style="{{$bestilo}}">{{ $item->tipo_sector}}</td>
                <td style="{{$bestilo}}">{{ $item->fecha_firma}}</td>
                <td style="{{$bestilo}}">{{ $item->fecha_vigencia}}</td>
                <td style="{{$bestilo}}">{{ $item->unidad}}</td>

                @foreach ($data['anios'] as $anio)
                    <td style="{{$bestilo}}">{{ $item->{'total_cursos_' . $anio} }}</td>
                    <td style="{{$bestilo}}">{{ $item->{'total_mujeres_' . $anio} }}</td>
                    <td style="{{$bestilo}}">{{ $item->{'total_hombres_' . $anio} }}</td>
                    <td style="{{$bestilo}}">{{ $item->{'total_alumnos_' . $anio} }}</td>
                @endforeach

                {{-- meses del anio en rango --}}
                {{-- Enero --}}
                @foreach ($data['meses'] as $mes)
                @php $mes_alias = str_replace('-', '_', $mes); @endphp
                    <td style="{{$bestilo}}">{{ $item->{'total_cursos_' . $mes_alias} }}</td>
                    <td style="{{$bestilo}}">{{ $item->{'total_mujeres_'. $mes_alias} }}</td>
                    <td style="{{$bestilo}}">{{ $item->{'total_hombres_'. $mes_alias} }}</td>
                    <td style="{{$bestilo}}">{{ $item->{'total_alumnos_'. $mes_alias} }}</td>
                    <td style="{{$bestilo}}">{{ $item->{'cursos_'. $mes_alias} }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
@endif
