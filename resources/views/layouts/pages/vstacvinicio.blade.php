<!--Creado por Orlando Chavez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Cursos Validados | SIVyC Icatech')
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
                    <h2>Cursos Validados</h2>
                </div>
                <br>
                <div class="pull-right">
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered">
            <caption>Lista de Cursos Validados</caption>
            <thead>
                <tr>
                    <th scope="col">Clave de Curso</th>
                    <th scope="col">Nombre de Curso</th>
                    <th scope="col">Nombre de Instructor</th>
                    <th width="160px">Estatus</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                        <td>{{$itemData->clave_curso}}</td>
                        <td>{{$itemData->nombrecur}}</td>
                        <td>{{$itemData->nombreins}}</td>
                        <td>
                            <a class="btn btn-info" href="{{route('instructor-ver', ['id' => $itemData->id])}}">Mostrar</a>
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
