{{-- Realizado por Jose Luis Morenoa Arcos --}}
@extends('theme.formatos.vlayout_conv')
@section('title', 'Acta de acuerdo | SIVyC Icatech')

@section('css')
    <style>
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
        .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px; font-weight: normal;}
        /* .tablas tr,th{font-size: 8px; border: gray 1px solid; text-align: center; padding: 2px;} */
        /* .tablad { border-collapse: collapse;position:fixed;margin-top:930px;margin-left:10px;}
        .tablad { font-size: 8px;border: gray 1px solid; text-align: left; padding: 2px;}
        .tablag { border-collapse: collapse; width: 100%;table-layout: relative;}
        .tablag tr td { font-size: 8px; padding: 0px;} */

        .color_dina{
            color: #000;
        }
        .negrita{
            font-weight: bold;
        }
        .estilo_p{
                line-height: 1.5;
            }
    </style>
@endsection

@php
    $municipio = $diace = $mesce = $anioce = $totalp = $diaini = $mesini = $anioini = $diafin = $mesfin = $aniofin = $unidad = $dias = $hini = $hfin =
    $tcapacitacion = $instructor = $curso = $cespecifico = $efisico = 'DATO REQUERIDO';

    if ($data1) {
        if ($data1->muni) $municipio = $data1->muni;
        if ($data1->fcespe) $diace = $data1->diaes; $mesce = $data1->mes; $anioce = $data1->anio;
        if ($data1->totalp) $totalp = $data1->totalp;
        if ($data1->inicio) $diaini = $data1->diaini; $mesini = $data1->mesini; $anioini = $data1->anioini;
        if ($data1->termino) $diafin = $data1->diafin; $mesfin = $data1->mesfin; $aniofin = $data1->aniofin;
        if ($data1->unidad) $unidad = $data1->unidad;
        if ($data1->dia) $dias = $data1->dia;
        if ($data1->hini) $hini = $data1->hini;
        if ($data1->hfin) $hfin = $data1->hfin;
        if ($data1->tcapacitacion) $tcapacitacion = $data1->tcapacitacion;
        if ($data1->nombre) $instructor = $data1->nombre;
        if ($data1->curso) $curso = $data1->curso;
        if ($data1->cespecifico) $cespecifico = $data1->cespecifico;
        if ($data1->efisico) $efisico = $data1->efisico;
    }

    $dunidad = $pdunidad = $delegado_administrativo = $pdelegado_administrativo = $academico =
    $pacademico = $vinculacion = $pvinculacion = 'DATO REQUERIDO';
    if ($data2 != null) {
        $dunidad = $data2->dunidad;
        $pdunidad = $data2->pdunidad;
        $academico = $data2->academico;
        $pacademico = $data2->pacademico;
        $vinculacion = $data2->vinculacion;
        $pvinculacion = $data2->pvinculacion;
        $delegado_administrativo = $data2->delegado_administrativo;
        $pdelegado_administrativo = $data2->pdelegado_administrativo;
    }
@endphp

@section('header')
    <img class="izquierda" src="{{ public_path('img/instituto_oficial.png') }}">
@endsection


