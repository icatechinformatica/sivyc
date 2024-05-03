<?php
$total=$consulta[0]->total;
$fecha=date("Y-m-d ");
$mes= date("m");
$periodo="0";
switch( $mes){
    case($mes <="3"):
    $periodo=3;
    break;
    case($mes<="6"):
    $periodo=4;
    break;
    case($mes<="9"):
    $periodo=1;
    break;
    case($mes<=12):
    $periodo=2;
    break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte RDCD-08</title>
</head>
<style type="text/ccs">
    body {  font-family: sans-serif;}
    @page {  margin: 80px 25px 140px 25px; }
    header {  position: fixed; left: 0px; top: -50px; right: 0px; text-align: center; }
    header h6 { height: 14px; padding: 0px; margin: 0; font-size: 11px;} 
    main {padding: 0; margin: 0; margin-top: 15px; }        
    footer { position:fixed;   left:0px;   bottom:-100px;   height:90px;   width:100%;}       
    .tablaf { border-collapse: collapse; width: 100%;}
    .tablaf tr td { font-size: 10px; padding: 3px;  text-align:center; height:90px;}

    .tabla_madre{border: 1px solid black; font-size: 11px; }
    .table1 { width: 100%; border-collapse: collapse; margin:6px 0px 6px 15px;}
    .table1 tr td { margin:0px; padding: 0px; line-height: 12px;}
    .table1 tr td p{ margin:0px; padding: 0px; line-height: 10px;}

    .table{width: 100%; text-align: center; border-collapse: collapse; }
    .table tr th { margin:0px; padding: 5px; line-height: 12px; font-size: 10px;}
    .table tr td { margin:0px; padding: 5px; line-height: 12px; font-size: 11px;}
    .centrado{width:50%; padding:3px; margin:auto; text-align:center; font-size: 11px;  font-weight: bold;}    
    .p{text-decoration: overline;}
    
    .div_madre{border: 1px solid black; height:650px; }

    .variable{text-align: center;border: 1px solid black;}
    img.izquierda {
        float: left;
        width: 18%;        
      }
</style>
<body>
    <header>
        <img src="img/reportes/sep.png" alt='sep' width="20%" style='position:fixed; left:0; margin: -50px 0px;' />
        <h6>SUBSECRETAR&Iacute;A DE EDUCACI&Oacute;N MEDIA SUPERIOR</h6>
        <h6>DIRECCI&Oacute;N GENERAL DE CENTROS DE FORMACI&Oacute;N PARA EL TRABAJO</h6>
        <h6>REPORTE DE DIPLOMAS O CONSTANCIAS EXPEDIDOS</h6>
        <h6>(RDCD-08)</h6>
    </header>    
    <footer>
            <table class="tablaf"  width="100%">
                <tr>
                    <td style="width: 60%">
                        <p>{{$cct->dunidad}}</p>
                        <hr style="width: 70%">
                        <p>{{$cct->pdunidad}} {{$cct->ubicacion}}</p>
                    </td>
                    <td style="width: 10%">&nbsp;</td>
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
                    <td colspan="3" style="text-align: center;">
                        INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TÉCNOLOGICO DEL ESTADO DE CHIAPAS
                    </td>                                    
                </tr>    
                <tr>
                    <td rowspan="2" style="width:65%;" >   <br/>                      
                        <p><b>{{ (substr($cct->cct, 0, 5) == "07EIC") ? "UNIDAD DE CAPACITACIÓN" : "CENTRO DE TRABAJO ACCIÓN MÓVIL" }}:</b>
                            &nbsp;&nbsp;{{$cct->plantel}}&nbsp;&nbsp;{{$cct->unidad}}</p> <br/>
                        <p><b>CLAVE CCT:</b> &nbsp;&nbsp;{{$cct->cct}} </p><br/>
                        <p><b>PERIODO QUE SE REPORTA:</b>&nbsp;&nbsp; {{$periodo}} <p> <br/>
                        <p><b>FECHA:</b> &nbsp;&nbsp; {{$fecha}} </p>
                    </td>                    
                    <td colspan="2" style="text-align: center;"><b>TIPO DE DOCUMENTO</b></td>
                </tr>               
                <tr>
                    <td  style="width:12%;  vertical-align: top;">                        
                        <b>CONSTANCIA:</b>                        
                    </td>
                    <td>                            
                        <p>CAE ({{ $modalidad=='CAE' ? 'X' : ' ' }}) <br/></p>
                        <p  style="padding-top:6px;">EXTENSION ({{ $modalidad=='EXT' ? 'X' : ' ' }})<br/></p>
                        <p  style="padding-top:6px;">GRAL ({{ $modalidad=='GRAL' ? 'X' : ' ' }})</p>
                    </td>
                </tr>           
            </table>
        </div>   
        
        <div><p class="centrado">SERIE ASIGNADA A LA UNIDAD DE CAPACITACIÓN</p></div>
        <div>
            <table class="table">
                <tr>
                    <td class="variable">DEL FOLIO:</td>
                    <td class="variable">AL FOLIO:</td>
                    <td class="variable">CANTIDAD</td>
                    <td class="variable">FECHA DEL ACTA ADMVA. DE<br/> ASIGNACIÓN:</td>
                </tr>
                <tr>
                    <td class="variable">{{$consulta[0]->finicial}}</td>
                    <td class="variable">{{$consulta[0]->ffinal}}</td>
                    <td class="variable">{{$consulta[0]->total}}</td>
                    <td class="variable">{{$consulta[0]->facta}}</td>
                </tr>
            </table>
        </div>
        <div><p class="centrado">CONSTANCIAS UTILIZADOS</p></div>
        <div class="div_madre">
            <table class="table">
                <tr>
                    <td colspan="2" class="variable">FOLIOS</td>
                    <td rowspan="2" class="variable">FECHA</td>
                    <td rowspan="2" class="variable">CANTIDAD<br/> EXPEDIDA</td>
                    <td rowspan="2" class="variable">CANTIDAD<br/> CANCELADA</td>
                    <td rowspan="2" class="variable">CANTIDAD<br/> EXISTENTE</td>
                </tr>
                <tr>
                    <td class="variable">DEL</td>
                    <td class="variable">AL</td>
                </tr>
                @foreach($cuerpo as $key=>$item)
                <tr>
                    <td class="variable">{{$item->mini}}</td>
                    <td class="variable">{{$item->maxi}}</td>
                    <td class="variable">{{$item->fecha_expedicion}}</td>
                    <td class="variable">{{$item->expedidos}}</td>
                    <td class="variable">{{$item->cancelados}}</td>
                    <td class="variable">@php $total= $total- ($item->expedidos + $item->cancelados); @endphp {{$total}}</td>
                </tr>
                @endforeach
                {{-- @if (count($fcancelados)>0)
                <tr>
                    <td colspan="6" class="variable">
                        @foreach($fcancelados as $alo)FOLIOS CANCELADOS: {{$alo->cance}} POR {{$alo->motivo}},@endforeach
                </td>
            </tr>
            @endif --}}
        </table>
        @if (count($fcancelados)>0)
            <div style="margin-top: 50px; border: 1px solid #000;">
            
                    @foreach($fcancelados as $alo)FOLIOS CANCELADOS: {{$alo->cance}} POR {{$alo->motivo}},@endforeach
            
            </div>
        @endif
    </div>    
    </main>
</body>
</html>
