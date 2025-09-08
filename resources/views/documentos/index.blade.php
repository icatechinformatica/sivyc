@extends('theme.sivyc.layout')

@section('title', 'Buzón Documentos Electrónicos | SIVyC Icatech')

@push('content_css_sign')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/alumnos/consulta.css') }}" />
    <style type="text/css">
        .doc-card-mobile {
            margin: 16px 4px 16px 4px !important;
            border-radius: 16px;
            background: #fff;
            border: 1.5px solid #e0e0e0;
            box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.08);
        }
    </style>
@endpush

@section('content')
    <div class="card-header rounded-lg shadow d-flex justify-content-between align-items-center">
        <div class="col-md-8">
            <span>Buzón de Documentos Electrónicos</span>
        </div>
    </div>


    <div class="card card-body">
        <!-- Buscador -->
        <div class="row mb-3">
            <div class="col-md-12">
                <form method="GET" class="d-flex align-items-center gap-2 buscador-form">
                    <input type="text" name="busqueda" class="form-control" placeholder="Busqueda por Folio..."
                        value="{{ request('busqueda') }}">
                    <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
                    <button type="submit" class="btn btn-primary" title="Buscar">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Información de paginación -->
        <div class="row mb-3 align-items-start" style="min-height: 48px;">
            <div class="col-md-12 d-flex align-items-center" style="gap: 1.5rem;">
                <p class="text-muted mb-0">
                    Mostrando {{ count($documentos) }} documento{{ count($documentos) == 1 ? '' : 's' }}
                    @if (request('busqueda'))
                        <span class="badge bg-danger ms-3 ml-3">Filtrado por: "{{ request('busqueda') }}"</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Tabla en escritorio, tarjetas en móvil -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>CONSECUTIVO</th>
                        <th>FOLIO</th>
                        <th>DOCUMENTO</th>
                        <th>FECHA DE CREACIÓN</th>
                        <th>AUTOR</th>
                        <th class="text-center">SEMÁFORO</th>
                        <th class="text-center">PROCESO</th>
                        <th class="text-center">DESCARGAR</th>
                        <th class="text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentos as $documento)
                        <tr>
                            <td>{{ $documento['id'] }}</td>
                            <td>{{ $documento['folio'] }}</td>
                            <td>{{ $documento['nombre'] }}</td>
                            <td>{{ $documento['fecha'] }}</td>
                            <td>{{ $documento['autor'] }}</td>
                            <td class="text-center">
                                <span
                                    style="display:inline-block;width:20px;height:20px;border-radius:50%;background:{{ $documento['semaforo'] }};border:1.5px solid #bbb;"></span>
                            </td>
                            <td class="text-center" style="min-width:120px;">
                                <div class="progress position-relative" style="height: 18px;">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: {{ $documento['proceso'] }}%;"
                                        aria-valuenow="{{ $documento['proceso'] }}" aria-valuemin="0" aria-valuemax="100"
                                        data-doc-id="{{ $documento['id'] }}">
                                    </div>
                                    <span class="position-absolute w-100 text-center progreso-texto"
                                        data-doc-id="{{ $documento['id'] }}"
                                        style="left:0; top:0; line-height:18px; color: {{ $documento['proceso'] > 40 ? '#fff' : '#222' }};">
                                        {{ $documento['proceso'] }}%
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="{{ asset('storage/dummy/' . $documento['archivo']) }}" download
                                    title="Descargar PDF">
                                    <i class="fas fa-print" style="font-size:2rem;"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-secondary btn-sm btn-abrir-modal-acciones"
                                    title="Acciones" data-doc-id="{{ $documento['id'] }}"
                                    data-doc-nombre="{{ $documento['nombre'] }}" style="vertical-align: middle;">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </td>

                            <!-- Modal de acciones (fuera del <td> para evitar problemas de anidación) -->

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No hay documentos de prueba disponibles.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tarjetas solo en móvil -->
        <div class="d-block d-md-none">

            @forelse($documentos as $documento)
                <div class="doc-card-mobile">
                    <div class="card-body p-3">
                        <div class="d-flex flex-column align-items-center mb-3">
                            <span
                                style="display:inline-block;width:70px;height:70px;border-radius:50%;background:{{ $documento['semaforo'] }};border:2px solid #bbb;"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Semáforo: {{ $documento['nombre'] }}"></span>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Nombre</div>
                            <div class="col-7">{{ $documento['nombre'] }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Folio</div>
                            <div class="col-7">{{ $documento['folio'] }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Fecha</div>
                            <div class="col-7">{{ $documento['fecha'] }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Autor</div>
                            <div class="col-7">{{ $documento['autor'] }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Proceso</div>
                            <div class="col-7">
                                <div class="progress" style="height: 16px;">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: {{ $documento['proceso'] }}%;" data-doc-id="{{ $documento['id'] }}">
                                    </div>
                                    <span class="position-absolute w-100 text-center progreso-texto"
                                        data-doc-id="{{ $documento['id'] }}"
                                        style="left:0; top:0; line-height:16px; color: {{ $documento['proceso'] > 40 ? '#fff' : '#222' }};">{{ $documento['proceso'] }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Archivo</div>
                            <div class="col-7">
                                <a href="{{ asset('storage/dummy/' . $documento['archivo']) }}" download
                                    title="Descargar PDF">
                                    <i class="fas fa-print" style="font-size:1.5rem;"></i>
                                </a>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button type="button" class="btn btn-secondary btn-sm btn-abrir-modal-acciones"
                                title="Acciones" data-doc-id="{{ $documento['id'] }}"
                                data-doc-nombre="{{ $documento['nombre'] }}">
                                <i class="fas fa-ellipsis-v"></i> Acciones
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">No hay documentos de prueba disponibles.</div>
            @endforelse
        </div>
    </div>

    {{-- Modal de acciones --}}
    <div class="modal fade" id="modalAccionesGlobal" tabindex="-1" aria-labelledby="modalAccionesGlobalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAccionesGlobalLabel">Acciones</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body d-flex flex-column align-items-center gap-2">
                    <a href="#" class="btn btn-success w-100" id="btnVerAccion"><i class="fas fa-eye me-2"></i>
                        Ver</a>
                    <a href="#" class="btn btn-danger w-100" id="btnEliminarAccion"><i
                            class="fas fa-trash me-2"></i> Eliminar</a>
                    <a href="#" class="btn btn-primary w-100" id="btnFirmarAccion"><i
                            class="fas fa-signature me-2"></i> Firma electrónica</a>
                </div>
            </div>
        </div>
    </div>
    {{-- modal de acciones --}}
@endsection

@push('script_sign')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Modal de acciones dinámico
            var modalAcciones = new bootstrap.Modal(document.getElementById('modalAccionesGlobal'));
            var modalTitulo = document.getElementById('modalAccionesGlobalLabel');
            var btnVer = document.getElementById('btnVerAccion');
            var btnEliminar = document.getElementById('btnEliminarAccion');
            var btnFirmar = document.getElementById('btnFirmarAccion');

            document.querySelectorAll('.btn-abrir-modal-acciones').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var docId = this.getAttribute('data-doc-id');
                    var docNombre = this.getAttribute('data-doc-nombre');
                    modalTitulo.textContent = 'Acciones para ' + docNombre;
                    // Aquí puedes personalizar los href o acciones de los botones según el docId
                    btnVer.setAttribute('href', '#');
                    btnEliminar.setAttribute('href', '#');
                    btnFirmar.setAttribute('href', '#');
                    // Mostrar modal
                    modalAcciones.show();
                });
            });
        });

        // Función para actualizar la barra de progreso y el porcentaje dinámicamente
        function actualizarProceso(docId, nuevoProceso) {
            // Actualiza barra y texto en tabla y tarjetas
            var barras = document.querySelectorAll('.progress-bar[data-doc-id="' + docId + '"]');
            var textos = document.querySelectorAll('.progreso-texto[data-doc-id="' + docId + '"]');
            barras.forEach(function(barra) {
                barra.style.width = nuevoProceso + '%';
                barra.setAttribute('aria-valuenow', nuevoProceso);
            });
            textos.forEach(function(texto) {
                texto.textContent = nuevoProceso + '%';
                texto.style.color = (nuevoProceso > 40) ? '#fff' : '#222';
            });
        }
    </script>
@endpush
