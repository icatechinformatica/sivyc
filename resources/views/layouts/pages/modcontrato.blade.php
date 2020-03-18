@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Formulario de Contrato | Sivyc Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        <form action="{{ route('contrato-save') }}" method="post" id="registercontrato">
            @csrf
            <div style="text-align: right;width:65%">
                <label for="titulocontrato"><h1>Formulario de Contrato</h1></label>
            </div>
            <br><br>
            <div style="text-align: right;width:100%">
                <button type="button" id="mod_contrato" class="btn btn-warning btn-lg">Modificar Campos</button>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputobservacion" class="control-label"><b>Observaciones de Rechazo</b></label>
                    <textarea cols="4" rows="4" type="text" class="form-control" disabled aria-required="true" id="observacion" name="observacion">{{$datacon->observacion}}</textarea>
                </div>
            </div>
             <hr style="border-color:dimgray">
             <div class="form-row">
                 <div class="form-group col-md-6">
                    <label for="numero_contrato" class="control-label">Número de Contrato</label>
                    <input type="text" disabled class="form-control" id="numero_contrato" name="numero_contrato" value={{$datacon->numero_contrato}}>
                 </div>
             </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputnombre_curso" class="control-label">Nombre del Curso</label>
                    <input type="text"  disabled class="form-control" value="{{$data->nombre}}" id="nombre_curso" name="nombre_curso">
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
                    <label for="clavecurso" class="control-label">Area de Conocimiento del Instructor</label>
                    <select class="form-control" name="perfil_instructor" disabled id="perfil_instructor">
                        <option value={{$perfil_sel->id}}>{{$perfil_sel->especialidad}}</option>
                        @foreach ( $perfil_prof as $value )
                            <option value={{$value->id}}>{{$value->especialidad}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputcantidad_letras1" class="control-label">Monto Total de los Honorarios (En Letra)</label>
                    <input type="text" class="form-control" disabled id="cantidad_letras1" name="cantidad_letras1" value={{$datacon->cantidad_letras1}}>
                </div>
                <div class="form-group col-md-6">
                    <label for="inputcantidad_letras2" class="control-label">Monto Total de los Honorarios Asimilables a Salarios (En Letra)</label>
                    <input type="text" class="form-control" disabled id="cantidad_letras2" name="cantidad_letras2" value={{$datacon->cantidad_letras2}}>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="lugar_expedicion" class="control-label">Municipio de la Firma</label>
                    <input type="text" class="form-control" disabled id="lugar_expedicion" name="lugar_expedicion" value={{$datacon->municipio}}>
                </div>
                <div class="form-group col-md-3">
                    <label for="fecha_firma" class="control-label">Fecha de Firma</label>
                    <input type="date" class="form-control" disabled id="fecha_firma" name="fecha_firma" value={{$datacon->fecha_firma}}>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="directordeunidaddecapacitacion" class="control-label">Nombre del Director de Unidad de Capacitación</label>
                    <input type="text" class="form-control" disabled id="nombre_director" name="nombre_director" value={{$datacon->nombre_director}}>
                </div>
                <div class="form-group col-md-4">
                    <label for="testigo_icatech" class="control-label">Unidad de Capacitación</label>
                    <input type="text" class="form-control" disabled id="unidad_capacitacion" name="unidad_capacitacion" value={{$datacon->unidad_capacitacion}}>
                </div>
                <div class="form-group col-md-4">
                    <label for="testigo_icatech" class="control-label">No. de Circular Desginando Director</label>
                    <input type="text" class="form-control" disabled id="no_circulardir" name="no_circulardir" value={{$datacon->numero_circular}}>
                </div>
            </div>
            <hr style="border-color:dimgray">
            <h2>Testigos</h2>
            <br>
            <!-- START TESTIGOS -->
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label for="inputtestigo1" class="control-label">Nombre de Primer Testigo</label>
                        <input type="text" class="form-control" disabled id="testigo1" name="testigo1" value={{$datacon->testigo1}}>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_testigo1" class="control-label">Puesto de Primer Testigo</label>
                        <input type="text" class="form-control" disabled id="puesto_testigo1" name="puesto_testigo1" value={{$datacon->puesto_testigo1}}>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label for="inputtestigo2" class="control-label">Nombre de Segundo Testigo</label>
                        <input type="text" class="form-control" disabled id="testigo2" name="testigo2" value={{$datacon->testigo2}}>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_testigo2" class="control-label">Puesto de Segundo Testigo</label>
                        <input type="text" class="form-control" disabled id="puesto_testigo2" name="puesto_testigo2" value={{$datacon->puesto_testigo2}}>
                    </div>
                </div>
            <!-- END TESTIGOS-->
            <br>
            <input id="id_folio" name="id_folio" hidden value='{{$data->id_folios}}'>
            <input id="id_contrato" name="id_contrato" hidden value='{{$datacon->id_contrato}}'>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button id="save-contrato" type="submit" disabled class="btn btn-primary" >Guardar</button>
                    </div>
                </div>
            </div>
        </form>
        <br>
    </div>
@endsection
