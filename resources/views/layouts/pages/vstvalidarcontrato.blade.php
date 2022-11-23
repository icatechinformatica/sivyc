<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')

@section('title', 'Validar Contrato| Sivyc Icatech')

@section('content')
    <div class="container g-pt-50">
        @if ($message =  Session::get('info'))
            <div class="alert alert-info alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <div style="text-align: right;width:65%">
            <label for="titulocontrato"><h1>Validar Contrato</h1></label>
        </div>
        <hr style="border-color:dimgray">
        <h2>Datos de Validación de Instructor</h2>
        <h2>Vista de Documentos</h2>
        <div class="form-row">
            @if ($data->archivo_ine != NULL)
                <a class="btn btn-info" href={{$data->archivo_ine}} target="_blank">Comprobante INE</a><br>
            @else
                <a class="btn btn-danger" disabled>Comprobante INE</a><br>
            @endif
            @if ($data->archivo_domicilio != NULL)
                <a class="btn btn-info" href={{$data->archivo_domicilio}} target="_blank">Comprobante de Domicilio</a><br>
            @else
                <a class="btn btn-danger" disabled>Comprobante de Domicilio</a><br>
            @endif
            @if ($data->archivo_alta != NULL)
                <a class="btn btn-info" href={{$data->archivo_alta}} target="_blank">Validación de Instructor</a><br>
            @else
                <a class="btn btn-danger" disabled>Validación de Instructor</a><br>
            @endif
            @if ($data->archivo_factura != NULL)
                <a class="btn btn-info" href={{$data->arch_factura}} target="_blank">Factura</a><br>
            @else
                <a class="btn btn-danger" disabled>Factura</a><br>
            @endif
            @if ($data->archivo_rfc != NULL)
                <a class="btn btn-info" href={{$data->archivo_rfc}} target="_blank">RFC/Constancia Fiscal</a><br>
            @else
                <a class="btn btn-danger" disabled>RFC/Constancia Fiscal</a><br>
            @endif
            @if ($data->doc_validado != NULL)
                <a class="btn btn-info" href={{$data->doc_validado}} target="_blank">Validación de Suficiencia Presupuestal</a><br>
            @else
                <a class="btn btn-danger" disabled>Validación Suficiencia Presupuestal</a><br>
            @endif
            @if ($data->comprobante_pago != NULL)
                <a class="btn btn-info" href={{$data->comprobante_pago}} target="_blank">Comprobante Pago</a><br>
            @else
                <a class="btn btn-danger" disabled>Comprobante Pago</a><br>
            @endif
        </div>
        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="inputnombre_instructor" class="control-label">Nombre del Instructor</label>
                <input type="text" disabled class="form-control" value="{{$data->insnom}} {{$data->apellidoPaterno}} {{$data->apellidoMaterno}}" id="nombre_instructor" name="nombre_instructor">
            </div>
            <div class="form-group col-md-5">
                <label for="clavecurso" class="control-label">Especialidad</label>
                <input class="form-control" name="perfil_instructor" disabled id="perfil_instructor" value="{{$data->espe}}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="clavecurso" class="control-label">Memorándum de Validación</label>
                <input class="form-control" name="memo_validacion" disabled id="memo_validacion" value="{{$data->instructor_mespecialidad}}">
            </div>
            <div class="form-group col-md-3">
                <label for="clavecurso" class="control-label">Tipo Honorario</label>
                <input class="form-control" name="memo_validacion" disabled id="memo_validacion" value="{{$data->modinstructor}}">
            </div>
            <div class="form-group col-md-3">
                <label for="tipo_identificacion" class="control-label">Tipo identificación</label>
                <input class="form-control" name="tipo_identificacion" disabled id="tipo_identificacion" value="{{$data->instructor_tipo_identificacion}}">
            </div>
            <div class="form-group col-md-3">
                <label for="folio_identificacion" class="control-label">Folio de Identificación</label>
                <input class="form-control" name="folio_identificacion" disabled id="folio_identificacion" value="{{$data->instructor_folio_identificacion}}">
            </div>
        </div>
        <hr style="border-color:dimgray">
        <h2>Previsualización de Contrato</h2>
        <div class="form-row">
            {{-- <div class="form-group col-md-3">
                <a class="btn btn-danger" id="contrato_pdf" name="contrato_pdf" href="/contrato-web/{data->id_contrato}}" target="_blank">Contrato de Instructor</a>
            </div> --}}
            <div class="form-group col-md-6">
                <a class="btn btn-danger" id="contrato_pdf2" name="contrato_pdf2" href="/contrato/{{$data->id_contrato}}" target="_blank">Contrato PDF</a>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <h2>Datos del Curso</h2>
        <div class="form-row">
            @if ($data->pdf_curso != NULL)
                <a class="btn btn-info" href={{$data->pdf_curso}} download>Validación de Clave de Curso</a><br>
            @else
                <a class="btn btn-danger" disabled>Validación Clave de Curso</a><br>
            @endif
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputnombre_curso" class="control-label">Nombre del Curso</label>
                <input type="text" disabled class="form-control" value="{{$data->curso}}" id="nombre_curso" name="nombre_curso">
            </div>
            <div class="form-group col-md-3">
                <label for="clavecurso" class="control-label">Clave del Curso</label>
                <input type="text" disabled value={{$data->clave}} class="form-control" id="clavecurso" name="clavecurso">
            </div>
            <div class="form-group col-md-3">
                <label for="modcurso" class="control-label">Modalidad</label>
                <input type="text" disabled value={{$data->mod}} class="form-control" id="modcurso" name="modcurso">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="inputnombre_curso" class="control-label">Duración</label>
                <input type="text" disabled class="form-control" value="{{$data->dura}} HORAS" id="nombre_curso" name="nombre_curso">
            </div>
            <div class="form-group col-md-1">
                <label for="modcurso" class="control-label">Cupo</label>
                <input type="text" disabled value="{{$cupo}}" class="form-control" id="cupo" name="cupo">
            </div>
            <div class="form-group col-md-3">
                <label for="modcurso" class="control-label">Cuota de Recuparación por Alumno</label>
                <input type="text" disabled value="$ {{$data->costo}}" class="form-control" id="costoinscr" name="costoinscr">
            </div>
            <div class="form-group col-md-5">
                <label for="clavecurso" class="control-label">Especialidad</label>
                <input class="form-control" name="perfil_instructor" disabled id="perfil_instructor" value="{{$data->espe}}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="inputfinicio" class="control-label">Fecha de Inicio</label>
                <input type="text" disabled value="{{$data->inicio}}" class="form-control" id="finicio" name="finicio">
            </div>
            <div class="form-group col-md-3">
                <label for="inputftermino" class="control-label">Fecha de Término</label>
                <input type="text" disabled value="{{$data->termino}}" class="form-control" id="ftermino" name="ftermino">
            </div>
            <div class="form-group col-md-4">
                <label for="inputefisico" class="control-label">Lugar de Impartición</label>
                <input type="text" disabled value="{{$data->efisico}}" class="form-control" id="efisico" name="efisico">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputfinicio" class="control-label">Días</label>
                <input type="text" disabled value="{{$data->dia}}" class="form-control" id="finicio" name="finicio">
            </div>
            <div class="form-group col-md-6">
                <label for="inputftermino" class="control-label">Horario</label>
                <input type="text" disabled value="{{$data->hini}} a {{$data->hfin}} HRS." class="form-control" id="ftermino" name="ftermino">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputfinicio" class="control-label">Perfil del Grupo</label>
                <input type="text" disabled value="{{$data->perfil}}" class="form-control" id="perfil" name="perfil">
            </div>
        </div>
        <hr style="border-color:dimgray">
        <h2>Datos del Contrato</h2>
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="numero_contrato" class="control-label">Número de Contrato</label>
            <input type="text" class="form-control" id="numero_contrato" name="numero_contrato" placeholder="Número de Contrato" value="{{$data->numero_contrato}}" disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="inputcantidad_numero" class="control-label">Monto Total de los Honorarios (En Numero)</label>
                <input type="text" class="form-control" id="cantidad_numero" name="cantidad_numero" value="{{$data->cantidad_numero}}" disabled>
            </div>
            <div class="form-group col-md-6">
                <label for="inputcantidad_letras" class="control-label">Monto Total de los Honorarios (En Letra)</label>
                <input type="text" class="form-control" id="cantidad_letras" name="cantidad_letras" value="{{$data->cantidad_letras1}}" disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="lugar_expedicion" class="control-label">Municipio de la Firma</label>
                <input type="text" class="form-control" id="lugar_expedicion" name="lugar_expedicion" placeholder="Lugar de Expedición" value="{{$data->municipio}}" disabled>
            </div>
            <div class="form-group col-md-3">
                <label for="fecha_firma" class="control-label">Fecha de Firma</label>
                <input type="date" class="form-control" id="fecha_firma" name="fecha_firma" value="{{$data->fecha_firma}}" disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="inputnombre_director" class="control-label">Nombre del Director/Encargado de Unidad de Capacitación</label>
                <input type="text" class="form-control" id="nombre_director" name="nombre_director" placeholder="Director de Unidad de Capacitación" value="{{$director->nombre}} {{$director->apellidoPaterno}} {{$director->apellidoMaterno}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label for="testigo_icatech" class="control-label">Unidad de Capacitación</label>
                <input type="text" class="form-control" id="unidad_capacitacion" name="unidad_capacitacion" value="{{$data->unidad_capacitacion}}" disabled>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <h2>Testigos</h2>
        <br>
        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="inputtestigo1" class="control-label">Nombre de Testigo de Departamento Académico</label>
                <input type="text" class="form-control" id="testigo1" name="testigo1" value="{{$testigo1->nombre}} {{$testigo1->apellidoPaterno}} {{$testigo1->apellidoMaterno}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label for="inputpuesto_testigo1" class="control-label">Puesto de Testigo</label>
                <input readonly type="text" class="form-control" id="puesto_testigo1" name="puesto_testigo1" value="{{$testigo1->puesto}}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="inputtestigo2" class="control-label">Nombre de Testigo del Departamento de Vinculación</label>
                <input type="text" class="form-control" id="testigo2" name="testigo2" value="{{$testigo2->nombre}} {{$testigo2->apellidoPaterno}} {{$testigo2->apellidoMaterno}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label for="inputpuesto_testigo2" class="control-label">Puesto del Testigo</label>
                <input readonly type="text" class="form-control" id="puesto_testigo2" name="puesto_testigo2" value="{{$testigo2->puesto}}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="inputtestigo2" class="control-label">Nombre de Testigo de la Delegación Administrativa</label>
                <input type="text" class="form-control" id="testigo3" name="testigo3" value="{{$testigo3->nombre}} {{$testigo3->apellidoPaterno}} {{$testigo3->apellidoMaterno}}" disabled>
            </div>
            <div class="form-group col-md-4">
                <label for="inputpuesto_testigo2" class="control-label">Puesto del Testigo</label>
                <input readonly type="text" class="form-control" id="puesto_testigo3" name="puesto_testigo3" value="{{$testigo3->puesto}}">
            </div>
        </div>
        <br>
        <div class="form-row" style="">
            <div class="form-group col-md-1">
                <button type="button" id="rechazarContrato" name="rechazarContrato" class="btn btn-danger">Rechazar</a>
            </div>
            <div class="form-group col-md-1">
                <a class="btn btn-success" id="verificar_contrato" name="verificar_contrato" data-toggle="modal" data-target="#validarContratoModel" data-id="{{$data->id_folios}}">Validar</a>
            </div>
        </div>
        <form method="POST" action="{{ route('contrato-rechazar') }}">
            @csrf
            <div id="rechazar_contrato" class="form-row d-none d-print-none">
                <div class="form-group col-md-6">
                    <label for="observaciones">Describa el motivo de rechazo</label>
                    <textarea name="observaciones" id="observaciones" cols="6" rows="6" class="form-control"></textarea>
                </div>
            </div>
            <div id="btn_rechazar" class="form-row d-none d-print-none">
                <div class="form-group col-md-3">
                    <button type="submit" class="btn btn-danger" >Confirmar Rechazo</button>
                    <input hidden id="idContrato" name="idContrato" value="{{$data->id_contrato}}">
                    <input hidden id="idfolios" name="idfolios" value="{{$data->id_folios}}">
                </div>
            </div>
        </form>
        <br>
        <input id="id_folio" name="id_folio" hidden value='{{$data->id_folios}}'>
        <input id="id_contrato" name="id_contrato" hidden value='{{$data->id_contrato}}'>
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                </div>
            </div>
        </div>
        <br>
    </div>

<!--Modal-->
    <div class="modal fade" id="validarContratoModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Validar Contrato</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <form action="{{ route('valcontrato') }}" id="validarForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <h3><b>¿ Estás seguro de validar el contrato?</b></h3>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="observaciones">Observaciones (Opcional)</label>
                                <textarea name="observaciones" id="observaciones" cols="6" rows="6" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                            <input type="hidden" name="id" id="id">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-success">Validar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!--Modal End-->
    <br>
    </section>
@stop
@section('script_content_js')
<script src="{{ asset("js/validate/modals.js") }}"></script>
<script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
@endsection
