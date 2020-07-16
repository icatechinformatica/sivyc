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
                    <h2>CURSOS VALIDADOS</h2>

                    {!! Form::open(['route' => 'cursos.index', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                        <select name="tipobusquedacursovalidado" class="form-control mr-sm-2" id="tipobusquedacursovalidado">
                            <option value="">BUSCAR POR TIPO</option>
                            <option value="clave">CLAVE DEL CURSO</option>
                            <option value="nombre_curso">NOMBRE DEL CURSO</option>
                            <option value="instructor">INSTRUCTORES</option>
                        </select>

                        {!! Form::text('busqueda_curso_validado', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
                        <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                    {!! Form::close() !!}
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