@section('body')
    <div class="contenedor">
        <p align=center style="margin-top: 5px; font-size: 14px; font-weight:bold;">ACTA DE ACUERDO</p>
        {{-- crear un div para encerrar todo lo que lleva al inicio --}}
        <div>
            <div align=right style="font-size:12px;"><b>NO. {{$cespecifico}}</b></div>
        </div>
        <br>

        <div class="table-responsive-sm">
            <div align="justify" style="font-size:12px;" class="estilo_p">
                EN LA CIUDAD DE <span class="color_dina">{{$municipio}}</span>, <span class="color_dina">EL DIA {{sprintf("%02d", $diace)}} DE  {{strtoupper($mesce)}} DEL AÑO  {{$anioce.','}} </span>ACEPTAN
                {{($tcapacitacion == 'PRESENCIAL') ? ' DE MANERA PRESENCIAL,' : ($tcapacitacion == 'A DISTANCIA' ? ' A TRAVÉS DE MEDIOS ELECTRÓNICOS Y/O DIGITALES,' : '')}}
                DE FORMA VOLUNTARIA LAS <span class="color_dina">{{$totalp}}</span> PERSONAS CUYOS
                NOMBRES QUE APARECEN EN EL APARTADO DEL LISTADO DE LA PRESENTE ACTA, RECIBIR EL CURSO DE
                CAPACITACIÓN DENOMINADO: <span class="color_dina">{{$curso}}</span>, QUE SERÁ IMPARTIDO POR EL INSTITUTO DE
                CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS, A TRAVÉS DE LA UNIDAD DE
                CAPACITACIÓN <span class="color_dina">{{$unidad}}</span>, CUYO TITULAR DE LA DIRECCION EL(LA) <span class="color_dina">{{$dunidad}}</span>,
                DURANTE LOS DÍAS: <span class="color_dina">{{$dias}} DEL {{sprintf("%02d", $diaini)}}
                {{$mesini != $mesfin ? 'DE '.strtoupper($mesini) : ''}}
                {{$anioini != $aniofin ? 'DEL '.$anioIni : ''}}
                AL {{sprintf("%02d", $diafin)}} DE {{strtoupper($mesfin)}} DEL AÑO {{$aniofin.','}} </span> EN EL HORARIO DE <span class="color_dina">{{date("H:i", strtotime($hini))}} A {{date("H:i", strtotime($hfin))}}</span>
                HORAS, SEDE DEL CURSO
                @if ($tcapacitacion == 'PRESENCIAL')
                {{' CON DOMICILIO EN '.$efisico.', '}}
                @elseif($tcapacitacion == 'A DISTANCIA')
                {{' PLATAFORMA VIRTUAL, '}}
                @endif
                CON UN COSTO DE $<span class="color_dina">{{ $data3[1]->costo }}</span> PESOS M/N POR PERSONA, SEÑALANDO COMO INSTRUCTOR DEL CURSO AL (A LA) C.
                <span class="color_dina">{{$instructor}}</span>.
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                LOS FIRMANTES ACEPTAN LAS CONDICIONES QUE EL ICATECH DETERMINA A TRAVÉS DE LOS INSTRUCTORES,
                COMO SON: FORMAS DE ACCESO Y OPERACIÓN A LA PLATAFORMA, CONDICIONES DE ACCESO A LAS
                AULAS, FORMAS DE REGISTRO DE ASISTENCIA, TAREAS SINCRÓNICAS Y ASINCRÓNICAS, FORMAS DE
                EVALUACIÓN, TOMA DE CAPTURAS DE PANTALLA CON CÁMARAS HABILITADAS DE TODOS LOS ASISTENTES
                UNA VEZ AL DÍA, O TOMA DE FOTOGRAFÍAS DE LOS CURSOS PRESENCIALES UNA VEZ AL DÍA, QUE SE
                INTEGRARÁ COMO EVIDENCIA.
            </div>
            <br>
            <div align="justify" style="font-size:12px;" class="estilo_p">
                POR LO QUE ENTERADOS DEL CONTENIDO Y ALCANCE LEGAL DE LA PRESENTE ACTA DE ACUERDO,
                MANIFIESTAN SU CONFORMIDAD CON EL SID DE INSCRIPCIÓN Y EL MEDIO DE COMUNICACIÓN QUE SE
                ADJUNTA AL PRESENTE, DE RECIBIR EL CURSO CITADO EN EL PÁRRAFO PRECEDENTE. SUSCRIBIENDO PARA
                LA LEGALIDAD DE LOS ACTOS, AL TITULAR DE LA DIRECCION DE LA UNIDAD DE CAPACITACIÓN <span class="color_dina">{{$unidad}}</span>,
                ANTE EL (LA) <span class="color_dina">{{$delegado_administrativo}}, {{$pdelegado_administrativo}}, EL (LA) {{$academico}},
                {{$pacademico}} Y EL (LA) {{$vinculacion}},
                {{$pvinculacion}}</span>.
            </div>
            <br>
            {{-- se llenan de datos esta tabla --}}
            <table class="tablas" border="1">
                <thead>
                    <tr><th colspan="2" style="font-size:11px; font-weight:bold;">LISTADO DE QUIENES INTEGRAN "EL GRUPO DE CAPACITACIÓN ABIERTA"</th></tr>
                    <tr>
                        <th style="font-size:11px; font-weight:bold; width:50%px;">NOMBRE COMPLETO DE CADA ALUMNO</th>
                        <th style=""><span style="font-size: 11px; font-weight:bold; width:50%;">FIRMA AUTOGRAFA</span>
                            <br>
                            <span style="font-size: 8px;">PARA CURSOS DE CAPACITACIÓN CON ACTIVIDAD NO PRESENCIAL: INDICAR MEDIO COMUNICACIÓN
                                (CORREO ELECTRÓNICO Y/O MENSAJE DE DATOS Y/O MEDIOS ELECTRÓNICOS SEÑALADOS POR EL INTERESADO).
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($data3 as $key => $a)
                    <tr>
                        <th><p style="font-size:11px; text-align:left; height: 20px;">{{$key+1}}.- {{ $a->nombre.' '.$a->apellido_paterno.' '.$a->apellido_materno}}</p></th>
                        <th>
                            {{-- {{$a->correo}} --}}
                            @if ($tcapacitacion == 'A DISTANCIA')
                                @if ($a->medio_confirmacion == 'WHATSAPP' || $a->medio_confirmacion == 'MENSAJE DE TEXTO' || $a->medio_confirmacion == 'TELEGRAM')
                                    <p style="font-size:13px; text-align:left; height: 20px;">{{$a->telefono_personal}}</p>
                                @elseif($a->medio_confirmacion == 'CORREO ELECTRÓNICO' || $a->medio_confirmacion == 'FACEBOOK'
                                    || $a->medio_confirmacion == 'INSTAGRAM' || $a->medio_confirmacion == 'TWITTER')
                                    <p style="font-size:13px; text-align:left; height: 20px;">{{$a->correo}}</p>
                                @endif
                            @endif
                        </th>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{-- Firmantes --}}
            <div style="margin-top: 30px;">
                <div align=center style="font-size:12px; margin-bottom:60px;"><b>{{$pdunidad}}</b></div>
                <div align=center style="font-size:12px;"><b><span class="color_dina">{{$dunidad}}</span></b></div>
            </div>
            {{-- Tabla para las firmas --}}
            <table class="tablas" border="1" style="margin-top: 20px;">
                    <tr style="padding-bottom: 10px">
                        <td colspan="2" style="font-size:12px;"><p><b>TESTIGOS <br>
                            <span class="color_dina">{{$delegado_administrativo}}</span></b></p>
                            <br><br>
                            <p><b><span class="color_dina">{{$pdelegado_administrativo}}</span></b></p></td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;"><p><b>{{$academico}}<br><br><br><br> {{$pacademico}}</b></p></td>
                        <td style="font-size:12px;"><p><b>{{$vinculacion}}<br><br><br><br> {{$pvinculacion}}</b></p></td>
                    </tr>
            </table>
            <br><br>

            {{-- Texto NOTA --}}
            <div align="justify" style="font-size:12px;" class="estilo_p">
                NOTA: LAS TECNOLOGÍAS DIGITALES COMO MEDIO DE COMUNICACIÓN (CORREO ELECTRÓNICO Y/O MENSAJE
                DE DATOS Y/O CUALQUIER OTRO DE LOS MEDIOS ELECTRÓNICOS SEÑALADOS POR EL (LA) INTERESADO (A)),
                SERÁN PRUEBA PLENA COMO MEDIO DIGITAL, PARA DEMOSTRAR LA ACEPTACIÓN DE RECIBIR EL CURSO
                CONFORME LO DISPUESTO POR LOS ARTÍCULOS 38, FRACCIÓN II DE LA LEY DE PROCEDIMIENTOS
                ADMINISTRATIVOS PARA EL ESTADO DE CHIAPAS.
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(73, 760, "Pág. $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }
    </script>
@endsection

