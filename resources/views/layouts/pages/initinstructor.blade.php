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
                    <h2>REGISTRO DE INSTRUCTORES</h2>

                    {!! Form::open(['route' => 'instructor-inicio', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                        <select name="tipo_busqueda_instructor" class="form-control mr-sm-2" id="tipo_busqueda_instructor">
                            <option value="">BUSCAR POR TIPO</option>
                            <option value="clave_instructor">CLAVE INSTRUCTOR</option>
                            <option value="nombre_instructor">NOMBRE</option>
                            <option value="telefono_instructor">TELÉFONO</option>
                            <option value="estatus_instructor">ESTATUS</option>
                        </select>

                        {!! Form::text('busquedaPorInstructor', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
                        <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                    {!! Form::close() !!}

                </div>
                <br>
                <div class="pull-right">
                    @can('instructor.create')
                        <a class="btn btn-success btn-lg" href="{{route('instructor-crear')}}"> Nuevo</a>
                    @endcan
                    @can('icatalogoinstructores')
                        <a class="btn btn-info btn-lg" href="{{route('academico.exportar.instructores')}}">Exportar Instructores</a>
                    @endcan
                </div>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered table-responsive-md">
            <caption>Catalogo de Instructrores</caption>
            <thead>
                <tr>
                    <th scope="col">Clave Instructor</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">telefono</th>
                    <th scope="col">Status</th>
                    <th width="160px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                    <th scope="row">{{$itemData->numero_control}}</th>
                        <td>{{$itemData->apellidoPaterno}} {{$itemData->apellidoMaterno}} {{$itemData->nombre}}</td>
                        <td>{{$itemData->telefono}}</td>
                        <td>{{$itemData->status}}</td>
                        <td>
                            @if ($itemData->status == 'En Proceso')
                                @can('instructor.validar')
                                    <a class="btn btn-info" href="{{route('instructor-validar', ['id' => $itemData->id])}}">Validar</a>
                                @endcan
                            @endif
                            @if ($itemData->status == 'Rechazado')
                                @can('instructor.editar_fase1')
                                    <a class="btn btn-info" href="{{route('instructor-editar', ['id' => $itemData->id])}}">Editar</a>
                                @endcan
                            @endif
                            @if ($itemData->status == 'Aprobado' || $itemData->status == 'Baja')
                                    <a class="btn btn-info" href="{{route('instructor-ver', ['id' => $itemData->id])}}">Mostrar</a>
                            @endif
                            @if ($itemData->status == 'Validado')
                                    <a class="btn btn-info" href="{{route('instructor-ver', ['id' => $itemData->id])}}">Mostrar</a>
                            @endif
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
        </table>
        <br>
    </div>
    <br>
@endsection
