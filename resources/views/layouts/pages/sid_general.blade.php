@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Inscripción Alumno | Sivyc Icatech')
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
            <h3><b>INSCRIPCIÓN (SID - 01)</b></h3>
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
                    <label for="apellidoPaterno" class="control-label">Apellido Paterno: {{$Alumno->apellido_paterno}}</label>
                </div>
                <!-- apellido paterno END -->
                <!-- apellido materno-->
                <div class="form-group col-md-3">
                    <label for="apellidoMaterno" class="control-label">Apellido Materno: {{$Alumno->apellido_materno}}</label>
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
            <form method="POST" id="form_sid_registro" action="{{ route('alumnos.update-sid') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tblubicacion" class="control-label">UNIDADES</label>
                        <select class="form-control" id="tblubicacion" name="tblubicacion" required>
                            <option value="">--SELECCIONAR--</option>
                            @foreach ($tblUnidades as $itemTblUnidades)
                                <option value="{{$itemTblUnidades->ubicacion}}">{{$itemTblUnidades->ubicacion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tblunidades" class="control-label">UNIDAD O ACCIÓN MÓVIL A LA QUE SE DESEA INSCRIBIRSE</label>
                        <select class="form-control" id="tblunidades" name="tblunidades" required>
                            <option value="">--SELECCIONAR--</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="especialidad" class="control-label">ESPECIALIDAD A LA QUE DESEA INSCRIBIRSE:</label>
                        <select class="form-control" id="especialidad_sid" name="especialidad_sid" required>
                            <option value="">--SELECCIONAR--</option>
                            @foreach ($especialidades as $itemEspecialidad)
                                <option value="{{$itemEspecialidad->id}}">{{ $itemEspecialidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="horario" class="control-label">TIPO DE CURSO</label>
                        <select class="form-control" id="tipo_curso" name="tipo_curso" required>
                            <option value="">--SELECCIONAR--</option>
                            <option value="PRESENCIAL">PRESENCIAL</option>
                            <option value="A DISTANCIA">A DISTANCIA</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8">
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
                <!--botones de enviar y retroceder-->
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                        </div>
                        @can('alumnos.inscripcion.store')
                            <div class="pull-right">
                                <button type="submit" class="btn btn-primary" >Guardar</button>
                            </div>
                        @endcan
                    </div>
                </div>
                <input type="hidden" name="alumno_id" id="alumno_id" value="{{ $Alumno->id }}">
            </form>
    </div>
@endsection
