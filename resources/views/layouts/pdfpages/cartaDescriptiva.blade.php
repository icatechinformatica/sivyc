<!DOCTYPE HTML>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <style>
            body{
                font-family: sans-serif;
            }
            @page {
                margin: 100px 20px 80px;
                color: black;
            }
            header { position: fixed;
                left: 0px;
                top: -90px;
                /* margin-left: -20px; */
                padding-left: 0px;
                height: 90px;
                width: 100%;
                background-color: white;
                color: black;
                text-align: center;
                line-height: 60px;
            }
            footer {
                position: fixed;
                left: 0px;
                bottom: -70px;
                right: 0px;
                height: 80px;
                width: 100%;
                padding-left: 0px;
                background-color: white;
                color: black;
                text-align: center;
            }
            img.izquierda {
                float: left;
                width: 100%;
                height: 100%;
            }

            img.izquierdabot {
                float: inline-end;
                width: 100%;
                height: 100%;
            }
            div.content
            {
                margin-top: 60%;
                margin-bottom: 70%;
                margin-right: 25%;
                margin-left: 0%;
            }
            .direccion
            {
                text-align: left;
                position: absolute;
                bottom: 0px;
                left: 65px;
                font-size: 8.5px;
                color: rgb(255, 255, 255);
                line-height: 1;
            }
            .tableA {
                font-size: 1.1rem;
            }
            .tableA td {
                padding-left: 5px;
            }
            .inline-block {
                display: inline-block;
                vertical-align: top; /* Optional: Aligns the elements vertically */
            }
            .center-text {
                text-align: center;
            }
            .page-break {
                page-break-after: always;
            }
            .page-break-non {
                page-break-after: avoid;
            }
        </style>
    </head>
    <body>
        <header>
            <img class="izquierda" src="{{ public_path('img/formatos/banner_carta_descriptiva.png') }}">
        </header>
        <footer>
            <img class="izquierdabot" src="{{ public_path('img/formatos/footer_carta_descriptiva.png') }}">
        </footer>
        <div id="wrapper">
            {{-- <br> --}}
            <h3 class="center-text">CARTA DESCRIPTIVA</h3>
            <table border="1" class="tableA" style="width: 100%;">
                <tbody>
                    <tr>
                        <th colspan="7" style="text-align: center;">Datos Generales</th>
                    </tr>
                    <tr>
                        <td rowspan="2"><b>Entidad Federativa: Chiapas</b></td>
                        <td rowspan="2"><b>Ciclo Escolar: </b>{{$ejercicio}}</td>
                        <td rowspan="2"><b>Programa Estratégico (en caso aplicable): {{$carta_descriptiva->datos_generales->pogrm_estra}}</b></td>
                        <td colspan="4" style="text-align: center;"><b>Modalidad</b></td>
                    </tr>
                    <tr style="text-align: center;">
                        <td><b>EXT</b></td>
                        <td>@if(in_array($data_curso->modalidad,['EXT','CAE Y EXT'])) X @endif</td>
                        <td><b>CAE</b></td>
                        <td>@if(in_array($data_curso->modalidad,['CAE','CAE Y EXT'])) X @endif</td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>Tipo:    Presencial   (</b>@if(in_array($data_curso->tipo_curso,['PRESENCIAL','PRESENCIAL Y A DISTANCIA'])) X @endif<b>)     A distancia (línea)    (</b>@if(in_array($data_curso->tipo_curso,['A DISTANCIA','PRESENCIAL Y A DISTANCIA'])) X @endif<b>)</b></td>
                        <td colspan="5"><b>Perfil idóneo del instructor: </b>{{$carta_descriptiva->datos_generales->perfil_instruc}}</td>
                    </tr>
                    <tr>
                        <td colspan="4"><b>Nombre del curso: </b>{{$data_curso->nombre_curso}}</td>
                        <td colspan="3"><b>Duración en horas: </b>{{$data_curso->horas}} horas</td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>Campo de Formación Profesional: </b>{{$data_curso->area}}</td>
                        <td colspan="5"><b>Especialidad: </b>{{$data_curso->especialidad}}</td>
                    </tr>
                    <tr>
                        <td colspan="7"><b>Aprendizaje esperado: </b>{!!$carta_descriptiva->datos_generales->aprendizaje_esp!!}</td>
                    </tr>
                    <tr>
                        <td colspan="7"><b>Objetivos específicos por tema: </b>{!!$carta_descriptiva->datos_generales->obj_especificos!!}</td>
                    </tr>
                    <tr>
                        <td colspan="7"><b>Transversalidad con otros cursos: </b>{!!$carta_descriptiva->datos_generales->transversalidad!!}</td>
                    </tr>
                    <tr>
                        <td colspan="7"><b>Público o personal a quien va dirigido el curso: </b>{!!$carta_descriptiva->datos_generales->dirigido!!}</td>
                    </tr>
                    <tr>
                        <td colspan="7"><b>Proceso de evaluación: </b>{!!$carta_descriptiva->datos_generales->proces_evalua!!}</td>
                    </tr>
                    <tr>
                        <td colspan="7"><b>Observaciones: </b>{!!$carta_descriptiva->datos_generales->observaciones!!}</td>
                    </tr>
                </tbody>
            </table>
            <div class="page-break"></div>
            <br>
            <table border="1" class="tableA" style="width: 100%;">
                <tbody>
                    <tr  style="text-align: center;">
                        <td><b>Contenido Temático</b></td>
                        <td><b>Estrategias Didácticas</b></td>
                        <td><b>Proceso de Evaluación</b></td>
                        <td width='12%'><b>Duración (EN HORAS)</b></td>
                    </tr>
                    @php $modulo = 0; @endphp
                    @foreach ($contenido_tematico as $key=>$moist)
                        @if($moist->nivel == 1)
                            @php
                                if($key != 0){ $modulo++;}
                                $id_principal = $moist->id;
                                $presencial = explode(':',$moist->duracion);
                                $sincrono = explode(':',$moist->sincrona);
                                $asincrono = explode(':',$moist->asincrona);
                            @endphp
                            <tr>
                                <td>
                                    @if($key != 0 && $moist->nivel == 1) Módulo {{$modulo}} @endif
                                    <br>
                                    {{$moist->nombre_modulo}}<br>
                                    @if($contenido_tematico[$key+1]->id_parent == $id_principal)
                                        Submódulos  <br>
                                        @foreach ($contenido_tematico as $data)
                                            @if ($id_principal == $data->id_parent)
                                                {{$data->numeracion}} {{$data->nombre_modulo}}<br>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td>{!!$moist->estra_didac!!}</td>
                                <td>{!!$moist->process_eval!!}</td>
                                <td style="text-align: center;">
                                    @if($presencial[0] > 0 || $presencial[1] > 0)
                                        <b>Presencial</b><br>
                                        @if($presencial[0] > 0)
                                            @if($presencial[0] >= 10)
                                                {{$presencial[0]}}
                                            @else
                                                {{$presencial[0]['1']}}
                                            @endif
                                            @if($presencial[0] > 1)Horas @else Hora @endif
                                        @endif
                                        @if($presencial[1] > 0)
                                            @if($presencial[1] >= 10)
                                                {{$presencial[1]}}
                                            @else
                                                {{$presencial[1]['1']}}
                                            @endif
                                            @if($presencial[1] > 1)Minutos @else Minuto @endif
                                        @endif
                                    @endif
                                    @if($sincrono[0] > 0 || $sincrono[1] > 0 || $asincrono[0] > 0 || $asincrono[1] > 0)
                                        <br><br><b>A Distancia</b>
                                        @if($sincrono[0] > 0)
                                            @if($sincrono[0] > 0 || $sincrono[1] > 0) <br><b>Sincronas</b><br>@endif
                                            @if($sincrono[0] >= 10)
                                                {{$sincrono[0]}}
                                            @else
                                                {{$sincrono[0]['1']}}
                                            @endif
                                            @if($sincrono[0] > 1)Horas @else Hora @endif
                                            @if($sincrono[1] > 0)
                                                @if($sincrono[1] >= 10)
                                                    {{$sincrono[1]}}
                                                @else
                                                    {{$sincrono[1]['1']}}
                                                @endif
                                                @if($sincrono[1] > 1)Minutos @else Minuto @endif
                                            @endif
                                        @endif
                                        @if($asincrono[0] > 0)
                                            @if($asincrono[0] > 0 || $asincrono[1] > 0) <br><b>Asincronas</b><br>@endif
                                            @if($asincrono[0] >= 10)
                                                {{$asincrono[0]}}
                                            @else
                                                {{$asincrono[0]['1']}}
                                            @endif
                                            @if($asincrono[0] > 1)Horas @else Hora @endif
                                            @if($asincrono[1] > 0)
                                                @if($asincrono[1] >= 10)
                                                    {{$asincrono[1]}}
                                                @else
                                                    {{$asincrono[1]['1']}}
                                                @endif
                                                @if($asincrono[1] > 1)Minutos @else Minuto @endif
                                            @endif
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div class="page-break"></div>
            <br>
            <table border="1" class="tableA" style="width: 100%;">
                <tbody>
                    <tr>
                        <td colspan="3" style="text-align: center;"><b>Recursos Didácticos</b></td>
                    </tr>
                    <tr>
                        <td><p style="text-align: center;"><b>Elementos de Apoyo: </b></p>{!!$carta_descriptiva->rec_didacticos->elem_apoyo!!}</td>
                        <td><p style="text-align: center;"><b>Auxiliares de la Enseñanza: </b></p>{!!$carta_descriptiva->rec_didacticos->auxiliares_ense!!}</td>
                        <td><p style="text-align: center;"><b>Referencias: </b></p>{!!$carta_descriptiva->rec_didacticos->referencias!!}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>
