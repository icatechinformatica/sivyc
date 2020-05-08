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
                    <label for="nombre " class="control-label">Nombre: {{$Alumno->nombre}}</label>
                </div>
                <!--nombre aspirante END-->
                <!-- apellido paterno -->
                <div class="form-group col-md-3">
                    <label for="apellidoPaterno" class="control-label">Apellido Paterno: {{$Alumno->apellidoPaterno}}</label>
                </div>
                <!-- apellido paterno END -->
                <!-- apellido materno-->
                <div class="form-group col-md-3">
                    <label for="apellidoMaterno" class="control-label">Apellido Materno: {{$Alumno->apellidoMaterno}}</label>
                </div>
                <!-- apellido materno END-->
                <div class="form-group col-md-3">
                    <label for="sexo" class="control-label">Genero: {{$Alumno->sexo}}</label>
                </div>
            </div>
            <div class="form-row">

                <div class="form-group col-md-3">
                    <label for="curp" class="control-label">CURP: {{$Alumno->curp}}</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="fecha_nacimiento" class="control-label">Fecha de Nacimiento: {{$Alumno->fecha_nacimiento}}</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="telefono" class="control-label">Teléfono: {{$Alumno->telefono}}</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="cp" class="control-label">C.P. {{$Alumno->cp}}</label>
                </div>
            </div>
            <div class="form-row">

                <div class="form-group col-md-3">
                    <label for="estado" class="control-label">Estado: {{$Alumno->estado}}</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="municipio" class="control-label">Municipio: {{$Alumno->municipio}}</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="estado_civil" class="control-label">Estado Civil: {{$Alumno->estado_civil}}</label>
                </div>
                <!---->
                <div class="form-group col-md-3">
                    <label for="discapacidad" class="control-label">Discapacidad que presenta: {{$Alumno->discapacidad}}</label>
                </div>
            </div>
            <div class="form-row">
                <!-- domicilio -->
                <div class="form-group col-md-6">
                    <label for="domicilio" class="control-label">Domicilio: {{$Alumno->domicilio}}</label>
                </div>
                <!-- domicilio END -->
                <div class="form-group col-md-6">
                    <label for="colonia" class="control-label">Colonia: {{$Alumno->colonia}}</label>
                </div>
            </div>
            <!---->
            <hr style="border-color:dimgray">
            <div style="text-align: center;">
                <h4><b>DATOS GENERALES</b></h4>
            </div>
            <form method="POST" id="form_sid" action="{{ route('alumnos.update-sid') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="especialidad" class="control-label">ESPECIALIDAD A LA QUE DESEA INSCRIBIRSE:</label>
                        <select class="form-control" id="especialidad_sid" name="especialidad" required>
                            <option value="">--SELECCIONAR--</option>
                            @foreach ($especialidades as $itemEspecialidad)
                                <option value="{{$itemEspecialidad->id}}">{{ $itemEspecialidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="cursos" class="control-label">CURSO:</label>
                        <select class="form-control" id="cursos_sid" name="cursos_sid" required>
                            <option value="">--SELECCIONAR--</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="horario" class="control-label">HORARIO:</label>
                        <input type="text" name="horario" id="horario" class="form-control" autocomplete="off">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="grupo" class="control-label">GRUPO:</label>
                        <input type="text" name="grupo" id="grupo" class="form-control" autocomplete="off">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="grado_estudios" class="control-label">ÚLTIMO GRADO DE ESTUDIOS:</label>
                        <input type="text" name="grado_estudios" id="grado_estudios" class="form-control" autocomplete="off">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <h5><b>DOCUMENTACIÓN ENTREGADA:</b></h5>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="acta_nacimiento" name="acta_nacimiento">
                            <label class="custom-file-label" for="customFile">ACTA DE NACIMIENTO</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="copia_curp" name="copia_curp">
                            <label class="custom-file-label" for="copia_curp">COPIA DE LA CURP</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="comprobante_domicilio" name="comprobante_domicilio">
                            <label class="custom-file-label" for="comprobante_domicilio">COMPROBANTE DE DOMICILIO</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="fotografias" name="fotografias">
                            <label class="custom-file-label" for="fotografias">FOTOGRAFÍAS</label>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="ine" name="ine">
                            <label class="custom-file-label" for="ine">COPIA DE LA CREDENCIAL DE ELECTOR</label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="licencia_manejo" name="licencia_manejo">
                            <label class="custom-file-label" for="licencia_manejo">(PASAPORTE, LICENCIA DE MANEJO)</label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="grado_estudios" name="grado_estudios">
                            <label class="custom-file-label" for="grado_estudios">ÚLTIMO GRADO DE ESTUDIOS</label>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <h5><b>EXTRANGEROS ANEXAR:</b></h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="comprobante_migratorio" name="comprobante_migratorio">
                            <label class="form-check-label" for="comprobante_migratorio">
                                COMPROBANTE DE CALIDAD MIGRATORIA CON LA QUE SE ENCUENTRA EN EL TERRITORIO NACIONAL
                            </label>
                        </div>
                    </div>
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
                <hr style="border-color:dimgray">
                <div style="text-align: center;">
                    <h4><b>DATOS PARA LA UNIDAD DE CAPACITACIÓN</b></h4>
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
                        <label for="medio_entero_especificar" class="control-label">ESPECIFIQUE</label>
                        <input type="text" class="form-control" name="medio_entero_especificar" id="medio_entero_especificar">
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
                        <label for="sistema_capacitacion_especificar" class="control-label">ESPECIFIQUE:</label>
                        <input type="text" class="form-control" name="sistema_capacitacion_especificar" id="sistema_capacitacion_especificar">
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
                <input type="hidden" name="alumno_id" id="alumno_id" value="{{ $Alumno->id }}">
            </form>
    </div>
@endsection
