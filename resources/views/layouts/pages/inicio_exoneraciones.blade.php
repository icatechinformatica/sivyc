@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'EXONERACIONES | Sivyc Icatech')

@section('content')

    <div class="container-fluid px-5 mt-4">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="row">
            <div class="col">
                <h1>EXONERACIONES</h1>
            </div>
        </div>

        <div class="row">
            <div class="col">
                {!! Form::open(['route' => 'exoneraciones.inicio', 'method' => 'GET', 'class' => 'form-inline']) !!}
                <select name="busqueda" class="form-control mr-sm-2" id="busqueda">
                    <option value="">BUSCAR POR TIPO</option>
                    <option value="id_unidad_capacitacion">UNIDAD DE CAPACITACIÓN</option>
                    <option value="no_memorandum">NÚMERO MEMORANDUM</option>
                    <option value="no_convenio">NÚMERO DE CONVENIO</option>
                    <option value="tipo_exoneracion">TIPO DE EXONERACION</option>
                </select>

                {!! Form::text('busqueda_exoneracionpor', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR',
                'aria-label' => 'BUSCAR']) !!}
                <button type="submit" class="btn btn-outline-primary">BUSCAR</button>
                {!! Form::close() !!}
            </div>

            <div class="col">
                @can('exoneraciones.create')
                    <div class="pull-right">
                        <a class="btn btn-success btn-lg" href="{{ route('exoneraciones.agregar') }}">Agregar</a>
                    </div>
                @endcan
            </div>
        </div>

        <table class="table table-bordered table-striped mt-5">
            <thead>
                <tr>
                    <th scope="col">NO. DE MEMORANDUM</th>
                    <th scope="col">UNIDAD DE CAPACITACIÓN</th>
                    <th scope="col">FECHA DE MEMORANDUM</th>
                    <th scope="col">TIPO DE EXONERACIÓN</th>
                    <th scope="col">NO. DE CONVENIO</th>
                    <th scope="col">ARCHIVO SOPORTE</th>
                    @can('exoneraciones.edit')
                        <th scope="col">MODIFICAR</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach ($exoneraciones as $exoneracion)
                    <tr>
                        <td>{{$exoneracion->no_memorandum}}</td>
                        <td>{{$exoneracion->unidad_capacitacion == null ? 'TODAS LAS UNIDADES' : $exoneracion->unidad_capacitacion}}</td>
                        <td>{{$exoneracion->fecha_memorandum}}</td>
                        <td>{{$exoneracion->tipo_exoneracion}}</td>
                        <td>{{$exoneracion->no_convenio}}</td>
                        <td class="text-center">
                            <div class="custom-file">
                                @if (isset($exoneracion->memo_soporte_dependencia))
                                    <a href="{{ $exoneracion->memo_soporte_dependencia }}" target="_blank"
                                        rel="{{ $exoneracion->memo_soporte_dependencia }}">
                                        <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="50px"
                                            height="50px">
                                    </a>
                                @else
                                    NO ADJUNTADO
                                @endif
                            </div>
                        </td>

                        @can('exoneraciones.edit')
                            <td class="text-center">
                                <a class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                    data-placement="top" title="EDITAR EXONERACIÓN"
                                    href="{{ route('exoneraciones.edit', ['id' => base64_encode($exoneracion->id)]) }}">
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
            {{ $exoneraciones->links() }}
        </div>
    </div>
@endsection