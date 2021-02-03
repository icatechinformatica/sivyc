@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'AREAS | SIVyC Icatech')

@section('content')

    <div class="container g-pt-30">
        <div class="row">
            <div class="col">
                <h1>ÁREAS</h1>
            </div>
        </div>

        <div class="row">
            <div class="col">
                {!! Form::open(['route' => 'areas.inicio', 'method' => 'GET', 'class' => 'form-inline']) !!}
                <select name="busqueda" class="form-control mr-sm-2" id="busqueda">
                    <option value="">BUSCAR POR TIPO</option>
                    <option value="formacion_profesional">FORMACIÓN PROFESIONAL</option>
                    {{-- <option value="nombre">NOMBRE</option>
                    --}}
                    {{-- <option value="prefijo">PREFIJO</option>
                    --}}
                </select>

                {!! Form::text('busqueda_aspirantepor', null, ['class' => 'form-control mr-sm-2', 'placeholder' => 'BUSCAR',
                'aria-label' => 'BUSCAR']) !!}
                <button type="submit" class="btn btn-outline-primary">BUSCAR</button>
                {!! Form::close() !!}
            </div>

            <div class="col">
                <div class="pull-right">
                    <a class="btn btn-success btn-lg" href="{{ route('areas.agregar') }}">Agregar</a>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th scope="col">Formación profesional</th>
                    <th scope="col">Creado</th>
                    <th scope="col">Actualizado</th>
                    <th scope="col">Creado por</th>
                    <th scope="col">Actualizado por</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Modificar</th>
                </tr>
            </thead>

            <tbody>

                {{-- {{ $areas }} --}}
                @foreach ($areas as $area)
                    <tr>
                        <td>{{ $area->formacion_profesional }}</td>
                        <td>{{ $area->created_at != null ? $area->created_at->format('d-m-y') : '' }}</td>
                        <td>{{ $area->updated_at != null ? $area->updated_at->format('d-m-y') : '' }}</td>
                        <td>{{ $area->nameCreated }}</td>
                        <td>{{ $area->nameUpdated }}</td>
                        @if ($area->activo == 'true')
                            <td>Activo</td>
                        @else
                            <td>Inactivo</td>
                        @endif

                        <td>
                            <a class="col d-flex justify-content-center align-items-center btn btn-warning btn-circle m-1 btn-circle-sm"
                                title="Editar" href="{{ route('areas.modificar', ['id' => $area->id]) }}">
                                <i class="fa fa-pencil-square-o fa-2x" aria-hidden="true"></i>
                            </a>
                            {{-- <div class="col"> --}}

                                {{-- <div class="col">
                                    <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="Eliminar"
                                        href="{{ route('areas.destroy', ['id' => $area->id]) }}">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </a>
                                </div> --}}
                                {{-- </div> --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>


        <div class="row py-4">
            <div class="col d-flex justify-content-center">
                {{ $areas->links() }}
            </div>
        </div>

    </div>

    {{-- @if (session('success'))
        <script>
            alert("{{ session('success') }}");

        </script>
    @endif --}}
@endsection
