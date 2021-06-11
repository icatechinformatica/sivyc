<?php
$a=1;
$value=0; $value1=0; $value2=0; $value3=0; $value4=0; $value5=0; $value6=0; $value7=0; $value8=0;
$sum1=0; $sum2=0; $sum3=0; $sum4=0; $sum5=0; $sum6=0; $sum7=0; $sum8=0; $sum9=0;
$act1=0; $act2=0; $act3=0; $act4=0; $act5=0; $act6=0; $act7=0; $act8=0; $act9=0;
$im1=0; $im2=0; $im3=0; $im4=0; $im5=0; $im6=0; $im7=0; $im8=0; $im9=0;
$em1=0; $em2=0; $em3=0; $em4=0; $em5=0; $em6=0; $em7=0; $em8=0; $em9=0;
$gpo=0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AM {{$unidades}}.pdf
    </title>
    <link rel="stylesheet" href="">
</head>
<style type="text/css">
    .tabla-madre{
        border: 1px solid gray;
        
    }
    .variable{
        
        border: 1px solid gray;
        text-align: center
    }
    .variable2{text-align: center}
    .variable3{
        width: 95%;
        border-spacing:10px 0px;
    }
    .variable4{text-align:center; border-bottom-style: solid; border-width: 1px;}
    .variable5{border-spacing:0px 0px;}

