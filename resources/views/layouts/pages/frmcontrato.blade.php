@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Formulario de Contrato | Sivyc Icatech')
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
    <div class="container g-pt-50">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div> <br>
        @endif
        <form action="{{ route('contrato-save') }}" method="post" id="registercontrato" enctype="multipart/form-data">
            @csrf
            <div style="text-align: right;width:80%">
                <label for="titulocontrato"><h1>Formulario de Contrato y Solicitud de Pago</h1></label>
            </div>
            <hr style="border-color:dimgray">
            <div class="form-container">
                <label for="titulocontrato"><h2>Apartado de Instructor</h2></label>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label class="control_label" for="forbanco">Banco</label>
                        <input class="form-control" type="text" id="banco" name="banco" readonly @if(isset($data->soportes_instructor)) value="{{$data->soportes_instructor->banco}}" @else value={{"$data->banco"}} @endif>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="control_label" for="fornocuenta">Numero de cuenta</label>
                        <input class="form-control" type="text" id="nocuenta" name="nocuenta" readonly @if(isset($data->soportes_instructor)) value="{{$data->soportes_instructor->no_cuenta}}" @else value={{"$data->no_cuenta"}} @endif>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="control_label" for="forclabe">Clabe</label>
                        <input class="form-control" type="text" id="clabe" name="clabe" readonly @if(isset($data->soportes_instructor)) value="{{$data->soportes_instructor->interbancaria}}" @else value={{"$data->interbancaria"}} @endif>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="control_label" for="forshow_banco">Archivo</label>
                        <a class="nav-link form-control" style="border: none !important;" target="_blank" title="Archivo de Banco" id="show_banco" name="show_banco" @if(isset($data->soportes_instructor)) href="{{$data->soportes_instructor->archivo_bancario}}" @else href={{"$data->archivo_bancario"}} @endif>
                            <i class="fa fa-file-pdf-o fa-2x fa-lg text-danger" style="margin: 0; padding: 0;" aria-hidden="true"></i>
                        </a>
                    </div>
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
                    <input type="text" class="form-control" id="numero_contrato" name="numero_contrato" placeholder="Número de Contrato" value="{{$uni_contrato}}" readonly>
                 </div>
             </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputnombre_curso" class="control-label">Nombre del Curso</label>
                    <input type="text" disabled class="form-control" value="{{$data->curso}}" id="nombre_curso" name="nombre_curso">
                </div>
                <div class="form-group col-md-4">
                    <label for="clavecurso" class="control-label">Clave del Curso</label>
                    <input type="text" disabled value={{$data->clave}} class="form-control" id="clavecurso" name="clavecurso">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputnombre_instructor" class="control-label">Nombre del Instructor</label>
                    <input type="text" disabled class="form-control" value="{{$nombrecompleto}}" id="nombre_instructor" name="nombre_instructor">
                </div>
                <div class="form-group col-md-3">
                    <label for="clavecurso" class="control-label">Especialidad de Conocimiento del Instructor</label>
                    <input type="text" readonly class="form-control" value="{{$data->espe}}" id="nombre_persel" name="nombre_persel">
                    {{-- <input type="text" hidden class="form-control" value="{{$especialidad_seleccionada->id}}" id="perfil_instructor" name="perfil_instructor"> --}}
                </div>
                <div class="form-group col-md-3">
                    <label for="clavecurso" class="control-label">Memorandum de Validación del Instructor</label>
                    <input type="text" readonly class="form-control" value="{{$data->instructor_mespecialidad}}" id="nombre_persel" name="nombre_persel">
                    {{-- <input type="text" hidden class="form-control" value="{{$especialidad_seleccionada->id}}" id="perfil_instructor" name="perfil_instructor"> --}}
                </div>
                <div class="form-group col-md-2">
                    <label for="clavecurso" class="control-label">Validación de instructor</label>
                    @if ($data->archivo_alta != NULL)
                        <a class="btn btn-info control-label" href={{$memoval}} target="_blank">Validación de Instructor</a><br>
                    @else
                        <a class="btn btn-danger" disabled>Validación de Instructor</a><br>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputcantidad_numero" class="control-label">Monto Total de los Honorarios sin IVA (En Numero)</label>
                    <input type="text" class="form-control" id="cantidad_numero" name="cantidad_numero" value="{{$pago}}" readonly>
                </div>
                <div class="form-group col-md-6">
                    <label for="inputcantidad_letras" class="control-label">Monto Total de los Honorarios (En Letra)</label>
                    <input type="text" class="form-control" id="cantidad_letras" name="cantidad_letras" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="lugar_expedicion" class="control-label">Municipio de la Firma</label>
                    <input type="text" class="form-control" id="lugar_expedicion" name="lugar_expedicion" placeholder="Lugar de Expedición" @if(isset($contrato))value="{{$contrato->municipio}}"@endif>
                </div>
                {{-- <div class="form-group col-md-3">
                    <label for="fecha_firma" class="control-label">Fecha de Firma</label>
                    <input type="date" class="form-control" id="fecha_firma" name="fecha_firma" @if(isset($contrato))value="{{$contrato->fecha_firma}}" @else value="{{$data->inicio}}"@endif readonly>
                </div> --}}
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputnombre_director" class="control-label">Nombre del Director/Encargado de Unidad de Capacitación</label>
                    <input type="text" class="form-control" id="nombre_director" name="nombre_director" placeholder="Director de Unidad de Capacitación" @if(isset($director))value="{{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}"@endif>
                    <input type="text" class="form-control" id="id_director" name="id_director" @if(isset($director))value="{{$director->id}}"@endif hidden>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo1" class="control-label">Puesto del Director/Encargado de Unidad de Capacitación</label>
                    <input readonly type="text" class="form-control" id="puesto_director" name="puesto_director" @if(isset($director))value="{{$director->puesto}}"@endif>
                </div>
                <div class="form-group col-md-4">
                    <label for="testigo_icatech" class="control-label">Unidad de Capacitación</label>
                    <input readonly type="text" class="form-control" id="unidad_capacitacion" name="unidad_capacitacion" @if(isset($contrato))value="{{$contrato->unidad_capacitacion}}" @else value="{{$data->unidad}}" @endif>
                    {{-- <select name="unidad_capacitacion" class="form-control mr-sm-2" id="unidad_capacitacion">
                        <option value="">SELECCIONE UNIDAD</option>
                        @foreach ($unidades as $cadwell)
                            <option value="{{$cadwell->unidad}}" @if(isset($contrato) && $contrato->unidad_capacitacion == $cadwell->unidad) selected @endif>{{$cadwell->unidad}}</option>
                        @endforeach
                    </select> --}}
                </div>
            </div>
            <h2>Testigos</h2>
            <br>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputtestigo1" class="control-label">Nombre de Testigo de Departamento Académico</label>
                    <input type="text" class="form-control" id="testigo1" name="testigo1" @if(isset($testigo1))value="{{$testigo1->nombre}} {{$testigo1->apellidoPaterno}} {{$testigo1->apellidoMaterno}}"@endif>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo1" class="control-label">Puesto de Testigo</label>
                    <input readonly type="text" class="form-control" id="puesto_testigo1" name="puesto_testigo1" @if(isset($testigo1))value="{{$testigo1->puesto}}"@endif>
                    <input type="text" name="id_testigo1" id="id_testigo1" @if(isset($testigo1))value="{{$testigo1->id}}"@endif hidden>
                </div>
            </div>
            {{-- <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputtestigo2" class="control-label">Nombre de Testigo del Departamento de Vinculación</label>
                    <input type="text" class="form-control" id="testigo2" name="testigo2" @if(isset($testigo2))value="{{$testigo2->nombre}} {{$testigo2->apellidoPaterno}} {{$testigo2->apellidoMaterno}}"@endif>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo2" class="control-label">Puesto del Testigo</label>
                    <input readonly type="text" class="form-control" id="puesto_testigo2" name="puesto_testigo2" @if(isset($testigo2))value="{{$testigo2->puesto}}"@endif>
                    <input type="text" name="id_testigo2" id="id_testigo2" @if(isset($testigo2))value="{{$testigo2->id}}"@endif hidden>
                </div>
            </div> --}}
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputtestigo2" class="control-label">Nombre de Testigo de la Delegación Administrativa</label>
                    <input type="text" class="form-control" id="testigo3" name="testigo3" @if(isset($testigo3))value="{{$testigo3->nombre}} {{$testigo3->apellidoPaterno}} {{$testigo3->apellidoMaterno}}"@endif>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo2" class="control-label">Puesto del Testigo</label>
                    <input readonly type="text" class="form-control" id="puesto_testigo3" name="puesto_testigo3" @if(isset($testigo3))value="{{$testigo3->puesto}}"@endif>
                    <input type="text" name="id_testigo3" id="id_testigo3" @if(isset($testigo3))value="{{$testigo3->id}}"@endif hidden>
                </div>
            </div>
            <hr style="border-color:dimgray">
            <div style="text-align: right;width:65%">
                <label for="titulocontrato"><h2>Apartado de Solicitud de Pago</h2></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputno_memo">Numero de Memorandum</label>
                    <input id="no_memo" name="no_memo" type="text" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputsolicitud_fecha">Fecha de Solicitud de Pago</label>
                    <input id="solicitud_fecha" name="solicitud_fecha" type="date" class="form-control">
                </div>
                {{-- <div class="form-group col-md-3">
                    <label for="inputfecha_agenda">Fecha de Entrega Fisica</label>
                    <input id="fecha_agenda" name="fecha_agenda" type="date" class="form-control">
                </div> --}}
            </div>
            <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputeremite">Nombre de Remitente</label>
                        <input id="remitente" name="remitente" type="text" class="form-control" readonly value="{{$funcionarios['director']}}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_para">Puesto de Remitente</label>
                        <input id="remitente_puesto" readonly name="remitente_puesto" type="text" class="form-control" value="{{$funcionarios['directorp']}}">
                        {{-- <input id="id_remitente" name="id_remitente" hidden> --}}
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
                        {{-- <input id="id_elabora" name="id_elabora" hidden> --}}
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
                    {{-- <input id="id_destino" name="id_destino" hidden> --}}
                </div>
            </div>
            <br>
            <h3>Información de factura para Soporte de Pago</h3>
            <br>
            <div class="form-row">
                @if($regimen->modinstructor == 'HONORARIOS')
                    <div class="form-group col-md-3">
                        <label for="inputarch_factura" class="control-label">Factura de Instructor PDF</label>
                        <input type="file" accept="application/pdf" class="form-control" id="arch_factura" name="arch_factura" placeholder="Archivo PDF">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputliquido" class="control-label">Importe Liquido en Factura</label>
                        <input type="number" step="0.01" name="liquido" id="liquido" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputfolio_fiscal" class="control-label">Folio Fiscal</label>
                        <input type="text" name="folio_fiscal" id="folio_fiscal" class="form-control">
                    </div>
                @else
                    <div class="form-group col-md-3">
                        <label for="inputliquido" class="control-label">Importe</label>
                        <input type="number" step="0.01" name="liquido" id="liquido" class="form-control">
                    </div>
                @endif
            </div>
            <br>
            <h2>Con Copia Para</h2>
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
                        {{-- <input id="id_ccp1" name="id_ccp1" hidden> --}}
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
                        <input id="ccpa2" readonly name="ccpa2" type="text" class="form-control" value="{{$funcionarios['ccp2']}}">
                        {{-- <input id="id_ccp2" name="id_ccp2" hidden> --}}
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
                        {{-- <input id="id_ccp3" name="id_ccp3" hidden> --}}
                    </div>
                </div>
            <!-- END CC -->
            <br>
            <input id="id_folio" name="id_folio" hidden value='{{$data->id_folios}}'>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" >Guardar</button>
                    </div>
                </div>
            </div>
        </form >
        <br>
    </div>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
<script src="{{ asset("js/validate/orlandoValidate.js") }}"></script>
@endsection
