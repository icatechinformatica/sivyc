<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Curso Validado para Impartir| Sivyc Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <form>
            @csrf
                <div class="text-center">
                    <h1>Añadir Curso Validado para Impartir</h1>
                </div>
                <br>
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label for="inputcurso_validado">Validado Unicamente para Impartir</label>
                        <select class="form-control" id="capacitado_icatech">
                            <option>Opcion 1</option>
                            <option>Opcion 2</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <br>
                        <button type="button" class="btn btn-success btn-lg" >Agregar</button>
                    </div>
                </div>
                <br>
                <div class="form-row" style="text-align: right;width:0%"">
                    <div class="form-group col-md-1">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                </div>
                <br>
        </form>
    </section>
@stop

