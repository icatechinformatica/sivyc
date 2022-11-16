<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Validación de Suficiencia presupuestal| Sivyc Icatech')
@section('content')
    <section class="container g-pt-50">
        <form method="POST" action="{{ route('valsupre-checkmod') }}">
            @csrf
                <div class="text-center">
                    <h1>Validacion de Suficiencia Presupuestal</h1>
                    <div style="align-content: center">
                        <h2>Por favor verifique el documento y que todo este correcto.</h2>
                    </div>
                </div>
                <br>
                <div class="text-center">
                    <h3><strong>Vista Previa del Documento</strong></h3>
                    <a class="btn btn-info" id="valsupre_pdf" name="valsupre_pdf" href="/supre/validacion/pdf/{{$idb64}}" target="_blank">Validación de Suficiencia Presupuestal</a><br>
                </div>
                <div class="form-row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <button type="submit" class="btn btn-danger" >Modificar</button>
                            <input hidden id="id" name="id" value="{{$id}}">
                            <input hidden id="directorio_id" name="directorio_id" value="{{$directorio_id}}">
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-success" href="/supre/solicitud/inicio">Confirmar Validación</a><br>
                        </div>
                    </div>
                </div>
                <br>
        </form>
    </section>
@stop
