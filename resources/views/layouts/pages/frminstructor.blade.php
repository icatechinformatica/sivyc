<!-- Creado por Orlando Chávez orlando@sidmac.com-->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Instructor | Sivyc Icatech')
@section('content')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}

        label.onpoint{
            cursor:pointer;
        }
        #center {
            vertical-align:middle;
            text-align:center;
            padding:0px;
        }

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

    </style>
    <div class="card-header">
        Registro Instructor
    </div>
    <div class="card card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif
        <form action="{{ url('/instructor/guardar') }}" method="post" id="reginstructor" enctype="multipart/form-data">
            @csrf
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" id="toggleAcordeon">
                    <span class="slider round"></span>
                </label>
                <span class="switch-text">Instructor Alfa</span>
            </div>

            <!-- Acordeón -->
            <div id="acordeonInstructor" class="panel-group acordeon-borde" style="display: none; border: 3px solid black;">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                        <p data-toggle="collapse" href="#collapse1">Información del Instructor Alfa</p>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="form-row">
                                <label class=" col-form-label">Hispanohablante:</label>
                                <div class="col-sm-1" style="padding-top: 9px;">
                                    <label class="radio-inline">
                                    <input type="radio" name="hispanohablante" value="si"> Sí
                                    </label>&nbsp;&nbsp;
                                    <label class="radio-inline">
                                    <input type="radio" name="hispanohablante" value="no"> No
                                    </label>
                                </div>
                                <!-- Lengua indígena -->
                                <label class="col-form-label">Lengua indígena:</label>
                                <div class="col-sm-2">
                                    <input id="lengua_indigena" name="lengua_indigena" type="text" class="form-control" placeholder="Especificar">
                                </div>
                                <!-- Etnia/Lengua (MIB) -->
                                <label class="col-form-label">Etnia / Lengua (MIB):</label>
                                <div class="col-sm-2">
                                    <input id="etnia" name="etnia" type="text" class="form-control" placeholder="Especificar">
                                </div>
                                <!-- Número de hijos -->
                                <label class="col-form-label">N° de Hijos:</label>
                                <div class="col-sm-1">
                                    <input id="hijos" name="hijos" type="text" class="form-control" maxlength="3" placeholder="0">
                                </div>
                            </div>
                            <!-- Subproyecto -->
                            @php
                                $puestos = ['Seleccione Puesto','Titular','Lider Comunitario','Conscriptos','Recién Egresado','Integrante de Familia','Promotora','Efectivos','Educación Indigena','En Servicio','Jubilado'];
                                $sinOcupacion = ['Seleccione','Estudiante','Pensionado','Desempleado'];
                                $conOcupacion = ['Seleccione','Trabajador Agropecuario','Inspector o Supervisor','Artesano u Obrero','Operador de Maquinaria Fija','Ayudante o Similar','Operador de Trans. o Maq. Mov.',
                                'Jefe de Actividades Administrativas','Trabajador Administrativo','Comerciante o Dependiente','Trabajador Ambulante','Trabajador en serv. al púb. o pers.','Trabajador Domestico',
                                'Protección o Vigilante','Personas dedicadas quehaceres hogar','Funcionario o Directivo','Profesionista','Empleado de Gobierno'];
                                $vialidad = ['SELECCIONE TIPO','AMPLIACIÓN','ANDADOR','AVENIDA','BOULEVARD','CALLE','CALLEJON','CALZADA','CERRADA','CIRCUITO','CIRCUNVALACIÓN','CONTINUACIÓN','CORREDOR','DIAGONAL','EJE VIAL','PASAJE','PEATONAL',
                                'PERIFERICO','PRIVADA','PROLONGACIÓN','RETORNO','VIADUCTO','CARRETERA','CAMINO','BRECHA','TERRACERIA','VEREDA'];
                                $asentamientos = ['SELECCIONE','AEROPUERTO','AMPLIACIÓN','BARRIO','CANTON','CIUDAD','CIUDAD INDUSTRIAL','COLONIA','CONDOMINIO','CONJUNTO HABITACIONAL','CORREDOR INDUSTRIAL','COTO','CUARTEL','EJIDO','EXHACIENDA','FRACCION','FRACCIONAMIENTO',
                                'GRANJA','HACIENDA','INGENIO','MANZANA','PARAJE','PARQUE INDSUTRIAL','PRIVADA','PROLONGACIÓN','PUEBLO','PUERTO','RANCHERIA','RANCHO','REGION','RESIDENCIAL','RINCONADA','SECCIÓN','SECTOR','SUPERMANZANA','UNIDAD',
                                'UNIDAD HABITACIONAL','VILLA','ZONA FEDERAL','ZONA INDUSTRIAL','ZONA MILITAR','ZONA NAVAL'];
                            @endphp
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Subproyecto:</strong></label>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="subproyecto[]" value="chiapas puede" {{--onclick="puestoOnOff(this, 'otrosub_puesto')"--}} checked>Chiapas Puede</label>
                                            {{-- <input type="text" class="form-control col-md-4" hidden name="otrosub" id="otrosub"> --}}
                                            <select class="form-control col-md-4" name="chiapas_puede_puesto" id="chiapas_puede_puesto">
                                                @foreach ($puestos as $puesto)
                                                    {{-- <option value="{{$puesto}}">{{$puesto}}</option> --}}
                                                @endforeach
                                                <option value="voluntario">VOLUNTARIO</option>
                                                <option value="no_voluntario">NO ES VOLUNTARIO</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <label><strong>Domicilio</strong></label>
                            <div class="form-row">
                                <label class="col-form-label">Vialidad:</label>
                                <div class="col-sm-3 form-group">
                                    <select name="tipo_vialidad" id="tipo_vialidad" class="form-control" aria-required="true">
                                        @foreach ($vialidad as $vial)
                                            <option value="{{$vial}}">{{$vial}}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted" style="text-align: center;">Tipo</small>
                                </div>
                                <div class="col-sm-5 form-group">
                                    <input name="nombre_vialidad" id="nombre_vialidad" type="text" class="form-control" aria-required="true">
                                    <small class="form-text text-muted" style="text-align: center;">Nombre</small>
                                </div>
                                <label class="col-form-label">Número:</label>
                                <div class="col-sm-1 form-group">
                                    <input name="numero_exterior" id="numero_exterior" type="text" class="form-control" aria-required="true">
                                    <small class="form-text text-muted" style="text-align: center;">Exterior</small>
                                </div>
                                <div class="col-sm-1 form-group">
                                    <input name="numero_interior" id="numero_interior" type="text" class="form-control" aria-required="true">
                                    <small class="form-text text-muted" style="text-align: center;">Interior</small>
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-form-label">Entre Vialidades:</label>
                                <div class="col-sm-2 form-group">
                                    <select name="entre_tipo_vialidad1" id="entre_tipo_vialidad1" class="form-control" aria-required="true">
                                        @foreach ($vialidad as $vial)
                                            <option value="{{$vial}}">{{$vial}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <input name="entre_vialidad1" id="entre_vialidad1" type="text" class="form-control" aria-required="true">
                                    </select>
                                </div>
                                <label class="col-form-label">Y</label>
                                <div class="col-sm-2 form-group">
                                    <select name="entre_tipo_vialidad2" id="entre_tipo_vialidad2" class="form-control" aria-required="true">
                                        @foreach ($vialidad as $vial)
                                            <option value="{{$vial}}">{{$vial}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <input name="entre_vialidad2" id="entre_vialidad2" type="text" class="form-control" aria-required="true">
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-form-label">Vialidad Posterior:</label>
                                <div class="col-sm-3 form-group">
                                    <input name="vialidad_posterior" id="vialidad_posterior" type="text" class="form-control" aria-required="true">
                                </div>
                                <label class="col-form-label">Carretera:</label>
                                <div class="col-sm-3 form-group">
                                    <input name="carretera" id="carretera" type="text" class="form-control" aria-required="true">
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-form-label">Asentamiento Humano:</label>
                                <div class="col-sm-3 form-group">
                                    {{-- <input name="tipo_asentamiento_humano" id="tipo_asentamiento_humano" type="text" class="form-control" aria-required="true">
                                    <small class="form-text text-muted" style="text-align: center;">Tipo</small> --}}
                                    <select name="tipo_asentamiento_humano" id="tipo_asentamiento_humano" class="form-control" aria-required="true">
                                        @foreach ($asentamientos as $asentamiento)
                                            <option value="{{$asentamiento}}">{{$asentamiento}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-5 form-group">
                                    <input name="nombre_asentamiento_humano" id="nombre_asentamiento_humano" type="text" class="form-control" aria-required="true">
                                    <small class="form-text text-muted" style="text-align: center;">Nombre</small>
                                </div>
                                <label class="col-form-label">C.P.</label>
                                <div class="col-sm-2 form-group">
                                    <input name="codigo_postal2" id="codigo_postal2" type="text" class="form-control" aria-required="true">
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-form-label">Descripción de Ubicación:</label>
                                <div class="col-sm-6 form-group">
                                    <input name="descripcion_ubicacion" id="descripcion_ubicacion" type="text" class="form-control" aria-required="true">
                                </div>
                            </div>
                            <label><strong>Ocupación:</strong></label>
                            <div class="form-row">
                                <label class="col-form-label">Con Ocupación:</label>
                                <div class="col-sm-2 form-group">
                                    <select class="form-control" name="ocupacion" id="ocupacion" onchange="toggleOcupacion()">
                                        <option value="default">Seleccione</option>
                                        <option value="si">Si</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                <label class="col-form-label" id="label_ocupa" hidden>Seleccionar:</label>
                                <div class="col-sm-2 form-group" id="sin_ocupa" hidden>
                                    <select class="form-control" name="sin_ocupacion" id="sin_ocupacion">
                                        @foreach ($sinOcupacion as $variable)
                                            <option value="{{$variable}}">{{$variable}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group" id="con_ocupa" hidden>
                                    <select class="form-control" name="con_ocupacion" id="con_ocupacion">
                                        @foreach ($conOcupacion as $variable)
                                            <option value="{{$variable}}">{{$variable}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="col-form-label" id="ingreso_label" hidden>Ingreso Mensual:</label>
                                <div class="col-sm-2 form-group" id="ingreso_mensual_div" hidden>
                                    <input name="ingreso_mensual" id="ingreso_mensual" type="text" class="form-control" aria-required="true">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><strong>Rol (es) de la figura operativa:</strong></label>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="roles_figura_operativa[]" value="aspirante_asesor" checked> Aspirante que apoya en asesoria educativa hispano 2024</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <label><strong>Incorporado a:</strong></label>
                            <div class="form-row">
                                <label class="col-form-label">Unidad de Capacitación:</label>
                                <div class="col-sm-3 form-group">
                                    <input name="unidad_operativa" id="unidad_operativa" type="text" class="form-control" aria-required="true">
                                    <small class="form-text text-muted" style="text-align: center;">Tipo</small>
                                </div>
                                <label class="col-form-label">Clave del Centro de Trabajo:</label>
                                <div class="col-sm-3 form-group">
                                    <input name="circulo_estudio" id="circulo_estudio" type="text" class="form-control" aria-required="true">
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-form-label">Responsable del Círculo de estudio:</label>
                                <div class="col-sm-5 form-group">
                                    <input name="responsable_circulo" id="responsable_circulo" type="text" class="form-control" aria-required="true">
                                </div>
                                <label class="col-form-label">Archivo de Registro Operativas:</label>
                                <div class="col-sm-2 form-group">
                                    <input type="file" accept="application/pdf" class="form-control" id="arch_alfa" name="arch_alfa" placeholder="Archivo PDF">
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-form-label">Fecha de Registro Alfa:</label>
                                <div class="col-sm-3 form-group">
                                    <input name="fecha_inicio" id="fecha_inicio" type="date" class="form-control" aria-required="true">
                                </div>
                                <label class="col-form-label">No. de Folio:</label>
                                <div class="col-sm-3 form-group">
                                    <input name="no_folio" id="no_folio" type="text" class="form-control" aria-required="true">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
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
                <div class="form-group col-md-3">
                    <label for="inputcurp">CURP</label>
                    <input name='curp' id='curp' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputrfc">RFC/Constancia Fiscal</label>
                    <input name='rfc' id='rfc' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputhonorarios">Regimen</label>
                    <select class="form-control" name="honorario" id="honorario">
                        <option value="sin especificar">Sin Especificar</option>
                        @foreach ($lista_regimen as $regimen)
                            <option value="{{$regimen->concepto}}">{{$regimen->concepto}}</option>
                        @endforeach
                        {{-- <option value="HONORARIOS">Honorarios</option> --}}
                        {{-- <option value="ASIMILADOS A SALARIOS">Asimilados a Salarios</option> --}}
                        {{-- <option value="HONORARIOS Y ASIMILADOS A SALARIOS">Honorarios y Asimilado a Salarios</option> --}}
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputhonorarios">Tipo de Instructor</label>
                    <select class="form-control" name="tipo_instructor" id="tipo_instructor">
                        <option value="sin especificar">Sin Especificar</option>
                        <option value="INTERNO">Interno</option>
                        <option value="EXTERNO">Externo</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputtipo_identificacion">Tipo de Identificación</label>
                    <select class="form-control" name="tipo_identificacion" id="tipo_identificacion">
                        <option value="">SIN ESPECIFICAR</option>
                        <option value="INE">INE</option>
                        <option value="PASAPORTE">PASAPORTE</option>
                        <option value="LICENCIA DE CONDUCIR">LICENCIA DE CONDUCIR</option>
                        <option value="CARTILLA MILITAR">CARTILLA MILITAR</option>
                        <option value="CEDULA PROFESIONAL">CEDULA PROFESIONAL</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputfolio_ine">Folio de Identificación</label>
                    <input name='folio_ine' id='folio_ine' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputexpiracion_identificacion">Expiración de Identificación</label>
                    <input name='expiracion_identificacion' id='expiracion_identificacion' type="date" class="form-control" aria-required="true" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputarch_ine">Archivo Identificación</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_ine" name="arch_ine" placeholder="Archivo PDF" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputsexo">Sexo</label>
                    <select class="form-control" name="sexo" id="sexo">
                        <option value="">SELECCIONE</option>
                        <option value='MASCULINO'>Masculino</option>
                        <option value='FEMENINO'>Femenino</option>
                    </select>
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputestado_civil">Estado Civil</label>
                    <select class="form-control" name="estado_civil" id="estado_civil">
                        <option value="">SELECCIONE</option>
                        @foreach ($lista_civil as $item)
                            <option value="{{$item->nombre}}">{{$item->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputfecha_nacimiento">Fecha de Nacimiento</label>
                    <input name='fecha_nacimientoins' id='fecha_nacimientoins' type="date" class="form-control" aria-required="true">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputentidad">País de Nacimiento</label>
                    <select class="form-control" name="pais_nacimiento" id="pais_nacimiento">
                        <option value="">SELECCIONE</option>
                        @foreach ($paises as $pais)
                            <option value="{{$pais->id}}" @if($pais->id == '115') selected @endif>{{$pais->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputentidad">Entidad de Nacimiento</label>
                    <select class="form-control" name="entidad_nacimiento" id="entidad_nacimiento" onchange="local2_nacimiento()">
                        <option value="">SELECCIONE</option>
                        @foreach ($estados as $cadwell)
                            <option value="{{$cadwell->id}}">{{$cadwell->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputmunicipio">Municipio de Nacimiento</label>
                    <select class="form-control" name="municipio_nacimiento" id="municipio_nacimiento" onchange="local_nacimiento()">
                        <option value="sin especificar">Sin Especificar</option>
                    </select>
                </div>
                <div class="form-gorup col-md-3">
                    <label for="inputlocalidad">Localidad de Nacimiento</label>
                    <select class="form-control" name="localidad_nacimiento" id="localidad_nacimiento">
                        <option value="sin especificar">Sin Especificar</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputentidad">País de Residencia</label>
                    <select class="form-control" name="pais" id="pais">
                        <option value="">SELECCIONE</option>
                        @foreach ($paises as $pais)
                            <option value="{{$pais->id}}" @if($pais->id == '115') selected @endif>{{$pais->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputentidad">Entidad de Residencia</label>
                    <select class="form-control" name="entidad" id="entidad" onchange="local2()">
                        <option value="">SELECCIONE</option>
                        @foreach ($estados as $cadwell)
                            <option value="{{$cadwell->id}}">{{$cadwell->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputmunicipio">Municipio de Residencia</label>
                    <select class="form-control" name="municipio" id="municipio" onchange="local()">
                        <option value="sin especificar">Sin Especificar</option>
                    </select>
                </div>
                <div class="form-gorup col-md-3">
                    <label for="inputlocalidad">Localidad de Residencia</label>
                    <select class="form-control" name="localidad" id="localidad">
                        <option value="sin especificar">Sin Especificar</option>
                    </select>
                </div>
            </div>
            <div class="form-row" id='form-domicilio'>
                <div class="form-group col-md-6">
                    <label for="inputbanco">Dirección de Domicilio</label>
                    <input name="domicilio" id="domicilio" type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputbanco">Codigo Postal</label>
                    <input name="codigo_postal" id="codigo_postal" type="text" class="form-control" aria-required="true">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputtelefono">Numero de Telefono Personal</label>
                    <input name="telefono" id="telefono" type="tel" class="form-control" aria-required="true" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputtelefono">Numero de Telefono de Casa</label>
                    <input name="telefono_casa" id="telefono_casa" type="tel" class="form-control" aria-required="true" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputcorreo">Correo Electronico</label>
                    <input name="correo" id="correo" type="email" class="form-control" placeholder="correo_electronico@ejemplo.com" aria-required="true" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputbanco">Nombre del Banco</label>
                    <select class="form-control" name="banco" id="banco">
                        <option value="">SELECCIONE</option>
                        @foreach ($bancos as $juicy)
                            <option value="{{$juicy->nombre}}">{{$juicy->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputclabe">Clabe Interbancaria</label>
                    <input name="clabe" id="clabe" type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputnumero_cuenta">Numero de Cuenta</label>
                    <input name="numero_cuenta" id="numero_cuenta" type="text" class="form-control" aria-required="true">
                </div>
            </div>
            <hr style="border-color:dimgray">
            <div>
                <label><h2>Requisitos</h2></label>
            </div>
            <table class="table table-borderless table-responsive-md" id="table-perfprof2">
                <tbody>
                    <tr >
                        <td id="center" width="200px">
                            <H5><small><small>Comprobante Domicilio</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_domicilio" required>
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" id="arch_domicilio" name="arch_domicilio" placeholder="Archivo PDF">
                               <br><span id="imageName"></span>
                            </label>
                        </td>
                        <td id="center" width="60px">
                            <H5><small><small>CURP</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_curp">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_curp" name="arch_curp" placeholder="Archivo PDF">
                               <br><span id="imageName2"></span>
                            </label>
                        </td>
                        <td id="center" width="180px">
                            <H5><small><small>Comprobante Bancario</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_banco">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_banco" name="arch_banco" placeholder="Archivo PDF">
                               <br><span id="imageName3"></span>
                            </label>
                        </td>
                        <td id="center" width="100px">
                            <H5><small><small>Fotografía</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_foto">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="image/jpeg" class="form-control" id="arch_foto" name="arch_foto" placeholder="Archivo PDF">
                               <br><span id="imageName4"></span>
                            </label>
                        </td>
                    </tr>
                    <tr >
                        <td id="center" width="200px">
                            <H5><small><small>Acta de nacimiento</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_id">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_id" name="arch_id" placeholder="Archivo PDF">
                               <br><span id="imageName5"></span>
                            </label>
                        </td>
                        <td id="center" width="50px">
                            <H5><small><small>RFC</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_rfc">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_rfc" name="arch_rfc" placeholder="Archivo PDF">
                               <br><span id="imageName6"></span>
                            </label>
                        </td>
                        <td id="center" width="180px">
                            <H5><small><small>Comprobante Estudios</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_estudio">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_estudio" name="arch_estudio" placeholder="Archivo PDF">
                               <br><span id="imageName7"></span>
                            </label>
                        </td>
                        <td id="center" width="100px">
                            <H5><small><small>Curriculum</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="far fa-file-pdf  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_curriculum_personal">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_curriculum_personal" name="arch_curriculum_personal" placeholder="Archivo PDF">
                               <br><span id="imageName8"></span>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn mr-sm-4 mt-3" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn mr-sm-4 mt-3" >Guardar</button>
                    </div>
                </div>
            </div>
            <br>
        </form>
    </div>
@endsection
@section('script_content_js')
    <script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
    <script>
        function local() {
            // var x = document.getElementById("municipio").value;

            var valor = document.getElementById("municipio").value;
            var datos = {valor: valor};
            var url = '/instructores/busqueda/localidad';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                $("#localidad").empty();
                var selectL = document.getElementById('localidad'),
                option,
                i = 0,
                il = respuesta.length;
                // console.log(il);
                // console.log( respuesta[1].id)
                for (; i < il; i += 1)
                {
                    newOption = document.createElement('option');
                    newOption.value = respuesta[i].clave;
                    newOption.text=respuesta[i].localidad;
                    // selectL.appendChild(option);
                    selectL.add(newOption);
                }
            });
        }

        function local2() {
            // var x = document.getElementById("municipio").value;
            // console.log(x);

            var valor = document.getElementById("entidad").value;
            var datos = {valor: valor};
            var url = '/instructores/busqueda/municipio';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                $("#municipio").empty();
                var selectL = document.getElementById('municipio'),
                option,
                i = 0,
                il = respuesta.length;
                // console.log(il);
                // console.log( respuesta[1].id)
                for (; i < il; i += 1)
                {
                    newOption = document.createElement('option');
                    newOption.value = respuesta[i].id;
                    newOption.text=respuesta[i].muni;
                    // selectL.appendChild(option);
                    selectL.add(newOption);
                }
            });
        }

        function local_nacimiento() {
            // var x = document.getElementById("municipio").value;
            // console.log(x);

            var valor = document.getElementById("municipio_nacimiento").value;
            var datos = {valor: valor};
            var url = '/instructores/busqueda/localidad';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                $("#localidad_nacimiento").empty();
                var selectL = document.getElementById('localidad_nacimiento'),
                option,
                i = 0,
                il = respuesta.length;
                // console.log(il);
                // console.log( respuesta[1].id)
                for (; i < il; i += 1)
                {
                    newOption = document.createElement('option');
                    newOption.value = respuesta[i].clave;
                    newOption.text=respuesta[i].localidad;
                    // selectL.appendChild(option);
                    selectL.add(newOption);
                }
            });
        }

        function local2_nacimiento() {
            // var x = document.getElementById("municipio").value;
            // console.log(x);

            var valor = document.getElementById("entidad_nacimiento").value;
            var datos = {valor: valor};
            var url = '/instructores/busqueda/municipio';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                $("#municipio_nacimiento").empty();
                var selectL = document.getElementById('municipio_nacimiento'),
                option,
                i = 0,
                il = respuesta.length;
                // console.log(il);
                // console.log( respuesta[1].id)
                for (; i < il; i += 1)
                {
                    newOption = document.createElement('option');
                    newOption.value = respuesta[i].id;
                    newOption.text=respuesta[i].muni;
                    // selectL.appendChild(option);
                    selectL.add(newOption);
                }
            });
        }

        let ardom = document.getElementById("arch_domicilio");
        let arcur = document.getElementById("arch_curp");
        let arban = document.getElementById("arch_banco");
        let arfot = document.getElementById("arch_foto");
        let arid = document.getElementById("arch_id");
        let arrfc = document.getElementById("arch_rfc");
        let arest = document.getElementById("arch_estudio");
        let aralt = document.getElementById("arch_curriculum_personal");
        let imageName = document.getElementById("imageName");
        let imageName2 = document.getElementById("imageName2");
        let imageName3 = document.getElementById("imageName3");
        let imageName4 = document.getElementById("imageName4");
        let imageName5 = document.getElementById("imageName5");
        let imageName6 = document.getElementById("imageName6");
        let imageName7 = document.getElementById("imageName7");
        let imageName8 = document.getElementById("imageName8");

        ardom.addEventListener("change", ()=>{
            let inputImage = document.querySelector("#arch_domicilio").files[0];
            imageName.innerText = inputImage.name;
        })
        arcur.addEventListener("change", ()=>{
            let inputImage2 = document.querySelector("#arch_curp").files[0];
            imageName2.innerText = inputImage2.name;
        })
        arban.addEventListener("change", ()=>{
            let inputImage3 = document.querySelector("#arch_banco").files[0];
            imageName3.innerText = inputImage3.name;
        })
        arfot.addEventListener("change", ()=>{
            let inputImage4 = document.querySelector("#arch_foto").files[0];
            imageName4.innerText = inputImage4.name;
        })
        arid.addEventListener("change", ()=>{
            let inputImage5 = document.querySelector("#arch_id").files[0];
            imageName5.innerText = inputImage5.name;
        })
        arrfc.addEventListener("change", ()=>{
            let inputImage6 = document.querySelector("#arch_rfc").files[0];
            imageName6.innerText = inputImage6.name;
        })
        arest.addEventListener("change", ()=>{
            let inputImage7 = document.querySelector("#arch_estudio").files[0];
            imageName7.innerText = inputImage7.name;
        })
        aralt.addEventListener("change", ()=>{
            let inputImage8 = document.querySelector("#arch_curriculum_personal").files[0];
            imageName8.innerText = inputImage8.name;
        })

        document.getElementById('reginstructor').addEventListener('submit', function (e) {
            var fileInputs = [
                'arch_domicilio',
                'arch_curp',
                'arch_banco',
                'arch_foto',
                'arch_id',
                'arch_rfc',
                'arch_estudio',
                'arch_curriculum_personal'
            ];

            for (var i = 0; i < fileInputs.length; i++) {
                var inputId = fileInputs[i];
                var fileInput = document.getElementById(inputId);
                if (fileInput.files.length === 0) {
                    e.preventDefault(); // Prevent form submission
                    switch(inputId) {
                        case 'arch_domicilio':
                            inputId = 'Comprobande de Domicilio';
                        break;
                        case 'arch_curp':
                            inputId = 'CURP';
                        break;
                        case 'arch_banco':
                            inputId = 'Comprobante Bancario';
                        break;
                        case 'arch_foto':
                            inputId = 'Fotografía';
                        break;
                        case 'arch_id':
                            inputId = 'Acta de Nacimiento';
                        break;
                        case 'arch_rfc':
                            inputId = 'RFC';
                        break;
                        case 'arch_estudio':
                            inputId = 'Comprobante de Estudios';
                        break;
                        case 'arch_curriculum_personal':
                            inputId = 'Curriculum';
                        break;
                    }
                    alert('El campo de ' + inputId + ' esta vacio. Favor de subir el documento.');
                    return;
                }
            }
        });

    </script>
    <script>
        document.getElementById('toggleAcordeon').addEventListener('change', function() {
          var acordeon = document.getElementById('acordeonInstructor');
          if (this.checked) {
            acordeon.style.display = 'block';  // Muestra el acordeón
            $('#collapse1').collapse('show'); // Expande el acordeón automáticamente
            // document.getElementById('form-domicilio').setAttribute('hidden', true);
          } else {
            acordeon.style.display = 'none';  // Oculta el acordeón
            $('#collapse1').collapse('hide'); // Colapsa el acordeón
            // document.getElementById('form-domicilio').removeAttribute('hidden');
          }
        });

        function puestoOnOff(checkbox, selectId) {
            var select = document.getElementById(selectId);  // Obtener el select por su ID

            // if()

            if (checkbox.checked) {
                select.removeAttribute('hidden');  // Muestra el select cuando el checkbox está marcado
            } else {
                select.setAttribute('hidden', true);  // Oculta el select cuando el checkbox no está marcado
            }
        }

        function toggleOcupacion() {
            const ocupacion = document.getElementById('ocupacion').value;
            const labelOcupa = document.getElementById('label_ocupa');
            const sinOcupa = document.getElementById('sin_ocupa');
            const conOcupa = document.getElementById('con_ocupa');
            const ingresoMensual = document.getElementById('ingreso_mensual_div');
            const ingresoLabel = document.getElementById('ingreso_label');

            if (ocupacion === 'si') {
                labelOcupa.removeAttribute('hidden');
                conOcupa.removeAttribute('hidden');
                ingresoMensual.removeAttribute('hidden');
                ingresoLabel.removeAttribute('hidden');
                sinOcupa.setAttribute('hidden', true);
            } else if (ocupacion === 'no') {
                labelOcupa.removeAttribute('hidden');
                sinOcupa.removeAttribute('hidden');
                conOcupa.setAttribute('hidden', true);
                ingresoMensual.setAttribute('hidden', true);
                ingresoLabel.setAttribute('hidden', true);
            } else {
                labelOcupa.setAttribute('hidden', true);
                conOcupa.setAttribute('hidden', true);
                sinOcupa.setAttribute('hidden', true);
                ingresoMensual.setAttribute('hidden', true);
                ingresoLabel.setAttribute('hidden', true);
            }
        }
    </script>
@endsection

