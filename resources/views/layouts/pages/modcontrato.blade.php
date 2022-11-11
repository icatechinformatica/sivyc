@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Modificación de Contrato | Sivyc Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        <form action="{{ route('contrato-savemod') }}" method="post" id="registercontrato"  enctype="multipart/form-data">
            @csrf
            <div style="text-align: right;width:65%">
                <label for="titulocontrato"><h1>Modificación de Contrato</h1></label>
            </div>
            <br><br>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputobservacion" class="control-label"><b>Observaciones de Rechazo</b></label>
                    <textarea cols="4" rows="4" type="text" class="form-control" readonly aria-required="true" id="observacion" name="observacion">{{$datacon->observacion}}</textarea>
                </div>
            </div>
            <hr style="border-color:dimgray">
            <div class="form-row">
                <div class="form-group col-md-6">
                <label for="numero_contrato" class="control-label">Número de Contrato</label>
                <input type="text" class="form-control" id="numero_contrato" name="numero_contrato" placeholder="Número de Contrato" value="{{$datacon->numero_contrato}}" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputnombre_curso" class="control-label">Nombre del Curso</label>
                    <input type="text"  class="form-control" value="{{$data->curso}}" id="nombre_curso" name="nombre_curso">
                </div>
                <div class="form-group col-md-4">
                    <label for="clavecurso" class="control-label">Clave del Curso</label>
                    <input type="text"  value={{$data->clave}} class="form-control" id="clavecurso" name="clavecurso">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputnombre_instructor" class="control-label">Nombre del Instructor</label>
                    <input type="text"  class="form-control" value="{{$nombrecompleto}}" id="nombre_instructor" name="nombre_instructor">
                </div>
                <div class="form-group col-md-4">
                    <label for="clavecurso" class="control-label">Area de Conocimiento del Instructor</label>
                    <input type="text" readonly  class="form-control" value="{{$perfil_sel->nombre_especialidad}}" id="perfnom" name="perfnom">
                    <input type="text" hidden  class="form-control" value="{{$perfil_sel->id}}" id="perfilinstructor" name="perfilinstructor">
                    {{-- <select class="form-control" name="perfilinstructor"  id="perfilinstructor">
                        <option value={{$perfil_sel->id}}>{{$perfil_sel->nombre_especialidad}}</option>
                        @foreach ( $perfil_prof as $value )
                            <option value={{$value->id_espins}}>{{$value->nombre_especialidad}}</option>
                        @endforeach
                    </select> --}}
                </div>
                <div class="form-group col-md-3">
                    <label for="clavecurso" class="control-label">Validación de instructor</label>
                    @if ($data->archivo_alta != NULL)
                        <a class="btn btn-info control-label" href={{$data->archivo_alta}} target="_blank">Validación de Instructor</a><br>
                    @else
                        <a class="btn btn-danger" disabled>Validación de Instructor</a><br>
                    @endif
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputcantidad_numero" class="control-label">Monto Total de los Honorarios (En Numero)</label>
                    <input type="text" class="form-control" id="cantidad_numero" name="cantidad_numero" value="{{$datacon->cantidad_numero}}" readonly >
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
                <div class="form-group col-md-3">
                    <label for="fecha_firma" class="control-label">Fecha de Firma</label>
                    <input type="date" class="form-control" id="fecha_firma" name="fecha_firma" value="{{$datacon->fecha_firma}}" >
                </div>
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
                    <select class="form-control" name="unidad_capacitacion"  id="unidad_capacitacion">
                        @if ($unidadsel != null)
                            <option value="{{$unidadsel->unidad}}">{{$unidadsel->unidad}}</option>
                        @else
                            <option value="">SELECCIONE UNIDAD</option>
                        @endif
                        @foreach ( $unidadlist as $cadwell )
                            <option value="{{$cadwell->unidad}}">{{$cadwell->unidad}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputfactura" class="control-label">Factura de Instructor o Anexo</label>
                    <input type="file" accept="application/pdf" id="factura" name="factura" class="form-control" placeholder="Archivo PDF">
                    <footer class="control-footer">Anexar documento de factura en caso de contar con ella</footer>
                </div>
                {{-- <div class="form-group col-md-3">
                    <label for="testigo_icatech" class="control-label">Tipo de Factura</label>
                    <select name="tipo_factura" class="form-control mr-sm-2" id="tipo_factura">
                        @if ($datacon->tipo_factura == 'NORMAL')
                            <option value="NORMAL" selected>NORMAL</option>
                            <option value="NUEVA">NUEVA</option>
                        @else
                            <option value="NORMAL">NORMAL</option>
                            <option value="NUEVA" selected>NUEVA</option>
                        @endif
                    </select>
                </div> --}}
            </div>
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
            <br>
            <input id="id_folio" name="id_folio" hidden value='{{$data->id_folios}}'>
            <input id="id_directorio" name="id_directorio" hidden value='{{$data_directorio->id}}'>
            <input id="id_contrato" name="id_contrato" hidden value='{{$datacon->id_contrato}}'>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" id="save-contrato" name="save-contrato" class="btn btn-primary" >Guardar</button>
                    </div>
                </div>
            </div>
        </form>
        <br>
    </div>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
<script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
@endsection
