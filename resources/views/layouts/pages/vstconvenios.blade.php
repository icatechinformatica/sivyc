<!--Creado por Daniel Méndez-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Convenios | SIVyC Icatech')
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

    <div class="container-fluid px-5 g-pt-30">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h1>Convenios</h1>
                </div>
                @can('convenios.create')
                    <div class="pull-right">
                        <a class="btn btn-success btn-lg" href="{{ route('convenio.create') }}">NUEVO</a>
                    </div>
                @endcan
            </div>
        </div>

        <div class="form-row">
            <div class="col">
                {!! Form::open(['route' => 'convenios.index', 'method' => 'GET', 'class' => 'form-inline']) !!}
                <select name="busqueda" class="form-control mr-sm-2" id="busqueda">
                    <option value="">BUSCAR POR TIPO</option>
                    <option value="no_convenio">N° DE CONVENIO</option>
                    <option value="institucion">INSTITUCIÓN</option>
                    <option value="tipo_convenio">TIPO DE CONVENIO</option>
                    <option value="sector">SECTOR</option>
                </select>

                {!! Form::text('busqueda_conveniopor', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR',
                'aria-label' => 'BUSCAR']) !!}
                <button type="submit" class="btn btn-outline-primary">BUSCAR</button>
                {!! Form::close() !!}
            </div>
        </div>

        <table id="table-instructor" class="table table-bordered table-striped mt-5">
            <thead>
                <tr>
                    <th scope="col">NO. DE CONVENIO</th>
                    <th scope="col">INSTITUCIÓN</th>
                    <th width="150px">FECHA DE FIRMA</th>
                    <th width="150px">FECHA DE TERMINO</th>
                    <th width="150px">TIPO DE CONVENIO</th>
                    <th width="150px">SECTOR</th>
                    <th scope="col">ARCHIVO CONVENIO</th>
                    @can('convenios.edit')
                        <th scope="col">MODIFICAR</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $itemData)
                    <tr>
                        <td scope="row">{{ $itemData->no_convenio }}</td>
                        <td>{{ $itemData->institucion }}</td>
                        <td>{{ $itemData->fecha_firma }}</td>
                        <td>{{ $itemData->fecha_vigencia }}</td>
                        <td>{{ $itemData->tipo_convenio }}</td>
                        <td>{{ $itemData->sector }}</td>
                        <td>
                            <div class="custom-file">
                                @if (isset($itemData->archivo_convenio))
                                    <a href="{{ $itemData->archivo_convenio }}" target="_blank"
                                        rel="{{ $itemData->archivo_convenio }}">
                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="50px"
                                            height="50px">
                                    </a>
                                @else
                                    NO ADJUNTADO
                                @endif
                            </div>
                        </td>
                        @can('convenios.edit')
                            <td>
                                <a class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="EDITAR CONVENIO"
                                    href="{{ route('convenios.edit', ['id' => base64_encode($itemData->id)]) }}">
                                    <i class="fa fa-pencil-square-o fa-2x mt-2" aria-hidden="true"></i>
                                </a>
                            </td>
                        @endcan
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row py-4">
        <div class="col d-flex justify-content-center">
            {{ $data->links() }}
        </div>
    </div>
@endsection
