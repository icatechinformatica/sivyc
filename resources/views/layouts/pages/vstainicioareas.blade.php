@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'AREAS | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
@endsection
@section('content')       
    <div class="card-header">
        Catálogos / Áreas
    </div>
    <div class="card card-body">       
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
                <button type="submit" class="btn ">BUSCAR</button>
                {!! Form::close() !!}
            </div>

            <div class="col">
                <div class="pull-right">
                    <a class="btn btn-lg" href="{{ route('areas.agregar') }}">Agregar</a>
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
                        <td>
                            @if ($area->activo == 'true')
                                Activo
                            @else
                                Inactivo
                            @endif
                        </td>
                        <td>
                        <a class="nav-link" href="{{ route('areas.modificar', ['id' => $area->id]) }}">
                            <i class="fa fa-edit  fa-2x fa-lg text-success" title="Editar" ></i>
                        </a>
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
