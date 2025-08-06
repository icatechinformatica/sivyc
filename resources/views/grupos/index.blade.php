@extends('theme.sivyc.layout')

@section('title', 'Grupos | SIVyC Icatech')

@push('content_css_sign')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{ asset('css/grupos/agenda_fullcalendar.css') }}" />
@endpush

@section('content')
<div class="card-header rounded-lg shadow d-flex justify-content-between align-items-center">
    <div class="col-md-8">
        <span>Registro de grupos</span>
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
                <input type="text" name="valor_buscar" class="form-control"
                    placeholder="Buscar por grupo o curso..." value="{{ request('valor_buscar') }}">
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
                    <th>ESTATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grupos as $grupo)
                <tr>
                    <td>{{ $grupo->folio_grupo }}</td>
                    <td>{{ $grupo->curso }}</td>
                    <td>{{ $grupo->unidad }}</td>
                    <td>{{ $grupo->id_instructor }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary">Enviar a DTA</button>
                    </td>
                    <td>{{ $grupo->turnado == 'DTA' ? 'EN REVISION' : $grupo->turnado }}</td>
                </tr>
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
<script src="{{ asset('js/grupos/agenda_fullcalendar.js') }}"></script>
@endpush