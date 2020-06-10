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
                        <th scope="col">NOMBRE</th>
                        <th scope="col">CURP</th>
                        <th scope="col">CURSO INSCRITO</th>
                        <th scope="col">MODIFICAR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumnos as $itemAlumnos)
                        <tr>
                            <td>{{$itemAlumnos->no_control}}</td>
                            <td scope="row">{{$itemAlumnos->apellidoPaterno}} {{$itemAlumnos->apellidoMaterno}} {{$itemAlumnos->nombrealumno}}</td>
                            <td>{{$itemAlumnos->curp_alumno}}</td>
                            <td>{{$itemAlumnos->nombre_curso}}</td>
                            <td>
                                <a href="{{route('alumno_registrado.modificar.show', ['id' => base64_encode($itemAlumnos->preiscripcion)])}}" class="btn btn-info btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="MODIFICAR REGISTRO">
                                    <i class="fa fa-database" aria-hidden="true"></i>
                                </a>
                            </td>
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
