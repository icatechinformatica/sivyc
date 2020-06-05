@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Modificar Alumno | Sivyc Icatech')
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
            <h3><b>MODIFICAR ALUMNOS</b></h3>
        </div>
        <hr style="border-color:dimgray">
        <div style="text-align: center;">
            <h4><b>DATOS PERSONALES</b></h4>
        </div>
        <form method="POST" id="form_sid_registro" action="{{ route('sid.modificar') }}">
            <div class="form-row">
                <!-- apellido paterno -->
                <div class="form-group col-md-3">
                    <label for="apellidoPaterno" class="control-label">APELLIDO PATERNO:</label>
                    <input type="text" class="form-control" name="apellido_paterno" id="apellido_paterno" value="{{$alumno->apellidoPaterno}}">
                </div>
                <!-- apellido paterno END -->
                <!-- apellido materno-->
                <div class="form-group col-md-3">
                    <label for="apellidoMaterno" class="control-label">APELLIDO MATERNO:</label>
                    <input type="text" name="apellido_materno" id="apellido_materno" class="form-control" value="{{$alumno->apellidoMaterno}}">
                </div>
                <!-- apellido materno END-->
                <!--nombre aspirante-->
                <div class="form-group col-md-3">
                    <label for="nombre " class="control-label">NOMBRE:</label>
                    <input type="text" class="form-control" name="nombre_alumno" id="nombre_alumno" value="{{$alumno->nombre}}">
                </div>
                <!--nombre aspirante END-->
                <div class="form-group col-md-3">
                    <label for="sexo" class="control-label">GENERO</label>
                    <select class="form-control" id="sexo" name="sexo">
                        <option value="">--SELECCIONAR--</option>
                        <option {{ trim($alumno->sexo) == "FEMENINO" ? "selected" : ""  }} value="FEMENINO">MUJER</option>
                        <option {{ trim($alumno->sexo) == "MASCULINO" ? "selected" : ""  }} value="MASCULINO">HOMBRE</option>
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
                    <input type="text" name="telefono" class="form-control" id="telefono" value="{{$alumno->telefono}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="cp" class="control-label">C.P.</label>
                    <input type="text" name="codigo_postal" id="codigo_postal" class="form-control" value="{{$alumno->cp}}">
                </div>
            </div>
            <div class="form-row">

                <div class="form-group col-md-3">
                    <label for="estado" class="control-label">ESTADO:</label>
                    <select class="form-control" id="estado" name="estado">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($estados as $itemEstado)
                            <option {{ (trim($alumno->estado) == trim($itemEstado->nombre)) ? "selected" : "" }} value="{{$itemEstado->nombre}}">{{ $itemEstado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="municipio" class="control-label">MUNICIPIO:</label>
                    <select class="form-control" id="municipio" name="municipio">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($municipios as $itemMunicipio)
                            <option {{ ($alumno->municipio == $itemMunicipio->muni) ? "selected" : ""  }} value="{{$itemMunicipio->muni}}">{{ $itemMunicipio->muni }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="estado_civil" class="control-label">ESTADO CIVIL</label>
                    <select class="form-control" id="estado_civil" name="estado_civil">
                        <option value="">--SELECCIONAR--</option>
                        <option {{( $alumno->estado_civil == "SOLTERO (A)") ? "selected" : "" }} value="SOLTERO (A)">SOLTERO (A)</option>
                        <option {{( $alumno->estado_civil == "CASADO (A)") ? "selected" : "" }} value="CASADO (A)">CASADO (A)</option>
                        <option {{( $alumno->estado_civil == "UNIÓN LIBRE") ? "selected" : "" }} value="UNIÓN LIBRE">UNIÓN LIBRE</option>
                        <option {{( $alumno->estado_civil == "DIVORCIADO (A)") ? "selected" : "" }} value="DIVORCIADO (A)">DIVORCIADO (A)</option>
                        <option {{( $alumno->estado_civil == "VIUDO (A)") ? "selected" : "" }} value="VIUDO (A)">VIUDO (A)</option>
                        <option {{( $alumno->estado_civil == "NO ESPECIFICA") ? "selected" : "" }} value="NO ESPECIFICA">NO ESPECIFICA</option>
                    </select>
                </div>
                <!---->
                <div class="form-group col-md-3">
                    <label for="discapacidad" class="control-label">DISCAPACIDAD QUE PRESENTA</label>
                    <select class="form-control" id="discapacidad" name="discapacidad">
                        <option value="">--SELECCIONAR--</option>
                        <option {{( $alumno->discapacidad == "VISUAL") ? "selected" : "" }} value="VISUAL">VISUAL</option>
                        <option {{( $alumno->discapacidad == "AUDITIVA") ? "selected" : "" }} value="AUDITIVA">AUDITIVA</option>
                        <option {{( $alumno->discapacidad == "DE COMUNICACIÓN") ? "selected" : "" }} value="DE COMUNICACIÓN">DE COMUNICACIÓN</option>
                        <option {{( $alumno->discapacidad == "MOTRIZ") ? "selected" : "" }} value="MOTRIZ">MOTRIZ</option>
                        <option {{( $alumno->discapacidad == "INTELECTUAL") ? "selected" : "" }} value="INTELECTUAL">INTELECTUAL</option>
                        <option {{( $alumno->discapacidad == "NINGUNA") ? "selected" : "" }} value="NINGUNA">NINGUNA</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <!-- domicilio -->
                <div class="form-group col-md-6">
                    <label for="domicilio" class="control-label">DOMICILIO:</label>
                    <input type="text" class="form-control" name="domicilio" id="domicilio" autocomplete="off" value="{{$alumno->domicilio}}">
                </div>
                <!-- domicilio END -->
                <div class="form-group col-md-6">
                    <label for="colonia" class="control-label">COLONIA O LOCALIDAD:</label>
                    <input type="text" class="form-control" name="colonia" id="colonia" autocomplete="off" value="{{$alumno->colonia}}">
                </div>
            </div>
            <!---->
            <hr style="border-color:dimgray">
            <div style="text-align: center;">
                <h4><b>DATOS GENERALES DE CAPACITACIÓN</b></h4>
            </div>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="ultimo_grado_estudios" class="control-label">ÚLTIMO GRADO DE ESTUDIOS:</label>
                    <input type="text" name="ultimo_grado_estudios" id="ultimo_grado_estudios" value="{{$alumno->ultimo_grado_estudios}}" class="form-control" autocomplete="off">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="medio_entero" class="control-label">MEDIO POR EL QUE SE ENTERÓ DEL SISTEMA</label>
                    <select class="form-control" id="medio_entero" name="medio_entero">
                        <option value="">--SELECCIONAR--</option>
                        <option {{( $alumno->medio_entero == "PRENSA") ? "selected" : "" }} value="PRENSA">PRENSA</option>
                        <option {{( $alumno->medio_entero == "RADIO") ? "selected" : "" }} value="RADIO">RADIO</option>
                        <option {{( $alumno->medio_entero == "TELEVISIÓN") ? "selected" : "" }} value="TELEVISIÓN">TELEVISIÓN</option>
                        <option {{( $alumno->medio_entero == "INTERNET") ? "selected" : "" }} value="INTERNET">INTERNET</option>
                        <option {{( $alumno->medio_entero == "FOLLETOS, CARTELES, VOLANTES") ? "selected" : "" }} value="FOLLETOS, CARTELES, VOLANTES">FOLLETOS, CARTELES, VOLANTES</option>
                        <option {{( $alumno->medio_entero == "SOLTERO (A)") ? "selected" : "" }} value="0">OTRO</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <div class="medio_especificar">
                        <label for="medio_entero_especificar" class="control-label">ESPECIFIQUE</label>
                        <input type="text" class="form-control" name="medio_entero_especificar" id="medio_entero_especificar">
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="motivos_eleccion_sistema_capacitacion" class="control-label">MOTIVOS DE ELECCIÓN DEL SISTEMA DE CAPACITACIÓN:</label>
                    <select class="form-control" name="motivos_eleccion_sistema_capacitacion" id="motivos_eleccion_sistema_capacitacion">
                        <option value="">--SELECCIONAR--</option>
                        <option value="EMPLEARSE O AUTOEMPLEARSE">PARA EMPLEARSE O AUTOEMPLEARSE</option>
                        <option value="AHORRAR GASTOS AL INGRESO FAMILIAR">PARA AHORRAR GASTOS AL INGRESO FAMILIAR</option>
                        <option value="ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA">POR ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA</option>
                        <option value="PARA MEJORAR SU SITUACIÓN EN EL TRABAJO">PARA MEJORAR SU SITUACIÓN EN EL TRABAJO</option>
                        <option value="POR DISPOSICIÓN DE TIEMPO LIBRE">POR DISPOSICIÓN DE TIEMPO LIBRE</option>
                        <option value="0">OTRO</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <div class="capacitacion_especificar">
                        <label for="sistema_capacitacion_especificar" class="control-label">ESPECIFIQUE:</label>
                        <input type="text" class="form-control" name="sistema_capacitacion_especificar" id="sistema_capacitacion_especificar">
                    </div>
                </div>
            </div>

            <!--DATOS DE EMPLEO-->
            <hr style="border-color: dimgray">
            <div style="text-align: center;">
                <h4><b>DATOS DE EMPLEO</b></h4>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="empresa" class="control-label">EMPRESA DONDE TRABAJA:</label>
                    <input type="text" name="empresa" id="empresa" class="form-control" value="{{$alumno->empresa_trabaja}}" autocomplete="off">
                </div>
                <div class="form-group col-md-6">
                    <label for="puesto_empresa" class="control-label">PUESTO:</label>
                    <input type="text" name="puesto_empresa" id="puesto_empresa" value="{{$alumno->puesto_empresa}}" class="form-control" autocomplete="off">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="antiguedad" class="control-label">ANTIGUEDAD:</label>
                    <input type="text" name="antiguedad" id="antiguedad" class="form-control" autocomplete="off">
                </div>
                <div class="form-group col-md-8">
                    <label for="direccion_empresa" class="control-label">DIRECCIÓN:</label>
                    <input type="text" name="direccion_empresa" id="direccion_empresa" class="form-control" autocomplete="off">
                </div>
            </div>

            <!--botones de enviar y retroceder-->
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" >Modificar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
