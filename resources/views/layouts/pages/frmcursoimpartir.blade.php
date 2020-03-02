<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Curso Validado para Impartir| Sivyc Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
                <div class="text-center">
                    <h1>Añadir Curso Validado para Impartir</h1>
                </div>
                <br>
                <table  id="table-instructor" class="table table-bordered">
                    <caption>Catalogo de Instructrores</caption>
                    <thead>
                        <tr>
                            <th scope="col">ID de Curso</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Clasificacion</th>
                            <th scope="col">Especialidad</th>
                            <th width="85px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data_curso as $itemData)
                            <tr>
                            <th scope="row">{{$itemData->id}}</th>
                                <td>{{$itemData->nombre_curso}}</td>
                                <td>{{$itemData->clasificacion}}</td>
                                <td>{{$itemData->especialidad}}</td>
                                <td>
                                    {{ Form::open(['route' => ['cursoimpartir-guardar', $itemData->id, $idInstructor],'style'=>'display:inline']) }}
                                    {{ Form::submit('Agregar', ['class' => 'btn btn-success']) }}
                                    {{ Form::close() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                        </tr>
                    </tfoot>
                </table>
                <br>
                <div class="form-row" style="text-align: right;width:0%"">
                    <div class="form-group col-md-1">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                </div>
                <br>
                <input type="hidden" name="idInstructor" id="idInstructor" value="{{ $idInstructor }}">
    </section>
@stop

