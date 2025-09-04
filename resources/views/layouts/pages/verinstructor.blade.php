<!-- Creado por Orlando Chávez orlando@sidmac.com -->
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
    <form action="{{ route('saveins') }}" enctype="multipart/form-data" method="post" id="reginstructor">
        @csrf
        <div class="card-header">
            <h1>Registro de Instructor</h1>
        </div>
        @if(session('mensaje'))
            <div class="alert alert-danger">
                {{ session('mensaje') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @php
            //Ruta pathfiles
            $path_files = 'https://sivyc.icatech.gob.mx/storage';
        @endphp

        <div class="card card-body">
            @if($datainstructor->rechazo != NULL && $datainstructor->status == 'RETORNO')
                <div class="row ">
                    <div class="col-md-12 alert alert-danger">
                        <p>OBSERVACION DE RECHAZO POR PARTE DE LA DTA: </p>
                        <p>{{$datainstructor->rechazo}}</p>
                    </div>
                </div>
                <hr style="border-color:dimgray">
            @endif
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            @endif
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" id="toggleAcordeon" @if($datainstructor->instructor_alfa) checked @endif >
                    <span class="slider round"></span>
                </label>
                <span class="switch-text">Instructor Alfa</span>
            </div>
            <!-- Acordeón -->
            <div id="acordeonInstructor" class="panel-group acordeon-borde" @if($datainstructor->instructor_alfa) style="border: 3px solid black;" @else style="display: none; border: 3px solid black;" @endif>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                        <p data-toggle="collapse" href="#collapse1">Información del Instructor Alfa</p>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse show">
                        <div class="panel-body">
                            <div class="form-row">
                                <label class=" col-form-label">Hispanohablante:</label>
                                <div class="col-sm-1" style="padding-top: 9px;">
                                    <label class="radio-inline">
                                    <input type="radio" name="hispanohablante" value="si" @if($datainstructor->datos_alfa['hispanohablante'] == 'si') checked @endif> Sí
                                    </label>&nbsp;&nbsp;
                                    <label class="radio-inline">
                                    <input type="radio" name="hispanohablante" value="no" @if($datainstructor->datos_alfa['hispanohablante'] == 'no') checked @endif> No
                                    </label>
                                </div>
                                <!-- Lengua indígena -->
                                <label class="col-form-label">Lengua indígena:</label>
                                <div class="col-sm-2">
                                    <input id="lengua_indigena" name="lengua_indigena" type="text" class="form-control" placeholder="Especificar" value="{{$datainstructor->datos_alfa['lengua_indigena']}}">
                                </div>
                                <!-- Etnia/Lengua (MIB) -->
                                <label class="col-form-label">Etnia / Lengua (MIB):</label>
                                <div class="col-sm-2">
                                    <input id="etnia" name="etnia" type="text" class="form-control" placeholder="Especificar" value="{{$datainstructor->datos_alfa['etnia']}}">
                                </div>
                                <!-- Número de hijos -->
                                <label class="col-form-label">N° de Hijos:</label>
                                <div class="col-sm-1">
                                    <input id="hijos" name="hijos" type="text" class="form-control" maxlength="3" placeholder="0" value="{{$datainstructor->datos_alfa['hijos']}}">
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
                                        {{-- <div class="checkbox">
                                            <label><input type="checkbox" name="subproyecto[]" value="oportunidades" onclick="puestoOnOff(this, 'oportunidades_puesto')"> Oportunidades</label>
                                            <select class="form-control col-md-4" name="oportunidades_puesto" id="oportunidades_puesto" hidden>
                                                @foreach ($puestos as $puesto)
                                                    <option value="{{$puesto}}">{{$puesto}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="subproyecto[]" value="conafe" onclick="puestoOnOff(this, 'conafe_puesto')"> CONAFE</label>
                                            <select class="form-control col-md-4" name="conafe_puesto" id="conafe_puesto" hidden>
                                                @foreach ($puestos as $puesto)
                                                    <option value="{{$puesto}}">{{$puesto}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="subproyecto[]" value="sedena" onclick="puestoOnOff(this, 'sedena_puesto')"> SEDENA</label>
                                            <select class="form-control col-md-4" name="sedena_puesto" id="sedena_puesto" hidden>
                                                @foreach ($puestos as $puesto)
                                                    <option value="{{$puesto}}">{{$puesto}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="subproyecto[]" value="profesores" onclick="puestoOnOff(this, 'profesores_puesto')"> Profesores</label>
                                            <select class="form-control col-md-4" name="profesores_puesto" id="profesores_puesto" hidden>
                                                @foreach ($puestos as $puesto)
                                                    <option value="{{$puesto}}">{{$puesto}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="subproyecto[]" value="becarios" onclick="puestoOnOff(this, 'becarios_puesto')"> Jóvenes Becarios</label>
                                            <select class="form-control col-md-4" name="becarios_puesto" id="becarios_puesto" hidden>
                                                @foreach ($puestos as $puesto)
                                                    <option value="{{$puesto}}">{{$puesto}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="subproyecto[]" value="osc" onclick="puestoOnOff(this, 'osc_puesto')"> Organizaciones de la Sociedad Civil (OSC)</label>
                                            <select class="form-control col-md-4" name="osc_puesto" id="osc_puesto" hidden>
                                                @foreach ($puestos as $puesto)
                                                    <option value="{{$puesto}}">{{$puesto}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label style="opacity: 0.0;"><strong>Subproyecto:</strong></label>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="subproyecto[]" value="ipf" onclick="puestoOnOff(this, 'ipf_puesto')"> Instituciones Públicas Federales</label>
                                            <select class="form-control col-md-4" name="ipf_puesto" id="ipf_puesto" hidden>
                                                @foreach ($puestos as $puesto)
                                                    <option value="{{$puesto}}">{{$puesto}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="subproyecto[]" value="programas federales" onclick="puestoOnOff(this, 'programas federales_puesto')"> Programas Federales</label>
                                            <select class="form-control col-md-4" name="programas federales_puesto" id="programas federales_puesto" hidden>
                                                @foreach ($puestos as $puesto)
                                                    <option value="{{$puesto}}">{{$puesto}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="subproyecto[]" value="conevyt" onclick="puestoOnOff(this, 'conevyt_puesto')"> Certificación CONEVyT (Empresas)</label>
                                            <select class="form-control col-md-4" name="conevyt_puesto" id="conevyt_puesto" hidden>
                                                @foreach ($puestos as $puesto)
                                                    <option value="{{$puesto}}">{{$puesto}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="subproyecto[]" value="instituciones academicas" onclick="puestoOnOff(this, 'instituciones academicas_puesto')"> Instituciones Académicas</label>
                                            <select class="form-control col-md-4" name="instituciones academicas_puesto" id="instituciones academicas_puesto" hidden>
                                                @foreach ($puestos as $puesto)
                                                    <option value="{{$puesto}}">{{$puesto}}</option>
                                                @endforeach
                                            </select>
                                        </div> --}}
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="subproyecto[]" value="chiapas puede" {{--onclick="puestoOnOff(this, 'otrosub_puesto')"--}} checked>Chiapas Puede</label>
                                            {{-- <input type="text" class="form-control col-md-4" hidden name="otrosub" id="otrosub"> --}}
                                            <select class="form-control col-md-4" name="chiapas_puede_puesto" id="chiapas_puede_puesto">
                                                @foreach ($puestos as $puesto)
                                                    {{-- <option value="{{$puesto}}">{{$puesto}}</option> --}}
                                                @endforeach
                                                <option value="voluntario">VOLUNTARIO</option>
                                                <option value="no_voluntario" @if(isset($datainstructor->datos_alfa['subproyectos']['chiapas puede']) && $datainstructor->datos_alfa['subproyectos']['chiapas puede'] == 'no_voluntario') selected @endif>NO ES VOLUNTARIO</option>
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
                                            <option value="{{$vial}}" @if($datainstructor->datos_alfa['tipo_vialidad'] == $vial) selected @endif>{{$vial}}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted" style="text-align: center;">Tipo</small>
                                </div>
                                <div class="col-sm-5 form-group">
                                    <input name="nombre_vialidad" id="nombre_vialidad" type="text" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['nombre_vialidad']}}">
                                    <small class="form-text text-muted" style="text-align: center;">Nombre</small>
                                </div>
                                <label class="col-form-label">Número:</label>
                                <div class="col-sm-1 form-group">
                                    <input name="numero_exterior" id="numero_exterior" type="text" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['numero_exterior']}}">
                                    <small class="form-text text-muted" style="text-align: center;">Exterior</small>
                                </div>
                                <div class="col-sm-1 form-group">
                                    <input name="numero_interior" id="numero_interior" type="text" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['numero_interior']}}">
                                    <small class="form-text text-muted" style="text-align: center;">Interior</small>
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-form-label">Entre Vialidades:</label>
                                <div class="col-sm-2 form-group">
                                    <select name="entre_tipo_vialidad1" id="entre_tipo_vialidad1" class="form-control" aria-required="true">
                                        @foreach ($vialidad as $vial)
                                            <option value="{{$vial}}" @if($datainstructor->datos_alfa['entre_tipo_vialidad1'] == $vial) selected @endif>{{$vial}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <input name="entre_vialidad1" id="entre_vialidad1" type="text" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['entre_vialidad1']}}">
                                    </select>
                                </div>
                                <label class="col-form-label">Y</label>
                                <div class="col-sm-2 form-group">
                                    <select name="entre_tipo_vialidad2" id="entre_tipo_vialidad2" class="form-control" aria-required="true">
                                        @foreach ($vialidad as $vial)
                                            <option value="{{$vial}}" @if($datainstructor->datos_alfa['entre_tipo_vialidad2'] == $vial) selected @endif>{{$vial}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <input name="entre_vialidad2" id="entre_vialidad2" type="text" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['entre_vialidad2']}}">
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-form-label">Vialidad Posterior:</label>
                                <div class="col-sm-3 form-group">
                                    <input name="vialidad_posterior" id="vialidad_posterior" type="text" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['vialidad_posterior']}}">
                                </div>
                                <label class="col-form-label">Carretera:</label>
                                <div class="col-sm-3 form-group">
                                    <input name="carretera" id="carretera" type="text" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['carretera']}}">
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-form-label">Asentamiento Humano:</label>
                                <div class="col-sm-3 form-group">
                                    <select name="tipo_asentamiento_humano" id="tipo_asentamiento_humano" class="form-control" aria-required="true">
                                        @foreach ($asentamientos as $asentamiento)
                                            <option value="{{$asentamiento}}" @if($datainstructor->datos_alfa['tipo_asentamiento_humano'] == $asentamiento) selected @endif>{{$asentamiento}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-5 form-group">
                                    <input name="nombre_asentamiento_humano" id="nombre_asentamiento_humano" type="text" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['nombre_asentamiento_humano']}}">
                                    <small class="form-text text-muted" style="text-align: center;">Nombre</small>
                                </div>
                                <label class="col-form-label">C.P.</label>
                                <div class="col-sm-2 form-group">
                                    <input name="codigo_postal" id="codigo_postal" type="text" class="form-control" aria-required="true" @if($datainstructor->codigo_postal == '0') value="00000" @else value="{{$datainstructor->codigo_postal}}" @endif>
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-form-label">Descripción de Ubicación:</label>
                                <div class="col-sm-6 form-group">
                                    <input name="descripcion_ubicacion" id="descripcion_ubicacion" type="text" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['descripcion_ubicacion']}}">
                                </div>
                            </div>
                            <label><strong>Ocupación:</strong></label>
                            <div class="form-row">
                                <label class="col-form-label">Con Ocupación:</label>
                                <div class="col-sm-2 form-group">
                                    <select class="form-control" name="ocupacion" id="ocupacion" onchange="toggleOcupacion()">
                                        <option value="default">Seleccione</option>
                                        <option value="si" @if($datainstructor->datos_alfa['ocupacion'] == 'si') selected @endif>Si</option>
                                        <option value="no" @if($datainstructor->datos_alfa['ocupacion'] == 'no') selected @endif>No</option>
                                    </select>
                                </div>
                                <label class="col-form-label" id="label_ocupa" @if($datainstructor->datos_alfa['ocupacion'] == 'si') hidden @endif>Seleccionar:</label>
                                <div class="col-sm-2 form-group" id="sin_ocupa" @if($datainstructor->datos_alfa['ocupacion'] == 'si') hidden @endif>
                                    <select class="form-control" name="sin_ocupacion" id="sin_ocupacion">
                                        @foreach ($sinOcupacion as $variable)
                                            <option value="{{$variable}}" @if($datainstructor->datos_alfa['sin_ocupacion'] == $variable) selected @endif>{{$variable}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group" id="con_ocupa" @if($datainstructor->datos_alfa['ocupacion'] == 'no') hidden @endif>
                                    <select class="form-control" name="con_ocupacion" id="con_ocupacion">
                                        @foreach ($conOcupacion as $variable)
                                            <option value="{{$variable}}" @if($datainstructor->datos_alfa['con_ocupacion'] == $variable) selected @endif>{{$variable}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="col-form-label" id="ingreso_label" @if($datainstructor->datos_alfa['ocupacion'] == 'no') hidden @endif>Ingreso Mensual:</label>
                                <div class="col-sm-2 form-group" id="ingreso_mensual_div" @if($datainstructor->datos_alfa['ocupacion'] == 'no') hidden @endif>
                                    <input name="ingreso_mensual" id="ingreso_mensual" type="text" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['ingreso_mensual']}}">
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
                                <div class="col-sm-5 form-group">
                                    <input name="unidad_operativa" id="unidad_operativa" type="text" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['unidad_operativa']}}">
                                    {{-- <small class="form-text text-muted" style="text-align: center;">Tipo</small> --}}
                                </div>
                                <label class="col-form-label">Clave del Centro de Trabajo:</label>
                                <div class="col-sm-3 form-group">
                                    <input name="circulo_estudio" id="circulo_estudio" type="text" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['circulo_estudio']}}">
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-form-label">Responsable del Círculo de estudio:</label>
                                <div class="col-sm-5 form-group">
                                    <input name="responsable_circulo" id="responsable_circulo" type="text" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['responsable_circulo']}}">
                                </div>
                                <label class="col-form-label">Archivo de Registro Operativas:</label>
                                <div class="col-sm-2 form-group">
                                    <input type="file" accept="application/pdf" class="form-control" id="arch_alfa" name="arch_alfa" placeholder="Archivo PDF">
                                </div>
                            </div>
                            <div class="form-row">
                                <label class="col-form-label">Fecha de Registro Alfa:</label>
                                <div class="col-sm-3 form-group">
                                    <input name="fecha_inicio" id="fecha_inicio" type="date" class="form-control" aria-required="true" value="{{$datainstructor->datos_alfa['fecha_inicio']}}">
                                </div>
                                <label class="col-form-label">No. de Folio:</label>
                                <div class="col-sm-3 form-group">
                                    <input name="no_folio" id="no_folio" type="text" class="form-control" aria-required="true" @if(isset($datainstructor->datos_alfa['numero_folio'])) value="{{$datainstructor->datos_alfa['numero_folio']}}" @endif>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div>
                <label><h2>Datos Personales</h2></label>@php
            @endphp
            </div>
            @php $stats = array('PREVALIDACION','EN FIRMA'); $ari = ['VALIDADO','EN CAPTURA','RETORNO','REACTIVACION EN CAPTURA']; $perfilprof_nom = NULL; @endphp
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputnombre">Nombre {{$roluser->role_id}}</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name='nombre' id='nombre' type="text" class="form-control" aria-required="true" value="{{$datainstructor->nombre}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_paterno">Apellido Paterno</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name='apellido_paterno' id='apellido_paterno' type="text" class="form-control" aria-required="true" value="{{$datainstructor->apellidoPaterno}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_materno">Apellido Materno</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name='apellido_materno' id='apellido_materno' type="text" class="form-control" aria-required="true" value="{{$datainstructor->apellidoMaterno}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputcurp">CURP</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name='curp' id='curp' type="text" class="form-control" aria-required="true" value="{{$datainstructor->curp}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputrfc">RFC/Constancia Fiscal</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name='rfc' id='rfc' type="text" class="form-control" aria-required="true" value="{{$datainstructor->rfc}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputhonorarios">Regimen</label>
                    <select @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif class="form-control" name="honorario" id="honorario">
                        <option value="sin especificar">Sin Especificar</option>
                        @foreach ($lista_regimen as $regimen)
                            <option value="{{$regimen->concepto}}" @if($datainstructor->tipo_honorario == $regimen->concepto) selected @endif>{{$regimen->concepto}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputhonorarios">Tipo de Instructor</label>
                    <select class="form-control" name="tipo_instructor" id="tipo_instructor">
                        <option value="INTERNO" @if($datainstructor->tipo_instructor == 'INTERNO') selected @endif>Interno</option>
                        <option value="EXTERNO" @if($datainstructor->tipo_instructor == 'EXTERNO') selected @endif>Externo</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputtipo_identificacion">Tipo de Identificación</label>
                    <select @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif class="form-control" name="tipo_identificacion" id="tipo_identificacion">
                        <option value="">SIN ESPECIFICAR</option>
                        <option value="INE" @if($datainstructor->tipo_identificacion == 'INE') selected @endif>INE</option>
                        <option value="PASAPORTE" @if($datainstructor->tipo_identificacion == 'PASAPORTE') selected @endif>PASAPORTE</option>
                        <option value="LICENCIA DE CONDUCIR" @if($datainstructor->tipo_identificacion == 'LICENCIA DE CONDUCIR') selected @endif>LICENCIA DE CONDUCIR</option>
                        <option value="CARTILLA MILITAR" @if($datainstructor->tipo_identificacion == 'CARTILLA MILITAR') selected @endif>CARTILLA MILITAR</option>
                        <option value="CEDULA PROFESIONAL" @if($datainstructor->tipo_identificacion == 'CEDULA PROFESIONAL') selected @endif>CEDULA PROFESIONAL</option>
                    </select @if($datainstructor->status != "VALIDADO") disabled @endif>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputfolio_ine">Folio de Identificación</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name='folio_ine' id='folio_ine' type="text" class="form-control" aria-required="true" value="{{$datainstructor->folio_ine}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputexpiracion_identificacion">Expiración de Identificación</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name='expiracion_identificacion' id='expiracion_identificacion' type="date" class="form-control" aria-required="true" required value="{{$datainstructor->expiracion_identificacion}}">
                </div>
                <div class="form-group col-md-1">
                    {{-- <label for="inputarch_ine">Archivo Identificación</label> --}}
                    <table class="table table-borderless table-responsive-md" id="table-perfprof2">
                        <tbody>
                            <tr >
                                <td></td>
                                <td id="center">
                                    Comprobante Identificación
                                </td>
                                <td></td>
                                <td id="center">
                                    @if($datainstructor->archivo_ine == NULL)
                                        <i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i>
                                    @else
                                        <a href="
                                            @if (filter_var($datainstructor->archivo_ine, FILTER_VALIDATE_URL))
                                                {{$datainstructor->archivo_ine}}
                                            @else
                                                {{$path_files.$datainstructor->archivo_ine}}
                                            @endif
                                            " target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                                    @endif
                                </td>
                                <td></td>
                                <td id="center">
                                    @if($datainstructor->status != "PREVALIDACION")
                                        @if($datainstructor->status != "EN FIRMA")
                                            @if($roluser->role_id != 3)
                                                <input hidden name="chkpre" id="chkpre" value="FALSE">
                                                <label class='onpoint' for="arch_ine">
                                                    <a class="btn mr-sm-4 mt-3 btn-sm">
                                                        Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                                    </a>
                                                    <input style='display:none;' type="file" accept="application/pdf" id="arch_ine" name="arch_ine" placeholder="Archivo PDF">
                                                <br><span id="imageName0"></span>
                                                </label>
                                            @endif
                                        @endif
                                    @endif
                                    @if($datainstructor->status == "PREVALIDACION" || $datainstructor->status == "EN FIRMA" || $datainstructor->status == "VALIDADO")
                                        <input hidden name="chkpre" id="chkpre" value="TRUE">
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputsexo">Sexo</label>
                    <select @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif class="form-control" name="sexo" id="sexo">
                        <option value="">SELECCIONE</option>
                        <option value='MASCULINO' @if($datainstructor->sexo == 'MASCULINO')selected @endif>Masculino</option>
                        <option value='FEMENINO' @if($datainstructor->sexo == 'FEMENINO')selected @endif>Femenino</option>
                    </select @if($datainstructor->status != "VALIDADO") disabled @endif>
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputestado_civil">Estado Civil</label>
                    <select @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif class="form-control" name="estado_civil" id="estado_civil">
                        <option value="">SELECCIONE</option>
                        @foreach ($lista_civil as $item)
                            <option value="{{$item->nombre}}" @if($datainstructor->estado_civil == $item->nombre)selected @endif>{{$item->nombre}}</option>
                        @endforeach
                    </select @if($datainstructor->status != "VALIDADO") disabled @endif>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputfecha_nacimiento">Fecha de Nacimiento</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name='fecha_nacimientoins' id='fecha_nacimientoins' type="date" class="form-control" aria-required="true" value="{{$datainstructor->fecha_nacimiento}}">
                </div>
            </div>
            {{-- <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputentidad">País de Nacimiento</label>
                    <select class="form-control" name="pais_nacimiento" id="pais_nacimiento">
                        <option value="">SELECCIONE</option>
                        @foreach ($paises as $pais)
                            <option value="{{$pais->id}}" @if($pais->id == $datainstructor->pais_nacimiento || $pais->id == '115') selected @endif>{{$pais->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputentidad">Entidad de Nacimiento</label>
                    <select @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif class="form-control" name="entidad_nacimiento" id="entidad_nacimiento" onchange="local2_nacimiento()">
                        <option value="">SELECCIONE</option>
                        @foreach ($estados as $cadwell)
                            <option value="{{$cadwell->id}}" @if($datainstructor->entidad_nacimiento == $cadwell->nombre) selected @endif>{{$cadwell->nombre}}</option>
                        @endforeach
                    </select @if($datainstructor->status != "VALIDADO") disabled @endif>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputmunicipio">Municipio de Nacimiento</label>
                    <select @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif class="form-control" name="municipio_nacimiento" id="municipio_nacimiento" onchange="local_nacimiento()">
                        <option value="sin especificar">Sin Especificar</option>
                        @if(isset($municipios_nacimiento))
                            @foreach ($municipios_nacimiento as $cadwell)
                                <option value="{{$cadwell->id}}" @if($datainstructor->municipio_nacimiento == $cadwell->muni) selected @endif>{{$cadwell->muni}}</option>
                            @endforeach
                        @endif
                    </select @if($datainstructor->status != "VALIDADO") disabled @endif>
                </div>
                <div class="form-gorup col-md-3">
                    <label for="inputlocalidad">Localidad de Nacimiento</label>
                    <select @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif class="form-control" name="localidad_nacimiento" id="localidad_nacimiento">
                        <option value="sin especificar">Sin Especificar</option>
                        @if(!is_null($localidades_nacimiento))
                            @foreach ($localidades_nacimiento as $cadwell)
                                <option value="{{$cadwell->clave}}" @if($datainstructor->localidad_nacimiento == $cadwell->localidad) selected @endif>{{$cadwell->localidad}}</option>
                            @endforeach
                        @endif
                    </select @if($datainstructor->status != "VALIDADO") disabled @endif>
                </div>
            </div> --}}
            <div class="form-row">
                {{-- <div class="form-group col-md-3">
                    <label for="inputentidad">País de Residencia</label>
                    <select class="form-control" name="pais" id="pais">
                        <option value="">SELECCIONE</option>
                        @foreach ($paises as $pais)
                            <option value="{{$pais->id}}" @if((isset($datainstructor->datos_alfa['pais_residencia']) && $pais->id == $datainstructor->datos_alfa['pais_residencia']) || $pais->id == '115') selected @endif>{{$pais->nombre}}</option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="form-group col-md-3">
                    <label for="inputentidad">Entidad de Residencia</label>
                    <select @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif class="form-control" name="entidad" id="entidad" onchange="local2()">
                        <option value="">SELECCIONE</option>
                        @foreach ($estados as $cadwell)
                            <option value="{{$cadwell->id}}" @if($datainstructor->entidad == $cadwell->nombre) selected @endif>{{$cadwell->nombre}}</option>
                        @endforeach
                    </select @if($datainstructor->status != "VALIDADO") disabled @endif>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputmunicipio">Municipio de Residencia</label>
                    <select @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif class="form-control" name="municipio" id="municipio" onchange="local()">
                        <option value="sin especificar">Sin Especificar</option>
                        @foreach ($municipios as $cadwell)
                            <option value="{{$cadwell->id}}" @if($datainstructor->municipio == $cadwell->muni) selected @endif>{{$cadwell->muni}}</option>
                        @endforeach
                    </select @if($datainstructor->status != "VALIDADO") disabled @endif>
                </div>
                {{-- <div class="form-gorup col-md-3">
                    <label for="inputlocalidad">Localidad de Residencia</label>
                    <select @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif class="form-control" name="localidad" id="localidad">
                        <option value="sin especificar">Sin Especificar</option>
                        @if(!is_null($localidades))
                            @foreach ($localidades as $cadwell)
                                <option value="{{$cadwell->clave}}" @if($datainstructor->localidad == $cadwell->localidad) selected @endif>{{$cadwell->localidad}}</option>
                            @endforeach
                        @endif
                    </select @if($datainstructor->status != "VALIDADO") disabled @endif>
                </div> --}}
            </div>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="inputbanco">Dirección de Domicilio</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name="domicilio" id="domicilio" type="text" class="form-control" aria-required="true" value="{{$datainstructor->domicilio}}">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputbanco">Codigo Postal</label>
                    <input  @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name="codigo_postal" id="codigo_postal" type="text" class="form-control" aria-required="true" required @if($datainstructor->codigo_postal == '0') value="00000" @else value="{{$datainstructor->codigo_postal}}" @endif>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputtelefono">Numero de Telefono Personal</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name="telefono" id="telefono" type="tel" class="form-control" aria-required="true" value="{{$datainstructor->telefono}}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputtelefono">Numero de Telefono de Casa</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name="telefono_casa" id="telefono_casa" type="tel" class="form-control" aria-required="true" required value="{{$datainstructor->telefono_casa}}" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputcorreo">Correo Electronico</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name="correo" id="correo" type="email" class="form-control" placeholder="correo_electronico@ejemplo.com" aria-required="true" value="{{$datainstructor->correo}}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputbanco">Nombre del Banco</label>
                    @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61']))
                        <input disabled name="banco" id="banco" type="text" class="form-control" aria-required="true" value="{{$datainstructor->banco}}">
                    @else
                        <select class="form-control" name="banco" id="banco">
                            <option selected value="">SELECCIONE</option>
                            @foreach ($bancos as $juicy)
                                <option value="{{$juicy->nombre}}" @if($juicy->nombre == $datainstructor->banco) selected @endif>{{$juicy->nombre}}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="form-group col-md-4">
                    <label for="inputclabe">Clabe Interbancaria</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name="clabe" id="clabe" type="text" class="form-control" aria-required="true" value="{{$datainstructor->interbancaria}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputnumero_cuenta">Numero de Cuenta</label>
                    <input @if(!in_array($datainstructor->status, $ari) || !in_array($roluser->role_id, ['30','31','61'])) disabled @endif name="numero_cuenta" id="numero_cuenta" type="text" class="form-control" aria-required="true" value="{{$datainstructor->no_cuenta}}">
                </div>
            </div>
            <hr style="border-color:dimgray">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h4>Experiencia Docente</h4>
                    </div>
                    @if(in_array($datainstructor->status, $ari))
                        <div class="pull-right">
                            @can('instructor.editar_fase2')
                                <button type="button" class="btn mr-sm-4 mt-3"
                                    id = 'buttonaddexpdoc'
                                    data-toggle="modal"
                                    data-placement="top"
                                    data-target="#addexpdocModal"
                                    data-id='{{$datainstructor->id}}'>Agregar Experiencia Docente
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
            <table class="table table-bordered table-responsive-md" id='tableexpdoc'>
                <thead>
                    <tr>
                        <th scope="col">Asignatura</th>
                        <th scope="col">Institución</th>
                        <th scope="col">Función</th>
                        <th scope="col">Periodo</th>
                        <th width="85px">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datainstructor->exp_docente))
                        @foreach ($datainstructor->exp_docente as $pl1 => $exdoc)
                            @php $lock = $pl1 + 1 @endphp
                            <tr>
                                <th scope="row">{{ $exdoc['asignatura'] }}</th>
                                <td>{{ $exdoc['institucion'] }}</td>
                                <td>{{ $exdoc['funcion'] }}</td>
                                <td>{{ $exdoc['periodo'] }}</td>
                                <td width="13%">
                                    @if(in_array($datainstructor->status, $ari))
                                        @can('instructor.editar_fase2')
                                            <button type="button" class="btn btn-warning mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="ELIMINAR REGISTRO"
                                                data-toggle="modal"
                                                data-placement="top"
                                                data-target="#delexpdocModal"
                                                data-id='["{{$lock}}", "{{$datainstructor->id}}"]'>
                                                    <i class="fa fa-eraser" aria-hidden="true"></i>
                                            </button>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <br>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h4>Experiencia Laboral</h4>
                    </div>
                    @if(in_array($datainstructor->status, $ari))
                        <div class="pull-right">
                            @can('instructor.editar_fase2')
                                <button type="button" class="btn mr-sm-4 mt-3"
                                    id = 'buttonaddexplab'
                                    data-toggle="modal"
                                    data-placement="top"
                                    data-target="#addexplabModal"
                                    data-id='{{$datainstructor->id}}'>Agregar Experiencia Laboral
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
            <table class="table table-bordered table-responsive-md" id='tableexplab'>
                <thead>
                    <tr>
                        <th scope="col">Puesto</th>
                        <th scope="col">Periodo</th>
                        <th scope="col">Institución</th>
                        <th width="85px">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datainstructor->exp_laboral))
                        @foreach ($datainstructor->exp_laboral as $pl2 => $exlab)
                            @php $lock2 = $pl2 + 1 @endphp
                            <tr>
                                <th scope="row">{{ $exlab['puesto'] }}</th>
                                <td>{{ $exlab['periodo'] }}</td>
                                <td>{{ $exlab['institucion'] }}</td>
                                <td width="13%">
                                    @if($datainstructor->status == 'VALIDADO' || $datainstructor->status == 'EN CAPTURA')
                                        @can('instructor.editar_fase2')
                                        @if(isset($lock))
                                            <button type="button" class="btn btn-warning mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="ELIMINAR REGISTRO"
                                                data-toggle="modal"
                                                data-placement="top"
                                                data-target="#delexplabModal"
                                                data-id='["{{$lock}}", "{{$datainstructor->id}}"]'>
                                                    <i class="fa fa-eraser" aria-hidden="true"></i>
                                            </button>
                                            @endif
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <br>
            <div class="pull-left">
                        <h4>Estandares de Competencia</h4>
                    </div>
             <table class="table table-bordered table-responsive-md" id="espec-table">
                <thead>
                    <tr>
                        <th scope="col">Tipo de Estandar de Competencia</th>
                        <th scope="col">Nombre del Estandar</th>
                        <th scope="col">Institución que Expide</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ((array) $datainstructor->certificados as $certificado)
                        <tr>
                            <td>{{ $certificado['tipo'] }}</td>
                            <td>{{ $certificado['nombre'] }}</td>
                            <td>{{ $certificado['entidad'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay certificados registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <hr style="border-color:dimgray">
            <div>
                <label><h2>Requisitos</h2></label>
            </div>
            {{-- joijojo --}}
            <table class="table table-borderless table-responsive-md" id="table-perfprof2">
                <tbody>
                    <tr >
                        <td id="center" width="200px">
                            <H5><small><small>Comprobante Domicilio</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            @if($datainstructor->archivo_domicilio == NULL)
                                <i id="arch_domicilio_pdf"  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i>
                            @else
                                <a id="arch_domicilio_pdf" href="
                                    @if (filter_var($datainstructor->archivo_domicilio, FILTER_VALIDATE_URL))
                                        {{$datainstructor->archivo_domicilio}}
                                    @else
                                        {{$path_files.$datainstructor->archivo_domicilio}}
                                    @endif
                                    " target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                            @endif
                        </td>
                        <td id="center" width="160px">
                            @if($datainstructor->status != "PREVALIDACION")
                                @if($datainstructor->status != "EN FIRMA")
                                    @can('instructor.editar_fase2')
                                        <label class='onpoint' for="arch_domicilio">
                                            <a class="btn mr-sm-4 mt-3 btn-sm">
                                                Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                            </a>
                                            <input @if(!in_array($datainstructor->status, $ari)) disabled @endif style='display:none;' type="file" accept="application/pdf" id="arch_domicilio" name="arch_domicilio" placeholder="Archivo PDF">
                                            <br><span id="imageName"></span>
                                        </label>
                                    @else
                                        <input hidden id="arch_domicilio">
                                    @endcan
                                @endif
                            @endif
                        </td>
                        <td id="center" width="60px">
                            <H5><small><small>CURP</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            @if($datainstructor->archivo_curp == NULL)
                                <i id="arch_curp_pdf"  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i>
                            @else
                                <a id="arch_curp_pdf" href="
                                    @if (filter_var($datainstructor->archivo_curp, FILTER_VALIDATE_URL))
                                        {{$datainstructor->archivo_curp}}
                                    @else
                                        {{$path_files.$datainstructor->archivo_curp}}
                                    @endif
                                    " target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                            @endif
                        </td>
                        <td id="center" width="160px">
                            @if($datainstructor->status != "PREVALIDACION")
                                @if($datainstructor->status != "EN FIRMA")
                                    @can('instructor.editar_fase2')
                                        <label class='onpoint' for="arch_curp">
                                            <a class="btn mr-sm-4 mt-3 btn-sm">
                                                Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                            </a>
                                            <input @if(!in_array($datainstructor->status, $ari)) disabled @endif style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_curp" name="arch_curp" placeholder="Archivo PDF">
                                            <br><span id="imageName2"></span>
                                        </label>
                                    @else
                                        <input hidden id="arch_curp">
                                    @endcan
                                @endif
                            @endif
                        </td>
                        <td id="center" width="180px">
                            <H5><small><small>Comprobante Bancario</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            @if($datainstructor->archivo_bancario == NULL)
                                <i id="arch_banco_pdf"  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i>
                            @else
                                <a id="arch_banco_pdf" href="
                                    @if (filter_var($datainstructor->archivo_bancario, FILTER_VALIDATE_URL))
                                        {{$datainstructor->archivo_bancario}}
                                    @else
                                        {{$path_files.$datainstructor->archivo_bancario}}
                                    @endif
                                    " target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                            @endif
                        </td>
                        <td id="center" width="160px">
                            @if($datainstructor->status != "PREVALIDACION")
                                @if($datainstructor->status != "EN FIRMA")
                                    @can('instructor.editar_fase2')
                                        <label class='onpoint' for="arch_banco">
                                            <a class="btn mr-sm-4 mt-3 btn-sm">
                                                Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                            </a>
                                            <input @if(!in_array($datainstructor->status, $ari)) disabled @endif style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_banco" name="arch_banco" placeholder="Archivo PDF">
                                            <br><span id="imageName3"></span>
                                        </label>
                                    @else
                                        <input hidden id="arch_banco">
                                    @endcan
                                @endif
                            @endif
                        </td>
                        <td id="center" width="100px">
                            <H5><small><small>Fotografía</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            @if($datainstructor->archivo_fotografia == NULL)
                                <i id="arch_foto_jpg" class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i>
                            @else
                                <a id="arch_foto_jpg" href="
                                    @if (filter_var($datainstructor->archivo_fotografia, FILTER_VALIDATE_URL))
                                        {{$datainstructor->archivo_fotografia}}
                                    @else
                                        {{$path_files.$datainstructor->archivo_fotografia}}
                                    @endif
                                    " target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                            @endif
                        </td>
                        <td id="center" width="160px">
                            @if($datainstructor->status != "PREVALIDACION")
                                @if($datainstructor->status != "EN FIRMA")
                                    @can('instructor.editar_fase2')
                                        <label class='onpoint' for="arch_foto">
                                            <a class="btn mr-sm-4 mt-3 btn-sm">
                                                Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                            </a>
                                            <input @if(!in_array($datainstructor->status, $ari)) disabled @endif style='display:none;' type="file" accept="image/jpeg" class="form-control" id="arch_foto" name="arch_foto" placeholder="Archivo PDF">
                                            <br><span id="imageName4"></span>
                                        </label>
                                    @else
                                        <input hidden id="arch_foto">
                                    @endcan
                                @endif
                            @endif
                        </td>
                    </tr>
                    <tr >
                        <td id="center" width="200px">
                            <H5><small><small>Acta de Nacimiento</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            @if($datainstructor->archivo_otraid == NULL)
                                <i id="arch_id_pdf" class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i>
                            @else
                                <a id="arch_id_pdf" href="
                                    @if (filter_var($datainstructor->archivo_otraid, FILTER_VALIDATE_URL))
                                        {{$datainstructor->archivo_otraid}}
                                    @else
                                        {{$path_files.$datainstructor->archivo_otraid}}
                                    @endif
                                    " target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                            @endif
                        </td>
                        <td id="center" width="160px">
                            @if($datainstructor->status != "PREVALIDACION")
                                @if($datainstructor->status != "EN FIRMA")
                                    @can('instructor.editar_fase2')
                                        <label class='onpoint' for="arch_id">
                                            <a class="btn mr-sm-4 mt-3 btn-sm">
                                                Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                            </a>
                                            <input @if(!in_array($datainstructor->status, $ari)) disabled @endif style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_id" name="arch_id" placeholder="Archivo PDF">
                                            <br><span id="imageName5"></span>
                                        </label>
                                    @else
                                        <input hidden id="arch_id">
                                    @endcan
                                @endif
                            @endif
                        </td>
                        <td id="center" width="50px">
                            <H5><small><small>RFC</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            @if($datainstructor->archivo_rfc == NULL)
                                <i id="arch_rfc_pdf" class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i>
                            @else
                                <a id="arch_rfc_pdf" href="
                                    @if (filter_var($datainstructor->archivo_rfc, FILTER_VALIDATE_URL))
                                        {{$datainstructor->archivo_rfc}}
                                    @else
                                        {{$path_files.$datainstructor->archivo_rfc}}
                                    @endif
                                    " target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                            @endif
                        </td>
                        <td id="center" width="160px">
                            @if($datainstructor->status != "PREVALIDACION")
                                @if($datainstructor->status != "EN FIRMA")
                                    @can('instructor.editar_fase2')
                                        <label class='onpoint' for="arch_rfc">
                                            <a class="btn mr-sm-4 mt-3 btn-sm">
                                                Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                            </a>
                                            <input @if(!in_array($datainstructor->status, $ari)) disabled @endif style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_rfc" name="arch_rfc" placeholder="Archivo PDF">
                                            <br><span id="imageName6"></span>
                                        </label>
                                    @else
                                        <input hidden id="arch_rfc">
                                    @endcan
                                @endif
                            @endif
                        </td>
                        <td id="center" width="180px">
                            <H5><small><small>Comprobante Estudios</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            @if($datainstructor->archivo_estudios == NULL)
                                <i id="arch_estudio_pdf" class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i>
                            @else
                                <a id="arch_estudio_pdf" href="
                                    @if (filter_var($datainstructor->archivo_estudios, FILTER_VALIDATE_URL))
                                        {{$datainstructor->archivo_estudios}}
                                    @else
                                        {{$path_files.$datainstructor->archivo_estudios}}
                                    @endif
                                    " target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                            @endif
                        </td>
                        <td id="center" width="160px">
                            @if($datainstructor->status != "PREVALIDACION")
                                @if($datainstructor->status != "EN FIRMA")
                                    @can('instructor.editar_fase2')
                                        <label class='onpoint' for="arch_estudio">
                                            <a class="btn mr-sm-4 mt-3 btn-sm">
                                                Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                            </a>
                                            <input @if(!in_array($datainstructor->status, $ari)) disabled @endif style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_estudio" name="arch_estudio" placeholder="Archivo PDF">
                                            <br><span id="imageName7"></span>
                                        </label>
                                    @else
                                        <input hidden id="arch_estudio">
                                    @endcan
                                @endif
                            @endif
                        </td>
                        <td id="center" width="100px">
                            <H5><small><small>Curriculum</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            @if($datainstructor->archivo_curriculum_personal == NULL)
                                <i id="arch_curriculum_personal_pdf" class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i>
                            @else
                                <a id="arch_curriculum_personal_pdf" href="
                                    @if (filter_var($datainstructor->archivo_curriculum_personal, FILTER_VALIDATE_URL))
                                        {{$datainstructor->archivo_curriculum_personal}}
                                    @else
                                        {{$path_files.$datainstructor->archivo_curriculum_personal}}
                                    @endif
                                    " target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                            @endif
                        </td>
                        <td id="center" width="160px">
                            @if($datainstructor->status != "PREVALIDACION")
                                @if($datainstructor->status != "EN FIRMA")
                                    @can('instructor.editar_fase2')
                                        <label class='onpoint' for="arch_curriculum_personal">
                                            <a class="btn mr-sm-4 mt-3 btn-sm">
                                                Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                            </a>
                                            <input @if(!in_array($datainstructor->status, $ari)) disabled @endif style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_curriculum_personal" name="arch_curriculum_personal" placeholder="Archivo PDF">
                                            <br><span id="imageName8"></span>
                                        </label>
                                    @else
                                        <input hidden id="arch_curriculum_personal">
                                    @endcan
                                @endif
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td id="center" width="100px">
                            <H5><small><small>Alta de Instructor</small></small></H5>
                        </td>
                        @php
                            $dataVal = null;
                            if($validado != false) {
                                foreach($validado as $point => $latest) {
                                    if(isset($latest->hvalidacion)){
                                        $hvalidacion = json_decode($latest->hvalidacion);
                                    } else {
                                        $hvalidacion = null;
                                    }
                                    if(!is_null($hvalidacion)) {
                                        if(!is_array($hvalidacion)) {
                                            $hvalidacion = json_decode($hvalidacion);
                                        }
                                        $hvalidacion = end($hvalidacion);
                                        if(is_null($dataVal) && !isset($hvalidacion->memo_baja)) {
                                            $dataVal = $hvalidacion;
                                        } elseif (isset($hvalidacion->memo_val) && !is_null($hvalidacion->memo_val) && $hvalidacion->fecha_val > $dataVal->fecha_val) {
                                            $dataVal = $hvalidacion;
                                        }
                                    }
                                }
                            } else {
                                $validado = [];
                            }
                        @endphp
                        <td id="center" width="50px">
                            @if(is_null($dataVal))
                                <i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i>
                            @elseif(isset($dataVal->arch_val))
                                <a href={{$dataVal->arch_val}} target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
                <div>
                    <label><h3>Entrevista para Candidatos a Instructores</h3></label>
                </div>
                <div class="form-row">
                    @if(isset($datainstructor->entrevista))
                        <div class="form-group col-md-2"><br>
                            @can('instructor.editar_fase2')
                                <button type="button" class="btn mr-sm-4 mt-3" @if($datainstructor->status != 'VALIDADO' && $datainstructor->status != 'EN CAPTURA' && $datainstructor->status != 'REACTIVACION EN CAPTURA') disabled @endif
                                    data-toggle="modal"
                                    data-placement="top"
                                    data-target="#modentrevistaModal"
                                    data-id='{{$datainstructor->id}}'><small><small>Modificar Entrevista</small></small>
                                </button>
                            @endcan
                        </div>
                        <div class="form-group col-md-3"><br>

                            <a class="btn mr-sm-4 mt-3" href="{{ route('instructor-entrevista-pdf', ['idins' => $id]) }}" target="_blank"><small><small>Generar PDF de entrevista</small></small></a>
                        </div>
                    @else
                        <div class="pull-right">
                            @can('instructor.editar_fase2')
                                <button type="button" class="btn mr-sm-4 mt-3" @if(!in_array($datainstructor->status, $ari)) disabled @endif
                                    data-toggle="modal"
                                    data-placement="top"
                                    data-target="#entrevistaModal"
                                    data-id='{{$datainstructor->id}}'><small>Llenar Entrevista</small>
                                </button>
                            @endcan
                        </div>
                    @endif
                    <div class="form-group col-md-3"><br>
                        <table class="table table-borderless table-responsive-md" id="table-perfprof2">
                            <tbody>
                                <tr >
                                    <td></td>
                                    <td id="center">
                                        Entrevista
                                    </td>
                                    <td></td>
                                    <td id="center">
                                        @if(!isset($datainstructor->entrevista['link']))
                                            <i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i>
                                        @else
                                            <a href={{$datainstructor->entrevista['link']}} target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                                        @endif
                                    </td>
                                    <td></td>
                                    <td id="center" width="160px">
                                        <label class='onpoint' for="arch_entrevista">
                                            <button type="button" class="btn mr-sm-4 mt-3 btn-sm" @if(!in_array($datainstructor->status, $ari)) disabled @endif
                                                data-toggle="modal"
                                                data-placement="top"
                                                data-target="#updentrevistaModal"
                                                data-id='{{$datainstructor->id}}'>Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                            </button>
                                        </label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                <hr style="border-color:dimgray">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h4>Perfiles Profesionales</h4>
                        </div>
                    @php $b = FALSE; foreach($perfil as $chkst){ switch($chkst->status){case 'VALIDADO': $b = TRUE; break; case 'EN CAPTURA': $b = TRUE; break;}} @endphp
                    @if($datainstructor->status == 'VALIDADO' || $datainstructor->status == 'EN CAPTURA' || $datainstructor->status == 'RETORNO')
                        <div class="pull-right">
                            @can('instructor.editar_fase2')
                                <button type="button" @if (count($perfil) == 0) class="d-none d-print-none" @else class="btn mr-sm-4 mt-3" @endif
                                    id = 'buttonaddperfprof'
                                    data-toggle="modal"
                                    data-placement="top"
                                    data-target="#addperprofModal"
                                    data-id='{{$datainstructor->id}}'>Agregar Perfil Profesional
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
            <div class="alert alert-success d-none d-print-none" id="newnrevisionwarning">
                <span id="newnrevisionspan"></span>
            </div>
            @if (count($perfil) > 0)
                <table class="table table-bordered table-responsive-md" id='tableperfiles'>
                    <thead>
                        <tr>
                            <th scope="col">Grado Profesional</th>
                            <th scope="col">Area de la Carrera</th>
                            <th scope="col">Nivel de Estudio</th>
                            <th scope="col">Nombre de Institucion</th>
                            <th scope="col">Status</th>
                            <th width="150px">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($perfil as $place => $item)
                        @php $loc = $place + 1 @endphp
                            <tr>
                                <th scope="row">{{$item->grado_profesional}}</th>
                                <td>{{ $item->area_carrera }}</td>
                                <td>{{ $item->estatus }}</td>
                                <td>{{ $item->nombre_institucion }}</td>
                                <td>{{ $item->status }}</td>
                                <td width="13%">
                                    @if(($datainstructor->status == 'VALIDADO' || $datainstructor->status == 'EN CAPTURA') || ($item->status == 'RETORNO' || $item->status == 'REVALIDACION RETORNADA'))
                                        @can('instructor.editar_fase2')
                                            <button type="button" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="MODIFICAR REGISTRO"
                                                data-toggle="modal"
                                                data-placement="top"
                                                data-target="#modperprofModal"
                                                data-id='["{{$item->grado_profesional}}","{{$item->area_carrera}}","{{$item->carrera}}","{{$item->estatus}}",
                                                    "{{$item->pais_institucion}}","{{$item->entidad_institucion}}","{{$item->ciudad_institucion}}",
                                                    "{{$item->nombre_institucion}}","{{$item->fecha_expedicion_documento}}","{{$item->periodo}}","{{$item->folio_documento}}",
                                                    "{{$item->cursos_recibidos}}","{{$item->capacitador_icatech}}","{{$item->recibidos_icatech}}",
                                                    "{{$item->cursos_impartidos}}","{{$item->id}}","{{$datainstructor->id}}","{{$loc}}"]'>
                                                    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
                                            </button>
                                        @endcan
                                        <button type="button" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="VER REGISTRO"
                                            data-toggle="modal"
                                            data-placement="top"
                                            data-target="#verperfprofModal"
                                            @if($item->status != 'VALIDADO')
                                                data-id='["{{$item->grado_profesional}}","{{$item->area_carrera}}","{{$item->carrera}}","{{$item->estatus}}",
                                                    "{{$item->pais_institucion}}","{{$item->entidad_institucion}}","{{$item->ciudad_institucion}}",
                                                    "{{$item->nombre_institucion}}","{{$item->fecha_expedicion_documento}}","{{$item->periodo}}","{{$item->folio_documento}}",
                                                    "{{$item->cursos_recibidos}}","{{$item->capacitador_icatech}}","{{$item->recibidos_icatech}}",
                                                    "{{$item->cursos_impartidos}}","{{$item->id}}","{{$item->status}}"]'
                                            @else
                                                data-id='["{{$item->id}}","{{$item->status}}"]'
                                            @endif
                                            ><i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="VER REGISTRO"
                                            data-toggle="modal"
                                            data-placement="top"
                                            data-target="#verperfprofModal"
                                            @if($item->status != 'VALIDADO')
                                                data-id='["{{$item->grado_profesional}}","{{$item->area_carrera}}","{{$item->carrera}}","{{$item->estatus}}",
                                                    "{{$item->pais_institucion}}","{{$item->entidad_institucion}}","{{$item->ciudad_institucion}}",
                                                    "{{$item->nombre_institucion}}","{{$item->fecha_expedicion_documento}}","{{$item->periodo}}","{{$item->folio_documento}}",
                                                    "{{$item->cursos_recibidos}}","{{$item->capacitador_icatech}}","{{$item->recibidos_icatech}}",
                                                    "{{$item->cursos_impartidos}}","{{$item->id}}","{{$item->status}}"]'
                                            @else
                                                data-id='["{{$item->id}}","{{$item->status}}"]'
                                            @endif
                                            ><i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                    @if($item->status == "EN CAPTURA")
                                        {{-- @can('instructor.editar_fase2')
                                            <button type="button" class="btn btn-warning mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="ELIMINAR REGISTRO"
                                                data-toggle="modal"
                                                data-placement="top"
                                                data-target="#delperprofModal"
                                                data-id='["{{$item->id}}","{{$loc}}"]'>
                                                    <i class="fa fa-eraser" aria-hidden="true"></i>
                                            </button>
                                        @endcan --}}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                @if(isset($datainstructor->entrevista))
                    <div class="pull-left alert alert-warning" id='warning1'>
                        <strong>Info!</strong> No hay Registros
                        @if($datainstructor->status == 'VALIDADO' || $datainstructor->status == 'EN CAPTURA')
                            <div class="pull-right">
                                @can('instructor.editar_fase2')
                                    <button type="button" class="btn mr-sm-4 mt-3"
                                        data-toggle="modal"
                                        data-placement="top"
                                        data-target="#addperprofModal"
                                        data-id='{{$datainstructor->id}}'>Agregar Perfil Profesional
                                    </button>
                                @endcan
                            </div>
                        @endif
                    </div>
                @else
                    <strong>Info!</strong> Llene la entrevista para candidatos a instructores antes de proseguir
                    <div class="pull-right">
                        @can('instructor.editar_fase2')
                            <button type="button" class="btn mr-sm-4 mt-3"
                                data-toggle="modal"
                                data-placement="top"
                                data-target="#entrevistaModal"
                                data-id='{{$datainstructor->id}}'>Entrevista para Candidatos a Instructores
                            </button>
                        @endcan
                    </div>
                @endif
                <table class="table table-bordered table-responsive-md" id='tableperfiles'>
                </table>
            @endif
            <br>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h4>Especialidades a Impartir</h4>
                    </div>
                    @if (count($validado) > 0)
                        @if($datainstructor->status == 'VALIDADO' || $datainstructor->status == 'EN CAPTURA' || $datainstructor->status == 'RETORNO')
                            <div class="pull-right">
                                @can('instructor.editar_fase2')
                                    <a class="btn mr-sm-4 mt-3" href="{{ route('cursoimpartir-form', ['idins' => $id]) }}">Agregar Especialidad Validado para Impartir</a>
                                @endcan
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            @if (count($validado) > 0)
                <table class="table table-bordered table-responsive-md" id="espec-table">
                    <thead>
                        <tr>
                            <th scope="col">Especialidad</th>
                            <th scope="col">Memo. Validación</th>
                            <th scope="col" width="90px">Fecha de Validación</th>
                            <th scope="col" width="20px">Criterio Pago</th>
                            <th scope="col">Obsevaciones</th>
                            <th scope="col">Status</th>
                            <th width="150px">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($validado as $place2 => $item)
                            @php
                                $loc2 = $place2 + 1; foreach ($perfil as $finder)
                                {
                                    if($finder->id == $item->perfilprof_id)
                                    {
                                        $perfilprof_nom = $finder->area_carrera;
                                    }
                                }
                            @endphp
                            @if($item->status != 'INACTIVO')
                                <tr>
                                    <th scope="row">{{$item->nombre}}</th>
                                    <td>{{ $item->memorandum_validacion}}</td>
                                    <td>{{ $item->fecha_validacion}}</td>
                                    <td style="text-align: center;">{{ $item->criterio_pago_id }}</td>
                                    <td>{{ $item->observacion }}</td>
                                    <td>{{ $item->status}}</td>
                                    <td><small>
                                        @if($datainstructor->status == 'VALIDADO' || $datainstructor->status == 'EN CAPTURA' || $datainstructor->status == 'RETORNO')
                                            @can('instructor.editar_fase2')
                                                <!--<a class="btn btn-info" href="{ route('instructor-editespectval', ['id' => item->especialidadinsid,'idins' => datains->id]) }}">Modificar</a>-->
                                                <button type="button" class="btn  mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="VER REGISTRO"
                                                    data-toggle="modal"
                                                    data-placement="top"
                                                    data-target="#verespevaliModal"
                                                    @if($item->status != 'VALIDADO')
                                                    data-id='["{{$item->nombre}}", "{{$perfilprof_nom}}","{{$item->unidad_solicita}}",
                                                            "{{$item->criterio_pago_id}}","{{$item->memorandum_solicitud}}","{{$item->fecha_solicitud}}",
                                                            "{{$item->observacion}}","{{$item->status}}","{{$item->espinid}}"]'
                                                    @else
                                                        data-id='["{{$item->espinid}}","{{$item->status}}"]'
                                                    @endif
                                                    ><i class="fa fa-eye" aria-hidden="true"></i>
                                                </button>
                                                @if($item->status != 'BAJA EN PREVALIDACION')
                                                    <a class="btn  mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="MODIFICAR REGISTRO" href="{{ route('instructor-editespectval', ['id' => $item->espinid, 'idins' => $id]) }}"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                                                    <button type="button" class="btn btn-warning mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="BAJA DE ESPECIALIDAD"
                                                        data-toggle="modal"
                                                        data-placement="top"
                                                        data-target="#bajaespeModal"
                                                        data-id='{{$item->espinid}}'>
                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                    </button>
                                                @endif
                                            @endcan
                                        @else
                                            <button type="button" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="VER REGISTRO"
                                                data-toggle="modal"
                                                data-placement="top"
                                                data-target="#verespevaliModal"
                                                @if($item->status != 'VALIDADO')
                                                    data-id='["{{$item->nombre}}","{{$perfilprof_nom}}","{{$item->unidad_solicita}}",
                                                            "{{$item->criterio_pago_id}}","{{$item->memorandum_solicitud}}","{{$item->fecha_solicitud}}",
                                                            "{{$item->observacion}}","{{$item->status}}","{{$item->espinid}}","{{$item->cursos_impartir}}"]'
                                                @else
                                                    data-id='["{{$item->espinid}}","{{$item->status}}"]'
                                                @endif
                                                ><i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                        @if($item->new)
                                            <button type="button" class="btn btn-warning mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="ELIMINAR REGISTRO"
                                                data-toggle="modal"
                                                data-placement="top"
                                                data-target="#delespecvalidModal"
                                                data-id='["{{$item->espinid}}","{{$loc2}}","{{$datainstructor->id}}"]'>
                                                    <i class="fa fa-eraser" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                        @if(isset($item->hvalidacion))
                                            @php if(!is_array($item->hvalidacion)){ $item->hvalidacion = json_decode($item->hvalidacion); if(is_array($item->hvalidacion)){ $item->hvalidacion = json_encode($item->hvalidacion);}} @endphp
                                            <button type="button" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="VALIDACIÓN"
                                                data-toggle="modal"
                                                data-placement="top"
                                                data-target="#validacionesModal"
                                                data-id='{{$item->hvalidacion}}'>
                                                    <i class="far fa-file-pdf" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </small></td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning">
                    @if (count($perfil) == 0)
                        <div id='divnonperfil'>
                            <strong>Info!</strong> No hay Registros en Perfil Profesional, Añada Uno para Poder Agregar una Especialidad a Validar
                        </div>
                    @endif
                    <div id=divperfil @if(count($perfil) == 0) class='d-none d-print-none' @endif>
                        <strong>Info!</strong> No hay Registros
                        @if($datainstructor->status == 'VALIDADO' || $datainstructor->status == 'EN CAPTURA')
                            <div class="pull-right">
                                @can('instructor.editar_fase2')
                                    <a class="btn mr-sm-4 mt-3" href="{{ route('cursoimpartir-form', ['idins' => $id]) }}">Agregar Especialidad Validado para Impartir</a>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            <hr style="border-color:dimgray">
            <div>
                <label><h3>Curriculum Vitae: ICATECH</h3></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-3"><br>
                    <a class="btn mr-sm-4 mt-3" href="{{ route('instructor-curriculumicatech-pdf', ['idins' => $id]) }}" target="_blank"><small><small>Generar PDF de curriculum</small></small></a>
                </div>
                <div class="form-group col-md-3"><br>
                    {{-- <label for="inputarch_ine">Archivo Identificación</label> --}}
                    <table class="table table-borderless table-responsive-md" id="table-perfprof2">
                        <tbody>
                            <tr >
                                <td></td>
                                <td id="center">
                                    Curriculum
                                </td>
                                <td></td>
                                <td id="center">
                                    @if($datainstructor->curriculum == NULL)
                                        <i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i>
                                    @else
                                        <a href={{$datainstructor->curriculum}} target="_blank"><i  class="far fa-file-pdf  fa-2x fa-lg text-danger from-control"></i></a>
                                    @endif
                                </td>
                                <td></td>
                                <td id="center" width="160px">
                                    <label class='onpoint' for="arch_curriculum">
                                        <button type="button" class="btn mr-sm-4 mt-3 btn-sm" @if($datainstructor->status != 'VALIDADO' && $datainstructor->status != 'EN CAPTURA' && $datainstructor->status != 'RETORNO') disabled @endif
                                            data-toggle="modal"
                                            data-placement="top"
                                            data-target="#updcurriculumModal"
                                            data-id='{{$datainstructor->id}}'>Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                        </button>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @if(($datainstructor->status == 'VALIDADO' || $datainstructor->status == 'EN CAPTURA' || $datainstructor->status == 'RETORNO') &&  in_array($roluser->role_id, ['30','31']) && $datainstructor->estado)
                <hr style="border-color:dimgray">
                <label><h2>Solicitar Baja del Instructor</h2></label>
                <div class="form-group col-md-8">
                    <button type="button" class="btn btn-danger"
                        data-toggle="modal"
                        data-placement="top"
                        data-target="#bajainstructorModal"
                        data-id='{{$datainstructor->id}}'>Solicitar Baja
                    </button>
                </div>
            @endif
            @if($datainstructor->status == 'BAJA')
                <hr style="border-color:dimgray">
                <label><h2>Solicitar Reactivación del Instructor</h2></label>
                <div class="form-group col-md-8">
                    <button type="button" class="btn btn-danger"
                        data-toggle="modal"
                        data-placement="top"
                        data-target="#reacinstructorModal"
                        data-id='{{$datainstructor->id}}'>Solicitar Reactivación
                    </button>
                    {{-- <a class="btn btn-danger" href="{{ route('instructor-alta_baja', ['id' => $datainstructor->id]) }}" >Solicitar Baja</a> --}}
                    {{-- <footer>El instructor dado de baja puede ser dado de alta de nuevo en cualquier momento necesario y viceversa.</footer> --}}
                </div>
            @endif
            @can('only_admins_wsp')
                <hr style="border-color:dimgray">
                <div>
                    <label><h3>Reenviar Credenciales de acceso para e.firma</h3></label>
                </div>
                <div class="form-row">
                    <div class="pull-right">
                        <a class="btn btn-info btn-sm mr-sm-4 mt-3"
                           href="{{ route('reenviar-wsp-instructor', ['idins' => $id]) }}"
                           title="Reenviar credenciales de acceso por WhatsApp"
                           data-toggle="tooltip"
                           data-placement="top">
                            <i class="fa fa-envelope"></i> Reenviar Credenciales
                        </a>
                    </div>
                </div>
            @endcan
            @if($datainstructor->status != 'VALIDADO' || $datainstructor->status != 'BAJA')
                <hr style="border-color:dimgray">
                <div>
                    <label><h2>Numero de Revisión</h2></label>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <br>
                        <input class="form-control" type="text" name="nrevision" id="nrevision" value='{{$datainstructor->nrevision}}' readonly>
                    </div>
                    @can('instructor.editar_fase2')
                        <div class="form-group col-md-4" style="padding-top: 5px;">
                            <a class="btn mr-sm-4 mt-3" href="{{ route('instructor.deshacer-movimimiento', ['id' => $datainstructor->id]) }}" >DESHACER CAMBIOS</a>
                        </div>
                    @endcan
                    <div class="form-group col-md-1">
                        @if(!isset($nrevisionlast->nrevision))
                            <input hidden value={{$nrevisionlast}} id="revlast" name="revlast">
                        @else
                            <input hidden value={{$nrevisionlast->nrevision}} id="revlast" name="revlast">
                        @endif
                        <input hidden value="{{$userunidad->ubicacion}}" id="userunidad" name="userunidad">
                    </div>
                </div>
            @endif
            <br>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn mr-sm-4 mt-3" href="{{URL::previous()}}">REGRESAR</a>
                    </div>
                    @if($roluser->role_id == 61)
                    <input type="hidden" name="id" id="id" value="{{$id}}">
                    @if($datainstructor->status == 'VALIDADO' || $datainstructor->status == 'EN CAPTURA' || $datainstructor->status == 'RETORNO')
                        <div class="pull-right">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    @can('instructor.editar_fase2')
                                        {{-- <button type="submit" class="btn mr-sm-4 mt-3 btn-danger">ENVIAR A DTA</button> --}}
                                        <button type="submit" class="btn mr-sm-4 mt-3 btn-danger">GUARDAR CAMBIOS</button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endif
                    <input type="hidden" name="id" id="id" value="{{$id}}">
                    </form>
                    <form action="{{ route('movimiento-retorno') }}" enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="pull-right form-group">
                            <label>MOVIMIENTO</label>
                            <select class="form-control" name="movimiento" id="movimiento">
                                <option value="sin especificar">SELECCIONE</option>
                                <option value="retornar en firma">RETORNAR A FIRMA</option>
                                <option value="retornar a captura">RETORNAR A CAPTURA</option>
                            </select>
                        </div>
                         <div class="pull-right form-group col-md-2">
                            <label>Status: {{$datainstructor->status}}</label>
                            <button type="submit" class="btn btn-danger form-control" id="enviarMovimiento">ACEPTAR</button>
                        </div>
                        <input type="hidden" name="id" id="id" value="{{$id}}">
                    @else
                        <input type="hidden" name="id" id="id" value="{{$id}}">
                        @if($datainstructor->status == 'VALIDADO' || $datainstructor->status == 'EN CAPTURA' || $datainstructor->status == 'RETORNO')
                            <div class="pull-right">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        @can('instructor.editar_fase2')
                                            {{-- <button type="submit" class="btn mr-sm-4 mt-3 btn-danger">ENVIAR A DTA</button> --}}
                                            <button type="submit" class="btn mr-sm-4 mt-3 btn-danger">GUARDAR CAMBIOS</button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endif
                        <input type="hidden" name="id" id="id" value="{{$id}}">
                    @endif
                </div>
            </div>
        </div>
    </form>
    <!-- Modal Agregar Perfil Profesional -->
    <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" id="addperprofModal" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Añadir Perfil Profesional</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="card card-body" >
                        <label><h2>Añadir Perfil Profesional</h2></label>
                        <div class="alert alert-danger d-none d-print-none" id="addperprofwarning">
                            <span id="addperfprofspan"></span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="inputgrado_prof">Nivel Educativo</label>
                                <select class="form-control" name="grado_prof" id="grado_prof">
                                    <option value="sin especificar">Sin Especificar</option>
                                    <option value="PRIMARIA">Primaria</option>
                                    <option value="SECUNDARIA">Secundaria</option>
                                    <option value="BACHILLERATO">Bachillerato</option>
                                    <option value="CARRERA TÉCNICA">Carrera Técnica</option>
                                    <option value="LICENCIATURA">Licenciatura</option>
                                    <option value="MAESTRIA">Maestría</option>
                                    <option value="DOCTORADO">Doctorado</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="inputarea_carrera">Area de la carrera</label>
                                <input name="area_carrera" id="area_carrera" type="text" class="form-control" aria-required="true" maxlength="50">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="inputgrado_prof">Nombre de la Carrera</label>
                                <input name="carrera" id="carrera" type="text" class="form-control" aria-required="true" maxlength="50">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="inputestatus">Documento Obtenido</label>
                                <select class="form-control" name="estatus" id="estatus">
                                    <option value="sin especificar">Sin Especificar</option>
                                    <option value="CONSTANCIA">Constancia</option>
                                    <option value="CERTIFICADO">Certificado</option>
                                    <option value="TITULO">Titulo</option>
                                    <option value="CEDULA">Cedula</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="inputinstitucion_pais">Pais de la Institución Educativa</label>
                                <input name="institucion_pais" id="institucion_pais" type="text" class="form-control" aria-required="true">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputinstitucion_entidad">Entidad de la Institución Educativa</label>
                                <input name="institucion_entidad" id="institucion_entidad" type="text" class="form-control" aria-required="true">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputinstitucion_ciudad">Ciudad de la Institución Educativa</label>
                                <input name="institucion_ciudad" id="institucion_ciudad" type="text" class="form-control" aria-required="true">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputinstitucion_nombre">Nombre de la Institución Educativa</label>
                                <input name="institucion_nombre" id="institucion_nombre" type="text" class="form-control" aria-required="true" maxlength="50">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputfecha_documento">Fecha de Expedicion del Documento</label>
                                <input name="fecha_documento" id="fecha_documento" type="date" class="form-control" aria-required="true" maxlength="50">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputperiodo">Periodo Escolar Cursado</label>
                                <input name="periodo" id="periodo" type="text" class="form-control" aria-required="true">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputfolio_documento">Folio del Documento</label>
                                <input name="folio_documento" id="folio_documento" type="text" class="form-control" aria-required="true">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="inputicursos_recibidos">Cursos Recibidos</label>
                                <select name="cursos_recibidos" id="cursos_recibidos" class="form-control">
                                    <option value="sin especificar">SIN ESPECIFICAR</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="inputicapacitador_icatech">Capacitador ICATECH</label>
                                <select name="capacitador_icatech" id="capacitador_icatech" class="form-control">
                                    <option value="sin especificar">SIN ESPECIFICAR</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="inputcursos_recibidos"><small>Cursos Recibidos ICATECH</small></label>
                                <select name="recibidos_icatech" id="recibidos_icatech" class="form-control">
                                    <option value="sin especificar">SIN ESPECIFICAR</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="inputcursos_impartidos">Cursos Impartidos</label>
                                <select name="cursos_impartidos" id="cursos_impartidos" class="form-control">
                                    <option value="sin especificar">SIN ESPECIFICAR</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                        </div>
                        {{-- <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h2>Experiencia Laboral</h2></label>
                                <textarea name="exp_lab" id="exp_lab" class="form-control" cols="5" rows="8"></textarea>
                            </div>
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h2>Experiencia Docente</h2></label>
                                <textarea name="exp_doc" id="exp_doc" class="form-control" cols="5" rows="8"></textarea>
                            </div>
                        </div> --}}
                        <div class="form-row">
                            <div class="form-group col-md-11" style="text-align: right;width:100%">
                                <button onclick="saveperprof()" class="btn mr-sm-4 mt-3" >Agregar</button>
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idInstructor" id="idInstructor">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Modificar Perfil Profesional -->
    <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" id="modperprofModal" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Modificar Perfil Profesional</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="card card-body" >
                        <label><h2>Modificar Perfil Profesional</h2></label>
                        <div class="alert alert-danger d-none d-print-none" id="modperprofwarning">
                            <span id="modperfprofspan"></span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="inputgrado_prof">Grado Profesional</label>
                                <select class="form-control" name="grado_prof2" id="grado_prof2">
                                    <option value="sin especificar">Sin Especificar</option>
                                    <option value="PRIMARIA">Primaria</option>
                                    <option value="SECUNDARIA">Secundaria</option>
                                    <option value="BACHILLERATO">Bachillerato</option>
                                    <option value="CARRERA TÉCNICA">Carrera Técnica</option>
                                    <option value="LICENCIATURA">Licenciatura</option>
                                    <option value="MAESTRIA">Maestría</option>
                                    <option value="DOCTORADO">Doctorado</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="inputarea_carrera">Area de la Carrera</label>
                                <input name="area_carrera2" id="area_carrera2" type="text" class="form-control" aria-required="true">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="inputcarrera">Nombre de la Carrera</label>
                                <input name="carrera2" id="carrera2" type="text" class="form-control" aria-required="true">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="inputestatus">Documento Obtenido</label>
                                <select class="form-control" name="estatus2" id="estatus2">
                                    <option value="sin especificar">Sin Especificar</option>
                                    <option value="CONSTANCIA">Constancia</option>
                                    <option value="CERTIFICADO">Certificado</option>
                                    <option value="TITULO">Titulo</option>
                                    <option value="CEDULA">Cedula</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="inputinstitucion_pais">Pais de la Institución Educativa</label>
                                <input name="institucion_pais2" id="institucion_pais2" type="text" class="form-control" aria-required="true">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputinstitucion_entidad">Entidad de la Institución Educativa</label>
                                <input name="institucion_entidad2" id="institucion_entidad2" type="text" class="form-control" aria-required="true">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="inputinstitucion_ciudad">Ciudad de la Institución Educativa</label>
                                <input name="institucion_ciudad2" id="institucion_ciudad2" type="text" class="form-control" aria-required="true">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputinstitucion_nombre">Nombre de la Institución Educativa</label>
                                <input name="institucion_nombre2" id="institucion_nombre2" type="text" class="form-control" aria-required="true">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputfecha_documento"><small>Fecha de Expedicion del Documento</small></label>
                                <input name="fecha_documento2" id="fecha_documento2" type="date" class="form-control" aria-required="true">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="inputperiodo">Periodo Escolar Cursado</label>
                                <input name="periodo2" id="periodo2" type="text" class="form-control" aria-required="true">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="inputfolio_documento">Folio del Documento</label>
                                <input name="folio_documento2" id="folio_documento2" type="text" class="form-control" aria-required="true">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="inputicursos_recibidos">Cursos Recibidos</label>
                                <select name="cursos_recibidos2" id="cursos_recibidos2" class="form-control">
                                    <option value="sin especificar">SIN ESPECIFICAR</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="inputicapacitador_icatech">Capacitador ICATECH</label>
                                <select name="capacitador_icatech2" id="capacitador_icatech2" class="form-control">
                                    <option value="sin especificar">SIN ESPECIFICAR</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="inputcursos_recibidos"><small>Cursos Recibidos ICATECH</small></label>
                                <select name="recibidos_icatech2" id="recibidos_icatech2" class="form-control">
                                    <option value="sin especificar">SIN ESPECIFICAR</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="inputcursos_impartidos">Cursos Impartidos</label>
                                <select name="cursos_impartidos2" id="cursos_impartidos2" class="form-control">
                                    <option value="sin especificar">SIN ESPECIFICAR</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                        </div>
                        {{-- <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h2>Experiencia Laboral</h2></label>
                                <textarea name="exp_lab2" id="exp_lab2" class="form-control" cols="5" rows="8"></textarea>
                            </div>
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h2>Experiencia Docente</h2></label>
                                <textarea name="exp_doc2" id="exp_doc2" class="form-control" cols="5" rows="8"></textarea>
                            </div>
                        </div> --}}
                        <div class="form-row">
                            <div class="form-group col-md-11" style="text-align: right;width:100%">
                                <button onclick="savemodperprof()" class="btn mr-sm-4 mt-3" >Modificar</button>
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idperfprof2" id="idperfprof2">
                        <input type="hidden" name="idInstructor2" id="idInstructor2">
                        <input type="hidden" name="row" id="row">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Eliminar Perfil Profesional -->
    <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="delperprofModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Eliminar Perfil Profesional</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card card-body" >
                    <div class="alert alert-danger d-none d-print-none" id="delperprofwarning">
                        <span id="delperfprofspan"></span>
                    </div>
                    <label style="text-align:center"><h5><small>¿Desea confirmar la eliminación de este perfil profesional?</small></h5></label>
                    <div class="form-row">
                        <div class="form-group col-md-11" style="text-align:center;width:100%">
                            <button onclick="delperprof()" class="btn btn-warning mt-3" >Eliminar</button>
                        </div>
                    </div>
                    <br>
                    <input type="hidden" name="iddelperfprof" id="iddelperfprof">
                    <input type="hidden" name="locdel" id="locdel">
                </div>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Eliminar Especialidad a Validar -->
    <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="delespecvalidModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Eliminar Especialidad a Impartir</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card card-body" >
                    <div class="alert alert-danger d-none d-print-none" id="delespecvalidwarning">
                        <span id="delespecvalidspan"></span>
                    </div>
                    <label style="text-align:center"><h5><small>¿Desea confirmar la eliminación de esta especialidad a impartir?</small></h5></label>
                    <div class="form-row">
                        <div class="form-group col-md-11" style="text-align:center;width:100%">
                            <button onclick="delespecvalid()" class="btn btn-warning mt-3" >Eliminar</button>
                        </div>
                    </div>
                    <br>
                    <input @if($datainstructor->turnado != "VALIDADO") disabled @endif hidden  name="idespecvalid" id="idespecvalid">
                    <input @if($datainstructor->turnado != "VALIDADO") disabled @endif hidden name="loc2del" id="loc2del">
                    <input @if($datainstructor->turnado != "VALIDADO") disabled @endif hidden name="idinsespecvalid" id="idinsespecvalid">
                </div>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Enviar a DTA -->
    <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="sendtodtaModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Enviar a DTA</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card card-body" >
                    <form action="{{ route('ins-to-dta') }}" id="regsupre" method="POST">
                        @csrf
                        <div class="alert alert-danger d-none d-print-none" id="sendtodtawarning">
                            <span id="sendtodtaspan"></span>
                        </div>
                        <label style="text-align:center"><h5><small>¿Desea confirmar el envio a prevalidacion de este instructor?</small></h5></label>
                        <div class="form-row">
                            <div class="form-group col-md-1">
                            </div>
                            <div class="form-group col-md-10">
                                <label for="inputmemosol">Numero de Memorandum para la Solicitud</label>
                                <input name="memosol" id="memosol" type="text" class="form-control" aria-required="true" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12" style="text-align:center;width:100%">
                                <button onclick="sendtodta()" class="btn mr-sm-4 mt-3 btn-danger" >Enviar a DTA</button>
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idtodta" id="idtodta">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Ver Perfil Profesional -->
    <div class="modal fade right" id="verperfprofModal" role="dialog">
        <div class="modal-dialog modal-full-height modal-right">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Informacion Del Perfil Profesional</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div id="listaperfprof">
                    </div>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Ver Especialidad Validada -->
    <div class="modal fade right" id="verespevaliModal" role="dialog">
        <div class="modal-dialog modal-full-height modal-right">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Informacion De La Especialidad a Validar</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div id="listaespevali">
                    </div>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Prevalidar -->
    <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="prevalidarModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Prevalidar Instructor</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card card-body" >
                    <form action="{{ route('instructor-prevalidar') }}" id="regsupre" method="POST">
                        @csrf
                        <div class="alert alert-danger d-none d-print-none" id="prevalidarwarning">
                            <span id="prevalidarspan"></span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-1"></div>
                            <div class="form-group col-md-11" style="text-aling:center;">
                                <label style="text-align:center"><h5><small>¿Desea confirmar la prevalidación de este instructor?</small></h5></label>
                            </div>
                        </div>
                        {{-- <div class="form-row">
                            <div class="form-group col-md-1">
                            </div>
                            <div class="form-group col-md-10">
                                <label for="inputmemosol">Numero de Memorandum de la Prevalidación</label>
                                <input name="memosol" id="memosol" type="text" class="form-control" aria-required="true" required>
                            </div>
                        </div> --}}
                        <div class="form-row">
                            <div class="form-group col-md-12" style="text-align:center;width:100%">
                                <button type="submit" class="btn mr-sm-4 mt-3 btn-danger" >Prevalidar</button>
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idinspreval" id="idinspreval">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Retornar a Unidad -->
    <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="returntounitModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Retornar Instructor a Unidad</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card card-body" >
                    <form action="{{ route('instructor-rechazo') }}" id="regsupre" method="POST">
                        @csrf
                        <div class="alert alert-danger d-none d-print-none" id="returntounitwarning">
                            <span id="returntounitspan"></span>
                        </div>
                        <div class="form-row">
                            {{-- <div class="form-group col-md-1"></div> --}}
                            <div class="form-group col-md-12" style="text-aling:center;">
                                <label style="text-align:center"><h5><small>¿Desea confirmar el retorno a unidad de este instructor?</small></h5></label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-1">
                            </div>
                            <div class="form-group col-md-10">
                                <label for="inputmemosol">Observaciones</label>
                                <textarea name="observacionreturn" id="observacionreturn" cols="30" rows="10" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12" style="text-align:center;width:100%">
                                <button type="submit" class="btn mr-sm-4 mt-3 btn-danger" >Retornar a Unidad</button>
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idinsreturn" id="idinsreturn">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Historial de Validaciones de Instructor-->
    <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="validacionesModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Historial de Validaciones</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card card-body" >
                    <div class="alert alert-danger d-none d-print-none" id="validacioneswarning">
                        <span id="validacionesspan"></span>
                    </div>
                    <div class="form-row">
                        {{-- <div class="form-group col-md-1"></div> --}}
                        <div class="form-group col-md-12" style="text-aling:center;">
                            <label style="text-align:center"><h5><small>Seleccione el memorandum de validación a visualizar</small></h5></label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-1">
                        </div>
                        <div class="form-group col-md-10">
                            <label for="inputmemosol">Lista de Validaciones</label>
                            <select class="form-control" name="validacionpdf" id="validacionpdf">
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12" style="text-align:center;width:100%">
                            <button onclick="validacionpdfview()" class="btn mr-sm-4 mt-3 btn-danger" >VER VALIDACIÓN</button>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Agregar Entrevista para Candidatos a Instructores -->
    <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" id="entrevistaModal" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Agregar Entrevista</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('save-entrevista') }}" method="post">
                    @csrf
                    <div class="card card-body" >
                        <label><h2>Agregar Entrevista para Candidatos a Instructores</h2></label>
                        <div class="alert alert-danger d-none d-print-none" id="entrevistawarning">
                            <span id="entrevistaspan"></span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Conoce Usted a que se dedica el ICATECH o ha escuchado de él? Indique</h5></label>
                                <textarea name="Q1" id="Q1" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿Qué lo motivó a impartir capacitación?</h5></label>
                                <textarea name="Q2" id="Q2" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Ha impartido cursos de Capacitación? Si, ¿Cuáles?</h5></label>
                                <textarea name="Q3" id="Q3" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿Cómo se llamó el último curso que dio, en qué fecha lo dio y para quién lo otorgó? Indique si cuenta con documento que lo acredite</h5></label>
                                <textarea name="Q4" id="Q4" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Considera estar lo suficientemente actualizado o contar con dominio sobre la especialidad en la cual impartirá cursos de capacitación? ¿Por qué?</h5></label>
                                <textarea name="Q5" id="Q5" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿Con qué frecuencia busca temas actuales sobre la especialidad en la cual imparte cursos de capacitación? ¿Y qué medios utiliza?</h5></label>
                                <textarea name="Q6" id="Q6" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Ha elaborado guías pedagógicas?</h5></label>
                                <textarea name="Q7" id="Q7" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿Qué técnicas de Enseñanza- Aprendizaje utiliza con los alumnos? Describa.</h5></label>
                                <textarea name="Q8" id="Q8" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Cómo comprueba que los alumnos entienden lo que Usted les enseña?</h5></label>
                                <textarea name="Q9" id="Q9" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿Estaría dispuesto a recibir capacitación acerca de la especialidad en la cual se desarrollará?</h5></label>
                                <textarea name="Q10" id="Q10" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Cómo definiría su personalidad frente a grupo?</h5></label>
                                <textarea name="Q11" id="Q11" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿A qué dedica la mayoría de su tiempo?</h5></label>
                                <textarea name="Q12" id="Q12" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Estaría dispuesto a viajar a cualquier parte del Estado en el momento en que se le indique? En caso negativo ¿Por qué?</h5></label>
                                <textarea name="Q13" id="Q13" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿Cuenta Usted con recibos de Honorarios? ¿O en su caso, estaría dispuesto a tramitarlos ante el SAT?</h5></label>
                                <textarea name="Q14" id="Q14" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-11" style="text-align: right;width:100%">
                                <button type="submit" class="btn mr-sm-4 mt-3" >Agregar</button>
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idInstructorentrevista" id="idInstructorentrevista">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Modificar Entrevista para Candidatos a Instructores -->
    <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" id="modentrevistaModal" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Modificar Entrevista</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('save-mod-entrevista') }}" method="post">
                    @csrf
                    <div class="card card-body" >
                        <label><h2>Modificar Entrevista para Candidatos a Instructores</h2></label>
                        <div class="alert alert-danger d-none d-print-none" id="modentrevistawarning">
                            <span id="modentrevistaspan"></span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Conoce Usted a que se dedica el ICATECH o ha escuchado de él? Indique</h5></label>
                                <textarea name="MQ1" id="MQ1" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿Qué lo motivó a impartir capacitación?</h5></label>
                                <textarea name="MQ2" id="MQ2" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Ha impartido cursos de Capacitación? Si, ¿Cuáles?</h5></label>
                                <textarea name="MQ3" id="MQ3" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿Cómo se llamó el último curso que dio, en qué fecha lo dio y para quién lo otorgó? Indique si cuenta con documento que lo acredite</h5></label>
                                <textarea name="MQ4" id="MQ4" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Considera estar lo suficientemente actualizado o contar con dominio sobre la especialidad en la cual impartirá cursos de capacitación? ¿Por qué?</h5></label>
                                <textarea name="MQ5" id="MQ5" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿Con qué frecuencia busca temas actuales sobre la especialidad en la cual imparte cursos de capacitación? ¿Y qué medios utiliza?</h5></label>
                                <textarea name="MQ6" id="MQ6" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Ha elaborado guías pedagógicas?</h5></label>
                                <textarea name="MQ7" id="MQ7" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿Qué técnicas de Enseñanza- Aprendizaje utiliza con los alumnos? Describa.</h5></label>
                                <textarea name="MQ8" id="MQ8" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Cómo comprueba que los alumnos entienden lo que Usted les enseña?</h5></label>
                                <textarea name="MQ9" id="MQ9" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿Estaría dispuesto a recibir capacitación acerca de la especialidad en la cual se desarrollará?</h5></label>
                                <textarea name="MQ10" id="MQ10" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Cómo definiría su personalidad frente a grupo?</h5></label>
                                <textarea name="MQ11" id="MQ11" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿A qué dedica la mayoría de su tiempo?</h5></label>
                                <textarea name="MQ12" id="MQ12" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_lab"><h5>¿Estaría dispuesto a viajar a cualquier parte del Estado en el momento en que se le indique? En caso negativo ¿Por qué?</h5></label>
                                <textarea name="MQ13" id="MQ13" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md 5">
                                <label for="inputexp_doc"><h5>¿Cuenta Usted con recibos de Honorarios? ¿O en su caso, estaría dispuesto a tramitarlos ante el SAT?</h5></label>
                                <textarea name="MQ14" id="MQ14" class="form-control" cols="5" rows="8" required></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-11" style="text-align: right;width:100%">
                                <button type="submit" class="btn mr-sm-4 mt-3" >Modificar</button>
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idInstructorentrevistamod" id="idInstructorentrevistamod">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Subir Entrevista para Candidatos a Instructores -->
    <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="updentrevistaModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Subir Entrevista</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('save-upd-entrevista') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card card-body" >
                        <label><h5>Subir Entrevista para Candidatos a Instructores</h5></label>
                        <div class="alert alert-danger d-none d-print-none" id="updentrevistawarning">
                            <span id="updentrevistaspan"></span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2"></div>
                            <div class="form-group col-md-7">
                                <input type="file" accept="application/pdf" class="form-control" id="doc_entrevista" name="doc_entrevista" placeholder="Archivo PDF">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-8" style="text-align: right;width:100%">
                                <button type="submit" class="btn mr-sm-4 mt-3" >Modificar</button>
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idInstructorentrevistaupd" id="idInstructorentrevistaupd">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Subir Curriculum para Candidatos a Instructores -->
    <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="updcurriculumModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Subir Curriculum</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('save-upd-curriculum') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card card-body" >
                        <label><h5>Subir Curriculum ICATECH de Candidatos a Instructores</h5></label>
                        <div class="alert alert-danger d-none d-print-none" id="updcurriculumwarning">
                            <span id="updcurriculumspan"></span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2"></div>
                            <div class="form-group col-md-7">
                                <input type="file" accept="application/pdf" class="form-control" id="doc_curriculum" name="doc_curriculum" placeholder="Archivo PDF">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-8" style="text-align: right;width:100%">
                                <button type="submit" class="btn mr-sm-4 mt-3" >Subir</button>
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idInstructorcurriculumupd" id="idInstructorcurriculumupd">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Agregar Experiencia Docente -->
    <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" id="addexpdocModal" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Añadir Experiencia Docente</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="card card-body" >
                        <label><h2>Añadir Experiencia Docente</h2></label>
                        <div class="alert alert-danger d-none d-print-none" id="addexpdocwarning">
                            <span id="addexpdocspan"></span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="asignatura" class="form-label">Asignatura</label>
                                <input class="form-control" type="text" id="asignatura" name="asignatura">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="institucion" class="form-label">Institucion</label>
                                <input class="form-control" type="text" id="institucion" name="institucion">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="funcion" class="form-label">Funcion</label>
                                <input class="form-control" type="text" id="funcion" name="funcion">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="periodoadddoc" class="form-label">Periodo</label>
                                <input class="form-control" type="text" id="periodoadddoc" name="periodoadddoc">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12" style="text-align: right;width:100%">
                                <button onclick="saveexpdoc()" class="btn mr-sm-4 mt-3" >Agregar</button>
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idInstructorexpdoc" id="idInstructorexpdoc">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Eliminar Experiencia Docente -->
    <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="delexpdocModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Eliminar Experiencia Docente</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card card-body" >
                    <div class="alert alert-danger d-none d-print-none" id="delexpdocwarning">
                        <span id="delexpdocspan"></span>
                    </div>
                    <label style="text-align:center"><h5><small>¿Desea confirmar la eliminación de este registro?</small></h5></label>
                    <div class="form-row">
                        <div class="form-group col-md-11" style="text-align:center;width:100%">
                            <button onclick="delexpdoc()" class="btn btn-warning mt-3" >Eliminar</button>
                        </div>
                    </div>
                    <br>
                    <input type="hidden" name="idinsdelexpdoc" id="idinsdelexpdoc">
                    <input type="hidden" name="locdelexpdoc" id="locdelexpdoc">
                    <input type="hidden" name="asdel" id="asdel">
                    <input type="hidden" name="indel" id="indel">
                    <input type="hidden" name="fudel" id="fudel">
                    <input type="hidden" name="pedel" id="pedel">
                </div>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Agregar Experiencia Laboral -->
    <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" id="addexplabModal" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Añadir Experiencia Laboral</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="card card-body" >
                        <label><h2>Añadir Experiencia Laboral</h2></label>
                        <div class="alert alert-danger d-none d-print-none" id="addexplabwarning">
                            <span id="addexplabspan"></span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="asignatura" class="form-label">Puesto</label>
                                <input class="form-control" type="text" id="puestolab" name="puestolab">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="periodo" class="form-label">Periodo</label>
                                <input class="form-control" type="text" id="periodolab" name="periodolab">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="institucion" class="form-label">Institucion</label>
                                <input class="form-control" type="text" id="institucionlab" name="institucionlab">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12" style="text-align: right;width:100%">
                                <button onclick="saveexplab()" class="btn mr-sm-4 mt-3" >Agregar</button>
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idInstructorexplab" id="idInstructorexplab">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Eliminar Experiencia Docente -->
    <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="delexplabModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Eliminar Experiencia Laboral</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card card-body" >
                    <div class="alert alert-danger d-none d-print-none" id="delexplabwarning">
                        <span id="delexplabspan"></span>
                    </div>
                    <label style="text-align:center"><h5><small>¿Desea confirmar la eliminación de este registro?</small></h5></label>
                    <div class="form-row">
                        <div class="form-group col-md-11" style="text-align:center;width:100%">
                            <button onclick="delexplab()" class="btn btn-warning mt-3" >Eliminar</button>
                        </div>
                    </div>
                    <br>
                    <input type="hidden" name="idinsdelexplab" id="idinsdelexplab">
                    <input type="hidden" name="locdelexplab" id="locdelexplab">
                    <input type="hidden" name="pulabdel" id="pulabdel">
                    <input type="hidden" name="pelabdel" id="pelabdel">
                    <input type="hidden" name="inlabdel" id="inlabdel">
                </div>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Baja Instructor -->
    <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="bajainstructorModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Solicitud de Baja de Instructor</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card card-body" >
                    <form action="{{ route('instructor-solicitud-baja') }}" method="post" id="registerperf_prof">
                        @csrf
                        <div class="alert alert-danger d-none d-print-none" id="bajainstructorwarning">
                            <span id="bajainstructorspan"></span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-1"></div>
                            <div class="form-group col-md-10" style="text-align:center;width:100%">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style="text-align:center"><h5><b>¿Desea confirmar la solicitud de baja?</b></h5></label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-1"></div>
                            <div class="form-group col-md-10" style="text-align:center;width:100%">
                                <textarea name="motivo_baja" id="motivo_baja" cols="30" rows="10"></textarea>
                                {{-- <a class="btn btn-danger" href="{{ route('instructor-solicitud-baja', ['id' => $datainstructor->id]) }}" >Solicitar Baja</a> --}}
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-11" style="text-align:center;width:100%">
                                <button type="submit" class="btn btn-danger mt-3" >Confirmar</button>
                                {{-- <a class="btn btn-danger" href="{{ route('instructor-solicitud-baja', ['id' => $datainstructor->id]) }}" >Solicitar Baja</a> --}}
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idbajains" id="idbajains">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Reactivación Instructor -->
    <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="reacinstructorModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Solicitud de Reactivación de Instructor</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card card-body" >
                    <form action="{{ route('instructor-solicitud-reactivacion') }}" method="post" id="registerperf_prof">
                        @csrf
                        <div class="alert alert-danger d-none d-print-none" id="reacinstructorwarning">
                            <span id="reacinstructorspan"></span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-1"></div>
                                <div class="form-group col-md-10" style="text-align:center;width:100%">
                                &nbsp;&nbsp;&nbsp;<label style="text-align:center; border:0px solid" class="form-control"><h5><b>¿Desea confirmar la solicitud de reactivación?</b></h5></label>
                                </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4"></div>
                            <div class="form-group col-md-4" style="text-align:center;width:100%">
                                <button type="submit" class="btn btn-danger mt-3 form-control" >Confirmar</button>
                                {{-- <a class="btn btn-danger" href="{{ route('instructor-solicitud-baja', ['id' => $datainstructor->id]) }}" >Solicitar Baja</a> --}}
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idreacins" id="idreacins">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END -->
    <!-- Modal Baja Especialidad -->
    <div class="modal fade bd-example-modal" tabindex="-1" role="dialog" id="bajaespeModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Solicitud de Baja de Especialidad</b></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card card-body" >
                    <form action="{{ route('instructor-solicitud-especialidad-baja') }}" method="post" id="registerperf_prof">
                        @csrf
                        <div class="alert alert-danger d-none d-print-none" id="bajaespewarning">
                            <span id="bajaespespan"></span>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-1"></div>
                                <div class="form-group col-md-10" style="text-align:center;width:100%">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style="text-align:center"><h5><b>¿Desea confirmar la solicitud de baja?</b></h5></label>
                                </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-1"></div>
                            <div class="form-group col-md-10" style="text-align:center;width:100%">
                                <textarea name="motivo_baja_especialidad" id="motivo_baja_especialidad" cols="30" rows="10"></textarea>
                                {{-- <a class="btn btn-danger" href="{{ route('instructor-solicitud-baja', ['id' => $datainstructor->id]) }}" >Solicitar Baja</a> --}}
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-11" style="text-align:center;width:100%">
                                <button type="submit" class="btn btn-danger mt-3" >Confirmar</button>
                                {{-- <a class="btn btn-danger" href="{{ route('instructor-solicitud-baja', ['id' => $datainstructor->id]) }}" >Solicitar Baja</a> --}}
                            </div>
                        </div>
                        <br>
                        <input type="hidden" name="idbajaespe" id="idbajaespe">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END -->
@stop
@section('script_content_js')
    <script src="{{ asset("js/validate/orlandoValidate.js") }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function local() {
            // var x = document.getElementById("municipio").value;
            // console.log(x);

            var valor = document.getElementById("municipio").value;
            var datos = {valor: valor};
            console.log('hola');

            var url ='/instructores/busqueda/nomesp';

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
            // console.log('hola');
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
            console.log('hola');

            var url ='/instructores/busqueda/nomesp';

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
            // console.log('hola');
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

        function saveperprof() {
            var datos = {
                            grado_prof: document.getElementById("grado_prof").value,
                            area_carrera: document.getElementById("area_carrera").value,
                            carrera: document.getElementById("carrera").value,
                            estatus: document.getElementById("estatus").value,
                            institucion_pais: document.getElementById("institucion_pais").value,
                            institucion_entidad: document.getElementById("institucion_entidad").value,
                            institucion_ciudad: document.getElementById("institucion_ciudad").value,
                            institucion_nombre: document.getElementById("institucion_nombre").value,
                            fecha_documento: document.getElementById("fecha_documento").value,
                            periodo: document.getElementById("periodo").value,
                            folio_documento: document.getElementById("folio_documento").value,
                            cursos_recibidos: document.getElementById("cursos_recibidos").value,
                            capacitador_icatech: document.getElementById("capacitador_icatech").value,
                            recibidos_icatech: document.getElementById("recibidos_icatech").value,
                            cursos_impartidos: document.getElementById("cursos_impartidos").value,
                            // exp_lab: document.getElementById("exp_lab").value,
                            // exp_doc: document.getElementById("exp_doc").value,
                            idInstructor: document.getElementById("idInstructor").value,
                            row: document.getElementById("tableperfiles").rows.length
                        };
            if(datos.grado_prof != '' && datos.area_carrera != '' && datos.carrera != '' && datos.estatus != 'SIN ESPECIFICAR' &&
             datos.institucion_pais != '' && datos.institucion_entidad != '' && datos.institucion_ciudad != '' &&
             datos.institucion_nombre != '' && datos.fecha_documento != '' && datos.periodo != '' && datos.folio_documento != '' &&
             datos.cursos_recibidos != 'SIN ESPECIFICAR' && datos.cursos_impartidos != 'SIN ESPECIFICAR' &&
             datos.capacitador_icatech != 'SIN ESPECIFICAR' && datos.recibidos_icatech != 'SIN ESPECIFICAR'
            //  &&  datos.exp_lab != '' && datos.exp_doc != ''
            )
            {
                var url = '/perfilinstructor/guardar';
                var request = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });
                request.done(( respuesta) =>
                {
                    $('#addperprofModal').modal('hide');

                    if(respuesta['exist'] == 'FALSO')
                    {
                        $('#warning1').prop("class", "d-none d-print-none")
                        var table = document.getElementById('tableperfiles')
                        var header = table.createTHead();
                        var row1 = header.insertRow(0);
                        var cell1 = row1.insertCell(0);
                        var cell2 = row1.insertCell(1);
                        var cell3 = row1.insertCell(2);
                        var cell4 = row1.insertCell(3);
                        var cell5 = row1.insertCell(4);
                        var cell6 = row1.insertCell(5);
                        cell1.innerHTML = 'Grado Profesional';
                        cell2.innerHTML = 'Area de la Carrera';
                        cell3.innerHTML = 'Nivel de Estudio';
                        cell4.innerHTML = 'Nombre de la Institución';
                        cell5.innerHTML = 'Status';
                        cell6.innerHTML = 'Accion';
                        var body = table.createTBody();
                        var row2 = body.insertRow(0);
                        var cell7 = row2.insertCell(0);
                        var cell8 = row2.insertCell(1);
                        var cell9 = row2.insertCell(2);
                        var cell10 = row2.insertCell(3);
                        var cell11 = row2.insertCell(4);
                        var cell12 = row2.insertCell(5);
                        cell7.innerHTML = datos.grado_prof;
                        cell8.innerHTML = datos.area_carrera;
                        cell9.innerHTML = datos.estatus;
                        cell10.innerHTML = datos.institucion_nombre;
                        cell11.innerHTML = respuesta['status'];
                        cell12.innerHTML = respuesta['button'] + ' ' + respuesta['button2'];
                        $('#buttonaddperfprof').prop("class", "btn mr-sm-4 mt-3");
                        $('#divperfil').prop("class", "");
                        $('#divnonperfil').prop("class", "d-none d-print-none");
                    }
                    else
                    {
                        var row = document.getElementById("tableperfiles").rows.length
                        var table = document.getElementById('tableperfiles')
                        var row = table.insertRow(row);
                        var cell1 = row.insertCell(0);
                        var cell2 = row.insertCell(1);
                        var cell3 = row.insertCell(2);
                        var cell4 = row.insertCell(3);
                        var cell5 = row.insertCell(4);
                        var cell6 = row.insertCell(5);
                        cell1.innerHTML = datos.grado_prof;
                        cell2.innerHTML = datos.area_carrera;
                        cell3.innerHTML = datos.estatus;
                        cell4.innerHTML = datos.institucion_nombre;
                        cell5.innerHTML = respuesta['status'];
                        cell6.innerHTML = respuesta['button'] + ' ' + respuesta['button2'];
                    }

                    document.getElementById("grado_prof").value = '';
                    document.getElementById("area_carrera").value = '';
                    document.getElementById("estatus").value = '';
                    document.getElementById("institucion_pais").value = '';
                    document.getElementById("institucion_entidad").value = '';
                    document.getElementById("institucion_ciudad").value = '';
                    document.getElementById("institucion_nombre").value = '';
                    document.getElementById("fecha_documento").value = '';
                    document.getElementById("folio_documento").value = '';
                    document.getElementById("cursos_recibidos").value = '';
                    document.getElementById("capacitador_icatech").value = '';
                    document.getElementById("recibidos_icatech").value = '';
                    document.getElementById("cursos_impartidos").value = '';
                    document.getElementById("exp_lab").value = '';
                    document.getElementById("exp_doc").value = '';
                    document.getElementById("idInstructor").value = '';
                    $('#addperprofwarning').prop("class", "d-none d-print-none")
                    const span = document.getElementById('newnrevisionspan');
                    $('#newnrevisionwarning').prop("class", "alert alert-success")
                    span.textContent = respuesta['nrevisiontext'];
                });

            }
            else
            {
                const span = document.getElementById('addperfprofspan');
                $('#addperprofwarning').prop("class", "alert alert-danger")
                span.textContent = 'Error: Uno o mas campos estan vacios';
                $('#addperprofModal').animate({scrollTop: 0},400);
                // console.log('nada');
            }
        }

        function savemodperprof() {
            var datos = {
                            grado_prof: document.getElementById("grado_prof2").value,
                            area_carrera: document.getElementById("area_carrera2").value,
                            carrera: document.getElementById("carrera2").value,
                            estatus: document.getElementById("estatus2").value,
                            institucion_pais: document.getElementById("institucion_pais2").value,
                            institucion_entidad: document.getElementById("institucion_entidad2").value,
                            institucion_ciudad: document.getElementById("institucion_ciudad2").value,
                            institucion_nombre: document.getElementById("institucion_nombre2").value,
                            fecha_documento: document.getElementById("fecha_documento2").value,
                            periodo: document.getElementById("periodo2").value,
                            folio_documento: document.getElementById("folio_documento2").value,
                            cursos_recibidos: document.getElementById("cursos_recibidos2").value,
                            capacitador_icatech: document.getElementById("capacitador_icatech2").value,
                            recibidos_icatech: document.getElementById("recibidos_icatech2").value,
                            cursos_impartidos: document.getElementById("cursos_impartidos2").value,
                            // exp_lab: document.getElementById("exp_lab2").value,
                            // exp_doc: document.getElementById("exp_doc2").value,
                            idInstructor: document.getElementById("idInstructor2").value,
                            idperfprof: document.getElementById("idperfprof2").value,
                            pos: document.getElementById("row").value
                        };
            if(datos.grado_prof != '' && datos.area_carrera != '' && datos.carrera != '' && datos.estatus != 'SIN ESPECIFICAR' &&
             datos.institucion_pais2 != '' && datos.institucion_entidad != '' && datos.institucion_ciudad != '' &&
             datos.institucion_nombre != '' && datos.fecha_documento != '' && datos.periodo != ''&& datos.folio_documento != '' &&
             datos.capacitador_icatech != 'SIN ESPECIFICAR' && datos.recibidos_icatech != 'SIN ESPECIFICAR' &&
             datos.exp_lab != '' && datos.exp_doc != ''
            )
            {
                var url = '/instructor/mod/perfilinstructor/guardar';
                var request2 = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });

                request2.done(( respuesta) =>
                {
                    position = document.getElementById("row").value;
                    // console.log(position);
                    $('#modperprofModal').modal('hide');
                        var table = document.getElementById('tableperfiles')
                        var row = table.rows[position];
                        var cell1 = row.cells[0];
                        var cell2 = row.cells[1];
                        var cell3 = row.cells[2];
                        var cell4 = row.cells[3];
                        var cell5 = row.cells[4];
                        var cell6 = row.cells[5];
                        cell1.innerHTML = datos.grado_prof;
                        cell2.innerHTML = datos.area_carrera;
                        cell3.innerHTML = datos.estatus;
                        cell4.innerHTML = datos.institucion_nombre;
                        cell5.innerHTML = respuesta['status'];
                        cell6.innerHTML = respuesta['button'] + ' ' + respuesta['button2'] + ' ' + respuesta['button3'];

                    $('#modperprofwarning').prop("class", "d-none d-print-none")
                });
            }
            else
            {
                const span = document.getElementById('modperfprofspan');
                $('#modperprofwarning').prop("class", "alert alert-danger")
                span.textContent = 'Error: Uno o mas campos estan vacios';
                $('#modperprofModal').animate({scrollTop: 0},400);
                // console.log('nada');
            }
        }

        function delperprof() {
            var datos = {
                            id: document.getElementById("iddelperfprof").value
                        };

            var url = '/instructor/mod/perfilinstructor/eliminar';
            var request2 = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request2.done(( respuesta) =>
            {
                if(respuesta['error'] != 'error')
                {
                    position = document.getElementById("row").value;
                    $('#delperprofModal').modal('hide');
                    $('#delperprofwarning').prop("class", "d-none d-print-none")
                        var table = document.getElementById('tableperfiles')
                        var locdel = document.getElementById('locdel').value
                        table.deleteRow(locdel);
                }
                else
                {
                    const span = document.getElementById('delperfprofspan');
                    $('#delperprofwarning').prop("class", "alert alert-danger")
                    span.textContent = 'Error: Una o mas especialidades dependen de este perfil profesional';
                    $('#delperprofModal').animate({scrollTop: 0},400);
                }
            });
        }

        function delespecvalid() {
            var datos = {
                            id: document.getElementById("idespecvalid").value,
                            idins: document.getElementById("idinsespecvalid").value
                        };

            var url = '/instructor/mod/especialidadimpartir/eliminar';
            var request2 = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request2.done(( respuesta) =>
            {
                position = document.getElementById("row").value;
                $('#delespecvalidModal').modal('hide');
                $('#delespecvalidwarning').prop("class", "d-none d-print-none")
                var table = document.getElementById('espec-table')
                var loc2del = document.getElementById('loc2del').value
                table.deleteRow(loc2del);
            });
        }

        function saveexpdoc() {
            var datos = {
                            asignatura: document.getElementById("asignatura").value,
                            institucion: document.getElementById("institucion").value,
                            funcion: document.getElementById("funcion").value,
                            periodo: document.getElementById("periodoadddoc").value,
                            idins: document.getElementById("idInstructorexpdoc").value
                        };
            if(datos.asignatura != '' && datos.institucion != '' && datos.funcion != '' &&
             datos.periodo != '' && datos.idins != '')
            {
                var url = '/expdoc/guardar';
                var request = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });
                request.done(( respuesta) =>
                {
                    console.log(respuesta);
                    $('#addexpdocModal').modal('hide');
                    var row = document.getElementById("tableexpdoc").rows.length
                    var table = document.getElementById('tableexpdoc')
                    var row = table.insertRow(respuesta['pos']);
                    var cell1 = row.insertCell(0);
                    var cell2 = row.insertCell(1);
                    var cell3 = row.insertCell(2);
                    var cell4 = row.insertCell(3);
                    var cell5 = row.insertCell(4);
                    cell1.innerHTML = datos.asignatura;
                    cell2.innerHTML = datos.institucion;
                    cell3.innerHTML = datos.funcion;
                    cell4.innerHTML = datos.periodo;
                    cell5.innerHTML = respuesta['button'];

                    document.getElementById("asignatura").value = '';
                    document.getElementById("institucion").value = '';
                    document.getElementById("funcion").value = '';
                    document.getElementById("periodo").value = '';
                    document.getElementById("idInstructorexpdoc").value = '';
                    $('#addexpdocwarning').prop("class", "d-none d-print-none")
                    const span = document.getElementById('newnrevisionspan');
                });

            }
            else
            {
                const span = document.getElementById('addexpdocspan');
                $('#addexpdocwarning').prop("class", "alert alert-danger")
                span.textContent = 'Error: Uno o mas campos estan vacios';
                $('#addexpdocModal').animate({scrollTop: 0},400);
                console.log('nada');
            }
        }

        function delexpdoc() {
            var datos = {
                            idins: document.getElementById("idinsdelexpdoc").value,
                            asignatura: document.getElementById("asdel").value,
                            institucion: document.getElementById("indel").value,
                            funcion: document.getElementById("fudel").value,
                            periodo: document.getElementById("pedel").value
                        };

            var url = '/instructor/expdoc/eliminar';
            var request2 = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request2.done(( respuesta) =>
            {
                var table = document.getElementById('tableexpdoc')
                countdel = table.rows.length

                for(i = 1; i < countdel; i++)
                {
                    table.deleteRow(1);
                }
                i = 1;
                respuesta.forEach( function(valor, indice, array){
                    row = table.insertRow(i)
                    cell1 = row.insertCell(0)
                    cell2 = row.insertCell(1)
                    cell3 = row.insertCell(2)
                    cell4 = row.insertCell(3)
                    cell5 = row.insertCell(4)
                    cell1.innerHTML = valor.asignatura
                    cell2.innerHTML = valor.institucion
                    cell3.innerHTML = valor.funcion
                    cell4.innerHTML = valor.periodo
                    cell5.innerHTML = valor.button});
                $('#delexpdocModal').modal('hide');
                console.log(respuesta)
            });
        }

        function saveexplab() {
            var datos = {
                            puesto: document.getElementById("puestolab").value,
                            periodo: document.getElementById("periodolab").value,
                            institucion: document.getElementById("institucionlab").value,
                            idins: document.getElementById("idInstructorexplab").value
                        };
            if(datos.puesto != '' && datos.periodo != '' &&  datos.institucion != '' && datos.idins != '')
            {
                var url = '/explab/guardar';
                var request = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });
                request.done(( respuesta) =>
                {
                    $('#addexplabModal').modal('hide');
                    var row = document.getElementById("tableexplab").rows.length
                    var table = document.getElementById('tableexplab')
                    var row = table.insertRow(respuesta['pos']);
                    var cell1 = row.insertCell(0);
                    var cell2 = row.insertCell(1);
                    var cell3 = row.insertCell(2);
                    var cell4 = row.insertCell(3);
                    cell1.innerHTML = datos.puesto;
                    cell2.innerHTML = datos.periodo;
                    cell3.innerHTML = datos.institucion;
                    cell4.innerHTML = respuesta['button'];

                    document.getElementById("puestolab").value = '';
                    document.getElementById("periodolab").value = '';
                    document.getElementById("institucionlab").value = '';
                    document.getElementById("idInstructorexplab").value = '';
                    $('#addexplabwarning').prop("class", "d-none d-print-none")
                    const span = document.getElementById('newnrevisionspan');
                });

            }
            else
            {
                const span = document.getElementById('addexplabspan');
                $('#addexplabwarning').prop("class", "alert alert-danger")
                span.textContent = 'Error: Uno o mas campos estan vacios';
                $('#addexplabModal').animate({scrollTop: 0},400);
            }
        }

        function delexplab() {
            var datos = {
                            idins: document.getElementById("idinsdelexplab").value,
                            puesto: document.getElementById("pulabdel").value,
                            periodo: document.getElementById("pelabdel").value,
                            institucion: document.getElementById("inlabdel").value
                        };

            var url = '/instructor/explab/eliminar';
            var request2 = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request2.done(( respuesta) =>
            {
                var table = document.getElementById('tableexplab')
                countdel = table.rows.length

                for(i = 1; i < countdel; i++)
                {
                    table.deleteRow(1);
                }
                i = 1;
                respuesta.forEach( function(valor, indice, array){
                    row = table.insertRow(i)
                    cell1 = row.insertCell(0)
                    cell2 = row.insertCell(1)
                    cell3 = row.insertCell(2)
                    cell4 = row.insertCell(3)
                    cell1.innerHTML = valor.puesto
                    cell2.innerHTML = valor.periodo
                    cell3.innerHTML = valor.institucion
                    cell4.innerHTML = valor.button});
                $('#delexplabModal').modal('hide');
                console.log(respuesta)
            });
        }

        function validacionpdfview()
        {
            window.open(document.getElementById('validacionpdf').value, "_blank");
            // console.log(document.getElementById('validacionpdf').value)
        }

        $('#addperprofModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            document.getElementById('idInstructor').value = id;
        });

        $('#modperprofModal').on('show.bs.modal', function(event){
            var button = $(event.relatedTarget);
            var id = button.data('id');
            if (!Array.isArray(id)) {
                if (!Array.isArray(id)) {
                    try {
                        id = JSON.parse(id);
                    } catch (e) {
                        id = id.split(",").map(item => item.trim().replace(/^"|"$/g, ''));
                    }
                }
            }
            $('#modperprofwarning').prop("class", "d-none d-print-none")
            document.getElementById('grado_prof2').value = id['0'];
            document.getElementById('area_carrera2').value = id['1'];
            document.getElementById('carrera2').value = id['2'];
            document.getElementById('estatus2').value = id['3'];
            document.getElementById('institucion_pais2').value = id['4'];
            document.getElementById('institucion_entidad2').value = id['5'];
            document.getElementById('institucion_ciudad2').value = id['6'];
            document.getElementById('institucion_nombre2').value = id['7'];
            document.getElementById('fecha_documento2').value = id['8'];
            document.getElementById('periodo2').value = id['9'];
            document.getElementById('folio_documento2').value = id['10'];
            document.getElementById('cursos_recibidos2').value = id['11'];
            document.getElementById('capacitador_icatech2').value = id['12'];
            document.getElementById('recibidos_icatech2').value = id['13'];
            document.getElementById('cursos_impartidos2').value = id['14'];
            // document.getElementById('exp_lab2').value = id['13'];
            // document.getElementById('exp_doc2').value = id['14'];
            document.getElementById('idperfprof2').value = id['15'];
            document.getElementById('idInstructor2').value = id['16'];
            document.getElementById('row').value = id['17'];
        });

        $('#delperprofModal').on('show.bs.modal', function(event){
            $('#delperprofwarning').prop("class", "d-none d-print-none")
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            document.getElementById('iddelperfprof').value = id['0'];
            document.getElementById('locdel').value = id['1'];
        });

        $('#delespecvalidModal').on('show.bs.modal', function(event){
            $('#delespecvalidwarning').prop("class", "d-none d-print-none")
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            document.getElementById('idespecvalid').value = id['0'];
            document.getElementById('loc2del').value = id['1'];
            document.getElementById('idinsespecvalid').value = id['2'];
        });

        $('#sendtodtaModal').on('show.bs.modal', function(event){
            $('#sentodtawarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id)
            document.getElementById('idtodta').value = id;
        });

        $('#verperfprofModal').on('show.bs.modal', function(event){
            var button = $(event.relatedTarget);
            var idb = button.data('id');
            if (idb['16'] != 'VALIDADO')
            {
                // console.log(idb)
                var div = document.getElementById('listaperfprof')
                div.innerHTML = '<li>Grado Profesional: <b>' + idb['1'] + '</b></li><br>' +
                    '<li>Estatus: <b>' + idb['16'] + '</b></li><br>' +
                    '<li>Pais: <b>' + idb['4'] + '</b></li><br>' +
                    '<li>Entidad: <b>' + idb['5'] + '</b></li><br>' +
                    '<li>Ciudad: <b>' + idb['6'] + '</b></li><br>' +
                    '<li>Institución: <b>' + idb['7'] + '</b></li><br>' +
                    '<li>Fecha de Expedicion: <b>' + idb['8'] + '</b></li><br>' +
                    '<li>Folio de Documento: <b>' + idb['10'] + '</b></li><br>' +
                    '<li>Cursos Recibidos: <b>' + idb['11'] + '</b></li><br>' +
                    '<li>Capacitador ICATECH: <b>' + idb['12'] + '</b></li><br>' +
                    '<li>Cursos Recibidos ICATECH: <b>' + idb['13'] + '</b></li><br>' +
                    '<li>Cursos Impartidos: <b>' + idb['14'] + '</b></li><br>';
            }
            else
            {
                var datos = {
                                id: idb
                            };

                var url = '/instructor/detalles/perfilinstructor';
                var request2 = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });

                request2.done(( respuesta) =>
                {
                    // console.log(respuesta);
                    var div = document.getElementById('listaperfprof')
                    div.innerHTML = '<li>Grado Profesional: <b>' + respuesta['grado_profesional'] + '</b></li><br>' +
                        '<li>Estatus: <b>' + respuesta['estatus'] + '</b></li><br>' +
                        '<li>Pais: <b>' + respuesta['pais_institucion'] + '</b></li><br>' +
                        '<li>Entidad: <b>' + respuesta['entidad_institucion'] + '</b></li><br>' +
                        '<li>Ciudad: <b>' + respuesta['ciudad_institucion'] + '</b></li><br>' +
                        '<li>Institución: <b>' + respuesta['nombre_institucion'] + '</b></li><br>' +
                        '<li>Fecha de Expedicion: <b>' + respuesta['fecha_expedicion_documento'] + '</b></li><br>' +
                        '<li>Folio de Documento: <b>' + respuesta['folio_documento'] + '</b></li><br>' +
                        '<li>Cursos Recibidos: <b>' + respuesta['cursos_recibidos'] + '</b></li><br>' +
                        '<li>Capacitador ICATECH: <b>' + respuesta['capacitador_icatech'] + '</b></li><br>' +
                        '<li>Cursos Recibidos ICATECH: <b>' + respuesta['recibidos_icatech'] + '</b></li><br>' +
                        '<li>Cursos Impartidos: <b>' + respuesta['cursos_impartidos'] + '</b></li><br>' +
                        '<li>Experiencia Laboral: <b>' + respuesta['experiencia_laboral'] + '</b></li><br>' +
                        '<li>Experiencia Docente: <b>' + respuesta['experiencia_docente'] + '</b></li><br>';
                });
            }
        });

        $('#verespevaliModal').on('show.bs.modal', function(event){
            var button = $(event.relatedTarget);
            var idb = button.data('id');
            console.log(idb)
            if(idb['7'] != 'VALIDADO')
            {
                console.log('a')

                var div = document.getElementById('listaespevali')
                div.innerHTML = '<li>Especialidad: <b>' + idb['0'] + '</b></li><br>' +
                    '<li>Peril Profesional: <b>' + idb['1'] + '</b></li><br>' +
                    '<li>Unidad Solicita: <b>' + idb['2'] + '</b></li><br>' +
                    '<li>Criterio Pago: <b>' + idb['3'] + '</b></li><br>' +
                    '<li>Memorandum de Solicitud: <b>' + idb['4'] + '</b></li><br>' +
                    '<li>Fecha de Solicitud: <b>' + idb['5'] + '</b></li><br>' +
                    '<li>Observaciones: <b>' + idb['6'] + '</b></li><br>' +
                    '<li>Cursos:</li><ul>' + idb['9'] + '</ul>';
            }
            else
            {
                var datos = {
                                id: idb
                            };

                var url = '/instructor/detalles/especialidadvalidada';
                var request2 = $.ajax
                ({
                    url: url,
                    method: 'POST',
                    data: datos,
                    dataType: 'json'
                });

                request2.done(( respuesta) =>
                {
                    console.log(respuesta);
                    var div = document.getElementById('listaespevali')
                    div.innerHTML = '<li>Especialidad: <b>' + respuesta['especialidad'] + '</b></li><br>' +
                        '<li>Peril Profesional: <b>' + respuesta['perfilprof'] + '</b></li><br>' +
                        '<li>Unidad Solicita: <b>' + respuesta['unidad_solicita'] + '</b></li><br>' +
                        '<li>Criterio Pago: <b>' + respuesta['cp'] + '</b></li><br>' +
                        '<li>Memorandum de Solicitud: <b>' + respuesta['memorandum_solicitud'] + '</b></li><br>' +
                        '<li>Fecha de Solicitud: <b>' + respuesta['fecha_solicitud'] + '</b></li><br>' +
                        '<li>Observaciones: <b>' + respuesta['observacion'] + '</b></li><br>' +
                        '<li>Cursos:</li><ul>' + respuesta['cursos'] + '</ul>';
                });
            }
        });

        $('#prevalidarModal').on('show.bs.modal', function(event){
            $('#prevalidarwarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id)
            document.getElementById('idinspreval').value = id;
        });

        $('#returntounitModal').on('show.bs.modal', function(event){
            $('#returntounitwarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id)
            document.getElementById('idinsreturn').value = id;
        });

        $('#validacionesModal').on('show.bs.modal', function(event){
            $('#validacionwarning').prop("class", "d-none d-print-none")
            var button = $(event.relatedTarget);
            var id = button.data('id');
            console.log(id)
            id = id.reverse();

            var selectL = document.getElementById('validacionpdf'),
            option,
            i = 0,
            il = id.length;

            // Convertir selectL en un objeto jQuery
            selectLJQ = $(selectL);
            // Limpiar las opciones del select
            selectLJQ.empty();
            // console.log(il);
            // console.log( id[0])
            for (; i < il; i += 1)
            {
                newOption = document.createElement('option');
                if(id[i].arch_val != null)
                {
                    newOption.value = id[i].arch_val;
                    newOption.text=id[i].memo_val;
                }
                else
                {
                    newOption.value = id[i].arch_baja;
                    newOption.text=id[i].memo_baja;
                }
                // selectL.appendChild(option);
                selectL.add(newOption);
            }
        });

        $('#entrevistaModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idInstructorentrevista').value = id;
        });

        $('#modentrevistaModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);

            var datos = {
                            id: id
                        };

            var url = '/instructores/detalles/getentrevista';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });
            request.done(( respuesta) =>
            {
                document.getElementById('MQ1').value = respuesta['1'];
                document.getElementById('MQ2').value = respuesta['2'];
                document.getElementById('MQ3').value = respuesta['3'];
                document.getElementById('MQ4').value = respuesta['4'];
                document.getElementById('MQ5').value = respuesta['5'];
                document.getElementById('MQ6').value = respuesta['6'];
                document.getElementById('MQ7').value = respuesta['7'];
                document.getElementById('MQ8').value = respuesta['8'];
                document.getElementById('MQ9').value = respuesta['9'];
                document.getElementById('MQ10').value = respuesta['10'];
                document.getElementById('MQ11').value = respuesta['11'];
                document.getElementById('MQ12').value = respuesta['12'];
                document.getElementById('MQ13').value = respuesta['13'];
                document.getElementById('MQ14').value = respuesta['14'];
                // var div = document.getElementById('listaperfprof')

            });
            document.getElementById('idInstructorentrevistamod').value = id;
        });

        $('#updentrevistaModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idInstructorentrevistaupd').value = id;
        });

        $('#updcurriculumModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idInstructorcurriculumupd').value = id;
        });

        $('#addexpdocModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            console.log(id);
            document.getElementById('idInstructorexpdoc').value = id;
        });

        $('#delexpdocModal').on('show.bs.modal', function(event){
            $('#delexpdocwarning').prop("class", "d-none d-print-none")
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var table = document.getElementById('tableexpdoc')
            var row = table.rows[id['0']];
            var asignatura = row.cells['0'].innerHTML
            var institucion = row.cells['1'].innerHTML
            var funcion = row.cells['2'].innerHTML
            var periodo = row.cells['3'].innerHTML

            document.getElementById('idinsdelexpdoc').value = id['1'];
            document.getElementById('locdelexpdoc').value = id['0'];
            document.getElementById('asdel').value = asignatura;
            document.getElementById('indel').value = institucion;
            document.getElementById('fudel').value = funcion;
            document.getElementById('pedel').value = periodo;
        });

        $('#addexplabModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idInstructorexplab').value = id;
        });

        $('#delexplabModal').on('show.bs.modal', function(event){
            $('#delexplabwarning').prop("class", "d-none d-print-none")
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var table = document.getElementById('tableexplab')
            var row = table.rows[id['0']];
            var puesto = row.cells['0'].innerHTML
            var periodo = row.cells['1'].innerHTML
            var institucion = row.cells['2'].innerHTML

            document.getElementById('idinsdelexplab').value = id['1'];
            document.getElementById('locdelexplab').value = id['0'];
            document.getElementById('pulabdel').value = puesto;
            document.getElementById('pelabdel').value = periodo;
            document.getElementById('inlabdel').value = institucion;
        });

        $('#bajainstructorModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idbajains').value = id;
        });

        $('#reacinstructorModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idreacins').value = id;
        });

        $('#bajaespeModal').on('show.bs.modal', function(event){
            // console.log(document.getElementById("tableperfiles").rows.length);
            var button = $(event.relatedTarget);
            var id = button.data('id');
            // console.log(id);
            document.getElementById('idbajaespe').value = id;
        });

        $('#enviarMovimiento').on('click', function() {
            // Get the selected value from the dropdown
            var movimiento = $('#movimiento').val();
            $.ajax({
                url: '/retorno/movimiento/instructor', // Replace with the actual endpoint
                type: 'POST',
                data: {
                    movimiento: movimiento,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Handle the response from the controller if needed
                    console.log(response);
                },
                error: function(error) {
                    // Handle the error if the request fails
                    console.error('AJAX Error:', error.responseText);
                }
            });
        });

        // document.getElementById('reginstructor').addEventListener('submit', function (e) {
        //     var fileInputs = [
        //         'arch_domicilio',
        //         'arch_curp',
        //         'arch_banco',
        //         'arch_foto',
        //         'arch_id',
        //         'arch_rfc',
        //         'arch_estudio',
        //         'arch_curriculum_personal'
        //     ];

        //     for (var i = 0; i < fileInputs.length; i++) {
        //         var inputId = fileInputs[i];
        //         var fileInput = document.getElementById(inputId);
        //         if (fileInput.files.length === 0) {
        //             switch(inputId) {
        //                 case 'arch_domicilio':
        //                     href = document.getElementById('arch_domicilio_pdf').getAttribute('href');
        //                     inputIdName = 'Comprobande de Domicilio';
        //                 break;
        //                 case 'arch_curp':
        //                     href = document.getElementById('arch_curp_pdf').getAttribute('href');
        //                     inputIdName = 'CURP';
        //                 break;
        //                 case 'arch_banco':
        //                     href = document.getElementById('arch_banco_pdf').getAttribute('href');
        //                     inputIdName = 'Comprobante Bancario';
        //                 break;
        //                 case 'arch_foto':
        //                     href = document.getElementById('arch_foto_jpg').getAttribute('href');
        //                     inputIdName = 'Fotografía';
        //                 break;
        //                 case 'arch_id':
        //                     href = document.getElementById('arch_id_pdf').getAttribute('href');
        //                     inputIdName = 'Acta de Nacimiento';
        //                 break;
        //                 case 'arch_rfc':
        //                     href = document.getElementById('arch_rfc_pdf').getAttribute('href');
        //                     inputIdName = 'RFC';
        //                 break;
        //                 case 'arch_estudio':
        //                     href = document.getElementById('arch_estudio_pdf').getAttribute('href');
        //                     inputIdName = 'Comprobante de Estudios';
        //                 break;
        //                 case 'arch_curriculum_personal':
        //                     href = document.getElementById('arch_curriculum_personal_pdf').getAttribute('href');
        //                     inputIdName = 'Curriculum';
        //                 break;
        //             }
        //             if(href == null) {
        //                 e.preventDefault(); // Prevent form submission
        //                 alert('El campo de ' + inputIdName + ' esta vacio. Favor de subir el documento.');
        //                 return;
        //             }
        //         }
        //     }
        // });
    </script>
    <script>
        document.getElementById('toggleAcordeon').addEventListener('change', function() {
          var acordeon = document.getElementById('acordeonInstructor');
          if (this.checked) {
            acordeon.style.display = 'block';  // Muestra el acordeón
            $('#collapse1').collapse('show'); // Expande el acordeón automáticamente
            document.getElementById('form-domicilio').setAttribute('hidden', true);
          } else {
            acordeon.style.display = 'none';  // Oculta el acordeón
            $('#collapse1').collapse('hide'); // Colapsa el acordeón
            document.getElementById('form-domicilio').removeAttribute('hidden');
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
            const sinOcupa = document.getElementById('sin_ocupacion');
            const conOcupa = document.getElementById('con_ocupacion');
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

