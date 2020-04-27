@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Solicitud de Inscripción | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container g-pt-50">
        <div style="text-align: center;">
            <h3><b>Solicitud de Inscripción (SID - 01)</b></h3>
        </div>
        <hr style="border-color:dimgray">
        <div style="text-align: center;">
            <h4><b>DATOS PERSONALES</b></h4>
        </div>
        <form method="POST" id="form_sid" action="{{ route('alumnos.save') }}">
            @csrf
            <div class="form-row">
                <!--nombre aspirante-->
                <div class="form-group col-md-4">
                    <label for="nombre " class="control-label">Nombre del Aspirante</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" autocomplete="off">
                </div>
                <!--nombre aspirante END-->
                <!-- apellido paterno -->
                <div class="form-group col-md-4">
                    <label for="apaterno" class="control-label">Apellido Paterno</label>
                    <input type="text" class="form-control" id="apaterno" name="apaterno" autocomplete="off">
                </div>
                <!-- apellido paterno END -->
                <!-- apellido materno-->
                <div class="form-group col-md-4">
                    <label for="amaterno" class="control-label">Apellido Materno</label>
                    <input type="text" class="form-control" id="amaterno" name="amaterno" autocomplete="off">
                </div>
                <!-- apellido materno END-->
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="genero" class="control-label">Genero</label>
                    <select class="form-control" id="genero" name="genero">
                        <option value="">--SELECCIONAR--</option>
                        <option value="Femenino">Mujer</option>
                        <option value="Masculino">Hombre</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="curp_" class="control-label">Curp Aspirante</label>
                    <input type="text" class="form-control" id="curp_" name="curp_" placeholder="Curp" autocomplete="off">
                </div>
                <div class="form-group col-md-3">
                    <label for="fecha_nac" class="control-label">Fecha de Nacimiento</label>
                    <input type="text" class="form-control" id="fecha_nac" name="fecha_nac" autocomplete="off">
                </div>
                <div class="form-group col-md-3">
                    <label for="telefono_personalizado" class="control-label">Teléfono</label>
                    <input type="text" class="form-control" id="telefono_personalizado" name="telefono_personalizado" autocomplete="off">
                </div>
            </div>
            <div class="form-row">
                <!-- domicilio -->
                <div class="form-group col-md-6">
                    <label for="domicilio_" class="control-label">Domicilio</label>
                    <input type="text" class="form-control" id="domicilio_" name="domicilio_" autocomplete="off">
                </div>
                <!-- domicilio END -->
                <div class="form-group col-md-6">
                    <label for="colonia_localidad" class="control-label">Colonia o Localidad</label>
                    <input type="text" class="form-control" id="colonia_localidad" name="colonia_localidad" autocomplete="off">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="codigo_postal" class="control-label">C.P.</label>
                    <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" autocomplete="off">
                </div>
                <div class="form-group col-md-4">
                    <label for="estado" class="control-label">Estado</label>
                    <select class="form-control" id="estado" name="estado">
                        <option value="">--SELECCIONAR--</option>
                        <option value="1">estado1</option>
                        <option value="2">estado2</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="municipio" class="control-label">Municipio</label>
                    <select class="form-control" id="municipio" name="municipio">
                        <option value="">--SELECCIONAR--</option>
                        <option value="1">estado1</option>
                        <option value="2">estado2</option>
                    </select>
                </div>
            </div>
            <!--formulario-->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="estado_civil" class="control-label">Estado Civil</label>
                    <select class="form-control" id="estado_civil" name="estado_civil">
                        <option value="">--SELECCIONAR--</option>
                        <option value="Soltero (a)">Soltero (a)</option>
                        <option value="Casado (a)">Casado (a)</option>
                        <option value="Union libre">Unión Libre</option>
                        <option value="Divorciado (a)">Divorciado (a)</option>
                        <option value="Viudo (a)">Viudo (a)</option>
                        <option value="No especifica">No especifica</option>
                    </select>
                </div>
                <!---->
                <div class="form-group col-md-6">
                    <label for="discapacidad_presenta" class="control-label">Discapacidad que presenta</label>
                    <select class="form-control" id="discapacidad_presenta" name="discapacidad_presenta">
                        <option value="">--SELECCIONAR--</option>
                        <option value="VISUAL">VISUAL</option>
                        <option value="AUDITIVA">AUDITIVA</option>
                        <option value="DE COMUNICACIÓN">DE COMUNICACIÓN</option>
                        <option value="MOTRIZ">MOTRIZ</option>
                        <option value="INTELECTUAL">INTELECTUAL</option>
                        <option value="NINGUNA">NINGUNA</option>
                    </select>
                </div>
            </div>
            <!--botones de enviar y retroceder-->
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
        </form>
    </div>
@endsection
