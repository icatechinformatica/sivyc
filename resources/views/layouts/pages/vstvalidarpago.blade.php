<!-- Creado por Daniel Méndez-->
@extends('theme.sivyc.layout')

@section('title', 'Validar Pago| Sivyc Icatech')

@section('content')
    <section class="container g-pt-50">
        <form method="POST" action="{{ route('supre-rechazo') }}" id="rechazosupre">
            @csrf
                <div class="text-center">
                    <h1>Validar Pagos</h1>
                </div>
                <br>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="dropno_memo">N°. de Contrato</label>
                    <input name="numero_contrato" id="numero_contrato" type="text" disabled value="{{$contratos->numero_contrato}}" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="dropfecha_memo">Cantidad</label>
                    <input name="cantidad_letras1" id="cantidad_letras1" type="text" disabled value="{{$contratos->cantidad_letras1}}" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="numero_circular">N°. de Circular</label>
                        <input name="numero_circular" id="numero_circular" type="text" disabled value="{{$contratos->numero_circular}}"  class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="unidad_capacitacion">Unidad de Capacitación</label>
                        <input name="unidad_capacitacion" id="unidad_capacitacion" type="text" disabled value="{{$contratos->unidad_capacitacion}}" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="nombre_director">Nombre del Director de Unidad</label>
                        <input name="nombre_director" id="nombre_director" type="text" disabled value="{{$contratos->nombre_director}}" class="form-control">
                    </div>
                </div>
                <br>
                <div class="form-row" style="">
                    <div class="form-group col-md-1">
                        <button type="button" id="valsupre_rechazar" name="valsupre_rechazar" class="btn btn-danger">Rechazar</a>
                    </div>
                    <div class="form-group col-md-1">
                        <button type="button" id="valsupre_validar" name="valsupre_validar" class="btn btn-success">Validar</a>
                    </div>
                </div>
                <div id="divrechazar" class="form-row d-none d-print-none">
                    <div class="form-group col-md-6">
                        <label for="inputcomentario_rechazo">Describa el motivo de rechazo</label>
                        <textarea name="comentario_rechazo" id="comentario_rechazo" cols="6" rows="6" class="form-control"></textarea>
                    </div>
                </div>
                <div id="divconf_rechazar" class="form-row d-none d-print-none">
                    <div class="form-group col-md-3">
                        <button type="submit" class="btn btn-danger" >Confirmar Rechazo</button>
                        <input hidden id="id" name="id" value="{{$contratos->id_contrato}}">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <a class="btn btn-warning" href="{{URL::previous()}}">Regresar</a>
                        </div>
                    </div>
                </div>
                <br>
        </form>
        <form method="POST" action="{{ route('supre-validado') }}" id="validadosupre">
            @csrf
                <hr style="border-color:dimgray">
                <div id="div1" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <label for="inputfolio_validacion">Folio de Validación</label>
                        <input name="folio_validacion" id="folio_validacion" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="inputfecha_validacion">Fecha de Validación</label>
                        <input name="fecha_validacion" id="fecha_validacion" type="date" class="form-control">
                    </div>
                </div>
                <div id="div2" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <label for="inputnombre_firmante">Nombre del Firmante</label>
                        <input name="nombre_firmante" id="nombre_firmante" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_firmante">Puesto de firmante</label>
                        <input name="puesto_firmante" id="puesto_firmante" type="text" class="form-control">
                    </div>
                </div>
                <hr style="border-color:dimgray">
                <div id="div3" class="d-none d-print-none">
                    <h3>Con Copia Para:</h3>
                </div>
            <!-- START CCP -->
                <div id="div4" class="form-row d-none d-print-none" >
                    <div class="form-group col-md-4">
                        <input name="ccp1" id="ccp1" class="form-control" placeholder="Nombre Completo">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa1" id="ccpa1" class="form-control" placeholder="Puesto">
                    </div>
                </div>
                <div id="div5" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <input name="ccp2" id="ccp2" class="form-control" placeholder="Nombre Completo">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa2" id="ccpa2" class="form-control" placeholder="Puesto">
                    </div>
                </div>
                <div id="div6" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <input name="ccp3" id="ccp3" class="form-control" placeholder="Nombre Completo">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa3" id="ccpa3" class="form-control" placeholder="Puesto">
                    </div>
                </div>
                <div id="div7" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <input name="ccp4" id="ccp4" class="form-control" placeholder="Nombre Completo">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa4" id="ccpa4" class="form-control" placeholder="puesto">
                    </div>
                </div>
            <!--END CCP-->
                <div id="confval" class="row d-none d-print-none">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-success" >Confirmar Validación</button>
                            <input hidden id="id" name="id" value="{{$contratos->id_contrato}}">
                        </div>
                    </div>
                </div>
                <br>
        </form>
    </section>
@stop
