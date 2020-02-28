<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Perfil Profesional | Sivyc Icatech')
@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> hay algunos problemas con los campos.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <form action="{{ route('perfilinstructor-guardar') }}" method="post" id="registerinstructor">
            @csrf
                <div class="text-center">
                    <h1>Añadir Perfil Profesional</h1>
                </div>
                <label><h2>Añadir Perfil Profesional</h2></label>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputgrado_estudio">Nivel de estudio que cubre</label>
                        <select class="form-control" id="grado_estudio" name="grado_estudio"
                            <option value="diploma">Diploma</option>
                            <option value="certificado">Certificado</option>
                            <option value="primaria">Primaria</option>
                            <option value="secundaria">Secundaria</option>
                            <option value="bachillerato">Bachillerato</option>
                            <option value="carrera_tecnica">Carrera Tecnica</option>
                            <option value="acta_evaluacion_profesional">Acta de Evaluacion Profesional</option>
                            <option value="profesional">Profesional</option>
                            <option value="especialidad">Especialidad</option>
                            <option value="maestria">Maestria</option>
                            <option value="doctorado">Doctorado</option>
                        </select>
                    </div>
                    <div class="form-group col-md-5">
                        <label for="inputperfil_profesional">Perfil Profesional</label>
                        <input name="perfil_profesional" id="perfil_profesional" type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputarea_carrera">Area del Estudio</label>
                        <input name="area_carrera" id="area_carrera" type="text" class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="especialidad">Especialidad</label>
                        <input name="especialidad" id="especialidad" type="text" class="form-control" aria-required="true">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="inputnombre_carrera">Nombre del Estudio</label>
                        <input name="nombre_carrera" id="nombre_carrera" type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputestatus">Estatus</label>
                        <select class="form-control" name="estatus" id="estatus">
                            <option value="Sin Especificar">Sin Especificar</option>
                            <option value="Trunco">Trunco</option>
                            <option value="pasante">Pasante</option>
                            <option value="titulo">Titulo</option>
                            <option value="cedula">Cedula</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputinstitucion_pais">Pais de la Institucion Educativa</label>
                        <input name="institucion_pais" id="institucion_pais" type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputinstitucion_entidad">Entidad de la Institucion Educativa</label>
                        <input name="institucion_entidad" id="institucion_entidad" type="text" class="form-control" aria-required="true">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="inputfecha_documento">Fecha de Expedicion del Documento</label>
                        <input name="fecha_documento" id="fecha_documento" type="date" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputfolio_documento">Folio del Documento</label>
                        <input name="folio_documento" id="folio_documento" type="text" class="form-control" aria-required="true">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputclave_especialidad">Clave de la Especialidad</label>
                        <input type="text" name="clave_especialidad" id="clave_especialidad" class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-row">
                    <div class="form-group col-md-1" style="text-align: right;width:0%">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="form-group col-md-11" style="text-align: right;width:100%">
                        <button type="submit" class="btn btn-primary" >Agregar</button>
                    </div>
                </div>
                <br>
                <input type="hidden" name="idInstructor" id="idInstructor" value="{{ $idInstructor }}">
        </form>
    </section>
@stop

