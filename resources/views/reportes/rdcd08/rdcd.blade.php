<?php
$total=$consulta[0]->total;
$fecha=date("Y-m-d ");
$mes= date("m");
$periodo="0";
switch( $mes){ 
    case($mes <="3"):
    $periodo=1;
    break;
    case($mes<="6"):
    $periodo=2;
    break;
    case($mes<="9"):
    $periodo=3;
    break;
    case($mes<=12):
    $periodo=4;
    break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte RCDC08</title>
</head>
<style type="text/ccs">
    @page{margin: 20px 30px 40px}
    .tabla_madre{border: 1px solid black;}
    .table{width: 100%; text-align: center; border-collapse: collapse;}
    .centrado{width:50%;padding:8px;margin:auto; text-align:center;}
    div{font_size: 12px;}
    .p{text-decoration: overline;}
    .variable{text-align: center;border: 1px solid black;}
    img.izquierda {
        float: left;
        width: 120;
        height: 50;
      }
</style>
<body>
    <div>
        <img src="img/sep.png" class="izquierda">
        <p style="font_size: 14px;width:50%;padding:8px;margin:auto; text-align:center;">SUBSECRETARIA DE EDUCACIÓN E INVESTIGACIÓN TECNOLÓGICAS <br>DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO <br>REPORTE DE DIPLOMAS O COSTANCIAS EXPEDIDOS <br>(RDCD-08)</p>
    </div>
    <div class="tabla_madre">
        <div class="centrado"><p>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNILÓGICA DEL ESTADO DE CHIAPAS</p></div>
        <div >
            <table width="100%">
                <tr><td></td><td align='right'>TIPO DE DOCUMENTO</td><td></td></tr>
                <tr>
                    <td>UNIDAD DE CAPACITACIÓN: {{$unidad}}</td>
                    <td align='right'>DIPLOMA</td>
                    <td align='right'>CURSO( )</td>  
                </tr>
                <tr>
                    <td>CLAVE CCT: {{$cct->cct}}</td>
                    <td></td>
                    <td align='right'>ESPECIALIDAD( )</td>
                </tr>
                <tr>
                    <td>PERIODO QUE SE REPORTA:{{$periodo}}</td>
                    <td></td>
                    <td align='right'>R.O.C.O.( )</td>
                </tr>
                <tr>
                    <td>FECHA: {{$fecha}}</td>
                    <td align='right'>CONSTANCIA</td>
                    <td align='right'>@php if($modalidad=="CAE"){echo "CAE(X)";} else {echo"CAE( )";} @endphp</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align='right'>@php if($modalidad=="EXT"){echo "EXT(X)";} else {echo"EXT( )";} @endphp</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align='right'>@php if($modalidad=="GRAL"){echo "GRAL(X)";} else {echo"GRAL( )";} @endphp</td>
                </tr>
            </table>
        </div>
    </div>
    <div><p class="centrado">SERIE ASIGNADA A LA UNIDAD</p></div>
    <div>
        <table class="table">
            <tr>
                <td class="variable">DEL FOLIO:</td>
                <td class="variable">AL FOLIO:</td>
                <td class="variable">CANTIDAD</td>
                <td class="variable">FECHA DEL ACTA ADMVA. DE ASIGANCIÓN:</td>
            </tr>
            <tr>
                <td class="variable">{{$consulta[0]->finicial}}</td>
                <td class="variable">{{$consulta[0]->ffinal}}</td>
                <td class="variable">{{$consulta[0]->total}}</td>
                <td class="variable">{{$consulta[0]->facta}}</td>
            </tr>
        </table>
    </div>
    <div><p class="centrado">DIPLOMAS UTILIZADOS</p></div>
    <div>
        <table class="table">
            <tr>
                <td colspan="2" class="variable">FOLIOS</td>
                <td rowspan="2" class="variable">FECHA</td>
                <td rowspan="2" class="variable">CANTIDAD EXPEDIDA</td>
                <td rowspan="2" class="variable">CANTIDAD CANCELADA</td>
                <td rowspan="2" class="variable">CANTIDAD EXISTENTE</td>
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
            
            <tr>
                 <td colspan="6" class="variable">@foreach($fcancelados as $alo)FOLIOS CANCELADOS: {{$alo->cance}} POR {{$alo->motivo}},@endforeach</td>
            </tr>
            
        </table>
    </div>
    <div><br><br><br><br><br><br><br><br><br><br></div>
    <div>
        <table class="table">
            <tr>
                <td style="align: left"> {{$cct->dunidad}} <br><p class="p"> NOMBRE Y FIRMA DEL DIRECTOR(A) DE LA UNIDAD {{$unidad}}</p></td>
                <td><p class="p">SELLO</p></td>
            </tr>
        </table>
    </div>
    
</body>
</html>