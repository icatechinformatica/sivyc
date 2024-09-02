<!--plantilla trabajada por DANIEL MENDEZ CRUZ-->
@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Alumnos Matriculados | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container g-pt-50">
        <div style="text-align: left;">
            <h4><b>N° de Control: {{$alumnos[0]->no_control}}</b></h4>
        </div>
        <div class="form-row">
            <!--nombre aspirante-->
            <div class="form-group col-md-6">
                <label for="nombre " class="control-label">Nombre: {{$alumnos[0]->nombrealumno}} {{$alumnos[0]->apellido_paterno}} {{$alumnos[0]->apellido_materno}}</label>
            </div>
            <!--nombre aspirante END-->
            <div class="form-group col-md-6">
                <label for="sexo" class="control-label">Genero: {{$alumnos[0]->sexo}} </label>
            </div>
        </div>
        <div class="form-row">

            <div class="form-group col-md-3">
                <label for="curp" class="control-label">CURP: {{$alumnos[0]->curp_alumno}} </label>
            </div>
            <div class="form-group col-md-3">
                <label for="fecha_nacimiento" class="control-label">Fecha de Nacimiento: {{$alumnos[0]->fecha_nacimiento}} </label>
            </div>
            <div class="form-group col-md-3">
                <label for="telefono" class="control-label">Teléfono: {{$alumnos[0]->telefono}} </label>
            </div>
            <div class="form-group col-md-3">
                <label for="cp" class="control-label">C.P. {{$alumnos[0]->cp}} </label>
            </div>
        </div>
        <div class="form-row">

            <div class="form-group col-md-3">
                <label for="estado" class="control-label">Estado: {{$alumnos[0]->estado}} </label>
            </div>
            <div class="form-group col-md-3">
                <label for="municipio" class="control-label">Municipio: {{$alumnos[0]->municipio}} </label>
            </div>
            <div class="form-group col-md-3">
                <label for="estado_civil" class="control-label">Estado Civil: {{$alumnos[0]->estado_civil}} </label>
            </div>
            <!---->
            <div class="form-group col-md-3">
                <label for="discapacidad" class="control-label">Discapacidad que presenta: {{$alumnos[0]->discapacidad}}</label>
            </div>
        </div>
        <div class="form-row">
            <!-- domicilio -->
            <div class="form-group col-md-6">
                <label for="domicilio" class="control-label">Domicilio: {{$alumnos[0]->domicilio}}</label>
            </div>
            <!-- domicilio END -->
            <div class="form-group col-md-6">
                <label for="colonia" class="control-label">Colonia: {{$alumnos[0]->colonia}} </label>
            </div>
        </div>
        <!---->
        <hr style="border-color:dimgray">
        <div style="text-align: center;">
            <h4><b>INFORMACIÓN DEL CURSO</b></h4>
        </div>
        <!---->
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="especialidad" class="control-label">ESPECIALIDAD: {{$alumnos[0]->especialidad}}</label>
            </div>
            <div class="form-group col-md-4">
                <label for="cursos" class="control-label">CURSO: {{$alumnos[0]->nombre_curso}} </label>
            </div>
            <div class="form-group col-md-4">
                <label for="unidad" class="control-label">UNIDAD DE CAPACITACIÓN: {{$alumnos[0]->unidad}}</label>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="horario" class="control-label">HORARIO: {{$alumnos[0]->horario}} </label>
            </div>
            <div class="form-group col-md-6">
                <label for="grupo" class="control-label">GRUPO: {{$alumnos[0]->grupo}} </label>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="grado_estudios" class="control-label">TIPO DE CURSO: {{$alumnos[0]->tipo_curso}} </label>
            </div>
            <div class="form-group col-md-6">
                <label for="grado_estudios" class="control-label">¿SE ENCUENTRA EN EL CERESO? {{($alumnos[0]->cerrs == 1) ? "SI" : "NO"}} </label>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <div style="text-align: center;">
            <h4><b>DATOS GENERALES</b></h4>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="empresa" class="control-label">EMPRESA DONDE TRABAJA:</label>
                {{$alumnos[0]->empresa_trabaja}}
            </div>
            <div class="form-group col-md-6">
                <label for="puesto_empresa" class="control-label">PUESTO:</label>
                {{$alumnos[0]->puesto_empresa}}
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="antiguedad" class="control-label">ANTIGUEDAD:</label>
                {{$alumnos[0]->antiguedad}}
            </div>
            <div class="form-group col-md-8">
                <label for="direccion_empresa" class="control-label">DIRECCIÓN DE LA EMPRESA:</label>
                {{$alumnos[0]->direccion_empresa}}
            </div>
        </div>
        <div class="form-group col-md-3">
                <a href="{{route('documento.sid', ['nocontrol' => base64_encode($alumnos[0]->no_control)])}}" class="btn btn-danger btn-circle m-1 btn-circle-sm" data-toggle="tooltip"  target="_blank" data-placement="top" title="DESCARGAR SID">
                    <i class="far fa-file-pdf" aria-hidden="true"></i>
                </a>
        </div>
        <!---->
    </div>
@endsection
