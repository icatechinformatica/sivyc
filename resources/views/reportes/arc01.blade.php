<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ARC01</title>
    <style>      
        body{font-family: sans-serif}
        @page {margin: 40px 30px 10px 30px;}
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
        .tablas tr{font-size: 8px; border: gray 1px solid; text-align: center; padding: 1px 1px;}
        .tablas th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 1px 1px;}
        .tablaf { border-collapse: collapse; width: 100%;}     
        .tablaf tr td { font-size: 8px; text-align: center; padding: 0px 0px;}
        .tablad { border-collapse: collapse;}     
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;}     
        .tablag tr td{ font-size: 8px; padding: 0px;}
        .variable{ border-bottom: gray 1px solid;border-left: gray 1px solid;border-right: gray 1px solid}
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
                    <font size=1><b>{{ $nombre_unidad }} {{ $reg_cursos[0]->unidad }} <br/>
                    <font size=1>DEPARTAMENTO ACADEMICO</font><br/>
                    <font size=1>ARC-01</font>                       
                </div><br><br><br>
            </div>
            <table class="tablag">
                <body>
                    <tr>
                        <td><b>PARA: {{ $reg_unidad->dacademico }}, {{$reg_unidad->pdacademico}}</b></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="right"><b>{{ $nombre_unidad }} {{ $reg_cursos[0]->unidad }}</b></td>
                    </tr> 
                    <tr>
                        <td><b>DE: {{ $reg_unidad->dunidad }}, {{$reg_unidad->pdunidad}}</b></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="right"><b>MEMORANDUM NO. {{ $memo_apertura }}</b></td>                        
                    </tr>
                    <tr>
                        <td><b>ASUNTO: SOLICITUD DE APERTURA</b></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="right"><b>{{ $reg_cursos[0]->unidad }},CHIS; {{$fecha_memo }}</b></td>                        
                    </tr>                                                                
                </body>                
            </table><br>
            <div><b>CC. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ARCHIVO MINUTARIO</b></div> <br>
            <div class="table table-responsive">
                <table class="tablas">
                    <thead>                        
                        <tr>
                            <th style="padding: 0px;" rowspan="2">SERVICIO</th>      	  
                            <th style="padding: 0px;" rowspan="2">ESPECIALIDAD</th>   
                            <th style="padding: 0px;" rowspan="2">NOMBRE</th>  
                            <th style="padding: 0px;" rowspan="2">MOD</th>               
                            <th style="padding: 0px;" colspan="2">TIPO<br>DE<br>CAPACITACIÓN</th>       
                            <th style="padding: 0px;" rowspan="2">D<br>U<br>R<br>A</th>         
                            <th style="padding: 0px;" rowspan="2">FECHA DE<br>INICIO</th>  
                            <th style="padding: 0px;" rowspan="2">FECHA DE<br>TERMINO</th>             
                            <th style="padding: 0px;" rowspan="2">HRS<br>DIA<br>RIAS</th>
                            <th style="padding: 0px;" rowspan="2">HORARIO</th>
                            <th style="padding: 0px;" rowspan="2">DIAS</th>
                            <th style="padding: 0px;" rowspan="2">C<br>U<br>P<br>O</th>
                            <th style="padding: 0px;" colspan="2">INSCRITOS</th>       
                            <th style="padding: 0px;" rowspan="2">INSTRUCTOR</th>
                            <th style="padding: 0px;" rowspan="2" >CRITE<br>RIO<br>DE<br>PAGO</th>
                            <th style="padding: 0px;" rowspan="2">MUNICIPIO</th>
                            <th style="padding: 0px;" rowspan="2">ZON<br>A<br>ECO<br>NOM<br>ICA</th>
                            <th style="padding: 0px;" rowspan="2">DEPENDEN<br>CIA<br>BENEFICIA<br>DA</th>
                            <th style="padding: 0px;" colspan="3">TIPO DE CUOTA</th>       
                            <th style="padding: 0px;" rowspan="2">ESPACIO FISICO<br>MEDIO VIRTUAL</th>
                            <th style="padding: 0px;" rowspan="2">OBSERVACIONES</th>
                        </tr>  
                        <tr> 
                            <th >PRES<br>EN</th>                 
                            <th >DISTA<br>NCIA</th> 
                            <th >F<b>E<b>M</th>   
                            <th >MA<b>S<b></th> 
                            <th >ORD</th>
                            <th >EXO</th>  
                            <th >RED</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reg_cursos as $a)
                        <tr>
                            <th>@php if($a->tipo_curso=='CURSO'){echo'CURSO';}if($a->tipo_curso=='CERTIFICACION'){echo'CERTIFICACION EXTRAORDINARIA';} @endphp</th>
                            <th>{{ $a->espe }}</th>
                            <th>{{ $a->curso }}</th>
                            <th>{{ $a->mod }}</th>                           
                            <th>@if($a->tcapacitacion=="PRESENCIAL"){{ "X" }}@endif</th>
                            <th>@if($a->tcapacitacion=="A DISTANCIA"){{ "X" }}@endif</th>
                            <th>{{ $a->dura }}</th>                           
                            <th>{{ $a->inicio }}</th>                           
                            <th>{{ $a->termino }}</th>                           
                            <th>{{ $a->horas }}</th>
                            <th>{{ $a->horario }}</th>                                                      
                            <th>{{ $a->dia }}</th>                           
                            <th>{{ $a->mujer + $a->hombre }}</th> 
                            <th>{{ $a->mujer }}</th>                                                     
                            <th>{{ $a->hombre }}</th>
                            <tH>{{ $a->nombre }}</tH>
                            <th>{{ $a->cp }}</th>                                                                                 
                            <th>{{ $a->muni }}</th>                           
                            <th>{{ $a->ze }}</th>
                            <th>{{ $a->depen }}</th>                           
                            <th>@if($a->tipo=="PINS"){{ "X" }}@endif</th>
                            
                            <th>@if($a->tipo=="EXO"){{ "X" }}@endif</th>
                            <th>@if($a->tipo=="EPAR"){{ "X" }}@endif</th>
                            <th>{{ $a->efisico }}</th>                           
                            <th>{{ $a->nota }}</th>                           
                        </tr>
                        @endforeach
                    </tbody>                                        
                </table>
                <br>
                <div align="left" ><style type="text/css"> BODY{ font-family: sans-serif;font-size:8px } </style><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CRITERIO DE CONTRATACION Y PAGO</b></div><br>
                <table class="tablad">
                    <tr>
                        <td colspan="4"><b>1. PRIMARIA INCONCLUSA 2. PRIMARIA 3. SECUNDARIA 4. BACHILLERATO / PREPARATORIA O CARRERA TECNICA </b></td>
                    </tr> 
                    <tr>
                        <td colspan="4"><b>5. PROFESIONAL TRUNCA 6. PROFESIONAL PASANTE 7. PROFESIONAL(TITULO Y/O CEDULA) 8. MAESTRIA (PASANTE) </b></td>
                    </tr>
                    <tr>
                        <td colspan="4"><b>9. MAESTRIA (TITULO Y/O CEDULA) 10. DOCTORADO (PASANTE) 11. DOCTORADO(TITULO Y/O CEDULA) </b></td>
                    </tr>
                </table><br><br><br><br>
                <table class="tablaf">
                    <tr>
                        <td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="center"><b>SOLICITA</b><br><br><br></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td>                
                        <td align="center"><b>ELABORO</b><br><br><br></td>                    
                        <td> </td><td> </td><td> </td><td> </td><td> </td>                
                        <td align="center"><b>Vo. Bo.</b><br><br><br></td>                
                    </tr>
                    <tr>
                        <td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="center"><b>{{ $reg_unidad->vinculacion }}</b><br>_____________________________________________________</td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="center"><b>{{ $reg_unidad->academico }}</b><br>_____________________________________________________</td>                    
                        <td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="center"><b>{{ $reg_unidad->dunidad }}</b><br>_____________________________________________________</td>                
                        <td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="center"><br><b>SELLO UNIDAD DE<br>CAPACITACION</b></td>                
                    </tr>            
                    <tr>
                        <td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="center"><b>{{ $reg_unidad->pvinculacion }}</b></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="center"><b>{{ $reg_unidad->pacademico }}</b></td>                    
                        <td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="center"><b>{{ $reg_unidad->pdunidad }}</b></td>                
                    </tr>
                </table>
            </div> 
        </div>
    </div>
</body>
</html>