@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Pagos | SIVyC Icatech')
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
                    <h2>Registro de Pagos</h2>
                </div>
                <br>
                <div class="pull-right">
                    <a class="btn btn-success btn-lg" href="{{route('instructor-crear')}}"> Nuevo</a>
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered">
            <caption>Lista de Pagos</caption>
            <thead>
                <tr>
                    <th scope="col">No. Memorandum</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Cantidad</th>
                    <th width="160px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                    <th scope="row">{{$itemData->no_memo}}</th>
                        <td>{{$itemData->fecha}}</td>
                        <td>Nombre a pagar</td>
                        <td>Cantidad a pagar</td>
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
