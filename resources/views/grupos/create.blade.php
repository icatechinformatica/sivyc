@extends('theme.sivyc.layout')

@section('title', 'Alumnos | SIVyC Icatech')

@push('content_css_sign')
<link rel="stylesheet" href="{{ asset('css/global.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.css">
<link rel="stylesheet" href="{{ asset('css/grupos/agenda_fullcalendar.css') }}">
@endpush

@section('content')
<div class="card-header rounded-lg shadow d-flex align-items-center">
    <div class="col-md-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div class="mb-2 mb-md-0">
            <h5 class="mb-0 font-weight-bold">
                Grupos <span class="text-muted">/ {{ $esNuevoRegistro ? 'Registro' : 'Edición' }}</span>
            </h5>
        </div>
        @if (!$esNuevoRegistro && isset($grupo))
            @php($estatus = $grupo->estatusActual())
            <div class="d-flex flex-wrap align-items-center">
                <span class="badge badge-pill badge-light text-uppercase mr-2" style="font-weight: 600;">
                    <i class="fa fa-hashtag mr-1" aria-hidden="true"></i>{{ $grupo->clave_grupo }}
                </span>
                @if ($estatus)
                    <span class="badge badge-pill mr-0" style="background-color: {{ $estatus->color ?? '#6c757d' }}; color: #fff; font-weight: 600;">
                        <i class="fa fa-info-circle mr-1" aria-hidden="true"></i>{{ $estatus->estatus }}
                    </span>
                @endif
            </div>
        @endif
    </div>
</div>


