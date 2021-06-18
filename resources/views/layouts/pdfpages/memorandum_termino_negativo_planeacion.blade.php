<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MEMORANDUM FORMATO ENTREGA NEGATIVA</title>
    <style type="text/css">
        body {
            font-family: sans-serif
        }

        /* margenes top 20px right 50px bottom 120px left 50px */
        @page {
            margin: 20px 50px 120px 50px;
            size: letter;
        }

        header {
            position: fixed;
            left: 0px;
            top: 30px;
            right: 0px;
            text-align: center;
            width: 100%;
            line-height: 30px;
        }

        img.izquierda {
            float: left;
            width: 200px;
            height: 60px;
        }

        img.izquierdabot {
            position: fixed;
            left: 50px;
            width: 350px;
            height: 60px;
        }

        img.derechabot {
            position: fixed;
            right: 50px;
            width: 350px;
            height: 60px;
        }

        img.derecha {
            float: right;
            width: 200px;
            height: 60px;
        }

        .tablas {
            border-collapse: collapse;
            width: 100%;
        }

        .tablas tr,
        th {
            font-size: 8px;
            border: gray 1px solid;
            text-align: center;
            padding: 2px;
        }

        .tablad {
            border-collapse: collapse;
            position: fixed;
            margin-top: 930px;
            margin-left: 10px;
        }

        .tablad {
            font-size: 8px;
            border: gray 1px solid;
            text-align: left;
            padding: 2px;
        }

        .tablag {
            border-collapse: collapse;
            width: 100%;
            table-layout: relative;
        }

        .tablag tr td {
            font-size: 8px;
            padding: 0px;
        }

        footer {
            position: fixed;
            left: 0px;
            bottom: 0px;
            height: 0px;
            width: 100%;
        }

        footer .page:after {
            content: counter(page, sans-serif);
        }

        .contenedor {
            position: RELATIVE;
            top: 120px;
            width: 100%;
            margin: auto;

            /* Propiedad que ha sido agreda*/

        }

        .margin_top_ccp {
            margin-top: 7em;
        }

    </style>
</head>

<body>
    {{-- SECCIÓN DE LA CABECERA --}}
    <header>
        <img class="izquierda" src='img/logohorizontalica1.jpg'>
        <img class="derecha" src='img/chiapas.png'>
        <br>
        <h6><b></b>"2021, AÑO DE LA INDEPENDENCIA"</h6>
    </header>
    {{-- SECCIÓN DE LA CABECERA FIN --}}
    {{-- SECCIÓN DE PIE DE PÁGINA --}}
    <footer>
        <script type="text/php">
            if (isset($pdf)) 
            {
                $x = 275;
                $y = 725;
                $text = "Hoja {PAGE_NUM} de {PAGE_COUNT}";
                $font = "Arial";
                $size = 11;
                $color = array(0,0,0);
                $word_space = 0.0;  //  default
                $char_space = 0.0;  //  default
                $angle = 0.0;   //  default
                $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
            }
        </script>

        <table class="tablad" bgcolor="#621132">
            <tr>
                <td colspan="4" style="color:white;"><b>14 Poniente Norte No. 239 Colonia Moctezuma </b></td>
            </tr>
            <tr>
                <td colspan="4" style="color:white;"><b>Tuxtla Gutiérrez, CP 29030 Teléfono: +52 (961) 61 21621</b></td>
            </tr>
            <tr>
                <td colspan="4" style="color:white;"><b>email: icatech@icatech.chiapas.gob.mx</b></td>
            </tr>
        </table>
        <img class="derecha" src='img/icatech-imagen.png'>
    </footer>
    {{-- SECCIÓN DE PIE DE PÁGINA FIN --}}
    {{-- SECCIÓN DE CONTENIDO --}}
    <div class="contenedor">
        <div align=right style="font-size:11px;"><b>DIRECCIÓN DE PLANEACIÓN. </b></div>
        <div align=right style="font-size:11px;"><b>MEMORÁNDUM NO. {{ $num_memo_planeacion }}</b></div>
        <div align=right style="font-size:11px;"><b>TUXTLA GUTIÉRREZ, CHIAPAS; {{ $fecha_ahora_espaniol }}</b></div>
        <br><br>
        <div align=left style="font-size:12px;"><b>{{ $reg_unidad->dacademico }}.</b></div>
        <div align=left style="font-size:11px;"><b>{{ $reg_unidad->pdacademico }}.</b></div>
        <div align="left" style="font-size: 11px;"><b>Presente</b></div>
        <br><br><br><br>
        <div align="justify" style="font-size:16px;">
            <p>
                De acuerdo a la información estadistica reportada mediante el Formato T, recibido por
                medio del Sistema Integral de Vinculación y Capacitación (SIVyC), con cierre al mes de <b>{{$mesReport}}</b>,
                me permito informarle que una vez revisada la información relativa a los números
                absolutos de las variables cualitativas y cuantitativas; esta Dirección a través del
                Departamento de Programación y Presupuesto, No da por concluido el cierre estadístico del mes
                de {{$mesReport}} del 2021, derivado a que presenta inconsistencias.
            </p>
        </div>
        <br>

        <br>
        <div align="justify" style="font-size:16px;">Sin más por el momento y agradeciéndole su valioso apoyo, le envío
            un cordial saludo.</div>
        <br>

        <br>
        <div style="font-size:11px;"> <b>A T E N T A M E N T E</b> </div>

        <div class="margin_top_ccp">
            <div style="font-size:11px;"> <b>C.P.
                    {{ $directorPlaneacion->nombre . ' ' . $directorPlaneacion->apellidoPaterno . ' ' . $directorPlaneacion->apellidoMaterno }}
                </b> </div>
            <div style="font-size:11px;"> <b> {{ $directorPlaneacion->puesto }} </b> </div>
            <br><br><br>
            <div style="font-size:11px;"> <b>C.C.P {{ $reg_unidad->dgeneral }} , {{ $reg_unidad->pdgeneral }} .
                    Edificio.</b> </div>
            <div style="font-size:11px"><b>C.C. ING.
                    {{ $directorio->nombre . ' ' . $directorio->apellidoPaterno . ' ' . $directorio->apellidoMaterno }} -
                    {{ $directorio->puesto }} . EDIFICIO.</b></div>
            <div style="font-size:10px;"> <b>ARCHIVO: MINUTARIO.</b> </div>
            <div style="font-size:10px;"> <b>VALIDÓ: ING.
                    {{ $directorio->nombre . ' ' . $directorio->apellidoPaterno . ' ' . $directorio->apellidoMaterno }} -
                    {{ $directorio->puesto }} .</b> </div>
            <div style="font-size:10px;"> <b>ELABORÓ: LIC. VIANEY SOLEDAD RÍOS CRUZ. - ANALISTA TÉCNICO ESPECIALIZADO.</b></div>
            <br><br>
        </div>

    </div>
    {{-- SECCIÓN DE CONTENIDO FIN --}}

</body>

</html>
