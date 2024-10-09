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

    /* Estilos de agregar titulos separados a una tabla */
    .container {
        overflow: hidden;
        display: inline-block;
        /* margin-right: 20px; */
        font-size: 10px;
    }

    .tamano1{
        width: 85px;
        margin-left: 125px;
    }
    .tamano2{
        width: 95px;
        margin-left: 25px;
    }
    .tamano3{
        width: 110px;
        margin-left: 7px;
    }
    .tamano4{
        width: 100px;
        margin-left: 8px;
    }
    .tamano5{
        width: 100px;
        margin-left: 12px;
    }

    .contenedor{
        margin-left: 28%;
    }

    .container p {
        margin-left: -10px;
        text-align: center;
    }
    .variable6{
        width: auto;
        border-spacing:10px 0px;
        text-align: left;
    }
    .tamano_celda{
        width: 60px;
    }
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
        {{-- Complementar el reporte 911 Made by Jose Luis Moreno Arcos --}}
        <br><br>
        <div class="variable3">
            <p>Del total de la matrícula existente reportada en la pregunta anterior,
            anote a los alumnos con discapacidades, dificultades, trasntornos, aptitudes sobresalientes u otras condiciones (no
            consideradas en los rubros anteriores) y desglóselos por sexo.</p>
        </div>
        <br>
        {{-- tabla de discapacidades --}}
        <div class='table'>
            <table class='variable3'>
                <tr>
                    <td align="center">CONDICIÓN DEL ALUMNO</td>
                    <td class='variable2'>HOMBRES</td>
                    <td class='variable2'>MUJERES</td>
                    <td class='variable2'><b>TOTAL</b></td>
                </tr>
                <tr>
                    <td>CEGUERA</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>BAJA VISIÓN</td>
                    <td class='variable'>{{ $vulnerav_h['disc_ver'] }}</td>
                    <td class='variable'>{{ $vulnerav_m['disc_ver'] }}</td>
                    <td class='variable'>{{ $vulnerav_h['disc_ver'] + $vulnerav_m['disc_ver'] }}</td>
                </tr>
                <tr>
                    <td>SORDERA</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>HIPOACUSIA</td>
                    <td class='variable'>{{ $vulnerav_h['disc_oir'] }}</td>
                    <td class='variable'>{{ $vulnerav_m['disc_oir'] }}</td>
                    <td class='variable'>{{ $vulnerav_h['disc_oir'] + $vulnerav_m['disc_oir'] }}</td>
                </tr>
                <tr>
                    <td>SORDOCEGUERA</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>DISCAPACIDAD MOTRIZ</td>
                    <td class='variable'>{{ $vulnerav_h['disc_motriz'] }}</td>
                    <td class='variable'>{{ $vulnerav_m['disc_motriz'] }}</td>
                    <td class='variable'>{{ $vulnerav_h['disc_motriz'] + $vulnerav_m['disc_motriz'] }}</td>
                </tr>
                <tr>
                    <td>DISCAPACIDAD INTELECTUAL</td>
                    <td class='variable'>{{ $vulnerav_h['disc_mental'] }}</td>
                    <td class='variable'>{{ $vulnerav_m['disc_mental'] }}</td>
                    <td class='variable'>{{ $vulnerav_h['disc_mental'] + $vulnerav_m['disc_mental'] }}</td>
                </tr>
                <tr>
                    <td>DISCAPACIDAD PSICOSOCIAL</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>TRANSTORNO DEL ESPECTRO AUTISTA</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>DISCAPACIDAD MÚLTIPLE</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>DIFICULTAD SEVERA DE CONDUCTA</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>DIFICULTAD SEVERA DE COMUNICACIÓN</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>DIFICULTAD SEVERA DE APRENDIZAJE</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>TRANSTORNO POR DEFICIT DE ATENCIÓN E HIPERACTIVIDAD</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>APTITUDES SOBRESALIENTES</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>OTRAS CONDICIONES</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>TOTAL</td>
                    <td class='variable'>{{ $vulnerav_h['totalreg'] }}</td>
                    <td class='variable'>{{ $vulnerav_m['totalreg'] }}</td>
                    <td class='variable'>{{ $vulnerav_h['totalreg'] +  $vulnerav_m['totalreg']}}</td>
                </tr>
            </table>
        </div>
    </div>
    {{-- Ultimo bloque --}}
    <br><br>
    <div class="tabla-madre" style="font_size: 12px">
        <div class='variable3'> <h2 class="variable2">PERSONAL POR FUNCIÓN</h2> <br></div>
        <div><p>1. Escriba el personal que realiza funciones de directivo (con o sin grupo), docente, servicios profesionales especiales,
            administrativo, auxiliar y de servicio, independientemente de su nombramiento, tipo y fuente de pago, desglóse segun su función,
            nivel maximo de estudios y sexo, Nota: Si una persona desempeña dos o más funciones anótela en aquélla a la que dedique más tiempo.</p>
        </div>

        <div class='table'>
            <div class="contenedor">
                <div class="container tamano1">
                    <p>PERSONAL DIRECTIVO CON GRUPO</p>
                </div>
                <div class="container tamano2">
                    <p>PERSONAL DIRECTIVO SIN GRUPO</p>
                </div>
                <div class="container tamano3">
                    <p>PERSONAL DOCENTE</p>
                </div>
                <div class="container tamano4">
                    <p>PERSONAL DE SERVICIOS PROFECIONALES Y ESPECIALES</p>
                </div>
                <div class="container tamano5">
                    <p>PERSONAL ADMINISTRATIVO, AUXILIAR Y DE SERVICIOS</p>
                </div>
            </div>

            <table class='variable3'>
                <tr>
                    <td><b>NIVEL EDUCATIVO</b></td>
                    <td class='variable2'>HOM</td>
                    <td class='variable2'>MUJ</td>
                    <td class='variable2'>HOM</td>
                    <td class='variable2'>MUJ</td>
                    <td class='variable2'>HOM</td>
                    <td class='variable2'>MUJ</td>
                    <td class='variable2'>HOM</td>
                    <td class='variable2'>MUJ</td>
                    <td class='variable2'>HOM</td>
                    <td class='variable2'>MUJ</td>
                </tr>
                <tr>
                    <td>PRIMARIA INCOMPLETA</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>{{ $instruc_h['primaria_inc'] }}</td>
                    <td class='variable'>{{ $instruc_m['primaria_inc'] }}</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>PRIMARIA</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>{{ $instruc_h['primaria'] }}</td>
                    <td class='variable'>{{ $instruc_m['primaria'] }}</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>SECUNDARIA</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>{{ $instruc_h['secundaria'] }}</td>
                    <td class='variable'>{{ $instruc_m['secundaria'] }}</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>BACHILLERATO / PROFESIONAL TÉCNICO</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>{{ $instruc_h['bachiller'] }}</td>
                    <td class='variable'>{{ $instruc_m['bachiller'] }}</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                {{-- <tr>
                    <td>BACHILLERATO</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr> --}}
                <tr>
                    <td>NORMAL PREESCOLAR</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>NORMAL PRIMARIA</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>NORMAL SUPERIOR</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>LICENCIATURA</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>{{ $instruc_h['licenciatura'] }}</td>
                    <td class='variable'>{{ $instruc_m['licenciatura'] }}</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>MAESTRIA</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>{{ $instruc_h['maestria'] }}</td>
                    <td class='variable'>{{ $instruc_m['maestria'] }}</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>DOCTORADO</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>{{ $instruc_h['doctorado'] }}</td>
                    <td class='variable'>{{ $instruc_m['doctorado'] }}</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>OTROS*</td>
                </tr>
                {{-- <tr>
                    <td>*ESPECIFIQUE:</td>
                </tr> --}}
                <tr>
                    <td>PROFESIONAL TRUNCA</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>{{ $instruc_h['prof_trunco'] }}</td>
                    <td class='variable'>{{ $instruc_m['prof_trunco'] }}</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>PROFESIONAL PASANTE</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>{{ $instruc_h['prof_pasante'] }}</td>
                    <td class='variable'>{{ $instruc_m['prof_pasante'] }}</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>MAESTRÍA CONCLUIDA (PASANTE)</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>{{ $instruc_h['maestria_pasante'] }}</td>
                    <td class='variable'>{{ $instruc_m['maestria_pasante'] }}</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>DOCTORADO PASANTE</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>{{ $instruc_h['doctorado_pasante'] }}</td>
                    <td class='variable'>{{ $instruc_m['doctorado_pasante'] }}</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td>PROFESIONAL CERTIFICADO</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>{{ $instruc_h['prof_cert_comp'] }}</td>
                    <td class='variable'>{{ $instruc_m['prof_cert_comp'] }}</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td><b>SUBTOTAL</b></td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>{{ $instruc_h['subtotal'] }}</td>
                    <td class='variable'>{{ $instruc_m['subtotal'] }}</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                    <td class='variable'>0</td>
                </tr>
                <tr>
                    <td colspan="10" align="right">TOTAL DE PERSONAL (SUMA DE SUBTOTALES)</td>
                    <td class='variable'>{{ $instruc_h['subtotal'] + $instruc_m['subtotal'] }}</td>
                </tr>

            </table>
        </div>
        {{-- punto 2 --}}
        <br><br>
        <div><p> 2. Desglose el personal docente, según el tiempo que dedica a la función académica, Nota: Si en la instutución no
            se utiliza el término tres cuartos de tiempo, no lo considere.
        </p>
        </div>
        <br><br>
        <table class='variable6'>
            <tr>
                <td>TIEMPO COMPLETO</td>
                <td class='variable tamano_celda'>0</td>
            </tr>
            <tr>
                <td>TRES CUARTOS DE TIEMPO</td>
                <td class='variable tamano_celda'>0</td>
            </tr>
            <tr>
                <td>MEDIO TIEMPO</td>
                <td class='variable tamano_celda'>0</td>
            </tr>
            <tr>
                <td>POR HORAS</td>
                <td class='variable tamano_celda'>{{ $instruc_h['subtotal'] + $instruc_m['subtotal'] }}</td>
            </tr>
            <tr>
                <td><b>TOTAL</b></td>
                <td class='variable tamano_celda'>{{ $instruc_h['subtotal'] + $instruc_m['subtotal'] }}</td>
            </tr>
        </table>
    </div>
</body>
</html>












