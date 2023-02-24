@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Organismos Publicos | SIVyC Icatech')

@section('content')
    <div class="container-fluid g-pt-30 px-5">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="row my-3">
            <div class="col">
                <h1>ORGANISMOS</h1>
            </div>
        </div>
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
                        <a class="btn btn-success btn-lg" href="{{ route('organismos.agregar') }}">Agregar</a>
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
                                <a class="btn btn-warning btn-circle m-1 btn-circle-sm" data-toggle="tooltip"
                                        data-placement="top" title="EDITAR ORGANISMOS"
                                        href="{{ route('organismos.agregar', ['id' => base64_encode($og->id)]) }}">
                                        <i class="fa fa-pencil-square-o fa-2x mt-2" aria-hidden="true"></i>
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