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
</head>
<style type="text/ccs">
    .tabla_madre{border: 1px solid black;}
    .table{width: 100%; text-align: center; border-collapse: collapse;}
    .centrado{width:50%;padding:8px;margin:auto; text-align:center;}
    div{font_size: 12px;font-weight: bold;}
    .p{text-decoration: overline;}
    .variable{text-align: center;border: 1px solid black; HEIGHT: 5%;}
    .variable2{text-align: left;border: 1px solid black;}
    img.izquierda {
        float: left;
        width: 100;
        height: 40;
      }
      
    img.derecha {
        float: right;
        width: 100;
        height: 40;
      }
</style>
<body>
    <div>
        <div><img src="img/sep3.png" class="izquierda"></div>
        <div><img src="img/gobLogo.jpg" class="derecha"></div>
        <p class="centrado" style="font_size: 14px">SUBSECRETARIA DE EDUCACIÓN MEDIA SUPERIOR <br> DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO <br> REGISTRO Y CONTROL DE DUPLICADOS OTORGADOS <br> (RCDOD-11)</p>
    </div>
    <div class="tabla_madre">
        <table class="table">
            <thead>
                <tr><td>INSTITUTO: CHIAPAS</td><td></td><td>TIPO DE DOCUMENTO</td></tr>
            <tr><td>UNIDAD DE CAPACITACIÓN PARA EL TRABAJO INDUSTRIAL NÚMERO: {{$sq[0]->plantel}}</td><td></td><td>DIPLOMAS: CURSOS Y/O ESPECIALIDAD( )</td></tr>
            <tr><td></td><td></td><td>CONSTANCIAS: CAE Y/O EXTENSIÓN (X)</td></tr>
            <tr><td>CLAVE CCT: {{$sq[0]->cct}}</td><td>CICLO QUE SE REPORTA:@if(date("m")>="01"&&date("m")<="07"){{$anioAnterior}}-{{$ciclo}} @else {{$ciclo}}-{{$anioSi}}@endif</td><td>FECHA: {{$fecha}}</td></tr>
            </thead>
            <tbody> 
            </tbody>
        </table>
    </div>
    <br>
    <div>
        <table class="table">
            <tr>
                <td class="variable">NUM</td>
                <td class="variable">NÚMERO DE CONTROL</td>
                <td class="variable" style="width: 35%"><P>NOMBRE DEL ALUMNO</P><P>PRIMER APELLIDO/SEGUNDO APELLIDO/NOMBRE(S)</P></td>
                <td class="variable">FOLIO DEL DIPLOMA</td>
                <td class="variable">FOLIO DEL DUPLICADO</td>
                <td class="variable">FECHA DE RECIBIDO</td>
                <td class="variable" style="width: 15%">FIRMA DEL ALUMNO</td>
            </tr>
            @foreach($consulta as $item)
            <tr>
                <td class="variable">{{$i++}}</td>
                <td class="variable2">{{$item->matricula}}</td>
                <td class="variable2">{{$item->nombre}}</td>
                <td class="variable">{{$item->folio}}</td>
                <td class="variable">{{$item->duplicado}}</td>
                <td class="variable"></td>
                <td class="variable"></td>
            </tr>
            @endforeach
        </table>
    </div>
    <br><br><br><br><br><br><br><br><br><br>
    <div>
        <table class="table">
            <tr>
                <td style="align: left">{{$sq[0]->dunidad}} <br> <p class="p"> NOMBRE Y FIRMA DEL DIRECTOR DE LA UNIDAD</p></td>
                <td><p class="p">SELLO</p></td>
            </tr>
        </table>
    </div>
</body>
</html>