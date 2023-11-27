<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Pago | SIVyC Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> <br>
        @endif
        <div style="text-align: right;width:60%">
            <label><h1>Registro de Pago</h1></label>
        </div>
        <br>
        <br>
            @csrf
            <div class="form-row">
                <h2> Confirmación de Datos </h2>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-3">
                    <label for="inputnumero_control">Numero de Control de Instructor</label>
                    <input type="text" name="numero_control" id="numero_control" disabled class="form-control" aria-required="true" value="{{$data->numero_control}}">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputnombre_instructor">Nombre de Instructor</label>
                    <input type="text" name="nombre_instructor" id="nombre_instructor" disabled class="form-control" aria-required="true" value="{{$nomins}}">
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-4">
                    <label for="inputnombre_curso">Nombre de Curso</label>
                    <input type="text" name="nombre_curso" id="nombre_curso" disabled class="form-control" aria-required="true" value="{{$data->curso}}">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputunidad_cap">Unidad de Capacitacion</label>
                    <input type="text" name="unidad_cap" id="unidad_cap" disabled class="form-control" aria-required="true" value="{{$data->unidad_capacitacion}}">
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-2">
                    <label for="inputclave_grupo">Clave de Grupo</label>
                    <input type="text" name="clave_grupo" id="clave_grupo" disabled class="form-control" aria-required="true" value="{{$data->clave}}">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputmonto_pago">Monto de Pago</label>
                    <input type="text" name="monto_pago" id="monto_pago" disabled class="form-control" aria-required="true" value="{{$data->liquido}}">
                </div>
                <div class="form-gorup col-md-2">
                    <label for="inputiva">IVA</label>
                    <input type="text" name="iva" id="iva" disabled class="form-control" aria-required="true" value="{{$data->iva}}">
                </div>
            </div>
            <hr style="border-color:dimgray">
            <div class="form-row">
                <h2>Ingreso de Datos</h2>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-3">
                    <label for="inputnumero_pago">Numero de Pago</label>
                    <input type="text" disabled name="numero_pago" id="numero_pago" class="form-control" value="{{$data->no_pago}}" aria-required="true">
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputfecha_pago">Fecha de Pago</label>
                    <input type="text" disabled name="fecha_pago" id="fecha_pago" class="form-control" value="{{$data->fecha}}" aria-required="true">
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-gorup col-md-6">
                    <label for="inputconcepto">Descripcion de concepto</label>
                    <textarea cols="6" disabled rows="5" name="concepto" id="concepto" class="form-control" aria-required="true">{{$data->descripcion}}</textarea>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                </div>
            </div>
    </section>
    <br>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
<script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
<script src="{{ asset("js/validate/orlandoValidate.js") }}"></script>
@endsection
