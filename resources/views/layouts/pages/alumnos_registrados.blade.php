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
                        <th scope="col">N° Control</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Curp</th>
                        <th width="160px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumnos as $itemData)
                        <tr>
                            <td>{{$itemData->no_control}}</td>
                            <td scope="row">{{$itemData->nombrealumno}} {{$itemData->apellidoPaterno}} {{$itemData->apellidoMaterno}}</td>
                            <td>{{$itemData->curp_alumno}}</td>
                            <td>
                                <a class="btn btn-info" href="{{route('alumnos.inscritos.detail', ['id' => $itemData->id_registro])}}">VER</a>
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
