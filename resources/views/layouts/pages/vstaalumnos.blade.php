@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Alumonos | SIVyC Icatech')
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
                    <h2>Registro de Alumnos</h2>
                </div>
                <br>
                <div class="pull-right">
                    <a class="btn btn-success btn-lg" href="{{route('inscripcion-paso1')}}"> Nuevo</a>
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        @if ($contador > 0)
            <table  id="table-instructor" class="table table-bordered">
                <caption>Catalogo de Alumnos</caption>
                <thead>
                    <tr>
                        <th scope="col">N° Control</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">telefono</th>
                        <th scope="col">Correo</th>
                        <th width="160px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($retrieveAlumnos as $itemData)
                        <tr>
                        <th scope="row">{{$itemData->no_control}}</th>
                            <td>{{$itemData->nombre}}</td>
                            <td>{{$itemData->telefono}}</td>
                            <td>{{$itemData->correo}}</td>
                            <td>
                                <a class="btn btn-info" href="{{route('instructor-ver', ['id' => $itemData->no_control])}}">Mostrar</a>
                                {!! Form::open(['method' => 'DELETE','route' => ['usuarios'],'style'=>'display:inline']) !!}
                                {!! Form::submit('Borrar', ['class' => 'btn btn-danger']) !!}
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="alert alert-warning" role="alert">
                <h2>No hay Alumnos Registrados!</h2>
            </div>
        @endif
        <br>
    </div>
    <br>
@endsection
