@extends('theme.sivyc.layout')

@section('title', 'Alumnos | SIVyC Icatech')

@push('content_css_sign')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.css">
<link rel="stylesheet" href="{{ asset('css/grupos/agenda_fullcalendar.css') }}">
@endpush

@section('content')
<div class="card-header rounded-lg shadow d-flex justify-content-between align-items-center">
    <div class="col-md-8">
        <span>Grupos / Registro</span>
    </div>
</div>

<div class="card card-body">
    {{ html()->form('POST', route('grupos.store'))->open() }}
    <!-- Sección: Información general -->
    <div class="px-3 rounded" style="border-left: 4px solid #007bff;">
        <small class="text-primary font-weight-bold">Información general</small>
        <div class="row my-1">
            <div class="form-group col-md-2 mb-1">
                {{ html()->label('IMPARTICIÓN', 'imparticion')->class('form-label mb-1') }}
                {{ html()->select('imparticion', ['' => 'SELECCIONAR', 1 => 'PRESENCIAL', 2 =>'A DISTANCIA'])->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-2 mb-1">
                {{ html()->label('CURSO/CERTIFICACIÓN', 'tipo')->class('form-label mb-1') }}
                {{ html()->select('tipo', ['' => 'SELECCIONAR', 1 => 'CURSO', 2 => 'CERTIFICACIÓN'])->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-8 mb-1">
                {{ html()->label('CURSO', 'curso')->class('form-label mb-1') }}
                {{ html()->select('curso', ['' => 'SELECCIONAR', 1 => 'CURSO 1', 2 => 'CURSO 2'])->class('form-control form-control-sm')->required() }}
            </div>
        </div>
    </div>

    <!-- Sección: Ubicación -->
    <div class="px-3 rounded" style="border-left: 4px solid #28a745;">
        <small class="text-success font-weight-bold">Ubicación</small>
        <div class="row my-1">
            <div class="form-group col-md-3 mb-1">
                {{ html()->label('UNIDAD/ACCIÓN MÓVIL', 'unidad_accion_movil')->class('form-label mb-1') }}
                {{ html()->select('unidad_accion_movil', ['' => 'SELECCIONAR', 1 => 'UNIDAD 1', 2 => 'UNIDAD 2'])->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-3 mb-1">
                {{ html()->label('MUNICIPIO', 'municipio')->class('form-label mb-1') }}
                {{ html()->select('municipio', ['' => 'SELECCIONAR', 1 => 'MUNICIPIO 1', 2 => 'MUNICIPIO 2'])->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-3 mb-1">
                {{ html()->label('LOCALIDAD', 'localidad')->class('form-label mb-1') }}
                {{ html()->select('localidad', ['' => 'SELECCIONAR', 1 => 'LOCALIDAD 1', 2 => 'LOCALIDAD 2'])->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-3 mb-1">
                {{ html()->label('MODALIDAD', 'modalidad')->class('form-label mb-1') }}
                {{ html()->select('modalidad', ['' => 'SELECCIONAR', 1 => 'EXTENCIÓN', 2 => 'CAE'])->class('form-control form-control-sm')->required() }}
            </div>
        </div>

        <div class="row my-1">
            <div class="form-group col-md-6 mb-1">
                {{ html()->label('NOMBRE DEL LUGAR O ESPACIO FÍSICO', 'nombre_lugar')->class('form-label mb-1') }}
                {{ html()->text('nombre_lugar')->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-6 mb-1">
                {{ html()->label('CALLE Y NÚMERO', 'calle_numero')->class('form-label mb-1') }}
                {{ html()->text('calle_numero')->class('form-control form-control-sm')->required() }}
            </div>
        </div>
        <div class="row my-1">
            <div class="form-group col-md-6 mb-1">
                {{ html()->label('COLONIA O BARRIO', 'colonia')->class('form-label mb-1') }}
                {{ html()->text('colonia')->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-6 mb-1">
                {{ html()->label('CÓDIGO POSTAL', 'codigo_postal')->class('form-label mb-1') }}
                {{ html()->text('codigo_postal')->class('form-control form-control-sm') }}
            </div>
        </div>
        <div class="form-group mb-1">
            {{ html()->label('REFERENCIAS ADICIONALES', 'referencias')->class('form-label mb-1') }}
            {{ html()->textarea('referencias')->class('form-control form-control-sm')->rows(2) }}
        </div>
    </div>

    <!-- Sección: Fechas y horarios -->
    <div class="p-2 mb-2 rounded" style="border-left: 4px solid #17a2b8;">
        <small class="text-info font-weight-bold">Fechas y horarios</small>
        <div class="form-row mt-2">
            <div class="form-group col-md-2">
                {{ html()->label('FECHA INICIO', 'fecha_inicio')->class('form-label mb-1') }}
                {{ html()->date('fecha_inicio')->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-2">
                {{ html()->label('FECHA FIN', 'fecha_fin')->class('form-label mb-1') }}
                {{ html()->date('fecha_fin')->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-2">
                {{ html()->label('HORA INICIO', 'hora_inicio')->class('form-label mb-1') }}
                {{ html()->time('hora_inicio')->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-2">
                {{ html()->label('HORA FIN', 'hora_fin')->class('form-label mb-1') }}
                {{ html()->time('hora_fin')->class('form-control form-control-sm')->required() }}
            </div>
            <div class="form-group col-md-4">
                <div class="d-flex justify-content-center align-items-center h-100">
                    {!! html()->button('<i class="fa fa-calendar mr-2 rounded"></i> AGENDA', 'button')->class('btn btn-agenda btn-lg w-100')->id('btn-agenda')->toHtml() !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Sección: Representante y organización -->
    <div class="p-2 mb-2 rounded" style="border-left: 4px solid #ffc107;">
        <small class="text-warning font-weight-bold">Representante y organización</small>
        <div class="row mt-2">
            <div class="form-group col-md-4">
                {{ html()->label('ORGANIZO PUBLICO', 'organizo_publico')->class('form-label') }}
                {{ html()->select('organizo_publico', ['' => 'SELECCIONAR', 1 => 'ORGANIZO 1', 2 => 'ORGANIZO 2'])->class('form-control')->required() }}
            </div>
            <div class="form-group col-md-4">
                {{ html()->label('NOMBRE DEL REPRESENTANTE', 'nombre_representante')->class('form-label') }}
                {{ html()->text('nombre_representante')->class('form-control')->required() }}
            </div>
            <div class="form-group col-md-4">
                {{ html()->label('TELÉFONO DEL REPRESENTANTE', 'telefono_representante')->class('form-label') }}
                {{ html()->text('telefono_representante')->class('form-control')->required() }}
            </div>
        </div>
    </div>

    <!-- Sección: Opciones adicionales -->
    <div class="p-2 mb-2 rounded" style="border-left: 4px solid #6c757d;">
        <small class="text-secondary font-weight-bold">Opciones adicionales</small>
        <div class="row mt-2">
            <div class="col-md-6">
                <div class="mb-2">
                    {{ html()->label('MEDIO VIRTUAL', 'medio_virtual')->class('form-label') }}
                    {{ html()->select('medio_virtual', ['' => 'SELECCIONAR', 1 => 'VIRTUAL 1', 2 => 'VIRTUAL 2'])->class('form-control')->disabled() }}
                </div>
                <div class="mb-2">
                    {{ html()->label('ENLACE VIRTUAL', 'enlace_virtual')->class('form-label') }}
                    {{ html()->text('enlace_virtual')->class('form-control')->disabled() }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-2">
                    {{ html()->label('CONVENIO ESPECIFICO', 'convenio_especifico')->class('form-label') }}
                    {{ html()->text('convenio_especifico')->class('form-control')->disabled() }}
                </div>
                <div class="mb-2">
                    {{ html()->label('FECHA DE CONVENIO ESPECIFICO', 'fecha_convenio')->class('form-label') }}
                    {{ html()->text('fecha_convenio')->class('form-control')->disabled() }}
                </div>
                <div class="form-check mb-2">
                    {{ html()->checkbox('cerss', false, 'true')->class('form-check-input')->id('cerss_check') }}
                    {{ html()->label('CERSS', 'cerss_check')->class('form-check-label ms-2') }}
                </div>
                <div>
                    {{ html()->select('cerss', ['' => 'SELECCIONAR', 1 => 'CERS 1', 2 => 'CERS 2'])->class('form-control')->disabled() }}
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-end">
            {!! html()->button('<i class="fa fa-save me-2"></i> Guardar grupo y asignar alumnos', 'submit')->class('btn btn-primary btn-lg rounded')->id('btn-guardar')->toHtml() !!}
        </div>
    </div>
    {{ html()->form()->close() }}
</div>

@endsection

@include('grupos.partials.modal_fullcalendar')

@push('content_css_sign')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
@endpush

@push('script_sign')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar el calendario cuando se abra el modal
        let calendar;
        $('#modalFullCalendar').on('shown.bs.modal', function() {
            if (!calendar) {
                var calendarEl = document.getElementById('calendar');
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'es',
                    height: 500
                });
                calendar.render();
            }
        });
    });

    $(document).on('click', '#btn-guardar', function() {
        window.location.href = "{!! route('grupos.asignar.alumnos') !!}";
    });

    // Mostrar el modal al hacer clic en el botón AGENDA
    $(document).on('click', '#btn-agenda', function() {
        $('#modalFullCalendar').modal('show');
    });

    document.addEventListener('DOMContentLoaded', function() {
        let calendar;
        $('#modalFullCalendar').on('shown.bs.modal', function() {
            if (!calendar) {
                var calendarEl = document.getElementById('calendar');
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    views: {
                        dayGridMonth: {
                            titleFormat: {
                                year: 'numeric',
                                month: 'long'
                            }
                        },
                        timeGridWeek: {
                            titleFormat: {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            }
                        },
                        timeGridDay: {
                            titleFormat: {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                weekday: 'long'
                            }
                        }
                    },
                    locale: 'es',
                    firstDay: 7,
                    height: 'auto',
                    buttonText: {
                        today: 'Hoy',
                        month: 'Mes',
                        week: 'Semana',
                        day: 'Día'
                    },
                    events: [{
                            title: 'Reunión de trabajo',
                            start: '2024-01-15',
                            color: '#007bff'
                        },
                        {
                            title: 'Cita médica',
                            start: '2024-01-20T10:00:00',
                            end: '2024-01-20T11:00:00',
                            color: '#28a745'
                        },
                        {
                            title: 'Evento de todo el día',
                            start: '2024-01-25',
                            allDay: true,
                            color: '#ffc107'
                        }
                    ],
                    selectable: true,
                    selectMirror: true,
                    select: function(arg) {
                        var title = prompt('Título del evento:');
                        if (title) {
                            calendar.addEvent({
                                title: title,
                                start: arg.start,
                                end: arg.end,
                                allDay: arg.allDay
                            });
                        }
                        calendar.unselect();
                    },
                    eventClick: function(arg) {
                        if (confirm('¿Eliminar evento "' + arg.event.title + '"?')) {
                            arg.event.remove();
                        }
                    },
                    editable: true,
                    dayCellDidMount: function(arg) {
                        arg.el.addEventListener('mouseenter', function() {
                            arg.el.style.backgroundColor = '#f8f9fa';
                            arg.el.style.cursor = 'pointer';
                        });
                        arg.el.addEventListener('mouseleave', function() {
                            arg.el.style.backgroundColor = '';
                            arg.el.style.cursor = '';
                        });
                    },
                    eventMouseEnter: function(info) {
                        info.el.style.transform = 'scale(1.05)';
                        info.el.style.transition = 'transform 0.2s';
                        info.el.style.zIndex = '999';
                        info.el.title = `${info.event.title}\nInicio: ${info.event.start.toLocaleString()}`;
                    },
                    eventMouseLeave: function(info) {
                        info.el.style.transform = 'scale(1)';
                        info.el.style.zIndex = '';
                    },
                    dateClick: function(arg) {
                        arg.dayEl.style.backgroundColor = '#e3f2fd';
                        setTimeout(function() {
                            arg.dayEl.style.backgroundColor = '';
                        }, 1000);
                    },
                    eventDrop: function(info) {
                        alert(`Evento "${info.event.title}" movido a ${info.event.start.toLocaleDateString()}`);
                    },
                    eventResize: function(info) {
                        alert(`Evento "${info.event.title}" redimensionado`);
                    },
                    datesSet: function(info) {
                        // Puedes agregar lógica aquí si necesitas reaccionar al cambio de vista
                    }
                });
                calendar.render();
            }
        });
    });
</script>
@endpush