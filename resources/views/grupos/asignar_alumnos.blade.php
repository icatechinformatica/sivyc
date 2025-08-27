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
        <div class="d-flex align-items-center p-0 m-0">
            <button class="btn btn-outline-info rounded" style="margin-right:0;">
                <i class="fa fa-share mr-2"></i> Exportar grupo (alumnos)
            </button>
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
                    <th>
                        <input type="number" name="cuota" id="cuota" class="form-control form-control-sm" placeholder="CUOTA $0.00" min="0" step="0.01" />
                    </th>
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
                        <td>{{ $alumno->fecha_nacimiento ? \Carbon\Carbon::parse($alumno->fecha_nacimiento)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $alumno->edad ?? '-' }}</td>
                        <td>{{ $alumno->gradoEstudio->grado_estudio }}</td>
                        <td>{{ optional($alumno->grupoVulnerable)->nombre ?? (isset($alumno->gpo_vulnerable) ? ($alumno->gpo_vulnerable ? 'Sí' : 'No') : '-') }}</td>
                        <td>{{ optional($alumno->nacionalidad)->nacionalidad ?? ($alumno->nacionalidad ?? '-') }}</td>
                        <td>{{ optional($alumno->tipoInscripcion)->tipo ?? ($alumno->tipo_inscripcion ?? '-') }}</td>
                        <td>{{ isset($alumno->cuota) ? number_format($alumno->cuota, 2) : '-' }}</td>
                        <td>
                            @if (!empty($documentos['curp']))
                            <a href="{{ asset('storage/' . $documentos['curp']['ruta']) }}" target="_blank" class="text-primary text-decoration-underline"> <i class="fa fa-file-pdf"></i> </a>
                            @endif
                        </td>
                        <td>{{ $alumno->sid ?? '-' }}</td>
                        <td>
                            <form method="POST" action="{{ route('grupos.eliminar.alumno', $grupo->id) }}">
                                @csrf
                                <input type="hidden" name="alumno_id" value="{{ $alumno->id }}"> <button class="btn btn-danger btn-sm" type="submit" name="action" value="eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end mt-4 gap-2">
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
@endsection