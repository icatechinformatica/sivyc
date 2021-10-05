<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')

@section('title', 'Historial de Validación| Sivyc Icatech')

@section('content')
    <div class="container g-pt-50">
        @if ($message =  Session::get('info'))
            <div class="alert alert-info alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <div style="text-align: right;width:65%">
            <label for="titulocontrato"><h1>Historial Validación de Contrato</h1></label>
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
            @if ($data->arch_factura != NULL)
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
        </div>
        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="inputnombre_instructor" class="control-label">Nombre del Instructor</label>
                <input type="text" disabled class="form-control" value="{{$data->insnom}} {{$data->apellidoPaterno}} {{$data->apellidoMaterno}}" id="nombre_instructor" name="nombre_instructor">
            </div>
            <div class="form-group col-md-5">
                <label for="clavecurso" class="control-label">Especialidad</label>
                <input class="form-control" name="perfil_instructor" disabled id="perfil_instructor" value="{{$data->especialidad}}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="clavecurso" class="control-label">Memorándum de Validación</label>
                <input class="form-control" name="memo_validacion" disabled id="memo_validacion" value="{{$data->memorandum_validacion}}">
            </div>
            <div class="form-group col-md-3">
                <label for="clavecurso" class="control-label">Tipo Honorario</label>
                <input class="form-control" name="memo_validacion" disabled id="memo_validacion" value="{{$data->tipo_honorario}}">
            </div>
        </div>
        <hr style="border-color:dimgray">
        <h2>Datos del Curso</h2>
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
                <input class="form-control" name="perfil_instructor" disabled id="perfil_instructor" value="{{$data->especialidad}}">
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
        @if ($data->status == 'Contrato_Rechazado')
            <br>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="observaciones">Motivo de Rechazo</label>
                <textarea name="observaciones" id="observaciones" cols="6" rows="6" class="form-control" disabled>{{$data->observacion}}</textarea>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                </div>
            </div>
        </div>
        <br>
    </div>
</section>
@stop
