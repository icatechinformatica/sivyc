<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'usuarios | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                              <h3 class="mb-0">Usuarios de SIVYC</h3>
                            </div>
                            <div class="col-4 text-right">
                              <a href="{{route('usuarios.perfil.crear')}}" class="btn btn-sm btn-success">Nuevo Usuario</a>
                            </div>
                        </div>
                    </div>
                    <!-- Light table -->
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">NOMBRE</th>
                                    <th scope="col">INFORMACIÓN</th>
                                    <th scope="col">MODIFICAR</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @foreach ($usuarios as $itemUsuarios)
                                    <tr>
                                        <td scope="row">{{$itemUsuarios->name}}</td>
                                        <td>
                                            <a href="{{route('usuarios.perfil.modificar', ['id' => base64_encode($itemUsuarios->id)])}}" class="btn btn-info btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR USUARIO">
                                                <i class="fa fa-user" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{route('usuarios_permisos.show', ['id' => base64_encode($itemUsuarios->id)])}}" class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR REGISTRO">
                                                <i class="fa fa-wrench" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Card footer -->
                    <div class="card-footer py-4">
                        <nav aria-label="...">
                            <ul class="pagination justify-content-end mb-0">
                                <li class="page-item">
                                    {{ $usuarios->appends(request()->query())->links() }}
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER PORTAL DE GOBIERNO -->
        @include("theme.sivyc_admin.footer")
        <!-- FOOTER PORTAL DE GOBIERNO END-->
    </div>
@endsection
