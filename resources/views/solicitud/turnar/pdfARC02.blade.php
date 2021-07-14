<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ARC02</title>
    <style>      
        body{font-family: sans-serif}
        @page {margin: 30px 20px 10px 20px;}
            header { position: fixed; left: 0px; top: 10px; right: 0px; text-align: center;}
            header h1{height:0; line-height: 14px; padding: 9px; margin: 0;}
            header h2{margin-top: 20px; font-size: 8px; border: 1px solid gray; padding: 12px; line-height: 18px; text-align: justify;}
            footer {position:fixed;   left:0px;   bottom:-170px;   height:150px;   width:100%;}
            footer .page:after { content: counter(page, sans-serif);}
            img.izquierda {float: left;width: 200px;height: 60px;}
            img.izquierdabot {position: absolute;left: 50px;width: 350px;height: 60px;}
            img.derechabot {position: absolute;right: 50px;width: 350px;height: 60px;}
            img.derecha {float: right;width: 200px;height: 60px;}
        .tablas{border-collapse: collapse;width: 100%;}
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;}
        .tablaf { border-collapse: collapse; width: 100%;}     
        .tablaf tr td { font-size: 8px; text-align: center; padding: 0px;}
        .tablaj { border-collapse: collapse;}     
        .tablaj { font-size: 8px;border: gray 1px solid; text-align: left; padding: 0px;}
        .tablag { border-collapse: collapse; width: 100%;}     
        .tablag tr td { font-size: 8px; padding: 0px;}
        .tablad { width: 170px;border-spacing: -6px;}     
        .tablad tr td{ font-size: 8px;text-align: right;padding: 0px;}
        div.ex1 {width: 200px;height: 70px;overflow: hidden;border:gray 1px solid;}
        label {display: block;padding-left: 40px;text-indent: -15px;}
        input {width: 13px;height: 13px;padding: 1;margin:0;vertical-align: bottom;position: relative;top: -1px;*overflow: hidden;
}
    }
    </style>
