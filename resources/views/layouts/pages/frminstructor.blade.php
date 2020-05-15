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
            <!-- Direccion de Domicilio
                <div class="form-row">
                    <div class="form-group col-md-10">
                        <label for="inputdomicilio">Direccion de Domicilio</label>
                        <input name="domicilio" id="domicilio" type="text" class="form-control" aria-required="true">
                    </div>
                </div>
            -->
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
                        <option value="ASALARIADO ASIMILADO">Asalariado Asimilado</option>
                    </select>
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

