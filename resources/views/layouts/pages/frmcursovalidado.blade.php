<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Curso Validado | Sivyc Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <form action="{{ url('/instructor/guardar') }}" method="post" id="registercv" enctype="multipart/form-data">
            @csrf
            <div class="text-center">
                <h1>Formulario para Validación de Cursos<h1>
            </div>
            <hr style="border-color:dimgray">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputnumero_contrato">Numero de Control de Instructor</label>
                    <input type="text" name="numero_control" id="numero_control" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-3">
                    <br>
                    <button type="button" id="search_cv" class="btn btn-info btn-lg">Buscar</button>
                </div>
            </div>
            <hr style="border-color:dimgray">
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputcurp">Nombre de Instructor</label>
                    <input name='nombreins' id='nombreins' disabled type="text" class="form-control" aria-required="true">
                    <input name='id_ins' id='id_ins' hidden type="text"> <!--guarda id-->
                </div>
            </div>
            <div class="form-row">
                <div class="form-gorup col-md-3">
                    <label for="inputclave_curso">Clave del Curso Validado</label>
                    <input  name="clave_curso" id="clave_curso" type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-2">
                    <label for="inputcurp">Fecha de Inicio</label>
                    <input name='fecha_inicio' id='fecha_inicio' type="date" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-2">
                    <label for="inputcurp">Fecha de Termino</label>
                    <input name='fecha_termino' id='fecha_termino' type="date" class="form-control" aria-required="true">
                </div>
            </div>
            <hr style="border-color:dimgray">
            <label><h2>Eleccion de Curso a Validar</h2></label>
            <table  id="table-instructor" class="table table-bordered">
                <caption>Catalogo de Cursos</caption>
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">especialidad</th>
                        <th scope="col">duración</th>
                        <th scope="col">modalidad</th>
                        <th scope="col">Documentos</th>
                        <th width="160px">Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $itemData)
                        <tr>
                        <th scope="row">{{$itemData->nombre_curso}}</th>
                            <td>{{$itemData->especialidad}}</td>
                            <td>{{$itemData->duracion}}</td>
                            <td>{{$itemData->modalidad}}</td>
                            <td>
                                <a class="btn btn-info btn-circle m-1 btn-circle-sm" title="Validación de clave de curso" download="{{$itemData->pdf_curso}}">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                                <a class="btn btn-danger btn-circle m-1 btn-circle-sm" title="Validación de instructor" download="{{$data->archivo_alta}}">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-info" href="{{route('instructor-ver', ['id' => $itemData->id])}}">Mostrar</a>
                                {!! Form::open(['method' => 'DELETE','route' => ['usuarios'],'style'=>'display:inline']) !!}
                                {!! Form::submit('Borrar', ['class' => 'btn btn-danger']) !!}
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                    </tr>
                </tfoot>
            </table>
            <hr style="border-color:dimgray">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                    </div>
                </div>
            </div>
            <br>
        </form>
    </section>
@stop

