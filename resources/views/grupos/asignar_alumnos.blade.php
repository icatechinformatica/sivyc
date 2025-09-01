@extends('theme.sivyc.layout')

@section('title', 'Alumnos | SIVyC Icatech')

@push('content_css_sign')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.css">
<link rel="stylesheet" href="{{ asset('css/grupos/agenda_fullcalendar.css') }}">
<style>
    /* Destacar el tipo de exoneración/pago */
    #tipo-exoneracion.tipo-exo-badge {
        display: inline-block;
        font-weight: 800;
        font-size: 0.9rem;
        letter-spacing: .5px;
        padding: .2rem;
        border-radius: 8px;
        text-transform: uppercase;
        min-width: 9rem;
        text-align: center;
    }

    #tipo-exoneracion.tipo-ordinario {
        background: #e5fde3;
        color: #0da137;
        border: 1px solid #92f990;
    }

    #tipo-exoneracion.tipo-exoneracion {
        background: #fffdeb;
        color: #b7931c;
        border: 1px solid #efee9a;
    }

    #tipo-exoneracion.tipo-reduccion {
        background: #e0e0ff;
        color: #1b00e6;
        border: 1px solid #808bff;
    }

    .exo-pulse {
        animation: exoPulse 1.2s ease-out 1;
    }

    @keyframes exoPulse {
        0% {
            transform: scale(0.98);
            box-shadow: 0 0 0 0 rgba(0, 0, 0, 0.12);
        }

        60% {
            transform: scale(1.02);
        }

        100% {
            transform: scale(1);
        }
    }
</style>
@endpush

@section('content')
<div class="card-header rounded-lg shadow d-flex justify-content-between align-items-center">
    <div class="col-md-8">
        <span>Grupos / Asignar Alumnos</span>
    </div>
    <div class="d-flex align-items-center">
        <span class="font-weight-bold mr-3">Curso:</span>
        <span class="badge badge-secondary p-2 mr-4" style="font-size:1rem;">{{ $grupo->curso->nombre_curso }}</span>
        <span class="font-weight-bold mr-2">Folio Grupo:</span>
        <span class="badge badge-info p-2" style="font-size:1rem;">{{ $grupo->clave_grupo ?? '-' }}</span>
    </div>
</div>

<div class="row mb-3 px-5">
    <div class="d-flex">
        <a href="{{ route('grupos.editar', $grupo->id) }}" class="btn-regresar">
            <i class="fa fa-arrow-left mr-2"></i> Regresar
        </a>
    </div>
</div>

<div class="card card-body mt-3">
    <div class="col-md-12 mb-3 d-flex justify-content-between align-items-center px-0">

        <div class="flex-grow-1">
            <form class="form-inline" method="POST" action="{{ route('grupos.asignar.alumnos', $grupo->id) }}">
                @csrf
                <div class="input-group">
                    <input type="text" name="curp" class="form-control" placeholder="Ingrese CURP" maxlength="18"
                        required>
                    <div class="input-group-append">
                        <button class="btn btn-primary mr-2 rounded" type="submit" name="action"
                            value="agregar">Agregar</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="ml-auto d-flex align-items-center p-0 m-0">
            @php
            $numAlumnos = $grupo->alumnos->count();
            $costoCurso = optional($grupo->curso)->costo;
            $perShare = ($numAlumnos > 0 && !is_null($costoCurso)) ? ($costoCurso / $numAlumnos) : null;
            $todosSinCosto = $grupo->alumnos->every(function($a){ return is_null($a->pivot->costo); });
            @endphp
            <form id="form-costos" class="form-inline justify-content-end" method="POST"
                action="{{ route('grupos.alumnos.costos', $grupo->id) }}">
                @csrf
                <div class="d-flex align-items-end">
                    <div class="d-flex flex-column text-right mr-2">
                        <label for="cuota" class="font-weight-bold mb-1">Cuota general</label>
                    </div>
                    <div class="input-group input-group-sm mr-2" style="width: 160px;">
                        <input id="cuota" name="cuota_general" type="number" step="0.01" min="0" class="form-control"
                            placeholder="0.00" data-num-alumnos="{{ $numAlumnos }}"
                            data-per-share="{{ !is_null($perShare) ? number_format($perShare, 2, '.', '') : '' }}"
                            data-costo-curso="{{ !is_null($costoCurso) ? number_format($costoCurso, 2, '.', '') : '' }}"
                            value="{{ $todosSinCosto && !is_null($perShare) ? number_format($perShare, 2, '.', '') : '' }}"
                            aria-label="Cuota general">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-save mr-1"></i> Guardar
                    </button>
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
            $span.removeClass('tipo-ordinario tipo-exoneracion tipo-reduccion exo-pulse');
            if (tipo === 'EXONERACION') $span.addClass('tipo-exoneracion');
            else if (tipo === 'REDUCCION DE CUOTA') $span.addClass('tipo-reduccion');
            else $span.addClass('tipo-ordinario');

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