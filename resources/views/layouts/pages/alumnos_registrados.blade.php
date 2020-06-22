<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Alumnos | SIVyC Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Alumnos Matriculados</h2>
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">

            <table  id="table-instructor" class="table table-bordered Datatables">
                <caption>Catalogo de Alumnos</caption>
                <thead>
                    <tr>
                        <th scope="col">N° CONTROL</th>
                        <th scope="col">CURSOS</th>
                        <th scope="col">NOMBRE</th>
                        <th width="160px">ACCIONES</th>
                        @can('alumno.inscrito.edit')
                            <th scope="col">MODIFICAR</th>
                        @endcan

                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumnos as $itemData)
                        <tr>
                            <td>{{$itemData->no_control}}</td>
                            <td scope="row">{{ $itemData->nombre_curso }}</td>
                            <td scope="row">{{$itemData->nombrealumno}} {{$itemData->apellidoPaterno}} {{$itemData->apellidoMaterno}}</td>
                            @can('alumno.inscrito.show')
                                <td>
                                    <a href="{{route('alumnos.inscritos.detail', ['id' => base64_encode($itemData->id_registro)])}}" class="btn btn-success btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="VER REGISTRO">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                </td>
                            @endcan

                            @can('alumno.inscrito.edit')
                                <td>
                                    <a href="{{route('alumnos.update.registro', ['id' => base64_encode($itemData->id_registro)])}}" class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR REGISTRO">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    </a>
                                </td>
                            @endcan

                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                    </tr>
                </tfoot>
            </table>
        <br>
    </div>
    <br>
@endsection
