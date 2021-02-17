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
                    <input name='nombre' id='nombre' value="{{ $getinstructor->nombre }}" type="text" disabled class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_paterno">Apellido Paterno</label>
                    <input name='apellido_paterno' id='apellido_paterno' value="{{ $getinstructor->apellidoPaterno }}" type="text" class="form-control" aria-required="true" disabled>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_materno">Apellido Materno</label>
                    <input name='apellido_materno' id='apellido_materno' value="{{ $getinstructor->apellidoMaterno}}" type="text" class="form-control" aria-required="true" disabled>
                </div>
            </div>
            <h2>Vista de Documentos</h2>
            <div class="form-row">
                <a class="btn btn-info" href={{$getinstructor->archivo_ine}} target="_blank" download>Solicitud de Pago</a><br>
                <a class="btn btn-info" href={{$getinstructor->archivo_domicilio}} target="_blank" download>Comprobante de Domicilio</a><br>
                <a class="btn btn-info" href={{$getinstructor->archivo_curp}} target="_blank" download>CURP</a><br>
                <a class="btn btn-info" href={{$getinstructor->archivo_alta}} target="_blank" download>Alta de Instructor</a><br>
            </div>
            <div class="form-row">
                <a class="btn btn-info" href={{$getinstructor->archivo_bancario}} target="_blank" download>Datos Bancarios</a><br>
                <a class="btn btn-info" href={{$getinstructor->archivo_rfc}} target="_blank" download>RFC/Constancia Fiscal</a><br>
                <a class="btn btn-info" href={{$getinstructor->archivo_fotografia}} target="_blank" download>Fotografía</a><br>
                <a class="btn btn-info" href={{$getinstructor->archivo_estudios}} target="_blank" download>Estudios</a><br>
            </div>
            <div class="form-row">
                <a class="btn btn-info" href={{$getinstructor->archivo_otraid}} target="_blank" download>Otra Identificación</a><br>
            </div>
            <br>
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
        <form method="POST" action="{{ route('instructor-validado') }}" id="validadoinstructor">
            @csrf
            <div id="div1" class="form-row d-none d-print-none">
                <div class="form-group col-md-4">
                    <label for="inputrfc">RFC</label>
                    <input name='rfc' id='rfc' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputfolio_ine">Folio de Elector</label>
                    <input name='folio_ine' id='folio_ine' type="text" class="form-control" aria-required="true">
                </div>
            </div>
            <div id="div2" class="form-row d-none d-print-none">
                <div class="form-group col-md-4">
                    <label for="inputsexo">Sexo</label>
                        <select class="form-control" name="sexo" id="sexo">
                            <option value="sin especificar">Sin Especificar</option>
                            <option value="MASCULINO">Masculino</option>
                            <option value="FEMENINO">Femenino</option>
                        </select>
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputestado_civil">Estado Civil</label>
                        <select class="form-control" name="estado_civil" id="estado_civil">
                            <option value="sin especificar">Sin Especificar</option>
                            <option value="SOLTERO">Soltero/a</option>
                            <option value="CASADO">Casado/a</option>
                            <option value="DDIVORCIADO">Divorciado/a</option>
                            <option value="VIUDO">Viudo/a</option>
                            <option value="CONCUBINATO">Concubinato</option>
                            <option value="UNION LIBRE">Union Libre</option>
                        </select>
                </div>
            </div>
            <div id="div3" class="form-row d-none d-print-none">
                <div class="form-group col-md-3">
                    <label for="inputfecha_nacimiento">Fecha de Nacimiento</label>
                    <input name='fecha_nacimientoins' id='fecha_nacimientoins' type="date" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputentidad">Entidad</label>
                    <input name='entidad' id='entidad' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputmunicipio">Municipio</label>
                    <input name='municipio' id='municipio' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputasentamiento">Asentamiento</label>
                    <input name='asentamiento' id='asentamiento' type="text" class="form-control" aria-required="true">
                </div>
            </div>
            <div id="div4" class="form-row d-none d-print-none">
                <div class="form-group col-md-4">
                    <label for="inputtelefono">Numero de Telefono Personal</label>
                    <input name="telefono" id="telefono" type="tel" class="form-control" aria-required="true" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="inputcorreo">Correo Electronico</label>
                    <input name="correo" id="correo" type="email" class="form-control" placeholder="correo_electronico@ejemplo.com" aria-required="true" required>
                </div>
            </div>
            <div id="div5" class="form-row d-none d-print-none">
                <div class="form-group col-md-3">
                    <label for="inputunidad_registra">Unidad que Registra</label>
                    <select class="form-control" name="unidad_registra" id="unidad_registra">
                        <option value="sin especificar">Sin Especificar</option>
                        @foreach ($data as $value )
                        <option value="{{$value->cct}}">{{$value->unidad}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputhonorarios">Tipo de Honorarios</label>
                    <select class="form-control" name="honorario" id="honorario">
                        <option value="sin especificar">Sin Especificar</option>
                        <option value="HONORARIOS">Honorarios</option>
                        <option value="SIN HONORARIOS">Sin Honorarios</option>
                        <option value="INTERNO">Interno</option>
                    </select>
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
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
@endsection
