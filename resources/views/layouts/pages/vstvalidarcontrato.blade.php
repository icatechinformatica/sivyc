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
        <div class="form-row">
            <div class="form-group col-md-6">
            <label for="numero_contrato" class="control-label">Número de Contrato</label>
            <input type="text" class="form-control" id="numero_contrato" name="numero_contrato" placeholder="Número de Contrato" value="{{$data->numero_contrato}}" disabled>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="inputnombre_curso" class="control-label">Nombre del Curso</label>
                <input type="text" disabled class="form-control" value="{{$data->nombre}}" id="nombre_curso" name="nombre_curso">
            </div>
            <div class="form-group col-md-4">
                <label for="clavecurso" class="control-label">Clave del Curso</label>
                <input type="text" disabled value={{$data->clave}} class="form-control" id="clavecurso" name="clavecurso">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="inputnombre_instructor" class="control-label">Nombre del Instructor</label>
                <input type="text" disabled class="form-control" value="{{$data->insnom}} {{$data->apellidoPaterno}} {{$data->apellidoMaterno}}" id="nombre_instructor" name="nombre_instructor">
            </div>
            <div class="form-group col-md-4">
                <label for="clavecurso" class="control-label">Area de Conocimiento del Instructor</label>
                <input class="form-control" name="perfil_instructor" disabled id="perfil_instructor" value="{{$data->especialidad}}">
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
                    <div class="modal-body">
                        ¿ Estás seguro de validar el contrato?
                    </div>
                    <div class="modal-footer">
                        <form action="" id="validarForm" method="get">
                            @csrf
                            <input type="hidden" name="id">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-success">Validar</button>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        <!--Modal End-->
        <br>

    </section>
@stop
