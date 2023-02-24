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
    </style>
    <?php
        $nombre = $apaterno = $amaterno = $nacionalidad = $telefono_casa = $telefono_cel = $email = $face = $twitter = $instagram = $tiktok = 
        $ecivil = $domicilio = $colonia = $estado = $muni = $localidad = $cp = $etnia = $gvulnerable = $escolaridad = $medio_entero =
        $motivo_eleccion = $empresa_trabaja = $puesto_empresa = $antiguedad = $direccion_empresa = $requisitos = $nexpediente_cerss = $fotografia = null;
        $publicaciones = $redes = $lgbt = $madre_soltera = $faminmigra = $inmigra = $empleado = $ficha_cerss = $cerss = false;
        if (isset($alumno)) {
            $nombre = $alumno->nombre;
            $apaterno = $alumno->apellido_paterno;
            $amaterno = $alumno->apellido_materno;
            $nacionalidad = $alumno->nacionalidad;
            $telefono_casa = $alumno->telefono_casa;
            $telefono_cel = $alumno->telefono_personal;
            $email = $alumno->correo;
            $face = $alumno->facebook;
            $twitter = $alumno->twitter;
            $instagram = $alumno->instagram;
            $tiktok = $alumno->tiktok;
            if ($alumno->recibir_publicaciones) { $publicaciones = true; }
            if ($alumno->ninguna_redsocial) { $redes = true; } 
            $ecivil = $alumno->estado_civil;
            $domicilio = $alumno->domicilio;
            $colonia = $alumno->colonia;
            $estado = $alumno->id_estado;
            $muni = $alumno->clave_municipio;
            $localidad = $alumno->clave_localidad;
            $cp = $alumno->cp;
            if ($alumno->lgbt) { $lgbt = true; } 
            if ($alumno->madre_soltera) { $madre_soltera = true; } 
            if ($alumno->familia_migrante) { $faminmigra = true; } 
            if ($alumno->inmigrante) { $inmigra = true; } 
            $etnia = $alumno->etnia;
            $gvulnerable = $alumno->id_gvulnerable;
            $escolaridad = $alumno->ultimo_grado_estudios;
            $medio_entero = $alumno->medio_entero;
            $motivo_eleccion = $alumno->sistema_capacitacion_especificar;
            if ($alumno->empleado) { $empleado = true; }
            $empresa_trabaja = $alumno->empresa_trabaja;
            $puesto_empresa = $alumno->puesto_empresa;
            $antiguedad = $alumno->antiguedad;
            $direccion_empresa = $alumno->direccion_empresa;
            $requisitos = json_decode($alumno->requisitos);
            $ficha_cerss = $alumno->chk_ficha_cerss;
            if ($alumno->es_cereso) { $cerss = true; }
            $nexpediente_cerss = $alumno->numero_expediente;
            $fotografia = $alumno->fotografia;
        }
    ?>
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
        @endif
        <form method="POST" id="frm2">
            @csrf
            <div class="row justify-content-center">
                <div class="for row">
                    <div class="form-group col-md-6">
                        <input type="text" oninput="validarInput(this)" name="busqueda" class="form-control" id="busqueda" placeholder="CURP" autocomplete="off">
                        <br>
                        <pre id="resultado" name='resultado'></pre>
                    </div>
                    <div class="form-group col-md-3">
                        <button type="button" class="btn btn-success" id="nuevo" name="nuevo">NUEVO</button>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="pull-left">
                            <a class="btn btn-danger" href="{{ route('alumnos.index') }}">Regresar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <form method="POST" id="frm" enctype="multipart/form-data" style="width: 100%;">
            @csrf
            @if ($curp)
                <div class="col-lg-12 margin-tb">
                    <hr style="border-color:dimgray">
                    @if (isset($alumno))
                        <h3><strong>Modificación de Inscripción (SID)</strong></h3>
                    @else
                        <h3><strong>Solicitud de Inscripción (SID)</strong></h3>
                    @endif
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
                                            <input type="file" class="custom-file-input" id="fotografia" name="fotografia" onchange="fileValidation()">
                                            <label class="custom-file-label" for="fotografia">Fotografía</label>
                                        </div>
                                        @if ($fotografia)
                                            <br><br>
                                            <a name="fotografia_url" id="fotografia_url" href="{{$fotografia}}" target="blank" class="btn btn-primary">IMAGEN PRECARGADA</a>
                                        @endif
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
                        <div class="form-group col-md-3">
                            <label for="nombre " class="control-label">Nombre del Aspirante</label>
                            {!! Form::text('nombre', $nombre, ['id'=>'nombre', 'class'=>'form-control']) !!}
                        </div>
                        <div class="form-group col-md-3">
                            <label for="apellido_paterno" class="control-label">Apellido Paterno</label>
                            {!! Form::text('apellido_paterno', $apaterno, ['id'=>'apellido_paterno', 'class'=>'form-control']) !!}
                        </div>
                        <div class="form-group col-md-3">
                            <label for="apellido_materno" class="control-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" value="{{$amaterno}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="">Fecha de nacimiento</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" value="{{$fnacimiento}}" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="sexo" class="control-label">Genero:</label>
                            <input type="text" name="sexo" id="sexo" class="form-control" readonly value="{{$sexo}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Nacionalidad:</label>
                            <input id="nacionalidad" name="nacionalidad" class="form-control" type="text" value="{{$nacionalidad}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Tel&eacute;fono Casa:</label>
                            <input id="telefono_casa" name="telefono_casa" class="form-control" type="text" value="{{$telefono_casa}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Tel&eacute;fono Celular:</label>
                            <input id="telefono_cel" name="telefono_cel" class="form-control" type="text" value="{{$telefono_cel}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Correo Electr&oacute;nico:</label>
                            <input type="email" id="correo" name="correo" class="form-control" placeholder="usuario@gmail.com" type="text" value="{{$email}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Facebook:</label>
                            <input id="facebook" name="facebook" class="form-control" type="text" value="{{$face}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Twitter:</label>
                            <input id="twitter" name="twitter" class="form-control" type="text" value="{{$twitter}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Instagram:</label>
                            <input id="instagram" name="instagram" class="form-control" type="text" value="{{$instagram}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>TikTok:</label>
                            <input id="tiktok" name="tiktok" class="form-control" type="text" value="{{$tiktok}}">
                        </div>
                        <div class="form-group col-md-3">
                            <br />
                            <label><input id="ninguna_redsocial" name="ninguna_redsocial" type="checkbox" value="true" @if ($redes) { checked } @endif>&nbsp;&nbsp;No tiene redes sociales</label>
                        </div>
                        <div class="form-group col-md-3">
                            <br />
                            <label><input id="recibir_publicaciones" name="recibir_publicaciones" type="checkbox" value="true"  @if ($publicaciones) { checked } @endif>&nbsp;&nbsp;¿Recibir publicaciones?</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Estado civil:</label>
                            {!! Form::select('estado_civil', $estado_civil, $ecivil, ['id'=>'estado_civil', 'class'=>'form-control mr-sm--2', 'placeholder' => '- SELECCIONAR -']) !!}
                        </div>
                        <div class="form-group col-md-3">
                            <label for="domicilio" class="control-label">Domicilio</label>
                            <input type="text" class="form-control" id="domicilio" name="domicilio" autocomplete="off" value="{{$domicilio}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="colonia" class="control-label">Colonia</label>
                            <input type="text" class="form-control" id="colonia" name="colonia" autocomplete="off" value="{{$colonia}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="estado" class="control-label">Estado</label>
                            {!! Form::select('estado', $estados, $estado, ['id'=>'estado', 'class'=>'form-control', 'placeholder'=>'- SELECCIONAR -']) !!}
                        </div>
                        <div class="form-group col-md-3">
                            <label for="municipio" class="control-label">Municipio</label>
                            {!! Form::select('municipio', $municipios, $muni, ['id'=>'municipio', 'class'=>'form-control', 'placeholder'=>'- SELECCIONAR -']) !!}
                        </div>
                        <div class="form-group col-md-3">
                            <label for="localidad" class="control-label">Localidad</label>
                            {!! Form::select('localidad', $localidades, $localidad, ['id'=>'localidad', 'class'=>'form-control', 'placeholder'=>'- SELECCIONAR -']) !!}
                        </div>
                        <div class="form-group col-md-3">
                            <label for="cp" class="control-label">Código Postal</label>
                            <input type="text" class="form-control" id="cp" name="cp" value="{{$cp}}">
                        </div>
                    </div>
                    <br>
                    <div class="form-row">
                        <div class="form-group col">
                            <div class="custom-control custom-checkbox">
                                <input value="true" type="checkbox" class="custom-control-input" id="lgbt" name="lgbt" @if ($lgbt) { checked } @endif>
                                <label class="custom-control-label" for="lgbt">LGBTTTI+</label>
                            </div>
                        </div>
                        <div class="form-group col">
                            <label><input id="madre_soltera" name="madre_soltera" type="checkbox" value="true" @if ($madre_soltera) { checked } @endif />&nbsp;&nbsp;¿Es Madre Soltera?</label>
                        </div>
                        <div class="form-group col">
                            <label><input id="familia_migrante" name="familia_migrante" type="checkbox" value="true" @if ($faminmigra) { checked } @endif />&nbsp;&nbsp;¿Tiene Familia Migrante?</label>
                        </div>
                        <div class="form-group col">
                            <label><input id="inmigrante" name="inmigrante" type="checkbox" value="true" @if ($inmigra) { checked } @endif>&nbsp;&nbsp;¿Es Inmigrante?</label>
                        </div>
                        <div class="form-group col">
                            {{ Form::select('etnia', $etnias, $etnia, ['id' => 'etnia', 'class' => 'form-control mr-sm-2', 'placeholder' => '--ETNIA--']) }}
                        </div>
                    </div>
                    <br>
                    <div>
                        <div class="form-row">
                            <label>&nbsp;&nbsp;¿El aspirante pertenece a algún Grupo Vulnerable?</label>
                        </div>
                        <br>
                        <div class="form-row">
                                @foreach ($gvulnerables as $item)
                                <div class="form-group col-md-4">
                                    @if ($gvulnerable && in_array($item->id, json_decode($gvulnerable)))
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
                        <h4><strong>DATOS GENERALES DE CAPACITACIÓN</strong></h4>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="ultimo_grado_estudios" class="control-label">ÚLTIMO GRADO DE ESTUDIOS:</label>
                            {!! Form::select('ultimo_grado_estudios', $grado_estudio, $escolaridad, ['id'=>'ultimo_grado_estudios', 'class'=>'form-control', 'placeholder'=>'- SELECCIONAR -']) !!}
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="medio_entero" class="control-label">MEDIO POR EL QUE SE ENTERÓ DEL
                                SISTEMA</label>
                            <select class="form-control" id="medio_entero" name="medio_entero"
                                    value="{{ $medio_entero }}">
                                @isset($medio_entero)
                                @if ($medio_entero == '')
                                    <option value="">--SELECCIONAR--</option> 
                                @elseif ($medio_entero !='PRENSA'&& $medio_entero !='RADIO'&&$medio_entero !='TELEVISIÓN'&&$medio_entero !='INTERNET'&&$medio_entero !='FOLLETOS, CARTELES, VOLANTES')
                                    <option value="O">OTRO</option> 
                                @else
                                    <option value="{{ $medio_entero }}">{{ $medio_entero }} @endif 
                                @else
                                    <option value="">--SELECCIONAR--</option>
                                @endisset
                                <option value="PRENSA">PRENSA</option>
                                <option value="RADIO">RADIO</option>
                                <option value="TELEVISIÓN">TELEVISIÓN</option>
                                <option value="INTERNET">INTERNET</option>
                                <option value="FOLLETOS, CARTELES, VOLANTES">FOLLETOS, CARTELES, VOLANTES</option>
                                <option value="O">OTRO</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <div id="ocultar_medio">
                                <label for="medio_especificar" class="control-label">ESPECIFIQUE</label>
                                <input type="text" class="form-control" name="medio_especificar" id="medio_especificar" value="{{$medio_entero}}">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                                <label for="motivos_eleccion_sistema_capacitacion" class="control-label">MOTIVOS DE ELECCIÓN DEL SISTEMA DE CAPACITACIÓN:</label>
                                <select class="form-control" name="motivos_eleccion_sistema_capacitacion" id="motivos_eleccion_sistema_capacitacion">
                                @isset($motivo_eleccion)
                                @if ($motivo_eleccion == '')
                                    <option value="">--SELECCIONAR--</option>
                                @elseif ($motivo_eleccion != 'PARA EMPLEARSE O AUTOEMPLEARSE' && $motivo_eleccion != 'PARA AHORRAR GASTOS AL INGRESO FAMILIAR' && $motivo_eleccion != 'POR ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA' && $motivo_eleccion != 'PARA MEJORAR SU SITUACIÓN EN EL TRABAJO' && $motivo_eleccion != 'POR DISPOSICIÓN DE TIEMPO LIBRE' && $motivo_eleccion != '')
                                    <option value="O">OTRO</option> 
                                @else
                                    <option value="{{ $motivo_eleccion }}">{{ $motivo_eleccion }} 
                                @endif 
                                @else
                                    <option value="">--SELECCIONAR--</option>
                                @endisset
                                <option value="PARA EMPLEARSE O AUTOEMPLEARSE">PARA EMPLEARSE O AUTOEMPLEARSE</option>
                                <option value="PARA AHORRAR GASTOS AL INGRESO FAMILIAR">PARA AHORRAR GASTOS AL INGRESO FAMILIAR</option>
                                <option value="POR ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA">POR ESTAR EN ESPERA DE INCORPORARSE A OTRA INSTITUCIÓN EDUCATIVA</option>
                                <option value="PARA MEJORAR SU SITUACIÓN EN EL TRABAJO">PARA MEJORAR SU SITUACIÓN EN EL TRABAJO</option>
                                <option value="POR DISPOSICIÓN DE TIEMPO LIBRE">POR DISPOSICIÓN DE TIEMPO LIBRE</option>
                                <option value="O">OTRO</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <div id="ocultar_motivo_capacitacion">
                                <label for="motivo_sistema_capacitacion_especificar"class="control-label">ESPECIFIQUE:</label>
                                <input type="text" class="form-control" name="motivo_sistema_capacitacion_especificar" id="motivo_sistema_capacitacion_especificar" value="{{ $motivo_eleccion }}">
                            </div>
                        </div>
                    </div>
                    <hr style="border-color: dimgray">
                    <div style="text-align: center;">
                        <h4><strong>DATOS DE EMPLEO</strong></h4>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label><input type="checkbox" id="trabajo" name="trabajo" value="true" @if ($empleado) { checked } @endif>&nbsp;&nbsp;¿El aspirante está empleado?</label>
                        </div>
                        {{-- <div class="form-group col-md-4">
                            <label><input type="checkbox" id="funcionario" name="funcionario" value="true">&nbsp;&nbsp;¿El aspirante es un servidor público?</label>
                        </div> --}}
                    </div>
                    <div id="content">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="empresa" class="control-label">EMPRESA DONDE TRABAJA:</label>
                                <input type="text" name="empresa" id="empresa" class="form-control" autocomplete="off" value="{{$empresa_trabaja}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="puesto_empresa" class="control-label">PUESTO:</label>
                                <input type="text" name="puesto_empresa" id="puesto_empresa" class="form-control" autocomplete="off" value="{{$puesto_empresa}}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="antiguedad" class="control-label">ANTIGUEDAD:</label>
                                <input type="text" name="antiguedad" id="antiguedad" class="form-control" autocomplete="off" value="{{$antiguedad}}">
                            </div>
                            <div class="form-group col-md-8">
                                <label for="direccion_empresa" class="control-label">DIRECCIÓN:</label>
                                <input type="text" name="direccion_empresa" id="direccion_empresa" class="form-control" autocomplete="off" value="{{$direccion_empresa}}">
                            </div>
                        </div>
                    </div>
                    <hr style="border-color: dimgray">
                    <h5><strong>REQUISITOS</strong></h5>
                    <hr />
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <table class="table table-striped" style="width: 100%; text-align: left; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 20%;">
                                        <label><input id="chk_acta" name="chk_acta" type="checkbox" value="true" @isset($requisitos) @if ($requisitos->chk_acta_nacimiento) { checked } @endif @endisset/>&nbsp;&nbsp;Acta de Nacimiento</label> 
                                    </td>
                                    <td>
                                        FECHA DE EXPEDICIÓN ACTA DE NACIMIENTO
                                    </td>
                                    <td>
                                        <input type="date" name="fecha_expedicion_acta_nacimiento" class="form-control" id="fecha_expedicion_acta_nacimiento" @isset($requisitos) value="{{$requisitos->fecha_expedicion_acta_nacimiento}}" @endisset>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label><input id="chk_curp" name="chk_curp" type="checkbox" value="true" @isset($requisitos) @if ($requisitos->chk_curp) { checked } @endif @endisset/>&nbsp;&nbsp;CURP</label>
                                    </td>
                                    <td>
                                        FECHA DE EXPEDICIÓN CURP
                                    </td>
                                    <td>
                                        <input type="date" name="fecha_expedicion_curp" id="fecha_expedicion_curp" class="form-control" @isset($requisitos) value="{{$requisitos->fecha_expedicion_curp}}" @endisset>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <label><input id="chk_escolaridad" name="chk_escolaridad" type="checkbox" value="true" @isset($requisitos) @if ($requisitos->chk_escolaridad) { checked } @endif @endisset/>&nbsp;&nbsp;&Uacute;ltimo Grado de Estudios</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label><input id="chk_comprobante_migratorio" name="chk_comprobante_migratorio" type="checkbox" value="true" @isset($requisitos) @if ($requisitos->chk_comprobante_migracion) { checked } @endif @endisset/>&nbsp;&nbsp;Comprobante Migratorio</label>
                                    </td>
                                    <td>
                                        FECHA DE VIGENCIA DE COMPROBANTE MIGRATORIO
                                    </td>
                                    <td>
                                        <input type="date" name="fecha_vigencia_migratorio" id="fecha_vigencia_migratorio" class="form-control" @isset($requisitos) value="{{$requisitos->fecha_vigencia_migratorio}}" @endisset>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <label><input id="chk_ficha_cerss" name="chk_ficha_cerss" type="checkbox" value="true" @if ($ficha_cerss) { checked } @endif/>&nbsp;&nbsp;Ficha Cerss</label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="form-group col-md-4">
                            <table class="table table-striped" style="width: 100%; text-align: left; border-collapse: collapse;">
                                <th>
                                    <td>
                                        <label for="">Carga de PDF con documentos</label><br>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFile" name="customFile" onchange="fileValidationpdf()">
                                            <label class="custom-file-label" for="customFile">SELECCIONAR DOCUMENTO</label>
                                        </div>
                                        @isset($requisitos) @if($requisitos->documento) 
                                            <br><br> 
                                            <a name="doc_url" id="doc_url" href="{{ $requisitos->documento }}" target="blank" class="btn btn-danger">PDF</a>
                                        @endif @endisset
                                    </td>
                                    <td>
                                        <a class="btn btn-dark-green" href="https://www.ilovepdf.com/es/unir_pdf" target="blank">UNIR PDF´s</a>
                                    </td>
                                </th>
                            </table>
                        </div>
                    </div>
                    <hr style="border-color: dimgray">
                    <h5><strong>DATOS CERSS</strong></h5>
                    <div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label><input type="checkbox" id="cerss_chk" name="cerss_chk" value="true" @if ($cerss) { checked } @endif>&nbsp;&nbsp;¿El aspirante pertenece a algún cereso?</label>
                            </div>
                        </div>
                        <div id="datos_cerss">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    {{ Form::text('num_expediente_cerss', $nexpediente_cerss, ['id' => 'num_expediente_cerss', 'class' => 'form-control', 'placeholder' => 'NÚMERO DE EXPEDIENTE']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr style="border-color: dimgray">
                    <div class="row">
                        <div class="col-lg-12 margin-tb">
                            <div class="pull-right">
                                <button type="submit" class="btn btn-primary" id="update" >GUARDAR CAMBIOS</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </form>
    </div>
    @section('script_content_js')
        <script type="text/javascript">
            $(document).ready(function(){
                $("#nuevo").click(function(){ $('#frm2').attr({'action':"{{route('alumnos.valid')}}",'target':'_self'}); $('#frm2').submit(); });
                $("#update").click(function(){
                    if ($("#frm").valid()) {
                        $('#frm').attr({'action':"{{route('alumnos.save')}}",'target':'_self'}); $('#frm').submit();
                    } else {
                        $("#frm").valid();
                    }
                });
                $("#estado" ).change(function(){
                    cmb_muni();
                });
                $("#municipio" ).change(function(){
                    cmb_loc();
                });
                function cmb_muni(){ 
                    var tipo =$('#estado').val();
                    $("#municipio").empty();                            
                    if(tipo){
                        $.ajax({
                            type: "GET",
                            url: "municipio_nov",
                            data:{estado_id:tipo, _token:"{{csrf_token()}}"},
                            contentType: "application/json",              
                            dataType: "json",
                            success: function (data) {// console.log(data); 
                                $("#municipio").append('<option value="" selected="selected">SELECCIONAR</option>');
                                $.each(data, function () {                                    
                                    //$("#id_curso").append('<option value="" selected="selected">SELECCIONAR</option>');                                    
                                    $("#municipio").append('<option value="'+this['clave']+'">'+this['muni']+'</option>');
                                });
                            }
                        });                        
                    }
                                    
                };
                function cmb_loc(){ 
                    var muni =$('#municipio').val();
                    var est = $('#estado').val();
                    $("#localidad").empty();                            
                    if(muni && est){
                        $.ajax({
                            type: "GET",
                            url: "localidad_nov",
                            data:{muni:muni, estado: est, _token:"{{csrf_token()}}"},
                            contentType: "application/json",              
                            dataType: "json",
                            success: function (data) {// console.log(data); 
                                $("#localidad").append('<option value="" selected="selected">SELECCIONAR</option>');         
                                $.each(data, function () {                                    
                                    //$("#id_curso").append('<option value="" selected="selected">SELECCIONAR</option>');                                    
                                    $("#localidad").append('<option value="'+this['clave']+'">'+this['localidad']+'</option>');
                                });
                            }
                        });                        
                    }
                                    
                };
                $('#frm').validate({
                    rules: {
                        curp: {
                            required: true
                        },
                        nombre: {
                            required: true,
                            minlength: 3
                        },
                        apellido_paterno: {
                            required: true,
                            minlength: 2
                        },
                        apellido_materno: {
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
                        ultimo_grado_estudios: {
                            required: true
                        },
                        medio_entero: {
                            required: true
                        },
                        motivos_eleccion_sistema_capacitacion: {
                            required: true
                        }
                    },
                    messages: {
                        curp: {
                            required: 'Por favor Ingresé la curp'
                        },
                        nombre: {
                            required: 'Por favor escriba el nombre'
                        },
                        apellido_paterno: {
                            required: 'Por favor escriba el apellido paterno'
                        },
                        apellido_materno: {
                            required: 'Por favor escriba el apellido materno'
                        },
                        nacionalidad: {
                            required: 'Por favor escriba la nacionalidad'
                        },
                        fecha: {
                            required: ''
                        },
                        sexo: {
                            required: ''
                        },
                        estado: {
                            required: 'Seleccione el estado'
                        },
                        municipio: {
                            required: 'Seleccione el municipio'
                        },
                        localidad: {
                            required: 'Seleccione la localidad'
                        },
                        estado_civil: {
                            required: 'Seleccione el estado civil'
                        },
                        ultimo_grado_estudios: {
                            required: 'Seleccione la escolaridad'
                        },
                        medio_entero: {
                            required: 'Seleccione una opción'
                        },
                        motivos_eleccion_sistema_capacitacion: {
                            required: 'Seleccione una opción'
                        }
                    }
                });
                if ($('#medio_entero').val()=='O') {
                    $("#ocultar_medio").show();
                } else {
                    $("#ocultar_medio").hide();
                }
                $('#medio_entero').change(function(){
                    if ($("#medio_entero").val()=='O') {
                        $("#ocultar_medio").show();
                    } else {
                        $("#ocultar_medio").hide();
                    }
                });
                if ($('#motivos_eleccion_sistema_capacitacion').val()=='O') {
                    $('#ocultar_motivo_capacitacion').show();
                } else {
                    $('#ocultar_motivo_capacitacion').hide();
                }
                $('#motivos_eleccion_sistema_capacitacion').change(function(){
                    if ($("#motivos_eleccion_sistema_capacitacion").val()=='O') {
                        $("#ocultar_motivo_capacitacion").show();
                    } else {
                        $("#ocultar_motivo_capacitacion").hide();
                    }
                });
                $('#trabajo').change(function(){
                    if( $(this).is(':checked') ) {
                        $('#content').show();
                    } else {
                        $('#content').hide();
                    }
                });
                if( $('#trabajo').is(':checked') ) {
                    $('#content').show();
                } else {
                    $('#content').hide();
                }
                $('#cerss_chk').change(function(){
                    if( $(this).is(':checked') ) {
                        $('#datos_cerss').show();
                    } else {
                        $('#datos_cerss').hide();
                    }
                });
                if( $('#cerss_chk').is(':checked') ) {
                    $('#datos_cerss').show();
                } else {
                    $('#datos_cerss').hide();
                }
            });
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
        </script>
    @endsection
@endsection