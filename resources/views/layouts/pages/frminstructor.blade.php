<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Instructor | Sivyc Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <form action="{{ url('/instructor/guardar') }}" method="post" id="registerinstructor" enctype="multipart/form-data">
            @csrf
                <div>
                    <label><h2>Datos Personales</h2></label>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputnombre">Nombre</label>
                        <input name='nombre' id='nombre' type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputapellido_paterno">Apellido Paterno</label>
                        <input name='apellido_paterno' id='apellido_paterno' type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputapellido_materno">Apellido Materno</label>
                        <input name='apellido_materno' id='apellido_materno' type="text" class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label for="inputcurp">CURP</label>
                        <input name='curp' id='curp' type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-5">
                        <label for="inputrfc">RFC</label>
                        <input name='rfc' id='rfc' type="text" class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputsexo">Sexo</label>
                            <select class="form-control" id="sexo">
                                <option>Hombre</option>
                                <option>Mujer</option>
                            </select>
                    </div>
                    <div class="form-gorup col-md-4">
                        <label for="inputestado_civil">Estado Civil</label>
                            <select class="form-control" id="estado_civil">
                                <option>Soltero/a</option>
                                <option>Casado/a</option>
                                <option>Divorciado/a</option>
                                <option>Viudo/a</option>
                            </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputfecha_nacimiento">Fecha de Nacimiento</label>
                        <input name='fecha_nacimiento' id='fecha_nacimiento' type="date" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputlugar_nacimiento">Lugar de Nacimiento</label>
                        <input name='lugar_nacimiento' id='lugar_nacimiento' type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputlugar_residencia">Lugar de Residencia</label>
                        <input name='lugar_residencia' id='lugar_residencia' type="text" class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-10">
                        <label for="inputdomicilio">Direccion de Domicilio</label>
                        <input name="domicilio" id="domicilio" type="text" class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputtelefono">Numero de Telefono Personal</label>
                        <input name="telefono" id="telefono" type="tel" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputcorreo">Correo Electronico</label>
                        <input name="correo" id="correo" type="email" class="form-control" placeholder="correo_electronico@ejemplo.com" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
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
                <hr style="border-color:dimgray">
                <label><h2>Datos Academicos</h2></label>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputgrado_estudio">Ultimo Grado de Estudio</label>
                        <input name="grado_estudio" id="grado_estudio" type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-5">
                        <label for="inputperfil_profesional">Perfil Profesional</label>
                        <input name="perfil_profesional" id="perfil_profesional" type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputarea_carrera">Area de la Carrera</label>
                        <input name="area_carrera" id="area_carrera" type="text" class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label for="inputlicenciatura">Licenciatura</label>
                        <input name="licenciatura" id="licenciatura" type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputestatus">Estatus</label>
                        <input name="estatus" id="estatus" type="text" class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputinstitucion_pais">Pais de la Institucion Educativa</label>
                        <input name="institucion_pais" id="institucion_pais" type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputinstitucion_entidad">Entidad de la Institucion Educativa</label>
                        <input name="institucion_entidad" id="institucion_entidad" type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputinstitucion_nombre">Nombre de la Institucion Educativa</label>
                        <input name="institucion_nombre" id="institucion_nombre" type="text" class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="inputfecha_documento">Fecha de Expedicion del Documento</label>
                        <input name="fecha_documento" id="fecha_documento" type="date" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputfolio_documento">Folio del Documento</label>
                        <input name="folio_documento" id="folio_documento" type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="inputcapacitado_icatech">Capacitado por el ICATECH</label>
                        <select class="form-control" id="capacitado_icatech">
                            <option>No</option>
                            <option>Si</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputcv">Curriculum Vitae</label>
                        <input name="cv" id="cv" type="file" accept="application/pdf" class="form-control" aria-required="true">
                    </div>
                </div>
                <hr style="border-color:dimgray">
                <label><h2>Datos Institucionales</h2></label>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="inputnumero_control">Numero de Control</label>
                        <input id="numero_control" name="numero_control" type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-5">
                        <label for="inputhonorario">Tipo de Honorario</label>
                        <input id="honorario" name="honorario" type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputregistro_agente">Registo Agente Capacitador Externo STPS</label>
                        <input id="registro_agente" name="registro_agente" type="file" accept="application/pdf" class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputuncap_validacion">Unidad de Capacitacion que Solicita Validacion</label>
                        <input id="uncap_validacion" name="uncap_validacion" type="text" class="form-control " aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputmemo_validacion">Memorandum de Validacion</label>
                        <input id="memo_validacion" name="memo_validacion" type="file" accept="application/pdf" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputmemo_mod">Modificacion de Memorandum</label>
                        <input id="memo_mod" name="memo_mod" type="file" accept="application/pdf" class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form_row">
                    <label for="inputobservacion">Observaciones</label>
                    <textarea cols="6" rows="6" id="observacion" name="observacion" class="form-control"></textarea>
                </div>
                <br>
                <div style="text-align: right;width:100%">
                    <button type="submit" class="btn btn-primary" >Agregar</button>
                </div>
                <br>
        </form>
    </section>
@stop

