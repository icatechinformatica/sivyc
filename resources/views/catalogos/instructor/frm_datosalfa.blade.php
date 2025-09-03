{{-- Formulario alfa --}}
<div>
    <div class="switch-container">
        <label class="switch">
            <input type="checkbox" id="toggleAcordeon">
            <span class="slider round"></span>
        </label>
        <span class="switch-text">Instructor Alfa</span>
    </div>

    <!-- Acordeón de datos Alfa -->
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
</div>
