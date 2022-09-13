<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />    
@php
    $hestilo = "background-color:#DEDEDE;";
@endphp
<table>
    <thead>    
    <tr>
        <th rowspan='2' style="{{$hestilo}}" align="center"><b>UNIDAD/ACC.MÓVIL/ZONA</b></th>
        <th colspan='4' style="{{$hestilo}}" align="center"><b>CURSOS</b></th>
        <th colspan='3' style="{{$hestilo}}" align="center"><b>HORAS</b></th>
        <th colspan='5' style="{{$hestilo}}" align="center"><b>REPORTADO EN FORMATO T</b></th>
    </tr>
    <tr>                        
        <th style="{{$hestilo}}"><b>PROG.ANUAL</b></th>
        <th style="{{$hestilo}}"><b>AUTORIZADOS</b></th>
        <th style="{{$hestilo}}"><b>DIFER.</b></th>
        <th style="{{$hestilo}}"><b>SUFIC.AUT.</b></th>
        <th style="{{$hestilo}}"><b>PROG.ANUAL</b></th>
        <th style="{{$hestilo}}"><b>AUTORIZADAS</b></th>
        <th style="{{$hestilo}}"><b>DIFER.</b></th>
        <th style="{{$hestilo}}"><b>CURSOS</b></th>
        <th style="{{$hestilo}}"><b>INSCRITOS</b></th>
        <th style="{{$hestilo}}"><b>EGRESADOS</b></th>
        <th style="{{$hestilo}}"><b>DESERCIÓN</b></th>
        <th style="{{$hestilo}}"><b>HORAS</b></th>
    </tr>
    </thead>    
        <tbody>  
            @php
                $totales = ['0'=>0,'1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0,'7'=>0,'8'=>0,'9'=>0,'10'=>0,'11'=>0];
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
                        $totales[4] += $i->horas_programadas;
                        $totales[5] += $i->horas_impartidas;
                        $totales[6] += $i->horas_programadas-$i->horas_impartidas;
                        $totales[7] += $i->cursos_reportados;
                        $totales[8] += $i->inscritos;
                        $totales[9] += $i->egresados;
                        $totales[10] += $i->desercion;
                        $totales[11] += $i->horas_reportadas;
                    @endphp                        
                @elseif($i->orden==2)
                    @php
                        $estilo = "";
                        $unidad =  "ZONA ".$i->ze ;                            
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
                    <td style ="{{$estilo}}" align="center">
                        @if($i->ze == $i->poa_ze)
                            {{ number_format($i->horas_programadas, 0, '', ',') }}
                        @else
                            {{ '0' }}
                        @endif
                    </td>
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->horas_impartidas, 0, '', ',') }}</td>
                    @if($i->horas_programadas-$i->horas_impartidas<0 AND $i->ze!='A')
                        <td style ="color:red;" align="center">{{ number_format($i->horas_programadas-$i->horas_impartidas, 0, '', ',') }}</td>
                    @else
                        <td style ="{{$estilo}}" align="center">{{ number_format($i->horas_programadas-$i->horas_impartidas, 0, '', ',') }}</td>
                    @endif                                
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->cursos_reportados, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->inscritos, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->egresados, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->desercion, 0, '', ',') }}</td>
                    <td style ="{{$estilo}}" align="center">{{ number_format($i->horas_reportadas, 0, '', ',') }}</td>
                </tr>
            @endforeach 
        <tr>
            <td style="{{$hestilo}}"  align="center"><b>TOTALES</b></td> 
            @for($n=0;$n<=11;$n++)
                <td style="{{$hestilo}}" align="center"><b>{{ number_format($totales[$n], 0, '', ',') }}</b></td>                             
             @endfor
        </tr>   
    </tbody>        
</table>