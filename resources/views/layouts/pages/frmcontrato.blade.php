@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Formulario de Contrato | Sivyc Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        <form method="POST">
            <div style="text-align: right;width:65%">
                <label for="titulocontrato"><h1>Formulario de Contrato</h1></label>
            </div>
             <hr style="border-color:dimgray">
            <div class="form-row">
            <!-- Unidad -->
            <div class="form-group col-md-6">
                <label for="numero_contrato" class="control-label">Número de Contrato</label>
                <input type="text" class="form-control" id="numero_contrato" name="numero_contrato" placeholder="Número de Contrato">
            </div>
            <!--Unidad Fin-->
            <!-- nombre curso -->
            <div class="form-group col-md-6">
                <label for="clavecurso" class="control-label">Clave del Curso</label>
                <input type="text" disabled value={{$data->clave}} class="form-control" id="clavecurso" name="clavecurso">
            </div>
            <!-- nombre curso FIN-->
            </div>
            <div class="form-row">
            <!-- Destinatario -->
            <div class="form-group col-md-6">
                <label for="lugar_expedicion" class="control-label">Lugar de Expedición</label>
                <input type="text" class="form-control" id="lugar_expedicion" name="lugar_expedicion" placeholder="Lugar de Expedición">
            </div>
            <!-- Destinatario END -->
            <!-- Puesto-->
            <div class="form-group col-md-6">
                <label for="fecha_firma" class="control-label">Fecha de Firma</label>
                <input type="date" class="form-control" id="fecha_firma" name="fecha_firma">
            </div>
            <!-- Puesto END-->

            </div>
            <div class="form-row">
                <!-- Duracion -->
                <div class="form-group col-md-6">
                <label for="municipio" class="control-label">Municipio</label>
                <input type="text" class="form-control" id="municipio" name="municipio" placeholder="Municipio">
                </div>
                <!-- Duracion END -->
                <!-- Perfil-->
                <div class="form-group col-md-6">
                <label for="nombrerepresentantelegal" class="control-label">Nombre de Representante Legal</label>
                <input type="text" class="form-control" id="nombrerepresentantelegal" name="nombrerepresentantelegal" placeholder="Nombre de Representante Legal">
                </div>
                <!-- Perfil END-->
            </div>
            <div class="form-row">
                <!-- Director de Unidad de Capacitación -->
                <div class="form-group col-md-6">
                <label for="directordeunidaddecapacitacion" class="control-label">Director de Unidad de Capacitación</label>
                <input type="text" class="form-control" id="directordeunidaddecapacitacion" name="directordeunidaddecapacitacion" placeholder="Director de Unidad de Capacitación">
                </div>
                <!-- Director de Unidad de Capacitación END -->
                <!-- Nombre de testigo representante-->
                <div class="form-group col-md-6">
                <label for="testigo_icatech" class="control-label">Nombre de Testigo Representante</label>
                <input type="text" class="form-control" id="testigo_icatech" name="testigo_icatech">
                </div>
                <!-- Nombre de testigo representante END-->
            </div>
            <div class="form-row">
                <!-- nombre Testigo Instructor -->
                <div class="form-group col-md-4">
                    <label for="testigo_instructor" class="control-label">Nombre de Testigo Instructor</label>
                    <input type="text" class="form-control" id="testigo_instructor" name="testigo_instructor">
                </div>
                <!-- nombre Testigo Instructor END -->
            </div>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="observaciones" class="control-label">Observaciones del Instructor</label>
                    <textarea cols="3" rows="4" class="form-control"></textarea>
                </div>
            </div>
            <br>
            <input id="id_folio" name="id_folio" hidden value='{{$data->id_folios}}'>
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
        <br>
    </div>
@endsection
