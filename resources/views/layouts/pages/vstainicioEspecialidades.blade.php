@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Especialidades | SIVyC Icatech')

@section('content')

    <div class="container-fluid g-pt-30 px-5">

        <div class="row my-3">
            <div class="col">
                <h1>ESPECIALIDADES</h1>
            </div>
        </div>

        <div class="row">
            <div class="col">
                {!! Form::open(['route' => 'especialidades.inicio', 'method' => 'GET', 'class' => 'form-inline']) !!}
                <select name="busqueda" class="form-control mr-sm-2" id="busqueda">
                    <option value="">BUSCAR POR TIPO</option>
                    <option value="clave">CLAVE</option>
                    <option value="nombre">NOMBRE</option>
                    <option value="prefijo">PREFIJO</option>
                    <option value="area">ÁREA</option>
                </select>

                {!! Form::text('busqueda_aspirantepor', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR',
                'aria-label' => 'BUSCAR']) !!}
                <button class="btn btn-outline-info my-2 my-sm-0" type="submit">BUSCAR</button>
                {!! Form::close() !!}
            </div>

            <div class="col">
                <div class="pull-right">
                    <a class="btn btn-success btn-lg" href="{{ route('especialidades.agregar') }}">Agregar</a>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th scope="col">Clave</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Creado</th>
                    <th scope="col">Actualizado</th>
                    <th scope="col">Área</th>
                    <th scope="col">Creado por</th>
                    <th scope="col">Actualizado por</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Prefijo</th>
                    <th scope="col">Modificar</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($especialidades as $especialidad)
                    <tr>
                        <td>{{ $especialidad->clave }}</td>
                        <td>{{ $especialidad->nombre }}</td>
                        <td>{{ $especialidad->created_at !=null ? $especialidad->created_at->format('d-m-y') : '' }}</td>
                        <td>{{ $especialidad->updated_at != null ? $especialidad->updated_at->format('d-m-y') : '' }}</td>
                        <td>{{ $especialidad->nameArea }}</td>
                        <td>{{ $especialidad->nameCreated }}</td>
                        <td>{{ $especialidad->nameUpdated }}</td>
                        @if ($especialidad->activo == 'true')
                            <td>Activo</td>
                        @else
                            <td>Inactivo</td>
                        @endif
                        <td>{{ $especialidad->prefijo }}</td>
                        <td>
                            {{-- <div class="col d-flex justify-content-center">
                                --}}
                                <a class="d-flex justify-content-center align-items-center btn btn-warning btn-circle m-1 btn-circle-sm"
                                    title="Editar"
                                    href="{{ route('especialidades.modificar', ['id' => $especialidad->id]) }}">
                                    <i class="fa fa-pencil-square-o fa-2x" aria-hidden="true"></i>
                                </a>
                                {{--
                            </div> --}}

                            {{-- <div class="col">
                                <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="Eliminar"
                                    href="{{ route('especialidades.destroy', ['id' => $especialidad->id]) }}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </a>
                            </div> --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row py-4">
            <div class="col d-flex justify-content-center">
                {{ $especialidades->links() }}
            </div>
        </div>
    </div>


    {{-- @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
    @endif --}}

@endsection
