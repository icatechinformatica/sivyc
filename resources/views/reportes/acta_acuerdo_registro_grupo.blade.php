<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ACTA DE ACUERDO</title>

    <style>
        body{font-family: sans-serif}
        @page {margin: 20px 50px 120px 50px;size: letter;}
        header { position: fixed; left: 0px; top: 30px; right: 0px;text-align: center;width:100%;line-height: 30px;}
        img.izquierda {float: left;width: 31%;height: 60px;}
        img.izquierdabot {
                float: inline-end;
                width: 100%;
                height: 100%;
            }
        img.derechabot {position:fixed;right: 50px;width: 350px;height: 60px;}
        img.derecha {float: right;width: 50%;height: 60px;}
        .tablas{border-collapse: collapse;width: 100%;}
        /* agregamos a 3 el padding para que no salte a la otra pagina y la deje en blanco */
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;}
        /* .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;} */
        .tablad { border-collapse: collapse;position:fixed;margin-top:930px;margin-left:10px;}
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;table-layout: relative;}
        .tablag tr td { font-size: 8px; padding: 0px;}
        footer { position:fixed;left:0px;bottom:-100px;height:0px;width:100%;}
        footer .page:after { content: counter(page, sans-serif);}
        .contenedor {
        position:RELATIVE;
        top:120px;
        width:100%;
        margin:auto;

        /* Propiedad que ha sido agreda*/

        }
        .direccion
        {
            text-align: left;
            position: absolute;
            bottom: 60px;
            left: 25px;
            font-size: 8.5px;
            color: rgb(255, 255, 255);
            line-height: 1;
        }
        .color_dina{
            color: #000;
        }
        .negrita{
            font-weight: bold;
        }
    </style>
