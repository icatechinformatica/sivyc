<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Validación de Suficiencia Presupuestal| Sivyc Icatech')
@section('content')
    <section class="container g-pt-50">
        <form method="POST" action="{{ route('supre-rechazo') }}" id="rechazosupre">
            @csrf
                <div class="text-center">
                    <h1>Validacion de Suficiencia Presupuestal</h1>
                </div>
                <br>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label for="dropfecha_apertura">Fecha de Apertura ARC-01</label>
                    <input name="fecha_apertura" id="fecha_apertura" type="date" disabled value="{{$fecha_apertura}}" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="dropno_memo">Numero de Memorandum</label>
                    <input name="no_memo" id="no_memo" type="text" disabled value="{{$data->no_memo}}" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="dropfecha_memo">Fecha de Memorandum</label>
                    <input name="fecha_memo" id="fecha_memo" type="date" disabled value="{{$data->fecha}}" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="dropcrit_pago">Criterio de Pago</label>
                        <input name="crit_pago" id="crit_pago" type="text" disabled value="{{$criterio_pago->cp}} - {{$criterio_pago->perfil_profesional}}" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="drouni_cap">Unidad de Capacitación</label>
                        <input name="uni_cap" id="uni_cap" type="text" disabled value="{{$data->unidad_capacitacion}}"  class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="droparea">Area de Adscripcion</label>
                        <input name="area" id="area" type="text" disabled value="{{$getremitente->puesto}}" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="dropnombre_dir">Nombre del Director de Unidad</label>
                        <input name="nombre_dir" id="nombre_dir" type="text" disabled value="{{$getremitente->nombre}} {{$getremitente->apellidoPaterno}} {{$getremitente->apellidoMaterno}}" class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <button type="button" id="valsupre_rechazar" name="valsupre_rechazar" class="btn btn-danger">Rechazar</a>
                    </div>
                    <div class="form-group col-md-2">
                        <button type="button" id="valsupre_validar" name="valsupre_validar" class="btn btn-success">Validar</a>
                    </div>
                </div>
                <div id="divrechazar" class="form-row d-none d-print-none">
                    <div class="form-group col-md-6">
                        <label for="inputcomentario_rechazo">Describa el motivo de rechazo</label>
                        <textarea name="comentario_rechazo" id="comentario_rechazo" cols="6" rows="6" class="form-control"></textarea>
                    </div>
                </div>
                <div id="divconf_rechazar" class="form-row d-none d-print-none">
                    <div class="form-group col-md-3">
                        <button type="submit" class="btn btn-danger" >Confirmar Rechazo</button>
                        <input hidden id="id" name="id" value="{{$data->id}}">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <a class="btn btn-warning" href="{{URL::previous()}}">Regresar</a>
                        </div>
                    </div>
                </div>
                <br>
        </form>
        <form method="POST" action="{{ route('supre-validado') }}" id="validadosupre">
            @csrf
                <div id="div1" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <label for="inputfolio_validacion">Folio de Validación</label>
                        <input  type="text" name="folio_validacion" id="folio_validacion" class="form-control" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputfecha_validacion">Fecha de Validación</label>
                        <input name="fecha_val" id="fecha_val" type="date" class="form-control" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputfinanciamiento">Fuente de Financiamiento</label>
                        <select class="form-control" name="financiamiento" id="financiamiento" required>
                            {{-- <option value="">SELECCIONE</option> --}}
                            <option value="FEDERAL" @if($criterio_pago->cp != '12') selected @endif>FEDERAL</option>
                            {{-- <option value="ESTATAL">ESTATAL</option> --}}
                            <option value="FEDERAL Y ESTATAL" @if($criterio_pago->cp == '12') selected @endif>FEDERAL Y ESTATAL</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputobservacion">Observación</label>
                        <textarea style="text-transform: none;" name="observacion" id="observacion" cols="6" rows="6" class="form-control"></textarea>
                    </div>
                </div>
                <hr style="border-color:dimgray">
                <div id="div3" class="d-none d-print-none">
                    <h3>Con Copia Para El Delegado:</h3>
                </div>
                </div>
                <div id="div7" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <input  type="text" name="ccp4" id="ccp4" class="form-control" placeholder="Nombre Completo" value="{{$delegado->delegado_administrativo}}" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa4" id="ccpa4" readonly class="form-control" placeholder="puesto" value="{{$delegado->pdelegado_administrativo}}" readonly>
                    </div>
                </div>
                <div id="confval" class="row d-none d-print-none">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-success" >Confirmar Validación</button>
                            <input hidden id="id" name="id" value="{{$data->id}}">
                            <input hidden id="directorio_id" name="directorio_id" value="{{$directorio->id}}">
                        </div>
                    </div>
                </div>
                <br>
        </form>
    </section>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
<script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
@endsection