<div class="card card-body">
    <div class="row">
        {{-- * Barra de pasos lateral --}}
        <div class="col-md-3 d-none d-md-block">
            <nav id="step-progress" class="nav-sticky">
                <ul class="list-group list-group-flush step-progress-nav">
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="info_general">
                        <span class="step-circle mr-2" data-status="actual">1</span>
                        <span class="fw-bold text-black">Información general</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="ubicacion">
                        <span class="step-circle mr-2" data-status="restante">2</span>
                        <span class="fw-bold">Ubicación</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="organismo">
                        <span class="step-circle mr-2" data-status="restante">3</span>
                        <span class="fw-bold">Organismo Publico</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="opciones">
                        <span class="step-circle mr-2" data-status="restante">4</span>
                        <span class="fw-bold">Opciones</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="agenda">
                        <span class="step-circle mr-2" data-status="restante">5</span>
                        <span class="fw-bold">Agenda</span>
                    </li>
                    <li class="list-group-item py-3 d-flex align-items-center" data-step="alumnos">
                        <span class="step-circle mr-2" data-status="restante">6</span>
                        <span class="fw-bold">Alumnos</span>
                    </li>
                </ul>
            </nav>
        </div>
        <input type="hidden" id="esNuevoRegistro" value="{{ $esNuevoRegistro ? 'true' : 'false' }}" />
        @if (!$esNuevoRegistro)
        <input type="hidden" name="id_grupo" id="id_grupo" value="{{ $grupo->id }}" />
        @endif
        <div class="col-md-9">
            {{-- * Sección: Información general --> --}}
            <div class="col-12 mb-4 step-section" id="info_general">
                {{ html()->form('POST')->id('info_general_form')->open() }}
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3">Información general</h5>
                    <div class="row my-1">
                        <div class="form-group col-md-4 mb-1">
                            {{ html()->label('IMPARTICIÓN', 'imparticion')->class('form-label mb-1') }}
                            {{ html()->select('imparticion', [null => 'SELECCIONE EL TIPO DE IMPARTICIÓN'] + $tiposImparticion->pluck('imparticion', 'id')->toArray())->class('form-control')->required()
                                    ->value($esNuevoRegistro ? null : $grupo->id_imparticion) }}
                        </div>
                        <div class="form-group col-md-4 mb-1">
                            {{ html()->label('MODALIDAD', 'modalidad')->class('form-label mb-1') }}
                            {{ html()->select('modalidad', [null => 'SELECCIONAR MODALIDAD'] + $modalidades->pluck('modalidad', 'id')->toArray())->class('form-control')->required()
                                    ->value($esNuevoRegistro ? null : $grupo->id_modalidad) }}
                        </div>
                        <div class="form-group col-md-4 mb-1">
                            {{ html()->label('UNIDAD/ACCIÓN MÓVIL', 'unidad_accion_movil')->class('form-label mb-1') }}
                            {{ html()->select('unidad_accion_movil', [null => 'SELECCIONAR'] + $unidades->pluck('unidad', 'id')->toArray())->class('form-control')->required()
                                    ->value($esNuevoRegistro ? null : $grupo->id_unidad) }}
                        </div>
                    </div>
                    <div class="row my-1">
                        <div class="form-group col-md-3 mb-1">
                            {{ html()->label('SERVICIO', 'servicio')->class('form-label mb-1') }}
                            {{ html()->select('servicio', [null => 'SELECCIONAR'] + $servicios->pluck('servicio', 'id')->toArray())->class('form-control ')->required()
                                    ->value($esNuevoRegistro ? null : $grupo->id_servicio) }}
                        </div>
                        <div class="form-group col-md-9 mb-1">
                            {{ html()->label('CURSO', 'curso')->class('form-label mb-1') }}
                            {{ html()->select('curso', [null => 'SELECCIONA CURSO'] + $cursos->pluck('nombre_curso', 'id')->toArray())->class('form-control ')->required()
                                    ->value($esNuevoRegistro ? null : $grupo->id_curso) }}
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        {{ html()->button('Guardar Info. General')->class( 'btn btn-primary float-end rounded')->id('guardar_info_general')->type('button') }}
                    </div>
                    {{ html()->form()->close() }}
                </div>
            </div>
            {{-- * Sección: Ubicación --> --}}
            <div class="col-12 mb-4 step-section" id="ubicacion" style="display:none;">
                {{ html()->form('POST')->id('ubicacion_form')->open() }}
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3">Ubicación</h5>
                    <div class="row my-1">
                        <div class="form-group col-md-6 mb-1">
                            {{ html()->label('MUNICIPIO', 'municipio')->class('form-label mb-1') }}
                            {{ html()->select('municipio', [null => 'SELECCIONAR'] + $municipios->pluck('muni', 'id')->toArray())->class('form-control')->id('municipio-select')->required()
                                    ->value($esNuevoRegistro ? null : $grupo->id_municipio) }}
                        </div>
                        <div class="form-group col-md-6 mb-1">
                            {{ html()->label('LOCALIDAD', 'localidad')->class('form-label mb-1') }}
                            @if ($esNuevoRegistro)
                                {{ html()->select('localidad', ['' => 'SELECCIONAR MUNICIPIO PRIMERO'])->class('form-control')->id('localidad-select')->disabled()->required() }}
                            @else
                                {{ html()->select('localidad', ['' => 'SELECCIONAR MUNICIPIO'] + $localidades->pluck('localidad', 'id')->toArray())->class('form-control')->id('localidad-select')->disabled($esNuevoRegistro)->required()
                                    ->value($esNuevoRegistro ? null : $grupo->id_localidad) }}
                            @endif
                        </div>
                    </div>
                    @if (!$esNuevoRegistro && $grupo->efisico)
                    <div class="form-group mb-1">
                        {{ html()->label('DIRECCIÓN ACTUAL', 'localidad')->class('form-label mb-1') }}
                        {{ html()->text('localidad')->class('form-control')->value($grupo->efisico)->disabled() }}
                    </div>
                    @endif
                    <div class="form-group mb-1">
                        {{ html()->label('LUGAR DONDE SE IMPARTIRÁ', 'nombre_lugar')->class('form-label mb-1') }}
                        {{ html()->text('nombre_lugar')->class('form-control ')->required() }}
                    </div>
                    <div class="form-group mb-1">
                        {{ html()->label('CALLE Y NÚMERO', 'calle_numero')->class('form-label mb-1') }}
                        {{ html()->text('calle_numero')->class('form-control ')->required() }}
                    </div>
                    <div class="form-group mb-1">
                        {{ html()->label('COLONIA O BARRIO', 'colonia')->class('form-label mb-1') }}
                        {{ html()->text('colonia')->class('form-control ')->required() }}
                    </div>
                    <div class="form-group mb-1">
                        {{ html()->label('CÓDIGO POSTAL', 'codigo_postal')->class('form-label mb-1') }}
                        {{ html()->number('codigo_postal')->class('form-control')->maxlength(5)->minlength(5)->required() }}
                    </div>
                    <div class="form-group mb-1">
                        {{ html()->label('REFERENCIAS ADICIONALES', 'referencias')->class('form-label mb-1') }}
                        {{ html()->textarea('referencias')->class('form-control')->rows(2) }}
                    </div>
                    <div class="d-flex justify-content-end">
                        {{ html()->button('Guardar Ubicación')->class('btn btn-primary float-end guardar-seccion')->id('guardar_ubicacion')->type('button') }}
                    </div>
                </div>
                {{ html()->form()->close() }}
            </div>
            {{-- * Sección: ORGANISMO PUBLICO --}}
            <div class="col-12 mb-4 step-section" id="organismo" style="display:none;">
                {{ html()->form('POST')->id('organismo_form')->open() }}
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3">Organismo Publico</h5>
                    <div class="row mt-2">
                        <div class="form-group col-md-12 mb-2">
                            {{ html()->label('ORGANISMO PUBLICO', 'organismo_publico')->class('form-label mb-1') }}
                            {{ html()->select('organismo_publico', ['' => 'SELECCIONAR'] + $organismos_publicos->pluck('organismo', 'id')->toArray())->class('form-control')->required()
                                    ->value($esNuevoRegistro ? null : $grupo->id_organismo_publico) }}
                        </div>
                        <div class="form-group col-md-12 mb-2">
                            {{ html()->label('NOMBRE DEL REPRESENTANTE', 'nombre_representante')->class('form-label mb-1') }}
                            {{ html()->text('nombre_representante')->class('form-control')->required()->value($esNuevoRegistro ? null : $grupo->organismo_representante) }}
                        </div>
                        <div class="form-group col-md-12 mb-2">
                            {{ html()->label('TELÉFONO DEL REPRESENTANTE', 'telefono_representante')->class('form-label mb-1') }}
                            {{ html()->text('telefono_representante')->class('form-control')->required()->value($esNuevoRegistro ? null : $grupo->organismo_telefono_representante) }}
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        {{ html()->button('Guardar Organismo')->class('btn btn-primary float-end guardar-seccion')->id('guardar_organismo') }}
                    </div>
                </div>
                {{ html()->form()->close() }}
            </div>

            {{-- * Sección: Opciones adicionales --}}
            <div class="col-12 mb-4 step-section" id="opciones" style="display:none;">
                <div class="p-3 mb-2">
                    {{ html()->form('POST')->id('opciones_form')->open() }}
                    <h5 class="fw-bold border-bottom pb-1 mb-3">Opciones adicionales</h5>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="mb-2">
                                {{ html()->label('MEDIO VIRTUAL', 'medio_virtual')->class('form-label mb-1') }}
                                {{ html()->select('medio_virtual', [ '' => 'SELECCIONAR', 1 => 'VIRTUAL 1', 2 => 'VIRTUAL 2'])->class('form-control ')->disabled(!$esNuevoRegistro && !$grupo->id_imparticion == 2)
                                        ->value($esNuevoRegistro ? null : $grupo->id_imparticion) }}
                            </div>
                            <div class="mb-2">
                                {{ html()->label('ENLACE VIRTUAL', 'enlace_virtual')->class('form-label mb-1') }}
                                {{ html()->text('enlace_virtual')->class('form-control ')->disabled(!$esNuevoRegistro && !$grupo->id_imparticion == 2)
                                        ->value($esNuevoRegistro ? null : $grupo->link_virtual) }}
                            </div>
                            <div class="mb-2">
                                {{ html()->label('CONVENIO ESPECIFICO', 'convenio_especifico')->class('form-label') }}
                                {{ html()->text('convenio_especifico')->class('form-control ')->value($esNuevoRegistro ? null : $grupo->cespecifico) }}
                            </div>
                            <div class="mb-2">
                                {{ html()->label('FECHA DE CONVENIO ESPECIFICO', 'fecha_convenio')->class('form-label mb-1') }}
                                {{ html()->date('fecha_convenio')->class('form-control ')->value($esNuevoRegistro ? null : $grupo->fecha_cespecifico) }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        {{ html()->button('Guardar')->class('btn btn-primary float-end
                        guardar-seccion')->id('guardar_opciones') }}
                    </div>
                    {{ html()->form()->close() }}
                </div>
            </div>

            {{-- * Sección: Agenda --}}
            <div class="col-12 mb-4 step-section" id="agenda" style="display:none;">
                {{ html()->form()->id('form-agenda')->open() }}
                <div class="p-3 mb-2">
                    <h5 class="fw-bold border-bottom pb-1 mb-3">Agenda</h5>
                    <div class="form-row mt-2">
                        <div class="form-group col-12">
                            <div class="d-flex justify-content-center align-items-center h-100">
                                {!! html()->button('<i class="fa fa-calendar mr-2 rounded"></i> AGENDA','button')->class('btn btn-agenda btn-lg w-100')->id('btn-agenda')->toHtml() !!}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        {{ html()->button('Guardar Agenda')->class('btn btn-primary float-end guardar-seccion')->id('guardar_agenda')->type('button') }}
                    </div>
                </div>
                {{ html()->form()->close() }}
            </div>

            {{-- * Sección: Alumnos --}}
            <div class="col-12 mb-4 step-section p-0" id="alumnos" style="display: none;">
                <div class="card card-body mt-3 shadow-none p-0">
                    {{-- Mensajes flash para asignación/eliminación de alumnos --}} 
                    <div class="my-2">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                        @endif
                        @if (session('info'))
                            <div class="alert alert-info" role="alert">{{ session('info') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-12 mb-3 d-flex justify-content-between align-items-center px-0">
                        <div class="flex-grow-1">
                            <form class="form-inline" method="POST" action="{{ route('grupos.asignar.alumnos') }}">
                                @csrf
                                <div class="input-group" style="width: 400px;">
                                    @if (!$esNuevoRegistro && $grupo)
                                    <input type="hidden" name="grupo_id" value="{{ $grupo->id }}">
                                    @endif
                                    <input type="text" name="curp" class="form-control" placeholder="Ingrese CURP" maxlength="18"
                                        required style="flex: 1 1 auto; min-width: 0;"
                                        value="{{ isset($uncodeCurp) ? $uncodeCurp : '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary mr-2 rounded" type="submit" name="action"
                                            value="agregar">Agregar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Curp</th>
                                    <th>Matrícula</th>
                                    <th>Nombre</th>
                                    <th>Sexo</th>
                                    <th>Edad</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($grupo) || empty($grupo->alumnos) || $grupo->alumnos->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center">No hay alumnos asignados aún</td>
                                </tr>
                                @else
                                @foreach ($grupo->alumnos as $alumno)
                                <tr>
                                    <td>{{ $alumno->curp }}</td>
                                    <td>{{ $alumno->matricula }}</td>
                                    <td>{{ $alumno->nombreCompleto() }}</td>
                                    <td>{{ $alumno->sexo->sexo }}</td>
                                    <td>{{ $alumno->edad }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('grupos.eliminar.alumno', $grupo->id) }}">
                                            @csrf
                                            <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">
                                            <button class="btn btn-danger btn-sm" type="submit" name="action" value="eliminar">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @if (!$esNuevoRegistro && isset($grupo))
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="row col-md-12 justify-content-end">
                            <p class="mx-4">Turnar a:</p>
                            @foreach ($grupo->estatusAdyacentes() as $estatus)
                            @if($estatus->id != $ultimoEstatus->id)
                            <button class="btn btn-md rounded turnar-btn" style="background-color: {{ $estatus->color }}; color: white; font-weight: 600; font-size: 0.95rem;" data-grupo-id="{{ $grupo->id }}" data-estatus-id="{{ $estatus->id }}">{{ $estatus->estatus }}</button>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- agregamos un componente para agregar CURP de alumno --}}
@if (session('bandera') == true)
<x-agregar-curp-alumno :curp="session('curp')" :grupo-id="session('grupo_id')" :bandera="session('bandera')" />
@endif
{{-- agregamos un componente para agregar CURP de alumno End --}}
{{-- modals --}}
@if (!$esNuevoRegistro && isset($grupo) && $grupo->id)
@include('grupos.partials.modal_fullcalendar')
@endif
{{-- * CSS --}}
@push('content_css_sign')
<link rel="stylesheet" href="{{ asset('css/stepbar.css') }}" />
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
@endpush


{{-- * JS --}}
@push('script_sign')
@if (session('bandera') == true)
<script>
    $(document).ready(function() {
                    $('#addCurpModal').modal('show');
                });
</script>
@endif
<script>
    // Variables globales para la obtención de la CURP JS
    window.registroBladeVars = {
        esNuevoRegistro: {{ $esNuevoRegistro ? 'true' : 'false' }},
        csrfToken: '{{ csrf_token() }}',
    };

    // Variables específicas para la stepbar de grupos.
    window.gruposStepVars = {
        ultimaSeccion: @json($ultimaSeccion ?? null),
        ordenSecciones: ['info_general', 'ubicacion', 'organismo', 'opciones', 'agenda', 'alumnos']
    };
    
    // Configuración para Agenda del Grupo
    window.GrupoAgenda = {
        grupoId: @json(isset($grupo) ? $grupo->id : null),
        // Horas máximas del curso (decimal). Usado para calcular horas restantes en la UI.
        maxHoras: @json(isset($grupo) && isset($grupo->curso) ? $grupo->curso->horas : null),
        // Plantillas de rutas (coinciden con routes/web.php)
        routes: {
            index: function(grupoId) {
                return grupoId ? '{{ url('grupos') }}/' + grupoId + '/agenda' : null;
            },
            store: function(grupoId) {
                return grupoId ? '{{ url('grupos') }}/' + grupoId + '/agenda' : null;
            },
            update: function(grupoId, agendaId) {
                return (grupoId && agendaId) ? '{{ url('grupos') }}/' + grupoId + '/agenda/' + agendaId : null;
            },
            destroy: function(grupoId, agendaId) {
                return (grupoId && agendaId) ? '{{ url('grupos') }}/' + grupoId + '/agenda/' + agendaId : null;
            }
        }
    };
</script>
<script src="{{ asset('js/grupos/stepbar.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.js"></script>
<script src="{{ asset('js/grupos/registro_validaciones.js') }}"></script>
<script src="{{ asset('js/grupos/registro.js') }}"></script>
<script src="{{ asset('js/grupos/agenda.js') }}"></script>
<script src="{{ asset('js/grupos/turnar.js') }}"></script>
@endpush

@endsection
