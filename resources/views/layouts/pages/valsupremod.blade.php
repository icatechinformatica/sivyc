<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Modificación de la Validación de Suficiencia Presupuestal| Sivyc Icatech')
@section('content')
    <section class="container g-pt-50">
        <div class="text-center">
            <h1>Modificación de la Validación de Suficiencia Presupuestal</h1>
        </div>
        <br>
        <form method="POST" action="{{ route('supre-validado') }}">
            @csrf
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
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputfolio_validacion">Folio de Validación</label>
                        <input  type="text" name="folio_validacion" id="folio_validacion" class="form-control" value="{{$data->folio_validacion}}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputfecha_validacion">Fecha de Validación</label>
                        <input name="fecha_val" id="fecha_val" type="date" class="form-control" value="{{$data->fecha_validacion}}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputfinanciamiento">Fuente de Financiamiento</label>
                        <select class="form-control" name="financiamiento" id="financiamiento" required>
                            <option value="">SELECCIONE</option>
                            <option value="FEDERAL" @if($data->financiamiento == 'FEDERAL') selected @endif>FEDERAL</option>
                            <option value="ESTATAL" @if($data->financiamiento == 'ESTATAL') selected @endif>ESTATAL</option>
                        </select>
                    </div>
                </div>
                <!--
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputnombre_firmante">Nombre del Firmante</label>
                        <input  type="text" name="nombre_firmante" id="nombre_firmante" class="form-control" value="{getfirmante->nombre}} {getfirmante->apellidoPaterno}} {getfirmante->apellidoMaterno}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_firmante">Puesto de firmante</label>
                        <input name="puesto_firmante" readonly id="puesto_firmante" type="text" class="form-control" value="{getfirmante->puesto}}">
                        <input id="id_firmante" name="id_firmante" hidden  value='{getfirmante->id}}'>
                    </div>
                </div>
                -->
                <hr style="border-color:dimgray">
                <h3>Con Copia Para El Delegado:</h3>
            <!-- START CCP
                <div id="div4" class="form-row " >
                    <div class="form-group col-md-4">
                        <input  type="text" name="ccp1" id="ccp1" class="form-control" placeholder="Nombre Completo" value="{getccp1->nombre}} {getccp1->apellidoPaterno}} {getccp1->apellidoMaterno}}">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa1" readonly id="ccpa1" class="form-control" placeholder="Puesto" value="{getccp1->puesto}}">
                        <input id="id_ccp1" name="id_ccp1" hidden value='{getccp1->id}}'>
                    </div>
                </div>
                <div id="div5" class="form-row ">
                    <div class="form-group col-md-4">
                        <input  type="text" name="ccp2" id="ccp2" class="form-control" placeholder="Nombre Completo" value="{getccp2->nombre}} {getccp2->apellidoPaterno}} {getccp2->apellidoMaterno}}">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa2" id="ccpa2" readonly class="form-control" placeholder="Puesto" value="{getccp2->puesto}}">
                        <input id="id_ccp2" name="id_ccp2" hidden value='{getccp2->id}}'>
                    </div>
                </div>
                <div id="div6" class="form-row ">
                    <div class="form-group col-md-4">
                        <input  type="text" name="ccp3" id="ccp3" class="form-control" placeholder="Nombre Completo" value="{getccp3->nombre}} {getccp3->apellidoPaterno}} {getccp3->apellidoMaterno}}">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa3" id="ccpa3" readonly class="form-control" placeholder="Puesto" value="{getccp3->puesto}}">
                        <input id="id_ccp3" name="id_ccp3" hidden value='{getccp3->id}}'>
                    </div>
                </div>-->
                <div id="div7" class="form-row ">
                    <div class="form-group col-md-4">
                        <input  type="text" name="ccp4" id="ccp4" class="form-control" placeholder="Nombre Completo" required @if(isset($getccp4->nombre)) value="{{$getccp4->nombre}} {{$getccp4->apellidoPaterno}} {{$getccp4->apellidoMaterno}}" @endif>
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa4" id="ccpa4" readonly class="form-control" placeholder="puesto" required @if(isset($getccp4->puesto)) value="{{$getccp4->puesto}}" @endif>
                        <input id="id_ccp4" name="id_ccp4" required hidden @if((isset($getccp4->id))) value='{{$getccp4->id}}' @endif>
                    </div>
                </div>
            <!--END CCP-->
                <div id="confval" class="row ">
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
@endsection
