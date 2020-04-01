<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Instructor | Sivyc Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <form action="{{ url('/instructor/guardar') }}" method="post" id="registerinstructor" enctype="multipart/form-data">
            @csrf
            <div class="text-center">
                <h1>Formulario Instructor<h1>
            </div>
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
                <div class="form-group col-md-4">
                    <label for="inputcurp">CURP</label>
                    <input name='curp' id='curp' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputrfc">RFC</label>
                    <input name='rfc' id='rfc' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputfolio_ine">Folio de INE</label>
                    <input name='folio_ine' id='folio_ine' type="text" class="form-control" aria-required="true">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputsexo">Sexo</label>
                        <select class="form-control" name="sexo" id="sexo">
                            <option value="sin especificar">Sin Especificar</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                        </select>
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputestado_civil">Estado Civil</label>
                        <select class="form-control" name="estado_civil" id="estado_civil">
                            <option value="sin especificar">Sin Especificar</option>
                            <option value="Soltero/a">Soltero/a</option>
                            <option value="Casado/a">Casado/a</option>
                            <option value="Divorciado/a">Divorciado/a</option>
                            <option value="Viudo/a">Viudo/a</option>
                            <option value="Concubinato">Concubinato</option>
                            <option value="Union Libre">Union Libre</option>
                        </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputfecha_nacimiento">Fecha de Nacimiento</label>
                    <input name='fecha_nacimiento' id='fecha_nacimiento' type="date" class="form-control" aria-required="true">
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
            <!-- *** START Text areas *** -->
                <div class="form-row">
                    <label for="inputexp_laboral"><h4>Experiencia Laboral</h4></label>
                    <textarea cols="6" rows="6" id="exp_laboral" name="exp_laboral" class="form-control"></textarea>
                </div>
                <br>
                <div class="form-row">
                    <label for="inputexp_docente"><h4>Experiencia Docente</h4></label>
                    <textarea cols="6" rows="6" id="exp_docente" name="exp_docente" class="form-control"></textarea>
                </div>
                <br>
                <div class="form-row">
                    <label for="inputcursos_recibidos"><h4>Cursos Recibidos</h4></label>
                    <textarea cols="6" rows="6" id="cursos_recibidos" name="cursos_recibidos" class="form-control"></textarea>
                </div>
                <br>
                <div class="form-row">
                    <label for="inputcursos_conocer"><h4>Cursos CONOCER</h4></label>
                    <textarea cols="6" rows="6" id="cursos_conocer" name="cursos_conocer" class="form-control"></textarea>
                </div>
                <br>
                <div class="form-row">
                    <label for="inputcursos_impartidos"><h4>Cursos Impartidos</h4></label>
                    <textarea cols="6" rows="6" id="cursos_impartidos" name="cursos_impartidos" class="form-control"></textarea>
                </div>
            <!-- *** END Text areas *** -->
            <br>
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="inputcapacitado_icatech"><h6>Capacitado por el ICATECH</h6></label>
                    <select id="cap_icatech" name="cap_icatech" class="form-control">
                        <option value="No">No</option>
                        <option value="Si">Si</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <label for="inputcursos_recicatech"><h4>Cursos Recibidos por el ICATECH</h4></label>
                <textarea cols="6" rows="6" id="cursos_recicatech" name="cursos_recicatech" class="form-control"></textarea>
            </div>
            <br>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputcv">Curriculum Vitae</label>
                    <input name="cv" id="cv" type="file" accept="application/pdf" class="form-control" aria-required="true">
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

