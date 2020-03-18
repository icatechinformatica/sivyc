<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Pago | SIVyC Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <div style="text-align: right;width:60%">
            <label><h1>Registro de Pago</h1></label>
        </div>
        <br>
        <br>
        <form action="{{ url('/pago/guardar') }}" method="post" id="registerpago" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <h2> Confirmación de Datos </h2>
            </div>
            <div class="form-row">
                <div class="form-gorup col-md-3">
                    <label for="inputnumero_control">Numero de Control de Instructor</label>
                    <input type="text" name="numero_control" id="numero_control" disabled class="form-control" aria-required="true">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputnombre_instructor">Nombre de Instructor</label>
                    <input type="text" name="nombre_instructor" id="nombre_instructor" disabled class="form-control" aria-required="true">
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-4">
                    <label for="inputnombre_curso">Nombre de Curso</label>
                    <input type="text" name="nombre_curso" id="nombre_curso" disabled class="form-control" aria-required="true">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputunidad_cap">Unidad de Capacitacion</label>
                    <input type="text" name="unidad_cap" id="unidad_cap" disabled class="form-control" aria-required="true">
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-2">
                    <label for="inputclave_grupo">Clave de Grupo</label>
                    <input type="text" name="clave_grupo" id="clave_grupo" disabled class="form-control" aria-required="true">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputtipo_pago">Tipo de Pago</label>
                    <input type="text" name="tipo_pago" id="tipo_pago" disabled class="form-control" aria-required="true">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputmonto_pago">Monto de Pago</label>
                    <input type="text" name="monto_pago" id="monto_pago" disabled class="form-control" aria-required="true">
                </div>
                <div class="form-gorup col-md-2">
                    <label for="inputiva">IVA</label>
                    <input type="text" name="iva" id="iva" disabled class="form-control" aria-required="true">
                </div>
            </div>
            <hr style="border-color:dimgray">
            <div class="form-row">
                <!-- PONER TITULO-->
            </div>
            <div class="form-row">
                <div class="form-gorup col-md-3">
                    <label for="inputnumero_pago">Numero de Pago</label>
                    <input type="text" name="numero_pago" id="numero_pago" class="form-control" aria-required="true">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputfecha_pago">Fecha de Pago</label>
                    <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" aria-required="true">
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-6">
                    <label for="inputconcepto">Descripcion de concepto</label>
                    <textarea cols="6" rows="5" name="concepto" id="concepto" class="form-control" aria-required="true"></textarea>
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-4">
                    <label for="inputnombre_solicita">Nombre de Solicitante</label>
                    <input type="text" name="nombre_solicita" id="nombre_solicita" class="form-control" aria-required="true">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputnombre_autoriza">Nombre de Autorizante</label>
                    <input type="text" name="nombre_autoriza" id="nombre_autoriza" class="form-control" aria-required="true">
                </div>
            </div>
            <br>
            <div  style="text-align: right;width:100%">
                <button type="submit" class="btn btn-primary" >Agregar</button>
            </div>
        </form>
    </section>
    <br>
@stop
