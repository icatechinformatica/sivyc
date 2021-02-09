<!--Creado por Orlando Chavez c-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'UNIDADES | SIVyC Icatech')
<!--seccion-->
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
        <div class="card-header">Lista de Unidades</div>
        <div class="card card-body">
            <div class="row ">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left" >
                        {!! Form::open(['route' => 'unidades.inicio', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                            <select name="tipo_unidad" class="form-control mr-sm-2" id="tipo_unidad">
                                <option value="">BUSCAR POR TIPO</option>
                                <option value="unidad">UNIDAD</option>
                                <option value="cct">CCT</option>
                                <option value="director">DIRECTOR</option>
                                <option value="ubicacion">UBICACIÓN</option>
                            </select>
                            {!! Form::text('busquedaporUnidad', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR']) !!}
                            <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                        {!! Form::close() !!}
                    </div>
                    <br>
                </div>
            </div>
            <table  id="table-instructor" class="table">
                <caption>Catalogo de Unidades</caption>
                <thead>
                    <tr>
                        <th scope="col">UNIDAD</th>
                        <th scope="col">CCT</th>
                        <th scope="col">DIRECTOR</th>
                        <th scope="col">UBICACION</th>
                        <th scope="col">ACCIÓN</th>
                    </tr>
                </thead>
                @if (count($data) > 0)
                    <tbody>
                        @foreach ($data as $itemData)
                            <tr>
                                <td>{{$itemData->unidad}}</td>
                                <td>{{$itemData->cct}}</td>
                                <td>{{$itemData->dunidad}}</td>
                                <td>{{$itemData->ubicacion}}</td>
                                <td>
                                    <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Modificar" href="{{route('unidades.editar', ['id' => $itemData->id])}}">
                                        <i class="fa fa-wrench" aria-hidden="true"></i>
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
        </div>
        <br>
    <br>
@endsection
