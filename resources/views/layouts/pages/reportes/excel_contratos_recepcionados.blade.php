<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
@php
    $hestilo = "background-color:#DEDEDE; border: 1px solid black; text-align:center;";
    $nestilo = "background-color:#E77C72; border: 1px solid black; text-align:center;";
    $azestilo = "background-color:#C3EFFC; border: 1px solid black; text-align:center;";
    $vestilo = "background-color:#87D87F; border: 1px solid black; text-align:center;";
    $td = "border: 1px solid black; text-align:center;";

    $conteo = 0; $total_rows = 4; $par_row = 2;
    foreach ($data as $ari)
    {
        if(isset($ari->historial))
        {
            foreach (json_decode($ari->historial) as $cadwell)
            {
                if($cadwell->status == 'Rechazado')
                {
                    $conteo++;
                }
            }

            $conteo = $conteo*2;

            if($conteo >= $total_rows)
            {
                if($conteo == $total_rows)
                {
                    $total_rows = $total_rows + 2;
                    $par_row++;
                }
                else
                {
                    $total_rows = $conteo;
                    $par_row = $conteo/2;
                }
            }
            $conteo = 0;
        }

    }
@endphp
<table style="border: 1px solid black">
    <thead>
    <tr style="border: 1px solid black;">
        <th width='7px;' rowspan='2' style="{{$hestilo}}" align="center"><b>CONS.</b></th>
        <th colspan='4' style="{{$hestilo}}" align="center"><b>VALIDACIÓN DE CONTRATO</b></th>
        <th width='15px;' rowspan='2' style="{{$nestilo}}"><b>FECHA FIRMA<br>DE CONTRATO</b></th>
        <th width='50px;' rowspan='2' style="{{$hestilo}}" align="center"><b>NOMBRE DEL INSTRUCTOR</b></th>
        <th rowspan="2"></th>
        <th colspan="{{$total_rows}}" style="{{$hestilo}}"><b>VALIDACIÓN POSTERIOR</b></th>
        <th rowspan="2"></th>
        <th width='14px;' rowspan="2" style="{{$nestilo}}"><b>FECHA DE <br> RECEPCIÓN</b></th>
    </tr>
    <tr>
        <th width='12px;' style="{{$nestilo}}"><b>FECHA</b></th>
        <th style="{{$azestilo}}"><b>NÚM.</b></th>
        <th width='22px;' style="{{$vestilo}}"><b>CLAVE CURSO</b></th>
        <th width='24px;' style="{{$vestilo}}"><b>ESTATUS</b></th>
        @for($i = 1; $i <= $par_row; $i++)
            <th width='11px;' style="{{$nestilo}}"><b>FECHA</b></th>
            <th width='14px;' style="{{$vestilo}}"><b>ESTATUS {{$i}}</b></th>
        @endfor
    </tr>
    </thead>
    <tbody>
        @foreach ($data as $key => $cadwell)
            @php
                $no_memo = explode('/',$cadwell->numero_contrato);
                switch ($cadwell->status_recepcion)
                {
                    case 'VALIDADO':
                        if(is_null($cadwell->recepcion)) {
                            $status = 'Entrega Fisica Recibida';
                            $status_estilo = $azestilo;
                        } else {
                            $status = 'Validado y listo para entrega fisica';
                            $status_estilo = $azestilo;
                        }
                    break;
                    case  'Rechazado':
                        $status = 'Documentacion Digital Rechazada';
                        $status_estilo = $nestilo;
                    break;
                    case 'recepcion tradicional':
                        $status = 'Recepcion Tradicional';
                        $status_estilo = $azestilo;
                    break;
                }
            @endphp
            <tr>
                <td style="{{$td}}">{{$key+1}}</td>
                <td style="{{$td}}">{{$cadwell->fecha_status}}</td>
                <td style="{{$td}}">{{$no_memo[3]}}</td>
                <td style="{{$td}}">{{$cadwell->clave}}</td>
                <td style="{{$status_estilo}}">{{$status}}</td>
                <td style="{{$td}}">{{$cadwell->fecha_firma}}</td>
                <td style="{{$td}}">{{$cadwell->nombre}}</td>
                <td></td>
                @php $rl = 0; @endphp
                @if(isset($cadwell->historial))

                    @foreach(json_decode($cadwell->historial) as $moist)
                        @if($moist->status == 'Rechazado')
                        {{-- @php dd($par_row); @endphp --}}
                            <th style="{{$td}}">{{$moist->fecha_rechazo}}</th>
                            <th style="{{$nestilo}}">RECHAZADO</th>
                            @php $rl++; @endphp
                        @elseif($moist->status == 'Citado')
                            <th style="{{$td}}">{{$moist->fecha_validacion}}</th>
                            <th style="{{$vestilo}}">CITADO</th>
                            @php $rl++; @endphp
                        @endif
                    @endforeach
                    @for($m = $rl; $m < $par_row; $m++)
                        <th width='11px;' style="{{$td}}"> - </th>
                        <th width='14px;' style="{{$td}}"> - </th>
                    @endfor
                @else
                    <th style="{{$td}}">{{$cadwell->fecha_status}}</th>
                    <th style="{{$vestilo}}">VALIDADO</th>
                    @for($i = 2; $i <= $par_row; $i++)
                        <th width='11px;' style="{{$td}}"> - </th>
                        <th width='14px;' style="{{$td}}"> - </th>
                    @endfor
                @endif
                <td></td>
                <td style="{{$td}}">{{$cadwell->recepcion}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
