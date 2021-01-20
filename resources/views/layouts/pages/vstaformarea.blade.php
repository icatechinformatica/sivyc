@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Agregar área | Sivyc Icatech')

@section('content')

    <div class="container g-pt-50 g-pb-20">

        <form action="{{ route('areas.guardar') }}" method="post">
            @csrf

            <h1>Agregar Área</h1>

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
                        <button type="submit" class="btn btn-primary">Guardar área</button>
                    </div>
                </div>
            </div>
        </form>

    </div>

@endsection
