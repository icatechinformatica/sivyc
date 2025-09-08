@extends('theme.sivyc.layout')

@section('title', 'Alumnos | SIVyC Icatech')

@push('content_css_sign')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.css">
<link rel="stylesheet" href="{{ asset('css/grupos/agenda_fullcalendar.css') }}">
<link rel="stylesheet" href="{{ asset('css/grupos/detalles.css') }}">
@endpush

@section('content')
<div class="card-header rounded-lg shadow d-flex align-items-center header-grupos">
    <div class="col-md-12 d-flex flex-column flex-md-row align-items-start align-items-md-center w-100">
        <!-- Título -->
        <div class="col-md-8 d-flex align-items-center">
            <a href="{{ route('grupos.editar', $grupo->id) }}" class="btn btn-outline-light btn-sm d-inline-flex align-items-center px-2 py-1 mr-3" title="Regresar a editar grupo" aria-label="Regresar a editar grupo">
                <i class="fa fa-arrow-left mr-1"></i>
            </a>
            <h5 class="mb-0 font-weight-bold">Grupos <span class="text-muted">/ Asignar Alumnos</span></h5>
        </div>

        <!-- Badges a la derecha -->
        <div class="badges-wrap d-flex flex-wrap align-items-center order-2 order-md-3 ml-md-auto">
            <span class="badge badge-pill badge-light" style="font-weight: 600;">
                <i class="fa fa-book mr-1" aria-hidden="true"></i>{{ $grupo->curso->nombre_curso }}
            </span>
            <span class="badge badge-pill badge-info ml-2" style="font-weight: 600;">
                <i class="fa fa-hashtag mr-1" aria-hidden="true"></i>{{ $grupo->clave_grupo ?? '-' }}
            </span>
        </div>
    </div>
</div>



