<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
@php
    $hestilo = "background-color:#AF114D;  text-align: center; color: white; font-weight: bold; font-size:12;";
    $bestilo = "text-align: center; font-size:12;";
@endphp
@if(count($data)>0) 
<table>
    <thead>    
        <tr>            
            <th style="{{$hestilo}} width: 90px;">N. de <br/>Quincena</th>
            <th style="{{$hestilo}} width: 95px;">Subsistema</th>
            <th style="{{$hestilo}} width: 95px;">Entidad</th>
            <th style="{{$hestilo}} width: 150px;">Zona Económica</th>
            <th style="{{$hestilo}} width: 150px;">Registro Federal de<br/> Contribuyentes<br/>(RFC)</th>
            <th style="{{$hestilo}} width: 200px;">Clave Única de<br/>Registro  de <br/>Personal (CURP)</th>
            <th style="{{$hestilo}} width: 120px; text-align: left;">Primer Apellido</th>
            <th style="{{$hestilo}} width: 120px; text-align: left;">Segundo Apellido</th>
            <th style="{{$hestilo}} width: 120px; text-align: left;">Nombres</th>            
            <th style="{{$hestilo}} width: 180px;">Tipo de Plaza <br/>(Directiva, <br/>Administrativa,<br/>Docente)</th>
            <th style="{{$hestilo}} width: 320px;">Denominación<br/> de Plaza o<br/> Categoría</th>
            <th style="{{$hestilo}} width: 120px;">Código de <br/>la plaza</th>
            <th style="{{$hestilo}} width: 120px;">Número de <br/>horas <br/>(Docente)</th>
            <th style="{{$hestilo}} width: 140px;">CCT de <br/>adscripción</th>
        </tr>                          
    </thead>
    @php
        $i = 1;        
    @endphp
    <tbody>
        @foreach($data as $item) 
            <tr>                
                <td style="{{$bestilo}}">{{ $item->nqna}}</td>
                <td style="{{$bestilo}}">{{ $item->subsistema}}</td>                                        
                <td style="{{$bestilo}}">{{ $item->entidad}}</td>
                <td style="{{$bestilo}}">{{ $item->ze}}</td>
                <td style="{{$bestilo}}">{{ $item->rfc}}</td>
                <td style="{{$bestilo}}">{{ $item->curp}}</td>
                <td style="font-size:12px;">{{ $item->apaterno}}</td>
                <td style="font-size:12px;">{{ $item->amaterno}}</td>
                <td style="font-size:12px;">{{ $item->nombre}}</td>
                <td style="{{$bestilo}}">{{ $item->tipo_plaza}}</td>
                <td style="{{$bestilo}}">{{ $item->plaza}}</td>
                <td style="{{$bestilo}}">{{ $item->codigo_plaza}}</td>                                        
                <td style="{{$bestilo}}">{{ $item->horas}}</td>
                <td style="{{$bestilo}}">{{ $item->cct}}</td>
            </tr>     
        @endforeach 
    </tbody>
</table>
@endif