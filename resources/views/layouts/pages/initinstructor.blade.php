<!--Creado por Orlando Chavez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Instructor | SIVyC Icatech')
<!--seccion-->
@section('content')
    <style>
        * {
        box-sizing: border-box;
        }

        #myInput {
        background-image: url('img/search.png');
        background-position: 5px 10px;
        background-repeat: no-repeat;
        background-size: 32px;
        width: 100%;
        font-size: 16px;
        padding: 12px 20px 12px 40px;
        border: 1px solid #ddd;
        margin-bottom: 12px;
        }
    </style>
    <div class="container g-pt-50">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Registro de Instructrores</h2>
                </div>
                <br>
                <div class="pull-right">
                    <a class="btn btn-success btn-lg" href="{{route('instructor-crear')}}"> Nuevo</a>
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered">
            <caption>Catalogo de Instructrores</caption>
            <thead>
                <tr>
                    <th scope="col">Clave Instructor</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">telefono</th>
                    <th scope="col">Correo</th>
                    <th width="160px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                    <th scope="row">{{$itemData->numero_control}}</th>
                        <td>{{$itemData->nombre}}</td>
                        <td>{{$itemData->telefono}}</td>
                        <td>{{$itemData->correo}}</td>
                        <td>
                            <a class="btn btn-info" href="{{route('instructor-ver', ['id' => $itemData->id])}}">Mostrar</a>
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
        <br>
    </div>
    <br>
@endsection
