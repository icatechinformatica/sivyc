@extends('theme.formatos.hlayout')
@section('title', 'Solicitud ARC-02 | SIVyC Icatech')
@section('css')
    <style>
         body { margin-top: 140px;  margin-bottom: 23px;} 
        .tableg td{padding: 0px;}
        .tablas{border-collapse: collapse;width: 100%;}
        .tablas tr, .tablas th, .tablas td{font-size: 8px; border: gray 1px solid; text-align: center;}
        .tablaf { border-collapse: collapse; width: 100%;}     
        .tablaf td { font-size: 8px; text-align: center; padding: 0px;}

        #titulo{position: fixed; top: 45px;}
        #titulo h4{padding:0px; margin:0px 0px 2px 0px; font-size: 11px; font-weight:bold;}
        #titulo h3{padding:0px; margin:0px; font-size: 12px; font-weight:bold;}
        #titulo table{position: fixed; top: 100px;}
        #para {position: relative; top: -30px; height:auto; width:60%; font-size: 9px; font-weight:bold; margin-bottom:-25px;}
    </style>
@endsection
@section('header') 
    @php
        if(strpos($reg_unidad->unidad.$reg_unidad->cct, "07EIC0")) 
            $nombre_unidad = "UNIDAD DE CAPACITACIÓN ";
        else
            $nombre_unidad = "ACCIÓN MÓVIL ";          
    @endphp
    <div id="titulo">
        <h4>{{ $nombre_unidad }} {{ $reg_cursos[0]->unidad }}</h4>
        <h4>DEPARTAMENTO ACADÉMICO</h4>
        <h4>ARC-02</h4>

        <table width="100%">
            <tr>        
                <td style='text-align:right;font-size: 9px;'>
                    {{ $nombre_unidad }} {{ $reg_cursos[0]->unidad }}<br/>
                    MEMORÁNDUM NO. {{ $memo_apertura }} <br/>
                    {{ $reg_unidad->municipio }}, Chiapas; {{$fecha_memo }}.<br/>
                </td>                    
            </tr>   
        </table>  
    </div>    
@endsection
@section('body')
    <div id="para"> 
        PARA: {{ $reg_unidad->dacademico }}, {{$reg_unidad->pdacademico}}<br/>
        DE: {{ $reg_unidad->dunidad }}, {{$reg_unidad->pdunidad}}<br/>
        ASUNTO: SOLICITUD DE REPROGRAMACION, CANCELACION O CORRECCIÓN<br/><br/>     
        CC. ARCHIVO/MINUTARIO
    </div>
    <table class="tablas">
            <tbody>
                <tr> 
                    <th rowspan="2">SERVICIO</th>     	  
                    <th rowspan="2">NOMBRE</th>     
                    <th rowspan="2">MOD</th>               
                    <th colspan="2">TIPO</th>       
                    <th rowspan="2">HORAS</th>
                    <th rowspan="2">CLAVE</th>
                    <th rowspan="2">NUM. DE <br> MEMORANDUM DE <br> AUT. DE CLAVE</th>
                    <th rowspan="2">INSTRUCTOR</th>
                    <th rowspan="2">INICIO </th>  
                    <th rowspan="2">TERMINO</th>                    
                    <th rowspan="2">ESPACIO FISICO</th>
                    <th rowspan="2">MOTIVO</th>
                    <th rowspan="2">SOLICITA</th>
                    <th rowspan="2">OBSERVACIONES</th>
                </tr>  
                <tr> 
                    <th >PRES<br>EN</th>                 
                    <th >DISTA<br>NCIA</th> 
                </tr>
                @foreach($reg_cursos as $a)         
                    <tr>
                        <td>@php if($a->tipo_curso=='CURSO'){echo'CURSO';}if($a->tipo_curso=='CERTIFICACION'){echo'CERTIFICACION EXTRAORDINARIA';} @endphp</td>
                        <td style="width: 8%;">{{ $a->curso }}</td>
                        <td>{{ $a->mod }}</td>                           
                        <td>@if($a->tcapacitacion=="PRESENCIAL"){{ "X" }}@endif</td>
                        <td>@if($a->tcapacitacion=="A DISTANCIA"){{ "X" }}@endif</td>
                        <td>{{ $a->dura }}</td>
                        <td style="width:8%;">{{ $a->clave }}</td>
                        <td>@if ($a->mvalida) {{ substr($a->mvalida ,0,12)}} {{ substr($a->mvalida ,12,strlen($a->mvalida ))}} @endif</td>
                        <td style="width: 10%;">{{ $a->nombre }}</td>    
                        <td style="width: 5%;">{{ $a->inicio }}</td>                           
                        <td style="width: 5%;">{{ $a->termino }}</td>                           
                        <td style="width: 15%;">{{ $a->efisico }}</td>
                        <td>@isset($a->motivo){{ $a->motivo }}@else {{$a->opcion}}@endisset</td>
                        <td style="width: 8%;">{{ $a->realizo }}</td> 
                        <td style="width: 15%;">{{$a->observaciones}}</td>                         
                    </tr> 
                @endforeach
            </tbody>
        </table>
        <br><br><br><br><br>
        <div>           
            <table class="tablaf">
                <tr>
                    <td> </td><td> </td><td> </td><td> </td>                
                    <td align="center"><b>ELABORO</b><br><br><br><br></td>                    
                    <td> </td><td> </td><td> </td><td> </td><td> </td>                
                    <td align="center"><b>Vo. Bo.</b><br><br><br><br></td>                
                </tr>
                <tr>
                    <td> </td><td> </td><td> </td><td> </td>                
                    <td align="center">_____________________________________________________<br><b>{{ $reg_unidad->academico }}</b></td>                    
                    <td> </td><td> </td><td> </td><td> </td><td> </td>
                    <td align="center">_____________________________________________________<br><b>{{ $reg_unidad->dunidad }}</b></td>                
                    <td> </td><td> </td><td> </td><td> </td><td> </td>
                    <td align="center"><br><b>SELLO UNIDAD DE<br>CAPACITACION</b></td>                
                </tr>            
                <tr>
                    <td> </td><td> </td><td> </td><td> </td>                
                    <td align="center"><b>{{ $reg_unidad->pacademico }}</b></td>                    
                    <td> </td><td> </td><td> </td><td> </td><td> </td>
                    <td align="center"><b>{{ $reg_unidad->pdunidad }}</b></td>                
                </tr>
            </table>           
        </div>

@endsection
@section('js')
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(40, 538, "Pág $PAGE_NUM de $PAGE_COUNT", $font, 8);
                
            ');
        }
    </script>
@endsection


       