@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'AREAS | SIVyC Icatech')

@section('content')

    <div class="container g-pt-30">
        <div class="row">
            <div class="col">
                <h1>ÁREAS</h1>
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
                    <th width="180px">Estado</th>
                    <th width="180px">Opciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($areas as $key => $area)
                    <tr>
                        <td>{{ $area->formacion_profesional }}</td>
                        <td>{{ $area->created_at }}</td>
                        <td>{{ $area->updated_at }}</td>
                        <td>{{ $created_names[$key]['name'] }}</td>
                        <td>{{ $updated_names[$key]['name'] }}</td>
                        @if ($area->activo == 'true')
                            <td>Activo</td>
                        @else
                            <td>Inactivo</td>
                        @endif

                        <td>
                            <div class="row">
                                <div class="col">
                                    <a class="btn btn-warning btn-circle m-1 btn-circle-sm" title="Editar"
                                        href="{{ route('areas.modificar', ['id' => $area->id]) }}">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    </a>
                                </div>

                                <div class="col">
                                    <form action="{{ route('areas.destroy', $area) }}" method="POST">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-circle m-1 btn-circle-sm" title="Eliminar"
                                            type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- @if (session('success'))
        <script>
            alert("{{session('success')}}");
        </script>
    @endif --}}
@endsection
