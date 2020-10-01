<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc_admin.layout')
<!--generado por Daniel Méndez-->
@section('title', ' ALUMNOS | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container-fluid mt--6">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div><br />
        @endif
        <div class="row">

            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                {!! Form::open(['route' => 'registrado_consecutivo.index', 'method' => 'POST', 'class' => 'form-inline' ]) !!}

                                {!! Form::text('busquedaConsecutivo', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
                                    <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                                {!! Form::close() !!}

                            </div>
                            <div class="col-4 text-right">
                                <a href="{{URL::previous()}}" class="btn btn-sm btn-danger">REGRESAR</a>
                            </div>
                        </div>
                    </div>
                    <!-- Light table -->
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead>
                                <tr>
                                    <th scope="col">N° CONTROL</th>
                                    <th scope="col">AÑO</th>
                                    <th scope="col">CONSECUTIVO</th>
                                    <th scope="col">UNIDAD</th>
                                    <th scope="col">ALUMNO PREREGISTRO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($consecutivos_unidad as $itemconsetuvitos)
                                    <tr>
                                        <td>{{$itemconsetuvitos->no_control}}</td>
                                        <td>{{$itemconsetuvitos->anio}}</td>
                                        <td>{{$itemconsetuvitos->consecutivo}}</td>
                                        <td>{{ $itemconsetuvitos->unidad }}</td>
                                        <td>{{ $itemconsetuvitos->id_pre }}</td>
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
@stop
