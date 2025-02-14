@extends('theme.sivyc.layout')
@section('title', 'Organismos Públicos | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />    
@endsection
@section('content')
    <div class="card-header">
        Catálogos / Organismos Públicos
    </div>
    <div class="card card-body">    
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif       
        <div class="row">
            <div class="col">
                {!! Form::open(['route' => 'organismos.index', 'method' => 'GET', 'class' => 'form-inline']) !!}
                <select name="busqueda" class="form-control mr-sm-2" id="busqueda">
                    <option value="">BUSCAR POR TIPO</option>
                    <option value="nombre">NOMBRE</option>
                    <option value="area">ÁREA</option>
                </select>
                {!! Form::text('busqueda_por', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR',
                'aria-label' => 'BUSCAR']) !!}
                <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                {!! Form::close() !!}
            </div>
            @can('organismo.agregar')
                <div class="col">
                    <div class="pull-right">
                        <a class="btn " href="{{ route('organismos.agregar') }}">Agregar</a>
                    </div>
                </div>
            @endcan
        </div>
        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Titular</th>
                    <th scope="col">Sector</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Dirección</th>
                    <th scope="col">Status</th>
                    @can('organismo.agregar')
                        <th scope="col">Modificar</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
               @foreach ($organismos as $og)
                    <tr>
                        <td>{{ $og->organismo }}</td>
                        <td>{{$og->nombre_titular}}</td>
                        <td>{{$og->sector}}</td>
                        <td>{{$og->telefono}}</td>
                        <td>{{$og->correo}}</td>
                        <td>{{$og->direccion}}</td>
                        @if ($og->activo=='true')
                        <td>ACTIVO</td>
                        @else
                          <td>INACTIVO</td>
                        @endif
                        @can('organismo.agregar')
                            <td>
                                <a class="nav-link pt-0" data-toggle="tooltip"
                                        data-placement="top" title="EDITAR ORGANISMOS"
                                        href="{{ route('organismos.agregar', ['id' => base64_encode($og->id)]) }}">
                                        <i class="fa fa-edit  fa-2x fa-lg text-success" aria-hidden="true"></i>
                                    </a>
                            </td>
                        @endcan
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="row py-4">
            <div class="col d-flex justify-content-center">
                {{ $organismos->links() }}
            </div>
        </div>
    </div>
@endsection
