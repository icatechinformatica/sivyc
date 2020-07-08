@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Inscripción Alumno | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container g-pt-50">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div><br />
        @endif
        <div style="text-align: center;">
            <h3><b>UNIDADES MOVILES</b></h3>
        </div>
        <form method="POST" id="form_sid_registro" action="{{ route('registrado_consecutivo.index') }}">
            @csrf
            <div class="form-row">
                <!-- domicilio -->
                <div class="form-group col-md-6">
                    <label for="ubicaciones" class="control-label">UNIDADES</label>
                    <select class="form-control" id="ubicaciones" name="ubicaciones" required>
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($tblUnidades as $itemTblUnidades)
                            <option value="{{$itemTblUnidades->ubicacion}}">{{$itemTblUnidades->ubicacion}}</option>
                        @endforeach
                    </select>
                </div>
                <!-- domicilio END -->
                <div class="form-group col-md-6">
                    <label for="unidades_ubicacion" class="control-label">ACCIÓN MÓVIL</label>
                    <select class="form-control" id="unidades_ubicacion" name="unidades_ubicacion" required>
                        <option value="">--SELECCIONAR--</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-outline-danger my-2 my-sm-0" type="submit">CARGAR</button>
        </form>
        <br><br>
    </div>
@endsection
