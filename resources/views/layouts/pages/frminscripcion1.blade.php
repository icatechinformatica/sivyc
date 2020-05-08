@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Solicitud de Inscripción | Sivyc Icatech')
<!--seccion-->
@section('content')

    <div class="container g-pt-50">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div><br />
        @endif
        <form method="POST" id="formsid" action="{{ route('alumnos.save') }}">
        @csrf
            <div style="text-align: center;">
                <label for="tituloformulariocurso"><h1>Solicitud de Inscripción (SID)</h1></label>
            </div>
            <hr style="border-color:dimgray">
            <div class="form-row">
            <!--nombre aspirante-->
            <div class="form-group col-md-4">
                <label for="nombre " class="control-label">Nombre del Aspirante</label>
                <input type="text" class="form-control" id="nombre" name="nombre">
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
                <label for="curp" class="control-label">Curp Aspirante</label>
                <input type="text" class="form-control" id="curp" name="curp" placeholder="Curp">
                </div>
                <!-- curp END -->
                <!-- genero-->
                <div class="form-group col-md-3">
                <label for="generoaspirante" class="control-label">Genero</label>
                <select class="form-control" id="generoaspirante" name="generoaspirante">
                    <option value="">--SELECCIONAR--</option>
                    <option value="Femenino">Mujer</option>
                    <option value="Masculino">Hombre</option>
                </select>
                </div>
                <!-- genero END-->
                <!-- edad -->
                <div class="form-group col-md-3">
                    <label for="fecha_nacimiento" class="control-label">Fecha de Nacimiento</label>
                    <input type="text" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
                </div>
                <!-- edad END -->
                <!-- telefono-->
                <div class="form-group col-md-3">
                    <label for="telefono" class="control-label">Teléfono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono">
                </div>
                <!-- telefono END-->
            </div>
            <div class="form-row">
                <!-- domicilio -->
                <div class="form-group col-md-6">
                    <label for="domicilio" class="control-label">Domicilio</label>
                    <input type="text" class="form-control" id="domicilio" name="domicilio">
                </div>
                <!-- domicilio END -->
                <div class="form-group col-md-6">
                    <label for="correo" class="control-label">Correo Electrónico</label>
                    <input type="text" class="form-control" id="correo" name="correo">
                </div>
            </div>
            <div class="form-row">
                <!-- colonia -->
                <div class="form-group col-md-3">
                <label for="colonia" class="control-label">Colonia</label>
                <input type="text" class="form-control" id="colonia" name="colonia">
                </div>
                <!-- colinia END -->
                <div class="form-group col-md-3">
                    <label for="codigo_postal" class="control-label">Código Postal</label>
                    <input type="text" class="form-control" id="codigo_postal" name="codigo_postal">
                </div>
                <!--estado-->
                <div class="form-group col-md-3">
                    <label for="estado" class="control-label">Estado</label>
                    <select class="form-control" id="estado" name="estado">
                        <option value="">--SELECCIONAR--</option>
                        <option value="1">estado1</option>
                        <option value="2">estado2</option>
                    </select>
                </div>
                <!--estado END-->
                <!--municipio-->
                <div class="form-group col-md-3">
                    <label for="municipio" class="control-label">Municipio</label>
                    <select class="form-control" id="municipio" name="municipio">
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
                <!-- estado civil END -->
                <div class="form-group col-md-6">
                    <label for="discapacidad_presente" class="control-label">Discapacidad que Presenta</label>
                    <input type="text" class="form-control" id="discapacidad_presente" name="discapacidad_presente">
                </div>
            </div>
            <hr>
            <!-- Campos para alumnos_registro
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="especialidad_que_desea_inscribirse" class="control-label">Especialidad a la que desea inscribirse</label>
                    <select class="form-control" id="especialidad_que_desea_inscribirse" name="especialidad_que_desea_inscribirse">
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
                    <label for="ultimo_grado_estudios" class="control-label">último grado de estudios</label>
                    <input type="text" class="form-control" id="ultimo_grado_estudios" name="ultimo_grado_estudios">
                </div>
                <div class="form-group col-md-4">
                    <label for="empresa_trabaja" class="control-label">Empresa donde trabaja</label>
                    <input type="text" class="form-control" id="empresa_trabaja" name="empresa_trabaja">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="antiguedad" class="control-label">Antigüedad</label>
                    <input type="text" class="form-control" id="antiguedad" name="antiguedad">
                </div>
                <div class="form-group col-md-6">
                    <label for="direccion_empresa" class="control-label">Dirección</label>
                    <input type="text" class="form-control" id="direccion_empresa" name="direccion_empresa">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="modo_entero_del_sistema" class="control-label">Medio por el que se enteró del sistema</label>
                    <br>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="modo_entero_del_sistema" value="prensa" id="hombre" class="form-check-input">
                        <label for="hombre" class="form-check-label">Prensa</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="modo_entero_del_sistema" value="television" id="mujer" class="form-check-input">
                        <label for="mujer" class="form-check-label">Televisión</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="modo_entero_del_sistema" value="folletos, carteles, volantes" id="alien" class="form-check-input">
                        <label for="alien" class="form-check-label">Folletos, Carteles, Volantes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="modo_entero_del_sistema" value="radio" id="alien" class="form-check-input">
                        <label for="alien" class="form-check-label">Radio</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="modo_entero_del_sistema" value="internet" id="alien" class="form-check-input">
                        <label for="alien" class="form-check-label">Internet</label>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="motivos_eleccion_sistema_capacitacion" class="control-label">Motivos de elección del sistema de capacitación</label>
                    <br>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="motivos_eleccion_sistema_capacitacion" value="para emplearse o autoemplearse" id="hombre" class="form-check-input">
                        <label for="hombre" class="form-check-label">Para emplearse o autoemplearse</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="motivos_eleccion_sistema_capacitacion" value="para ahorrar gastos al ingreso familia" id="mujer" class="form-check-input">
                        <label for="mujer" class="form-check-label">Para ahorrar gastos al ingreso familiar</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="motivos_eleccion_sistema_capacitacion" value="por estar en espera de incorporarse a otra institucion educativa" id="alien" class="form-check-input">
                        <label for="alien" class="form-check-label">Por estar en espera de incorporarse a otra institución educativa</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="motivos_eleccion_sistema_capacitacion" value="por disposicion de tiempo libre" id="alien" class="form-check-input">
                        <label for="alien" class="form-check-label">Por disposición de tiempo libre</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="motivos_eleccion_sistema_capacitacion" value="para mejorar la situacion en el trabajo" id="alien" class="form-check-input">
                        <label for="alien" class="form-check-label">Para mejorar la situación en el trabajo</label>
                    </div>
                </div>
            </div>-->
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
        <br>
    </div>
@endsection
