@extends('theme.sivyc.layout')

@section('title', 'Previsualización de Importación | Sivyc Icatech')

@section('content')
<div class="card-header">
    <h3 class="card-title">Previsualización de Datos - Importación de Grupos</h3>
</div>
<div class="card card-light">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12">
                @if($has_errors)
                    <div class="alert alert-danger">
                        <strong><i class="fas fa-exclamation-triangle"></i> Se encontraron errores en los datos</strong>
                        <p class="mb-0">Por favor, revise las filas marcadas en rojo y corrija el archivo Excel antes de continuar.</p>
                    </div>
                @else
                    <div class="alert alert-success">
                        <strong><i class="fas fa-check-circle"></i> Validación exitosa</strong>
                        <p class="mb-0">Todos los datos fueron validados correctamente. Puede proceder con la importación.</p>
                    </div>
                @endif

                <div class="alert alert-info">
                    <strong>Total de registros:</strong> {{ $total_rows }}
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Unidad</th>
                        <th>Curso</th>
                        <th>Instructor</th>
                        <th>Fechas</th>
                        <th>Horario</th>
                        <th>ID Preview</th>
                        <th>Folio Preview</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr class="{{ !empty($row['errors']) ? 'table-danger' : '' }}">
                            <td>{{ $row['fila'] }}</td>
                            <td>
                                {{ $row['unidad'] }}
                                @if(isset($row['cct']))
                                    <br><small class="text-muted">CCT: {{ $row['cct'] }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $row['curso'] }}
                                @if(isset($row['especialidad']))
                                    <br><small class="text-info">{{ $row['especialidad'] }}</small>
                                @endif
                                @if(isset($row['curso_data']))
                                    <br><small class="text-muted">Horas: {{ $row['curso_data']->horas }}</small>
                                @endif
                            </td>
                            <td>
                                @if(isset($row['instructor']))
                                    {{ $row['instructor']->nombre_completo }}
                                    <br><small class="text-muted">{{ $row['curp'] }}</small>
                                @else
                                    <span class="text-danger">No encontrado</span>
                                    <br><small>{{ $row['curp'] }}</small>
                                @endif
                            </td>
                            <td>
                                <small>
                                    Inicio: {{ $row['inicio'] }}<br>
                                    Fin: {{ $row['fin'] }}
                                </small>
                            </td>
                            <td>
                                <small>
                                    {{ $row['hora_inicio'] }}<br>
                                    {{ $row['hora_fin'] }}
                                </small>
                            </td>
                            <td><small>{{ $row['id_preview'] ?? 'N/A' }}</small></td>
                            <td><small>{{ $row['folio_grupo_preview'] ?? 'N/A' }}</small></td>
                            <td>
                                @if(!empty($row['errors']))
                                    @foreach($row['errors'] as $error)
                                        <span class="badge badge-danger d-block mb-1">{{ $error }}</span>
                                    @endforeach
                                @endif
                                @if(!empty($row['warnings']))
                                    @foreach($row['warnings'] as $warning)
                                        <span class="badge badge-warning d-block mb-1">{{ $warning }}</span>
                                    @endforeach
                                @endif
                                @if(empty($row['errors']) && empty($row['warnings']))
                                    <span class="badge badge-success">OK</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <form action="{{ route('preinscripcion.importar_grupos.store') }}" method="POST" id="formConfirmar">
                    @csrf
                    <input type="hidden" name="temp_file" value="{{ $temp_file }}">
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('preinscripcion.importar_grupos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        
                        @if(!$has_errors)
                            <button type="submit" class="btn btn-success btn-lg" id="btnConfirmar">
                                <i class="fas fa-check"></i> Confirmar e Importar {{ $total_rows }} Grupos
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary" disabled>
                                <i class="fas fa-ban"></i> No se puede importar con errores
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Confirmación antes de importar
    document.getElementById('formConfirmar')?.addEventListener('submit', function(e) {
        if (!confirm('¿Está seguro de que desea importar {{ $total_rows }} grupos? Esta acción no se puede deshacer.')) {
            e.preventDefault();
        } else {
            // Deshabilitar botón para evitar doble clic
            document.getElementById('btnConfirmar').disabled = true;
            document.getElementById('btnConfirmar').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importando...';
        }
    });
</script>
@endsection
