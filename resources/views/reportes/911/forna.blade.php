<?php
$a=1;
$value=0; $value1=0; $value2=0; $value3=0; $value4=0; $value5=0; $value6=0; $value7=0; $value8=0;
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
                    <td class='variable'> @isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh1}}@else{{''}}@endisset</td>
                    <td class='variable'> @isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh2}}@else{{''}}@endisset</td>
                    <td class='variable'> @isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh3}}@else{{''}}@endisset</td>
                    <td class='variable'> @isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh4}}@else{{''}}@endisset</td>
                    <td class='variable'> @isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh5}}@else{{''}}@endisset</td>
                    <td class='variable'> @isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh6}}@else{{''}}@endisset</td>
                    <td class='variable'> @isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh7}}@else{{''}}@endisset</td>
                    <td class='variable'> @isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh8}}@else{{''}}@endisset</td>
                    <td class='variable'> @isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh9}}@else{{''}}@endisset</td>
                </tr>
                <tr>
                    <td class='variable5'>HOMBRES</td>
                    <td class='variable5'>EXISTENCIA</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh1}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh2}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh3}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh4}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh5}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh6}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh7}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh8}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insh9}}@else{{''}}@endisset</td>
                </tr>
                <tr>
                    <td class='variable5'></td>
                    <td class='variable5'>ACREDITADOS</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacreh1}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacreh2}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacreh3}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacreh4}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacreh5}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacreh6}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacreh7}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacreh8}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacreh9}}@else{{''}}@endisset</td>
                </tr>
                <tr>
                    <td class='variable5'></td>
                    <td class='variable5'>INSCRIPCIÓN TOTAL</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm1}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm2}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm3}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm4}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm5}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm6}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm7}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm8}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm9}}@else{{''}}@endisset</td>
                    
                </tr>
                <tr>
                    <td class='variable5'>MUJERES</td>
                    <td class='variable5'>EXISTENCIA</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm1}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm2}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm3}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm4}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm5}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm6}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm7}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm8}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->insm9}}@else{{''}}@endisset</td>
                </tr>
                <tr>
                    <td class='variable5'></td>
                    <td class='variable5'>ACREDITADOS</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacrem1}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacrem2}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacrem3}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacrem4}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacrem5}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacrem6}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacrem7}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacrem8}}@else{{''}}@endisset</td>
                    <td class='variable'>@isset($consulta_inscritos[$key]){{$consulta_inscritos[$key]->iacrem9}}@else{{''}}@endisset</td>
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
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $value1 += $consulta_inscrito->insh1; @endphp@endforeach{{$value1}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $value2 += $consulta_inscrito->insh2; @endphp@endforeach{{$value2}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $value3 += $consulta_inscrito->insh3; @endphp@endforeach{{$value3}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $value4 += $consulta_inscrito->insh4; @endphp@endforeach{{$value4}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $value5 += $consulta_inscrito->insh5; @endphp@endforeach{{$value5}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $value6 += $consulta_inscrito->insh6; @endphp@endforeach{{$value6}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $value7 += $consulta_inscrito->insh7; @endphp@endforeach{{$value7}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $value8 += $consulta_inscrito->insh8; @endphp@endforeach{{$value8}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $value += $consulta_inscrito->insh9; @endphp@endforeach{{$value}}</td>
                </tr>
                <tr>
                    <td>HOMBRES</td>
                    <td>EXISTENCIA</td>
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
                    <td></td>
                    <td>ACREDITADOS</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $act1 += $consulta_inscrito->iacreh1; @endphp@endforeach{{$act1}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $act2 += $consulta_inscrito->iacreh2; @endphp@endforeach{{$act2}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $act3 += $consulta_inscrito->iacreh3; @endphp@endforeach{{$act3}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $act4 += $consulta_inscrito->iacreh4; @endphp@endforeach{{$act4}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $act5 += $consulta_inscrito->iacreh5; @endphp@endforeach{{$act5}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $act6 += $consulta_inscrito->iacreh6; @endphp@endforeach{{$act6}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $act7 += $consulta_inscrito->iacreh7; @endphp@endforeach{{$act7}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $act8 += $consulta_inscrito->iacreh8; @endphp@endforeach{{$act8}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $act9 += $consulta_inscrito->iacreh9; @endphp@endforeach{{$act9}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>INSCRIPCIÓN TOTAL</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $im1 += $consulta_inscrito->insm1; @endphp@endforeach{{$im1}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $im2 += $consulta_inscrito->insm2; @endphp@endforeach{{$im2}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $im3 += $consulta_inscrito->insm3; @endphp@endforeach{{$im3}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $im4 += $consulta_inscrito->insm4; @endphp@endforeach{{$im4}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $im5 += $consulta_inscrito->insm5; @endphp@endforeach{{$im5}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $im6 += $consulta_inscrito->insm6; @endphp@endforeach{{$im6}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $im7 += $consulta_inscrito->insm7; @endphp@endforeach{{$im7}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $im8 += $consulta_inscrito->insm8; @endphp@endforeach{{$im8}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $im9 += $consulta_inscrito->insm9; @endphp@endforeach{{$im9}}</td>
                </tr>
                <tr>
                    <td>MUJERES</td>
                    <td>EXISTENCIA</td>
                    <td class='variable'>{{$im1}}</td>
                    <td class='variable'>{{$im2}}</td>
                    <td class='variable'>{{$im3}}</td>
                    <td class='variable'>{{$im4}}</td>
                    <td class='variable'>{{$im5}}</td>
                    <td class='variable'>{{$im6}}</td>
                    <td class='variable'>{{$im7}}</td>
                    <td class='variable'>{{$im8}}</td>
                    <td class='variable'>{{$im9}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>ACREDITADOS</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $em1 += $consulta_inscrito->iacrem1; @endphp@endforeach{{$em1}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $em2 += $consulta_inscrito->iacrem2; @endphp@endforeach{{$em2}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $em3 += $consulta_inscrito->iacrem3; @endphp@endforeach{{$em3}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $em4 += $consulta_inscrito->iacrem4; @endphp@endforeach{{$em4}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $em5 += $consulta_inscrito->iacrem5; @endphp@endforeach{{$em5}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $em6 += $consulta_inscrito->iacrem6; @endphp@endforeach{{$em6}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $em7 += $consulta_inscrito->iacrem7; @endphp@endforeach{{$em7}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $em8 += $consulta_inscrito->iacrem8; @endphp@endforeach{{$em8}}</td>
                    <td class='variable'>@foreach($consulta_inscritos as $consulta_inscrito)@php $em9 += $consulta_inscrito->iacrem9; @endphp@endforeach{{$em9}}</td>
                </tr>
            </table>
        </div>
        <br>
    </div>
</body>
</html>












       