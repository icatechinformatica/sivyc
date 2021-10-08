<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AUTORIZACIÓN {{$opt}}</title>
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
        .tablas{border-collapse: collapse;width: 990px;}
        .tablas tr{font-size: 7px; border: gray 1px solid; text-align: center; padding: 0px;}
        .tablas th{font-size: 7px; border: gray 1px solid; text-align: center; padding: 0px;}
        .tablaf { border-collapse: collapse; width: 100%;border: gray 1px solid; }     
        .tablaf tr td { font-size: 7px; text-align: center; padding: 0px;}
        .tablad { border-collapse: collapse;font-size: 7px;border: gray 1px solid; text-align: left; padding:0px;} 
        .tablag { border-collapse: collapse; width: 100%; margin-top:10px;}  
        .tablag tr td{ font-size: 8px; padding: 1px;}
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
                    $nombre_unidad= "UNIDAD DE CAPACITACION";              
                @endphp
            @else
                @php
                    $nombre_unidad= "ACCION MOVIL"
                @endphp
            @endif  

            @php 
                $valido= $reg_cursos[0]->valido;               
                $munidad = $reg_cursos[0]->munidad;
                $nmunidad = $reg_cursos[0]->nmunidad;
                $fecha = $asunto = $det = $memo =  $obs = "";
                switch($opt){
                    case 'ARC-01':
                        $fecha = $reg_cursos[0]->fecha_apertura;
                        $memo = $reg_cursos[0]->mvalida;
                        $asunto = "AUTORIZACIÓN DE ASIGNACIÓN DE CLAVES DE APERTURAS";
                        $det = "Por este medio envió a Usted el formato de autorización de asignación de claves de apertura de servicios, en atención a la solicitud con número de memorándum $munidad.";
                        
                        break;
                    case 'ARC-02':
                        $fecha = $reg_cursos[0]->fecha_modificacion; 
                        $memo = $reg_cursos[0]->nmacademico;
                        $asunto = "REPROGRAMACIÓN, MODIFICACIÓN O CANCELACIÓN DE APERTURAS";
                        $det = "Por este medio envió a Usted el formato de autorización de reprogramación, modificación o cancelación de aperturas de servicios, en atención a la solicitud con número de memorándum $nmunidad.";
                       
                    break;
                }                
            @endphp
            <div id="wrappertop">
                <div align=center><br>                     
                    <font size=1><b><i>{{$distintivo}}</i></b></font><br/>          
                    <font size=1><b>AUTORIZACIÓN DE {{$opt}}</b></font>                       
                </div><br><br><br>
            </div>
            <table class="tablag">
                <body>
                    <tr>
                        <td style="width:45px"><b>PARA: </td><td style="width:500px"> <b>{{ $reg_unidad->dunidad }}.- {{$reg_unidad->pdunidad}}</b></td>
                        <td></td>                        
                        <td align="right"><b>DIRECCIÓN TÉCNICA ACADÉMICA</b></td>
                    </tr> 
                    <tr>
                        <td><b>DE: </td><td><b>{{ $reg_unidad->dacademico }}.- {{$reg_unidad->pdacademico}}</b></td>
                        <td> </td>
                        <td align="right"><b>MEMORANDUM NO. {{ $memo }}</b></td>                        
                    </tr>
                    <tr>
                        <td><b>ASUNTO: </td><td><b>{{ $asunto }}</b></td>
                        <td> </td>
                        <td align="right"><b>TUXTLA GUTIÉRREZ, CHIAPAS; {{$fecha}}.</b></td>                        
                    </tr>                                                                
                </body>                
            </table><br>   
            <p><font size='xx-small'>{{ $det }}</font></p>
            <div class="table table-responsive">
                <table class="tablas">
                    <thead>                        
                        <tr>
                            <th style="padding: 0px;" rowspan="2" >SERVICIO</th>  
                            <th style="padding: 0px;" rowspan="2" >UNIDAD DE CAPACITACIÓN</th>    	  
                            <th style="padding: 0px;" rowspan="2" >ESPECIALIDAD</th>   
                            <th style="padding: 0px;" rowspan="2" >NOMBRE</th>
                            <th style="padding: 0px;" rowspan="2" >CLAVE</th>  
                            <th style="padding: 0px;" rowspan="2" >MOD</th>               
                            <th style="padding: 0px;" colspan="2" >TIPO DE<br>CAPACI<BR/>TACIÓN</th>       
                            <th style="padding: 0px;" rowspan="2" >D<br>U<br>R<br>A</th>         
                            <th style="padding: 0px;" rowspan="2" >FECHA DE<br>INICIO</th>  
                            <th style="padding: 0px;" rowspan="2" >FECHA DE<br>TÉRMINO</th>
                            <th style="padding: 0px;" rowspan="2" >HORARIO</th>
                            <th style="padding: 0px;" rowspan="2" >DIAS</th>
                            <th style="padding: 0px;" rowspan="2" >C<br>U<br>P<br>O</th>     
                            <th style="padding: 0px;" rowspan="2" >INSTRUCTOR</th>
                            <th style="padding: 0px;" rowspan="2" >CRITE<br>RIO<br>DE<br>PAGO</th>
                            <th style="padding: 0px;" rowspan="2" >MUNICIPIO</th>
                            <th style="padding: 0px;" rowspan="2" >ESPACIO FISICO<br>MEDIO VIRTUAL</th>
                            <th style="padding: 0px;" rowspan="2" >ZON<br>A<br>ECO<br>NOM<br>ICA</th>
                            <th style="padding: 0px;" colspan="3" >TIPO DE <BR/> CUOTA</th>                            
                            <th style="padding: 0px;" rowspan="2" width='15%'>OBSERVACIONES</th>
                        </tr>  
                        <tr> 
                            <th >PRES<br>EN</th>                 
                            <th >DISTA<br>NCIA</th>                             
                            <th >ORD</th>
                            <th >EXO</th>  
                            <th >RED</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reg_cursos as $a)
                        <tr>
                            <th>@php if($a->tipo_curso=='CURSO'){echo'CURSO';}if($a->tipo_curso=='CERTIFICACION'){echo'CERTIFICACION EXTRAORDINARIA';} @endphp</th>
                            <th>{{$nombre_unidad}} {{ $a->unidad }}</th>
                            <th>{{ $a->espe }}</th>                            
                            <th>{{ $a->curso }}</th>
                            <th>{{ $a->clave }}</th>
                            <th>{{ $a->mod }}</th>                           
                            <th>@if($a->tcapacitacion=="PRESENCIAL"){{ "X" }}@endif</th>
                            <th>@if($a->tcapacitacion=="A DISTANCIA"){{ "X" }}@endif</th>
                            <th>{{ $a->dura }}</th>                           
                            <th>{{ $a->inicio }}</th>                           
                            <th>{{ $a->termino }}</th> 
                            <th>{{ $a->horario }}</th>                                                      
                            <th>{{ $a->dia }}</th>                           
                            <th>{{ $a->mujer + $a->hombre }}</th> 
                            <tH>{{ $a->nombre }}</tH>
                            <th>{{ $a->cp }}</th>                                                                                 
                            <th>{{ $a->muni }}</th>
                            <th>{{ $a->efisico }}</th>                          
                            <th>{{ $a->ze }}</th>                          
                            <th>@if($a->tipo=="PINS"){{ "X" }}@endif</th>                            
                            <th>@if($a->tipo=="EXO"){{ "X" }}@endif</th>
                            <th>@if($a->tipo=="EPAR"){{ "X" }}@endif</th>                                                      
                            <th>@if($opt == "ARC-01"){{ $a->nota }} @else {{ $a->observaciones}}@endif</th>                           
                        </tr>
                        @endforeach
                    </tbody>                                        
                </table>
                <br>
                <div align="left" ><style type="text/css"> BODY{ font-family: sans-serif;font-size:7px } </style><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CRITERIO DE CONTRATACION Y PAGO</b></div><br>
                <table class="tablad">
                    <tr>
                        <td colspan="4"><b>1. PRIMARIA INCONCLUSA 2. PRIMARIA 3. SECUNDARIA 4. BACHILLERATO / PREPARATORIA O CARRERA TÉCNICA </b></td>
                    </tr> 
                    <tr>
                        <td colspan="4"><b>5. PROFESIONAL TRUNCA 6. PROFESIONAL PASANTE 7. PROFESIONAL(TÍTULO Y/O CÉDULA) 8. MAESTRÍA (PASANTE) </b></td>
                    </tr>
                    <tr>
                        <td colspan="4"><b>9. MAESTRÍA (TÍTULO Y/O CÉDULA) 10. DOCTORADO (PASANTE) 11. DOCTORADO(TÍTULO Y/O CÉDULA) </b></td>
                    </tr>
                </table><br>
                <table style='with:100%; border-spacing: 0px;' >                    
                    <tr>
                        <td align="center" style='padding:50px 0 2px 0;  text-alingn:center; width:240px;border: gray 1px solid;'>
                            <b>{{ $realizo }}</b><br/>
                            ________________________________________________<br/><br/>
                            <b>{{ $puesto }} </b><br/><br/><br/>
                            <b>ELABORÓ</b>
                        </td>
                        <td>&nbsp; &nbsp; </td>
                        <td align="center" style='padding:50px 0 2px 0; text-alingn:center; width:240px;border: gray 1px solid;'>
                            <b>{{ $reg_unidad->jcyc }}</b><br/>
                            ________________________________________________<br/><br/>
                            <b>{{ $reg_unidad->pjcyc }} </b><br/><br/><br/>
                            <b>REVISÓ</b>
                        </td>
                        <td>&nbsp; &nbsp; </td>
                        <td align="center" style='padding:50px 0 2px 0; text-alingn:center; width:240px;border: gray 1px solid;'>
                            <b>{{ $reg_unidad->dacademico }}</b>
                            ________________________________________________<br/><br/>
                            <b>{{ $reg_unidad->pdacademico}} </b><br/><br/><br/>
                            <b>AUTORIZÓ</b>
                        </td>
                        <td>&nbsp; &nbsp; </td>     
                        <td align="center" style='padding-top:100px; text-alingn:center; width:230px;border: gray 1px solid;'><b>SELLO DE LA DIRECCIÓN</b></td>                                 
                    </tr>                    
                </table>
                <p>CCP. {{ $reg_unidad->academico}}.-{{ $reg_unidad->pacademico}} DE LA UNIDAD DE CAPACITACIÓN {{ $reg_unidad->ubicacion}}.<br/>
                ARCHIVO/MINUTARIO<BR/>
                </p>
            </div> 
        </div>
    </div>
</body>
</html>