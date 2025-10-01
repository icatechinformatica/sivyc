<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
@php
    $hestilo = "background-color:#DEDEDE;";
@endphp
<table>
    <thead>
    <tr>
        <th rowspan='2' style="{{$hestilo}}" align="center"><b>UNIDAD/ACC.MÓVIL/ZONA</b></th>
        <th colspan='6' style="{{$hestilo}}" align="center"><b>CURSOS</b></th>
        <th colspan='3' style="{{$hestilo}}" align="center"><b>HORAS</b></th>
        <th colspan='4' style="{{$hestilo}}" align="center"><b>COSTOS</b></th>
        <th colspan='5' style="{{$hestilo}}" align="center"><b>REPORTADO EN FORMATO T</b></th>
    </tr>
    <tr>
        <th style="{{$hestilo}}"><b>PROG.ANUAL</b></th>
        <th style="{{$hestilo}}"><b>AUTORIZADOS</b></th>
        <th style="{{$hestilo}}"><b>DIFER.</b></th>
        <th style="{{$hestilo}}"><b>SUFIC.AUT.</b></th>
        <th style="{{$hestilo}}">RECEP.FINAN</th>
        <th style="{{$hestilo}}">VALID.FINAN</th>

        <th style="{{$hestilo}}"><b>PROG.ANUAL</b></th>
        <th style="{{$hestilo}}"><b>AUTORIZADAS</b></th>
        <th style="{{$hestilo}}"><b>DIFER.</b></th>

        <th style="{{$hestilo}}"><b>APERTURADOS</b></th>
        <th style="{{$hestilo}}"><b>SUFIC.AUT.</b></th>
        <th style="{{$hestilo}}"><b>DIFER.</b></th>
        <th style="{{$hestilo}}"><b>PAGADO</b></th>

        <th style="{{$hestilo}}"><b>CURSOS</b></th>
        <th style="{{$hestilo}}"><b>INSCRITOS</b></th>
        <th style="{{$hestilo}}"><b>EGRESADOS</b></th>
        <th style="{{$hestilo}}"><b>DESERCIÓN</b></th>
        <th style="{{$hestilo}}"><b>HORAS</b></th>
    </tr>
    </thead>
        <tbody>
            @php
                $totales = $totalz2 = $totalz3 =  ['0'=>0,'1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0,'7'=>0,'8'=>0,'9'=>0,'10'=>0,'11'=>0,'12'=>0,'13'=>0,'14'=>0,'15'=>0,'16'=>0,'17'=>0];
            @endphp
            @foreach ($data as $i)
                @if($i->orden==1)
                    @php
                        $estilo = "background-color:#900C3F; color:white;";
                        $unidad = "UNIDAD ".$i->unidad;
                        $totales[0] += $i->cursos_programados;
                        $totales[1] += $i->cursos_autorizados;
                        $totales[2] += $i->cursos_programados-$i->cursos_autorizados;
                        $totales[3] += $i->suficiencia_autorizada;
                        $totales[4] += $i->recep_financ;
                        $totales[5] += $i->valid_financ;
                        $totales[6] += $i->horas_programadas;
                        $totales[7] += $i->horas_impartidas;
                        $totales[8] += $i->horas_programadas-$i->horas_impartidas;

                        $totales[9] += $i->costo_aperturado;
                        $totales[10] += $i->costo_supre;
                        $totales[11] += $i->costo_aperturado-$i->costo_supre;
                        $totales[12] += $i->pagado;

                        $totales[13] += $i->cursos_reportados;
                        $totales[14] += $i->inscritos;
                        $totales[15] += $i->egresados;
                        $totales[16] += $i->desercion;
                        $totales[17] += $i->horas_reportadas;
                    @endphp
                @elseif($i->orden==3)
                    @php
                        $estilo = "";
                        $unidad =  "ZONA ".$i->ze ;
                        
                        $color = "text-warning";
                        $totalz3[0] += $i->cursos_programados;
                        $totalz3[1] += $i->cursos_autorizados;
                        $totalz3[2] += $i->cursos_programados-$i->cursos_autorizados;
                        $totalz3[3] += $i->suficiencia_autorizada;
                        $totalz3[4] += $i->recep_financ;
                        $totalz3[5] += $i->valid_financ;
                        $totalz3[6] += $i->horas_programadas;
                        $totalz3[7] += $i->horas_impartidas;
                        $totalz3[8] += $i->horas_programadas-$i->horas_impartidas;
                        
                        $totalz3[9] += $i->costo_aperturado;
                        $totalz3[10] += $i->costo_supre;
                        $totalz3[11] += $i->costo_aperturado-$i->costo_supre;
                        $totalz3[12] += $i->pagado;

                        $totalz3[13] += $i->cursos_reportados;
                        $totalz3[14] += $i->inscritos;
                        $totalz3[15] += $i->egresados;
                        $totalz3[16] += $i->desercion;
                        $totalz3[17] += $i->horas_reportadas;
                     @endphp
                @elseif($i->orden==20)
                    @php
                        $estilo = "";
                        $unidad =  "ZONA ".$i->ze ;
                        $color = "text-warning";
                        
                        $totalz2[0] += $i->cursos_programados;
                        $totalz2[1] += $i->cursos_autorizados;
                        $totalz2[2] += $i->cursos_programados-$i->cursos_autorizados;
                        $totalz2[3] += $i->suficiencia_autorizada;
                        $totalz2[4] += $i->recep_financ;
                        $totalz2[5] += $i->valid_financ;
                        $totalz2[6] += $i->horas_programadas;
                        $totalz2[7] += $i->horas_impartidas;
                        $totalz2[8] += $i->horas_programadas-$i->horas_impartidas;
                        
                        $totalz2[9] += $i->costo_aperturado;
                        $totalz2[10] += $i->costo_supre;
                        $totalz2[11] += $i->costo_aperturado-$i->costo_supre;
                        $totalz2[12] += $i->pagado;

                        $totalz2[13] += $i->cursos_reportados;
                        $totalz2[14] += $i->inscritos;
                        $totalz2[15] += $i->egresados;
                        $totalz2[16] += $i->desercion;
                        $totalz2[17] += $i->horas_reportadas;
                     @endphp
                @else
                    @php
                        $estilo = "";
                        $unidad =  $i->unidad ;
                    @endphp
                @endif
                <tr>
                    <td style ="{{$estilo}}">{{ $unidad }}</td>
                    <td style ="{{$estilo}}" align="center">
                            {{ number_format($i->cursos_programados, 0, '', ',') }}
                    </td>
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->cursos_autorizados, 0, '', ',') }}</td>
                    @if($i->cursos_autorizados>$i->cursos_programados AND $i->ze!='A')
                        <td style ="color:red;" align="center">{{ number_format($i->cursos_programados-$i->cursos_autorizados, 0, '', ',') }}</td>
                    @else
                        <td style ="{{$estilo}}"align="center">{{ number_format($i->cursos_programados-$i->cursos_autorizados, 0, '', ',') }}</td>
                    @endif
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->suficiencia_autorizada, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->recep_financ, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->valid_financ, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}" align="center">                        
                        {{ number_format($i->horas_programadas, 0, '', ',') }}                        
                    </td>
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->horas_impartidas, 0, '', ',') }}</td>
                    @if($i->horas_programadas-$i->horas_impartidas<0 AND $i->ze!='A')
                        <td style ="color:red;" align="center">{{ number_format($i->horas_programadas-$i->horas_impartidas, 0, '', ',') }}</td>
                    @else
                        <td style ="{{$estilo}}" align="center">{{ number_format($i->horas_programadas-$i->horas_impartidas, 0, '', ',') }}</td>
                    @endif
                    <td style ="{{$estilo}}">{{ number_format($i->costo_aperturado, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}">{{ number_format($i->costo_supre, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}">{{ number_format($i->costo_aperturado-$i->costo_supre, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}">{{ number_format($i->pagado, 0, '', ',') }}</td>

                    <td style ="{{$estilo}}" align="center">{{ number_format($i->cursos_reportados, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->inscritos, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->egresados, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->desercion, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->horas_reportadas, 0, '', ',') }}</td>
                </tr>
            @endforeach
        <tr>
            <td style="{{$hestilo}}"  align="center"><b>TOTAL ZONA III</b></td>
            @for($n=0;$n<=17;$n++)
                <td style="{{$hestilo}}" align="center"><b>{{ number_format($totalz3[$n], 0, '', ',') }}</b></td>
             @endfor
        </tr>
        <tr>
            <td style="{{$hestilo}}"  align="center"><b>TOTAL ZONA II</b></td>
            @for($n=0;$n<=17;$n++)
                <td style="{{$hestilo}}" align="center"><b>{{ number_format($totalz2[$n], 0, '', ',') }}</b></td>
             @endfor
        </tr>
        <tr>
            <td style="{{$hestilo}}"  align="center"><b>TOTALES</b></td>
            @for($n=0;$n<=17;$n++)
                <td style="{{$hestilo}}" align="center"><b>{{ number_format($totales[$n], 0, '', ',') }}</b></td>
             @endfor
        </tr>
    </tbody>
</table>
