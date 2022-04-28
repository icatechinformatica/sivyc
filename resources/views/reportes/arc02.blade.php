<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ARC02</title>
    <style>  
        body{font-family: sans-serif; margin: 30px 10px 30px ; }    
        img.izquierda {float: left;width: 200px;height: 60px;}
        img.derecha {float: right;width: 200px;height: 60px;}
        .tableg td{padding: 0px;}
        .tablas{border-collapse: collapse;width: 100%;}
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 1px 1px autom;}
        .tablaf { border-collapse: collapse; width: 100%;}     
        .tablaf td { font-size: 8px; text-align: center; padding: 0px;}
        #watermark {
            position: fixed;

            /** 
                Establece una posición en la página para tu imagen
                Esto debería centrarlo verticalmente
            **/
            bottom:   .1cm;
            left:     .1cm;

            /** Cambiar las dimensiones de la imagen **/
            width:    26cm;
            height:   20cm;

            /** Tu marca de agua debe estar detrás de cada contenido **/
            z-index:  -1000;
        }
    }
    </style>
</head>
<body>
    <div class= "container g-pt-30">
        @if($reg_cursos[0]->unidad=="COMITAN" || $reg_cursos[0]->unidad=="OCOSINGO" || $reg_cursos[0]->unidad=="SAN CRISTOBAL" || $reg_cursos[0]->unidad=="TUXTLA" || $reg_cursos[0]->unidad=="CATAZAJA" || $reg_cursos[0]->unidad=="YAJALON" || $reg_cursos[0]->unidad=="JIQUIPILAS" || $reg_cursos[0]->unidad=="REFORMA" || $reg_cursos[0]->unidad=="TAPACHULA" || $reg_cursos[0]->unidad=="TONALA" || $reg_cursos[0]->unidad=="VILLAFLORES")
            @php
                $nombre_unidad= "UNIDAD DE CAPACITACION"
            @endphp
        @else
            @php
                $nombre_unidad= "ACCION MOVIL"
            @endphp
        @endif
        @if ($marca)
        <div id="watermark">
            <img src="img/marcadeagua.png" height="100%" width="100%" />
        </div>  
        @endif
        <div>
            <img class="izquierda" src='img/logohorizontalica1.png'>
            <img class="derecha" src='img/chiapas.png'><br>
            <div align=center style="font-size: 10px;"> 
                <b>{{$distintivo}}</b><br>
                <b>{{ $nombre_unidad }} {{ $reg_cursos[0]->unidad }}</b><br>
                <b>DEPARTAMENTO ACADEMICO</b><br>
                <b>ARC-02</b>                                              
            </div><br>
        </div>
        <div style="font-size: 8px;">
            <table style="border-collapse: collapse; width: 100%;" class="tableg">
                    <tr style="padding: 0px;">
                        <td><b>PARA: {{ $reg_unidad->dacademico }}, {{$reg_unidad->pdacademico}}</b></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td ALIGN="right"><b>{{ $nombre_unidad }} {{ $reg_cursos[0]->unidad }}</b></td>
                    </tr> 
                    <tr style="padding: 0px;">
                        <td><b>DE: {{ $reg_unidad->dunidad }}, {{$reg_unidad->pdunidad}}</b></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="right"><b>MEMORANDUM NO. {{ $memo_apertura }}</b></td>                        
                    </tr>
                    <tr style="padding: 0px;">
                        <td><b>ASUNTO: SOLICITUD DE REPROGRAMACION, CANCELACION O CORRECCIÓN</b></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="right"><b>{{ $reg_cursos[0]->unidad }},CHIS; {{$fecha_memo }}</b></td>                        
                    </tr>
                    <tr style="padding: 0px;">
                        <td><b>CC. ARCHIVO MINUTARIO</b></td>                        
                    </tr>               
            </table>
        </div>
        <br>
        <div >
            <table class="tablas">
                <tbody>                        
                    <tr> 
                        <th rowspan="2">SERVICIO</th>     	  
                        <th rowspan="2">NOMBRE</th>     
                        <th rowspan="2">MOD.</th>               
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
                            <th>@php if($a->tipo_curso=='CURSO'){echo'CURSO';}if($a->tipo_curso=='CERTIFICACION'){echo'CERTIFICACION EXTRAORDINARIA';} @endphp</th>
                            <th style="width: 8%;">{{ $a->curso }}</th>
                            <th>{{ $a->mod }}</th>                           
                            <th>@if($a->tcapacitacion=="PRESENCIAL"){{ "X" }}@endif</th>
                            <th>@if($a->tcapacitacion=="A DISTANCIA"){{ "X" }}@endif</th>
                            <th>{{ $a->dura }}</th>
                            <th style="width:8%;">{{ $a->clave }}</th>
                            <th style="width: 5%;">{{ $a->mvalida }}</th>
                            <th style="width: 10%;">{{ $a->nombre }}</th>    
                            <th style="width: 5%;">{{ $a->inicio }}</th>                           
                            <th style="width: 5%;">{{ $a->termino }}</th>                           
                            <th style="width:5%;">{{ $a->efisico }}</th>
                            <th>@isset($a->motivo){{ $a->motivo }}@else {{$a->opcion}}@endisset</th>
                            <th>{{ $a->realizo }}</th> 
                            <th style="width: 15%;">{{$a->observaciones}}</th>                         
                        </tr> 
                    @endforeach
                </tbody>                                               
            </table>
        </div><br><br><br><br><br>
        <div>
            @if (!$marca)
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
            @endif
        </div>
    
    </div>
</body>
</html>