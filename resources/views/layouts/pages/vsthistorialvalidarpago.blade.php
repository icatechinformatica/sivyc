<!-- Creado por Daniel Méndez-->
@extends('theme.sivyc.layout')

@section('title', 'Historial de Validación de Pago| Sivyc Icatech')

@section('content')
    <section class="container g-pt-50">

        <div class="text-center">
            <h1>Historial de Validación de Pago</h1>
        </div>
        <hr style="border-color:dimgray">
        <h2>Vista de Documentos</h2>
        <br>
        <div class="form-row">
            @if ($contratos->archivo_bancario!= NULL)
                <a class="btn btn-info" href={{$contratos->archivo_bancario}} download>Archivo Bancario</a><br>
            @else
                <a class="btn btn-danger" disabled>Archivo Bancario</a><br>
            @endif
            @if ($contratos->arch_factura != NULL)
                <a class="btn btn-info" href={{$contratos->arch_factura}} download>Factura</a><br>
            @else
                <a class="btn btn-danger" disabled>Factura</a><br>
            @endif
            @if ($datapago->arch_asistencia != NULL)
                <a class="btn btn-info" href={{$datapago->arch_asistencia}} download>Lista de Asistencia</a><br>
            @else
                <a class="btn btn-danger" disabled>Lista de Asistencia</a><br>
            @endif
            @if ($datapago->arch_evidencia != NULL)
                <a class="btn btn-info" href={{$datapago->arch_evidencia}} download>Evidencia Fotográfica</a><br>
            @else
                <a class="btn btn-danger" disabled>Evidencia Fotográfica</a><br>
            @endif
        </div>
        <hr style="border-color:dimgray">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="dropno_memo">N°. de Contrato</label>
            <input name="numero_contrato" id="numero_contrato" type="text" disabled value="{{$contratos->numero_contrato}}" class="form-control">
            </div>
            <div class="form-group col-md-2">
                <label for="dropfecha_memo">Cantidad</label>
            <input name="cantidad_letras1" id="cantidad_letras1" type="text" disabled value="{{$datapago->liquido}}" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="unidad_capacitacion">Unidad de Capacitación</label>
                <input name="unidad_capacitacion" id="unidad_capacitacion" type="text" disabled value="{{$contratos->unidad_capacitacion}}" class="form-control">
            </div>
            <div class="form-group col-md-4">
                <label for="nombre_director">Nombre del Director de Unidad</label>
                <input name="nombre_director" id="nombre_director" type="text" disabled value="{{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}" class="form-control">
            </div>
        </div>
        <br>
        @if ($contratos->status == 'Pago_Rechazado')
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="observaciones">Describa el motivo de rechazo</label>
                    <textarea name="observaciones" id="observaciones" cols="6" rows="6" class="form-control" disabled>{{$datapago->observacion}}</textarea>
                </div>
            </div>
            <br>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-warning" href="{{URL::previous()}}">Regresar</a>
                </div>
            </div>
        </div>
    </section>
@stop
