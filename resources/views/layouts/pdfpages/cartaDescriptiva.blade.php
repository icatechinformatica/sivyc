<!DOCTYPE HTML>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <style>
            body {
                font-family: sans-serif;
                padding: 13% 2% 7% 2% !important;
            }
            @page {
                margin: 0px;
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

        #fondo1 {
            background-image: url('img/membretado/membretado_carta_descriptiva.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        </style>
    </head>
    <body id="fondo1">
        <div class="content">
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
            {{-- <div class="page-break"></div> --}}
            <br>
            <table border="1" class="tableA" style="width: 100%;">
                <tbody>
                    <tr  style="text-align: center;">
                        <td width="20%"><b>Contenido Temático</b></td>
                        <td width="48%"><b>Estrategias Didácticas</b></td>
                        <td width="22%"><b>Proceso de Evaluación</b></td>
                        <td width='10%'><b>Duración (EN HORAS)</b></td>
                    </tr>
                    @php
                        $modulo = 0;
                        $conteo_array = count($contenido_tematico)-1;
                    @endphp
                    @foreach ($contenido_tematico as $key=>$moist)
                        @if($moist->nivel == 1)
                            @php
                                if($key != 0){
                                    $modulo++;
                                }
                                $id_principal = $moist->id;
                                $presencial = explode(':',$moist->duracion);
                                $sincrono = explode(':',$moist->sincrona);
                                $asincrono = explode(':',$moist->asincrona);
                            @endphp
                            <tr>
                                <td>
                                    @if($moist->nivel == 1 && !is_null($moist->numeracion) && $moist->numeracion[0] != '0') Módulo  {{$moist->numeracion[0]}} @endif
                                    <br>
                                    {{$moist->nombre_modulo}}<br>
                                    @if(isset($contenido_tematico[$key+1]) && $contenido_tematico[$key+1]->id_parent == $id_principal)
                                        @if($contenido_tematico[$key+1]->numeracion[0] != '0')Submódulos  <br>@endif
                                        @foreach ($contenido_tematico as $data)
                                            @if ($id_principal == $data->id_parent)
                                                @if($data->numeracion[0] != '0'){{$data->numeracion}} @endif{{$data->nombre_modulo}}<br>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                {{-- <td>{!!$moist->estra_didac!!}</td> --}}
                                @php
                                    // $longitud = strlen($moist->estra_didac);
                                    $textoPlano = strip_tags($moist->estra_didac); // Elimina todas las etiquetas HTML
                                    $textoDecodificado = html_entity_decode($textoPlano);
                                    $longitud = strlen($textoDecodificado); // Cuenta solo los caracteres visibles
                                    $parte1 = $parte2 = '';
                                    if($longitud >= 1550){
                                        list($parte1, $parte2) = app('App\Http\Controllers\webController\CursosController')->dividirHtml($moist->estra_didac, 2);
                                    }else{
                                       $parte1 = $moist->estra_didac;
                                    }
                                @endphp

                                <td>{!!$parte1!!}</td>
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
                                        @if($sincrono[0] > 0 || $sincrono[1] > 0)
                                            @if($sincrono[0] > 0 || $sincrono[1] > 0) <br><b>Sincronas</b><br>@endif
                                            @if($sincrono[0] >= 10)
                                                {{$sincrono[0]}}
                                            @else
                                                @if ($sincrono[0]['1'] != 0)
                                                    {{$sincrono[0]['1']}}
                                                @endif
                                            @endif
                                            @if($sincrono[0] > 1)Horas @elseif($sincrono[0] == 1) Hora @endif
                                            @if($sincrono[1] > 0)
                                                @if($sincrono[1] >= 10)
                                                    {{$sincrono[1]}}
                                                @else
                                                    {{$sincrono[1]['1']}}
                                                @endif
                                                @if($sincrono[1] > 1)Minutos @elseif($sincrono[1] == 1) Minuto @endif
                                            @endif
                                        @endif
                                        @if($asincrono[0] > 0 || $sincrono[1] > 0)
                                            @if($asincrono[0] > 0 || $asincrono[1] > 0) <br><b>Asincronas</b><br>@endif
                                            @if($asincrono[0] >= 10)
                                                {{$asincrono[0]}}
                                            @else
                                                @if ($asincrono[0]['1'] != 0)
                                                    {{$asincrono[0]['1']}}
                                                @endif
                                            @endif
                                            @if($asincrono[0] > 1)Horas @elseif($asincrono[0] == 1) Hora @endif
                                            @if($asincrono[1] > 0)
                                                @if($asincrono[1] >= 10)
                                                    {{$asincrono[1]}}
                                                @else
                                                    {{$asincrono[1]['1']}}
                                                @endif
                                                @if($asincrono[1] > 1)Minutos @elseif($asincrono[1] == 1) Minuto @endif
                                            @endif
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            {{-- Agregar el otro en caso de que haya parte 2 --}}
                            @if (!empty($parte2))
                                <div  style="page-break-after: always;"></div>
                                <tr>
                                    <td></td>
                                    <td>{!!$parte2!!}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endif

                        @endif

                    @endforeach
                </tbody>
            </table>
            {{-- <div class="page-break"></div> --}}
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
