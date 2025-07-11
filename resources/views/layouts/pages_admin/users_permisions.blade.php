<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'USUARIOS DEL SISTEMA | Sivyc Icatech')

@section('csscontent')
    <link rel="stylesheet" href="{{asset("vendor/bootstrap/bootstrapcustomizer.css") }}">
    <link rel="stylesheet" href="{{asset("vendor/bootstrap/bootstrap.min.css") }}">
    <style type="text/css">
        .card-grid .card-header,
		.card-grid .card-body {
			padding: 0;
		}
		.card-grid .card-header .row div[class^="col"],
		.card-grid .card-body .row div[class^="col"],
		.card-grid .card-grid-caption {
			padding-left: 0.5rem;
			padding-right: 0.5rem;
			padding-bottom: 0.25rem;
			padding-top: 0.25rem;
		}
		.card-grid .card-header .row {
			display: none;
			margin-left: 0;
			margin-right: 0;
		}
		.card-grid .card-body .row {
			position: relative;
			margin-left: 0;
			margin-right: 0;
		}
		.card-grid .card-body .row div[class^="col"] {
			position: static;
		}

		.card-grid .card-body .row div[class^="col"]:last-of-type {
			border-bottom: 1px solid #dee2e6;
		}
		.card-grid .card-body .row:last-of-type div[class^="col"] {
			border-bottom: none !important;
		}
		@media (min-width: 768px) {
			.card-grid .card-grid-caption {
				display: none;
			}
			.card-grid .card-header .row {
				display: flex;
			}

			.card-grid .card-header .row div[class^="col"] {
				border-left: 1px solid #dee2e6;
			}
			.card-grid .card-header .row div[class^="col"]:nth-of-type(1) {
				border-left: none !important;
			}

			.card-grid .card-body .row div[class^="col"] {
				border-bottom: 1px solid #dee2e6;
				border-left: 1px solid #dee2e6;
			}
			.card-grid .card-body .row div[class^="col"]:nth-of-type(1) {
				border-left: none !important;
			}
			.card-grid .card-body .row div[class^="col"] label {
				display: none;
			}
			.card-grid .card-body .row div[class^="col"] a.stretched-link:hover:after {
				color: #212529;
				background-color: rgba(0, 0, 0, .075);
			}
		}
		
		/* Estilos para ocultar registros N/A por defecto */
		.na-row {
			display: none;
		}
    </style>
@endsection

