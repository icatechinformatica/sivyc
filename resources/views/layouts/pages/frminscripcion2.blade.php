@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Solicitud de Inscripción | Sivyc Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        <form method="POST">
            @csrf
            <div style="text-align: center;">
                <label for="tituloformulariocurso"><h1>Solicitud de Inscripción (SID)</h1></label>
            </div>
            <hr style="border-color:dimgray">
            <div class="form-row">
                <!-- Unidad -->
                <div class="form-group col-md-4">
                    <label for="nocontrol" class="control-label">Número de Control</label>
                    <input type="text" class="form-control" id="nocontrol" name="nocontrol" placeholder="N° de Control">
                </div>
                <!--Unidad Fin-->
                <!-- nombre curso -->
                <div class="form-group col-md-4">
                    <label for="fecha" class="control-label">Fecha</label>
                    <input type="date" class="form-control" id="fecha" name="fecha">
                </div>
                <!-- nombre curso FIN-->
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nosolicitud  " class="control-label">Número de Solicitud</label>
                    <input type="text" class="form-control" id="nosolicitud" name="nosolicitud">
                </div>
            </div>
            <div class="form-row">
                <!--nombre aspirante-->
                <div class="form-group col-md-4">
                    <label for="nombreaspirante " class="control-label">Nombre del Aspirante</label>
                    <input type="text" class="form-control" id="nombreaspirante" name="nombreaspirante">
                </div>
                <!--nombre aspirante END-->
                <!-- apellido paterno -->
                <div class="form-group col-md-4">
                    <label for="apaternoaspirante" class="control-label">Apellido Paterno</label>
                    <input type="text" class="form-control" id="apaternoaspirante" name="apaternoaspirante">
                </div>
                <!-- apellido paterno END -->
                <!-- apellido materno-->
                <div class="form-group col-md-4">
                    <label for="amaternoaspirante" class="control-label">Apellido Materno</label>
                    <input type="text" class="form-control" id="amaternoaspirante" name="amaternoaspirante">
                </div>
                <!-- apellido materno END-->

            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
        <br>
    </div>
@endsection
