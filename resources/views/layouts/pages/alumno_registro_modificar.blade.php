@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Matricular Alumno | Sivyc Icatech')
<!--contenido-->
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
        <div style="text-align: center;">
            <h3><b>Matricular (SID - 01)</b></h3>
        </div>
        <hr style="border-color:dimgray">
        <div style="text-align: center;">
            <h4><b>DATOS PERSONALES</b></h4>
        </div>
            <div class="form-row">
                <!--nombre aspirante-->
                <div class="form-group col-md-3">
                    <label for="nombre " class="control-label">NOMBRE:</label>
                    <input type="text" class="form-control" name="nombre_alumno" id="nombre_alumno" value="{{$alumnos[0]->nombrealumno}}">
                </div>
                <!--nombre aspirante END-->
                <!-- apellido paterno -->
                <div class="form-group col-md-3">
                    <label for="apellidoPaterno" class="control-label">APELLIDO PATERNO:</label>
                    <input type="text" class="form-control" name="apellido_paterno" id="apellido_paterno" value="{{$alumnos[0]->apellidoPaterno}}">
                </div>
                <!-- apellido paterno END -->
                <!-- apellido materno-->
                <div class="form-group col-md-3">
                    <label for="apellidoMaterno" class="control-label">APELLIDO MATERNO:</label>
                    <input type="text" name="apellido_materno" id="apellido_materno" class="form-control" value="{{$alumnos[0]->apellidoMaterno}}">
                </div>
                <!-- apellido materno END-->
                <div class="form-group col-md-3">
                    <label for="sexo" class="control-label">GENERO</label>
                    <select class="form-control" id="sexo" name="sexo">
                        <option value="">--SELECCIONAR--</option>
                        <option {{ trim($alumnos[0]->sexo) == "FEMENINO" ? "selected" : ""  }} value="FEMENINO">MUJER</option>
                        <option {{ trim($alumnos[0]->sexo) == "MASCULINO" ? "selected" : ""  }} value="MASCULINO">HOMBRE</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <b><label for="fechanacimiento" class="control-label">FECHA DE NACIMIENTO</label></b>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="dia" class="control-label">DÍA</label>
                    <select class="form-control" id="dia" name="dia">
                        <option value="">--SELECCIONAR--</option>
                        @for ($i = 01; $i <= 31; $i++)
                        <option {{ ($dia_nac == $i) ? "selected" : ""  }}  value="{{$i}}">{{$i}}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="mes" class="control-label">MES</label>
                    <select class="form-control" id="mes" name="mes">
                        <option value="">--SELECCIONAR--</option>
                        <option {{ ($mes_nac == "01") ? "selected" : ""  }} value="01">ENERO</option>
                        <option {{ ($mes_nac == "02") ? "selected" : ""  }} value="02">FEBRERO</option>
                        <option {{ ($mes_nac == "03") ? "selected" : ""  }} value="03">MARZO</option>
                        <option {{ ($mes_nac == "04") ? "selected" : ""  }} value="04">ABRIL</option>
                        <option {{ ($mes_nac == "05") ? "selected" : ""  }} value="05">MAYO</option>
                        <option {{ ($mes_nac == "06") ? "selected" : ""  }} value="06">JUNIO</option>
                        <option {{ ($mes_nac == "07") ? "selected" : ""  }} value="07">JULIO</option>
                        <option {{ ($mes_nac == "08") ? "selected" : ""  }} value="08">AGOSTO</option>
                        <option {{ ($mes_nac == "09") ? "selected" : ""  }} value="09">SEPTIEMBRE</option>
                        <option {{ ($mes_nac == "10") ? "selected" : ""  }} value="10">OCTUBRE</option>
                        <option {{ ($mes_nac == "11") ? "selected" : ""  }} value="11">NOVIEMBRE</option>
                        <option {{ ($mes_nac == "12") ? "selected" : ""  }} value="12">DICIEMBRE</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="anio" class="control-label">AÑO</label>
                    <input type="text" class="form-control" id="anio" name="anio" value="{{$anio_nac}}" placeholder="INGRESA EL AÑO EJ. 1943" autocomplete="off">
                </div>
            </div>
            <div class="form-row">

                <div class="form-group col-md-3">
                    <label for="telefono" class="control-label">TELÉFONO:</label>
                    <input type="text" name="telefono" class="form-control" id="telefono" value="{{$alumnos[0]->telefono}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="cp" class="control-label">C.P.</label>
                    <input type="text" name="codigo_postal" id="codigo_postal" class="form-control" value="{{$alumnos[0]->cp}}">
                </div>
            </div>
            <div class="form-row">

                <div class="form-group col-md-3">
                    <label for="estado" class="control-label">ESTADO:</label>
                    <select class="form-control" id="estado" name="estado">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($estados as $itemEstado)
                            <option {{ (trim($alumnos[0]->estado) == trim($itemEstado->nombre)) ? "selected" : "" }} value="{{$itemEstado->nombre}}">{{ $itemEstado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="municipio" class="control-label">MUNICIPIO:</label>
                    <select class="form-control" id="municipio" name="municipio">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($municipios as $itemMunicipio)
                            <option {{ ($alumnos[0]->municipio == $itemMunicipio->muni) ? "selected" : ""  }} value="{{$itemMunicipio->muni}}">{{ $itemMunicipio->muni }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="estado_civil" class="control-label">ESTADO CIVIL</label>
                    <select class="form-control" id="estado_civil" name="estado_civil">
                        <option value="">--SELECCIONAR--</option>
                        <option {{( $alumnos[0]->estado_civil == "SOLTERO (A)") ? "selected" : "" }} value="SOLTERO (A)">SOLTERO (A)</option>
                        <option {{( $alumnos[0]->estado_civil == "CASADO (A)") ? "selected" : "" }} value="CASADO (A)">CASADO (A)</option>
                        <option {{( $alumnos[0]->estado_civil == "UNIÓN LIBRE") ? "selected" : "" }} value="UNIÓN LIBRE">UNIÓN LIBRE</option>
                        <option {{( $alumnos[0]->estado_civil == "DIVORCIADO (A)") ? "selected" : "" }} value="DIVORCIADO (A)">DIVORCIADO (A)</option>
                        <option {{( $alumnos[0]->estado_civil == "VIUDO (A)") ? "selected" : "" }} value="VIUDO (A)">VIUDO (A)</option>
                        <option {{( $alumnos[0]->estado_civil == "NO ESPECIFICA") ? "selected" : "" }} value="NO ESPECIFICA">NO ESPECIFICA</option>
                    </select>
                </div>
                <!---->
                <div class="form-group col-md-3">
                    <label for="discapacidad" class="control-label">DISCAPACIDAD QUE PRESENTA</label>
                    <select class="form-control" id="discapacidad" name="discapacidad">
                        <option value="">--SELECCIONAR--</option>
                        <option {{( $alumnos[0]->discapacidad == "VISUAL") ? "selected" : "" }} value="VISUAL">VISUAL</option>
                        <option {{( $alumnos[0]->discapacidad == "AUDITIVA") ? "selected" : "" }} value="AUDITIVA">AUDITIVA</option>
                        <option {{( $alumnos[0]->discapacidad == "DE COMUNICACIÓN") ? "selected" : "" }} value="DE COMUNICACIÓN">DE COMUNICACIÓN</option>
                        <option {{( $alumnos[0]->discapacidad == "MOTRIZ") ? "selected" : "" }} value="MOTRIZ">MOTRIZ</option>
                        <option {{( $alumnos[0]->discapacidad == "INTELECTUAL") ? "selected" : "" }} value="INTELECTUAL">INTELECTUAL</option>
                        <option {{( $alumnos[0]->discapacidad == "NINGUNA") ? "selected" : "" }} value="NINGUNA">NINGUNA</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <!-- domicilio -->
                <div class="form-group col-md-6">
                    <label for="domicilio" class="control-label">DOMICILIO:</label>
                    <input type="text" class="form-control" name="domicilio" id="domicilio" autocomplete="off" value="{{$alumnos[0]->domicilio}}">
                </div>
                <!-- domicilio END -->
                <div class="form-group col-md-6">
                    <label for="colonia" class="control-label">COLONIA:</label>
                    <input type="text" class="form-control" name="colonia" id="colonia" autocomplete="off" value="{{$alumnos[0]->colonia}}">
                </div>
            </div>
            <!---->
            <hr style="border-color:dimgray">
            <div style="text-align: center;">
                <h4><b>DATOS GENERALES</b></h4>
            </div>
            <form method="POST" id="form_sid_registro" action="" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="especialidad" class="control-label">ESPECIALIDAD:</label>
                        <select class="form-control" id="especialidad_sid" name="especialidad_sid" disabled>
                            <option value="">--SELECCIONAR--</option>
                            @foreach ($especialidades as $itemEspecialidad)
                                <option {{ ($alumnos[0]->id_especialidad == $itemEspecialidad->id) ? "selected" : "" }} value="{{$itemEspecialidad->id}}">{{ $itemEspecialidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="cursos" class="control-label">CURSO:</label>
                        <select class="form-control" id="cursos_sid" name="cursos_sid" disabled>
                            <option value="">--SELECCIONAR--</option>
                            @foreach ($cursos as $itemCursos)
                                <option {{ ($alumnos[0]->id_curso == $itemCursos->id) ? "selected" : "" }} value="{{$itemCursos->id}}">{{ $itemCursos->nombre_curso }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="horario" class="control-label">HORARIO:</label>
                        <input type="text" name="horario" id="horario" value="{{$alumnos[0]->horario}}" class="form-control" autocomplete="off">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="grupo" class="control-label">GRUPO:</label>
                        <input type="text" name="grupo" id="grupo" value="{{$alumnos[0]->grupo}}" class="form-control" autocomplete="off">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="horario" class="control-label">TIPO DE CURSO</label>
                        <select class="form-control" id="tipo_curso" name="tipo_curso" required>
                            <option value="">--SELECCIONAR--</option>
                            <option {{ ($alumnos[0]->tipo_curso == "PRESENCIAL") ? "selected" : "" }} value="PRESENCIAL">PRESENCIAL</option>
                            <option {{ ($alumnos[0]->tipo_curso == "ONLINE") ? "selected" : "" }} value="ONLINE">EN LÍNEA</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="cerrs" class="control-label">¿SE ENCUENTRA EN EL CERESO?</label>
                        <select class="form-control" id="cerrs" name="cerrs" required>
                            <option value="">--SELECCIONAR--</option>
                            <option {{ ($alumnos[0]->cerrs == true) ? "selected" : "" }} value="true">SI</option>
                            <option {{ ($alumnos[0]->cerrs == false) ? "selected" : "" }} value="false">NO</option>
                        </select>
                    </div>
                </div>
                <!--botones de enviar y retroceder-->
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                        </div>
                        @can('alumno.inscrito.update')
                            <div class="pull-right">
                                <button type="submit" class="btn btn-primary" >Modificar</button>
                            </div>
                        @endcan
                    </div>
                </div>
                <input type="hidden" name="alumno_id" id="alumno_id" value="{{$alumnos[0]->id}}">
            </form>
    </div>
@endsection
