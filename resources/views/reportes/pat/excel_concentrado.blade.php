<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
@php
    $hestilo = "background-color:#DEDEDE;  text-align: center;";
@endphp
@if(count($data)>0) 
<table>
    <thead>    
        <tr>
            <th rowspan="2" style="{{$hestilo}}">NÚM</th>
            <th rowspan="2" style="{{$hestilo}} width: 150px;">FUNCIONES</th>
            <th rowspan="2" style="{{$hestilo}} width: 250px;">ACTIVIDADES</th>
            <th class="col-1" rowspan="2" style="{{$hestilo}}">UM</th> 
            <th rowspan="2" style="{{$hestilo}}"> TIPO UM</th>
            <th colspan="2" style="{{$hestilo}}">META ANUAL</th>
            @php 
                $m = 1;
            @endphp
            @foreach($data[1] as $idorg => $org )
                <th colspan="2" style="{{$hestilo}}   height: 40px;">{{$m++}}<br/> {{ $org }}</th>
            @endforeach                                
        </tr>
        <tr>                                
            <th style="{{$hestilo}}">PROG</th>
            <th style="{{$hestilo}}">ALC</th>
            @foreach($data[1] as $org )
                <th style="{{$hestilo}}">PROG</th>
                <th style="{{$hestilo}}">ALC</th>
            @endforeach
        </tr>                        
    </thead>
    @php
        $i = 1;
        $funcion = $itemfuncion = null;
    @endphp
    <tbody>
        @foreach($data[0] as $item) 
            @php
                if($itemfuncion <> $item->funcion)$funcion = $itemfuncion = $item->funcion;
                else $funcion = null;                                        
            @endphp                              
            <tr>
                <th scope="row"> @if($funcion){{ $i++ }}@endif</th>
                <td>{{ $funcion }}</td>
                <td>{{ $item->procedimiento}}</td>
                <td>{{ $item->unidadm}}</td>
                <td>{{ $item->tipo_unidadm}}</td>
                <td>{{ $item->programada}}</td>
                <td>{{ $item->alcanzada}}</td>
                @foreach($data[1] as $idorg => $org )
                    @php
                        $prog = "prog_".$idorg;
                        $alc = "alc_".$idorg;                                                
                    @endphp
                    <td>{{ $item->$prog}}</td>
                    <td>{{ $item->$alc}}</td>                                            
                @endforeach
            </tr>     
        @endforeach    
    </tbody>
</table>
@endif