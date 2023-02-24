<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Confirmación de Contrato| Sivyc Icatech')
@section('content')
    <section class="container g-pt-50">
        <div class="text-center">
            <h1>Confirmación de Datos de Contrato</h1>
            <div style="align-content: center">
                <h2>Por favor verifique el documento y que todo este correcto.</h2>
            </div>
        </div>
        <br>
        <div class="text-center">
            <h3><strong>Vista Previa del Documento</strong></h3>
            <a class="btn btn-info" id="valsupre_pdf" name="valsupre_pdf" href="{{route('pre_contrato', ['id' => $idc])}}" href="/contrato/previsualizacion/{{$idc}}" target="_blank">Previsualización de Contrato</a><br>
        </div>
        <div class="form-row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-warning" href="{{route('contrato-mod', ['id' => $idc])}}">Modificar</a>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="/contrato/inicio">Confirmar Solicitud de Contrato</a><br>
                </div>
            </div>
        </div>
        <br>
    </section>
@stop
