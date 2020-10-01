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
                            <option value="unidad">UNIDAD</option>
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
                    <th scope="col">UNIDAD</th>
                    <th scope="col">CLAVE DE CURSO</th>
                    <th scope="col">NOMBRE DEL CURSO</th>
                    <th scope="col">INSTRUCTOR</th>
                    <th scope="col">FECHA IMPARTIR</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                        <td>{{$itemData->unidad}}</td>
                        <td>{{$itemData->clave}}</td>
                        <td>{{$itemData->nombrecur}}</td>
                        <td>{{$itemData->nombre}} {{$itemData->apellidoPaterno}} {{$itemData->apellidoMaterno}}</td>
                        <td>{{ \Carbon\Carbon::parse($itemData->inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($itemData->termino)->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">
                        {{ $data->appends(request()->query())->links() }}
                    </td>
                </tr>
            </tfoot>
        </table>
        <br>
    </div>
    <br>
@endsection
