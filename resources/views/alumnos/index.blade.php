@extends('theme.sivyc.layout')

@section('title', 'Alumnos | SIVyC Icatech')

@push('content_css_sign')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{ asset('css/alumnos/consulta.css') }}" />
@endpush

@section('content')
<div class="card-header rounded-lg shadow d-flex justify-content-between align-items-center">
    <div class="col-md-8">
        <span>Registro de aspirantes</span>
    </div>

    <div class="col-md-4 curp-nuevo-compacto justify-content-end">
        <form class="w-100 m-0 p-0" style="display: contents;" method="POST" action="{{ route('alumnos.consultar.curp') }}">
            @csrf
            {{-- * INPUT CURP --}}
            <input type="text" class="form-control d-none" placeholder="Ingrese la CURP" id="registro_curp" name="curp">
            {{-- * DESPLIEGUE NUEVO REGISTRO --}}
            <button class="btn btn-white btn-nuevo text-dark align-items-center" title="Crear nuevo registro"
                type="button" id="btn_nuevo_registro_curp">
                <i class="fas fa-plus m-0 mr-2" style="font-size:1.1rem;"></i>
                <span class="d-none d-md-inline">Nuevo registro</span>
            </button>
            {{-- * REGISTRAR --}}
            <button class="btn btn-primary btn-interaccion d-none rounded" title="Iniciar registro CURP" type="submit"
                id="btn_iniciar_registro_curp">
                <i class="fas fa-user-plus" style="font-size:1.1rem;"></i>
            </button>
            {{-- * CERRAR INPUT CURP --}}
            <button class="btn btn-danger btn-interaccion d-none rounded" title="Cerrar registro CURP" type="button"
                id="btn_cerrar_registro_curp">
                <i class="fas fa-times m-0" style="font-size:1.1rem;"></i>
            </button>
        </form>
    </div>
</div>
<div class="card card-body">
    <!-- Buscador -->
    <div class="row mb-3">
        <div class="col-md-12">
            <form method="GET" class="d-flex align-items-center gap-2 buscador-form">
                <input type="text" name="busqueda" class="form-control"
                    placeholder="Buscar por nombre, CURP o matrícula..." value="{{ request('busqueda') }}">
                <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
                <button type="submit" class="btn btn-primary" title="Buscar">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('busqueda'))
                <a href="{{ route('alumnos.paginado') }}" class="btn-personalizado" title="Limpiar búsqueda">Limpiar</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Información de paginación -->
    <div class="row mb-3 align-items-start" style="min-height: 48px;">
        <div class="col-md-12 d-flex align-items-center" style="gap: 1.5rem;">
            <p class="text-muted mb-0">
                Mostrando {{ $alumnos->firstItem() }} a {{ $alumnos->lastItem() }} de {{ $alumnos->total() }} registros
                @if(request('busqueda'))
                <span class="badge bg-danger ms-3 ml-3">Filtrado por: "{{ request('busqueda') }}"</span>
                @endif
            </p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="">
                <tr>
                    <th>NOMBRE COMPLETO</th>
                    <th>CURP</th>
                    <th class="text-center">FECHA ACTUALIZACIÓN</th>
                    <th>ACTUALIZADO POR</th>
                    <th class="text-center">DOCUMENTOS</th>
                    <th class="text-center">EDITAR</th>
                    <th class="text-center">CURSO EXTRA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alumnos as $alumno)
                <tr>
                    <td>{{ $alumno->nombre }} {{ $alumno->apellido_paterno }} {{ $alumno->apellido_materno }}</td>
                    <td>{{ $alumno->curp }}</td>
                    <td class="text-center">{{ $alumno->updated_at ? $alumno->updated_at->format('d-m-Y') : '' }}</td>
                    <td class="text-uppercase">{{ $alumno->realizo}}</td>
                    <td class="text-center">
                        @if(!empty($alumno->requisitos['documento']))
                        <a href="{{ $alumno->requisitos['documento'] }}"
                            class="btn btn-sm btn-outline-danger d-flex justify-content-between align-items-center px-2 py-1 shadow-sm"
                            target="_blank" title="Ver documentos PDF">
                            <i class="fas fa-file-pdf"></i>
                            <span class="d-none d-md-inline ml-2 flex-grow-1 text-end">DOCS</span>
                        </a>
                        @else
                        <span class="text-muted">Sin documento</span>
                        @endif
                    </td>
                    <td>
                        <a href=""
                            class="btn btn-sm btn-outline-warning d-flex justify-content-between align-items-center px-2 py-1 shadow-sm"
                            title="Editar información del alumno">
                            <i class="fas fa-edit"></i>
                            <span class="d-none d-md-inline ml-2 flex-grow-1 text-end">Editar</span>
                        </a>
                    </td>
                    <td>
                        <a href=""
                            class="btn btn-sm btn-outline-success d-flex justify-content-between align-items-center px-2 py-1 shadow-sm"
                            title="Ver cursos extra del alumno">
                            <i class="fas fa-graduation-cap"></i>
                            <span class="d-none d-md-inline ml-2 flex-grow-1 text-end">Extra</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">
                        @if(request('busqueda'))
                        No se encontraron alumnos que coincidan con la búsqueda "{{ request('busqueda') }}".
                        @else
                        No hay registros de alumnos disponibles.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Enlaces de paginación -->
    <div class="d-flex justify-content-center">
        {{ $alumnos->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection

@push('script_sign')
<script src="{{ asset('js/alumnos/consulta.js') }}"></script>
<script>
$(document).ready(function() {
    // Inicializar validación para el formulario de CURP
    var form = $("form[action='{{ route('alumnos.consultar.curp') }}']");
    var notyf = new Notyf({ duration: 5000, position: { x: 'right', y: 'top' } });
    form.validate({
        rules: {
            curp: {
                required: true,
                minlength: 18,
                maxlength: 18
            }
        },
        messages: {
            curp: {
                required: "Ingrese la CURP a 18 caracteres.",
                minlength: "La CURP debe tener 18 caracteres.",
                maxlength: "La CURP debe tener 18 caracteres."
            }
        },
        errorPlacement: function(error, element) {
            // No mostrar mensajes inline
        },
        invalidHandler: function(event, validator) {
            if (validator.errorList.length) {
                notyf.dismissAll();
                validator.errorList.forEach(function(err) {
                    notyf.error(err.message);
                });
            }
        },
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        }
    });

    // Mostrar input y botón de registro al dar click en "Nuevo registro"
    $('#btn_nuevo_registro_curp').on('click', function() {
        $('#registro_curp').removeClass('d-none').focus();
        $('#btn_iniciar_registro_curp, #btn_cerrar_registro_curp').removeClass('d-none');
        $(this).addClass('d-none');
    });
    // Ocultar input y botones al cerrar
    $('#btn_cerrar_registro_curp').on('click', function() {
        $('#registro_curp').addClass('d-none').val('');
        $('#btn_iniciar_registro_curp, #btn_cerrar_registro_curp').addClass('d-none');
        $('#btn_nuevo_registro_curp').removeClass('d-none');
    });
    // Validar antes de enviar
    $('#btn_iniciar_registro_curp').on('click', function(e) {
        if(!form.valid()) {
            e.preventDefault();
            // Los mensajes ya se muestran con Notyf en invalidHandler
        }
    });
});
</script>
@endpush