</head>
<body>


    <header>
            <img class="izquierda" src="{{ public_path('img/instituto_oficial.png') }}">
            <br>
            <br>
    </header>
    {{-- <footer>
        <img class="izquierdabot" src="{{ public_path('img/formatos/footer_horizontal.jpeg') }}">
        <p class='direccion'><b>@foreach($direccion as $point => $ari)@if($point != 0)<br> @endif {{$ari}}@endforeach</b></p>
        <script type="text/php">
            if (isset($pdf))
            {
                $x = 275;
                $y = 700;
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
    </footer> --}}
    <div class="contenedor">
        <h5 align=center>ACTA DE ACUERDO</h5>
        {{-- crear un div para encerrar todo lo que lleva al inicio --}}
        <div>
            <div align=right style="font-size:12px;"><b>NO. {{$data1->cespecifico}}</b></div>
        </div>
        <br>

        <div class="table-responsive-sm">
            <div align="justify" style="font-size:12px;">
                EN LA CIUDAD DE <span class="color_dina">{{$data1->muni}}</span>, <span class="color_dina">EL DIA {{$data1->diaes}} DE  {{strtoupper($data1->mes)}} DEL AÑO  {{$data1->anio}} </span>, ACEPTAN MEDIANTE
                TECNOLOGÍAS DIGITALES (CORREO ELECTRÓNICO Y/O MENSAJE DE DATOS Y/O CUALQUIER OTRO MEDIO
                ELECTRÓNICO, COMO MEDIO DE COMUNICACIÓN), DE FORMA VOLUNTARIA LAS <span class="color_dina">{{$data1->totalp}}</span> PERSONAS CUYOS
                NOMBRES QUE APARECEN EN EL APARTADO DEL LISTADO DE LA PRESENTE ACTA, RECIBIR EL CURSO DE
                CAPACITACIÓN DENOMINADO: <span class="color_dina">{{$data1->curso}}</span>, QUE SERÁ IMPARTIDO POR EL INSTITUTO DE
                CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS, A TRAVÉS DE LA UNIDAD DE
                CAPACITACIÓN <span class="color_dina">{{$data1->unidad}}</span>, CUYO TITULAR DE LA DIRECCION EL(LA) <span class="color_dina">{{$data2->dunidad}}</span>,
                DURANTE LOS DÍAS: <span class="color_dina">{{$data1->dia}} DEL {{$data1->diaini}}
                {{$data1->mesini != $data1->mesfin ? 'DE '.strtoupper($data1->mesini) : ''}}
                {{$data1->anioini != $data1->aniofin ? 'DEL '.$data1->anioIni : ''}}
                AL {{$data1->diafin}} DE {{strtoupper($data1->mesfin)}} DEL AÑO {{$data1->aniofin}} </span>, EN EL HORARIO DE <span class="color_dina">{{strtoupper($data1->hini)}} A {{strtoupper($data1->hfin)}}</span>

                HORAS, SEDE DEL CURSO MODALIDAD <span class="color_dina">{{$data1->tcapacitacion}}</span>, EN EL MUNICIPIO DE <span class="color_dina">{{$data1->muni}}</span>, CON UN
                COSTO DE $<span class="color_dina">{{ $data3[1]->costo }}</span> PESOS M/N POR PERSONA, SEÑALANDO COMO INSTRUCTOR DEL CURSO AL (A LA) C.
                <span class="color_dina">{{$data1->nombre}}</span>.
            </div>
            <br><br>
            <div align="justify" style="font-size:12px;">
                LOS FIRMANTES ACEPTAN LAS CONDICIONES QUE EL ICATECH DETERMINA A TRAVÉS DE LOS INSTRUCTORES,
                COMO SON: FORMAS DE ACCESO Y OPERACIÓN A LA PLATAFORMA, CONDICIONES DE ACCESO A LAS
                AULAS, FORMAS DE REGISTRO DE ASISTENCIA, TAREAS SINCRÓNICAS Y ASINCRÓNICAS, FORMAS DE
                EVALUACIÓN, TOMA DE CAPTURAS DE PANTALLA CON CÁMARAS HABILITADAS DE TODOS LOS ASISTENTES
                UNA VEZ AL DÍA, O TOMA DE FOTOGRAFÍAS DE LOS CURSOS PRESENCIALES UNA VEZ AL DÍA, QUE SE
                INTEGRARÁ COMO EVIDENCIA.
            </div>
            <br><br>
            <div align="justify" style="font-size:12px;">
                POR LO QUE ENTERADOS DEL CONTENIDO Y ALCANCE LEGAL DE LA PRESENTE ACTA DE ACUERDO,
                MANIFIESTAN SU CONFORMIDAD CON EL SID DE INSCRIPCIÓN Y EL MEDIO DE COMUNICACIÓN QUE SE
                ADJUNTA AL PRESENTE, DE RECIBIR EL CURSO CITADO EN EL PÁRRAFO PRECEDENTE. SUSCRIBIENDO PARA
                LA LEGALIDAD DE LOS ACTOS, AL TITULAR DE LA DIRECCION DE LA UNIDAD DE CAPACITACIÓN <span class="color_dina">{{$data1->unidad}}</span>,
                ANTE EL(LA) <span class="color_dina">{{$data2->delegado_administrativo}}, {{$data2->pdelegado_administrativo}}, EL(LA) {{$data2->academico}},
                {{$data2->pacademico}} Y EL(LA) {{$data2->vinculacion}},
                {{$data2->pvinculacion}}</span>.
            </div>
            <br>

            {{-- se llenan de datos esta tabla --}}
            <table class="tablas" border="1">
                <thead>
                    <tr><th colspan="2" style="font-size:12px;">LISTADO DE QUIENES INTEGRAN "EL GRUPO DE CAPACITACIÓN ABIERTA"</th></tr>
                    <tr>
                        <th style="font-size:12px;">NOMBRE COMPLETO DE CADA ALUMNO</th>
                        <th style="font-size:12px;">INDICAR MEDIO DE COMUNICACIÓN FIRMA O TECNOLOGÍA <br>
                            DIGITAL (CORREO ELECTRÓNICO Y/O MENSAJE DE DATOS Y/O <br>
                            MEDIOS ELECTRÓNICOS SEÑALADOS POR EL INTERESADO)</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach ($data3 as $key => $a)
                    <tr>
                        <th style="font-size:11px; text-align:left;">{{$key+1}}.- {{ $a->nombre.' '.$a->apellido_paterno.' '.$a->apellido_materno}}</th>
                        <th style="font-size:11px; text-align:left;">{{$a->correo}}</th>
                    </tr>
                   @endforeach
                </tbody>
            </table>
            {{-- contenido despues de la tabla --}}
            <br><br><br><br>
            <div align=center style="font-size:12px;"><b>TITULAR DE LA DIRECCIÓN DE LA UNIDAD DE CAPACITACIÓN.</b></div>
            <br><br><br>
            <div align=center style="font-size:12px;"><b><span class="color_dina">{{$data2->dunidad}}</span></b></div>
            <br><br>
            {{-- Tabla para las firmas --}}
            <table class="tablas" border="1">
                    <tr style="padding-bottom: 10px">
                        <td colspan="2" style="font-size:12px;"><p><b>TESTIGOS <br><span class="color_dina">{{$data2->delegado_administrativo}}</span></b></p>
                            <br><br><br>
                            <p><b><span class="color_dina">{{$data2->pdelegado_administrativo}}</span></b></p></td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;"><p><b>{{$data2->academico}}<br><br><br><br> TITULAR DEL DEPARTAMENTO ACADÉMICO</b></p></td>
                        <td style="font-size:12px;"><p><b>{{$data2->vinculacion}}<br><br><br><br> TITULAR DEL DEPARTAMENTO DE VINCULACIÓN</b></p></td>
                    </tr>
            </table>
            <br><br>
            {{-- <span class="color_dina"></span> --}}
            {{-- el texto de NOTA --}}
            <div align="justify" style="font-size:12px;">
                NOTA: LAS TECNOLOGÍAS DIGITALES COMO MEDIO DE COMUNICACIÓN (CORREO ELECTRÓNICO Y/O MENSAJE
                DE DATOS Y/O CUALQUIER OTRO DE LOS MEDIOS ELECTRÓNICOS SEÑALADOS POR EL (LA) INTERESADO (A)),
                SERÁN PRUEBA PLENA COMO MEDIO DIGITAL, PARA DEMOSTRAR LA ACEPTACIÓN DE RECIBIR EL CURSO
                CONFORME LO DISPUESTO POR LOS ARTÍCULOS 38, FRACCIÓN II DE LA LEY DE PROCEDIMIENTOS
                ADMINISTRATIVOS PARA EL ESTADO DE CHIAPAS.
            </div>
        </div>

    </div>
</body>
</html>
