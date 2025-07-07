@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Actualizar | Sivyc Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
@endsection
@section('content')       
    <div class="card-header">
        Catálogos / Actualizar Área
    </div>
    <div class="card card-body">  
        <form action="{{ route('areas.update_save') }}" method="post">
            @csrf
            <div class="row mt-5">
                <div class="col">
                    <div class="form-group">
                        <label for="nombre" class="control-label">Nombre del area</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del area"
                            value="{{ $area->formacion_profesional }}" required>
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="status" class="control-label">Estado del área</label>

                        @if ($area->activo == 'true')
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

            <div class="row my-2">
                <div class="col">
                    <div class="pull-right">
                        <button type="submit" class="btn">Actualizar área</button>
                        <input type="text" name="idarea" id="idarea" hidden value="{{ $area->id }}">
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection
