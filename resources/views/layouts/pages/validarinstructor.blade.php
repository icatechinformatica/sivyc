<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Validación de Instructor | Sivyc Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <form method="POST" action="{{ route('instructor-rechazo') }}" id="rechazoinstructor">
            @csrf
            <div class="text-center">
                <h1>Validación de Instructor<h1>
            </div>
            <div>
                <label><h2>Datos Personales</h2></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputnombre">Nombre</label>
                    <input type="text" class="form-control" disabled value="{{$getinstructor->nombre}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_paterno">Apellido Paterno</label>
                    <input type="text" class="form-control" disabled value="{{$getinstructor->apellidoPaterno}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_materno">Apellido Materno</label>
                    <input type="text" class="form-control" disabled value="{{$getinstructor->apellidoMaterno}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputcurp">CURP</label>
                    <input type="text" class="form-control" disabled value="{{$getinstructor->curp}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputcontrol">Numero de Control</label>
                    <input type="text" class="form-control" disabled value="{{$getinstructor->numero_control}}">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <button type="button" id="instructor_rechazar" name="instructor_rechazar" class="btn btn-danger">Rechazar</a>
                    </div>
                    <div class="pull-right">
                        <button type="button" id="instructor_validar" name="instructor_validar" class="btn btn-success">Validar</a>
                    </div>
                </div>
            </div>
            <div id="divrechazarins" class="form-row d-none d-print-none">
                <div class="form-group col-md-6">
                    <label for="inputcomentario_rechazo">Describa el motivo de rechazo</label>
                    <textarea name="comentario_rechazo" id="comentario_rechazo" cols="6" rows="6" class="form-control"></textarea>
                </div>
            </div>
            <div id="divconf_rechazarins" class="form-row d-none d-print-none">
                <div class="form-group col-md-3">
                    <button type="submit" class="btn btn-danger" >Confirmar Rechazo</button>
                    <input hidden id="id" name="id" value="{{$getinstructor->id}}">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-warning" href="{{URL::previous()}}">Regresar</a>
                    </div>
                </div>
            </div>
        </form>
        <hr style="border-color:dimgray">
        <br>
        <form method="POST" action="{{ route('instructor-validado') }}" id="validadoinstructor" enctype="multipart/form-data">
            @csrf
            <div id="div1" class="form-row d-none d-print-none">
                <div class="form-group col-md-4">
                    <label for="inputbanco">Nombre del Banco</label>
                    <input name="banco" id="banco" type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputclabe">Clabe Interbancaria</label>
                    <input name="clabe" id="clabe" type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputnumero_cuenta">Numero de Cuenta</label>
                    <input name="numero_cuenta" id="numero_cuenta" type="text" class="form-control" aria-required="true">
                </div>
            </div>
            <div id="div2" class="form-row d-none d-print-none">
                <div class="form-group col-md-6">
                    <label for="inputbanco">Dirección de Domicilio</label>
                    <input name="domicilio" id="domicilio" type="text" class="form-control" aria-required="true">
                </div>
            </div>
            <div id="div3" class="form-row d-none d-print-none">
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
            <div id="div4" class="form-row d-none d-print-none">
                <div class="form-group col-md-3">
                    <label for="inputarch_banco">Archivo Datos Bancarios</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_banco" name="arch_banco" placeholder="Archivo PDF">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputarch_foto">Archivo Fotografia</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_foto" name="arch_foto" placeholder="Archivo PDF">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputarch_estudio">Archivo Grado de Estudios</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_estudio" name="arch_estudio" placeholder="Archivo PDF">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputarch_id">Archivo Otra Identificación</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_id" name="arch_id" placeholder="Archivo PDF">
                </div>
            </div>
            <div id="confvali" class="row d-none d-print-none">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-success" >Confirmar Validación</button>
                        <input hidden id="id" name="id" value="{{$getinstructor->id}}">
                    </div>
                </div>
            </div>
            <br>
        </form>
    </section>
@stop