</style>
<body>
    <div class="tabla-madre" style="font_size: 12px">
        <div class='variable3'>
            <h2>1.ALUMNOS POR ESPECIALIDAD</h2>
        <P>1.Escriba por cada especialidad impartida de a {{$fecha_inicio}} a {{$fecha_termino}} , el número progresivo, la clave de especialidad, nombre, grupos, el total de alumnos, desglosándolos por sexo, inscripcion total, existencia,
        acreditados y rango de edad.</P>
        <p>2.Verifique que las sumas de los alumnos por rango de edad sean igual al total.</p>
        <br>
        </div>
        <div class='table' class='variable3'>
            <table align='right'>
                <tr>
                    <td>TURNO:</td>
                    <td>{{$turno}}</td>
                </tr>
            </table>
        <br>
        </div>
        <br>
        @foreach($encabezado as $key=>$item)
        <div class='table'>
            <table class='variable3'>
                <tr>
                    <td>NÚMERO PROGRESIVO</td>
                    <td class='variable'>{{$a++}}</td>
                    <td>CLAVE DE LA ESPECIALIDAD</td>
                    <td class='variable'>{{$item->clave}}</td>
                    <td>NOMBRE DE LA ESPECIALIDAD</td>
                    <td class='variable4'>{{$item->especialidad}}</td>
                    <td>GRUPOS</td>
                    <td class='variable'>{{$item->count}}</td>
                </tr>
                <br>
            </table>
            <table class='variable3'>
               
                <tr>
                    <td class='variable5'></td>
                    <td class='variable5'></td>
                    <td class='variable2'>MENOS DE 15 AÑOS</td>
                    <td class='variable2'>15-19 AÑOS</td>
                    <td class='variable2'>20-24 AÑOS</td>
                    <td class='variable2'>25-34 AÑOS</td>
                    <td class='variable2'>35-44 AÑOS</td>
                    <td class='variable2'>45-54 AÑOS</td>
                    <td class='variable2'>55-64 AÑOS</td>
                    <td class='variable2'>65 AÑOS Y MÁS</td>
                    <td class='variable2'>TOTAL</td>
                </tr>
                <tr>
                    <td class='variable5'></td>
                    <td class='variable5'>INSCRIPCIÓN TOTAL</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos1}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos2}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos3}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos4}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos5}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos6}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos7}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos8}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos}}</td>
                </tr>
                <tr>
                    <td class='variable5'>HOMBRES</td>
                    <td class='variable5'>EXISTENCIA</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insh1}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insh2}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insh3}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insh4}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insh5}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insh6}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insh7}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insh8}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insh9}}</td>
                </tr>
                <tr>
                    <td class='variable5'></td>
                    <td class='variable5'>ACREDITADOS</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacreh1}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacreh2}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacreh3}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacreh4}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacreh5}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacreh6}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacreh7}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacreh8}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacreh9}}</td>
                </tr>
                <tr>
                    <td class='variable5'></td>
                    <td class='variable5'>INSCRIPCIÓN TOTAL</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos1}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos2}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos3}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos4}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos5}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos6}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos7}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos8}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->total_inscritos}}</td>
                    
                </tr>
                <tr>
                    <td class='variable5'>MUJERES</td>
                    <td class='variable5'>EXISTENCIA</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insm1}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insm2}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insm3}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insm4}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insm5}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insm6}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insm7}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insm8}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->insm9}}</td>
                </tr>
                <tr>
                    <td class='variable5'></td>
                    <td class='variable5'>ACREDITADOS</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacrem1}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacrem2}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacrem3}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacrem4}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacrem5}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacrem6}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacrem7}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacrem8}}</td>
                    <td class='variable'>{{$consulta_inscritos[$key]->iacrem9}}</td>
                </tr>
                <br>
            </table>
        </div>
        @endforeach
    </div>
    <div class='variable3'> <h2 class="variable2">CONCENTRADO TOTAL</h2> <br></div>
    <div class="tabla-madre" style="font_size: 12px">
        <div class='table'>
            <table>
                <tr>
                    <td>TOTAL  DE GRUPOS: </td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $gpo += $item->count; @endphp@endforeach{{$gpo}}</td>
                </tr>
            </table>
            <br>
        </div>
        <div class='table'>
            <table class='variable3'>
                <tr>
                    <td></td>
                    <td></td>
                    <td class='variable2'>MENOS DE 15 AÑOS</td>
                    <td class='variable2'>15-19 AÑOS</td>
                    <td class='variable2'>20-24 AÑOS</td>
                    <td class='variable2'>25-34 AÑOS</td>
                    <td class='variable2'>35-44 AÑOS</td>
                    <td class='variable2'>45-54 AÑOS</td>
                    <td class='variable2'>55-64 AÑOS</td>
                    <td class='variable2'>65 AÑOS Y MÁS</td>
                    <td class='variable2'>TOTAL</td>
                </tr>
                <tr>
                    <td></td>
                    <td>INSCRIPCIÓN TOTAL</td> 
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $value1 += $consulta_inscritos[$key]->total_inscritos1; @endphp@endforeach{{$value1}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $value2 += $consulta_inscritos[$key]->total_inscritos2; @endphp@endforeach{{$value2}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $value3 += $consulta_inscritos[$key]->total_inscritos3; @endphp@endforeach{{$value3}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $value4 += $consulta_inscritos[$key]->total_inscritos4; @endphp@endforeach{{$value4}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $value5 += $consulta_inscritos[$key]->total_inscritos5; @endphp@endforeach{{$value5}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $value6 += $consulta_inscritos[$key]->total_inscritos6; @endphp@endforeach{{$value6}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $value7 += $consulta_inscritos[$key]->total_inscritos7; @endphp@endforeach{{$value7}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $value8 += $consulta_inscritos[$key]->total_inscritos8; @endphp@endforeach{{$value8}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $value += $consulta_inscritos[$key]->total_inscritos; @endphp@endforeach{{$value}}</td>
                </tr>
                <tr>
                    <td>HOMBRES</td>
                    <td>EXISTENCIA</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $sum1 += $consulta_inscritos[$key]->insh1; @endphp@endforeach{{$sum1}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $sum2 += $consulta_inscritos[$key]->insh2; @endphp@endforeach{{$sum2}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $sum3 += $consulta_inscritos[$key]->insh3; @endphp@endforeach{{$sum3}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $sum4 += $consulta_inscritos[$key]->insh4; @endphp@endforeach{{$sum4}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $sum5 += $consulta_inscritos[$key]->insh5; @endphp@endforeach{{$sum5}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $sum6 += $consulta_inscritos[$key]->insh6; @endphp@endforeach{{$sum6}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $sum7 += $consulta_inscritos[$key]->insh7; @endphp@endforeach{{$sum7}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $sum8 += $consulta_inscritos[$key]->insh8; @endphp@endforeach{{$sum8}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $sum9 += $consulta_inscritos[$key]->insh9; @endphp@endforeach{{$sum9}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>ACREDITADOS</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $act1 += $consulta_inscritos[$key]->iacreh1; @endphp@endforeach{{$act1}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $act2 += $consulta_inscritos[$key]->iacreh2; @endphp@endforeach{{$act2}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $act3 += $consulta_inscritos[$key]->iacreh3; @endphp@endforeach{{$act3}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $act4 += $consulta_inscritos[$key]->iacreh4; @endphp@endforeach{{$act4}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $act5 += $consulta_inscritos[$key]->iacreh5; @endphp@endforeach{{$act5}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $act6 += $consulta_inscritos[$key]->iacreh6; @endphp@endforeach{{$act6}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $act7 += $consulta_inscritos[$key]->iacreh7; @endphp@endforeach{{$act7}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $act8 += $consulta_inscritos[$key]->iacreh8; @endphp@endforeach{{$act8}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $act9 += $consulta_inscritos[$key]->iacreh9; @endphp@endforeach{{$act9}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>INSCRIPCIÓN TOTAL</td>
                    <td class='variable'>{{$value1}}</td>
                    <td class='variable'>{{$value2}}</td>
                    <td class='variable'>{{$value3}}</td>
                    <td class='variable'>{{$value4}}</td>
                    <td class='variable'>{{$value5}}</td>
                    <td class='variable'>{{$value6}}</td>
                    <td class='variable'>{{$value7}}</td>
                    <td class='variable'>{{$value8}}</td>
                    <td class='variable'>{{$value}}</td>
                    
                </tr>
                <tr>
                    <td>MUJERES</td>
                    <td>EXISTENCIA</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $im1 += $consulta_inscritos[$key]->insm1; @endphp@endforeach{{$im1}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $im2 += $consulta_inscritos[$key]->insm2; @endphp@endforeach{{$im2}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $im3 += $consulta_inscritos[$key]->insm3; @endphp@endforeach{{$im3}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $im4 += $consulta_inscritos[$key]->insm4; @endphp@endforeach{{$im4}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $im5 += $consulta_inscritos[$key]->insm5; @endphp@endforeach{{$im5}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $im6 += $consulta_inscritos[$key]->insm6; @endphp@endforeach{{$im6}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $im7 += $consulta_inscritos[$key]->insm7; @endphp@endforeach{{$im7}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $im8 += $consulta_inscritos[$key]->insm8; @endphp@endforeach{{$im8}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $im9 += $consulta_inscritos[$key]->insm9; @endphp@endforeach{{$im9}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>ACREDITADOS</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $em1 += $consulta_inscritos[$key]->iacrem1; @endphp@endforeach{{$em1}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $em2 += $consulta_inscritos[$key]->iacrem2; @endphp@endforeach{{$em2}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $em3 += $consulta_inscritos[$key]->iacrem3; @endphp@endforeach{{$em3}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $em4 += $consulta_inscritos[$key]->iacrem4; @endphp@endforeach{{$em4}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $em5 += $consulta_inscritos[$key]->iacrem5; @endphp@endforeach{{$em5}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $em6 += $consulta_inscritos[$key]->iacrem6; @endphp@endforeach{{$em6}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $em7 += $consulta_inscritos[$key]->iacrem7; @endphp@endforeach{{$em7}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $em8 += $consulta_inscritos[$key]->iacrem8; @endphp@endforeach{{$em8}}</td>
                    <td class='variable'>@foreach($encabezado as $key=>$item)@php $em9 += $consulta_inscritos[$key]->iacrem9; @endphp@endforeach{{$em9}}</td>
                </tr>

            </table>
        </div>
        <br>
    </div>
</body>
</html>