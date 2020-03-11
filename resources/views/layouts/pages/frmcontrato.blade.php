@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Formulario de Contrato | Sivyc Icatech')
<!--seccion-->
@section('content')
    <div class="container g-pt-50">
        <form method="POST">
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
                    <input type="text" disabled class="form-control" value="{{$nombrecompleto}}" id="nombre_instructor" name="nombre_instructor">
                </div>
                <div class="form-group col-md-4">
                    <label for="clavecurso" class="control-label">Area de Conocimiento del Instructor</label>
                    <select class="form-control" name="perfil_instructor" id="perfil_instructor">
                        <option value="sin especificar">Sin Especificar</option>
                        @foreach ( $perfil_prof as $value )
                            <option value={{$value->id}}>{{$value->especialidad}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputcantidad_letras1" class="control-label">Monto Total de los Honorarios (En Letra)</label>
                    <input type="text" class="form-control" id="cantidad_letras1" name="cantidad_letras1">
                </div>
                <div class="form-group col-md-6">
                    <label for="inputcantidad_letras2" class="control-label">Monto Total de los Honorarios Asimilables a Salarios (En Letra)</label>
                    <input type="text" class="form-control" id="cantidad_letras2" name="cantidad_letras2">
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
                <div class="form-group col-md-4">
                    <label for="directordeunidaddecapacitacion" class="control-label">Nombre del Director de Unidad de Capacitación</label>
                    <input type="text" class="form-control" id="nombre_director" name="nombre_director" placeholder="Director de Unidad de Capacitación">
                </div>
                <div class="form-group col-md-4">
                    <label for="testigo_icatech" class="control-label">Unidad de Capacitación</label>
                    <input type="text" class="form-control" id="unidad_capacitacion" name="unidad_capacitacion">
                </div>
                <div class="form-group col-md-4">
                    <label for="testigo_icatech" class="control-label">No. de Circular Desginando Director</label>
                    <input type="text" class="form-control" id="no_circulardir" name="no_circulardir">
                </div>
            </div>
            <hr style="border-color:dimgray">
            <h2>Testigos</h2>
            <br>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputtestigo1" class="control-label">Nombre de Primer Testigo</label>
                    <input type="text" class="form-control" id="testigo1" name="testigo1">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo1" class="control-label">Puesto de Primer Testigo</label>
                    <input type="text" class="form-control" id="puesto_testigo1" name="puesto_testigo1">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputtestigo2" class="control-label">Nombre de Segundo Testigo</label>
                    <input type="text" class="form-control" id="testigo2" name="testigo2">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_testigo2" class="control-label">Puesto de Segundo Testigo</label>
                    <input type="text" class="form-control" id="puesto_testigo2" name="puesto_testigo2">
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
