@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Modificación de Contrato | Sivyc Icatech')
<!--seccion-->
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
@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="container g-pt-50">
        <form action="{{ route('contrato-savemod') }}" method="post" id="registercontrato"  enctype="multipart/form-data">
            @csrf
            <div style="text-align: right;width:80%">
                <label for="titulocontrato"><h1>Modificación de Contrato y Solicitud de Pago</h1></label>
            </div>
            <br><br>
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
                {{-- <div class="form-group col-md-3">
                    <label for="fecha_firma" class="control-label">Fecha de Firma</label>
                    <input type="date" class="form-control" id="fecha_firma" name="fecha_firma" value="{{$datacon->fecha_firma}}" readonly>
                </div> --}}
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputnombre_director" class="control-label">Nombre del Director/Encargado de Unidad de Capacitación</label>
                    <input type="text" class="form-control" id="nombre_director" name="nombre_director" placeholder="Director de Unidad de Capacitación" value="{{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}" >
                    <input type="text" class="form-control" id="id_director" name="id_director" value="{{$director->id}}" hidden>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo1" class="control-label">Puesto del Director/Encargado de Unidad de Capacitación</label>
                    <input readonly type="text" class="form-control" id="puesto_director" name="puesto_director" value="{{$director->puesto}}">
                </div>
                <div class="form-group col-md-4">
                    <label for="testigo_icatech" class="control-label">Unidad de Capacitación</label>
                    <input readonly type="text" class="form-control" id="unidad_capacitacion" name="unidad_capacitacion"  value="{{$unidadsel->unidad}}">
                    {{-- <select class="form-control" name="unidad_capacitacion"  id="unidad_capacitacion">
                        @if ($unidadsel != null)
                            <option value="{{$unidadsel->unidad}}">{{$unidadsel->unidad}}</option>
                        @else
                            <option value="">SELECCIONE UNIDAD</option>
                        @endif
                        @foreach ( $unidadlist as $cadwell )
                            <option value="{{$cadwell->unidad}}">{{$cadwell->unidad}}</option>
                        @endforeach
                    </select> --}}
                </div>
            </div>
            <footer class="control-footer">Anexar documento de factura en caso de contar con ella</footer>
            <hr style="border-color:dimgray">
            <h2>Testigos</h2>
            <br>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputtestigo1" class="control-label">Nombre de Testigo de Departamento Académico</label>
                    <input type="text" class="form-control" id="testigo1" name="testigo1" value="{{$testigo1->nombre}} {{$testigo1->apellidoPaterno}} {{$testigo1->apellidoMaterno}}" >
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo1" class="control-label">Puesto de Testigo</label>
                    <input readonly type="text" class="form-control" id="puesto_testigo1" name="puesto_testigo1" value="{{$testigo1->puesto}}">
                    <input type="text" name="id_testigo1" id="id_testigo1" value="{{$testigo1->id}}" hidden>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputtestigo2" class="control-label">Nombre de Testigo del Departamento de Vinculación</label>
                    <input type="text" class="form-control" id="testigo2" name="testigo2" value="{{$testigo2->nombre}} {{$testigo2->apellidoPaterno}} {{$testigo2->apellidoMaterno}}" >
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo2" class="control-label">Puesto del Testigo</label>
                    <input readonly type="text" class="form-control" id="puesto_testigo2" name="puesto_testigo2" value="{{$testigo2->puesto}}">
                    <input type="text" name="id_testigo2" id="id_testigo2" value="{{$testigo2->id}}" hidden>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputtestigo2" class="control-label">Nombre de Testigo de la Delegación Administrativa</label>
                    <input type="text" class="form-control" id="testigo3" name="testigo3" value="{{$testigo3->nombre}} {{$testigo3->apellidoPaterno}} {{$testigo3->apellidoMaterno}}" >
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo2" class="control-label">Puesto del Testigo</label>
                    <input readonly type="text" class="form-control" id="puesto_testigo3" name="puesto_testigo3" value="{{$testigo3->puesto}}">
                    <input type="text" name="id_testigo3" id="id_testigo3" value="{{$testigo3->id}}" hidden>
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
                {{-- <div class="form-group col-md-3">
                    <label for="inputfecha_agenda">Fecha de Entrega Fisica</label>
                    <input id="fecha_agenda" name="fecha_agenda" type="date" class="form-control" value="{{$datap->fecha_agenda}}">
                </div> --}}
            </div>
            <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputeremite">Nombre de Remitente</label>
                        <input id="remitente" name="remitente" type="text" class="form-control" @if(isset($director))value="{{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}" @endif>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_para">Puesto de Remitente</label>
                        <input id="remitente_puesto" readonly name="remitente_puesto" type="text" class="form-control" @if(isset($director))value="{{$director->puesto}}" @endif>
                        <input id="id_remitente" name="id_remitente" @if(isset($director))value="{{$director->id}}" @endif hidden>
                    </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputelaboro">Nombre de Quien Elabora</label>
                    <input id="nombre_elabora" name="nombre_elabora" type="text" class="form-control" @if(isset($elaboro))value="{{$elaboro->nombre}} {{$elaboro->apellidoPaterno}} {{$elaboro->apellidoMaterno}}"@endif>

                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_para">Puesto de Quien Elabora</label>
                    <input id="puesto_elabora" readonly name="puesto_elabora" type="text" class="form-control" @if(isset($elaboro))value="{{$para->puesto}}"@endif>
                    <input id="id_elabora" name="id_elabora" hidden @if(isset($elaboro))value="{{$directorio->solpa_elaboro}}"@endif>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputnombre_para">Nombre del Destinatario</label>
                    <input id="destino" name="destino" type="text" class="form-control" @if(isset($para))value="{{$para->nombre}} {{$para->apellidoPaterno}} {{$para->apellidoMaterno}}"@endif>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_para">Puesto del Destinatario</label>
                    <input id="destino_puesto" readonly name="destino_puesto" type="text" class="form-control" @if(isset($para))value="{{$para->puesto}}"@endif>
                    <input id="id_destino" name="id_destino" hidden @if(isset($para))value="{{$directorio->solpa_para}}"@endif>
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
                    {{-- <div class="form-group col-md-3">
                        <label for="inputarch_factura_xml" class="control-label">Factura de Instructor XML</label>
                        <input type="file" accept="application/xml" class="form-control" id="arch_factura_xml" name="arch_factura_xml" placeholder="Archivo XML">
                    </div> --}}
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
                        <input id="ccp1" name="ccp1" type="text" class="form-control" @if(isset($ccp1->nombre))value="{{$ccp1->nombre}} {{$ccp1->apellidoPaterno}} {{$ccp1->apellidoMaterno}}"@endif>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_para">Puesto</label>
                        <input id="ccpa1" readonly name="ccpa1" type="text" class="form-control" @if(isset($ccp1->nombre))value="{{$ccp1->puesto}}"@endif>
                        <input id="id_ccp1" name="id_ccp1" hidden @if(isset($ccp1->nombre))value="{{$directorio->solpa_ccp1}}"@endif>
                    </div>
                </div>
                <h3>CCP 2</h3>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputnombre_ccp2">Nombre</label>
                        <input id="ccp2" name="ccp2" type="text" class="form-control" @if(isset($ccp2->nombre)) value="{{$ccp2->nombre}} {{$ccp2->apellidoPaterno}} {{$ccp2->apellidoMaterno}}" @endif>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_para">Puesto</label>
                        <input id="ccpa2" readonly name="ccpa2" type="text" class="form-control"  @if(isset($ccp2->nombre)) value="{{$ccp2->puesto}}" @endif>
                        <input id="id_ccp2" name="id_ccp2" hidden  @if(isset($ccp2->nombre)) value="{{$directorio->solpa_ccp2}}" @endif>
                    </div>
                </div>
                <h3>CCP 3</h3>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputnombre_ccp3">Nombre</label>
                        <input id="ccp3" name="ccp3" type="text" class="form-control" @if(isset($ccp3->nombre)) value="{{$ccp3->nombre}} {{$ccp3->apellidoPaterno}} {{$ccp3->apellidoMaterno}}" @endif>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_para">Puesto</label>
                        <input id="ccpa3" readonly name="ccpa3" type="text" class="form-control" @if(isset($ccp3->nombre)) value="{{$ccp3->puesto}}"@endif>
                        <input id="id_ccp3" name="id_ccp3" hidden @if(isset($ccp3->nombre)) value="{{$directorio->solpa_ccp3}}"@endif>
                    </div>
                </div>
            <!-- END CC -->
            <br>
            <input id="id_folio" name="id_folio" hidden value='{{$data->id_folios}}'>
            <input id="id_directorio" name="id_directorio" hidden value='{{$data_directorio->id}}'>
            <input id="id_contrato" name="id_contrato" hidden value='{{$datacon->id_contrato}}'>
            <input hidden id="id_pago" name="id_pago" @if(isset($datap))value="{{$datap->id}}"@endif>
            <div class="form-row">
                <div class="form-group col-md-8">
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
