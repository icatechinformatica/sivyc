@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Agregar área | Sivyc Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
@endsection
@section('content')       
    <div class="card-header">
        Catálogos / Agregar Área
    </div>
    <div class="card card-body">
        <form action="{{ route('areas.guardar') }}" method="post">
            @csrf
            <div class="row mt-5">
                <div class="col">
                    <div class="form-group">
                        <label for="nombre" class="control-label">Nombre del area</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del area"
                            required>
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="status" class="control-label">Estado del área</label>
                        <select id="status" name="status" class="custom-select">
                            {{-- <option selected>Estado del área</option>
                            --}}
                            <option selected value="true">Activo</option>
                            <option value="false">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="pull-right">
                        <button type="submit" class="btn">Guardar área</button>
                    </div>
                </div>
            </div>
        </form>

    </div>

@endsection
