
@extends('theme.sivyc.layout')
@section('title', 'Solicitud de Inscripción | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        table tr td, table tr th { font-size: 12px; width: 10%; }
        table tr th{ text-align: center; }
        #resultado { background-color: red; color: white; font-weight: bold; }
        #resultado.ok { background-color: green; }
        #resultado.white { background-color: white; }
        .check-input {transform: scale(1.5); }

        .img-preview { width: 150px; height: auto; display: none; }
        #foto{ border: 2px solid #808080; padding: 10px; border-radius: 4px; height: 250px;}
        h5 { margin-top:30px; }
        hr { margin-top:10px; }
        .icon-size {
            font-size: 2.5rem;
        }

        /* Estilos del switch */
        .switch-container {
            display: flex;
            align-items: center;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            border-radius: 50%;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .switch-text {
            margin-left: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .acordeon-borde {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background-color: #f9f9f9;
        }
        input:checked + .slider {
            background-color: #28a745; /* Color verde */
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .checkbox {
            margin-top: 5px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .card-header {
            background-color: #4e73df;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }
        .custom-file {
            position: relative;
            display: inline-block;
            width: 100%;
            height: calc(2.25rem + 2px);
            margin-bottom: 0;
        }
        .custom-file-input {
            position: relative;
            z-index: 2;
            width: 100%;
            height: calc(2.25rem + 2px);
            margin: 0;
            opacity: 0;
        }
        .custom-file-label {
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1;
            height: calc(2.25rem + 2px);
            padding: 0.375rem 0.75rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .custom-file-label::after {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 3;
            display: block;
            height: calc(calc(2.25rem + 2px) - 1px * 2);
            padding: 0.375rem 0.75rem;
            line-height: 1.5;
            color: #495057;
            content: "Examinar";
            background-color: #e9ecef;
            border-left: inherit;
            border-radius: 0 0.25rem 0.25rem 0;
        }
        .icon-size {
            font-size: 1.8rem;
        }
        .file-info {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
            font-size: 0.9rem;
        }
        .requirements-list {
            background-color: #f0f8ff;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
        }
        .preview-container {
            margin-top: 20px;
            display: none;
        }
        .preview-content {
            border: 1px dashed #ccc;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            background-color: #f9f9f9;
        }
    </style>
@endsection
@section('content')
    <?php
        $clase_alfa = '';
        $nombre = $apaterno = $amaterno = $nacionalidad = $telefono_casa = $telefono_cel = $email = $face = $twitter = $instagram = $tiktok =
        $ecivil = $domicilio = $colonia = $estado = $muni = $localidad = $cp = $etnia = $gvulnerable = $escolaridad = $medio_entero =
        $motivo_eleccion = $empresa_trabaja = $puesto_empresa = $antiguedad = $direccion_empresa = $requisitos = $nexpediente_cerss = $fotografia = null;
        $publicaciones = $redes = $lgbt = $madre_soltera = $faminmigra = $inmigra = $empleado = $ficha_cerss = $cerss = $confirmacion = $check_bolsa = $switch_alfa = false;
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
            $confirmacion = $alumno->medio_confirmacion;
            if ($alumno->check_bolsa) {$check_bolsa = true;}
            $aspiranteId = $alumno->id;
        }

        if(isset($datos_alfa->switch_alfa)){
            $switch_alfa = $datos_alfa->switch_alfa;
            if($datos_alfa->switch_alfa == false){
                $clase_alfa = 'd-none';
            }
        }else{ $clase_alfa = 'd-none'; }
    ?>
    <div class="card-header">
        Presincripción / Editar Aspirante
    </div>
    <div class="card card-body  p-5" style=" min-height:450px;">
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
            <div class="row ">
                <div class="col-md-12 alert alert-danger">
                    <p>{{ $message ?? '' }}</p>
                </div>
            </div>
        @endif
        <form method="POST" id="frm2">
            @csrf
            <div class="form-row justify-content-end bg-light">
                    <div class="form-group col-md-3">
                        <br />
                        <input name='busqueda' id='busqueda' oninput="validarInput(this)" type="text" class="form-control" placeholder="CURP" value="{{old('curp')}}"/>
                        <pre id="resultado"></pre>
                    </div>
                    @if($permisos['alumnos-inscripcion-paso2'])
                        <div class="col-md-2"><br />
                            <button type="button" id="nuevo" class="btn">NUEVO ASPIRANTE</button>
                        </div>
                    @endif
                    <div class="col-md-2"><br />
                        <a class="btn" href="{{ route('alumnos.index') }}"> REGRESAR </a>
                    </div>
            </div>
        </form>
        <form method="POST" id="frm" enctype="multipart/form-data" style="width: 100%;">
            @csrf
            @if ($curp)
                <div class="col-lg-12 margin-tb">
                    <h5><b>DEL ASPIRANTE</b></h5>
                    <hr/>
                    <div class="form-row">
                        <div class="form-group col-md-3 form-inline mr-5" id="foto">
                            <div id="image-preview" class="mt-2 text-center">
                                <i id="camera-icon" class="fa fa-camera-retro fa-3x" style="vertical-align: center"></i>
                                <img id="selected-image" class="img-preview" src="{{$fotografia}}" alt="Vista previa">
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="fotografia" name="fotografia" onchange="fileValidation()">
                                <label class="custom-file-label" for="fotografia">Fotografía</label>
                            </div>
                        </div>
                        <div class="form-group col-md-8">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="curp" class="control-label">CURP</label>
                                    {!! Form::text('curp', $curp, ['id' => 'curp', 'class' => 'form-control', 'placeholder' => 'CURP', 'readonly' => 'true', 'style' => 'width: 300px;']) !!}
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
                                    <label for="apellido_materno" class="control-label">Pais de Nacimiento</label>
                                    {{-- {!! Form::select('pais_nacimiento', $paises, $datos_alfa->pais_nacimiento ?? '', ['id'=>'pais_nacimiento', 'class'=>'form-control mr-sm--2', 'placeholder' => '- SELECCIONAR -']) !!} --}}
                                    <select class="form-control" name="pais_nacimiento" id="pais_nacimiento">
                                        <option value="">SELECCIONE</option>
                                        @foreach ($paises as $pais)
                                            <option value="{{$pais->id}}"
                                                @if (!isset($datos_alfa->pais_nacimiento))
                                                    @if ($pais->id == '115')
                                                        selected
                                                    @endif
                                                @elseif($datos_alfa->pais_nacimiento == $pais->id)
                                                    selected
                                                @endif
                                                >{{$pais->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
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
                                <label>Estado civil:</label>
                                    {!! Form::select('estado_civil', $estado_civil, $ecivil, ['id'=>'estado_civil', 'class'=>'form-control mr-sm--2', 'placeholder' => '- SELECCIONAR -']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">

                        <div class="form-group col-md-5">
                            <label for="domicilio" class="control-label">Domicilio</label>
                            <input type="text" class="form-control" id="domicilio" name="domicilio" autocomplete="off" value="{{$domicilio}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="colonia" class="control-label">Colonia</label>
                            <input type="text" class="form-control" id="colonia" name="colonia" autocomplete="off" value="{{$colonia}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="cp" class="control-label">Código Postal</label>
                            <input type="text" class="form-control" id="cp" name="cp" value="{{$cp}}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="estado" class="control-label">Pais</label>
                            {{-- {!! Form::select('pais', $paises, $datos_alfa->pais ?? '', ['id'=>'pais', 'class'=>'form-control', 'placeholder'=>'- SELECCIONAR -']) !!} --}}
                            <select class="form-control" name="pais" id="pais">
                                <option value="">SELECCIONE</option>
                                @foreach ($paises as $pais)
                                    <option value="{{$pais->id}}"
                                        @if (!isset($datos_alfa->pais))
                                            @if ($pais->id == '115')
                                                selected
                                            @endif
                                        @elseif($datos_alfa->pais == $pais->id)
                                            selected
                                        @endif
                                        >{{$pais->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
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

                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label>Tel&eacute;fono Casa:</label>
                            <input id="telefono_casa" name="telefono_casa" class="form-control" type="text" value="{{$telefono_casa}}">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Tel&eacute;fono Celular:</label>
                            <input id="telefono_cel" name="telefono_cel" class="form-control" type="text" value="{{$telefono_cel}}">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Correo Electrónico:</label>
                            <input type="email" id="correo" name="correo" class="form-control"
                                placeholder="usuario@gmail.com" type="text" value="{{ $email }}" data-original-email="{{ $email }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Facebook:</label>
                            <input id="facebook" name="facebook" class="form-control" type="text" value="{{$face}}">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Twitter:</label>
                            <input id="twitter" name="twitter" class="form-control" type="text" value="{{$twitter}}">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Instagram:</label>
                            <input id="instagram" name="instagram" class="form-control" type="text" value="{{$instagram}}">
                        </div>
                        <div class="form-group col-md-3 mr-4">
                            <label>TikTok:</label>
                            <input id="tiktok" name="tiktok" class="form-control" type="text" value="{{$tiktok}}">
                        </div>
                        <div class="form-group col-md-5 pt-sm-2  pb-sm-3 pt-md-5">
                            <label class="mr-4"><input id="ninguna_redsocial" name="ninguna_redsocial" type="checkbox" value="true" @if ($redes) { checked } @endif>&nbsp;&nbsp;No tiene redes sociales</label>
                            <label><input id="recibir_publicaciones" name="recibir_publicaciones" type="checkbox" value="true"  @if ($publicaciones) { checked } @endif>&nbsp;&nbsp;¿Recibir publicaciones?</label>
                        </div>
                    </div>

                    {{-- Agregar ckeck bolsa de trabajo  --}}
                    <div class="d-inline-flex p-3 w-100 pl-5 mb-4" style="background-color: #f7d351;">
                        <b>
                            ¿Usted autoriza dar su número de celular para alguna oportunidad en la Bolsa de Trabajo? &nbsp;&nbsp;
                            <input class="check-input" id="chk_bolsa" name="chk_bolsa" type="checkbox" value="true" @isset($check_bolsa) @if ($check_bolsa) { checked } @endif @endisset />
                            &nbsp; SI
                        </b>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-2 col-sm-3">
                            <label><input id="lgbt" name="lgbt" type="checkbox" value="true" @if ($madre_soltera) { checked } @endif />&nbsp;&nbsp;LGBTTTI+</label>
                        </div>
                        <div class="form-group col-md-3 col-sm-5">
                            <label><input id="madre_soltera" name="madre_soltera" type="checkbox" value="true" @if ($madre_soltera) { checked } @endif />&nbsp;&nbsp;¿Es Madre Soltera?</label>
                        </div>
                        <div class="form-group col-md-2 col-sm-4">
                            <label><input id="inmigrante" name="inmigrante" type="checkbox" value="true" @if ($inmigra) { checked } @endif>&nbsp;&nbsp;¿Es Inmigrante?</label>
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                            <label><input id="familia_migrante" name="familia_migrante" type="checkbox" value="true" @if ($faminmigra) { checked } @endif />&nbsp;&nbsp;¿Tiene Familia Migrante?</label>
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                            {{ Form::select('etnia', $etnias, $etnia, ['id' => 'etnia', 'class' => 'form-control mr-sm-2', 'placeholder' => '--ETNIA--']) }}
                        </div>
                    </div>
                    <div>
                        <div class="form-row mb-5">
                            <h5><b>Seleccionar si pertenece a algún Grupo Vulnerable:</b></h5>
                        </div>
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
                    <h5><b>DE LA CAPACITACIÓN</b></h5>
                    </>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="ultimo_grado_estudios" class="control-label">ÚLTIMO GRADO DE ESTUDIOS:</label>
                            {!! Form::select('ultimo_grado_estudios', $grado_estudio, $escolaridad, ['id'=>'ultimo_grado_estudios', 'class'=>'form-control', 'placeholder'=>'- SELECCIONAR -']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            <label for="ultimo_grado_estudios" class="control-label">MEDIO DE CONFIRMACIÓN:</label>
                            {!! Form::select('medio_confirmacion', $medio_confirmacion, $confirmacion, ['id'=>'medio_confirmacion', 'class'=>'form-control', 'placeholder'=>'- SELECCIONAR -']) !!}
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

                    {{-- Formulario para alumno alfa --}}
                    {{-- Agregar un check de Alumno alfa para cuando se le de clic mostrar el formulario completo y si ya hay datos simplemente validar que se encuentre seleccionado osea mantener la seleccion y mostrar los datos quiza quitando la clase d-none de bootstrap --}}
                    <br>
                    <hr style="border: 2px solid rgb(123, 120, 120);">

                    {{-- Prueba de switch --}}
                    <div class="switch-container">
                        <label class="switch">
                            <input type="checkbox" id="toggleAcordeon" name="switch_alfa"
                            @if ($switch_alfa == true)
                                checked
                            @endif>
                            <span class="slider round"></span>
                        </label>
                        <span class="switch-text">Alumno Alfa</span>
                    </div>

                    <div id="contenedor_alfa" class="{{$clase_alfa}} mt-5">
                        <div class="form-row">
                            {{-- <div class="form-group col-md-4">
                                <label for="coordzona" class="control-label">Coordinación de Zona (Numero y Nombre):</label>
                                <input type="text" name="coordzona" id="coordzona" class="form-control" value="{{ $datos_alfa->coordzona ?? ''}}">
                            </div> --}}
                            <div class="form-group col-md-2">
                                <label for="fec_registro" class="control-label">Fecha de Registro:</label>
                                <input type="date" name="fec_registro" id="fec_registro" class="form-control" value="{{ $datos_alfa->fec_registro ?? ''}}">
                            </div>
                            {{-- <div class="form-group col-md-4">
                                <label for="dato_rfe" class="control-label">RFE (Anotar una vez que haya sido asignado):</label>
                                <input type="text" name="dato_rfe" id="dato_rfe" class="form-control" value="{{ $datos_alfa->dato_rfe ?? ''}}">
                            </div> --}}
                            <div class="form-group col-md-2">
                                <label for="" class="control-label">Entidad de nacimiento:</label>
                                {!! Form::select('entidad_naci', $estados, $datos_alfa->entidad_naci ?? '', ['id'=>'entidad_naci', 'class'=>'form-control', 'placeholder'=>'- SELECCIONAR -']) !!}
                                {{-- <input type="text" name="dato_rfe" id="dato_rfe" class="form-control" value=""> --}}
                            </div>
                        </div>

                        <div class="form-row mt-3">
                            <div class="form-inline col-md-2">
                                <label> ¿Habla español? <input id="check_habla_espa" name="check_habla_espa" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_habla_espa ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-5">
                                <label>¿Habla algun dialecto o lengua indigena? ¿Cual?  <input id="txt_dialecto" class="form-control ml-2" name="txt_dialecto" type="text" value="{{ $datos_alfa->txt_dialecto ?? '' }}"/></label>
                            </div>
                            <div class="form-inline col-md-5">
                                <label>Otro idioma adicional al español ¿Cual? <input id="txt_adicional_esp" class="form-control ml-2" name="txt_adicional_esp" type="text" value="{{ $datos_alfa->txt_adicional_esp ?? '' }}"/></label>
                            </div>
                        </div>

                        <div class="form-row mt-4">
                            <div class="form-group col-md-6">
                                <label>De acuerdo con su cultura, ¿Usted se considera indígena? <input id="check_indigena" name="check_indigena" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_indigena ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-group col-md-6">
                                <label>¿Usted se considera afromexicano(a) negro(a) o afrodescendiente <input id="check_afrodec" name="check_afrodec" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_afrodec ?? false) { checked } @endif/></label>
                            </div>
                        </div>

                        <br>
                        {{-- Datos domicilio --}}
                        <label for="txt_tipo_vialidad" class="font-weight-bold">Vialidad</label>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                {{-- <input type="text" name="txt_tipo_vialidad" id="txt_tipo_vialidad" class="form-control"
                                placeholder="Tipo: andador, avenida, boulevard, callejón, calle, carretera." value="{{ $datos_alfa->txt_tipo_vialidad ?? '' }}"> --}}
                                {!! Form::select('txt_tipo_vialidad', $vialidad, $datos_alfa->txt_tipo_vialidad ?? '', ['id'=>'txt_tipo_vialidad', 'class'=>'form-control mr-sm--2', 'placeholder' => '- SELECCIONAR -']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                <input id="txt_nom_vialidad" class="form-control" name="txt_nom_vialidad" placeholder="Nombre" type="text" value="{{ $datos_alfa->txt_nom_vialidad ?? '' }}"/>
                            </div>
                            <div class="form-group col-md-2">
                                <input id="txt_num_ext" class="form-control" name="txt_num_ext" placeholder="Núm. Exterior" type="text" value="{{ $datos_alfa->txt_num_ext ?? '' }}"/>
                            </div>
                            <div class="form-group col-md-2">
                                <input id="txt_num_int" class="form-control" name="txt_num_int" placeholder="Núm. Interior" type="text" value="{{ $datos_alfa->txt_num_int ?? '' }}"/>
                            </div>
                        </div>

                        <label for="txt_tipo_asentamiento" class="font-weight-bold">Asentamiento humano</label>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                {{-- <input type="text" name="txt_tipo_asentamiento" id="txt_tipo_asentamiento" class="form-control"
                                placeholder="Tipo: colonia, conjunto habitacional, ejido, fraccionamiento, rancho, zona, manzana, pueblo." value="{{ $datos_alfa->txt_tipo_asentamiento ?? '' }}"> --}}
                                {!! Form::select('txt_tipo_asentamiento', $asentamientos, $datos_alfa->txt_tipo_asentamiento ?? '', ['id'=>'txt_tipo_asentamiento', 'class'=>'form-control mr-sm--2', 'placeholder' => '- SELECCIONAR -']) !!}
                            </div>
                            <div class="form-group col-md-6">
                                <input id="txt_nom_asentamiento" class="form-control" name="txt_nom_asentamiento" placeholder="Nombre" type="text" value="{{ $datos_alfa->txt_nom_asentamiento ?? '' }}"/>
                            </div>
                        </div>

                        <label for="txt_tipo_entre_vialidad" class="font-weight-bold">Entre qué vialidad</label>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                {{-- <input type="text" name="txt_tipo_entre_vialidad" id="txt_tipo_entre_vialidad" class="form-control"
                                placeholder="Tipo" value="{{ $datos_alfa->txt_tipo_entre_vialidad ?? '' }}"> --}}
                                {!! Form::select('txt_tipo_entre_vialidad', $vialidad, $datos_alfa->txt_tipo_entre_vialidad ?? '', ['id'=>'txt_tipo_entre_vialidad', 'class'=>'form-control mr-sm--2', 'placeholder' => '- SELECCIONAR -']) !!}
                            </div>
                            <div class="form-group col-md-6">
                                <input id="txt_nom_entre_vialidad" class="form-control" name="txt_nom_entre_vialidad" placeholder="Nombre" type="text" value="{{ $datos_alfa->txt_nom_entre_vialidad ?? '' }}"/>
                            </div>
                        </div>

                        <label for="txt_Ytipo_entre_vialidad" class="font-weight-bold">Y qué vialidad</label>
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                {{-- <input type="text" name="txt_Ytipo_entre_vialidad" id="txt_Ytipo_entre_vialidad" class="form-control"
                                placeholder="Tipo" value="{{ $datos_alfa->txt_Ytipo_entre_vialidad ?? '' }}"> --}}
                                {!! Form::select('txt_Ytipo_entre_vialidad', $vialidad, $datos_alfa->txt_Ytipo_entre_vialidad ?? '', ['id'=>'txt_Ytipo_entre_vialidad', 'class'=>'form-control mr-sm--2', 'placeholder' => '- SELECCIONAR -']) !!}
                            </div>
                            <div class="form-group col-md-5">
                                <input id="txt_Ynom_entre_vialidad" class="form-control" name="txt_Ynom_entre_vialidad" placeholder="Nombre" type="text" value="{{ $datos_alfa->txt_Ynom_entre_vialidad ?? '' }}"/>
                            </div>
                            <div class="form-group col-md-2">
                                <input id="txt_Ycp_entre_vialidad" class="form-control" name="txt_Ycp_entre_vialidad" placeholder="Codigo Postal" type="text" value="{{ $datos_alfa->txt_Ycp_entre_vialidad ?? '' }}"/>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-inline col-md-3">
                                <label> Tiene equipo de cómputo <input id="check_equipo_computo" name="check_equipo_computo" class="check-input ml-3" type="checkbox" value="true"  @if ($datos_alfa->check_equipo_computo ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label> Tiene acceso a Internet <input id="check_acces_internet" name="check_acces_internet" class="check-input ml-3" type="checkbox" value="true"  @if ($datos_alfa->check_acces_internet ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="txt_correo_inea">Correo electrónico INEA </label>
                                <input id="txt_correo_inea" name="txt_correo_inea" class="form-control" type="email" value="{{ $datos_alfa->txt_correo_inea ?? '' }}"/>
                            </div>
                        </div>

                        <br>

                        <label for="" class="font-weight-bold">En su vida diaria, ¿Usted tiene dificultad para?</label>
                        <div class="form-row mt-3">
                            {{-- Pendiente para seguir agregregando los checksbox --}}
                            <div class="form-inline col-md-4">
                                <label>Caminar, subir o bajar <input id="check_difi_caminar" name="check_difi_caminar" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_difi_caminar ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-4">
                                <label>Oir, aún usando aparato auditivo <input id="check_difi_oir" name="check_difi_oir" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_difi_oir ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-4">
                                <label>Ver, aún usando lentes <input id="check_difi_ver" name="check_difi_ver" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_difi_ver ?? false) { checked } @endif/></label>
                            </div>

                        </div>
                        <div class="form-row mt-4">
                            <div class="form-inline col-md-4">
                                <label>Bañarse, vestirse o comer <input id="check_difi_vestir" name="check_difi_vestir" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_difi_vestir ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-4">
                                <label>Hablar o comunicarse <br>(por ejemplo entender o ser entendido por otros) <input id="check_difi_comunicar" name="check_difi_comunicar" class="check-input ml-1" type="checkbox" value="true" @if ($datos_alfa->check_difi_comunicar ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-4">
                                <label>Recordar o concentrarse <input id="check_difi_recordar" name="check_difi_recordar" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_difi_recordar ?? false) { checked } @endif/></label>
                            </div>
                        </div>
                        <div class="form-row mt-4">
                            <div class="form-inline col-md-3">
                                <label>¿Tiene algún problema o condición mental? (Autismo, sindrome de Down, esquizofrenia, etcétera) <input id="check_difi_mental" name="check_difi_mental" class="check-input ml-1" type="checkbox" value="true" @if ($datos_alfa->check_difi_mental ?? false) { checked } @endif/></label>
                            </div>
                        </div>

                        <br>

                        <label for="" class="mt-3 font-weight-bold">¿Tiene trabajo activo?</label>
                        <div class="form-row">
                            <div class="form-inline col-md-3">
                                <label>Jubilado/a o Pensionado/a <input id="check_jubilado" name="check_jubilado" class="check-input ml-3" type="checkbox" value="true"  @if ($datos_alfa->check_jubilado ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Desempleado/a <input id="check_desempleado" name="check_desempleado" class="check-input ml-3" type="checkbox" value="true"  @if ($datos_alfa->check_desempleado ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Estudiante <input id="check_estudiante" name="check_estudiante" class="check-input ml-3" type="checkbox" value="true"  @if ($datos_alfa->check_estudiante ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label for="">Otro</label>
                                <input id="txt_otro_trabajo" name="txt_otro_trabajo" class="form-control ml-md-3" type="text" value="{{ $datos_alfa->txt_otro_trabajo ?? '' }}"/>
                            </div>
                        </div>
                        <br>
                        <label for="" class="mt-2 font-weight-bold">Tipos de ocupación</label>
                        <div class="form-row d-flex justify-content-between">
                            <div class="form-inline col-md-3">
                                <label>Trabajador/a agropecuario <input id="check_trabajador" name="check_trabajador" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_trabajador ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Inspector/a supervisor/a <input id="check_inspector" name="check_inspector" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_inspector ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Artesano/a <input id="check_artesano" name="check_artesano" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_artesano ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Obrero/a <input id="check_obrero" name="check_obrero" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_obrero ?? false) { checked } @endif/></label>
                            </div>


                        </div>

                        <div class="form-row mt-3 d-flex justify-content-between">
                            <div class="form-inline col-md-3">
                                <label>Ayudante o similar <input id="check_ayudante" name="check_ayudante" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_ayudante ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Empleado/a de gobierno <input id="check_empleado" name="check_empleado" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_empleado ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Operador/a de transporte o maquinaria en movimiento <input id="check_operador" name="check_operador" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_operador ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Comerciante o vendedor <input id="check_vendedor" name="check_vendedor" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_vendedor ?? false) { checked } @endif/></label>
                            </div>

                        </div>

                        <div class="form-row mt-3 d-flex justify-content-between">
                            <div class="form-inline col-md-3">
                                <label>Trabajador/a del hogar <input id="check_hogar" name="check_hogar" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_hogar ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Protección o vigilancia <input id="check_vigilancia" name="check_vigilancia" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_vigilancia ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Quehaceres del hogar <input id="check_quehaceres" name="check_quehaceres" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_quehaceres ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Trabajador ambulante <input id="check_ambulante" name="check_ambulante" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_ambulante ?? false) { checked } @endif/></label>
                            </div>

                        </div>
                        <div class="form-row mt-4 d-flex">
                            <div class="form-inline col-md-3">
                                <label>Deportista <input id="check_deportista" name="check_deportista" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_deportista ?? false) { checked } @endif/></label>
                            </div>
                        </div>

                        <br>

                        <label for="" class="mt-4 font-weight-bold">Antecedentes escolares</label>
                        <div class="form-row d-flex justify-content-between">
                            <div class="form-inline col-md-3">
                                <label>Sin estudios <input id="check_sinestudios" name="check_sinestudios" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_sinestudios ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-1">
                                <label>Primaria <input id="check_ante_primaria" name="check_ante_primaria" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_ante_primaria ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-2">
                                <input id="txt_grado_primaria" name="txt_grado_primaria" class="form-control ml-md-3" type="text" placeholder="Grado" value="{{ $datos_alfa->txt_grado_primaria ?? '' }}"/>
                                {{-- <label> <input id="check_equipo_computo" name="check_equipo_computo" class="check-input ml-3" type="checkbox" value="true"/></label> --}}
                            </div>
                            <div class="form-inline col-md-1">
                                <label>Secundaria <input id="check_ante_secundaria" name="check_ante_secundaria" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_ante_secundaria ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-2">
                                <input id="txt_grado_secundaria" name="txt_grado_secundaria" class="form-control ml-md-3" type="text" placeholder="Grado" value="{{ $datos_alfa->txt_grado_secundaria ?? '' }}"/>
                                {{-- <label>Ayudante o similar <input id="check_equipo_computo" name="check_equipo_computo" class="check-input ml-3" type="checkbox" value="true"/></label> --}}
                            </div>
                        </div>

                        <br>

                        <label for="" class="mt-4 font-weight-bold">Nivel al que ingresa</label>
                        <div class="form-row d-flex justify-content-between mt-3">
                            <div class="form-inline col-md-3">
                                <label>Alfabetización <input id="check_nivel_alfa" name="check_nivel_alfa" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_nivel_alfa ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Primaria <input id="check_nivel_primaria" name="check_nivel_primaria" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_nivel_primaria ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Primaria 10-14 <input id="check_nivel_primaria10" name="check_nivel_primaria10" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_nivel_primaria10 ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Secundaria <input id="check_nivel_secundaria" name="check_nivel_secundaria" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_nivel_secundaria ?? false) { checked } @endif/></label>
                            </div>
                        </div>

                        <div class="form-row d-flex justify-content-between mt-3">
                            <div class="form-inline col-md-3">
                                <label>Ejercicio diagnóstico (Alfabetización) <input id="check_eje_diag" name="check_eje_diag" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_eje_diag ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Examen diagnóstico <input id="check_exam_diag" name="check_exam_diag" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_exam_diag ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Reconocimiento de saberes <input id="check_reco_saberes" name="check_reco_saberes" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_reco_saberes ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Atención educativa <input id="check_aten_educ" name="check_aten_educ" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_aten_educ ?? false) { checked } @endif/></label>
                            </div>
                        </div>

                        <div class="form-row d-flex justify-content-between mt-3">
                            <div class="form-inline col-md-3">
                                <label>Hispanohablante <input id="check_hispanohabla" name="check_hispanohabla" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_hispanohabla ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <label>Hablante de lengua indigena <input id="check_hablante_lengua" name="check_hablante_lengua" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_hablante_lengua ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-6">
                                <input id="txt_hablante_lengua" name="txt_hablante_lengua" class="form-control" type="text" placeholder="Entia/Lengua" value="{{ $datos_alfa->txt_hablante_lengua ?? '' }}"/>
                            </div>
                        </div>

                        <br>

                        <label for="" class="mt-4 font-weight-bold">¿Que le motiva a estudiar?</label>
                        <div class="form-row d-flex justify-content-start mt-3">
                            <div class="form-inline col-md-4">
                                <label>Obtener el certificado de Primaria/Secundaria <input id="check_motiv_certificado" name="check_motiv_certificado" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_motiv_certificado ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-4">
                                <label>Continuar la Educación Media Superior <input id="check_motiv_continuar" name="check_motiv_continuar" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_motiv_continuar ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-2">
                                <label>Obtener un empleo <input id="check_motiv_obtempleo" name="check_motiv_obtempleo" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_motiv_obtempleo ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-2">
                                <label>Mejorar mis condiciones laborales <input id="check_motiv_condlaborales" name="check_motiv_condlaborales" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_motiv_condlaborales ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-4">
                                <label>Ayudar a mis hijos/nietos con las tareas <input id="check_motiv_ayudar" name="check_motiv_ayudar" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_motiv_ayudar ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-4">
                                <label>Superacion personal <input id="check_motiv_superacion" name="check_motiv_superacion" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_motiv_superacion ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-3">
                                <input id="txt_motiv_otro" name="txt_motiv_otro" class="form-control" type="text" placeholder="Otro" value="{{ $datos_alfa->txt_motiv_otro ?? '' }}"/>
                            </div>
                        </div>
                        <br>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="num_hijos" class="control-label font-weight-bold">Numero de Hijos</label>
                                <input type="number" name="num_hijos" id="num_hijos" class="form-control" value="{{ $datos_alfa->num_hijos ?? ''}}">
                            </div>
                        </div>

                        <label for="" class="mt-4 font-weight-bold">¿Como se enteró de nuestros servicios?</label>
                        <div class="form-row d-flex justify-content-start mt-3">
                            <div class="form-inline col-md-4">
                                <label>Difusión de INEA <input id="check_difu_inea" name="check_difu_inea" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_difu_inea ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-4">
                                <label>Invitación personal <input id="check_invit_personal" name="check_invit_personal" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_invit_personal ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-4">
                                <input id="txt_enterar_otro" name="txt_enterar_otro" class="form-control" type="text" placeholder="Otro" value="{{ $datos_alfa->txt_enterar_otro ?? '' }}"/>
                            </div>
                        </div>

                        <br>

                        <div class="form-row mt-3 d-flex justify-content-between">
                            <div class="form-group col-md-6">
                                <label for="" class="font-weight-bold">Modelo</label>
                                <select id="modelo" class="form-control" name="modelo" onchange="updateEtapaEB()">
                                    <option value="">- SELECCIONAR -</option>
                                    @foreach ($cat_modelo as $key => $item)
                                        <option value="{{ $item }}" @if (isset($datos_alfa->modelo) && $datos_alfa->modelo == $item) selected @endif>{{ $key }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="txt_etapaeb" class="font-weight-bold">Etapa EB.</label>
                                <input id="txt_etapaeb" name="txt_etapaeb" class="form-control" type="text" readonly value="{{ $datos_alfa->txt_etapaeb ?? '' }}"/>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="" class="font-weight-bold">Subproyecto</label>
                                <input id="txt_subproyecto" name="txt_subproyecto" class="form-control" type="text" readonly value="CHIAPAS PUEDE INSTITUTOS"/>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="" class="font-weight-bold">Dependencia</label>
                                <input id="txt_dependencia" name="txt_dependencia" class="form-control" readonly type="text" value="ICATECH"/>
                            </div>
                        </div>

                        {{-- <div class="form-row mt-3 d-flex justify-content-between">
                            <div class="form-inline col-md-4">
                                <label>Confirmado <input id="check_confirmado" name="check_confirmado" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_confirmado ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-4">
                                <label>Reingreso <input id="check_reingreso" name="check_reingreso" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_reingreso ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-4">
                                <label>Expediente Validado <input id="check_expevalid" name="check_expevalid" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_expevalid ?? false) { checked } @endif/></label>
                            </div>
                        </div> --}}

                        {{-- <div class="form-row mt-4 d-flex justify-content-between">
                            <div class="form-group col-md-6">
                                <label for="" class="font-weight-bold">Observaciones</label>
                                <textarea name="area_observa" id="area_observa" cols="100" rows="3">{{$datos_alfa->area_observa ?? ''}}</textarea>
                            </div>
                        </div> --}}

                        <br>

                        <label for="" class="font-weight-bold">Documentación de la persona beneficiaria</label>
                        <div class="form-row d-flex justify-content-start">
                            <div class="form-inline col-md-4">
                                <label>Fotografia <input id="check_doc_fotografia" name="check_doc_fotografia" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_doc_fotografia ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-4">
                                <label>Documento legal equivalente (extranjeros) <input id="check_doc_legal" name="check_doc_legal" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_doc_legal ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-4">
                                <label>Ficha signalética (CERESO) <input id="check_doc_ficha" name="check_doc_ficha" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_doc_ficha ?? false) { checked } @endif/></label>
                            </div>
                        </div>

                        <br>

                        <label for="" class="font-weight-bold mt-3">Documentos Probatorios / Constancias de capacitación.</label>
                        <div class="form-row d-flex justify-content-start">
                            <div class="form-inline col-md-3">
                                <label>Certificado de primaria <input id="check_doc_certi" name="check_doc_certi" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_doc_certi ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-2">
                                <label>Boletas de primaria <input id="check_boletas_primaria" name="check_boletas_primaria" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_boletas_primaria ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-2">
                                <input id="txt_boletas_primaria" name="txt_boletas_primaria" class="form-control" type="text" placeholder="Grado" value="{{ $datos_alfa->txt_boletas_primaria ?? '' }}"/>
                            </div>
                            <div class="form-inline col-md-2">
                                <label>Boletas de secundaria <input id="check_boletas_secu" name="check_boletas_secu" class="check-input ml-3" type="checkbox" value="true" @if ($datos_alfa->check_boletas_secu ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-2">
                                <input id="txt_boletas_secu" name="txt_boletas_secu" class="form-control" type="text" placeholder="Grado" value="{{ $datos_alfa->txt_boletas_secu ?? '' }}"/>
                            </div>
                        </div>
                        <div class="form-row d-flex justify-content-start mt-4">
                            <div class="form-inline col-md-3">
                                <label>Informe de calificaciones INEA <input id="check_informe_cali" name="check_informe_cali" class="check-input ml-2" type="checkbox" value="true" @if ($datos_alfa->check_informe_cali ?? false) { checked } @endif/></label>
                            </div>
                            <div class="form-inline col-md-8">
                                <label for="">Constancias de Capacitación: </label>
                                <input id="txt_num_const_cap" name="txt_num_const_cap" class="form-control mx-3" type="text" placeholder="Numero" value="{{ $datos_alfa->txt_num_const_cap ?? '' }}"/>
                                <input id="txt__const_cap" name="txt_hr_const_cap" class="form-control" type="text" placeholder="Horas" value="{{ $datos_alfa->txt_hr_const_cap ?? '' }}"/>
                            </div>
                        </div>

                        <br>

                        {{-- <label for="" class="font-weight-bold mt-3">Cotejo de Documentos impresos mostrados por la persona beneficiaria</label>
                        <div class="form-row d-flex justify-content-start mt-2">
                            <div class="form-group col-md-6">
                                <label for="">Nombre completo de quien cotejó los documentos:</label>
                                <input id="text_cotejo_doc" name="text_cotejo_doc" class="form-control" type="text" placeholder="Nombre completo" value="{{ $datos_alfa->text_cotejo_doc ?? '' }}"/>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Fecha de cotejo de documentos:</label>
                                <input id="txt_fecha_cotejo" name="txt_fecha_cotejo" class="form-control" type="date" placeholder="Ingresar Fecha" value="{{ $datos_alfa->txt_fecha_cotejo ?? '' }}"/>
                            </div>
                        </div>
                        <br> --}}

                        <label for="" class="font-weight-bold mt-3">Información de la Unidad Operativa</label>
                        <div class="form-row d-flex justify-content-start mt-2">
                            <div class="form-group col-md-6">
                                <label for="">Unidad Operativa</label>
                                <input id="txt_unidad_operativa" name="txt_unidad_operativa" class="form-control" type="text" placeholder="Ingresa la Unidad Operativa" value="{{ $datos_alfa->txt_unidad_operativa ?? '' }}"/>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Círculo de estudio</label>
                                <input id="txt_circulo_estudio" name="txt_circulo_estudio" class="form-control" type="text" placeholder="Ingresa el Círculo de Estudio" value="{{ $datos_alfa->txt_circulo_estudio ?? '' }}"/>
                            </div>
                        </div>

                        <br>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="">Fecha de llenado del registro</label>
                                <input id="txt_fecha_llenado" name="txt_fecha_llenado" class="form-control" type="date" placeholder="" value="{{ $datos_alfa->txt_fecha_llenado ?? '' }}"/>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="">Nombre completo de la persona beneficiaria del INEA</label>
                                <input id="txt_persona_beneficiaria" name="txt_persona_beneficiaria" class="form-control" type="text" placeholder="" value="{{ $datos_alfa->txt_persona_beneficiaria ?? '' }}"/>
                            </div>
                            {{-- <div class="form-group col-md-4">
                                <label for="">Nombre completo del padre o tutor (En caso de Inscripción al MEVyT 10-14)</label>
                                <input id="txt_nom_tutor" name="txt_nom_tutor" class="form-control" type="text" placeholder="" value="{{ $datos_alfa->txt_nom_tutor ?? '' }}"/>
                            </div> --}}
                            {{-- <div class="form-group col-md-4">
                                <label for="">Nombre completo de la figura que incorpora</label>
                                <input id="txt_nom_figura" name="txt_nom_figura" class="form-control" type="text" placeholder="" value="{{ $datos_alfa->txt_nom_figura ?? '' }}"/>
                            </div> --}}
                            {{-- <div class="form-group col-md-4">
                                <label for="">Nombre completo del Coordinador de Zona</label>
                                <input id="txt_nom_coordinador" name="txt_nom_coordinador" class="form-control" type="text" placeholder="" value="{{ $datos_alfa->txt_nom_coordinador ?? '' }}"/>
                            </div> --}}
                            {{-- <div class="form-group col-md-4">
                                <label for="">Nombre completo del Responsable de Acreditación de la Coordinación de Zona</label>
                                <input id="txt_nom_responsable_zona" name="txt_nom_responsable_zona" class="form-control" type="text" placeholder="" value="{{ $datos_alfa->txt_nom_responsable_zona ?? '' }}"/>
                            </div> --}}
                            <div class="form-group col-md-4">
                                <label for="">Nombre completo de la persona que capturó</label>
                                <input id="txt_nom_capturista" name="txt_nom_capturista" class="form-control" type="text" placeholder="" value="{{ $datos_alfa->txt_nom_capturista ?? '' }}"/>
                            </div>
                        </div>

                    </div>
                    <hr style="border: 2px solid rgb(123, 120, 120);">
                    {{-- Fin formulario Alfa --}}

                    <div class="form-row p-0">
                            <table class="table table-striped" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-left" style="width: 1%;"><h5><b>REQUISITOS</b></h5>  </th>
                                        <th scope="col" class="text-left" style="width: 350px;">Seleccionar documento</th>
                                        <th scope="col" class="text-left" style="width: 30%;">Fecha de Expedición o Vigencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-right"><input id="chk_curp" name="chk_curp" type="checkbox" value="true" @isset($requisitos) @if ($requisitos->chk_curp) { checked } @endif @endisset/></td>
                                        <td><b>1. </b> CURP</td>
                                        <td><input type="date" name="fecha_expedicion_curp" id="fecha_expedicion_curp" style="width: 150px;"  class="form-control" @isset($requisitos) value="{{$requisitos->fecha_expedicion_curp}}" @endisset></td>
                                    </tr>
                                    <tr>

                                        <td class="text-right"><input id="chk_acta" name="chk_acta" type="checkbox" value="true" @isset($requisitos) @if ($requisitos->chk_acta_nacimiento) { checked } @endif @endisset/></td>
                                        <td><label><b>2. </b> ACTA DE NACIMIENTO</label></td>
                                        <td><input type="date" name="fecha_expedicion_acta_nacimiento" class="form-control" style="width: 150px;" id="fecha_expedicion_acta_nacimiento" @isset($requisitos) value="{{$requisitos->fecha_expedicion_acta_nacimiento}}" @endisset></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><input id="chk_escolaridad" name="chk_escolaridad" type="checkbox" value="true" @isset($requisitos) @if ($requisitos->chk_escolaridad) { checked } @endif @endisset/></td>
                                        <td><b>3. </b> &Uacute;LTIMO GRADO DE ESTUDIOS</td>
                                        <td> </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><input id="chk_comprobante_migratorio" name="chk_comprobante_migratorio" type="checkbox" value="true" @isset($requisitos) @if ($requisitos->chk_comprobante_migracion) { checked } @endif @endisset/></td>
                                        <td><b>4. </b> COMPROBANTE MIGRATORIO</td>
                                        <td><input type="date" name="fecha_vigencia_migratorio" id="fecha_vigencia_migratorio" style="width: 150px;"  class="form-control" @isset($requisitos) value="{{$requisitos->fecha_vigencia_migratorio}}" @endisset></td>
                                    </tr>
                                <tr>
                                    <td class="text-right"><input id="chk_ficha_cerss" name="chk_ficha_cerss" type="checkbox" value="true" @if ($ficha_cerss) { checked } @endif/></td>
                                    <td><b>5. </b> FICHA CERSS</td>
                                    <td></td>
                                </tr>
                                <tbody>
                            </table>
                        </div>
                        <div class="form-row p-2 bg-light justify-content-center align-items-center">
                            <div class="col-md-9">
                                <label for="customFile" class="form-label mt-2 align-items-end mr-2">
                                    Adjuntar un solo PDF los requisitos en el orden especificado:
                                </label>
                                <div class="col-md-6 d-inline-flex p-0">
                                    <div class="custom-file col-md-11">
                                        <input type="file" class="custom-file-input" id="customFile" name="customFile" onchange="fileValidationpdf()">
                                        <label class="custom-file-label" for="customFile">Seleccionar Archivo</label>
                                    </div>
                                    @isset($requisitos)
                                        @if($requisitos->documento)
                                            <a class="nav-link pt-0 col-md-1"  href="{{ $requisitos->documento }}" target="_blank">
                                                <i class="far fa-file-pdf text-danger icon-size" title="DESCARGAR PDF DE REQUISITOS."></i>
                                            </a>
                                        @endif
                                    @else
                                        <i  class="far fa-file-pdf text-muted col-md-1 icon-size"  title='ARCHIVO NO DISPONIBLE.'></i>
                                    @endisset
                                </div>
                                <div id="pdfPreview" style="margin-top: 1rem;"></div>
                            </div>
                            <div class="col-md-2 p-0">
                                <a href="https://www.ilovepdf.com/es/unir_pdf" class="btn btn-white text-primary" target="_blank" >
                                    Fusionar PDFs<i class="fas fa-external-link-alt ml-2"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <h5><b>
                            ¿Está empleado el Aspirante?  &nbsp;
                            <input type="checkbox" id="trabajo" name="trabajo" value="true" @if ($empleado) { checked } @endif>
                            SI
                            </b></h5>
                        </div>
                        {{-- <div class="form-group col-md-4">
                            <label><input type="checkbox" id="funcionario" name="funcionario" value="true">&nbsp;&nbsp;¿El aspirante es un servidor público?</label>
                        </div> --}}
                    </div>
                    <hr/>
                    <div id="content" style="display:none">
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
                    <div>
                        <div class="form-row">
                            <div class="form-group col-md-12 form-inline">
                                <h5><b>
                                ¿El Aspirante pertenece a algún CERSS? &nbsp;
                                <input type="checkbox" id="cerss_chk" name="cerss_chk" value="true" @if ($cerss) { checked } @endif>
                                SI
                                {{ Form::text('num_expediente_cerss', $nexpediente_cerss, ['id' => 'datos_cerss', 'class' => 'form-control ml-3','size' => '50', 'placeholder' => 'NÚMERO DE EXPEDIENTE',  'style' => 'display: none;']) }}
                                </b></h5>
                            </div>
                        </div>
                        <hr/>

                    </div>
                    @if($permisos['alumnos-inscripcion-paso2'])
                        <div class="form-row justify-content-end ">
                            <button type="submit" class="btn" id="update" >GUARDAR CAMBIOS</button>
                        </div>
                    @endif
                </div>
            @endif
            <input type="hidden" name="aspirante_id" value="{{ $aspiranteId }}">
        </form>
    </div>
    @section('script_content_js')
        <script type="text/javascript">
            // Cache en memoria para no repetir peticiones
            let emailCheckCache = {
                value: null, // último correo validado
                isUnique: null // resultado booleano de la última validación
            };
            $(document).ready(function(){
                $("#medio_confirmacion" ).change(function(){
                    switch($("#medio_confirmacion" ).val()){
                        case "WHATSAPP":
                            if(!$("#telefono_cel" ).val()) alert("FAVOR DE INGRESAR EL NÚMERO DE CELULAR.")
                        break;
                        case "MENSAJE DE TEXTO":
                            if(!$("#telefono_cel" ).val()) alert("FAVOR DE INGRESAR EL NÚMERO DE CELULAR.")
                        break;
                        default:
                            if(!$("#correo" ).val()) alert("FAVOR DE INGRESAR EL CORREO ELECTRÓNICO.")
                        break;
                    }

                });


                $("#nuevo").click(function(){ $('#frm2').attr({'action':"{{route('alumnos.valid')}}",'target':'_self'}); $('#frm2').submit(); });
                $("#update").click(function(){
                    //Validar check bolsa de trabajo
                    let telefonoCel  = $('#telefono_cel').val();
                    if( $('#chk_bolsa').is(':checked') && telefonoCel.length != 10) {
                        alert("Usted activó la casilla de bolsa de trabajo.\nPor lo tanto, debe agregar un número de teléfono.");
                        return false;
                    }

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

                // Supongamos que tienes algo así en global o en tu script:
                // const emailCheckCache = { value: "", isUnique: null };
                // y optional: const originalEmail = "correo@que.ya.existe"; // en modo edición

                $.validator.addMethod("emailUniqueCached", function (value, element) {
                    const email = $.trim(value);

                    // 1) Si el campo está vacío → NO validar unicidad
                    if (email === "") {
                        return true;
                    }

                    // 2) Si estamos en edición y el correo no cambió,
                    //    lo dejamos pasar sin checar unicidad.
                    //    Puedes usar data-original-email o una variable global.
                    const originalEmail =
                        $(element).data("original-email") || window.originalEmail || "";

                    if (originalEmail && email === originalEmail) {
                        // Mismo correo que ya tenía el registro: no se dispara la validación de unicidad
                        return true;
                    }

                    // 3) Si el correo actual NO es el mismo que está cacheado,
                    //    significa que todavía no tenemos información sobre este valor.
                    //    No bloqueamos hasta que el AJAX actualice el cache.
                    if (emailCheckCache.value !== email) {
                        return true;
                    }

                    // 4) Si el cache aún no sabe si es único (null), tampoco bloqueamos.
                    if (emailCheckCache.isUnique === null) {
                        return true;
                    }

                    // 5) Decisión final: solo si el cache dice que NO es único se bloquea.
                    return emailCheckCache.isUnique === true;

                }, "Ya existe un aspirante con este correo.");



                $.validator.addMethod("pdfOnly", function (value, element) {

                    // Si no hay archivo, la validación pasa (a menos que el campo sea required)
                    if (element.files.length === 0) return true;

                    const file = element.files[0];
                    const fileName = file.name.toLowerCase();
                    const mime = file.type;

                    const isPDFext = fileName.endsWith(".pdf");
                    const isPDFmime =
                        mime === "application/pdf" ||
                        mime === ""; // algunos navegadores no mandan mime

                    return isPDFext && isPDFmime;

                }, "El archivo debe ser un PDF válido.");


                // Solo valida tamaño si hay archivo
                $.validator.addMethod("maxSize", function (value, element, param) {
                    // 👈 AQUÍ EL CAMBIO IMPORTANTE
                    if (!element.files || element.files.length === 0) return true;

                    return element.files[0].size <= param;
                }, "El archivo excede el tamaño permitido.");


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
                        },
                        // 👇 sin remote, sólo nuestra regla con cache
                        correo: {
                            emailUniqueCached: true,
                            email: true
                        },
                        customFile: {
                            pdfOnly: true,
                            maxSize: 5 * 1024 * 1024 // 5MB
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
                        },
                        correo: {
                            emailUniqueCached: "Ya existe un aspirante con este correo.",
                            email: "Ingresa un correo válido."
                        },
                        customFile: {
                            pdfOnly: "Solo se permiten archivos PDF (.pdf).",
                            maxSize: "El archivo debe pesar menos de 5MB."
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

                //Validacion de alumno alfa
                const toggleSwitch = document.getElementById('toggleAcordeon');
                const contenedorAlfa = document.getElementById('contenedor_alfa');

                toggleSwitch.addEventListener('change', function () {
                    if (this.checked) {
                        contenedorAlfa.classList.remove('d-none'); // Mostrar div
                    } else {
                        contenedorAlfa.classList.add('d-none'); // Ocultar div
                    }
                });

                $('#correo').on('blur', function() {

                    const inputEl = this;
                    const email = $.trim(inputEl.value);
                    const validator = $('#frm').data('validator');

                    // ---------------------------------------------
                    // 1) Campo vacío → resetear y validar como OK
                    // ---------------------------------------------
                    if (!email) {
                        emailCheckCache = {
                            value: "",
                            isUnique: null
                        };
                        if (validator) validator.element(inputEl);
                        return;
                    }

                    // ---------------------------------------------
                    // 2) Validar sintaxis básica de email
                    // ---------------------------------------------
                    const emailMethod = $.validator?.methods?.email;
                    if (validator && typeof emailMethod === "function") {

                        const isValidFormat = emailMethod.call(validator, email, inputEl);

                        if (!isValidFormat) {
                            // Mantener el cache pero marcando que no sabemos unicidad
                            emailCheckCache = {
                                value: email,
                                isUnique: null
                            };

                            validator.showErrors({
                                correo: validator.settings.messages?.correo?.email ||
                                    'Ingrese un correo electrónico válido.'
                            });

                            return;
                        }
                    }

                    // ---------------------------------------------
                    // 3) Si ya lo validamos y tenemos resultado → reutilizar cache
                    // ---------------------------------------------
                    if (emailCheckCache.value === email &&
                        emailCheckCache.isUnique !== null) {

                        if (validator) validator.element(inputEl);
                        return;
                    }

                    // ---------------------------------------------
                    // 4) Empezar validación nueva → marcar cache como en proceso
                    // ---------------------------------------------
                    emailCheckCache = {
                        value: email,
                        isUnique: null
                    };

                    // ---------------------------------------------
                    // 5) Hacer AJAX
                    // ---------------------------------------------
                    $.ajax({
                        url: "{{ route('aspirantes.checkEmail') }}",
                        type: "GET",
                        dataType: "json",
                        data: {
                            correo: email,
                            aspirante_id: "{{ $alumno->id ?? '' }}"
                        }

                    }).done(function(resp) {

                        // Aquí ajusta según tu API
                        // Si tu API devuelve: { exists: true }
                        const exists = resp.exists === true;

                        emailCheckCache = {
                            value: email,
                            isUnique: !exists   // true si NO existe (único)
                        };

                        if (validator) validator.element(inputEl);

                    }).fail(function() {

                        emailCheckCache = {
                            value: email,
                            isUnique: null
                        };

                        if (validator) {
                            validator.showErrors({
                                correo: 'No se pudo validar el correo en este momento.'
                            });
                        }

                    });
                });

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
                var previewContainer = document.getElementById('pdfPreview');

                // Limpia la vista previa anterior
                previewContainer.innerHTML = '';

                // 1) Verificar que se haya seleccionado un archivo
                if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                alert('Por favor selecciona un archivo.');
                return false;
                }

                var file = fileInput.files[0];
                var fileName = file.name || '';
                var fileSize = file.size || 0;
                var maxSize = 5 * 1024 * 1024; // 5 MB en bytes
                var allowedExtensions = /\.pdf$/i;

                // 2) Validar extensión por nombre
                if (!allowedExtensions.test(fileName)) {
                alert('Por favor solo carga archivos PDF (.pdf).');
                fileInput.value = '';
                return false;
                }

                // 3) Validar tipo MIME cuando está disponible
                // Algunos navegadores ponen file.type = "application/pdf" para PDFs
                if (file.type && file.type !== 'application/pdf') {
                alert('El archivo no parece ser un PDF válido.');
                fileInput.value = '';
                return false;
                }

                // 4) Validar tamaño máximo
                if (fileSize > maxSize) {
                alert('El archivo debe pesar menos de 5MB.');
                fileInput.value = '';
                return false;
                }

                // 5) Si todo está bien, mostrar vista previa del PDF
                if (window.FileReader) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    // e.target.result es un DataURL (data:application/pdf;base64,...)
                    var embedHtml =
                    '<embed src="' +
                    e.target.result +
                    '" type="application/pdf" width="100%" height="500px" />';

                    previewContainer.innerHTML = embedHtml;
                };

                reader.readAsDataURL(file);
                } else {
                // Navegadores muy viejos que no soportan FileReader
                previewContainer.innerHTML =
                    '<p>Tu navegador no soporta vista previa, pero el archivo es válido.</p>';
                }

                return true;
            }


            function fileValidation() {
                const fileInput = document.getElementById('fotografia');
                const file = fileInput.files[0];
                const imagePreview = document.getElementById('selected-image');
                const cameraIcon = document.getElementById('camera-icon');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                        cameraIcon.style.display = 'none'; // Ocultar el ícono
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.style.display = 'none';
                    cameraIcon.style.display = 'block';
                }
            }

            //Para rellenar campo al seleccionar el select modelo
            function updateEtapaEB() {
                const select = document.getElementById('modelo');
                const selectedText = select.options[select.selectedIndex].text;
                const parts = selectedText.split(' / ');
                const secondPart = parts[1] || '';
                document.getElementById('txt_etapaeb').value = secondPart;
            }

            /* SOLO SI SE AUTORIZA LA CARGA Y VISUALIZACIÓN DE FOTOGRAFÍA
            document.addEventListener('DOMContentLoaded', function () {
                const imagePreview = document.getElementById('selected-image');
                const cameraIcon = document.getElementById('camera-icon');

                // Verifica si la imagen tiene una URL definida (esto cubre el caso de imagen precargada)
                if (imagePreview.src) {
                    imagePreview.style.display = 'block'; // Mostrar la imagen
                    cameraIcon.style.display = 'none'; // Ocultar el ícono
                } else {
                    imagePreview.style.display = 'none'; // Ocultar la imagen
                    cameraIcon.style.display = 'block'; // Mostrar el ícono
                }
            });
            */




            /* EVALUANDO PARA SER SE LIMINADO
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
            }*/
        </script>
    @endsection
@endsection
