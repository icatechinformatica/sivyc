<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Edición de Instructor | Sivyc Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <form action="{{ url('/instructor/guardar-mod') }}" method="post" id="registerinstructor" enctype="multipart/form-data">
            @csrf
            <div class="text-center">
                <h1>Editar Instructor<h1>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputobservacion" class="control-label"><b>Observaciones de Rechazo</b></label>
                    <textarea cols="4" rows="4" type="text" class="form-control" readonly aria-required="true" id="observacion" name="observacion">{{$datains->rechazo}}</textarea>
                </div>
            </div>
            <hr style="border-color:dimgray">
            <h2>Vista de Documentos</h2>
            <div class="form-row">
                <a class="btn btn-info" href={{$datains->archivo_ine}} target="_blank">Solicitud de Pago</a><br>
                <a class="btn btn-info" href={{$datains->archivo_domicilio}} target="_blank">Comprobante de Domicilio</a><br>
                <a class="btn btn-info" href={{$datains->archivo_curp}} target="_blank">CURP</a><br>
                <a class="btn btn-info" href={{$datains->archivo_alta}} target="_blank">Alta de Instructor</a><br>
            </div>
            <div class="form-row">
                <a class="btn btn-info" href={{$datains->archivo_bancario}} target="_blank">Datos Bancarios</a><br>
                <a class="btn btn-info" href={{$datains->archivo_rfc}} target="_blank">RFC/Constancia Fiscal</a><br>
                <a class="btn btn-info" href={{$datains->archivo_fotografia}} target="_blank">Fotografía</a><br>
                <a class="btn btn-info" href={{$datains->archivo_estudios}} target="_blank">Estudios</a><br>
            </div>
            <div class="form-row">
                <a class="btn btn-info" href={{$datains->archivo_otraid}} target="_blank">Otra Identificación</a><br>
            </div>
            <hr style="border-color:dimgray">
            <div>
                <label><h2>Cambiar Documentos</h2></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputarch_ine">Archivo INE</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_ine" name="arch_ine" placeholder="Archivo PDF">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputarch_domicilio">Archivo Comprobante de Domicilio</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_domicilio" name="arch_domicilio" placeholder="Archivo PDF">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputarch_curp">Archivo CURP</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_curp" name="arch_curp" placeholder="Archivo PDF">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputarch_alta">Archivo Alta de Instructor</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_alta" name="arch_alta" placeholder="Archivo PDF">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputarch_banco">Archivo Datos Bancarios</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_banco" name="arch_banco" placeholder="Archivo PDF">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputarch_rfc">RFC/Constancia Fiscal</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_rfc" name="arch_rfc" placeholder="Archivo PDF">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputarch_foto">Archivo Fotografia</label>
                    <input type="file" accept="image/jpeg" class="form-control" id="arch_foto" name="arch_foto" placeholder="Archivo PDF">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputarch_estudio">Archivo Grado de Estudios</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_estudio" name="arch_estudio" placeholder="Archivo PDF">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputarch_id">Archivo Otra Identificación</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_id" name="arch_id" placeholder="Archivo PDF">
                </div>
            </div>
            <hr style="border-color:dimgray">
            <div>
                <label><h2>Datos Personales</h2></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputnombre">Nombre</label>
                    <input name='nombre' id='nombre' type="text" class="form-control" aria-required="true" value="{{$datains->nombre}}">
                    <input name="id" id="id" type="text" hidden value="{{$datains->id}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_paterno">Apellido Paterno</label>
                    <input name='apellido_paterno' id='apellido_paterno' type="text" class="form-control" aria-required="true" value="{{$datains->apellidoPaterno}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_materno">Apellido Materno</label>
                    <input name='apellido_materno' id='apellido_materno' type="text" class="form-control" aria-required="true" value="{{$datains->apellidoMaterno}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputbanco">Nombre del Banco</label>
                    <input name="banco" id="banco" type="text" class="form-control" aria-required="true" value="{{$datains->banco}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputclabe">Clabe Interbancaria</label>
                    <input name="clabe" id="clabe" type="text" class="form-control" aria-required="true" value="{{$datains->interbancaria}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputnumero_cuenta">Numero de Cuenta</label>
                    <input name="numero_cuenta" id="numero_cuenta" type="text" class="form-control" aria-required="true" value="{{$datains->no_cuenta}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputbanco">Dirección de Domicilio</label>
                    <input name="domicilio" id="domicilio" type="text" class="form-control" aria-required="true" value="{{$datains->domicilio}}">
                </div>
            </div>
            <br>
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

