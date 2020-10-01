<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', 'CONTROL DE ALUMNOS | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                              <h3 class="mb-0">PERMISOS ROLES</h3>
                                {!! Form::open(['route' => 'alumno_registrado.modificar.index', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                                    <select name="tipo_busqueda_por_alumno_registrado" class="form-control mr-sm-2" id="tipo_busqueda_por_alumno_registrado">
                                        <option value="">BUSCAR POR TIPO</option>
                                        <option value="no_control_busqueda">N° DE CONTROL</option>
                                        <option value="nombres">NOMBRE</option>
                                        <option value="curso">CURSO INSCRITO</option>
                                        <option value="curp">CURP</option>
                                    </select>

                                    {!! Form::text('busquedaporAlumnoRegistrado', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
                                    <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                                {!! Form::close() !!}
                            </div>
                            <div class="col-4 text-right">
                            </div>
                        </div>
                    </div>
                    <!-- Light table -->
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">N° CONTROL</th>
                                    <th scope="col">NOMBRE</th>
                                    <th scope="col">CURP</th>
                                    <th scope="col">MODIFICAR</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @foreach ($alumnos as $itemAlumnos)
                                    <tr class="{{ ($itemAlumnos->estatus_modificacion == true) ? "bg-warning" : "" }}">
                                        <td>{{$itemAlumnos->no_control}}</td>
                                        <td scope="row">{{$itemAlumnos->apellido_paterno}} {{$itemAlumnos->apellido_materno}} {{$itemAlumnos->nombrealumno}}</td>
                                        <td>{{$itemAlumnos->curp_alumno}}</td>
                                        <td>
                                            <a href="{{route('alumno_registrado.modificar.show', ['id' => base64_encode($itemAlumnos->preiscripcion)])}}" class="btn btn-info btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR REGISTRO">
                                                <i class="fa fa-database" aria-hidden="true"></i>
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
                                    {{ $alumnos->appends(request()->query())->links() }}
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
