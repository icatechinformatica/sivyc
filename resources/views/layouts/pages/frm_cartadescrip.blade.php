<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Carta descriptiva | SIVyC Icatech')

    <!--seccion-->

@section('content_script_css')
    <style>
        * {
            box-sizing: border-box;
        }

        .card-header{
            font-variant: small-caps;
            background-color: #621132 !important;
            color: white;
            margin: 1.7% 1.7% 1% 1.7%;
            padding: 1.3% 39px 1.3% 39px;
            font-style: normal;
            font-size: 22px;
        }

        .card-body{
            margin: 1%;
            margin-left: 1.7%;
            margin-right: 1.7%;
            /* padding: 55px; */
            -webkit-box-shadow: 0 8px 6px -6px #999;
            -moz-box-shadow: 0 8px 6px -6px #999;
            box-shadow: 0 8px 6px -6px #999;
        }
        .card-body.card-msg{
            background-color: yellow;
            margin: .5% 1.7% .5% 1.7%;
            padding: .5% 5px .5% 25px;
        }

        body { background-color: #E6E6E6; }

        .btn, .btn:focus{ color: white; background: #12322b; font-size: 14px; border-color: #12322b; margin: 0 5px 0 5px; padding: 10px 13px 10px 13px; }
        .btn:hover { color: white; background:#2a4c44; border-color: #12322b; }

        .form-control { height: 40px; }


        #text_buscar_curso {
            height: fit-content;
            width: auto;
        }


        /* Estilo del loader */
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fondo semi-transparente */
            z-index: 9999; /* Asegura que esté por encima de otros elementos */
            /* display: none;  */
        }

        #loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            border: 6px solid #fff;
            border-top: 6px solid #621132;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }
            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .tam_area {
            width: 100%;
            box-sizing: border-box;
        }
        .negrita{

            font-weight: bold;
        }

        /*Deshabilitamos la parte de forzar mayusculas*/
        input[type=text],
        select,
        textarea {
            text-transform: none !important;
        }

        .input-text {
            width: 100%;
            margin-bottom: 10px; /* Espaciado inferior entre inputs */
        }
    </style>
@endsection

@section('content')

    <div class="card-header py-2">
        <h3>DATOS DE LA CARTA DESCRIPTIVA</h3>
    </div>

    {{-- Loader --}}
    <div id="loader-overlay">
        <div id="loader"></div>
    </div>
    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <strong>{{ session('message') }}</strong>
        </div>
    @endif

    {{-- card para el contenido --}}
    <div class="card card-body" style=" min-height:450px;">
        <div class="container-fluid">
            <div class="d-flex justify-content-end">
                <a href="{{route('cursos-catalogo.show',['id' => base64_encode($curso->id)])}}" class="btn btn-danger"><i class="fa fa-reply" aria-hidden="true"></i> REGRESAR</a>
            </div>
            @if ($tparte == 'general')
                <form action="" method="post" id="frm_primera">
                    @csrf
                    <p class="h5 text-center font-weight-bold" style="font-size:20px;">DATOS GENERALES</p>
                    <div class="col-12 row mt-4">
                        <div class="col-2">
                            <div class="col-12 px-0">
                                <div class="form-group">
                                    <label for="nombre" class="negrita">Entidad Federativa:</label>
                                    <input type="text" class="form-control" id="entidad" name="entidad" placeholder="Ingrese" value="CHIAPAS" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="nombre" class="negrita">Tipo de capacitacón:</label>
                                    <input type="text" class="form-control input-sm" id="tipocap" name="tipocap" value="{{$curso->tipo_curso}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="col-12 px-0">
                                <div class="form-group">
                                    <label for="nombre" class="negrita">Ciclo Escolar:</label>
                                    <input type="text" class="form-control input-sm" id="ciclo_esc" name="ciclo_esc" placeholder="2023-2024" value="{{ $ejercicio }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="nombre" class="negrita">Duración en horas:</label>
                                    <input type="text" class="form-control input-sm" id="duracion" name="duracion" placeholder="Ingrese" value="{{$curso->horas}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="col-12 px-0">
                                <div class="form-group">
                                    <label for="nombre" class="negrita">Programa Estrategico (en caso aplicable):</label>
                                    <input type="text" class="form-control input-sm" id="pogrm_estra" name="pogrm_estra" placeholder="Programa estrategico" value="{{ data_get($json_general, 'pogrm_estra', '')}}">
                                </div>
                                <div class="form-group">
                                    <label for="nombre" class="negrita">Campo de formación profesional:</label>
                                    <input type="text" class="form-control input-sm" id="form_profesion" name="form_profesion" placeholder="Campo de formación profesional" value="{{$curso->formacion_profesional}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="col-12 px-0">
                                <div class="form-group">
                                    <label for="nombre" class="negrita">Modalidad:</label>
                                    <input type="text" class="form-control input-sm" id="modalidad" name="modalidad" value="{{$curso->modalidad}}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="nombre" class="negrita">Especialidad:</label>
                                    <input type="text" class="form-control input-sm" id="especialidad" name="especialidad" value="{{$curso->especialidad}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 row pr-0">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="nombre" class="negrita">Perfil idoneo del instructor externo:</label>
                                    <input type="text" class="form-control input-sm" id="perfil_instruc" name="perfil_instruc" placeholder="PERFIL IDONEO DEL INSTRUCTOR EXTERNO" value="{{ data_get($json_general, 'perfil_instruc', '')}}">
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="form-group">
                                    <label for="nombre" class="negrita">Nombre del curso:</label>
                                    <input type="text" class="form-control input-sm" id="curso" name="curso" value="{{$curso->nombre_curso}}" readonly>
                                </div>
                            </div>
                        </div>
                        {{-- Aprendizaje esperado --}}
                        <div class="col-12">
                            <div class="form-group">
                                <label for="nombre" class="negrita">Aprendizaje esperado:</label>
                                <textarea id="aprendizaje_esp" name="aprendizaje_esp" class="tam_area" placeholder="Aprendizaje esperado">{{ data_get($json_general, 'aprendizaje_esp', '')}}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="nombre" class="negrita">Objetivos especificos por tema:</label>
                                {{-- <textarea name="" id="obj_especificos" rows="3" class="tam_area" placeholder="Objetivos especificos por tema">{{ data_get($json_general, 'obj_especificos', '')}}</textarea> --}}
                                {{-- <textarea name="" id="obj_especificos" class="tam_area"></textarea> --}}
                                <textarea id="obj_especificos" name="obj_especificos" class="tam_area" placeholder="Objetivos especificos por tema">{{ data_get($json_general, 'obj_especificos', '')}}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="nombre" class="negrita">Transversalidad con otros cursos:</label>
                                <textarea name="transversalidad" id="transversalidad" rows="2" class="tam_area" placeholder="Transversalidad con otros cursos">{{ data_get($json_general, 'transversalidad', '')}}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="nombre" class="negrita">Publico o personal a quien va dirigido el curso:</label>
                                <textarea name="dirigido" id="dirigido" class="tam_area"  placeholder="Publico o personal a quien va dirigido el curso">{{ data_get($json_general, 'dirigido', '')}}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="nombre" class="negrita">Proceso de evaluación:</label>
                                <textarea name="proces_evalua" id="proces_evalua" class="tam_area" placeholder="Proceso de evaluación">{{ data_get($json_general, 'proces_evalua', '')}}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="nombre" class="negrita">Observaciones:</label>
                                <textarea name="observaciones" id="observaciones" rows="3" class="tam_area" placeholder="Observaciones">{{ data_get($json_general, 'observaciones', '')}}</textarea>
                            </div>
                        </div>
                        <input type="hidden" name="id_curso" value="{{ $curso->id }}">
                        {{-- <div class="col-12">
                            <button class="btn float-right" id="btn_save_uno" onclick="guardarParteUno('{{$curso->id}}', '1')">GUARDAR INFORMACIÓN</button>
                        </div> --}}
                        <div class="col-12">
                            <button class="btn float-right" id="btn_primera_parte">GUARDAR INFORMACIÓN</button>
                        </div>
                    </div>
                </form>
            @elseif($tparte == 'tematico')
                <p class="text-center font-weight-bold" style="font-size:20px;">CONTENIDO TEMATICO</p>
                <form action=""  method="post" id="frm_segunda">
                    @csrf
                    <div class="col-9 px-0 justify-content-start" id="frm_tematico">
                        <div class="form-group">
                            <label for="name_modulo">Nombre del modulo</label>
                            <input type="text" class="form-control" name="name_modulo" id="name_modulo" placeholder="1. Generalidades de facturacion electronica">
                            <input type="hidden" name="id_modupd" id="id_modupd">
                        </div>
                    </div>

                    <div class="col-12 row">
                        <div class="col-8" style="border: 2px solid black">
                            <p class="text-center font-weight-bold">Submodulos</p>
                            <textarea name="submodulos" id="submodulos" rows="7" class="tam_area" placeholder="1.1 Conceptos básicos de la facturación electrónica."></textarea>
                            <input type="hidden" name="ids_subs" id="ids_subs" value="0">
                        </div>
                        <div class="col-3 ml-2" style="border: 2px solid black">
                            <p class="text-center font-weight-bold">Duración</p>
                            <div class="d-flex d-row justify-content-center">
                                <div class="form-group">
                                    {{-- <span class="d-block text-center">Duración</span> --}}
                                    <div class="form-row">
                                        <div class="col">
                                            <label for="curso_hora">Horas</label>
                                            <input type="number" class="form-control form-control-sm" name="curso_hora" id="curso_hora" placeholder="Hrs" min="0">
                                        </div>
                                        <div class="col">
                                            <label for="curso_minuto">Minutos</label>
                                            <input type="number" class="form-control form-control-sm" name="curso_minuto" id="curso_minuto" placeholder="Min" min="0" max="59">
                                        </div>
                                    </div>
                                    {{-- a distancia --}}
                                    <span class="d-block text-center font-weight-bold mt-2">A Distancia</span>
                                    <span class="d-block text-center">(Sincronas)</span>
                                    <div class="form-row">
                                        <div class="col">
                                            <input type="number" class="form-control form-control-sm mb-1" name="hora_sincro" id="hora_sincro" placeholder="Hrs" min="0">
                                        </div>
                                        <div class="col">
                                            <input type="number" class="form-control form-control-sm mb-1" name="minuto_sincro" id="minuto_sincro" placeholder="Min" min="0" max="59">
                                        </div>
                                    </div>
                                    <span class="d-block text-center">(Asincronas)</span>
                                    <div class="form-row">
                                        <div class="col">
                                            <input type="number" class="form-control form-control-sm mb-1" name="hora_asincro" id="hora_asincro" placeholder="Hrs" min="0">
                                        </div>
                                        <div class="col">
                                            <input type="number" class="form-control form-control-sm mb-1" name="minuto_asincro" id="minuto_asincro" placeholder="Min" min="0" max="59">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 row mt-2">
                        <div class="col-6 px-0" style="border: 2px solid black">
                            <p class="text-center font-weight-bold">Estrategias Didácticas</p>
                            <textarea name="estra_dida" id="estra_dida" rows="7" class="tam_area" placeholder=""></textarea>
                        </div>
                        <div class="col-5 ml-2 px-0" style="border: 2px solid black">
                            <p class="text-center font-weight-bold">Proceso de Evaluación</p>
                            <textarea name="proceso_evalua" id="proceso_evalua" rows="7" class="tam_area" placeholder=""></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="id_curso2" value="{{ $curso->id }}">
                    <button id="btn_segunda_parte" class="btn mt-3">GUARDAR INFORMACIÓN</button>
                </form>



                <hr style="border-color:dimgray">
                <span style="position:absolute; right: 8%; font-size: 18px; font-weight:bold; color:#621132">{{$tFormatHour != '' ? 'Horas capturadas: '.$tFormatHour : ''}} </span>
                <p class="font-weight-bold text-center" style="font-size:20px;">ELEMENTOS GUARDADOS</p>
                {{-- Imprimir datos del contenido tematico --}}
                <div class="col-12">
                    @if (count($modulo_first) > 0)
                        @foreach ($modulo_first as $key => $valor)
                            <div class="col-9 px-0 mt-3 justify-content-start">
                                <p><b>Titulo / Nombre del modulo:</b> {{$valor->nombre_modulo}}</p>
                            </div>

                            <div class="col-12 row justify-content-between">
                                <div class="d-flex flex-column">
                                    <button class="btn-sm btn-outline-danger" onclick="edit_delete('{{$valor->id}}', '{{$valor->id_curso}}', 'eliminar')"><i class="fa fa-times fa-2x" aria-hidden="true"></i></button>
                                    <button class="btn-sm btn-outline-warning mt-2" onclick="edit_delete('{{$valor->id}}', '{{$valor->id_curso}}', 'editar')"><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true"></i></button>
                                </div>
                                <div class="col-3 px-0" style="border: 2px solid black">
                                    <p class="text-center font-weight-bold" style="background-color: #999; color:#fff;">Contenido Tematico</p>
                                    <div class="px-2">
                                        @if (count($res_tematico[$key]) > 0)
                                            @foreach ($res_tematico[$key] as $subs)
                                                <p class="my-0 mt-2 font-italic font-weight-bold">* {{$subs->numeracion.' '.$subs->nombre_modulo}}</p>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="col-3 px-0" style="border: 2px solid black">
                                    <p class="text-center font-weight-bold" style="background-color: #999; color:#fff;">Estrategias Didácticas</p>
                                    <div class="px-1">{!! $valor->estra_didac !!}</div>
                                </div>
                                <div class="col-3 px-0" style="border: 2px solid black">
                                    <p class="text-center font-weight-bold" style="background-color: #999; color:#fff;">Proceso de Evaluación</p>
                                    <div class="px-1">{!! $valor->process_eval !!}</div>
                                </div>
                                <div class="col-2 px-0" style="border: 2px solid black">
                                    <p class="text-center font-weight-bold" style="background-color: #999; color:#fff;">Duración (En Horas)</p>
                                    @php
                                        $duracion_h = \Carbon\Carbon::parse($valor->duracion);
                                        $duracion_i = \Carbon\Carbon::parse($valor->sincrona);
                                        $duracion_j = \Carbon\Carbon::parse($valor->asincrona);

                                        $hours_h = $duracion_h->hour; $minutes_h = $duracion_h->minute;
                                        $hours_i = $duracion_i->hour; $minutes_i = $duracion_i->minute;
                                        $hours_j = $duracion_j->hour; $minutes_j = $duracion_j->minute;

                                        $time_1 = $time_2 = $time_3 = '';
                                        if ($hours_h > 1 && $minutes_h == 0) {$time_1 = $duracion_h->format('H:i') . ' HORAS';}
                                        elseif ($minutes_h > 1 && $hours_h == 0) {$time_1 = $duracion_h->format('H:i') . ' MINUTOS';}
                                        else {$time_1 = $duracion_h->format('H:i') . ' HORAS';}

                                        if ($hours_i > 1) {$time_2 = $duracion_i->format('H:i') . ' HORAS';}
                                        elseif ($minutes_i > 1) {$time_2 = $duracion_i->format('H:i') . ' MINUTOS';}

                                        if ($hours_j > 1) {$time_3 = $duracion_j->format('H:i') . ' HORAS';}
                                        elseif ($minutes_j > 1) {$time_3 = $duracion_j->format('H:i') . ' MINUTOS';}

                                    @endphp
                                    {{-- <div class="text-center">{{$valor->duracion}}</div> --}}
                                    <div class="text-center">{{$time_1}}</div>
                                    @if ($valor->sincrona != '00:00:00' && $valor->asincrona != '00:00:00')
                                        <p class="text-center font-weight-bold mt-2 mb-0">A distancia</p>
                                        <p class="text-center mb-0">(Sincrona)</p>
                                        <p class="text-center">{{$time_2}}</p>
                                        <p class="text-center mb-0">(Asincrona)</p>
                                        <p class="text-center">{{$time_3}}</p>
                                        {{-- <div class="text-center">{{$valor->duracion}}</div> --}}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

            @elseif($tparte == 'didactico')
                <form action="" method="post" id="frm_tercera">
                    @csrf
                    <p class="h5 text-center font-weight-bold" style="font-size:20px;">RECURSOS DIDÁCTICOS</p>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="nombre" class="negrita">ELEMENTOS DE APOYO:</label>
                            <textarea name="elem_apoyo" id="elem_apoyo" rows="5" class="tam_area" placeholder="Describe los elementos de apoyo">{{ data_get($json_didactico, 'elem_apoyo', '')}}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="nombre" class="negrita">Auxiliares de la enseñanza:</label>
                            <textarea name="auxiliares_ense" id="auxiliares_ense" rows="5" class="tam_area" placeholder="Auxiliares de enseñanza">{{ data_get($json_didactico, 'auxiliares_ense', '')}}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="nombre" class="negrita">Referencias:</label>
                            <textarea name="referencias" id="referencias" rows="5" class="tam_area" placeholder="Referencias">{{ data_get($json_didactico, 'referencias', '')}}</textarea>
                        </div>
                    </div>
                    <input type="hidden" name="id_curso3" value="{{$curso->id}}">
                    {{-- <div class="col-12">
                        <button class="btn float-right" id="btn_save_uno" onclick="guardarParteUno('{{$curso->id}}', '3')">GUARDAR INFORMACIÓN</button>
                    </div> --}}
                    <div class="col-12">
                        <button class="btn float-right" id="btn_tercera_parte">GUARDAR INFORMACIÓN</button>
                    </div>
                </form>

            @else
                <div class="text-center p-5 bg-danger">
                    <h5> <b>¡ERROR AL INTENTAR CARGAR LOS DATOS DEL FORMULARIO!</b></h5>
                </div>
            @endif
            {{-- <hr style="border-color: #ddd; border-width: 2px; margin: 10px 0;"> --}}

        </div>
    </div>
    {{-- termino del card --}}


    {{-- Modal de mensaje de alertas --}}
    <div class="modal fade" id="modalp" tabindex="-1" role="dialog" aria-labelledby="modalp" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #343a40; color:#fff">
                    <p class="modal-title font-weight-bold text-center" style="font-size:18px;" id="">¡Mensaje!</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p id="mensaje_modal" style="font-size:16px;"></p>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script_content_js')
    <script src="{{asset('vendor/ckeditor5-decoupled-document/ckeditor.js') }}"></script>
    <script language="javascript">
        //Ocultar loader cuando cargue la pagina
        window.addEventListener('load', function() {
            loader('hide');
        });

        //Iniciamos los objetos para el estilo de textos
        let estraDidaEditor, procesoEvaluaEditor;
        function objetos_ckeditor(parte) {
            switch (parte) {
                case "general": //Parte 1
                    ClassicEditor.create(document.querySelector('#obj_especificos'),{
                        toolbar: {items: ['bold', 'italic', 'underline', 'alignment', 'fontColor', 'backColor', '|', 'bulletedList', 'numberedList','|', 'indent', 'outdent', '|', 'undo', 'redo']}
                    });
                    ClassicEditor.create(document.querySelector('#aprendizaje_esp'),{
                        toolbar: {items: ['bold', 'italic', 'underline', 'alignment', 'fontColor', 'backColor', '|', 'bulletedList', 'numberedList','|', 'indent', 'outdent', '|', 'undo', 'redo']}
                    });
                    ClassicEditor.create(document.querySelector('#transversalidad'),{
                        toolbar: {items: ['bold', 'italic', 'underline', 'alignment', 'fontColor', 'backColor', '|', 'bulletedList', 'numberedList','|', 'indent', 'outdent', '|', 'undo', 'redo']}
                    });
                    ClassicEditor.create(document.querySelector('#dirigido'),{
                        toolbar: {items: ['bold', 'italic', 'underline', 'alignment', 'fontColor', 'backColor', '|', 'bulletedList', 'numberedList','|', 'indent', 'outdent', '|', 'undo', 'redo']}
                    });
                    ClassicEditor.create(document.querySelector('#proces_evalua'),{
                        toolbar: {items: ['bold', 'italic', 'underline', 'alignment', 'fontColor', 'backColor', '|', 'bulletedList', 'numberedList','|', 'indent', 'outdent', '|', 'undo', 'redo']}
                    });
                    ClassicEditor.create(document.querySelector('#observaciones'),{
                        toolbar: {items: ['bold', 'italic', 'underline', 'alignment', 'fontColor', 'backColor', '|', 'bulletedList', 'numberedList','|', 'indent', 'outdent', '|', 'undo', 'redo']}
                    });
                    break;
                case "tematico": //Parte 2
                    ClassicEditor
                    .create(document.querySelector('#estra_dida'),{
                        toolbar: {items: ['bold', 'italic', 'underline', 'alignment', 'fontColor', 'backColor', '|', 'bulletedList', 'numberedList','|', 'indent', 'outdent', '|', 'undo', 'redo']}
                    })
                    .then(editor => {estraDidaEditor = editor;})
                    .catch(error => {console.error(error);});

                    ClassicEditor.create(document.querySelector('#proceso_evalua'),{
                        toolbar: {items: ['bold', 'italic', 'underline', 'alignment', 'fontColor', 'backColor', '|', 'bulletedList', 'numberedList','|', 'indent', 'outdent', '|', 'undo', 'redo']}
                    })
                    .then(editor => {procesoEvaluaEditor = editor;})
                    .catch(error => {console.error(error);});
                    break;
                case "didactico": //Parte 3
                    ClassicEditor.create(document.querySelector('#elem_apoyo'),{
                        toolbar: {items: ['bold', 'italic', 'underline', 'alignment', 'fontColor', 'backColor', '|', 'bulletedList', 'numberedList','|', 'indent', 'outdent', '|', 'undo', 'redo']}
                    });
                    ClassicEditor.create(document.querySelector('#auxiliares_ense'),{
                        toolbar: {items: ['bold', 'italic', 'underline', 'alignment', 'fontColor', 'backColor', '|', 'bulletedList', 'numberedList','|', 'indent', 'outdent', '|', 'undo', 'redo']}
                    });
                    ClassicEditor.create(document.querySelector('#referencias'),{
                        toolbar: {items: ['bold', 'italic', 'underline', 'alignment', 'fontColor', 'backColor', '|', 'bulletedList', 'numberedList','|', 'indent', 'outdent', '|', 'undo', 'redo']}
                    });
                    break;
                default:
                    break;
            }
        }

        // Ejecutar la función automáticamente cuando la página haya cargado
        document.addEventListener('DOMContentLoaded', function() {
            let parte = @json($tparte); // Pasamos el valor de Laravel a JavaScript
            objetos_ckeditor(parte);
        });


        $(document).ready(function(){

            /*Deshabilitamos la prate de convertir a mayusculas*/
            $("input[type=text], textarea, select").off("keyup");


            $('#btn_primera_parte').click(function() {
                if(confirm("Esta seguro de guardar los datos?")==true){
                        $('#frm_primera').attr('action', "{{route('cursos-catalogo.saveparteuno')}}"); $('#frm_primera').submit();
                }else{
                    return false;
                }
            });

            $('#btn_segunda_parte').click(function() {
                // Verificar y asignar valor a los campos directamente
                if (isEmpty($("#curso_hora").val())) {$("#curso_hora").val('0');} if (isEmpty($("#curso_minuto").val())) {$("#curso_minuto").val('0');}
                if (isEmpty($("#hora_sincro").val())) {$("#hora_sincro").val('0');} if (isEmpty($("#minuto_sincro").val())) {$("#minuto_sincro").val('0');}
                if (isEmpty($("#hora_asincro").val())) {$("#hora_asincro").val('0');} if (isEmpty($("#minuto_asincro").val())) {$("#minuto_asincro").val('0');}

                //Valido los campos de fechas que coincidan
                let dura_hora = $("#curso_hora").val(); let dura_minuto = $("#curso_minuto").val();
                let sinc_hora = $("#hora_sincro").val(); let sinc_minuto = $("#minuto_sincro").val();
                let asinc_hora = $("#hora_asincro").val(); let asinc_minuto = $("#minuto_asincro").val();

                // Concatenar los valores en formato "hora:minuto"
                let dura_total = dura_hora + ":" + dura_minuto;
                let sinc_total = sinc_hora + ":" + sinc_minuto;
                let asinc_total = asinc_hora + ":" + asinc_minuto;

                // Convertir los valores a minutos
                let minutosVal1 = convertirAMinutos(dura_total);
                let minutosVal2 = convertirAMinutos(sinc_total);
                let minutosVal3 = convertirAMinutos(asinc_total);

                // Sumar los minutos de val2 y val3
                let sumaMinutos = minutosVal2 + minutosVal3;

                if (minutosVal1 === 0) {alert("El campo duración esta vacia");return false;}

                // Validar si la suma coincide con val1
                if ((minutosVal1 === sumaMinutos)) {console.log("Es valido, continua el proceso");}
                else if(sumaMinutos === 0){console.log("Es valido, continua el proceso");}
                else {alert("Verifica que la suma de fechas Sincronas y Asincronas coincidan con la duracion asignada."); return false;}

                if(confirm("Esta seguro de guardar los datos?")==true){
                        $('#frm_segunda').attr('action', "{{route('cursos-catalogo.savepartedos')}}"); $('#frm_segunda').submit();
                }else{
                    return false;
                }
            });

            $('#btn_tercera_parte').click(function() {
                if(confirm("Esta seguro de guardar los datos?")==true){
                        $('#frm_tercera').attr('action', "{{route('cursos-catalogo.savepartetres')}}"); $('#frm_tercera').submit();
                }else{
                    return false;
                }
            });

        });


        function loader(make) {
            if(make == 'hide') make = 'none';
            if(make == 'show') make = 'block';
            document.getElementById('loader-overlay').style.display = make;
        }

        function modal(mensaje, accion) {
            if(accion == 'show'){
                $("#mensaje_modal").html(mensaje);
                $("#modalp").modal("show");
            }else{
                $("#modalp").modal("hide");
            }
        }

        function edit_delete(indice, id_curso, accion) {
            if (accion == 'eliminar') {
                if (!confirm('¿Estás seguro de eliminar este elemento?')) {return false;}
            }
            loader('show');
            data = {
                "_token": $("meta[name='csrf-token']").attr("content"),
                "indice": indice,
                "accion": accion,
                "id_curso": id_curso
            }
            $.ajax({
                type:"post",
                url: "{{ route('cursos-catalogo.editcartadecrip') }}",
                data: data,
                dataType: "json",
                success: function (response) {
                    //Accion Eliminar
                    if(response.status == 200 && response.accion == "eliminar"){
                        alert("¡Registro eliminado!")
                        location.reload();
                    }

                    //Accion Editar
                    if (response.accion == 'editar') {
                        console.log(response);
                        $("#submodulos").val('');
                        $("#name_modulo").val('');
                        $("#curso_hora").val('');
                        $("#curso_minuto").val('');
                        $("#hora_sincro").val('');
                        $("#minuto_sincro").val('');
                        $("#hora_asincro").val('');
                        $("#minuto_asincro").val('');
                        $("#estra_dida").val('');
                        $("#proceso_evalua").val('');

                        //Agregamos valores a los campos
                        $("#name_modulo").val(response.datos_uno.nombre_modulo);
                        $("#curso_hora").val(response.datos_uno.hr_dura);
                        $("#curso_minuto").val(response.datos_uno.min_dura);
                        $("#hora_sincro").val(response.datos_uno.hr_sinc);
                        $("#minuto_sincro").val(response.datos_uno.min_sinc);
                        $("#hora_asincro").val(response.datos_uno.hr_asin);
                        $("#minuto_asincro").val(response.datos_uno.min_asin);
                        $("#id_modupd").val(response.datos_uno.id);
                        if (response.datos_uno.estra_didac != null) {estraDidaEditor.setData(response.datos_uno.estra_didac);}
                        if (response.datos_uno.process_eval != null) {procesoEvaluaEditor.setData(response.datos_uno.process_eval);}

                        let idObject = {};
                        if (response.datos_dos.length != 0) {
                            Object.entries(response.datos_dos).forEach(([key, value]) => {
                                idObject[key] = value.id;
                                $("#submodulos").val(function(index, val) {
                                    return val + value.numeracion +' '+ value.nombre_modulo + "\r\n";  // Añade una nueva línea después de cada valor
                                });
                            });
                        }
                        $("#ids_subs").val(JSON.stringify(idObject));
                        // Hacer enfoque o scroll hacia el elemento div principal
                        $("#frm_tematico")[0].scrollIntoView({ behavior: "smooth" });
                        loader('hide');

                    }
                }
            });

        }

        // Función para convertir una hora en formato "HH:MM" a minutos totales
        function convertirAMinutos(hora) {
            let [horas, minutos] = hora.split(':').map(Number);
            return horas * 60 + minutos;
        }

        //Funcion para validar campos
        function isEmpty(value) {
            return value == null || value === '';
        }



    </script>
@endsection
