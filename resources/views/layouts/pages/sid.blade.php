@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Solicitud de Inscripción | Sivyc Icatech')
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
            </div> <br>
        @endif
        <div style="text-align: center;">
            <h3><b>Solicitud de Inscripción (SID)</b></h3>
        </div>
        <hr style="border-color:dimgray">
        <div style="text-align: center;">
            <h4><b>DATOS PERSONALES</b></h4>
        </div>
        <form method="POST" id="form_sid" action="{{ route('alumnos.save') }}">
            @csrf
            <div class="form-row">
                <!--nombre aspirante-->
                <div class="form-group col-md-4">
                    <label for="nombre " class="control-label">Nombre del Aspirante</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" autocomplete="off">
                </div>
                <!--nombre aspirante END-->
                <!-- apellido paterno -->
                <div class="form-group col-md-4">
                    <label for="apellidoPaterno" class="control-label">Apellido Paterno</label>
                    <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" autocomplete="off">
                </div>
                <!-- apellido paterno END -->
                <!-- apellido materno-->
                <div class="form-group col-md-4">
                    <label for="apellidoMaterno" class="control-label">Apellido Materno</label>
                    <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno" autocomplete="off">
                </div>
                <!-- apellido materno END-->
            </div>
            <div class="form-row">
                <b><label for="fechanacimiento" class="control-label">FECHA DE NACIMIENTO</label></b>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="dia" class="control-label">DÍA</label>
                    <select class="form-control" id="dia" name="dia">
                        <option value="">--SELECCIONAR--</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="19">19</option>
                        <option value="20">20</option>
                        <option value="21">21</option>
                        <option value="22">22</option>
                        <option value="23">23</option>
                        <option value="24">24</option>
                        <option value="25">25</option>
                        <option value="26">26</option>
                        <option value="27">27</option>
                        <option value="28">28</option>
                        <option value="29">29</option>
                        <option value="30">30</option>
                        <option value="31">31</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="mes" class="control-label">MES</label>
                    <select class="form-control" id="mes" name="mes">
                        <option value="">--SELECCIONAR--</option>
                        <option value="01">ENERO</option>
                        <option value="02">FEBRERO</option>
                        <option value="03">MARZO</option>
                        <option value="04">ABRIL</option>
                        <option value="05">MAYO</option>
                        <option value="06">JUNIO</option>
                        <option value="07">JULIO</option>
                        <option value="08">AGOSTO</option>
                        <option value="09">SEPTIEMBRE</option>
                        <option value="10">OCTUBRE</option>
                        <option value="11">NOVIEMBRE</option>
                        <option value="12">DICIEMBRE</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="anio" class="control-label">AÑO</label>
                    <input type="text" class="form-control" id="anio" name="anio" placeholder="INGRESA EL AÑO EJ. 1943" autocomplete="off">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="sexo" class="control-label">Genero</label>
                    <select class="form-control" id="sexo" name="sexo">
                        <option value="">--SELECCIONAR--</option>
                        <option value="FEMENINO">MUJER</option>
                        <option value="MASCULINO">HOMBRE</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="curp" class="control-label">Curp Aspirante</label>
                    <input type="text" class="form-control" id="curp" name="curp" placeholder="Curp" autocomplete="off">
                </div>
                <div class="form-group col-md-4">
                    <label for="telefonosid" class="control-label">Teléfono</label>
                    <input type="text" class="form-control" id="telefonosid" name="telefonosid" autocomplete="off">
                </div>
            </div>
            <div class="form-row">
                <!-- domicilio -->
                <div class="form-group col-md-6">
                    <label for="domicilio" class="control-label">Domicilio</label>
                    <input type="text" class="form-control" id="domicilio" name="domicilio" autocomplete="off">
                </div>
                <!-- domicilio END -->
                <div class="form-group col-md-6">
                    <label for="colonia" class="control-label">Colonia o Localidad</label>
                    <input type="text" class="form-control" id="colonia" name="colonia" autocomplete="off">
                </div>
                <!--COLONIA END-->
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="cp" class="control-label">C.P.</label>
                    <input type="text" class="form-control" id="cp" name="cp" autocomplete="off">
                </div>
                <div class="form-group col-md-4">
                    <label for="estado" class="control-label">Estado</label>
                    <select class="form-control" id="estado" name="estado">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($estados as $itemEstado)
                            <option value="{{$itemEstado->nombre}}">{{ $itemEstado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="municipio" class="control-label">Municipio</label>
                    <select class="form-control" id="municipio" name="municipio">
                        <option value="">--SELECCIONAR--</option>
                        @foreach ($municipios as $itemMunicipio)
                            <option value="{{$itemMunicipio->muni}}">{{ $itemMunicipio->muni }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!--formulario-->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="estado_civil" class="control-label">Estado Civil</label>
                    <select class="form-control" id="estado_civil" name="estado_civil">
                        <option value="">--SELECCIONAR--</option>
                        <option value="SOLTERO (A)">SOLTERO (A)</option>
                        <option value="CASADO (A)">CASADO (A)</option>
                        <option value="UNIÓN LIBRE">UNIÓN LIBRE</option>
                        <option value="DIVORCIADO (A)">DIVORCIADO (A)</option>
                        <option value="VIUDO (A)">VIUDO (A)</option>
                        <option value="NO ESPECIFICA">NO ESPECIFICA</option>
                    </select>
                </div>
                <!---->
                <div class="form-group col-md-6">
                    <label for="discapacidad" class="control-label">Discapacidad que presenta</label>
                    <select class="form-control" id="discapacidad" name="discapacidad">
                        <option value="">--SELECCIONAR--</option>
                        <option value="VISUAL">VISUAL</option>
                        <option value="AUDITIVA">AUDITIVA</option>
                        <option value="DE COMUNICACIÓN">DE COMUNICACIÓN</option>
                        <option value="MOTRIZ">MOTRIZ</option>
                        <option value="INTELECTUAL">INTELECTUAL</option>
                        <option value="NINGUNA">NINGUNA</option>
                    </select>
                </div>
            </div>
            <hr style="border-color:dimgray">
            <div style="text-align: center;">
                <h4><b>DATOS GENERALES DE CAPACITACIÓN</b></h4>
            </div>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="ultimo_grado_estudios" class="control-label">ÚLTIMO GRADO DE ESTUDIOS:</label>
                    <input type="text" name="ultimo_grado_estudios" id="ultimo_grado_estudios" class="form-control" autocomplete="off">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="medio_entero" class="control-label">MEDIO POR EL QUE SE ENTERÓ DEL SISTEMA</label>
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
                    <input type="text" name="empresa" id="empresa" class="form-control" autocomplete="off">
                </div>
                <div class="form-group col-md-6">
                    <label for="puesto_empresa" class="control-label">PUESTO:</label>
                    <input type="text" name="puesto_empresa" id="puesto_empresa" class="form-control" autocomplete="off">
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
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
