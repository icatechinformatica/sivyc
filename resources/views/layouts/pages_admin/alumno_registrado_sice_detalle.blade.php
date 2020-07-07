<!--Creado por Daniel Méndez Cruz-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Alumnos | SIVyC Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>MODIFICACIÓN DE NÚMERO DE CONTROL DE ALUMNO REGISTRADO DEL SICE</h2>
                </div>
            </div>
        </div>
        <form action="{{ route('registro_alumnos_sice.modificar.update', ['id' => $alumnoRegistrado->id ])}}" id="frmalumno_registrado_modificar" method="post">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="curp_edit" class="control-label">CURP</label>
                    <input type="text" name="curp_edit" id="curp_edit" class="form-control" value="{{ $alumnoRegistrado->curp }}" readonly>
                </div>
                <div class="form-group col-md-6">
                    <label for="numero_control_edit" class="control-label">NÚMERO DE CONTROL PARA MODIFICAR</label>
                    <input type="text" name="numero_control_edit" id="numero_control_edit" class="form-control" placeholder="NÚMERO DE CONTROL PARA MODIFICAR">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-success" >Modificar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <br>
@endsection
