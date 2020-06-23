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
                    <h2>FORMULARIO DE PERMISOS PARA USUARIOS</h2>
                </div>
            </div>
        </div>
        <form action="" id="frmalumno_registrado_modificar" method="post">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="numero_control_edit" class="control-label">NÚMERO DE CONTROL PARA MODIFICAR</label>
                    <input type="text" name="numero_control_edit" id="numero_control_edit" class="form-control" value="{{ $usuario->name }}" readonly placeholder="NÚMERO DE CONTROL PARA MODIFICAR">
                </div>
                <div class="form-group col-md-8">
                    <label for="codigo_verificacion_edit" class="control-label">CÓDIGO DE VERIFICACIÓN</label>
                    <select class="form-control" id="areaCursos" name="areaCursos">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($roles as $itemRol)
                            @foreach ($usuario->roles as $itemUserRol)
                                <option {{ ($itemUserRol->pivot->role_id == $itemRol->id) ? 'selected' : '' }} value="{{ $itemRol->id }}">{{ $itemRol->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-success" >Asignar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <br>
@endsection
