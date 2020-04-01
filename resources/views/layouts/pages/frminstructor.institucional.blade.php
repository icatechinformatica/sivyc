<!--creado por Daniel Méndez-->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Instructor Contenido Institucional | Sivyc Icatech')
@section('content')
    <div class="container g-pt-50">
        <form action="{{ route('instructor-institucional-save') }}" method="POST" id="instructoresInstitucional">
            @csrf
            <div style="text-align: right;width:65%">
                <label for="titulocontrato"><h1>Datos Institucionales</h1></label>
            </div>
            <hr style="border-color:dimgray">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputnumero_control">Numero de Control</label>
                    <input id="numero_control" name="numero_control" type="text" disabled class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-5">
                    <label for="inputhonorario">Tipo de Honorario</label>
                    <select class="form-control" id="tipo_honorario" name="tipo_honorario">
                        <option value="sin especificar">Sin Especificar</option>
                        <option value="Interno">Interno</option>
                        <option value="De Honorarios">De Honorarios</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputregistro_agente">Registo Agente Capacitador Externo STPS</label>
                    <input id="registro_agente" name="registro_agente" type="text" class="form-control" aria-required="true">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputuncap_validacion">Unidad de Capacitacion que Solicita Validacion</label>
                    <input id="uncap_validacion" name="uncap_validacion" type="text" class="form-control " aria-required="true">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputmemo_validacion">Memorandum de Validacion</label>
                    <input id="memo_validacion" name="memo_validacion" type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputfecha_validacion">Fecha de Validación</label>
                    <input id="fecha_validacion" name="fecha_validacion" type="date" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputmemo_mod">Modificacion de Memorandum</label>
                    <input id="memo_mod" name="memo_mod" type="text" class="form-control" aria-required="true">
                </div>
            </div>
            <div class="form-row">
                <label for="inputobservacion">Observaciones</label>
                <textarea cols="6" rows="6" id="observacion" name="observacion" class="form-control"></textarea>
            </div>
            <br>
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
            <br>
            <input type="hidden" name="idInstructor" id="idInstructor" value="{{$id}}">
        </form>
    </div>
@endsection
