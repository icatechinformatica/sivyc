@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Modificación de Contrato | Sivyc Icatech')
<!--seccion-->
@section('content')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        /* display: none; <- Crashes Chrome on hover */
        -webkit-appearance: none;
        margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
    }

    input[type=number] {
        -moz-appearance:textfield; /* Firefox */
    }
</style>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card-header">
        <h1>Modificación de Contrato y Solicitud de Pago</h1>
    </div>
    <div class="card card-body" style=" min-height:450px;">
        <form action="{{ route('contrato-savemod') }}" method="post" id="registercontrato"  enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputobservacion" class="control-label"><b>Observaciones de Rechazo</b></label>
                    <textarea cols="4" rows="4" type="text" class="form-control" readonly aria-required="true" id="observacion" name="observacion">{{$datacon->observacion}}</textarea>
                </div>
            </div>
            <hr style="border-color:dimgray">
             <div style="text-align: right;width:60%">
                <label for="titulocontrato"><h2>Apartado de Contrato</h2></label>
            </div>
            <div style="text-align: right;width:90%">
                <label for="titulocontrato"><h5>fecha: {{$fechaActual}}</h5></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                <label for="numero_contrato" class="control-label">Número de Contrato</label>
                <input type="text" class="form-control" id="numero_contrato" name="numero_contrato" placeholder="Número de Contrato" value="{{$datacon->numero_contrato}}" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputnombre_curso" class="control-label">Nombre del Curso</label>
                    <input type="text"  class="form-control" value="{{$data->curso}}" id="nombre_curso" name="nombre_curso" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label for="clavecurso" class="control-label">Clave del Curso</label>
                    <input type="text"  value={{$data->clave}} class="form-control" id="clavecurso" name="clavecurso" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputnombre_instructor" class="control-label">Nombre del Instructor</label>
                    <input type="text"  class="form-control" value="{{$nombrecompleto}}" id="nombre_instructor" name="nombre_instructor" readonly>
                </div>
                <div class="form-group col-md-3">
                    <label for="clavecurso" class="control-label">Area de Conocimiento del Instructor</label>
                    <input type="text" readonly  class="form-control" value="{{$data->espe}}" id="perfnom" name="perfnom">
                </div>
                <div class="form-group col-md-3">
                    <label for="clavecurso" class="control-label">Memorandum de Validación del Instructor</label>
                    <input type="text" readonly class="form-control" value="{{$data->instructor_mespecialidad}}" id="nombre_persel" name="nombre_persel">
                    {{-- <input type="text" hidden class="form-control" value="{{$especialidad_seleccionada->id}}" id="perfil_instructor" name="perfil_instructor"> --}}
                </div>
                <div class="form-group col-md-2">
                    <label for="clavecurso" class="control-label">Validación de instructor</label>
                    @if ($memoval != NULL)
                        <a class="btn btn-info control-label" href={{$memoval}} target="_blank">Validación de Instructor</a><br>
                    @else
                        <a class="btn btn-danger" disabled>Validación de Instructor</a><br>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputcantidad_numero" class="control-label">Monto Total de los Honorarios (En Numero)</label>
                    <input type="text" class="form-control" id="cantidad_numero" name="cantidad_numero" value="{{$pago}}" readonly >
                </div>
                <div class="form-group col-md-6">
                    <label for="inputcantidad_letras" class="control-label">Monto Total de los Honorarios (En Letra)</label>
                    <input type="text" class="form-control" id="cantidad_letras" name="cantidad_letras" readonly >
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="lugar_expedicion" class="control-label">Municipio de la Firma</label>
                    <input type="text" class="form-control" id="lugar_expedicion" name="lugar_expedicion" placeholder="Lugar de Expedición" value="{{$datacon->municipio}}" >
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputnombre_director" class="control-label">Nombre del Director/Encargado de Unidad de Capacitación</label>
                    <input type="text" class="form-control" id="nombre_director" name="nombre_director" placeholder="Director de Unidad de Capacitación" readonly value="{{$funcionarios['director']}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo1" class="control-label">Puesto del Director/Encargado de Unidad de Capacitación</label>
                    <input readonly type="text" class="form-control" id="puesto_director" name="puesto_director" value="{{$funcionarios['directorp']}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="testigo_icatech" class="control-label">Unidad de Capacitación</label>
                    <input readonly type="text" class="form-control" id="unidad_capacitacion" name="unidad_capacitacion"  value="{{$unidadsel->unidad}}">
                </div>
            </div>
            <footer class="control-footer">Anexar documento de factura en caso de contar con ella</footer>
            <hr style="border-color:dimgray">
            <h2>Testigos</h2>
            <br>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputtestigo1" class="control-label">Nombre de Testigo de Departamento Académico</label>
                    <input type="text" class="form-control" id="testigo1" name="testigo1" readonly value="{{$funcionarios['academico']}}" >
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo1" class="control-label">Puesto de Testigo</label>
                    <input readonly type="text" class="form-control" id="puesto_testigo1" name="puesto_testigo1" value="{{$funcionarios['academicop']}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputtestigo2" class="control-label">Nombre de Testigo de la Delegación Administrativa</label>
                    <input type="text" class="form-control" id="testigo3" name="testigo3" readonly value="{{$funcionarios['delegado']}}" >
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo2" class="control-label">Puesto del Testigo</label>
                    <input readonly type="text" class="form-control" id="puesto_testigo3" name="puesto_testigo3" value="{{$funcionarios['delegadop']}}">
                </div>
            </div>
            {{-- seccion de solcitud de pago --}}
            <hr style="border-color:dimgray">
            <div style="text-align: right;width:65%">
                <label for="titulocontrato"><h2>Apartado de Solicitud de Pago</h2></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputno_memo">Numero de Memorandum</label>
                    <input id="no_memo" name="no_memo" type="text" class="form-control" @if(isset($datap))value="{{$datap->no_memo}}"@endif>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputsolicitud_fecha">Fecha de Solicitud de Pago</label>
                    <input id="solicitud_fecha" name="solicitud_fecha" type="date" class="form-control" @if(isset($datap))value="{{$datap->solicitud_fecha}}@endif">
                </div>
            </div>
            <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputeremite">Nombre de Remitente</label>
                        <input id="remitente" name="remitente" type="text" class="form-control" readonly value="{{$funcionarios['director']}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_para">Puesto de Remitente</label>
                        <input id="remitente_puesto" readonly name="remitente_puesto" type="text" class="form-control" value="{{$funcionarios['directorp']}}">
                    </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputelaboro">Nombre de Quien Elabora</label>
                    <input id="nombre_elabora" name="nombre_elabora" type="text" class="form-control" readonly value="{{$funcionarios['elabora']}}">

                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_para">Puesto de Quien Elabora</label>
                    <input id="puesto_elabora" readonly name="puesto_elabora" type="text" class="form-control" value="{{$funcionarios['elaborap']}}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputnombre_para">Nombre del Destinatario</label>
                    <input id="destino" name="destino" type="text" class="form-control" readonly value="{{$funcionarios['destino']}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_para">Puesto del Destinatario</label>
                    <input id="destino_puesto" readonly name="destino_puesto" type="text" class="form-control" value="{{$funcionarios['destinop']}}">
                </div>
            </div>
            <br>
            <h3>Información para Soporte de Pago</h3>
            <div class="form-row">
                @if($regimen->modinstructor == 'HONORARIOS')
                    <div class="form-group col-md-3">
                        <label for="inputarch_factura" class="control-label">Factura de Instructor</label>
                        <input type="file" accept="application/pdf" class="form-control" id="arch_factura" name="arch_factura" placeholder="Archivo PDF">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputliquido" class="control-label">Importe Liquido en Factura</label>
                        <input type="number" step="0.01" name="liquido" id="liquido" class="form-control" @if(isset($datap)) value="{{$datap->liquido}}" @endif>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputfolio_fiscal" class="control-label">Folio Fiscal</label>
                        <input type="text" name="folio_fiscal" id="folio_fiscal" class="form-control" value="{{$datacon->folio_fiscal}}">
                    </div>
                @else
                    <div class="form-group col-md-3">
                        <label for="inputliquido" class="control-label">Importe</label>
                        <input type="number" step="0.01" name="liquido" id="liquido" class="form-control" @if(isset($datap)) value="{{$datap->liquido}}" @endif>
                    </div>
                @endif
            </div>
            <h2>Con Copia Para</h2>
            <br>
            <!-- START CCP -->
                <h3>CCP 1</h3>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputnombre_ccp1">Nombre</label>
                        <input id="ccp1" name="ccp1" type="text" class="form-control" readonly value="{{$funcionarios['ccp1']}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_para">Puesto</label>
                        <input id="ccpa1" readonly name="ccpa1" type="text" class="form-control" value="{{$funcionarios['ccp1p']}}">
                    </div>
                </div>
                <h3>CCP 2</h3>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputnombre_ccp2">Nombre</label>
                        <input id="ccp2" name="ccp2" type="text" class="form-control" readonly value="{{$funcionarios['ccp2']}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_para">Puesto</label>
                        <input id="ccpa2" readonly name="ccpa2" type="text" class="form-control"  value="{{$funcionarios['ccp2p']}}">
                    </div>
                </div>
                <h3>CCP 3</h3>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputnombre_ccp3">Nombre</label>
                        <input id="ccp3" name="ccp3" type="text" class="form-control" readonly value="{{$funcionarios['delegado']}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_para">Puesto</label>
                        <input id="ccpa3" readonly name="ccpa3" type="text" class="form-control" value="{{$funcionarios['delegadop']}}">
                    </div>
                </div>
            <!-- END CC -->
            <br>
            <input id="id_folio" name="id_folio" hidden value='{{$data->id_folios}}'>
            <input id="id_contrato" name="id_contrato" hidden value='{{$datacon->id_contrato}}'>
            <input hidden id="id_pago" name="id_pago" @if(isset($datap))value="{{$datap->id}}"@endif>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                </div>
                <div class="form-group col-md-2">
                        <button type="submit" id="save-contrato" name="save-contrato" class="btn btn-primary" >Guardar</button>
                            </form>
                </div>
                @if($generarEfirmaContrato)
                    <div class="form-group col-md-2">
                        <form action="{{ route('contrato-efirma') }}" method="post" id="registerecontrato">
                            @csrf
                            <input type="text" name="idc" id="idc" value='{{$datacon->id_contrato}}' hidden>
                            <input type="text" name="clavecurso" id="clavecurso" value='{{$data->clave}}' hidden>
                            <button   button type="submit" class="btn btn-red" >Generar Contrato E.Firma</button>
                    </div>
                @endif
                @if($generarEfirmaPago && isset($datap->id))
                    <div class="form-group col-md-2">
                        <form action="{{ route('solicitud-pago-efirma') }}" method="post" id="registersolicitudpago">
                            @csrf
                            <input type="text" name="idp" id="idp" value='{{$datap->id}}' hidden>
                            <input type="text" name="idcon" id="idcon" value='{{$datacon->id_contrato}}' hidden>
                            <input type="text" name="clavecursop" id="clavecursop" value='{{$data->clave}}' hidden>
                            <button   button type="submit" class="btn btn-red" >Generar Solicitud de Pago E.Firma</button>
                        </form>
                    </div>
                @endif
            </div>
        </form>
        <br>
    </div>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
<script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
@endsection
