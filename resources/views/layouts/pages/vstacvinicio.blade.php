<!--Creado por Orlando Chavez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'SUPRE | SIVyC Icatech')
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
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered table-responsive-md Datatables">
            <caption>Catalogo de Cursos Validados</caption>
            <thead>
                <tr>
                    <th scope="col">Clave de Curso</th>
                    <th scope="col">Nombre del Curso</th>
                    <th scope="col">Instructor</th>
                    <th scope="col">Fecha Impartir</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                    <th scope="row">{{$itemData->clave}}</th>
                        <td>{{$itemData->nombrecur}}</td>
                        <td>{{$itemData->nombre}} {{$itemData->apellidoPaterno}} {{$itemData->apellidoMaterno}}</td>
                        <td>{{$itemData->inicio}} al {{$itemData->termino}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
    </div>
    <br>
@endsection
