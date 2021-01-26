@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Actualizar especialidad | Sivyc Icatech')

@section('content')

    <div class="container g-pt-50 g-pb-20">
        <form action="{{route('especialidades.update', $especialidad->id)}}" method="post">
            @csrf

            <h1>ACTUALIZAR ESPECIALIDAD</h1>

            <div class="row mt-5 mb-4">
                <div class="col">
                    <div class="form-group">
                        <label for="clave" class="control-label">Clave de la especialidad</label>
                        <input type="text" class="form-control" id="clave" name="clave" placeholder="Clave de la especialidad"
                            value="{{$especialidad->clave}}" required>
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="nombre" class="control-label">Nombre de la especialidad</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre de la especialidad"
                            value="{{$especialidad->nombre}}" required>
                    </div>
                </div>
            </div>

            <div class="row my-4">
                <div class="col">
                    <div class="form-group">
                        <label for="area" class="control-label">Area de la especialidad</label>
                        <select name="area" id="area" class="custom-select">
                            @foreach ($areas as $area)
                                <option {{$area->id == $especialidad->id_areas ? 'selected' : ''}}  value="{{$area->id}}">{{$area->formacion_profesional}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="status" class="control-label">Estado de la especialidad</label>

                        @if ($especialidad->activo == 'true')
                            <select id="status" name="status" class="custom-select">
                                <option selected value="true">Activo</option>
                                <option value="false">Inactivo</option>
                            </select>
                        @else
                            <select id="status" name="status" class="custom-select">
                                <option value="true">Activo</option>
                                <option selected value="false">Inactivo</option>
                            </select>
                        @endif


                    </div>
                </div>
            </div>

            <div class="row my-4">
                <div class="col">
                    <div class="form-group">
                        <label for="prefijo" class="control-label">Prefijo de la especialidad</label>
                        <input type="text" class="form-control" id="prefijo" name="prefijo" placeholder="Prefijo de la especialidad"
                            value="{{$especialidad->prefijo}}" required>
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary">Actualiar especialidad</button>
                    </div>
                </div>
            </div>
        </form>
    </div>


@endsection