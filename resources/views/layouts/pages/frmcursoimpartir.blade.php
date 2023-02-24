<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Especialidad Validada para Impartir| Sivyc Icatech')
@section('content')
<link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
<div class="card-header">
    <h1>Añadir Especialidad Validada para Impartir</h1>
</div>
<div class="card card-body">
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif
    <table  id="table-instructor" class="table table-bordered">
        <caption>Catalogo de Especialidades</caption>
        <thead>
            <tr>
                <th scope="col">Clave</th>
                <th scope="col">Nombre</th>
                <th scope="col">Campo de Formacion</th>
                <th width="85px">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data_especialidad as $itemData)
                <tr>
                <th scope="row">{{$itemData->clave}}</th>
                    <td>{{$itemData->nombre}}</td>
                    <td>{{$itemData->campo_formacion}}</td>
                    <td>
                        <a class="btn mr-sm-4 mt-3" style="color: white;" href="{{route('cursoimpartir-form',['id' => $itemData->id, 'idins' => $idins])}}">AÑADIR</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="8">
                {{ $data_especialidad->appends(request()->query())->links() }}
            </td>
        </tr>
    </tfoot>
    </table>
    <br>
    <div class="form-row" style="text-align: right;width:0%">
        <div class="form-group col-md-1">
            <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
        </div>
    </div>
    <br>
    <input type="hidden" name="idInstructor" id="idInstructor" value="{{ $idins }}">
</div>
@stop

