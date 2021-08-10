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
        <form method="POST" id="sid_registro_modificacion" action="{{ route('sid.modificar', ['idAspirante' => base64_encode($alumno->id) ]) }}">
            @csrf
            {{-- @method('PUT') --}}
            <div class="form-row">
                <!-- apellido paterno -->
                <div class="form-group col-md-3">
                    <label for="apellidoPaterno" class="control-label">APELLIDO PATERNO:</label>
                    <input type="text" class="form-control" name="apellido_pat_mod" id="apellido_pat_mod" value="{{$alumno->apellido_paterno}}">
                </div>
                <!-- apellido paterno END -->
                <!-- apellido materno-->
                <div class="form-group col-md-3">
                    <label for="apellidoMaterno" class="control-label">APELLIDO MATERNO:</label>
                    <input type="text" name="apellido_mat_mod" id="apellido_mat_mod" class="form-control" value="{{$alumno->apellido_materno}}">
                </div>
                <!-- apellido materno END-->
                <!--nombre aspirante-->
                <div class="form-group col-md-3">
                    <label for="nombre " class="control-label">NOMBRE:</label>
                    <input type="text" class="form-control" name="nombre_alum_mod" id="nombre_alum_mod" value="{{$alumno->nombre}}">
                </div>
                <!--nombre aspirante END-->
                <div class="form-group col-md-3">
                    <label for="sexo_mod" class="control-label">GENERO</label>
                    <select class="form-control" id="sexo_mod" name="sexo_mod">
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
                    <label for="dia_mod" class="control-label">DÍA</label>
                    <select class="form-control" id="dia_mod" name="dia_mod">
                        <option value="">--SELECCIONAR--</option>
                        @for ($i = 01; $i <= 31; $i++)
                        <option {{ ($dia_nac == $i) ? "selected" : ""  }}  value="{{$i}}">{{$i}}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="mes_mod" class="control-label">MES</label>
                    <select class="form-control" id="mes_mod" name="mes_mod">
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
                    <label for="anio_mod" class="control-label">AÑO</label>
                    <input type="text" class="form-control" id="anio_mod" name="anio_mod" value="{{$anio_nac}}" placeholder="INGRESA EL AÑO EJ. 1943" autocomplete="off">
                </div>
            </div>
            <div class="form-row">

                <div class="form-group col-md-3">
                    <label for="telefono_mod" class="control-label">TELÉFONO:</label>
                    <input type="text" name="telefono_mod" class="form-control" id="telefono_mod" value="{{$alumno->telefono}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="codigo_postal_mod" class="control-label">C.P.</label>
                    <input type="text" name="codigo_postal_mod" id="codigo_postal_mod" class="form-control" value="{{$alumno->cp}}">
                </div>
            </div>
            <div class="form-row">

                <div class="form-group col-md-3">
                    <label for="estado_mod" class="control-label">ESTADO:</label>
                    <select class="form-control" id="estado_mod" name="estado_mod">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($estados as $itemEstado)
                            <option {{ (trim($alumno->estado) == trim($itemEstado->nombre)) ? "selected" : "" }} value="{{$itemEstado->id}}">{{ $itemEstado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="municipio_mod" class="control-label">MUNICIPIO:</label>
                    <select class="form-control" id="municipio_mod" name="municipio_mod">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($municipios as $itemMunicipio)
                            <option {{ ($alumno->municipio == $itemMunicipio->muni) ? "selected" : ""  }} value="{{$itemMunicipio->muni}}">{{ $itemMunicipio->muni }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="estado_civil_mod" class="control-label">ESTADO CIVIL</label>
                    <select class="form-control" id="estado_civil_mod" name="estado_civil_mod">
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
                    <label for="discapacidad_mod" class="control-label">DISCAPACIDAD QUE PRESENTA</label>
                    <select class="form-control" id="discapacidad_mod" name="discapacidad_mod">
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
                    <label for="domicilio_mod" class="control-label">DOMICILIO:</label>
                    <input type="text" class="form-control" name="domicilio_mod" id="domicilio_mod" autocomplete="off" value="{{$alumno->domicilio}}">
                </div>
                <!-- domicilio END -->
                <div class="form-group col-md-6">
                    <label for="colonia_mod" class="control-label">COLONIA O LOCALIDAD:</label>
                    <input type="text" class="form-control" name="colonia_mod" id="colonia_mod" autocomplete="off" value="{{$alumno->colonia}}">
                </div>
            </div>
            <!---->
            <hr style="border-color:dimgray">
            <div style="text-align: center;">
                <h4><b>DATOS GENERALES DE CAPACITACIÓN</b></h4>
            </div>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="ultimo_grado_estudios_mod" class="control-label">ÚLTIMO GRADO DE ESTUDIOS:</label>
                    <select class="form-control" id="ultimo_grado_estudios_mod" name="ultimo_grado_estudios_mod">
                        <option value="">--SELECCIONAR--</option>
                    @foreach ($grado_estudio as $itemGradoEstudio => $val)
                        <option {{( $alumno->ultimo_grado_estudios == $val) ? "selected" : "" }} value="{{$val}}">{{$val}}</option>
                    @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="medio_entero_mod" class="control-label">MEDIO POR EL QUE SE ENTERÓ DEL SISTEMA</label>
                    <select class="form-control" id="medio_entero_mod" name="medio_entero_mod">
                        <option value="">--SELECCIONAR--</option>
                        <option {{( $alumno->medio_entero == "PRENSA") ? "selected" : "" }} value="PRENSA">PRENSA</option>
                        <option {{( $alumno->medio_entero == "RADIO") ? "selected" : "" }} value="RADIO">RADIO</option>
                        <option {{( $alumno->medio_entero == "TELEVISIÓN") ? "selected" : "" }} value="TELEVISIÓN">TELEVISIÓN</option>
                        <option {{( $alumno->medio_entero == "INTERNET") ? "selected" : "" }} value="INTERNET">INTERNET</option>
                        <option {{( $alumno->medio_entero == "FOLLETOS, CARTELES, VOLANTES") ? "selected" : "" }} value="FOLLETOS, CARTELES, VOLANTES">FOLLETOS, CARTELES, VOLANTES</option>
                        <option {{( $alumno->medio_entero == "PRENSA" || $alumno->medio_entero == "RADIO" || $alumno->medio_entero == "TELEVISIÓN" || $alumno->medio_entero == "INTERNET" || $alumno->medio_entero == "FOLLETOS, CARTELES, VOLANTES") ? "" : "selected" }} value="0">OTRO</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    @switch($alumno->medio_entero)
                        @case("PRENSA")
                        <label for="medio_entero_especificar_mod" class="control-label">ESPECIFIQUE</label>
                        <input type="text" disabled class="form-control" name="medio_entero_especificar_mod" id="medio_entero_especificar_mod">
                            @break
                        @case("RADIO")
                        <label for="medio_entero_especificar_mod" class="control-label">ESPECIFIQUE</label>
                        <input type="text" disabled class="form-control" name="medio_entero_especificar_mod" id="medio_entero_especificar_mod">
                            @break
                        @case("TELEVISIÓN")
                        <label for="medio_entero_especificar_mod" class="control-label">ESPECIFIQUE</label>
                        <input type="text" disabled class="form-control" name="medio_entero_especificar_mod" id="medio_entero_especificar_mod">
                            @break
                        @case("INTERNET")
                        <label for="medio_entero_especificar_mod" class="control-label">ESPECIFIQUE</label>
                        <input type="text" disabled class="form-control" name="medio_entero_especificar_mod" id="medio_entero_especificar_mod">
                            @break
                        @case("FOLLETOS, CARTELES, VOLANTES")
                        <label for="medio_entero_especificar_mod" class="control-label">ESPECIFIQUE</label>
                        <input type="text" disabled class="form-control" name="medio_entero_especificar_mod" id="medio_entero_especificar_mod">
                            @break
                        @default
                        <label for="medio_entero_especificar_mod" class="control-label">ESPECIFIQUE</label>
                        <input type="text" class="form-control" value="{{$alumno->medio_entero}}" name="medio_entero_especificar_mod" id="medio_entero_especificar_mod">
                    @endswitch

                </div>
            </div>
            <!--modificaciones-->
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="motivos_eleccion_sistema_capacitacion_mod" class="control-label">MOTIVOS DE ELECCIÓN DEL SISTEMA DE CAPACITACIÓN:</label>
                    <select class="form-control" name="motivos_eleccion_sistema_capacitacion_mod" id="motivos_eleccion_sistema_capacitacion_mod">
                        <option value="">--SELECCIONAR--</option>
                        <option {{( $alumno->sistema_capacitacion_especificar == "EMPLEARSE O AUTOEMPLEARSE") ? "selected" : "" }} value="EMPLEARSE O AUTOEMPLEARSE">PARA EMPLEARSE O AUTOEMPLEARSE</option>
                        <option {{( $alumno->sistema_capacitacion_especificar == "AHORRAR GASTOS AL INGRESO FAMILIAR") ? "selected" : "" }} value="AHORRAR GASTOS AL INGRESO FAMILIAR">PARA AHORRAR GASTOS AL INGRESO FAMILIAR</option>
                        <option {{( $alumno->sistema_capacitacion_especificar == "ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA") ? "selected" : "" }} value="ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA">POR ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA</option>
                        <option {{( $alumno->sistema_capacitacion_especificar == "PARA MEJORAR SU SITUACIÓN EN EL TRABAJO") ? "selected" : "" }} value="PARA MEJORAR SU SITUACIÓN EN EL TRABAJO">PARA MEJORAR SU SITUACIÓN EN EL TRABAJO</option>
                        <option {{( $alumno->sistema_capacitacion_especificar == "POR DISPOSICIÓN DE TIEMPO LIBRE") ? "selected" : "" }} value="POR DISPOSICIÓN DE TIEMPO LIBRE">POR DISPOSICIÓN DE TIEMPO LIBRE</option>
                        <option {{( $alumno->sistema_capacitacion_especificar == "EMPLEARSE O AUTOEMPLEARSE" || $alumno->sistema_capacitacion_especificar == "AHORRAR GASTOS AL INGRESO FAMILIAR" || $alumno->sistema_capacitacion_especificar == "ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA" || $alumno->sistema_capacitacion_especificar == "PARA MEJORAR SU SITUACIÓN EN EL TRABAJO" || $alumno->sistema_capacitacion_especificar == "POR DISPOSICIÓN DE TIEMPO LIBRE") ? "" : "selected" }} value="0">OTRO</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                   @switch($alumno->sistema_capacitacion_especificar)
                        @case("EMPLEARSE O AUTOEMPLEARSE")
                        <div class="capacitacion_especificar_mod">
                            <label for="sistema_capacitacion_especificar_mod" class="control-label">ESPECIFIQUE:</label>
                            <input type="text" disabled class="form-control" name="sistema_capacitacion_especificar_mod" id="sistema_capacitacion_especificar_mod">
                        </div>
                            @break
                        @case("AHORRAR GASTOS AL INGRESO FAMILIAR")
                        <div class="capacitacion_especificar_mod">
                            <label for="sistema_capacitacion_especificar_mod" class="control-label">ESPECIFIQUE:</label>
                            <input type="text" disabled class="form-control" name="sistema_capacitacion_especificar_mod" id="sistema_capacitacion_especificar_mod">
                        </div>
                            @break
                        @case("ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA")
                        <div class="capacitacion_especificar_mod">
                            <label for="sistema_capacitacion_especificar_mod" class="control-label">ESPECIFIQUE:</label>
                            <input type="text" disabled class="form-control" name="sistema_capacitacion_especificar_mod" id="sistema_capacitacion_especificar_mod">
                        </div>
                            @break
                        @case("PARA MEJORAR SU SITUACIÓN EN EL TRABAJO")
                        <div class="capacitacion_especificar_mod">
                            <label for="sistema_capacitacion_especificar_mod" class="control-label">ESPECIFIQUE:</label>
                            <input type="text" disabled class="form-control" name="sistema_capacitacion_especificar_mod" id="sistema_capacitacion_especificar_mod">
                        </div>
                            @break
                        @case("POR DISPOSICIÓN DE TIEMPO LIBRE")
                        <div class="capacitacion_especificar_mod">
                            <label for="sistema_capacitacion_especificar_mod" class="control-label">ESPECIFIQUE:</label>
                            <input type="text" disabled class="form-control" name="sistema_capacitacion_especificar_mod" id="sistema_capacitacion_especificar_mod">
                        </div>
                            @break
                        @default
                        <div class="capacitacion_especificar_mod">
                            <label for="sistema_capacitacion_especificar_mod" class="control-label">ESPECIFIQUE:</label>
                            <input type="text" class="form-control" value="{{$alumno->sistema_capacitacion_especificar}}" name="sistema_capacitacion_especificar_mod" id="sistema_capacitacion_especificar_mod">
                        </div>
                    @endswitch
                </div>
            </div>

            <!--DATOS DE EMPLEO-->
            <hr style="border-color: dimgray">
            <div style="text-align: center;">
                <h4><b>DATOS DE EMPLEO</b></h4>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="empresa_mod" class="control-label">EMPRESA DONDE TRABAJA:</label>
                    <input type="text" name="empresa_mod" id="empresa_mod" class="form-control" value="{{$alumno->empresa_trabaja}}" autocomplete="off">
                </div>
                <div class="form-group col-md-6">
                    <label for="puesto_empresa_mod" class="control-label">PUESTO:</label>
                    <input type="text" name="puesto_empresa_mod" id="puesto_empresa_mod" value="{{$alumno->puesto_empresa}}" class="form-control" autocomplete="off">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="antiguedad_mod" class="control-label">ANTIGUEDAD:</label>
                    <input type="text" name="antiguedad_mod" id="antiguedad_mod" class="form-control" value="{{$alumno->antiguedad}}" autocomplete="off">
                </div>
                <div class="form-group col-md-8">
                    <label for="direccion_empresa_mod" class="control-label">DIRECCIÓN:</label>
                    <input type="text" name="direccion_empresa_mod" id="direccion_empresa_mod" class="form-control" value="{{$alumno->direccion_empresa}}" autocomplete="off">
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
            <input type="hidden" value="{{$alumno->curp}}" id="curp_alumno" name="curp_alumno">
        </form>
    </div>
@endsection
