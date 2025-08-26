@extends('theme.sivyc.layout')

@section('title', 'Grupos | SIVyC Icatech')

@push('content_css_sign')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{ asset('css/grupos/agenda_fullcalendar.css') }}" />
@endpush

@section('content')
<div class="card-header rounded-lg shadow d-flex justify-content-between align-items-center">
    <div class="col-md-8">
        <span>Registro de grupos | Unidad: {{ auth()->user()->unidad->unidad }}</span>
    </div>
    <div class="col-md-4 d-flex justify-content-end">
        {{-- * INPUT NUEVO GRUPO --}}
        <a href="{{ route('grupos.crear') }}" class="btn btn-success">Nuevo Grupo</a>
    </div>
</div>
<div class="card card-body">
    <!-- Buscador -->
    <div class="row mb-3">
        <div class="col-md-12">
            <form method="GET" class="d-flex align-items-center gap-2 buscador-form">
                <input type="text" name="valor_buscar" class="form-control" placeholder="Buscar por grupo o curso..."
                    value="{{ request('valor_buscar') }}">
                <button type="submit" class="btn btn-primary" title="Buscar">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('valor_buscar'))
                <a href="{{ route('grupos.index') }}" class="btn-personalizado" title="Limpiar búsqueda">Limpiar</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Información de paginación -->
    <div class="row mb-3 align-items-start" style="min-height: 48px;">
        <div class="col-md-12 d-flex align-items-center" style="gap: 1.5rem;">
            <p class="text-muted mb-0">
                Mostrando {{ $grupos->firstItem() }} a {{ $grupos->lastItem() }} de {{ $grupos->total() }} registros
                @if(request('valor_buscar'))
                <span class="badge bg-danger ms-3 ml-3">Filtrado por: "{{ request('valor_buscar') }}"</span>
                @endif
            </p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>FOLIO GRUPO</th>
                    <th>CURSO</th>
                    <th>UNIDAD</th>
                    <th>INSTRUCTOR</th>
                    <th class="text-center">ESTATUS</th>
                    <th>Turnar a:</th>
                    <th class="text-center">Editar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grupos as $grupo)
                @if(auth()->user()->can('ver-cursos-todos') || auth()->user()->id == $grupo->id_usuario_captura)
                <tr>
                    <td>{{ $grupo->clave_grupo ?? 'SIN ASIGNAR' }}</td>
                    <td>{{ $grupo->curso->nombre_curso }}</td>
                    <td>{{ $grupo->unidad->unidad }}</td>
                    <td>{{ $grupo->instructor->nombre ?? 'SIN ASIGNAR' }}</td>
                    <td class="text-center">
                        <span class="badge" style="background-color: {{ $grupo->estatus->last()->color ?? '#6c757d' }}">{{ $grupo->estatusActual()->estatus ?? 'SIN ASIGNAR' }}</span>
                    </td>
                    <td class="text-center">
                        @foreach ($grupo->estatusAdyacentes() as $estatus)
                            @if($estatus->id != $grupo->estatusActual()->id && auth()->user()->can($estatus->permisos->pluck('ruta_corta')->toArray()))
                                <button class="btn btn-sm btn-info turnar-btn" data-grupo-id="{{ $grupo->id }}" data-estatus-id="{{ $estatus->id }}"> {{ $estatus->estatus }}</button>
                            @endif
                        @endforeach
                    </td>
                    <td class="text-center">
                        <a href="{{ route('grupos.editar', $grupo->id) }}" class="btn btn-sm btn-warning rounded"
                            title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="5" class="text-center">
                        @if(request('valor_buscar'))
                        No se encontraron grupos que coincidan con la búsqueda "{{ request('valor_buscar') }}".
                        @else
                        No hay registros de grupos disponibles.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Enlaces de paginación -->
    <div class="d-flex justify-content-center">
        {{ $grupos->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection

@push('script_sign')
<script>
    window.registroBladeVars = {
        csrfToken: '{{ csrf_token() }}',
    };
</script>
<script src="{{ asset('js/grupos/turnar.js') }}"></script>
@endpush
