@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Editar Especialidad | Sivyc Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />   
    <style>
        .table tr th { text-align: center; padding:12px;}       
    </style>
@endsection
@section('content')
    <div class="card-header">
        Catálogos / Especialidades / Editar Especialidad
    </div>
    <div class="card card-body">
        @if($message ?? '')
            <div class="row ">
                <div class="col-md-12 alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif
        <form action="{{route('especialidades.update', $especialidad->id)}}" method="post">
            @csrf
            <div class="row mt-5 mb-4">
                <div class="col">
                    <div class="form-group">
                        <label for="clave" class="control-label">Clave de la Especialidad</label>
                        <input type="text" class="form-control" id="clave" name="clave" placeholder="Clave de la especialidad"
                            value="{{$especialidad->clave}}" required>
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="nombre" class="control-label">Nombre de la Especialidad</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre de la especialidad"
                            value="{{$especialidad->nombre}}" required>
                    </div>
                </div>
            </div>

            <div class="row my-4">
                <div class="col">
                    <div class="form-group">
                        <label for="area" class="control-label">Campo de Formación Profesional</label>
                        <select name="area" id="area" class="custom-select">
                            @foreach ($areas as $area)
                                <option {{$area->id == $especialidad->id_areas ? 'selected' : ''}}  value="{{$area->id}}">{{$area->formacion_profesional}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="status" class="control-label">Estado de la Especialidad</label>

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
                        <label for="prefijo" class="control-label">Prefijo de la Especialidad</label>
                        <input type="text" class="form-control" id="prefijo" name="prefijo" placeholder="Prefijo de la especialidad"
                            value="{{$especialidad->prefijo}}" required>
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col d-flex justify-content-end">                    
                    <button type="submit" class="btn">Actualizar Especialidad</button>                    
                </div>
            </div>
        </form>
    </div>
@endsection
