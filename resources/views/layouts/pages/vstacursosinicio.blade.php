<!--Creado por Orlando Chavez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('Cursos', 'SUPRE | SIVyC Icatech')
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
                    <h2>Catalogo de Cursos</h2>
                </div>
                <br>
                <div class="pull-right">
                    <a class="btn btn-success btn-lg" href="{{route('frm-cursos')}}"> Nuevo</a>
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered Datatables">
            <caption>Catalogo de Cursos</caption>
            <thead>
                <tr>
                    <th scope="col">Especialidad</th>
                    <th scope="col">Curso</th>
                    <th scope="col">Duración</th>
                    <th scope="col">Modalidad</th>
                    <th scope="col">Clasificación</th>
                    <th scope="col">Costo</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                    <th scope="row">{{$itemData->nombre}}</th>
                        <td>{{$itemData->nombre_curso}}</td>
                        <td>{{$itemData->duracion}}</td>
                        <td>{{$itemData->modalidad}}</td>
                        <td>{{$itemData->clasificacion}}</td>
                        <td>{{$itemData->costo}}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-circle m-1 btn-circle-sm" data-toggle="tooltip" data-placement="top" title="Editar Registro">
                                <i class="fa fa-wrench" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
    </div>
    <br>
@endsection
