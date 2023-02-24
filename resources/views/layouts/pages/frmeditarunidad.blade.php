<!--Creado por Orlando Chavez com-->
@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'SUPRE | SIVyC Icatech')
<!--seccion-->
@section('content')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <div class="card-header">Editar Unidad {{$data->unidad}}</div>
    <form method="POST" action="{{ route('unidades-actualizar') }}" id="unidadupdate">
        @csrf
        <div class="card card-body">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="unidad" class="control-label">Nombre de Unidad</label>
                    <input type="text" id="unidad" name="unidad" class="form-control" value="{{$data->unidad}}" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="cct" class="control-label">CCT</label>
                    <input type="text" id="cct" name="cct" class="form-control" value="{{$data->cct}}" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="plantel" class="control-label">Plantel</label>
                    <input type="text" id="plantel" name="plantel" class="form-control" value="{{$data->plantel}}" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="telefono" class="control-label">Telefono</label>
                    <input type="number" id="telefono" name="telefono" class="form-control" value="{{$data->telefono}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="direccion" class="control-label">Dirección</label>
                    <input type="text" id="direccion" name="direccion" class="form-control" value="{{$data->direccion}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="ubicacion" class="control-label">Ubicación</label>
                    <input type="text" id="ubicacion" name="ubicacion" class="form-control" value="{{$data->ubicacion}}" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="coordenadas" class="control-label">Coordenadas</label>
                    <input type="text" id="coordenadas" name="coordenadas" class="form-control" value="{{$data->coordenadas}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="codigo_postal" class="control-label">Codigo Postal</label>
                    <input type="text" id="codigo_postal" name="codigo_postal" class="form-control" value="{{$data->codigo_postal}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="correo" class="control-label">Correo E-mail</label>
                    <input type="text" id="correo" name="correo" class="form-control" value="{{$data->correo}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="dunidad" class="control-label">Director de Unidad</label>
                    <input type="text" id="dunidad" name="dunidad" class="form-control" value="{{$data->dunidad}}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="pdunidad" class="control-label">Puesto de Director de Unidad</label>
                    <input type="text" id="pdunidad" name="pdunidad" class="form-control" value="{{$data->pdunidad}}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="dgeneral" class="control-label">Director General</label>
                    <input type="text" id="dgeneral" name="dgeneral" class="form-control" value="{{$data->dgeneral}}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="pdgeneral" class="control-label">Puesto de Director General</label>
                    <input type="text" id="pdgeneral" name="pdgeneral" class="form-control" value="{{$data->pdgeneral}}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="academico" class="control-label">Academico</label>
                    <input type="text" id="academico" name="academico" class="form-control" value="{{$data->academico}}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="pacademico" class="control-label">Puesto de Academico</label>
                    <input type="text" id="pacademico" name="pacademico" class="form-control" value="{{$data->pacademico}}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="vinculacion" class="control-label">Vinculación</label>
                    <input type="text" id="vinculacion" name="vinculacion" class="form-control" value="{{$data->vinculacion}}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="pvinculacion" class="control-label">Puesto de Vinculación</label>
                    <input type="text" id="pvinculacion" name="pvinculacion" class="form-control" value="{{$data->pvinculacion}}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="dacademico" class="control-label">Director Academico</label>
                    <input type="text" id="dacademico" name="dacademico" class="form-control" value="{{$data->dacademico}}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="pdacademico" class="control-label">Puesto de Director Academico</label>
                    <input type="text" id="pdacademico" name="pdacademico" class="form-control" value="{{$data->pdacademico}}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="jcyc" class="control-label">Certificación y Control</label>
                    <input type="text" id="jcyc" name="jcyc" class="form-control" value="{{$data->jcyc}}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="pjcyc" class="control-label">Puesto de Certificación y Control</label>
                    <input type="text" id="pjcyc" name="pjcyc" class="form-control" value="{{$data->pjcyc}}" required>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                        <input type="text" name="idunidad" id="idunidad" hidden value="{{$data->id}}">
                    </div>
                </div>
            </div>
        </div>
    </form>>
@endsection