<!--contenido-->
@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-header">
                {!! html()->form('GET', route('usuario_permisos.index'))->class('form-inline')->open() !!}
                    {{--<select name="tipo_busqueda_personal" class="form-control mr-sm-2" id="tipo_busqueda_personal">
                        <option value="nombres">NOMBRE COMPLETO</option>
                    </select>--}}
                    {!! html()->text('busquedaPersonal')->class('form-control mr-sm-2')->placeholder('NOMBRE / CURP / EMAIL')->attribute('aria-label', 'BUSCAR') !!}
                    <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                {!! html()->form()->close() !!}
                <br>
                <div class="btn-actions-pane-right justify-content-end d-flex">
                    <div role="group" class="btn-group-sm btn-group">
                        <button id="toggleNARows" class="btn btn-sm btn-secondary mr-4" type="button">
                            <i class="fa fa-eye-slash" aria-hidden="true"></i> Mostrar N/A
                        </button>
                        <a href="{{route('usuarios.alta.funcionarios-instructores')}}" class="btn btn-sm btn-success">Nuevo Usuario</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-grid mb-4" role="grid" aria-labelledby="gridLabel">
                            <div class="card-header">
                                <div class="row" role="row">
                                    <div class="col-md-6" role="columnheader">
                                        <p class="form-control-plaintext">NOMBRE</p>
                                    </div>
                                    <div class="col-md-2 text-center" role="columnheader">
                                        <p class="form-control-plaintext">INFORMACIÓN</p>
                                    </div>
                                    <div class="col-md-2 text-center" role="columnheader">
                                        <p class="form-control-plaintext">MODIFICAR</p>
                                    </div>
                                    <div class="col-md-2 text-center" role="columnheader">
                                        <p class="form-control-plaintext">PERMISOS</p>
                                    </div>
                                </div>
                                {{-- <div id="gridLabel" class="card-grid-caption">
                                    <p class="form-control-plaintext">USUARIOS</p>
                                </div> --}}
                            </div>
                            <div class="card-body">
                                @foreach ($usuarios as $itemUsuarios)

                                @php
                                    // Obtener el nombre según el tipo de registro
                                    $nombreCompleto = null;
                                    if ($itemUsuarios->registro) {
                                        if ($itemUsuarios->registro_type == 'App\Models\instructor') {
                                            $nombreCompleto = $itemUsuarios->registro->nombre . ' ' . 
                                                            ($itemUsuarios->registro->apellidoPaterno ?? '') . ' ' . 
                                                            ($itemUsuarios->registro->apellidoMaterno ?? '');
                                        } elseif ($itemUsuarios->registro_type == 'App\Models\funcionario') {
                                            $nombreCompleto = $itemUsuarios->registro->nombre_trabajador;
                                        }
                                    }
                                    $nombreCompleto = $nombreCompleto ? trim($nombreCompleto) : null;
                                @endphp

                                    <div class="row {{ is_null($nombreCompleto) || trim($nombreCompleto) === '' ? 'na-row' : '' }}" role="row" data-nombre="{{ $nombreCompleto ?? 'N/A' }}">
                                        <div class="col-md-6" role="gridcell">
                                            <div class="form-control-plaintext text-truncate">{{ $nombreCompleto ?? 'N/A' }}</div>
                                        </div>

                                        <div class="col-md-2 text-center" role="gridcell">
                                            <div class="form-control-plaintext text-truncate">
                                                <a href="{{route('usuarios.perfil.modificar', ['id' => base64_encode($itemUsuarios->id)])}}" class="btn btn-info btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR USUARIO">
                                                    <i class="fa fa-user" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="col-md-2 text-center" role="gridcell">
                                            <div class="form-control-plaintext text-truncate">
                                                <a href="{{route('usuarios_permisos.show', ['id' => base64_encode($itemUsuarios->id)])}}" class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR REGISTRO">
                                                    <i class="fa fa-wrench" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="col-md-2 text-center" role="gridcell">
                                            <div class="form-control-plaintext text-truncate">
                                                <a href="{{route('permiso-rol-menu.usuarios.permisos', ['id' => $itemUsuarios->id])}}" class="btn btn-success btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="VER Y MODIFICAR PERMISOS">
                                                    <i class="fa fa-braille" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-block text-center card-footer">
                                <!--footer-->
                                {{ $usuarios->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('toggleNARows');
    let showingNA = false;
    
    function toggleNARows() {
        // Buscar las filas N/A cada vez que se hace clic (por si hay paginación)
        const naRows = document.querySelectorAll('.na-row');
        console.log('Filas N/A encontradas:', naRows.length);
        
        showingNA = !showingNA;
        
        naRows.forEach(function(row) {
            if (showingNA) {
                row.style.display = 'flex';
                row.classList.add('show');
            } else {
                row.style.display = 'none';
                row.classList.remove('show');
            }
        });
        
        // Actualizar el texto y icono del botón
        if (showingNA) {
            toggleButton.innerHTML = '<i class="fa fa-eye" aria-hidden="true"></i> Ocultar N/A';
        } else {
            toggleButton.innerHTML = '<i class="fa fa-eye-slash" aria-hidden="true"></i> Mostrar N/A';
        }
    }
    
    // Asegurar que las filas N/A estén ocultas al cargar la página
    setTimeout(function() {
        const naRows = document.querySelectorAll('.na-row');
        naRows.forEach(function(row) {
            row.style.display = 'none';
        });
        console.log('Filas N/A inicialmente ocultas:', naRows.length);
    }, 100);
    
    toggleButton.addEventListener('click', toggleNARows);
});
</script>
@endpush
