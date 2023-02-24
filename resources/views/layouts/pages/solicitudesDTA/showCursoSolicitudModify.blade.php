@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Modificación de un curso | SIVyC Icatech')

@section('content_script_css')
    <link rel="stylesheet" href="{{ asset('fullCalendar/core/main.css') }}">
    <link rel="stylesheet" href="{{ asset('fullCalendar/daygrid/main.css') }}">
    <link rel="stylesheet" href="{{ asset('fullCalendar/list/main.css') }}">
    <link rel="stylesheet" href="{{ asset('fullCalendar/timegrid/main.css') }}">
@endsection

@section('content')

    <div class="container-fluid px-5 mt-4">
        {{-- titulo --}}
        <div class="row pb-2">
            <div class="col text-center">
                <h3><strong>MODIFICACIÓN DEL CURSO {{ $curso[0]->curso }}</strong></h3>
                <h5><strong>{{$curso[0]->opcion_solicitud}}</strong></h5>
            </div>
        </div>

        <div class="row my-5 d-flex align-items-center">
            <div class="col-8">
                <div class="form-group">
                    <label for="instructor" class="control-label">INSTRUCTOR ASIGNADO</label>
                    <select name="instructor" id="instructor" class="custom-select">
                        {{-- <option value="">Seleccionar instructor</option> --}}
                        @if ($curso[0]->opcion_solicitud == 'REPROGRAMACIÓN FECHA/HORA')
                            @foreach ($instructores as $instructor)
                                @if ($instructor->id == $curso[0]->id_instructor)
                                    <option selected
                                        value="{{ $instructor->id . ',' . $instructor->rfc . ' - ' . $instructor->nombre . ' ' . $instructor->apellidoPaterno . ' ' . $instructor->apellidoMaterno }}">
                                        {{ $instructor->rfc . ' - ' . $instructor->nombre . ' ' . $instructor->apellidoPaterno . ' ' . $instructor->apellidoMaterno }}
                                    </option>
                                @endif
                            @endforeach
                        @else
                            @foreach ($instructores as $instructor)
                                <option {{ $instructor->id == $curso[0]->id_instructor ? 'selected' : '' }}
                                    value="{{ $instructor->id . ',' . $instructor->rfc . ' - ' . $instructor->nombre . ' ' . $instructor->apellidoPaterno . ' ' . $instructor->apellidoMaterno }}">
                                    {{ $instructor->rfc . ' - ' . $instructor->nombre . ' ' . $instructor->apellidoPaterno . ' ' . $instructor->apellidoMaterno }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-4 d-flex d-flex justify-content-center">
                <button id="btnShowCalendar" type="button" class="btn btn-info">Agendar</button>
            </div>
        </div>

        <hr>

        <form method="POST" id="formRespuesta" action="{{route('solicitudesDTA.saveCambios')}}" enctype="multipart/form-data">
            @csrf

            <div class="form-group row d-flex align-items-end">
                <input class="d-none" type="text" id="numSolicitud" name="numSolicitud" value="{{$curso[0]->num_solicitud}}">
                <input class="d-none" type="text" id="idCurso" name="idCurso" value="{{$curso[0]->id}}">
                <input class="d-none" type="text" class="form-control" id="id_solicitud" name="id_solicitud" value="{{$curso[0]->id_solicitud}}">
                
                <div class="form-group col">
                    <label for="num_respuesta" class="control-label">NÚMERO DE RESPUESTA</label>
                    <input type="text" class="form-control" id="num_respuesta" name="num_respuesta"
                        placeholder="NÚMERO DE RESPUESTA">
                </div>
                <div class="form-group col">
                    <label for="fecha_respuesta" class="control-label">FECHA DE RESPUESTA</label>
                    <input type='text' id="fecha_respuesta" autocomplete="off" readonly="readonly"
                        name="fecha_respuesta" class="form-control datepicker" placeholder="FECHA DE RESPUESTA">
                </div>
                <div class="form-group col-12">
                    <label for="observacionesRes" class="control-label">OBSERVACIONES</label>
                    <textarea class="form-control" name="observacionesRes" id="observacionesRes" cols="30" placeholder="OBSERVACIONES" rows="2"></textarea>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col text-right">
                    <button id="btnNoProcede" type="button" class="btn btn-danger">No procede</button>
                    <button type="submit" class="btn btn-primary">GUARDAR CAMBIOS</button>
                </div>
            </div>
        </form>
    </div>

    <!-- modal para mostrar el calendario -->
    <div class="modal fade" id="modalCalendar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-notify modal-info" role="document">
            <!--Content-->
            <div class="modal-content text-center">
                <!--Header-->
                <div class="modal-header d-flex justify-content-center bg-primary" id="cabezaModal">
                    <p id="titleCalendar" class="heading font-weight-bold h4 text-white text-center"></p>
                    <p id="subTitle" class="heading h6 text-info text-center"></p>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!--Body-->
                <div class="modal-body">
                    <div aria-live="assertive" aria-atomic="true" style="position: absolute; top: 0; left: 0;" role="alert"
                        class="toast mt-3 mr-3" data-autohide="false">
                        <div class="toast-header bg-primary">
                            <strong id="titleToast" class="mr-auto text-white"></strong>
                            <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast"
                                aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div id="msgVolumen" class="toast-body">
                        </div>
                    </div>

                    {{-- action="javascript:void(0)" --}}
                    <div class="row d-flex align-items-center">
                        <div class="col-12 col-md-6">
                            <form id="formCalendario">
                                <div class="row">
                                    <input type="text" name="txtId" id="txtId" class="d-none">
                                    <div class="col">
                                        <!-- fecha inicial -->
                                        <div class="form-group">
                                            <label for="fecha_firma" class="control-label">FECHA DE INCIO</label>
                                            <input type='text' id="fecha_firma" autocomplete="off" readonly="readonly"
                                                name="fecha_firma" class="form-control datepicker" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <!-- Fecha conclusion -->
                                        <div class="form-group">
                                            <label for="fecha_termino" class="control-label">FECHA DE TERMINO</label>
                                            <input type='text' id="fecha_termino" autocomplete="off" readonly="readonly"
                                                name="fecha_termino" class="form-control datepicker" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">HORA INICIO</label>
                                            <input type="time" class="form-control" name="txtHora" id="txtHora" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">HORA TERMINO</label>
                                            <input type="time" class="form-control" name="txtHoraTermino"
                                                id="txtHoraTermino" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col">
                                        <label for="observaciones" class="control-label">OBSERVACIONES</label>
                                        <textarea name="observaciones" class="form-control" id="observaciones"
                                            required></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="row">
                                <div class="col">
                                    <div id='calendar'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Footer-->
                <div class="modal-footer flex-center py-2">
                    <div class="col d-flex justify-content-start">
                        <button id="btnClean" type="button" class="btn btn-info">Limpiar campos</button>
                    </div>
                    <div class="row">
                        <div class="col">
                            <button id="btnAgregar" type="button" class="btn btn-success">Agregar</button>
                            <button id="btnModificar" class="btn btn-warning">Modificar</button>
                            <button id="btnBorrar" class="btn btn-danger">Borrar</button>
                            <button type="button" data-dismiss="modal" class="btn btn-outline-danger">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--/.Content-->
        </div>
    </div>

    <!-- Modal no procede -->
    <div class="modal fade" id="modalNoProcede" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                
                <form id="formNoProcede" enctype="multipart/form-data" action="{{route('solicitudesDTA.noProcede')}}" method="post">
                    @csrf

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">NO PROCEDE LA MODIFICACIÓN DEL CURSO</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        {{-- archivo --}}
                        <input class="d-none" type="text" id="numSolicitudNo" name="numSolicitudNo" value="{{$curso[0]->num_solicitud}}">
                        <input class="d-none" type="text" id="idCursoNo" name="idCursoNo" value="{{$curso[0]->id}}">
                        <input class="d-none" type="text" class="form-control" id="id_solicitudNo" name="id_solicitudNo" value="{{$curso[0]->id_solicitud}}">
                        <div class="form-group row d-flex align-items-end">
                            <div class="form-group col-6">
                                <label for="num_respuestaNo" class="control-label">NÚMERO DE RESPUESTA</label>
                                <input type="text" class="form-control" id="num_respuestaNo" name="num_respuestaNo"
                                    placeholder="NÚMERO DE RESPUESTA">
                            </div>
                            <div class="form-group col-6">
                                <label for="fecha_respuestaNo" class="control-label">FECHA DE RESPUESTA</label>
                                <input type='text' id="fecha_respuestaNo" autocomplete="off" readonly="readonly"
                                    name="fecha_respuestaNo" class="form-control datepicker" placeholder="FECHA DE RESPUESTA">
                            </div>
                            <div class="form-group col-12">
                                <label for="observacionesNo" class="control-label">OBSERVACIONES</label>
                                <textarea class="form-control" name="observacionesNo" id="observacionesNo" 
                                    placeholder="OBSERVACIONES" cols="30" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">CANCELAR</button>
                        <button type="submit" class="btn btn-danger">NO PROCEDE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script_content_js')
    <script src="{{ asset('fullCalendar/core/main.js') }}" defer></script>
    <script src="{{ asset('fullCalendar/core/locales-all.js') }}" defer></script>
    <script src="{{ asset('fullCalendar/interaction/main.js') }}" defer></script>
    <script src="{{ asset('fullCalendar/daygrid/main.js') }}" defer></script>
    <script src="{{ asset('fullCalendar/list/main.js') }}" defer></script>
    <script src="{{ asset('fullCalendar/timegrid/main.js') }}" defer></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>


    <script>
        var calendarEl = document.getElementById('calendar');
        var calendar, idEvento, objEvento;
        var idIstructor = {!! json_encode($curso[0]->id_instructor) !!};
        var idCurso = {!! json_encode($curso[0]->id) !!};
        var isIgualInstructor;

        $('#btnShowCalendar').click(function(e) {
            if (document.getElementById('instructor').value) {
                limpiarFormulario();
                inicializarCalendar();
                $('#titleCalendar').html(document.getElementById('instructor').value.split(',')[1]);
                if (document.getElementById('instructor').value.split(',')[0] == idIstructor) {
                    $('#subTitle').html('');
                    isIgualInstructor = true;
                } else {
                    $('#subTitle').html('Cambio de instructor');
                    isIgualInstructor = false;
                }
                $("#modalCalendar").modal("show");
            } else {
                console.log('no se selecciono instructor');
            }
        });

        function limpiarFormulario() {
            $('#formCalendario')[0].reset();
            idEvento = null;
            objEvento = null;

            $('#btnAgregar').prop('disabled', false);
            $('#btnModificar').prop('disabled', true);
            $('#btnBorrar').prop('disabled', true);
        }

        function inicializarCalendar() {
            objEvento = [];
            nuevoEvento = [];
            if (calendar) {
                calendar.destroy();
            }
            calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['dayGrid', 'interaction', 'timeGrid', 'list'],

                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth, timeGridWeek, timeGridDay',
                },

                eventClick: function(info) {
                    $('#btnAgregar').prop('disabled', true);
                    $('#btnModificar').prop('disabled', false);
                    $('#btnBorrar').prop('disabled', false);

                    // $('#txtTitulo').val(info.event.title);

                    // fecha y hora inicial
                    mes = (info.event.start.getMonth() + 1);
                    dia = (info.event.start.getDate());
                    anio = (info.event.start.getFullYear());
                    mes = (mes < 10) ? '0' + mes : mes;
                    dia = (dia < 10) ? '0' + dia : dia;
                    minutos = info.event.start.getMinutes();
                    hora = info.event.start.getHours();
                    minutos = (minutos < 10) ? '0' + minutos : minutos;
                    hora = (hora < 10) ? '0' + hora : hora;
                    horario = (hora + ':' + minutos);

                    // fecha y hora final
                    mes2 = (info.event.end.getMonth() + 1);
                    dia2 = (info.event.end.getDate());
                    anio2 = (info.event.end.getFullYear());
                    mes2 = (mes2 < 10) ? '0' + mes2 : mes2;
                    dia2 = (dia2 < 10) ? '0' + dia2 : dia2;
                    minutos2 = info.event.end.getMinutes();
                    hora2 = info.event.end.getHours();
                    minutos2 = (minutos2 < 10) ? '0' + minutos2 : minutos2;
                    hora2 = (hora2 < 10) ? '0' + hora2 : hora2;
                    horario2 = (hora2 + ':' + minutos2);

                    idEvento = info.event.id;
                    $('#txtId').val(info.event.id);
                    $('#fecha_firma').val(dia + '-' + mes + '-' + anio);
                    $('#txtHora').val(horario);
                    $('#fecha_termino').val(dia2 + '-' + mes2 + '-' + anio2);
                    $('#txtHoraTermino').val(horario2);
                    $('#observaciones').val(info.event.extendedProps.observaciones);
                },
                events: ("{{ url('/calendario/showEvents') }}" + ('/' + document.getElementById('instructor')
                    .value.split(',')[0])),
            });
            calendar.setOption('locale', 'es');
            calendar.render();
        }

        $('#btnAgregar').click(function() {
            isEquals = false;
            isEquals2 = false;
            objEvento = null;
            objEvento = recolectarDatos("POST");

            if ($('#fecha_firma').val() == '' || $('#txtHora').val() == '' || $('#fecha_termino').val() == '' ||
                $('#txtHoraTermino').val() == '' || $('#observaciones').val() == '') {
                objEvento = null;
                $('#titleToast').html('Campos vacios');
                $("#msgVolumen").html("Todos los campos son requeridos");
                $(".toast").toast("show");
            } else {
                if ($('#txtHora').val() < $('#txtHoraTermino').val()) {
                    // EnviarInformacion("", objEvento, 'insert');

                    calendar.getEvents().forEach(function(evento) {
                        mess = (evento['start'].getMonth() + 1);
                        messT = (evento['end'].getMonth() + 1);
                        mesc = (mess < 10) ? '0' + mess : mess;
                        mesT = (messT < 10) ? '0' + messT : messT;
                        diac = evento['start'].getDate() < 10 ? '0' + evento['start'].getDate() : evento[
                            'start'].getDate();
                        diaT = evento['end'].getDate() < 10 ? '0' + evento['end'].getDate() : evento['end']
                            .getDate();
                        anioc = evento['start'].getFullYear();
                        anioT = evento['end'].getFullYear();
                        horac = evento['start'].getHours() < 10 ? '0' + evento['start'].getHours() : evento[
                            'start'].getHours();
                        horat = evento['end'].getHours() < 10 ? '0' + evento['end'].getHours() : evento[
                            'end'].getHours();
                        minutoc = evento['start'].getMinutes() < 10 ? '0' + evento['start'].getMinutes() :
                            evento['start'].getMinutes();
                        minutot = evento['end'].getMinutes() < 10 ? '0' + evento['end'].getMinutes() :
                            evento['end'].getMinutes();

                        fechaBD = (diac + '-' + mesc + '-' + anioc);
                        fechaBdFinal = (diaT + '-' + mesT + '-' + anioT);
                        if ($('#fecha_firma').val() >= fechaBD && $('#fecha_firma').val() <= fechaBdFinal) {
                            if ($('#txtHora').val() >= (horac + ':' + minutoc) && $('#txtHora').val() <= (
                                    horat + ':' + minutot)) {
                                isEquals = true;
                                console.log('1111');
                            }
                        }
                        //
                        if ($('#fecha_termino').val() >= fechaBD && $('#fecha_termino').val() <=
                            fechaBdFinal) {
                            if ($('#txtHoraTermino').val() >= (horac + ':' + minutoc) && $(
                                    '#txtHoraTermino').val() <= (horat + ':' + minutot)) {
                                isEquals2 = true;
                            }
                        }
                    });
                    if (isEquals) {
                        objEvento = null;
                        $('#titleToast').html('Fecha incorrecta');
                        $("#msgVolumen").html("La fecha y hora de inicio ya se encuentra en uso");
                        $(".toast").toast("show");
                    } else if (isEquals2) {
                        $('#titleToast').html('Fecha incorrecta');
                        $("#msgVolumen").html("La fecha y hora de termino ya se encuentra en uso");
                        $(".toast").toast("show");
                    } else {
                        EnviarInformacion("", objEvento, 'insert');
                    }

                } else {
                    objEvento = null;
                    $('#titleToast').html('Hora incorrecta');
                    $("#msgVolumen").html("La hora de inicio debe ser menor a la hora de termino");
                    $(".toast").toast("show");
                }
            }
        });

        // boton eliminar
        $('#btnBorrar').click(function() {
            objEvento = [];
            objEvento = recolectarDatos("DELETE");
            EnviarInformacion('/' + $('#txtId').val(), objEvento, 'delete');
        });

        // boton modificar
        $('#btnModificar').click(function() {
            isEquals = false;
            isEquals2 = false;
            objEvento = null;
            objEvento = recolectarDatos("POST");

            if ($('#fecha_firma').val() == '' || $('#txtHora').val() == '' || $('#fecha_termino').val() == '' ||
                $('#txtHoraTermino').val() == '' || $('#observaciones').val() == '') {
                $('#titleToast').html('Campos vacios');
                $("#msgVolumen").html("Todos los campos son requeridos");
                $(".toast").toast("show");
            } else {
                if ($('#txtHora').val() < $('#txtHoraTermino').val()) {
                    // EnviarInformacion('/' + $('#txtId').val(), objEvento, 'update');

                    calendar.getEvents().forEach(function(evento) {
                        mess = (evento['start'].getMonth() + 1);
                        messT = (evento['end'].getMonth() + 1);
                        mesc = (mess < 10) ? '0' + mess : mess;
                        mesT = (messT < 10) ? '0' + messT : messT;
                        diac = evento['start'].getDate() < 10 ? '0' + evento['start'].getDate() : evento[
                            'start'].getDate();
                        diaT = evento['end'].getDate() < 10 ? '0' + evento['end'].getDate() : evento['end']
                            .getDate();
                        anioc = evento['start'].getFullYear();
                        anioT = evento['end'].getFullYear();
                        horac = evento['start'].getHours() < 10 ? '0' + evento['start'].getHours() : evento[
                            'start'].getHours();
                        horat = evento['end'].getHours() < 10 ? '0' + evento['end'].getHours() : evento[
                            'end'].getHours();
                        minutoc = evento['start'].getMinutes() < 10 ? '0' + evento['start'].getMinutes() :
                            evento['start'].getMinutes();
                        minutot = evento['end'].getMinutes() < 10 ? '0' + evento['end'].getMinutes() :
                            evento['end'].getMinutes();

                        fechaBD = (diac + '-' + mesc + '-' + anioc);
                        fechaBdFinal = (diaT + '-' + mesT + '-' + anioT);
                        if ($('#fecha_firma').val() >= fechaBD && $('#fecha_firma').val() <= fechaBdFinal) {
                            if ($('#txtHora').val() >= (horac + ':' + minutoc) && $('#txtHora').val() <= (
                                    horat + ':' + minutot)) {
                                if (evento['id'] != idEvento) {
                                    isEquals = true;
                                }
                            }
                        }
                        // 
                        if ($('#fecha_termino').val() >= fechaBD && $('#fecha_termino').val() <=
                            fechaBdFinal) {
                            if ($('#txtHoraTermino').val() >= (horac + ':' + minutoc) && $(
                                    '#txtHoraTermino').val() <= (horat + ':' + minutot)) {
                                if (evento['id'] != idEvento) {
                                    isEquals2 = true;
                                    console.log('2222');
                                }
                            }
                        }
                    });
                    if (isEquals) {
                        console.log('fechas iguales');
                        $('#titleToast').html('Fecha incorrecta');
                        $("#msgVolumen").html("La fecha y hora de inicio ya se encuentra en uso");
                        $(".toast").toast("show");
                    } else if (isEquals2) {
                        console.log('fechas iguales');
                        $('#titleToast').html('Fecha incorrecta');
                        $("#msgVolumen").html("La fecha y hora de termino ya se encuentra en uso");
                        $(".toast").toast("show");
                    } else {
                        EnviarInformacion('/' + $('#txtId').val(), objEvento, 'update');
                    }

                } else {
                    $('#titleToast').html('Hora incorrecta');
                    $("#msgVolumen").html("La hora de inicio debe ser menor a la hora de termino");
                    $(".toast").toast("show");
                }
            }
        });

        $('#btnClean').click(function() {
            limpiarFormulario();
        });

        function recolectarDatos(method) {
            nuevoEvento = []
            nuevoEvento = {
                title: 'pruebas',
                start: $('#fecha_firma').val() + ' ' + $('#txtHora').val(),
                end: $('#fecha_termino').val() + ' ' + $('#txtHoraTermino').val(),
                textColor: '#000000',
                observaciones: $('#observaciones').val(),
                id_instructor: document.getElementById('instructor').value.split(',')[0],
                isEquals: isIgualInstructor,
                idIstructor: idIstructor,
                idCurso: idCurso,
                '_token': $("meta[name='csrf-token']").attr("content"),
                '_method': method
            }
            return nuevoEvento;
        }

        function EnviarInformacion(accion, objEvento, tipo) {
            link = ((tipo == 'insert') ? "{{ url('/calendario/guardarEvents') }}" : (tipo == 'update') ?
                "{{ url('/calendario/updateEvents') }}" + accion : "{{ url('/calendario') }}" + accion);
            tipo2 = (tipo == 'insert') ? 'POST' : (tipo == 'update') ? 'POST' : 'GET';

            $.ajax({
                type: tipo2,
                url: link,
                data: objEvento,
                success: function(msg) {
                    console.log(msg);
                    if (tipo == 'insert' || tipo == 'update') {
                        if (msg == 'iguales') { //hay registro con la fecha y hora
                            $('#titleToast').html('Fecha incorrecta');
                            $("#msgVolumen").html("La fecha y hora de inicio ya se encuentra en uso");
                            $(".toast").toast("show");
                        } else if (msg == 'iguales2') {
                            $('#titleToast').html('Fecha incorrecta');
                            $("#msgVolumen").html("La fecha y hora de termino ya se encuentra en uso");
                            $(".toast").toast("show");
                        } else if (msg == 'duplicado') {
                            $('#titleToast').html('Datos incorrectos');
                            $("#msgVolumen").html("Los datos ingresados coinciden con registros existentes");
                            $(".toast").toast("show");
                        } else {
                            if (!isIgualInstructor) {
                                location.reload();
                            }
                            calendar.refetchEvents();
                            limpiarFormulario();
                        }
                    } else {
                        calendar.refetchEvents();
                        limpiarFormulario();
                    }
                },
                error: function(jqXHR, textStatus) {
                    console.log(textStatus);
                    console.log(jqXHR);
                    alert("Hubo un error: " + jqXHR.status);
                }
            });
        }

        // formato fechas
        var dateFormat = "dd-mm-yy",
            from = $("#fecha_firma").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: 'dd-mm-yy'
            }).on("change", function() {
                to.datepicker("option", "minDate", getDate(this));
            }),
            to = $("#fecha_termino").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: 'dd-mm-yy'
            })
            .on("change", function() {
                // from.datepicker("option", "maxDate", getDate(this));
            });

        function getDate(element) {
            var date;
            try {
                date = $.datepicker.parseDate(dateFormat, element.value);
            } catch (error) {
                date = null;
            }
            return date;
        }

        $('#formRespuesta').validate({
            rules: {
                num_respuesta: {
                    required: true
                },
                fecha_respuesta: {
                    required: true
                }
            },
            messages: {
                num_respuesta: {
                    required: 'Número de solicitud requerido'
                },
                fecha_respuesta: {
                    required: 'Fecha de solicitud requerida'
                }
            }
        });

        $("#fecha_respuesta").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            dateFormat: 'yy-mm-dd'
        });

        $('#btnNoProcede').click(function () {
            $('#modalNoProcede').modal('show');
        });

        $("#fecha_respuestaNo").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            dateFormat: 'yy-mm-dd'
        });

        $('#formNoProcede').validate({
            rules: {
                num_respuestaNo: {
                    required: true
                },
                fecha_respuestaNo: {
                    required: true
                }
            },
            messages: {
                num_respuestaNo: {
                    required: 'Número de solicitud requerido'
                },
                fecha_respuestaNo: {
                    required: 'Fecha de solicitud requerida'
                }
            }
        });

    </script>

@endsection
