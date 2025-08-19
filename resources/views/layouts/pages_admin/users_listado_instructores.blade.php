<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'LISTADO DE INSTRUCTORES | Sivyc Icatech')
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
                    <h3 class="mb-0">Listado de Instructores</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>CURP</th>
                                    <th>RFC</th>
                                    <th>Nombre del Instructor</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Alta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registros as $registro)
                                <tr>
                                    <td>{{ $registro->curp_usuario }}</td>
                                    <td>{{ $registro->rfc_usuario }}</td>
                                    <td>{{ $registro->nombre_trabajador }}</td>
                                    <td class="text-center">
                                        @if ($registro->tipo === 'Instructor')
                                            <i class="fas fa-chalkboard-teacher text-warning fa-2x" title="Instructor"></i>
                                        @elseif ($registro->tipo === 'Funcionario')
                                            <i class="fas fa-user-tie text-info fa-2x" title="Funcionario"></i> 
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('usuarios.alta.instructores.post') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id_instructor" value="{{ $registro->f_id }}">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                title="Dar de alta al instructor">
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
