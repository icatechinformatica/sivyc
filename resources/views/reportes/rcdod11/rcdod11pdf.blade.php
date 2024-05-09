<?php
$fecha= date("Y-m-d");
$i=1;
$ciclo=date("Y");
$anioAnterior = date('Y', strtotime('-1 year')) ;
$anioSi = date('Y', strtotime('+1 year')) ;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>RCDOD-11</title>

    <style type="text/ccs">
        body {  font-family: sans-serif;}
        @page {  margin: 90px 25px 140px 25px; }    
        header {  position: fixed; left: 0px; top: -60px; right: 0px; text-align: center; }
        header h6 { height: 14px; padding: 0px; margin: 0; font-size: 12px;}    
        img.izquierda { float: left; width: 100; height: 40; } 

        .tabla_madre{border: 1px solid black; font-size: 12px; padding: 6px 0px 5px 11px; margin-bottom:3px;}
        .table1 { width: 100%; border-collapse: collapse; }
        .table1 tr td { font-size: 11px; margin:0px; padding: 0px; line-height: 8px;}
        .table1 tr td p { font-size: 11px; padding: 0px; margin:0px; line-height: 10px;}

        .table {width: 100%; border-collapse: collapse;}
        .table tr th { border: 1px solid black; font-size: 11px; padding: 0px; margin:0px; line-height: 13px; font-size: 10px;}
        .table tr td { border: 1px solid black; font-size: 11px; padding: 5px; margin:0px; line-height: 25px;}
        .center{ text-align:center; }

        main {padding: 0; margin: 0; margin-top: 0px; }        
        footer { position:fixed;   left:0px;   bottom:-100px;   height:90px;   width:100%;}       
        .tablaf { border-collapse: collapse; width: 100%;}
        .tablaf tr td { font-size: 10px; padding: 3px;  text-align:center; height:90px;}

        
    </style>
</head>
<body>
    <header>
        <img src="img/reportes/sep.png" alt='sep' width="16%" style='position:fixed; left:0; margin: -60px 0 0 20px;' />
        <h6>SUBSECRETAR&Iacute;A DE EDUCACI&Oacute;N MEDIA SUPERIOR</h6>
        <h6>DIRECCI&Oacute;N GENERAL DE CENTROS DE FORMACI&Oacute;N PARA EL TRABAJO</h6>
        <h6>REGISTRO Y CONTROL DE DUPLICADOS OTORGADOS</h6>
        <h6>(RCDOD-11)</h6>
    </header>    
    <footer>
            <table class="tablaf"  width="100%">
                <tr>
                    <td style="width: 40%">
                        <p>{{$sq->dunidad}}</p>
                        <hr style="width: 70%">
                        <p>{{$sq->pdunidad}}  {{$sq->ubicacion}}</p>
                    </td>
                    <td style="width: 30%">&nbsp;</td>
                    <td style="width: 30%">
                        <hr style="width: 60%">
                        SELLO
                    </td>
                </tr>
           </table>
     </footer>
     <main> 
        <div class="tabla_madre">
            <table class="table1">  
                <tr>
                    <td rowspan="2" style="width:75%;" ><br/>
                        <p><b>INSTITUTO DESCENTRALIZADO:</b> &nbsp;&nbsp;INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TÉCNOLOGICO DEL ESTADO DE CHIAPAS</p><br/>
                        <p><b>{{ (substr($sq->cct, 0, 5) == "07EIC") ? "UNIDAD DE CAPACITACIÓN" : "CENTRO DE TRABAJO ACCIÓN MÓVIL" }}:</b> &nbsp;&nbsp;{{$sq->plantel}}&nbsp;&nbsp;{{$sq->unidad}}</p><br/>
                        <p><b>CLAVE CCT:</b> &nbsp;&nbsp;{{$sq->cct}} <b style="margin-left:100px;">PERIODO:</b>&nbsp;&nbsp; {{$periodo}}
                        <b style="margin-left:100px;">FECHA:</b> &nbsp;&nbsp; {{$fecha}} </p><br/>
                    </td>                    
                    <td colspan="2" style="text-align: center;"><b>TIPO DE DOCUMENTO</b></td>
                </tr>               
                <tr>
                    <td  style="width:9%;  vertical-align: top;">                        
                        <b>CONSTANCIA:</b>                        
                    </td>
                    <td>
                        CAE ({{ $consulta[0]->mod=='CAE' ? 'X' : ' ' }}) <br/><br/> EXTENSION ({{ $consulta[0]->mod=='EXT' ? 'X' : ' ' }})
                    </td>
                </tr>           
            </table>
        </div>    
        <div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 1%">N<br/>U<br/>M</th>
                        <th style="width: 5%">NÚMERO DE<br/>CONTROL</th>
                        <th style="width: 27%">NOMBRE DEL CAPACITADO<br/>PRIMER APELLIDO/SEGUNDO APELLIDO/NOMBRE(S)</th>
                        <th style="width: 23%" >ESPECIALIDAD</th>
                        <th style="width: 7%">FOLIO DEL<br/>DIPLOMA O<br/>CONSTANCIA</th>
                        <th style="width: 7%">FOLIO DEL DUPLICADO</th>
                        <th>FECHA DE<br/>RECIBIDO</th>
                        <th style="width: 16%">FIRMA DEL <br/>CAPACITADO</th>
                    </tr>
                </thead>           
                @foreach($consulta as $item)
                <tr>
                    <td>{{$i++}}</td>
                    <td>{{$item->matricula}}</td>
                    <td>{{$item->alumno}}</td>
                    <td >{{$item->espe}}</td>
                    <td class="center">{{$item->folio}}</td>
                    <td class="center">{{$item->duplicado}}</td>
                    <td></td>
                    <td></td>
                </tr>
                @endforeach
            </table>
        </div>
    </main>
</body>
</html>