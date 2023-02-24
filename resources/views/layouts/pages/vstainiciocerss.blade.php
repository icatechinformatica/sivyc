<!--Creado por Orlando Chavez-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'CERSS | SIVyC Icatech')
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
                    <h2>Lista de CERSS</h2>
                    {!! Form::open(['route' => 'cerss.inicio', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
                        <select name="tipo_cerss" class="form-control mr-sm-2" id="tipo_suficiencia">
                            <option value="">BUSCAR POR TIPO</option>
                            <option value="nombre">NOMBRE</option>
                            <option value="titular">TITULAR</option>
                        </select>

                        {!! Form::text('busquedaporCerss', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR', 'aria-label' => 'BUSCAR', 'value' => 1]) !!}
                        <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                    {!! Form::close() !!}
                </div>
                <br>
                @can('cerss.create')
                    <div class="pull-right">
                        <a class="btn btn-success btn-lg" href="{{route('cerss.frm')}}">Nuevo</a>
                    </div>
                @endcan
            </div>
        </div>
        <hr style="border-color:dimgray">
        <table  id="table-instructor" class="table table-bordered">
            <caption>Catalogo de Solcitudes</caption>
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Municipio</th>
                    <th scope="col">Titular</th>
                    <th scope="col">Telefono</th>
                    <th scope="col">Status</th>
                    <th width="180px">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key=>$itemData)
                    <tr>
                    <th scope="row">{{$itemData->nombre}}</th>
                        <td>{{$muni[$key]->muni}}</td>
                        <td>{{$itemData->titular}}</td>
                        <td>{{$itemData->telefono}}</td>
                        @if ($itemData->activo == TRUE)
                            <td>Activo</td>
                        @else
                            <td>Inactivo</td>
                        @endif
                        <td>
                            @can('cerss.update')
                                <a class="btn btn-warning btn-circle m-1 btn-circle-sm" title="Editar" href="{{route('cerss.update', ['id' => $itemData->id])}}">
                                    <i class="fa fa-wrench" aria-hidden="true"></i>
                                </a>
                            @endcan
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
@endsection
