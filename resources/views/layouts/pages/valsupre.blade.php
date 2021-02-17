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
                    <div class="form-group col-md-4">
                        <label for="dropno_memo">Numero de Memorandum</label>
                    <input name="no_memo" id="no_memo" type="text" disabled value="{{$data->no_memo}}" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="dropfecha_memo">Fecha de Memorandum</label>
                    <input name="fecha_memo" id="fecha_memo" type="date" disabled value="{{$data->fecha}}" class="form-control">
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
                <div class="form-row" style="">
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
                        <input  type="text" name="folio_validacion" id="folio_validacion" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputfecha_validacion">Fecha de Validación</label>
                        <input name="fecha_val" id="fecha_val" type="date" class="form-control">
                    </div>
                </div>
                <div id="div2" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <label for="inputnombre_firmante">Nombre del Firmante</label>
                        <input  type="text" name="nombre_firmante" id="nombre_firmante" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_firmante">Puesto de firmante</label>
                        <input name="puesto_firmante" readonly id="puesto_firmante" type="text" class="form-control">
                        <input id="id_firmante" name="id_firmante" hidden>
                    </div>
                </div>
                <hr style="border-color:dimgray">
                <div id="div3" class="d-none d-print-none">
                    <h3>Con Copia Para:</h3>
                </div>
            <!-- START CCP -->
                <div id="div4" class="form-row d-none d-print-none" >
                    <div class="form-group col-md-4">
                        <input  type="text" name="ccp1" id="ccp1" class="form-control" placeholder="Nombre Completo">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa1" readonly id="ccpa1" class="form-control" placeholder="Puesto">
                        <input id="id_ccp1" name="id_ccp1" hidden>
                    </div>
                </div>
                <div id="div5" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <input  type="text" name="ccp2" id="ccp2" class="form-control" placeholder="Nombre Completo">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa2" id="ccpa2" readonly class="form-control" placeholder="Puesto">
                        <input id="id_ccp2" name="id_ccp2" hidden>
                    </div>
                </div>
                <div id="div6" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <input  type="text" name="ccp3" id="ccp3" class="form-control" placeholder="Nombre Completo">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa3" id="ccpa3" readonly class="form-control" placeholder="Puesto">
                        <input id="id_ccp3" name="id_ccp3" hidden>
                    </div>
                </div>
                <div id="div7" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <input  type="text" name="ccp4" id="ccp4" class="form-control" placeholder="Nombre Completo">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa4" id="ccpa4" readonly class="form-control" placeholder="puesto">
                        <input id="id_ccp4" name="id_ccp4" hidden >
                    </div>
                </div>
            <!--END CCP-->
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
