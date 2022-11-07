
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />    
@php
    $hestilo = "background-color:#900C3F; color:white;";
    $filas = count($data)+1;
@endphp
<table>
    <thead>    
    <tr>
        <th style="{{$hestilo}} width: 30px;" align="center"><b>UNIDAD/ACC.MÓVIL/ZONA</b></th>        
        <th style="{{$hestilo}} width: 8px;" align="center"><b>CUOTA</b></th>
        <th style="{{$hestilo}} width: 10px;" align="center"><b>CURSOS</b></th>
        <th style="{{$hestilo}} width: 12px;" align="center"><b>INSCRITOS</b></th>
        <th style="{{$hestilo}} width: 12px;" align="center"><b>AGRESADOS</b></th>
    </tr>  
    </thead>    
        <tbody>            
            @foreach ($data as $i)               
                <tr>
                    <td>{{ $i->unidad }}</td>
                    <td align="center">{{ $i->costo }}</td>
                    <td align="center">{{ number_format($i->cursos_reportados, 0, '', ',') }}</td>
                    <td align="center">{{ number_format($i->inscritos, 0, '', ',') }}</td>
                    <td align="center">{{ number_format($i->inscritos-$i->desercion, 0, '', ',') }}</td>                    
                </tr>
            @endforeach 
        <tr>
        <td style="{{$hestilo}}"  align="center"></td>
            <td style="{{$hestilo}}"  align="center"><b>TOTALES</b></td>
            <td style="{{$hestilo}}"  align="center"><b>{{"= SUBTOTAL(9,C2:C".$filas.")"}}</b></td>
            <td style="{{$hestilo}}"  align="center"><b>{{"= SUBTOTAL(9,D2:D".$filas.")"}}</b></td>
            <td style="{{$hestilo}}"  align="center"><b>{{"= SUBTOTAL(9,E2:E".$filas.")"}}</b></td>
        </tr>   
    </tbody>        
</table>