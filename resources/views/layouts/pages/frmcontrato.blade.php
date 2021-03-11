@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Formulario de Contrato | Sivyc Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        <form action="{{ route('contrato-save') }}" method="post" id="registercontrato" enctype="multipart/form-data">
            @csrf
            <div style="text-align: right;width:65%">
                <label for="titulocontrato"><h1>Formulario de Contrato</h1></label>
            </div>
             <hr style="border-color:dimgray">
             <div class="form-row">
                 <div class="form-group col-md-6">
                    <label for="numero_contrato" class="control-label">Número de Contrato</label>
                    <input type="text" class="form-control" id="numero_contrato" name="numero_contrato" placeholder="Número de Contrato">
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
                <div class="form-group col-md-5">
                    <label for="inputnombre_instructor" class="control-label">Nombre del Instructor</label>
                    <input type="text" disabled class="form-control" value="{{$nombrecompleto}}" id="nombre_instructor" name="nombre_instructor">
                </div>
                <div class="form-group col-md-4">
                    <label for="clavecurso" class="control-label">Especialidad de Conocimiento del Instructor</label>
                    <select class="form-control" name="perfil_instructor" id="perfil_instructor">
                        <option value="sin especificar">Sin Especificar</option>
                        @foreach ( $perfil_prof as $value )
                            <option value={{$value->id_espins}}>{{$value->nombre_especialidad}}</option>
                        @endforeach
                    </select>
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
                    <input type="text" class="form-control" id="lugar_expedicion" name="lugar_expedicion" placeholder="Lugar de Expedición">
                </div>
                <div class="form-group col-md-3">
                    <label for="fecha_firma" class="control-label">Fecha de Firma</label>
                    <input type="date" class="form-control" id="fecha_firma" name="fecha_firma">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputnombre_director" class="control-label">Nombre del Director/Encargado de Unidad de Capacitación</label>
                    <input type="text" class="form-control" id="nombre_director" name="nombre_director" placeholder="Director de Unidad de Capacitación">
                    <input type="text" class="form-control" id="id_director" name="id_director" hidden>
                </div>
                <div class="form-group col-md-4">
                    <label for="testigo_icatech" class="control-label">Unidad de Capacitación</label>
                    <select name="unidad_capacitacion" class="form-control mr-sm-2" id="unidad_capacitacion">
                        <option value="">SELECCIONE UNIDAD</option>
                        @foreach ($unidades as $cadwell)
                            <option value="{{$cadwell->unidad}}">{{$cadwell->unidad}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputfactura" class="control-label">Factura de Instructor o Anexo</label>
                    <input type="file" accept="application/pdf" id="factura" name="factura" class="form-control" placeholder="Archivo PDF">
                    @if ($term == TRUE)
                        <footer style="color:red;" class="control-footer">La fecha de termino del curso ha sido alcanzada. Anexar documento de factura en caso de contar con ella</footer>
                    @else
                        <footer class="control-footer">Anexar documento de factura en caso de contar con ella</footer>
                    @endif
                </div>
            </div>
            <hr style="border-color:dimgray">
            <h2>Testigos</h2>
            <br>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputtestigo1" class="control-label">Nombre de Testigo de Departamento Académico</label>
                    <input type="text" class="form-control" id="testigo1" name="testigo1">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo1" class="control-label">Puesto de Testigo</label>
                    <input readonly type="text" class="form-control" id="puesto_testigo1" name="puesto_testigo1">
                    <input type="text" name="id_testigo1" id="id_testigo1" hidden>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputtestigo2" class="control-label">Nombre de Testigo del Departamento de Vinculación</label>
                    <input type="text" class="form-control" id="testigo2" name="testigo2" h>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo2" class="control-label">Puesto del Testigo</label>
                    <input readonly type="text" class="form-control" id="puesto_testigo2" name="puesto_testigo2">
                    <input type="text" name="id_testigo2" id="id_testigo2" hidden>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputtestigo2" class="control-label">Nombre de Testigo de la Delegación Administrativa</label>
                    <input type="text" class="form-control" id="testigo3" name="testigo3">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo2" class="control-label">Puesto del Testigo</label>
                    <input readonly type="text" class="form-control" id="puesto_testigo3" name="puesto_testigo3">
                    <input type="text" name="id_testigo3" id="id_testigo3" hidden>
                </div>
            </div>
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
        </form>
        <br>
    </div>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
@endsection