</head>
<body>
    <div class= "container g-pt-30">
        <div id="content">
        <img class="izquierda" src='img/logohorizontalica1.png'>
        <img class="derecha" src='img/chiapas.png'>
        @if($reg_cursos[0]->unidad=="COMITAN" || $reg_cursos[0]->unidad=="OCOSINGO" || $reg_cursos[0]->unidad=="SAN CRISTOBAL" || $reg_cursos[0]->unidad=="TUXTLA" || $reg_cursos[0]->unidad=="CATAZAJA" || $reg_cursos[0]->unidad=="YAJALON" || $reg_cursos[0]->unidad=="JIQUIPILAS" || $reg_cursos[0]->unidad=="REFORMA" || $reg_cursos[0]->unidad=="TAPACHULA" || $reg_cursos[0]->unidad=="TONALA" || $reg_cursos[0]->unidad=="VILLAFLORES")
            @php
                $nombre_unidad= "UNIDAD DE CAPACITACION"
            @endphp
        @else
            @php
                $nombre_unidad= "ACCION MOVIL"
            @endphp
        @endif  
            <div id="wrappertop">
                <div align=center><br> 
                    <font size=1><b>{{ $nombre_unidad }} {{ $reg_cursos[0]->unidad }}<br/>
                    <font size=1>DEPARTAMENTO ACADEMICO</font><br/>
                    <font size=1>ARC-02 DE </font><br/>
                    <font size=1>{{$instituto->distintivo}}</font><br/>                                              
                </div><br>
            </div>
            <table class="tablag">
                <body>
                    <tr>
                        <td><b>PARA: {{ $reg_unidad->dacademico }}</b></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td ALIGN="right"><b>{{ $nombre_unidad }} {{ $reg_cursos[0]->unidad }}</b></td>
                    </tr> 
                    <tr>
                        <td><b>DE: {{ $reg_unidad->dunidad }}</b></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="right"><b>MEMORANDUM NO. {{ $memo_apertura }}</b></td>                        
                    </tr>
                    <tr>
                        <td><b>ASUNTO: SOLICITUD DE REPROGRAMACION O CANCELACION DE CURSO</b></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="right"><b>{{ $reg_unidad->unidad }},CHIAPAS; {{$fecha_memo }}</b></td>                        
                    </tr>
                    <tr>
                        <td><b>CC. ARCHIVO MINUTARIO</b></td>                        
                    </tr>                                                                
                </body>                
            </table>
            <div class="ex1" style="font-size:8px;float:right;" align="right">
                    @if($reg_cursos[0]->opcion=="REPROGRAMACION FECHA/HORA")
                        <label><input type="checkbox" id="cb1" value="repro" checked><b>REPROGRAMACION FECHA/HORA</b></label>
                    @else
                        <label><input type="checkbox" id="cb1" value="repro"><b>REPROGRAMACION FECHA/HORA</b></label>
                    @endif       
                    @if($reg_cursos[0]->opcion=="CAMBIO DE INSTRUCTOR")
                        <label class="label"><input class="input" type="checkbox" id="cb2" value="cambio" checked><b>CAMBIO DE INSTRUCTOR</b></label>
                    @else
                        <label class="label"><input class="input" type="checkbox" id="cb2" value="cambio"><b>CAMBIO DE INSTRUCTOR</b></label>
                    @endif
                    @if($reg_cursos[0]->opcion=="CANCELACION DE CURSO")
                        <label class="label"><input class="input" type="checkbox" id="cb2" value="cambio" checked><b>CANCELACION DE CURSO</b></label>
                    @else
                        <label class="label"><input class="input" type="checkbox" id="cb2" value="cambio"><b>CANCELACION DE CURSO</b></label>
                    @endif
                    @if($reg_cursos[0]->opcion=="OTRO")
                        <label class="label"><input class="input" type="checkbox" id="cb2" value="cambio" checked><b>OTRO</b></label>
                    @else
                        <label class="label"><input class="input" type="checkbox" id="cb2" value="cambio"><b>OTRO</b></label>
                    @endif
            </div>
            
            <br><br><br>
            <div class="table-responsive-sm">
                <table class="tablas">
                    <tbody>
                        <tr>
                            <th> ARC02 </th>
                            <td> </td>
                        </tr>                        
                        <tr>      	  
                            <th rowspan="2">NOMBRE DEL CURSO</th>     
                            <th rowspan="2">MOD</th>               
                            <th colspan="2">TIPO<br>DE<br>CURSO</th>       
                            <th rowspan="2">DUR.</th>
                            <th rowspan="2">CLAVE DEL CURSO</th>
                            <th rowspan="2">NUM. DE MEMORANDUM DE AUT. DE CURSO</th>
                            <th rowspan="2">INSTRUCTOR</th>
                            <th rowspan="2">INICIO </th>  
                            <th rowspan="2">TERMINO</th>                    
                            <th rowspan="2">ESPACIO FISICO<br> DONDE SE <br>IMPARTE EL CURSO</th>
                            <th rowspan="2">MOTIVO DE LA MODIFICACION / CANCELACION DEL CURSO</th>
                            <th rowspan="2">SOLICITA</th>
                            <th rowspan="2">OBSERVACIONES</th>
                        </tr>  
                        <tr> 
                            <th >PRE<br>SEN<br>CIAL</th>                 
                            <th >A <br>DIS<br>TAN<br>CIA</th> 
                        </tr>
                        @foreach($reg_cursos as $a)         
                            <tr>
                                <th>{{ $a->curso }}</th>
                                <th>{{ $a->mod }}</th>                           
                                <th>@if($a->tcapacitacion=="PRESENCIAL"){{ "X" }}@endif</th>
                                <th>@if($a->tcapacitacion=="A DISTANCIA"){{ "X" }}@endif</th>
                                <th>{{ $a->dura }}</th>
                                <th style="width:10%;">{{ $a->clave }}</th>
                                <th>{{ $a->mvalida }}</th>
                                <th>{{ $a->nombre }}</th>    
                                <th>{{ $a->inicio }}</th>                           
                                <th>{{ $a->termino }}</th>                           
                                <th style="width:5%;">{{ $a->efisico }}</th>
                                <th>{{ $a->motivo }}</th>
                                <th>{{ $a->realizo }}</th>                           
                                <th>{{ $a->observaciones }}</th>                           
                            </tr> 
                        @endforeach
                    </tbody>                                               
                </table>
            </div><br><br>
        <table class="tablaf">
            <tr>
                <td> </td><td> </td><td> </td><td> </td>                
                <td align="center"><b>ELABORO</b><br><br><br><br><br><br><br><br><br></td>                    
                <td> </td><td> </td><td> </td><td> </td><td> </td>                
                <td align="center"><b>Vo. Bo.</b><br><br><br><br><br><br><br><br><br></td>                
            </tr>
            <tr>
                <td> </td><td> </td><td> </td><td> </td>                
                <td align="center"><b>{{ $reg_unidad->academico }}</b><br>_____________________________________________________</td>                    
                <td> </td><td> </td><td> </td><td> </td><td> </td>
                <td align="center"><b>{{ $reg_unidad->dunidad }}</b><br>_____________________________________________________</td>                
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
</body>
</html>