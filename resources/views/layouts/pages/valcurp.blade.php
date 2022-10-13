{{-- AGC --}}
@extends('theme.sivyc.layout')
@section('title', 'Solicitud de Inscripción | SIVyC Icatech')
@section('content')
    <style>
        #resultado {
            background-color: red;
            color: white;
            font-weight: bold;
        }

        #resultado.ok {
            background-color: green;
        }

        #resultado.white {
            background-color: white;
        }

        #resultado2 {
            background-color: red;
            color: white;
            font-weight: bold;
        }

        #resultado2.ok {
            background-color: green;
        }

        #resultado2.white {
            background-color: white;
        }

    </style>
    <div class="card card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> <br>
        @endif
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif {{-- busqueda de curp --}}
        <form method="POST" id="cacahuate" action="{{ route('alumnos.csid') }}" name="cacahuate">
            {{ csrf_field() }}
            <div class="row justify-content-center">
                <div class="for row">
                    <div class="form-group col-md-6">
                        <input type="text" oninput="validarInput(this)" name="curp_val" class="form-control" id="curp_val" placeholder="CURP" autocomplete="off">
                        <br>
                        <pre id="resultado" name='resultado'></pre>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="submit" value="NUEVO" class="btn btn-success" id="nuevo" name="nuevo">
                    </div>
                    <div class="form-group col-md-3">
                        <div class="pull-left">
                            <a class="btn btn-danger" href="{{ route('alumnos.index') }}">Regresar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @if ($curp != '')
            @if ($a == false) {{-- inscricion de aspirante --}}
                <form method="POST" id="form_sid" action="{{ route('alumnos.save') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="col-lg-12 margin-tb">
                        <hr style="border-color:dimgray">
                        <h3><b>Solicitud de Inscripción (SID)</b></h3>
                        <hr style="border-color:dimgray">
                        <div align="center">
                            <h4><b>DATOS PERSONALES</b></h4>
                        </div> <br><br>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <table class="table table-striped">
                                    <tr>
                                        <td>
                                            <span><i class="fa fa-camera-retro fa-3x" style="vertical-align: middle"></i></span>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="fotografia"
                                                    name="fotografia" onchange="fileValidation()">
                                                <label class="custom-file-label" for="fotografia">Fotografía</label>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="curp" class="control-label">Curp Aspirante</label>
                                {{ Form::text('curp', $curp, ['id' => 'curp', 'class' => 'form-control', 'placeholder' => 'CURP', 'readonly' => 'true']) }}
                            </div>
                        </div>
                        <div class="form-row">
                            <!--nombre aspirante-->
                            <div class="form-group col-md-3">
                                <label for="nombre " class="control-label">Nombre del Aspirante</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" autocomplete="off">
                            </div>
                            <!--nombre aspirante END-->
                            <!-- apellido paterno -->
                            <div class="form-group col-md-3">
                                <label for="apellidoPaterno" class="control-label">Apellido Paterno</label>
                                <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno"
                                    autocomplete="off">
                            </div>
                            <!-- apellido paterno END -->
                            <!-- apellido materno-->
                            <div class="form-group col-md-3">
                                <label for="apellidoMaterno" class="control-label">Apellido Materno</label>
                                <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno"
                                    autocomplete="off">
                            </div>
                            <!-- apellido materno END-->
                            @php $ka= date('Y-m-d',strtotime($fecha_t)); @endphp
                            <div class="form-group col-md-3">
                                <label for="">Fecha de nacimiento</label>
                                <input type="date" readonly name="fecha" id="fecha" class="form-control"
                                    value="{{ $ka }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="sexo" class="control-label">Genero:</label>
                                <select class="form-control" id="sexo" name="sexo" aria-readonly="true">
                                    @if ($sexo == 'M')
                                        <option value="FEMENINO">MUJER</option>
                                    @endif
                                    @if ($sexo == 'H')
                                        <option value="MASCULINO">HOMBRE</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Nacionalidad:</label>
                                <input id="nacionalidad" name="nacionalidad" class="form-control" type="text" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Tel&eacute;fono Casa:</label>
                                <input id="telefono_casa" name="telefono_casa" class="form-control" type="text" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Tel&eacute;fono Celular:</label>
                                <input id="telefono_cel" name="telefono_cel" class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Correo Electr&oacute;nico:</label>
                                <input type="email" id="correo" name="correo" class="form-control"
                                    placeholder="usuario@gmail.com" type="text" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Facebook:</label>
                                <input id="facebook" name="facebook" class="form-control" type="text" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Twitter:</label>
                                <input id="twitter" name="twitter" class="form-control" type="text" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Instagram:</label>
                                <input id="instagram" name="instagram" class="form-control" type="text" />
                            </div>
                            <div class="form-group col-md-3">
                                <label>TikTok:</label>
                                <input id="tiktok" name="tiktok" class="form-control" type="text" />
                            </div>
                            <div class="form-group col-md-3">
                                <br />
                                <label><input id="ninguna_redsocial" name="ninguna_redsocial" type="checkbox"
                                        value="true">&nbsp;&nbsp;No tiene redes sociales</label>
                            </div>
                            <div class="form-group col-md-3">
                                <br />
                                <label><input id="recibir_publicaciones" name="recibir_publicaciones" type="checkbox"
                                        value="true">&nbsp;&nbsp;¿Recibir publicaciones?</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Estado civil:</label>
                                <select class="form-control mr-sm--2" id="estado_civil" name="estado_civil" required>
                                    <option value="">--SELECCIONAR--</option>
                                    @foreach ($estado_civil as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{--<div class="form-group col-md-3">
                                <label>Discapacidad:</label>
                                {{ Form::select('discapacidad', $discapacidad, null, ['id' => 'discapacidad', 'class' => 'form-control mr-sm-2', 'placeholder' => '--SELECCIONAR--']) }}
                            </div>--}}
                            <!-- domicilio -->
                            <div class="form-group col-md-3">
                                <label for="domicilio" class="control-label">Domicilio</label>
                                <input type="text" class="form-control" id="domicilio" name="domicilio"
                                    autocomplete="off">
                            </div>
                            <!-- domicilio END -->
                            <div class="form-group col-md-3">
                                <label for="colonia" class="control-label">Colonia</label>
                                <input type="text" class="form-control" id="colonia" name="colonia" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="estado" class="control-label">Estado</label>
                                <select class="form-control" id="estado" name="estado" required>
                                    <option value="">Seleccione un estado</option>
                                    @foreach ($estados as $id => $nombre)
                                        <option value="{{ $nombre }}">{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="municipio" class="control-label">Municipio</label>
                                <select class="form-control" id="municipio" name="municipio">
                                    <option value="">Seleccione un municipio</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="localidad" class="control-label">Localidad</label>
                                <select class="form-control" id="localidad" name="localidad">
                                    <option value="">Seleccione una localidad</option>
                                </select>
                            </div>

                            <!--COLONIA END-->
                            <div class="form-group col-md-3">
                                <label for="cp" class="control-label">Código Postal</label>
                                <input type="text" class="form-control" id="cp" name="cp" autocomplete="off">
                            </div>
                        </div>
                        <!--formulario-->
                        <br>
                        <div class="form-row">
                            <div class="form-group col">
                                <div class="custom-control custom-checkbox">
                                    <input value="true" type="checkbox" class="custom-control-input" id="lgbt" name="lgbt">
                                    <label class="custom-control-label" for="lgbt">LGBTTTI+</label>
                                </div>
                            </div>

                            <div class="form-group col">
                                <label><input id="madre_soltera" name="madre_soltera" type="checkbox"
                                        value="true" />&nbsp;&nbsp;¿Es Madre Soltera?</label>
                            </div>
                            <div class="form-group col">
                                <label><input id="familia_migrante" name="familia_migrante" type="checkbox"
                                        value="true" />&nbsp;&nbsp;¿Tiene Familia Migrante?</label>
                            </div>
                            <div class="form-group col">
                                <label><input id="inmigrante" name="inmigrante" type="checkbox" value="true">&nbsp;&nbsp;¿Es
                                    Inmigrante?</label>
                            </div>
                            {{--<div class="form-group col">
                                <label>
                                    <input id="indigena" name="indigena" type="checkbox" value="true" />&nbsp;&nbsp¿Es
                                    Indígena?
                                </label>
                            </div>--}}
                            <div class="form-group col">
                                {{ Form::select('etnia', $etnia, null, ['id' => 'etnia', 'class' => 'form-control mr-sm-2', 'placeholder' => '--ETNIA--']) }}
                            </div>
                        </div>
                        <br>
                        <div>
                            <div class="form-row">
                                <label>&nbsp;&nbsp;¿El aspirante pertenece a algún Grupo Vulnerable?</label>
                            </div>
                            <br>
                            <div class="form-row">
                                    @foreach ($gvulnerable as $item)
                                    <div class="form-group col-md-4">
                                        <input type="checkbox" name="itemEdith[{{$item->grupo}}]" value="{{$item->id}}">&nbsp;&nbsp;{{$item->grupo}}</input>
                                    </div>
                                    @endforeach
                            </div>
                        </div>
                        <hr style="border-color:dimgray">
                        <div style="text-align: center;">
                            <h4><b>DATOS GENERALES DE CAPACITACIÓN</b></h4>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="ultimo_grado_estudios" class="control-label">ÚLTIMO GRADO DE ESTUDIOS:</label>
                                <select class="form-control" id="ultimo_grado_estudios" name="ultimo_grado_estudios">
                                    <option value="">--SELECCIONAR--</option>
                                    @foreach ($grado_estudio as $itemGradoEstudio => $val)
                                        <option value="{{ $val }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="medio_entero" class="control-label">MEDIO POR EL QUE SE ENTERÓ DEL
                                    SISTEMA</label>
                                <select class="form-control" id="medio_entero" name="medio_entero">
                                    <option value="">--SELECCIONAR--</option>
                                    <option value="PRENSA">PRENSA</option>
                                    <option value="RADIO">RADIO</option>
                                    <option value="TELEVISIÓN">TELEVISIÓN</option>
                                    <option value="INTERNET">INTERNET</option>
                                    <option value="FOLLETOS, CARTELES, VOLANTES">FOLLETOS, CARTELES, VOLANTES</option>
                                    <option value="0">OTRO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="medio_especificar">
                                    <label for="medio_entero_especificar" class="control-label">ESPECIFIQUE</label>
                                    <input type="text" class="form-control" name="medio_entero_especificar"
                                        id="medio_entero_especificar">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="motivos_eleccion_sistema_capacitacion" class="control-label">MOTIVOS DE
                                    ELECCIÓN DEL SISTEMA DE CAPACITACIÓN:</label>
                                <select class="form-control" name="motivos_eleccion_sistema_capacitacion"
                                    id="motivos_eleccion_sistema_capacitacion">
                                    <option value="">--SELECCIONAR--</option>
                                    <option value="PARA EMPLEARSE O AUTOEMPLEARSE">PARA EMPLEARSE O AUTOEMPLEARSE</option>
                                    <option value="PARA AHORRAR GASTOS AL INGRESO FAMILIAR">PARA AHORRAR GASTOS AL INGRESO
                                        FAMILIAR</option>
                                    <option value="POR ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA">POR
                                        ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA</option>
                                    <option value="PARA MEJORAR SU SITUACIÓN EN EL TRABAJO">PARA MEJORAR SU SITUACIÓN EN EL
                                        TRABAJO</option>
                                    <option value="POR DISPOSICIÓN DE TIEMPO LIBRE">POR DISPOSICIÓN DE TIEMPO LIBRE</option>
                                    <option value="0">OTRO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="capacitacion_especificar">
                                    <label for="sistema_capacitacion_especificar"
                                        class="control-label">ESPECIFIQUE:</label>
                                    <input type="text" class="form-control" name="sistema_capacitacion_especificar"
                                        id="sistema_capacitacion_especificar">
                                </div>
                            </div>
                        </div>
                        <!--DATOS DE EMPLEO-->
                        <hr style="border-color: dimgray">
                        <div style="text-align: center;">
                            <h4><b>DATOS DE EMPLEO</b></h4>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label><input type="checkbox" id="trabajo" name="trabajo" value="true"
                                        onchange="javascript:showContent()">&nbsp;&nbsp;¿El aspirante está empleado?</label>
                            </div>
                            {{-- <div class="form-group col-md-4">
                                <label><input type="checkbox" id="funcionario" name="funcionario" value="true">&nbsp;&nbsp;¿El aspirante es un servidor público?</label>
                            </div> --}}
                        </div>
                        <div id="content" style="display: none;">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="empresa" class="control-label">EMPRESA DONDE TRABAJA:</label>
                                    <input type="text" name="empresa" id="empresa" class="form-control"
                                        autocomplete="off">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="puesto_empresa" class="control-label">PUESTO:</label>
                                    <input type="text" name="puesto_empresa" id="puesto_empresa" class="form-control"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="antiguedad" class="control-label">ANTIGUEDAD:</label>
                                    <input type="text" name="antiguedad" id="antiguedad" class="form-control"
                                        autocomplete="off">
                                </div>
                                <div class="form-group col-md-8">
                                    <label for="direccion_empresa" class="control-label">DIRECCIÓN:</label>
                                    <input type="text" name="direccion_empresa" id="direccion_empresa"
                                        class="form-control" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <hr style="border-color: dimgray">
                        <h5><b>REQUISITOS</b></h5>
                        <hr />
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <table class="table table-striped"
                                    style="width: 100%; text-align: left; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 20%;"><label><input id="chk_acta" name="chk_acta" type="checkbox"
                                                    value="true" />&nbsp;&nbsp;Acta de Nacimiento</label> </td>
                                        <td>
                                            <div>
                                                <label for="">FECHA DE EXPEDICIÓN ACTA DE NACIMIENTO&nbsp;&nbsp;</label>
                                                <input type="date" name="fecha_expedicion_acta_nacimiento"
                                                    id="fecha_expedicion_acta_nacimiento">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label><input id="chk_curp" name="chk_curp" type="checkbox"
                                                    value="true" />&nbsp;&nbsp;CURP</label></td>
                                        <td>
                                            <div>
                                                <label for="">FECHA DE EXPEDICIÓN CURP</label>
                                                <input type="date" name="fecha_expedicion_curp" id="fecha_expedicion_curp">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label><input id="chk_escolaridad" name="chk_escolaridad" type="checkbox"
                                                    value="true" />&nbsp;&nbsp;&Uacute;ltimo Grado de Estudios</label></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><label><input id="chk_comprobante_migratorio" name="chk_comprobante_migratorio"
                                                    type="checkbox" value="true" />&nbsp;&nbsp;Comprobante
                                                Migratorio</label></td>
                                        <td>
                                            <div>
                                                <label for="">FECHA DE VIGENCIA DE COMPROBANTE MIGRATORIO</label>
                                                <input type="date" name="fecha_vigencia_migratorio"
                                                    id="fecha_vigencia_migratorio">
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="form-group col-md-4">
                                <table class="table table-striped"
                                    style="width: 100%; text-align: left; border-collapse: collapse;">
                                    <th>
                                    <td>
                                        <label for="">Carga de PDF con documentos</label><br>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFile" name="customFile"
                                                onchange="fileValidationpdf()">
                                            <label class="custom-file-label" for="customFile">SELECCIONAR DOCUMENTO</label>
                                        </div>
                                    </td>
                                    <td>
                                        <a class="btn btn-dark-green" href="https://www.ilovepdf.com/es/unir_pdf"
                                            target="blank">UNIR PDF´s</a>
                                    </td>
                                    </th>
                                </table>
                            </div>
                        </div>
                        <hr style="border-color: dimgray">
                        {{-- datos cerss --}}
                        <h5><b>DATOS CERSS</b></h5>
                        <div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label><input type="checkbox" id="cerss_chk" name="cerss_chk" value="true"
                                            onchange="javascript:showContent()">&nbsp;&nbsp;¿El aspirante pertenece a algún
                                        cereso?</label>
                                </div>
                            </div>
                            <div id="datos_cerss" style="display: none;">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        {{ Form::text('num_expediente_cerss', null, ['id' => 'num_expediente_cerss', 'class' => 'form-control', 'placeholder' => 'NÚMERO DE EXPEDIENTE']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr style="border-color: dimgray">
                        <!--botones de enviar y retroceder-->
                        <div class="row">
                            <div class="col-lg-12 margin-tb">
                                <div class="pull-right">
                                    <button type="submit" class="btn btn-primary">Guardar Registro</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            @elseif ($a == true) {{-- modificacion aspirante jefe de vinculacion --}}
                @if ($rol == 'unidad_vinculacion' || $rol == 'admin' || $rol == 'vinculadores_administrativo')
                    <form method="POST" id="sid_registro_modificacion" action="{{ route('sid.modificar', ['idAspirante' => base64_encode($alumno->id)]) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="col-lg-12 margin-tb">
                            <hr style="border-color:dimgray">
                            <div>
                                <div>
                                    <h3><b>MODIFICAR ASPIRANTES</b></h3>
                                </div>
                            </div>
                            <hr style="border-color:dimgray">
                            <div align="center">
                                <h4><b>DATOS PERSONALES</b></h4>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <table class="table table-striped">
                                        <tr>
                                            <td>
                                                <span><i class="fa fa-camera-retro fa-3x"
                                                        style="vertical-align: middle"></i></span>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="fotografia_mod"
                                                        name="fotografia_mod" onchange="fileValidationmod()">
                                                    <label class="custom-file-label" for="fotografia_mod">SELECCIONAR
                                                        FOTOGRAFIA</label>
                                                </div>
                                            </td>
                                            @if (isset($alumno->fotografia))
                                                <td>
                                                    <a name="fotografia_url_mod" id="doc_url_mod"
                                                        href="{{ $alumno->fotografia }}" target="blank"
                                                        class="btn btn-primary">IMAGEN PRECARGADA</a>
                                                </td>
                                            @endif
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="form-row">
                                @if ($rol == 'admin')
                                    <div class="form-group col-md-4">
                                        <label for="curp_mod" class="control-label">Curp Aspirante</label>
                                        {{ Form::text('curp_mod', $alumno->curp, ['id' => 'curp_mod', 'name' => 'curp_mod', 'class' => 'form-control', 'placeholder' => 'CURP', 'oninput' => 'validarInputMod(this)']) }}
                                        <br>
                                        <pre id="resultado2"></pre>
                                    </div>
                                @else
                                    <div class="form-group col-md-4">
                                        <label for="curp_mod" class="control-label">Curp Aspirante</label>
                                        {{ Form::text('curp_mod', $alumno->curp, ['id' => 'curp_mod', 'name' => 'curp_mod', 'class' => 'form-control', 'placeholder' => 'CURP', 'oninput' => 'validarInputMod(this)', 'readonly' => 'true']) }}
                                        <br>
                                        <pre id="resultado2"></pre>
                                    </div>
                                @endif
                            </div>
                            <div class="form-row">
                                <!--nombre aspirante-->
                                <div class="form-group col-md-3">
                                    <label for="nombre_mod " class="control-label">Nombre del Aspirante</label>
                                    <input type="text" class="form-control" id="nombre_mod" name="nombre_mod"
                                        autocomplete="off" value="{{ $alumno->nombre }}">
                                </div>
                                <!--nombre aspirante END-->
                                <!-- apellido paterno -->
                                <div class="form-group col-md-3">
                                    <label for="apellidoPaterno_mod" class="control-label">Apellido Paterno</label>
                                    <input type="text" class="form-control" id="apellidoPaterno_mod"
                                        name="apellidoPaterno_mod" autocomplete="off"
                                        value="{{ $alumno->apellido_paterno }}">
                                </div>
                                <!-- apellido paterno END -->
                                <!-- apellido materno-->
                                <div class="form-group col-md-3">
                                    <label for="apellidoMaterno_mod" class="control-label">Apellido Materno</label>
                                    <input type="text" class="form-control" id="apellidoMaterno_mod"
                                        name="apellidoMaterno_mod" autocomplete="off"
                                        value="{{ $alumno->apellido_materno }}">
                                </div>
                                <!-- apellido materno END-->
                                <div class="form-group col-md-3">
                                    <label for="">Fecha de nacimiento</label>
                                    @if ($rol == 'admin')
                                        <input type="date" name="fecha_nacimiento_mod" id="fecha_nacimiento_mod"
                                            class="form-control" value="{{ $alumno->fecha_nacimiento }}">
                                    @else
                                        <input type="date" name="fecha_nacimiento_mod" id="fecha_nacimiento_mod"
                                            class="form-control" value="{{ $alumno->fecha_nacimiento }}" readonly>
                                    @endif
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="sexo_mod" class="control-label">Genero</label>
                                    @if ($rol == 'admin')
                                        @if ($alumno->sexo == 'MASCULINO')
                                            <input type="text" class="form-control" id="sexo_mod" name="sexo_mod"
                                                value="HOMBRE">
                                        @elseif ($alumno->sexo=='FEMENINO')
                                            <input type="text" class="form-control" id="sexo_mod" name="sexo_mod"
                                                value="MUJER">
                                        @else
                                            <input type="text" class="form-control" id="sexo_mod" name="sexo_mod"
                                                value="{{ $alumno->sexo }}">
                                        @endif
                                    @else
                                        @if ($alumno->sexo == 'MASCULINO')
                                            <input type="text" class="form-control" id="sexo_mod" name="sexo_mod"
                                                value="HOMBRE" readonly>
                                        @elseif ($alumno->sexo=='FEMENINO')
                                            <input type="text" class="form-control" id="sexo_mod" name="sexo_mod"
                                                value="MUJER" readonly>
                                        @else
                                            <input type="text" class="form-control" id="sexo_mod" name="sexo_mod"
                                                value="{{ $alumno->sexo }}" readonly>
                                        @endif
                                    @endif
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Nacionalidad:</label>
                                    <input id="nacionalidad_mod" name="nacionalidad_mod" class="form-control" type="text"
                                        value="{{ $alumno->nacionalidad }}" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Tel&eacute;fono Casa:</label>
                                    <input id="telefono_casa_mod" name="telefono_casa_mod" class="form-control"
                                        type="text" value="{{ $alumno->telefono_casa }}" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Tel&eacute;fono Celular:</label>
                                    <input id="telefono_cel_mod" name="telefono_cel_mod" class="form-control" type="text"
                                        value="{{ $alumno->telefono_personal }}" />
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Correo Electr&oacute;nico:</label>
                                    <input type="email" id="correo_mod" name="correo_mod" class="form-control"
                                        placeholder="usuario@gmail.com" type="text" value="{{ $alumno->correo }}" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Facebook:</label>
                                    <input id="facebook_mod" name="facebook_mod" class="form-control" type="text"
                                        value="{{ $alumno->facebook }}" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Twitter:</label>
                                    <input id="twitter_mod" name="twitter_mod" class="form-control" type="text"
                                        value="{{ $alumno->twitter }}" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Instagram:</label>
                                    <input id="instagram_mod" name="instagram_mod" class="form-control" type="text" value="{{ $alumno->instagram }}" />
                                </div>
                                <div class="form-group col-md-3">
                                    <label>TikTok:</label>
                                    <input id="tiktok_mod" name="tiktok_mod" class="form-control" type="text" value="{{ $alumno->tiktok }}" />
                                </div>
                                <div class="form-group col-md-3">
                                    <br />
                                    @if ($alumno->ninguna_redsocial == 'true')
                                        <label><input id="ninguna_redsocial_mod" name="ninguna_redsocial_mod" type="checkbox"
                                            value="true" checked>&nbsp;&nbsp;No tiene redes sociales</label>
                                    @else
                                        <label><input id="ninguna_redsocial_mod" name="ninguna_redsocial_mod" type="checkbox"
                                            value="true">&nbsp;&nbsp;No tiene redes sociales</label>
                                    @endif
                                </div>
                                <div class="form-group col-md-3">
                                    <br />
                                    @if ($alumno->recibir_publicaciones == 'true')
                                        <label><input id="recibir_publicaciones_mod" name="recibir_publicaciones_mod"
                                                type="checkbox" value="true" checked>&nbsp;&nbsp;¿Recibir
                                            publicaciones?</label>
                                    @else
                                        <label><input id="recibir_publicaciones_mod" name="recibir_publicaciones_mod"
                                                type="checkbox" value="true">&nbsp;&nbsp;¿Recibir publicaciones?</label>
                                    @endif
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Estado civil:</label>
                                    <select class="form-control mr-sm--2" id="estado_civil_mod" name="estado_civil_mod">
                                        @isset($alumno->estado_civil)<option value="{{ $alumno->estado_civil }}">
                                                {{ $alumno->estado_civil }}@else
                                        <option value="">--SELECCIONAR--</option>@endisset
                                        @foreach ($estado_civil as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{--<div class="form-group col-md-3">
                                    <label>Discapacidad:</label>
                                    <select name="discapacidad_mod" id="discapacidad_mod" class="form-control mr-sm--2">
                                        @isset($alumno->discapacidad)<option value="{{ $alumno->discapacidad }}">
                                                {{ $alumno->discapacidad }}@else
                                        <option value="">--SELECCIONAR--</option>@endisset
                                        @foreach ($discapacidad as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>--}}
                                <!-- domicilio -->
                                <div class="form-group col-md-3">
                                    <label for="domicilio_mod" class="control-label">Domicilio</label>
                                    <input type="text" class="form-control" id="domicilio_mod" name="domicilio_mod"
                                        autocomplete="off" value="{{ $alumno->domicilio }}">
                                </div>
                                <!-- domicilio END -->
                                <div class="form-group col-md-3">
                                    <label for="colonia_mod" class="control-label">Colonia</label>
                                    <input type="text" class="form-control" id="colonia_mod" name="colonia_mod"
                                        autocomplete="off" value="{{ $alumno->colonia }}">
                                </div>
                                <!--COLONIA END-->
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="estados_mod" class="control-label">Estado</label>
                                    <select class="form-control" id="estados_mod" name="estados_mod">
                                        @isset($alumno->estado)<option value="{{ $alumno->estado }}">
                                                {{ $alumno->estado }}@else
                                        <option value="">--SELECCIONAR--</option>@endisset
                                        @foreach ($estados as $id => $nombre)
                                            <option value="{{ $nombre }}">{{ $nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="municipios_mod" class="control-label">Municipio</label>
                                    <select class="form-control" id="municipios_mod" name="municipios_mod">
                                        {{-- @isset($alumno->municipio)<option value="{{ $alumno->municipio }}">
                                                {{ $alumno->municipio }}@else
                                                @endisset
                                        --}}
                                        <option value="">--SELECCIONAR--</option>
                                        @foreach ($municipios as $municipio)
                                            <option {{ $municipio->clave == $alumno->clave_municipio ? 'selected' : '' }} value="{{ $municipio->clave }}">{{ $municipio->muni }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="localidad_mod" class="control-label">Localidad</label>
                                    <select class="form-control" id="localidad_mod" name="localidad_mod">
                                        <option value="">Seleccione una localidad</option>
                                        @foreach ($localidades as $localidad)
                                            <option {{ $localidad->clave == $alumno->clave_localidad ? 'selected' : '' }} value="{{ $localidad->clave }}">{{ $localidad->localidad }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="cp_mod" class="control-label">Código Postal</label>
                                    <input type="text" class="form-control" id="cp_mod" name="cp_mod" autocomplete="off" value="{{ $alumno->cp }}">
                                </div>
                            </div>
                            <!--formulario-->
                            <br>
                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <div class="custom-control custom-checkbox">
                                        <input {{ $alumno->lgbt ? 'checked' : '' }} value="true" type="checkbox" class="custom-control-input" id="lgbt_mod" name="lgbt_mod">
                                        <label class="custom-control-label" for="lgbt_mod">LGBTTTI+</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    @if ($alumno->madre_soltera == 'true')
                                        <label><input id="madre_soltera_mod" name="madre_soltera_mod" type="checkbox"
                                                value="true" checked />&nbsp;&nbsp;¿Es Madre Soltera?</label>
                                    @else
                                        <label><input id="madre_soltera_mod" name="madre_soltera_mod" type="checkbox"
                                                value="true" />&nbsp;&nbsp;¿Es Madre Soltera?</label>
                                    @endif
                                </div>
                                <div class="form-group col-md-2">
                                    @if ($alumno->familia_migrante == 'true')
                                        <label><input id="familia_migrante_mod" name="familia_migrante_mod" type="checkbox"
                                                value="true" checked />&nbsp;&nbsp;¿Tiene Familia Migrante?</label>
                                    @else
                                        <label><input id="familia_migrante_mod" name="familia_migrante_mod" type="checkbox"
                                                value="true" />&nbsp;&nbsp;¿Tiene Familia Migrante?</label>
                                    @endif
                                </div>
                                <div class="form-group col-md-2">
                                    @if ($alumno->inmigrante == 'true')
                                        <label><input id="inmigrante_mod" name="inmigrante_mod" type="checkbox" value="true"
                                                checked>&nbsp;&nbsp;¿Es Inmigrante?</label>
                                    @else
                                        <label><input id="inmigrante_mod" name="inmigrante_mod" type="checkbox"
                                                value="true">&nbsp;&nbsp;¿Es Inmigrante?</label>
                                    @endif
                                </div>
                                {{--<div class="form-group col-md-2">
                                    @if ($alumno->indigena == 'true')
                                        <label><input id="indigena_mod" name="indigena_mod" type="checkbox" value="true" /
                                                checked>&nbsp;&nbsp;¿Es Indígena?</label>
                                    @else
                                        <label><input id="indigena_mod" name="indigena_mod" type="checkbox"
                                                value="true" />&nbsp;&nbsp;¿Es Indígena?</label>
                                    @endif
                                </div>--}}
                                <div class="form-group col-md-2">
                                    <select name="etnia_mod" id="etnia_mod" class="form-control mr-sm--2"
                                        placeholder="--ETNIA--">
                                        @isset($alumno->etnia)@if ($alumno->etnia == '')<option value="">--ETNIA--</option>@else<option value="{{ $alumno->etnia }}">{{ $alumno->etnia }}</option>@endif @else<option value="">--ETNIA--
                                        </option> @endisset
                                        @foreach ($etnia as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div>
                                <div class="form-row">
                                    <label>&nbsp;&nbsp;¿El aspirante pertenece a algún Grupo Vulnerable?</label>
                                </div>
                                <br>
                                <div class="form-row">
                                        @foreach ($gvulnerable as $item)
                                        <div class="form-group col-md-4">
                                            @if ($alumno->id_gvulnerable && in_array($item->id, json_decode($alumno->id_gvulnerable)))
                                            <input checked type="checkbox" name="itemEdith[{{$item->grupo}}]" value="{{$item->id}}">&nbsp;&nbsp;{{$item->grupo}}</input>
                                            @else
                                            <input type="checkbox" name="itemEdith[{{$item->grupo}}]" value="{{$item->id}}">&nbsp;&nbsp;{{$item->grupo}}</input>
                                            @endif
                                        </div>
                                        @endforeach
                                </div>
                            </div>
                            <hr style="border-color:dimgray">
                            <div style="text-align: center;">
                                <h4><b>DATOS GENERALES DE CAPACITACIÓN</b></h4>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="ultimo_grado_estudios_mod" class="control-label">ÚLTIMO GRADO DE
                                        ESTUDIOS:</label>
                                    <select class="form-control" id="ultimo_grado_estudios_mod"
                                        name="ultimo_grado_estudios_mod">
                                        @isset($alumno->ultimo_grado_estudios)<option
                                                value="{{ $alumno->ultimo_grado_estudios }}">
                                            {{ $alumno->ultimo_grado_estudios }}</option>@else<option value="">
                                            --SELECCIONAR--</option>@endisset
                                        @foreach ($grado_estudio as $itemGradoEstudio => $val)
                                            <option value="{{ $val }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="medio_entero_mod" class="control-label">MEDIO POR EL QUE SE ENTERÓ DEL
                                        SISTEMA</label>
                                    <select class="form-control" id="medio_entero_mod" name="medio_entero_mod"
                                            value="{{ $alumno->medio_entero }}">
                                        @isset($alumno->medio_entero)@if ($alumno->medio_entero == '')<option value="">--SELECCIONAR--</option> @elseif ($alumno->medio_entero !='PRENSA'&& $alumno->medio_entero !='RADIO'&&$alumno->medio_entero !='TELEVISIÓN'&&$alumno->medio_entero !='INTERNET'&&$alumno->medio_entero !='FOLLETOS, CARTELES, VOLANTES')<option value="0">OTRO</option> @else<option value="{{ $alumno->medio_entero }}">{{ $alumno->medio_entero }} @endif @else<option value="">
                                            --SELECCIONAR--</option>@endisset
                                        <option value="PRENSA">PRENSA</option>
                                        <option value="RADIO">RADIO</option>
                                        <option value="TELEVISIÓN">TELEVISIÓN</option>
                                        <option value="INTERNET">INTERNET</option>
                                        <option value="FOLLETOS, CARTELES, VOLANTES">FOLLETOS, CARTELES, VOLANTES</option>
                                        <option value="0">OTRO</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <div>
                                        <label for="medio_especificar_mod" class="control-label">ESPECIFIQUE</label>
                                        @if ($alumno->medio_entero == '')<input type="text" class="form-control" name="medio_especificar_mod" id="medio_especificar_mod"> @elseif($alumno->medio_entero !='PRENSA'&& $alumno->medio_entero !='RADIO'&&$alumno->medio_entero !='TELEVISIÓN'&&$alumno->medio_entero !='INTERNET'&&$alumno->medio_entero !='FOLLETOS, CARTELES, VOLANTES')<input type="text" class="form-control" name="medio_especificar_mod" id="medio_especificar_mod" value="{{ $alumno->medio_entero }}"> @else<input type="text" class="form-control" name="medio_especificar_mod" id="medio_especificar_mod"> @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                        <label for="motivos_eleccion_sistema_capacitacion_mod" class="control-label">MOTIVOS
                                            DE ELECCIÓN DEL SISTEMA DE CAPACITACIÓN:</label>
                                        <select class="form-control" name="motivos_eleccion_sistema_capacitacion_mod"
                                            id="motivos_eleccion_sistema_capacitacion_mod">
                                        @isset($alumno->sistema_capacitacion_especificar)@if ($alumno->sistema_capacitacion_especificar != 'PARA EMPLEARSE O AUTOEMPLEARSE' && $alumno->sistema_capacitacion_especificar != 'PARA AHORRAR GASTOS AL INGRESO FAMILIAR' && $alumno->sistema_capacitacion_especificar != 'POR ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA' && $alumno->sistema_capacitacion_especificar != 'PARA MEJORAR SU SITUACIÓN EN EL TRABAJO' && $alumno->sistema_capacitacion_especificar != 'POR DISPOSICIÓN DE TIEMPO LIBRE' && $alumno->sistema_capacitacion_especificar != '')<option value="0">OTRO</option> @else<option value="{{ $alumno->sistema_capacitacion_especificar }}">{{ $alumno->sistema_capacitacion_especificar }} @endif @else
                                        <option value="">--SELECCIONAR--</option>@endisset
                                        <option value="PARA EMPLEARSE O AUTOEMPLEARSE">PARA EMPLEARSE O AUTOEMPLEARSE
                                        </option>
                                        <option value="PARA AHORRAR GASTOS AL INGRESO FAMILIAR">PARA AHORRAR GASTOS AL
                                            INGRESO FAMILIAR</option>
                                        <option value="POR ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA">POR
                                            ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA</option>
                                        <option value="PARA MEJORAR SU SITUACIÓN EN EL TRABAJO">PARA MEJORAR SU SITUACIÓN EN
                                            EL TRABAJO</option>
                                        <option value="POR DISPOSICIÓN DE TIEMPO LIBRE">POR DISPOSICIÓN DE TIEMPO LIBRE
                                        </option>
                                        <option value="0">OTRO</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="motivo_capacitacion_especificar">
                                        <label for="motivo_sistema_capacitacion_especificar_mod"
                                            class="control-label">ESPECIFIQUE:</label>
                                        @if ($alumno->sistema_capacitacion_especificar != 'PARA EMPLEARSE O AUTOEMPLEARSE' && $alumno->sistema_capacitacion_especificar != 'PARA AHORRAR GASTOS AL INGRESO FAMILIAR' && $alumno->sistema_capacitacion_especificar != 'POR ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA' && $alumno->sistema_capacitacion_especificar != 'PARA MEJORAR SU SITUACIÓN EN EL TRABAJO' && $alumno->sistema_capacitacion_especificar != 'POR DISPOSICIÓN DE TIEMPO LIBRE' && $alumno->sistema_capacitacion_especificar != '')<input type="text" class="form-control" name="motivo_sistema_capacitacion_especificar_mod" id="motivo_sistema_capacitacion_especificar_mod" value="{{ $alumno->sistema_capacitacion_especificar }}">@else <input type="text" class="form-control" name="motivo_sistema_capacitacion_especificar_mod" id="motivo_sistema_capacitacion_especificar_mod"> @endif
                                    </div>
                                </div>
                            </div>
                            <!--DATOS DE EMPLEO-->
                            <hr style="border-color: dimgray">
                            <div>
                                <div style="text-align: center;">
                                    <h4><b>DATOS DE EMPLEO</b></h4>
                                </div>
                                <div class="form-row">
                                    @if ($alumno->empleado == true)
                                        <div class="form-group col-md-4">
                                            <label><input type="checkbox" id="trabajo_mod" name="trabajo_mod" value="true"
                                                    checked>&nbsp;&nbsp;¿El aspirante está empleado?</label>
                                        </div>
                                    @else
                                        <div class="form-group col-md-4">
                                            <label><input type="checkbox" id="trabajo_mod" name="trabajo_mod"
                                                    value="true">&nbsp;&nbsp;¿El aspirante está empleado?</label>
                                        </div>
                                    @endif
                                    {{-- <div class="form-group col-md-4">
                                        <label>
                                            <input type="checkbox" id="funcionario_mod" name="funcionario_mod" value="true" @if ($alumno->servidor_publico == true)
                                                checked
                                            @endif>&nbsp;&nbsp;¿El aspirante es un servidor público?
                                        </label>
                                    </div> --}}
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="empresa_mod" class="control-label">EMPRESA DONDE TRABAJA:</label>
                                        <input type="text" name="empresa_mod" id="empresa_mod" class="form-control"
                                            autocomplete="off" value="{{ $alumno->empresa_trabaja }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="puesto_empresa_mod" class="control-label">PUESTO:</label>
                                        <input type="text" name="puesto_empresa_mod" id="puesto_empresa_mod"
                                            class="form-control" autocomplete="off"
                                            value="{{ $alumno->puesto_empresa }}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="antiguedad_mod" class="control-label">ANTIGUEDAD:</label>
                                        <input type="text" name="antiguedad_mod" id="antiguedad_mod" class="form-control"
                                            autocomplete="off" value="{{ $alumno->antiguedad }}">
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label for="direccion_empresa_mod" class="control-label">DIRECCIÓN:</label>
                                        <input type="text" name="direccion_empresa_mod" id="direccion_empresa_mod"
                                            class="form-control" autocomplete="off"
                                            value="{{ $alumno->direccion_empresa }}">
                                    </div>
                                </div>
                            </div>
                            <hr style="border-color: dimgray">
                            <h5><b>REQUISITOS</b></h5>
                            <hr />
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <table class="table table-striped">
                                        <tr>
                                            <td>
                                                <div class="form-inline">
                                                    @if (@isset($requisitos) || $alumno->chk_acta_nacimiento == 'true')
                                                        @if ($alumno->chk_acta_nacimiento == 'true' || $requisitos->chk_acta_nacimiento == 'true')
                                                            <label><input id="chk_acta_mod" name="chk_acta_mod"
                                                                    type="checkbox" value="true" checked />&nbsp;&nbsp;Acta
                                                                de Nacimiento</label>
                                                        @else
                                                            <label><input id="chk_acta_mod" name="chk_acta_mod"
                                                                    type="checkbox" value="true" />&nbsp;&nbsp;Acta de
                                                                Nacimiento</label>
                                                        @endif
                                                    @else
                                                        <label><input id="chk_acta_mod" name="chk_acta_mod" type="checkbox"
                                                                value="true" />&nbsp;&nbsp;Acta de Nacimiento</label>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if (isset($requisitos->fecha_expedicion_acta_nacimiento))
                                                    <div class="form-inline">
                                                        <div>
                                                            <label for="">FECHA DE EXPEDICIÓN ACTA DE NACIMIENTO
                                                                <br></label>
                                                            <input type="date" name="fecha_expedicion_acta_nacimiento_mod"
                                                                id="fecha_expedicion_acta_nacimiento_mod"
                                                                value="{{ $requisitos->fecha_expedicion_acta_nacimiento }}">
                                                        </div>
                                                        @if ($vigencia_acta >= 730)
                                                            <div class="form-group col-md-6">
                                                                <a class="btn btn-danger" id="vigencia_mod"
                                                                    name="vigencia_mod" readonly>LA VIGENCIA DEL DOCUMENTO
                                                                    ACTA DE NACIMIENTO HA EXPIRADO, ACTUALIZAR REQUISITO A
                                                                    LA BREVEDAD</a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div>
                                                        <label for="">FECHA DE EXPEDICIÓN ACTA DE NACIMIENTO <br></label>
                                                        <input type="date" name="fecha_expedicion_acta_nacimiento_mod"
                                                            id="fecha_expedicion_acta_nacimiento_mod">
                                                    </div>
                                                @endif
                                            </td>
                                            @if (empty($requisitos))
                                                <td>
                                                    @if ($alumno->chk_acta_nacimiento == 'true')
                                                        <div class="form-group col-md-4">
                                                            <a name="doc_url_acta_naci_mod" id="doc_url_acta_naci_mod"
                                                                href="{{ $alumno->acta_nacimiento }}" target="blank"
                                                                class="btn btn-danger">PDF</a>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-inline">
                                                    @if (isset($requisitos) || $alumno->chk_curp == 'true')
                                                        @if ($alumno->chk_curp == 'true' || $requisitos->chk_curp == 'true')
                                                            <label><input id="chk_curp_mod" name="chk_curp_mod"
                                                                    type="checkbox" value="true"
                                                                    checked />&nbsp;&nbsp;CURP</label>
                                                        @else
                                                            <label><input id="chk_curp_mod" name="chk_curp_mod"
                                                                    type="checkbox" value="true" />&nbsp;&nbsp;CURP</label>
                                                        @endif
                                                    @else
                                                        <label><input id="chk_curp_mod" name="chk_curp_mod" type="checkbox"
                                                                value="true" />&nbsp;&nbsp;CURP</label>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if (isset($requisitos->fecha_expedicion_curp))
                                                    <DIV class="form-inline">
                                                        <div>
                                                            <label for="">FECHA DE EXPEDICIÓN CURP <br></label>
                                                            <input type="date" name="fecha_expedicion_curp_mod"
                                                                id="fecha_expedicion_curp_mod"
                                                                value="{{ $requisitos->fecha_expedicion_curp }}">
                                                        </div>
                                                        @if ($vigencia_curp >= 365)
                                                            <div class="form-group col-md-6">
                                                                <a class="btn btn-danger" id="vigencia_mod"
                                                                    name="vigencia_mod" readonly>LA VIGENCIA DEL DOCUMENTO
                                                                    CURP HA EXPIRADO, ACTUALIZAR REQUISITO A LA BREVEDAD</a>
                                                            </div>
                                                        @endif
                                                    </DIV>
                                                @else
                                                    <div>
                                                        <label for="">FECHA DE EXPEDICIÓN CURP <br></label>
                                                        <input type="date" name="fecha_expedicion_curp_mod"
                                                            id="fecha_expedicion_curp_mod">
                                                    </div>
                                                @endif
                                            </td>
                                            @if (empty($requisitos))
                                                <td>
                                                    @if ($alumno->chk_curp == 'true')
                                                        <div class="form-group col-md-4">
                                                            <a name="doc_url_curp_mod" id="doc_url_curp_mod"
                                                                href="{{ $alumno->documento_curp }}" target="blank"
                                                                class="btn btn-danger">PDF</a>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-inline">
                                                    @if (isset($requisitos) || $alumno->chk_comprobante_ultimo_grado == 'true')
                                                        @if ($alumno->chk_comprobante_ultimo_grado == 'true' || $requisitos->chk_escolaridad)
                                                            <label><input id="chk_escolaridad_mod"
                                                                    name="chk_escolaridad_mod" type="checkbox" value="true"
                                                                    checked />&nbsp;&nbsp;&Uacute;ltimo Grado de
                                                                Estudios</label>
                                                        @else
                                                            <label><input id="chk_escolaridad_mod"
                                                                    name="chk_escolaridad_mod" type="checkbox"
                                                                    value="true" />&nbsp;&nbsp;&Uacute;ltimo Grado de
                                                                Estudios</label>
                                                        @endif
                                                    @else
                                                        <label><input id="chk_escolaridad_mod" name="chk_escolaridad_mod"
                                                                type="checkbox" value="true" />&nbsp;&nbsp;&Uacute;ltimo
                                                            Grado de Estudios</label>
                                                    @endif

                                                </div>
                                            </td>
                                            <td></td>
                                            @if (empty($requisitos))
                                                <td>
                                                    @if ($alumno->chk_comprobante_ultimo_grado == 'true')
                                                        <div class="form-group col-md-4">
                                                            <a name="doc_url_ultimo_grado_mod" id="doc_url_ultimo_grado_mod"
                                                                href="{{ $alumno->comprobante_ultimo_grado }}"
                                                                target="blank" class="btn btn-danger">PDF</a>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-inline">
                                                    @if (isset($requisitos) || $alumno->chk_comprobante_calidad_migratoria == 'true')
                                                        @if ($alumno->chk_comprobante_calidad_migratoria == 'true' || $requisitos->chk_comprobante_migracion)
                                                            <label><input id="chk_comprobante_migracion_mod"
                                                                    name="chk_comprobante_migratorio_mod" type="checkbox"
                                                                    value="true" checked />&nbsp;&nbsp;Comprobante
                                                                Migratorio</label>
                                                        @else
                                                            <label><input id="chk_comprobante_migracion_mod"
                                                                    name="chk_comprobante_migratorio_mod" type="checkbox"
                                                                    value="true" />&nbsp;&nbsp;Comprobante
                                                                Migratorio</label>
                                                        @endif
                                                    @else
                                                        <label><input id="chk_comprobante_migracion_mod"
                                                                name="chk_comprobante_migratorio_mod" type="checkbox"
                                                                value="true" />&nbsp;&nbsp;Comprobante Migratorio</label>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if (isset($requisitos->fecha_vigencia_migratorio))
                                                    <div class="form-inline">
                                                        <div>
                                                            <label for="">FECHA DE VIGENCIA DE COMPROBANTE MIGRATORIO
                                                                <br></label>
                                                            <input type="date" name="fecha_vigencia_migratorio_mod"
                                                                id="fecha_vigencia_migratorio_mod"
                                                                value="{{ $requisitos->fecha_vigencia_migratorio }}">
                                                        </div>
                                                        @if ($vigencia_migracion == true)
                                                            <div class="form-group col-md-6">
                                                                <a class="btn btn-danger" id="vigencia_mod"
                                                                    name="vigencia_mod" readonly>LA VIGENCIA DEL DOCUMENTO
                                                                    COMPROBANTE MIGRATORIO HA EXPIRADO, ACTUALIZAR REQUISITO
                                                                    A LA BREVEDAD</a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div>
                                                        <label for="">FECHA DE VIGENCIA DE COMPROBANTE MIGRATORIO
                                                            <br></label>
                                                        <input type="date" name="fecha_vigencia_migratorio_mod"
                                                            id="fecha_vigencia_migratorio_mod">
                                                    </div>
                                                @endif
                                            </td>
                                            @if (empty($requisitos))
                                                <td>
                                                    @if ($alumno->chk_comprobante_calidad_migratoria == 'true')
                                                        <div class="form-group col-md-4">
                                                            <a name="doc_url_compro_migra_mod" id="doc_url_compro_migra_mod"
                                                                href="{{ $alumno->comprobante_calidad_migratoria }}"
                                                                target="blank" class="btn btn-danger">PDF</a>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    </table>
                                </div>
                                <div class="form-inline col-md-12">
                                    <table class="table table-striped">
                                        <tr>
                                            <td>
                                                @if (isset($requisitos->documento))
                                                    <div class="form-inline col-md-12">
                                                        <div class="form-group col-md-4">
                                                            <label for="">Carga requisitos</label>
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input"
                                                                    id="customFile_mod" name="customFile_mod"
                                                                    onchange="fileValidationpdfmod()">
                                                                <label class="custom-file-label"
                                                                    for="customFile_mod">SELECCIONAR DOCUMENTO</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-5">
                                                            <a name="doc_url_mod" id="doc_url_mod"
                                                                href="{{ $requisitos->documento }}" target="blank"
                                                                class="btn btn-danger">PDF</a>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="form-group col-md-4">
                                                        <label for="">Carga requisitos</label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                id="customFile_mod" name="customFile_mod"
                                                                onchange="fileValidationpdfmod()">
                                                            <label class="custom-file-label"
                                                                for="customFile_mod">SELECCIONAR DOCUMENTO</label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <a class="btn btn-dark-green" href="https://www.ilovepdf.com/es/unir_pdf"
                                                    target="blank">UNIR PDF´s</a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <hr style="border-color: dimgray">
                            <h5><b>DATOS CERSS</b></h5>
                            <br><br>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    @if ($alumno->es_cereso == 'true')
                                        <label><input type="checkbox" id="cerss_chk_mod" name="cerss_chk_mod" value="true"
                                                checked>&nbsp;&nbsp;¿El aspirante pertenece a algún cereso?</label>
                                    @else
                                        <label><input type="checkbox" id="cerss_chk_mod" name="cerss_chk_mod"
                                                value="true">&nbsp;&nbsp;¿El aspirante pertenece a algún cereso?</label>
                                    @endif
                                </div>
                            </div>
                            <div id="datos_cerss">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        {{ Form::text('num_expediente_cerss_mod', $alumno->numero_expediente, ['id' => 'num_expediente_cerss_mod', 'class' => 'form-control', 'placeholder' => 'NÚMERO DE EXPEDIENTE']) }}
                                    </div>
                                </div>
                            </div>
                            <br><br><br><br>
                            <!--botones de enviar y retroceder-->
                            <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-right">
                                        <button type="submit" class="btn btn-warning" id="guardarMod">GUARDAR
                                            MODIFICACIÓN</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif
            @endif
        @endif
        <br><br><br>
    </div>
@endsection
@section('script_content_js')
<script type="text/javascript">
    //ocultar sección empleado y cerss
    function showContent() {
        element = document.getElementById("content");
        element1 = document.getElementById("datos_cerss");
        check = document.getElementById("trabajo");
        check1 = document.getElementById("cerss_chk");
        if (trabajo.checked) {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
        if (cerss_chk.checked) {
            element1.style.display = 'block';
        } else {
            element1.style.display = 'none';
        }
    };

    function validarInput(input) {
        if (!$('#cerss_ok').prop('checked')) {
            var curp = input.value.toUpperCase(),
                resultado = document.getElementById("resultado"),
                valido = "No válido";
            document.getElementById('nuevo').disabled = true;

            if (curpValida(curp)) {
                valido = "Válido";
                resultado.classList.add("ok");
                document.getElementById('nuevo').disabled = false;
            } else {
                resultado.classList.remove("ok");
            }
            resultado.innerText = "  Formato: " + valido;

        }
    }

    function validarInputMod(input) {
        if (!$('#cerss_ok').prop('checked')) {
            var curp = input.value.toUpperCase(),
                resultado = document.getElementById("resultado2"),
                valido = "No válido";
            document.getElementById('guardarMod').disabled = true;

            if (curpValida(curp)) {
                valido = "Válido";
                resultado.classList.add("ok");
                document.getElementById('guardarMod').disabled = false;
            } else {
                resultado.classList.remove("ok");
            }
            resultado.innerText = "  Formato: " + valido;

        }
    }

    function curpValida(curp) {
        var re =
            /^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0\d|1[0-2])(?:[0-2]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/,
            validado = curp.match(re);
        if (!validado) return false;

        function digitoVerificador(curp17) {
            var diccionario = "0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ",
                lngSuma = 0.0,
                lngDigito = 0.0;
            for (var i = 0; i < 17; i++) lngSuma = lngSuma + diccionario.indexOf(curp17.charAt(i)) * (18 - i);
            lngDigito = 10 - lngSuma % 10;
            if (lngDigito == 10) return 0;
            return lngDigito;
        }
        if (validado[2] != digitoVerificador(validado[1])) return false;

        return true;
    }

    function fileValidation() {
        var fileInput = document.getElementById('fotografia');
        var filePath = fileInput.value;
        var fileSize = fileInput.files[0].size;
        var allowedExtensions = /(.jpg|.jpeg|.png|.gif)$/i;
        if (!allowedExtensions.exec(filePath)) {
            alert('Por favor solo cargar archivos con extensión .jpeg/.jpg/.png/.gif ');
            fileInput.value = '';
            return false;
        } else {
            if (fileSize > 5000000) {
                alert('Por favor el archivo debe pesar menos de 5MB');
                fileInput.value = '';
                return false;
            }
            //Image preview
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').innerHTML = '<img src="' + e.target.result + '"/>';
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }

    function fileValidationmod() {
        var fileInput = document.getElementById('fotografia_mod');
        var filePath = fileInput.value;
        var fileSize = fileInput.files[0].size;
        var allowedExtensions = /(.jpg|.jpeg|.png|.gif)$/i;
        if (!allowedExtensions.exec(filePath)) {
            alert('Por favor solo cargar archivos con extensión .jpeg/.jpg/.png/.gif ');
            fileInput.value = '';
            return false;
        } else {
            if (fileSize > 5000000) {
                alert('Por favor el archivo debe pesar menos de 5MB');
                fileInput.value = '';
                return false;
            }
            //Image preview
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').innerHTML = '<img src="' + e.target.result + '"/>';
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }

    function fileValidationpdf() {
        var fileInput = document.getElementById('customFile');
        var filePath = fileInput.value;
        var fileSize = fileInput.files[0].size;
        var allowedExtensions = /(.pdf)$/i;
        if (!allowedExtensions.exec(filePath)) {
            alert('Por favor solo cargar archivos pdf');
            fileInput.value = '';
            return false;
        } else {
            if (fileSize > 5000000) {
                alert('Por favor el archivo debe pesar menos de 5MB');
                fileInput.value = '';
                return false;
            }
            //Image preview
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').innerHTML = '<img src="' + e.target.result + '"/>';
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }

    function fileValidationpdfmod() {
        var fileInput = document.getElementById('customFile_mod');
        var filePath = fileInput.value;
        var fileSize = fileInput.files[0].size;
        var allowedExtensions = /(.pdf)$/i;
        if (!allowedExtensions.exec(filePath)) {
            alert('Por favor solo cargar archivos pdf');
            fileInput.value = '';
            return false;
        } else {
            if (fileSize > 5000000) {
                alert('Por favor el archivo debe pesar menos de 5MB');
                fileInput.value = '';
                return false;
            }
            //Image preview
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').innerHTML = '<img src="' + e.target.result + '"/>';
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }

    $(function() {
        $('#estado').on("change", function() {
            var estados_id = $(this).val();
            if ($(estados_id != '')) {
                $.get('municipio_nov', {
                    estado_id: estados_id
                }, function(municipio) {
                    $('#municipio').empty();
                    $('#municipio').append("<option value=''>Seleccione un municipio</option>");
                    $.each(municipio, function(index, value) {
                        $('#municipio').append("<option value='" + value['clave'] + "'>" + value['muni'] + "</option>");
                    });
                });
            }
        });

        $(document).ready(function() {
            $('#curp_mod').focus();
            $('#curp_mod').keyup(function(e) {
                var fa = $(this).val();
                $.get('/alumnos/fecha_s', {
                    fa: fa
                }, function(low) {

                    $.each(low, function(index, value) {
                        $('#fecha_nacimiento_mod').val(low.fecha);
                        $('#sexo_mod').val(low.sexo);
                    });
                });
            });
        });

        $('#curp_mod').on("change", function() {
            console.log('print');
            var fa = $(this).val();
            if ($(fa != '')) {
                $.get('')
            }
        });

        $('#estados_mod').on("change", function() {
            var estados_id = $(this).val();
            if ($(estados_id != '')) {
                $.get('/alumnos/municipio_nov', {
                    estado_id: estados_id
                }, function(novo) {
                    $('#municipios_mod').empty();
                    $('#municipios_mod').append("<option value=''>Seleccione un municipio</option>");
                    $.each(novo, function(index, value) {
                        $('#municipios_mod').append("<option value='" + value['clave'] + "'>" + value['muni'] + "</option>");
                    });
                });
            }
        });

        $('#cacahuate').validate({
            rules: {
                curp_val: {
                    required: true
                }
            },
            messages: {
                curp_val: {
                    required: 'Por favor Ingresé la curp',
                }
            }
        });
        $('#form_sid').validate({
            rules: {
                nombre: {
                    required: true,
                    minlength: 3
                },
                apellidoPaterno: {
                    required: true,
                    minlength: 2
                },
                apellidoMaterno: {

                    minlength: 2
                },
                nacionalidad: {
                    required: true,
                    minlength: 2
                },
                fecha: {
                    required: true
                },
                sexo: {
                    required: true
                },
                curp: {
                    required: true,
                    CURP: true
                },
                telefonosid: {
                    required: true,
                    //phoneMEXICO: /^\(?(\d{3})\)?[-\. ]?(\d{3})[-\. ]?(\d{4})$/
                },
                estado: {
                    required: true
                },
                municipio: {
                    required: true
                },
                localidad: {
                    required: true
                },
                estado_civil: {
                    required: true
                },
                discapacidad: {
                    required: true
                },
                dia: {
                    required: true
                },
                mes: {
                    required: true
                },
                anio: {
                    required: true,
                    maxlength: 4,
                    number: true
                },
                ultimo_grado_estudios: {
                    required: true
                },
                medio_entero: {
                    required: true
                },
                motivos_eleccion_sistema_capacitacion: {
                    required: true
                },
                requisitos: {
                    required: true
                },
                chk_curp: {
                    required: true
                },
                customFile: {
                    required: true
                },
                fecha_expedicion_curp: {
                    required: true
                }
            },
            messages: {
                nombre: {
                    required: 'Por favor ingrese su nombre',
                    minlength: jQuery.validator.format(
                        "Por favor, al menos {0} caracteres son necesarios")
                },
                apellidoPaterno: {
                    required: 'Por favor ingrese su apellido'
                },
                nacionalidad: {
                    required: 'Por favor ingrese su nacionalidad'
                },
                fecha: {
                    required: 'Por favor ingrese fecha de nacimiento'
                },
                sexo: {
                    required: 'Por favor Elegir su genero'
                },
                curp: {
                    required: 'Por favor Ingresé la curp',
                },
                telefonosid: {
                    required: 'Por favor, ingrese telefóno',
                },
                estado: {
                    required: 'Por favor, seleccione un estado'
                },
                municipio: {
                    required: 'Por favor, seleccione el municipio'
                },
                localidad: {
                    required: 'Por favor, seleccione la localidad'
                },
                estado_civil: {
                    required: 'Por favor, seleccione su estado civil'
                },
                discapacidad: {
                    required: 'Por favor seleccione una opción'
                },
                ultimo_grado_estudios: {
                    required: "Agregar último grado de estudios"
                },
                dia: {
                    required: "Por favor, seleccione el día"
                },
                mes: {
                    required: "Por favor, seleccione el mes"
                },
                anio: {
                    required: "Por favor, Ingrese el año",
                    maxlength: "Sólo acepta 4 digitos",
                    number: "Sólo se aceptan números"
                },
                ultimo_grado_estudios: {
                    required: "Por favor, seleccione una opción"
                },
                medio_entero: {
                    required: "Por favor, seleccione una opción"
                },
                motivos_eleccion_sistema_capacitacion: {
                    required: "Por favor, seleccione una opción"
                },
                requisitos: {
                    required: "Por favor, cargue uno de los documentos requisitados"
                },
                chk_curp: {
                    required: "Por favor, seleccione el check"
                },
                customFile: {
                    required: "Por favor, cargue los documentos requisitados"
                },
                fecha_expedicion_curp: {
                    required: "Por favor, ingrese la fecha asignada"
                }
            }
        });
        $('#sid_registro_modificacion').validate({
            rules: {
                curp_mod: {
                    required: true,
                    CURP_VAL: true
                },
                localidad_mod: {
                    required: true,
                },
                fecha_nacimiento_mod: {
                    required: true
                },
                sexo_mod: {
                    required: true
                },
            },
            messages: {
                curp: {
                    required: 'Por favor Ingresé la curp',
                },
                localidad_mod: {
                    required: 'Seleccione la localidad'
                },
                fecha_nacimiento_mod: {
                    required: 'Por favor ingrese fecha de nacimiento'
                },
                sexo_mod: {
                    required: 'Por favor Elegir su genero'
                },
            }
        });

    });

    $('#municipio').on('change', function() {
        municipio = $(this).val();
        if (municipio != '') {
            $.get('/inscripciones/localidad', {
                search: municipio
            }, function (localidades) {
                $('#localidad').empty();
                $('#localidad').append("<option value=''>Seleccione una localidad</option>")
                $.each(localidades, function(index, value) {
                    $('#localidad').append("<option value='" + value['clave'] + "'>" + value['localidad'] + "</option>")
                })
            })
        }
    });

    $('#municipios_mod').on('change', function() {
        municipio = $(this).val();
        if (municipio != '') {
            $.get('/inscripciones/localidad', {
                search: municipio
            }, function (localidades) {
                $('#localidad_mod').empty();
                $('#localidad_mod').append("<option value=''>Seleccione una localidad</option>")
                $.each(localidades, function(index, value) {
                    $('#localidad_mod').append("<option value='" + value['clave'] + "'>" + value['localidad'] + "</option>")
                })
            })
        }
    });

</script>
@endsection
