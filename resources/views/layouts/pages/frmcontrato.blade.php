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
                <label for="numerocontrato" class="control-label">Número de Contrato</label>
                <input type="text" class="form-control" id="numerocontrato" name="numerocontrato" placeholder="Número de Contrato">
            </div>
            <!--Unidad Fin-->
            <!-- nombre curso -->
            <div class="form-group col-md-6">
                <label for="clavecurso" class="control-label">Clave del Curso</label>
                <input type="text" class="form-control" id="clavecurso" name="clavecurso">
            </div>
            <!-- nombre curso FIN-->
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="numerocontrolinstructor" class="control-label">Número de control de Instructor</label>
                    <input type="text" class="form-control" id="numerocontrolinstructor" name="numerocontrolinstructor">
                </div>
                <!--clasificacion-->
                <div class="form-group col-md-6">
                    <label for="iva" class="control-label">IVA</label>
                    <input type="text" class="form-control" id="iva" name="iva">
                </div>
                <!--clasificacion END-->
            </div>
            <div class="form-row">
            <!-- Destinatario -->
            <div class="form-group col-md-6">
                <label for="lugarexpedicion" class="control-label">Lugar de Expedición</label>
                <input type="text" class="form-control" id="lugarexpedicion" name="lugarexpedicion" placeholder="Lugar de Expedición">
            </div>
            <!-- Destinatario END -->
            <!-- Puesto-->
            <div class="form-group col-md-6">
                <label for="fechafirma" class="control-label">Fecha de Firma</label>
                <input type="date" class="form-control" id="fechafirma" name="fechafirma">
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
                <label for="nombredetestigorepresentante" class="control-label">Nombre de Testigo Representante</label>
                <input type="text" class="form-control" id="nombredetestigorepresentante" name="nombredetestigorepresentante">
                </div>
                <!-- Nombre de testigo representante END-->
            </div>
            <div class="form-row">
                <!-- nombre Testigo Instructor -->
                <div class="form-group col-md-4">
                    <label for="nombretestigoinstructor" class="control-label">Nombre de Testigo Instructor</label>
                    <input type="text" class="form-control" id="nombretestigoinstructor" name="nombretestigoinstructor">
                </div>
                <!-- nombre Testigo Instructor END -->
            </div>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="nombretestigoinstructor" class="control-label">Observaciones del Instructor</label>
                    <textarea class="form-control"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
        <br>
    </div>
@endsection