{{-- Mensajes flash para asignación/eliminación de alumnos --}}
<div class="row px-3 py-0 mx-0 my-2 ">
    <div class="col-12 my-2">
        @if (session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif
        @if (session('info'))
            <div class="alert alert-info" role="alert">{{ session('info') }}</div>
        @endif
    </div>
</div>

<div class="card card-body mt-0">
    <div class="row mb-3">
        <form class="col-12 col-md-6 p-3 rounded d-flex flex-column flex-md-row align-items-center h-100" method="POST" action="{{ route('grupos.asignar.alumnos', $grupo->id) }}">
            @csrf
            <div class="form-group col-12 col-md-8 mb-2 mb-md-auto">
                <label for="curp" class="d-block">CURP</label>
                <input type="text" name="curp" class="form-control" id="curp" placeholder="Ingrese CURP" maxlength="18" required>
            </div>
            <div class="form-group col-12 col-md-4">
                <label class="d-md-block d-none" style="opacity: 0;">adjust</label>
                <button type="submit" name="action" value="agregar" class="btn btn-sm btn-primary w-100">Agregar</button>
            </div>
        </form>

        <form id="form-costos" class="col-12 col-md-6 p-3 rounded d-flex flex-column flex-md-row align-items-center h-100" method="POST" action="{{ route('grupos.alumnos.costos', $grupo->id) }}">
            @csrf
            <div class="form-group col-12 col-md-8 mb-2 mb-md-auto">
                <label for="cuota" class="d-block">Cuota general</label>
                <input id="cuota" name="cuota_general" type="number" step="0.01" min="0" class="form-control" placeholder="0.00"
                    data-num-alumnos="{{ $numAlumnos }}"
                    data-per-share="{{ !is_null($perShare) ? number_format((float) $perShare, 2, '.', '') : '' }}"
                    data-costo-curso="{{ !is_null($costoCurso) ? number_format((float) $costoCurso, 2, '.', '') : '' }}"
                    value="{{ $todosSinCosto && !is_null($perShare) ? number_format((float) $perShare, 2, '.', '') : '' }}"
                    aria-label="Cuota general">
            </div>
            <div class="form-group col-12 col-md-4">
                <label class="d-md-block d-none" style="opacity: 0;">adjust</label>
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fa fa-save mr-1"></i> Guardar</button>
            </div>
        </form>
    </div>


    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">

            <thead class="thead-dark">
                <tr>
                    <th>Curp</th>
                    <th>Matrícula</th>
                    <th>Nombre</th>
                    <th>Sexo</th>
                    <th>Fec. Nac.</th>
                    <th>Edad</th>
                    <th>Escolaridad</th>
                    <th>Gpo. Vulnerable</th>
                    <th>Nac.</th>
                    <th>Tipo Inscrip.</th>
                    <th>Cuota</th>
                    <th>CURP</th>
                    <th>SID</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @if (empty($grupo) || empty($grupo->alumnos) || $grupo->alumnos->isEmpty())
                <tr>
                    <td colspan="15" class="text-center">No hay alumnos asignados aún</td>
                </tr>
                @else
                @foreach ($grupo->alumnos as $alumno)
                @php
                $documentos = json_decode($alumno->archivos_documentos ?? '[]', true);
                @endphp
                <tr>
                    <td>{{ $alumno->curp ?? '-' }}</td>
                    <td>{{ $alumno->matricula ?? '-' }}</td>
                    <td>{{ $alumno->nombreCompleto() }}</td>
                    <td>{{ $alumno->sexo->sexo ?? '-' }}</td>
                    <td>{{ $alumno->fecha_nacimiento ? \Carbon\Carbon::parse($alumno->fecha_nacimiento)->format('d/m/Y')
                        : '-' }}</td>
                    <td>{{ $alumno->edad ?? '-' }}</td>
                    <td>{{ $alumno->gradoEstudio->grado_estudio }}</td>
                    <td>{{ optional($alumno->grupoVulnerable)->nombre ?? (isset($alumno->gpo_vulnerable) ?
                        ($alumno->gpo_vulnerable ? 'Sí' : 'No') : '-') }}</td>
                    <td>{{ optional($alumno->nacionalidad)->nacionalidad ?? ($alumno->nacionalidad ?? '-') }}</td>
                    <td>{{ optional($alumno->tipoInscripcion)->tipo ?? ($alumno->tipo_inscripcion ?? '-') }}</td>
                    <td>
                        <input form="form-costos" type="number" step="0.01" min="0"
                            class="form-control cuota-individual" name="costos[{{ $alumno->id }}]"
                            value="{{ $alumno->pivot->costo !== null ? number_format((float)$alumno->pivot->costo, 2, '.', '') : '' }}"
                            placeholder="0.00" inputmode="decimal">
                    </td>
                    <td class="text-center align-middle">
                        @if (!empty($documentos['curp']))
                        <a href="{{ asset('storage/' . $documentos['curp']['ruta']) }}" target="_blank"
                            class="btn btn-primary btn-sm p-2 rounded"> <i class="fa fa-file-pdf"></i> </a>
                        @endif
                    </td>
                    <td>{{ $alumno->sid ?? '-' }}</td>
                    <td class="text-center align-middle">
                        <form method="POST" action="{{ route('grupos.eliminar.alumno', $grupo->id) }}" class="">
                            @csrf
                            <input type="hidden" name="alumno_id" value="{{ $alumno->id }}"> <button
                                class="btn btn-danger btn-sm rounded p-2" type="submit" name="action" value="eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>

        </table>
    </div>

    <div class="d-flex flex-column mt-4 gap-2 align-items-center">
        <div>
            <p>Tipo de pago: <span id="tipo-exoneracion" class="tipo-exo-badge" aria-live="polite"></span></p>
        </div>
        <div class="d-flex justify-content-end">
            <button class="btn border border-danger text-danger bg-white" style="margin-right: 10px;">
                <i class="fa fa-file-pdf mr-2" style="color: #d32f2f;"></i> ACTA DE ACUERDO
            </button>
            <button class="btn border border-danger text-danger bg-white" style="margin-right: 10px;">
                <i class="fa fa-file-pdf mr-2" style="color: #d32f2f;"></i> SOLICITUD APERTURA
            </button>
            <button class="btn border border-danger text-danger bg-white" style="margin-right: 10px;">
                <i class="fa fa-file-pdf mr-2" style="color: #d32f2f;"></i> LISTA ALUMNOS
            </button>
        </div>
    </div>

</div>
@endsection

@push('script_sign')
<script>
    $(function() {

        function parseCuota(val) {
            if (val === undefined || val === null) return null;
            // Limpia símbolos y separadores por si vienen del backend o del usuario
            var cleaned = String(val).replace(/[^0-9.,-]/g, '').replace(/,/g, '');
            var num = parseFloat(cleaned);
            return isNaN(num) ? null : num;
        }

        function updateTipoExoneracion() {
            var $cuotaGeneral = $('#cuota');
            var numAlumnos = parseInt($cuotaGeneral.data('num-alumnos'), 10) || $('.cuota-individual').length || 0;

            // Determinar perShare: usar valor de #cuota si existe; si no, data-per-share
            var perShare = parseCuota($cuotaGeneral.val());
            if (perShare === null) {
                perShare = parseCuota($cuotaGeneral.data('per-share'));
            }
            // Costo total del curso (para evaluar reducción por suma total menor al costo)
            var costoCurso = parseCuota($cuotaGeneral.data('costo-curso'));

            var cuotas = [];
            $('.cuota-individual').each(function() {
                var v = parseCuota($(this).val());
                cuotas.push(v);
            });

            var tol = 0.01;
            var anyZero = cuotas.some(function(v) { return v !== null && Math.abs(v) < tol; });
            // Suma de cuotas individuales (ignorando nulls)
            var sumCuotas = cuotas.reduce(function(acc, v) { return acc + (v !== null ? v : 0); }, 0);
            var hasAnyValue = cuotas.some(function(v) { return v !== null; });
            // Reducción si la suma de todas las cuotas es menor al costo del curso (con tolerancia)
            var isReduction = false;
            if (costoCurso !== null && hasAnyValue) {
                isReduction = (sumCuotas + tol) < costoCurso;
            }

            var tipo = 'PAGO ORDINARIO';
            if (anyZero) tipo = 'EXONERACION';
            else if (isReduction) tipo = 'REDUCCION DE CUOTA';

            var $span = $('#tipo-exoneracion');
            $span.text(tipo);
            // actualizar clases visuales
            $span.removeClass('tipo-pago-ordinario tipo-exoneracion tipo-reduccion-de-cuota exo-pulse');
            if (tipo === 'EXONERACION') $span.addClass('tipo-exoneracion');
            else if (tipo === 'REDUCCION DE CUOTA') $span.addClass('tipo-reduccion-de-cuota');
            else $span.addClass('tipo-pago-ordinario');

            var prev = $span.data('prev-tipo');
            if (prev !== tipo) {
                $span.addClass('exo-pulse');
                setTimeout(function(){ $span.removeClass('exo-pulse'); }, 1200);
                $span.data('prev-tipo', tipo);
            }
        }

        function applyCuotaToIndividuals() {
            var num = parseCuota($('#cuota').val());
            var display = num !== null ? num.toFixed(2) : '';
            // Asignar al value de los inputs dentro del td
            $('.cuota-individual').each(function() {
                $(this).val(display);
            });
        }

        // Precargar al cargar la página: solo autollenar si NINGÚN alumno tiene costo asignado
        var algunoTieneCosto = false;
        $('.cuota-individual').each(function() {
            var v = parseCuota($(this).val());
            if (v !== null) { algunoTieneCosto = true; return false; }
        });
        if (!algunoTieneCosto) {
            applyCuotaToIndividuals();
        }
        updateTipoExoneracion();


        var debounceTimer;
        $('#cuota').on('input change', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                applyCuotaToIndividuals();
                updateTipoExoneracion();
            }, 500);
        });

        // Recalcular tipo si cambian cuotas individuales manualmente
        $(document).on('input change', '.cuota-individual', function() {
            updateTipoExoneracion();
        });
    });
</script>
@endpush