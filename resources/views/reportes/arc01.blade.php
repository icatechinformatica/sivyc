<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ARC01</title>
    <style>      
        body{font-family: sans-serif}
        @page {margin: 40px 20px 10px 20px;}
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
        .tablag tr td { font-size: 8px; padding: 0px;}
    </style>
</head>
<body>
    <div class= "container g-pt-30">
        <div id="content">
            <img class="izquierda" src='img/logohorizontalica1.png'>
            <img class="derecha" src='img/chiapas.png'>
            <div id="wrappertop">
                <div align=center><br> 
                    <font size=1><b>UNIDAD DE CAPACITACION {{ $reg_unidad->unidad }}<br/>
                    <font size=1>DEPARTAMENTO ACADEMICO</font><br/>
                    <font size=1>SOLICITUD DE APERTURA DE CURSO</font><br/>
                    <font size=1>"2021, AÑO DE LA INDEPENDENCIA"</font><br/>                       
                </div><br><br><br>
            </div>
            <table class="tablag">
                <body>
                    <tr>
                        <td><b>PARA: {{ $reg_unidad->dacademico }}</b></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="right"><b>UNIDAD DE CAPACITACION {{ $reg_unidad->unidad }}</b></td>
                    </tr> 
                    <tr>
                        <td><b>DE: {{ $reg_unidad->dunidad }}</b></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="right"><b>MEMORANDUM NO. {{ $memo_apertura }}</b></td>                        
                    </tr>
                    <tr>
                        <td><b>ASUNTO: SOLICITUD DE CURSO</b></td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td><td> </td>
                        <td align="right"><b>{{ $reg_unidad->unidad }},CHIAPAS; {{$fecha_memo }}</b></td>                        
                    </tr>
                    <tr>
                        <td><b>CC. ARCHIVO MINUTARIO</b></td>                        
                    </tr>                                                                
                </body>                
            </table><br> 
            <div class="table table-responsive">
                <table class="tablas">
                    <tbody>
                        <tr>
                            <th> ARC01 </th>
                            <td> </td>
                        </tr>                        
                        <tr>      	  
                            <th rowspan="2">ESPECIALIDAD</th>   
                            <th rowspan="2">NOMBRE <br>DEL<br> CURSO</th>  
                            <th rowspan="2">MOD</th>               
                            <th colspan="2">TIPO<br>DE<br>CURSO</th>       
                            <th rowspan="2">D<br>U<br>R</th>         
                            <th rowspan="2">INICIO</th>  
                            <th rowspan="2">TERMIN<br>O</th>             
                            <th rowspan="2">HOR<br>AS <br>DIA<br>RIAS</th>
                            <th rowspan="2">HORA<br>RIO</th>
                            <th rowspan="2">DIAS</th>
                            <th rowspan="2">CU<br>PO</th>
                            <th colspan="2">INSC<br>RITOS</th>       
                            <th rowspan="2">INSTRUCTOR</th>
                            <th rowspan="2" >CRI<br>TER<br>IO<br>DE<br>PA<br>GO</th>
                            <th rowspan="2">MUNICIPIO</th>
                            <th rowspan="2">ZO<br>NA<br>EC<br>ON<br>OM<br>ICA</th>
                            <th rowspan="2">DEPENDENCIA<br>BENEFICIADA</th>
                            <th colspan="4">TIPO DE CUOTA POR CURSO (MARCAR CON X LA OPCION)</th>       
                            <th rowspan="2">ESPACIO FISICO DONDE SE IMPARTE EL CURSO</th>
                            <th rowspan="2">OBSERVACIONES</th>
                        </tr>  
                        <tr> 
                            <th >PRE<br>SEN<br>CIA<br>L</th>                 
                            <th >A <br>DIS<br>TAN<br>CIA</th> 
                            <th >F<b>E<b>M</th>   
                            <th >MA<b>S<b>C</th> 
                            <th >C. RE<br>CUP</th> 
                            <th >CER</th> 
                            <th >EXO</th>  
                            <th >EXO<br>PAR</th>
                        </tr>
                        @foreach($reg_cursos as $a)         
                            <tr>
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
                                <th>@if($a->tipo=="CERTI"){{ "X" }}@endif</th>
                                <th>@if($a->tipo=="EXO"){{ "X" }}@endif</th>
                                <th>@if($a->tipo=="EPAR"){{ "X" }}@endif</th>
                                <th>{{ $a->efisico }}</th>                           
                                <th>{{ $a->nota }}</th>                           
                            </tr> 
                        @endforeach
                    </tbody>                                               
                </table>
            </div> 
            <div align="left" ><style type="text/css"> BODY{ font-family: sans-serif;font-size:8px } </style><b>CRITERIO DE CONTRATACION Y PAGO</b></div>
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
            </table><br><br>
            <table class="tablaf">
                <tr>
                    <td> </td><td> </td><td> </td><td> </td><td> </td>
                    <td align="center"><b>SOLICITO</b><br><br><br><br><br><br><br><br></td>
                    <td> </td><td> </td><td> </td><td> </td><td> </td>                
                    <td align="center"><b>ELABORO</b><br><br><br><br><br><br><br><br></td>                    
                    <td> </td><td> </td><td> </td><td> </td><td> </td>                
                    <td align="center"><b>Vo. Bo.</b><br><br><br><br><br><br><br><br></td>                
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
</body>
</html>