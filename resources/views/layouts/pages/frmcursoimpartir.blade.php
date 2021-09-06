<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Especialidad Validada para Impartir| Sivyc Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
                <div class="text-center">
                    <h1>Añadir Especialidad Validada para Impartir</h1>
                </div>
                <br>
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
                                    <a class="btn btn-success" href="{{route('cursoimpartir-form',['id' => $itemData->id, 'idins' => $idins])}}">Agregar</a>
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
    </section>
@stop

