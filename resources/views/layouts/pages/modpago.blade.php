<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Modificacion de Pago | SIVyC Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
            <div style="text-align: right;width:65%">
                <label><h1>Modificacion de Pago</h1></label>
            </div>
            <hr style="border-color:dimgray">
            <div style="text-align: right;width:100%">
                <button type="button" id="mod_" class="btn btn-warning btn-lg">Modificar Campos</button>
            </div>
        <form action="{{ url('/pago/guardar') }}" method="post" id="registerpago" enctype="multipart/form-data">
            @csrf
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
                <div class="form-group col-md-4">
                    <label for="inputnumero_contrato">Numero de Contrato</label>
                    <input type="text" name="numero_contrato" id="numero_contrato" disabled class="form-control" aria-required="true">
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
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-3">
                    <label for="inputnumero_pago">Numero de Pago</label>
                    <input type="text" name="numero_pago" id="numero_pago" disabled class="form-control" aria-required="true">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputfecha_pago">Fecha de Pago</label>
                    <input type="date" name="fecha_pago" id="fecha_pago" disabled class="form-control" aria-required="true">
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-6">
                    <label for="inputconcepto">Descripcion de concepto</label>
                    <textarea cols="6" rows="5" name="concepto" id="concepto" disabled class="form-control" aria-required="true"></textarea>
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-4">
                    <label for="inputnombre_solicita">Nombre de Solicitante</label>
                    <input type="text" name="nombre_solicita" id="nombre_solicita" disabled class="form-control" aria-required="true">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputnombre_autoriza">Nombre de Autorizante</label>
                    <input type="text" name="nombre_autoriza" id="nombre_autoriza" disabled class="form-control" aria-required="true">
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-6">
                    <label for="inputreacd02">Documento REACD-02</label>
                    <input type="file" accept="application/pdf" name="reacd02" id="reacd02" disabled class="form-control" aria-required="true">
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
            <br>
            <div  style="text-align: right;width:100%">
                <button type="submit" class="btn btn-primary" >Guardar Cambios</button>
            </div>
        </form>
    </section>
    <br>
@stop
