<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Curso Validado para Impartir| Sivyc Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <form>
            @csrf
                <div class="text-center">
                    <h1>Validacion de Suficiencia Presupuestal</h1>
                </div>
                <br>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="dropno_memo">Numero de Memorandum</label>
                        <input name="no_memo" id="no_memo" type="text" disabled class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="dropfecha_memo">Fecha de Memorandum</label>
                        <input name="fecha_memo" id="fecha_memo" type="date" disabled class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="drouni_cap">Unidad de Capacitación</label>
                        <input name="uni_cap" id="uni_cap" type="text" disabled class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="droparea">Area de Adscripcion</label>
                        <input name="area" id="area" type="text" disabled class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="dropnombre_dir">Nombre del Director de Unidad</label>
                        <input name="nombre_dir" id="nombre_dir" type="text" disabled class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-row" style="">
                    <div class="form-group col-md-1">
                        <a class="btn btn-danger" href="">Rechazar</a>
                    </div>
                    <div class="form-group col-md-1">
                        <a class="btn btn-success" href="">Validar</a>
                    </div>
                </div>
                <div id="divrechazar" class="form-row ">
                    <div class="form-group col-md-6">
                        <label for="inputcomentario_rechazo">Describa el motivo de rechazo</label>
                        <textarea name="comentario_rechazo" id="comentario_rechazo" cols="6" rows="6" class="form-control"></textarea>
                    </div>
                    <div class="form-group col-md-4">
                    </div>
                    <div class="form-group col-md-2">
                        <br><br><br>
                        <button type="submit" class="btn btn-danger" >Confrimar Rechazo</button>
                    </div>
                </div>
                <hr style="border-color:dimgray">
                <div id="div1" class="form-row  d-none d-print-none">
                    <div class="form-group col-md-4">
                        <label for="inputfolio_validacion">Folio de Validación</label>
                        <input name="folio_validacion" id="folio_validacion" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputfecha_validacion">Fecha de Validación</label>
                        <input name="fecha_validacion" id="fecha_validacion" type="date" class="form-control">
                    </div>
                </div>
                <br>
        </form>
    </section>
@stop

