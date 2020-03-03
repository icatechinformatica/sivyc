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

