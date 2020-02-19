<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Pago | SIVyC Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <form action="{{ url('/pago/guardar') }}" method="post" id="registerpago" enctype="multipart/form-data">
            @csrf
            <div style="text-align: right;width:60%">
                <label><h1>Registro de Pago</h1></label>
            </div>
            <br>
            <br>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputclave_grupo">Clave de Grupo</label>
                    <input type="text" name="clave_grupo" id="clave_grupo" class="form-control" aria-required="true">

                </div>
                <div class="form-group col-md-3">
                    <br>
                    <button type="submit" class="btn btn-info btn-lg">Buscar</button>
                </div>
            </div>
            <hr style="border-color:dimgray">
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
                    <input type="text" name="nombre-curso" id="nombre-curso" disabled class="form-control" aria-required="true">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputunidad_cap">Unidad de Capacitacion</label>
                    <input type="text" name="unidad_cap" id="unidad_cap" disabled class="form-control" aria-required="true">
                </div>
            </div>
            <br>
            <div class="form-row">
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
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-4">
                    <label for="inputfecha_pago">Fecha de Pago</label>
                    <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" aria-required="true">
                </div>
                <div class="form-gorup col-md-6">
                    <label for="inputconcepto">Descripcion de concepto</label>
                    <textarea cols="6" rows="4" name="concepto" id="concepto" class="form-control" aria-required="true"></textarea>
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-4">
                    <label for="inputnombre_solicita">Nombre de Solicitante</label>
                    <input type="text" name="nombre_solicita" id="nombre_solicita" class="form-control" aria-required="true">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputnombre_autoriza">Unidad de Autorizante</label>
                    <input type="text" name="nombre_autoriza" id="nombre_autoriza" class="form-control" aria-required="true">
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-6">
                    <label for="inputreacd02">Documento REACD-02</label>
                    <input type="file" accept="application/pdf" name="reacd02" id="reacd02" class="form-control" aria-required="true">
                </div>
                <div class="form-gorup col-md-2">
                    <label for="inputestatus_pago">Estatus de Pago</label>
                    <select class="form-control" id="estatus_pago">
                        <option>Pendiente</option>
                        <option>Pagado</option>
                        <option>Cancelado</option>
                    </select>
                </div>
            </div>
        </form>
    </section>
    <br>
@stop
