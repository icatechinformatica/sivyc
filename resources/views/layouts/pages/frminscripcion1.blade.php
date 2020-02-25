@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Solicitud de Inscripción | Sivyc Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        <form method="POST" id="formsid">
            @csrf
            <div style="text-align: center;">
                <label for="tituloformulariocurso"><h1>Solicitud de Inscripción (SID)</h1></label>
            </div>
            <hr style="border-color:dimgray">
            <div class="form-row">
            <!--nombre aspirante-->
            <div class="form-group col-md-4">
                <label for="nombreaspirante " class="control-label">Nombre del Aspirante</label>
                <input type="text" class="form-control" id="nombreaspirante" name="nombreaspirante">
            </div>
            <!--nombre aspirante END-->
            <!-- apellido paterno -->
            <div class="form-group col-md-4">
                <label for="apaternoaspirante" class="control-label">Apellido Paterno</label>
                <input type="text" class="form-control" id="apaternoaspirante" name="apaternoaspirante">
            </div>
            <!-- apellido paterno END -->
            <!-- apellido materno-->
            <div class="form-group col-md-4">
                <label for="amaternoaspirante" class="control-label">Apellido Materno</label>
                <input type="text" class="form-control" id="amaternoaspirante" name="amaternoaspirante">
            </div>
            <!-- apellido materno END-->

            </div>
            <div class="form-row">
                <!-- curp -->
                <div class="form-group col-md-3">
                <label for="curpaspirante" class="control-label">Curp Aspirante</label>
                <input type="text" class="form-control" id="curpaspirante" name="curpaspirante" placeholder="Curp">
                </div>
                <!-- curp END -->
                <!-- genero-->
                <div class="form-group col-md-3">
                <label for="generoaspirante" class="control-label">Genero</label>
                <select class="form-control" id="generoaspirante" name="generoaspirante">
                    <option value="">--SELECCIONAR--</option>
                    <option value="1">Mujer</option>
                    <option value="2">Hombre</option>
                </select>
                </div>
                <!-- genero END-->
                <!-- edad -->
                <div class="form-group col-md-3">
                    <label for="fechanacaspirante" class="control-label">Fecha de Nacimiento</label>
                    <input type="text" class="form-control" id="fechanacaspirante" name="fechanacaspirante">
                </div>
                <!-- edad END -->
                <!-- telefono-->
                <div class="form-group col-md-3">
                    <label for="telefonoaspirante" class="control-label">Teléfono</label>
                    <input type="text" class="form-control" id="telefonoaspirante" name="telefonoaspirante">
                </div>
                <!-- telefono END-->
            </div>
            <div class="form-row">
                <!-- domicilio -->
                <div class="form-group col-md-12">
                <label for="domicilioaspirante" class="control-label">Domicilio</label>
                <input type="text" class="form-control" id="domicilioaspirante" name="domicilioaspirante">
                </div>
                <!-- domicilio END -->
            </div>
            <div class="form-row">
                <!-- colonia -->
                <div class="form-group col-md-3">
                <label for="coloniaaspirante" class="control-label">Colonia</label>
                <input type="text" class="form-control" id="coloniaaspirante" name="coloniaaspirante">
                </div>
                <!-- colinia END -->
                <div class="form-group col-md-3">
                    <label for="codigopostalaspirante" class="control-label">Código Postal</label>
                    <input type="text" class="form-control" id="codigopostalaspirante" name="codigopostalaspirante">
                </div>
                <!--estado-->
                <div class="form-group col-md-3">
                    <label for="estadoaspirante" class="control-label">Estado</label>
                    <select class="form-control" id="estadoaspirante" name="estadoaspirante">
                        <option value="">--SELECCIONAR--</option>
                        <option value="1">estado1</option>
                        <option value="2">estado2</option>
                    </select>
                </div>
                <!--estado END-->
                <!--municipio-->
                <div class="form-group col-md-3">
                    <label for="municipioaspirante" class="control-label">Municipio</label>
                    <select class="form-control" id="municipioaspirante" name="municipioaspirante">
                        <option value="">--SELECCIONAR--</option>
                        <option value="1">estado1</option>
                        <option value="2">estado2</option>
                    </select>
                </div>
                <!--municipio END-->
            </div>
            <div class="form-row">
                <!-- estado civil -->
                <div class="form-group col-md-6">
                    <label for="estadocivil" class="control-label">Estado Civil</label>
                    <select class="form-control" id="estadocivil" name="estadocivil">
                        <option value="">--SELECCIONAR--</option>
                        <option value="1">opcion1</option>
                        <option value="2">opcion2</option>
                    </select>
                </div>
                <!-- estado civil END -->
                <div class="form-group col-md-6">
                    <label for="discapacidadaspirante" class="control-label">Discapacidad que Presenta</label>
                    <input type="text" class="form-control" id="discapacidadaspirante" name="discapacidadaspirante">
                </div>
            </div>
            <hr>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="especialidadquedeseainscribirse" class="control-label">Especialidad a la que desea inscribirse</label>
                    <select class="form-control" id="especialidadquedeseainscribirse" name="especialidadquedeseainscribirse">
                        <option value="">--SELECCIONAR--</option>
                        <option value="1">opcion1</option>
                        <option value="2">opcion2</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="cursoaspirante" class="control-label">Curso</label>
                    <input type="text" class="form-control" id="curso" name="curso">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="horario" class="control-label">Horario</label>
                    <input type="text" class="form-control" id="horario" name="horario">
                </div>
                <div class="form-group col-md-4">
                    <label for="ultimogradodeestudios" class="control-label">último grado de estudios</label>
                    <input type="text" class="form-control" id="ultimogradodeestudios" name="ultimogradodeestudios">
                </div>
                <div class="form-group col-md-4">
                    <label for="empresadondetrabaja" class="control-label">Empresa donde trabaja</label>
                    <input type="text" class="form-control" id="empresadondetrabaja" name="empresadondetrabaja">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="antiguedad" class="control-label">Antigüedad</label>
                    <input type="text" class="form-control" id="antiguedad" name="antiguedad">
                </div>
                <div class="form-group col-md-6">
                    <label for="direccion" class="control-label">Dirección</label>
                    <input type="text" class="form-control" id="direccion" name="direccion">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="direccion" class="control-label">Medio por el que se enteró del sistema</label>
                    <br>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="sexo" id="hombre" class="form-check-input">
                        <label for="hombre" class="form-check-label">Presan</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="sexo" id="mujer" class="form-check-input">
                        <label for="mujer" class="form-check-label">Televisión</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="sexo" id="alien" class="form-check-input">
                        <label for="alien" class="form-check-label">Folletos, Carteles, Volantes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="sexo" id="alien" class="form-check-input">
                        <label for="alien" class="form-check-label">Radio</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="sexo" id="alien" class="form-check-input">
                        <label for="alien" class="form-check-label">Internet</label>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="direccion" class="control-label">Motivos de elección del sistema de capacitación</label>
                    <br>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="sexo" id="hombre" class="form-check-input">
                        <label for="hombre" class="form-check-label">Para emplearse o autoemplearse</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="sexo" id="mujer" class="form-check-input">
                        <label for="mujer" class="form-check-label">Para ahorrar gastos al ingreso familiar</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="sexo" id="alien" class="form-check-input">
                        <label for="alien" class="form-check-label">Por estar en espera de incorporarse a otra institución educativa</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="sexo" id="alien" class="form-check-input">
                        <label for="alien" class="form-check-label">Por disposición de tiempo libre</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="sexo" id="alien" class="form-check-input">
                        <label for="alien" class="form-check-label">Para mejorar la situación en el trabajo</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
        <br>
    </div>
@endsection
