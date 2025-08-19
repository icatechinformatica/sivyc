
<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'PERMISOS | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <div class="row align-items-center">
                            <div class="col-8">
                              <h3 class="mb-0">PERMISOS</h3>
                            </div>
                            <div class="col-4 text-right">
                              <a href="{{ route('permisos.crear') }}" class="btn btn-sm btn-success">Nuevo permiso</a>
                            </div>
                        </div>
                    </div>
                    <!-- Light table -->
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">NOMBRE</th>
                                    <th scope="col">SLUG</th>
                                    <th scope="col">DESCRIPCIÓN</th>
                                    <th scope="col">MODIFICACIÓN</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @foreach ($permisos as $itemPermisos)
                                    <tr>
                                        <td scope="row">{{$itemPermisos->nombre}}</td>
                                        <td scope="row">{{$itemPermisos->ruta_corta}}</td>
                                        <td scope="row">{{$itemPermisos->descripcion}}</td>
                                        <td>
                                            <a href="{{route('permisos.editar', ['id' => base64_encode($itemPermisos->id)])}}" class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR REGISTRO">
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
                                    {{ $permisos->appends(request()->query())->links('pagination::bootstrap-4') }}
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
