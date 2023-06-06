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
        if(isset($ari->fecha_rechazo))
        {
            foreach ($ari->fecha_rechazo as $cadwell)
            {
                $conteo++;
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
                switch ($cadwell->status)
                {
                    case 'Verificando_Pago':
                        $status = 'Verificando Solicitud Pago';
                        $status_estilo = $azestilo;
                    break;
                    case 'Pago_Verificado':
                        $status = 'Solicitud Pago Verificado';
                        $status_estilo = $azestilo;
                    break;
                    case  'Pago_Rechazado':
                        $status = 'Solicitud Pago Rechazado';
                        $status_estilo = $nestilo;
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
                @if(isset($cadwell->fecha_rechazo))
                    @foreach($cadwell->fecha_rechazo as $moist)
                        <th style="{{$td}}">{{$moist['fecha']}}</th>
                        <th style="{{$nestilo}}">RECHAZADO</th>
                    @endforeach
                        <th style="{{$td}}">{{$cadwell->fecha_status}}</th>
                        <th style="{{$vestilo}}">VALIDADO</th>
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
