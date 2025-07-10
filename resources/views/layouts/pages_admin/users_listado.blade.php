<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'LISTADO DE FUNCIONARIOS | Sivyc Icatech')
<!--contenido-->
@section('content')
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            @error('error')
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <span class="alert-text">{{ $message }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @enderror
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <span class="alert-text">{{ session('success') }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Listado de Funcionarios</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    {{-- <th>ID Funcionario</th> --}}
                                    {{-- <th>Clave Empleado</th> --}}
                                    <th>Nombre del Trabajador</th>
                                    <th>Organización</th>
                                    <th class="text-center">Alta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registros as $registro)
                                <tr>
                                    {{-- <td>{{ $funcionario->f_id }}</td> --}}
                                    {{-- <td>{{ $registro->clave_empleado }}</td> --}}
                                    <td>{{ $registro->nombre_trabajador }}</td>
                                    <td>{{ $registro->nombre}}</td>
                                    <td class="text-center">
                                        <form action="{{ route('usuarios.alta.funcionarios.post') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id_funcionario" value="{{ $registro->f_id }}">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                title="Dar de alta al funcionario">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-block text-center">
                        {{ $registros->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- FOOTER PORTAL DE GOBIERNO -->
    @include("theme.sivyc_admin.footer")
    <!-- FOOTER PORTAL DE GOBIERNO END-->
</div>
@endsection
@section('scripts_content')
@endsection