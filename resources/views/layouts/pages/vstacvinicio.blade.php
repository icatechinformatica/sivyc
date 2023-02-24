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

                    {!! Form::open(['route' => 'cursos_validados.index', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                        <select name="tipobusquedacursovalidado" class="form-control mr-sm-2" id="tipobusquedacursovalidado">
                            <option value="">BUSCAR POR TIPO</option>
                            <option value="arc01">MEMO DE SOLICITUD ARC-01</option>
                            <option value="clave">CLAVE DEL CURSO</option>
                            <option value="nombre_curso">NOMBRE DEL CURSO</option>
                            <option value="instructor">INSTRUCTORES</option>
                            <option value="unidad">UNIDAD</option>
                            <option value="anio">AÑO</option>
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
                    <th scope="col">MEMO DE SOLICITUD ARC-01</th>
                    <th scope="col">NOMBRE DEL CURSO</th>
                    <th scope="col">TIPO DE CURSO</th>
                    <th scope="col">INSTRUCTOR</th>
                    <th scope="col">FECHA IMPARTIR</th>
                    <th scope="col">Documentos</th>
                </tr>
            </thead>
            @if (count($data) > 0)
                <tbody>
                    @foreach ($data as $itemData)
                        <tr>
                            <td>{{$itemData->unidad}}</td>
                            <td>{{$itemData->clave}}</td>
                            <td>{{$itemData->munidad}}</td>
                            <td>{{$itemData->nombrecur}}</td>
                            <td>{{$itemData->tcapacitacion}}</td>
                            <td>{{$itemData->nombre}} {{$itemData->apellidoPaterno}} {{$itemData->apellidoMaterno}}</td>
                            <td>{{ \Carbon\Carbon::parse($itemData->inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($itemData->termino)->format('d/m/Y') }}</td>
                            <td>
                                <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Validación de clave de curso" href="{{$itemData->pdf_curso}}" target="_blank">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                                <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="Validación de instructor" href="{{$itemData->archivo_alta}}" target="_blank">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">
                            {{ $data->appends(request()->query())->links() }}
                        </td>
                    </tr>
                </tfoot>
            @else
                <tbody>
                    <tr>
                        <td colspan="5">
                            <h4>
                                <center>
                                    <b>NO HAY REGISTROS DISPONIBLES</b>
                                </center>
                            </h4>
                        </td>
                    </tr>
                </tbody>
            @endif
        </table>
        <br>
    </div>
    <br>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
@endsection
