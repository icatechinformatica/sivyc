<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Curso Validado para Impartir| Sivyc Icatech')
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <form>
            @csrf
                <div class="text-center">
                    <h1>Validacion de Suficiencia Presupuestal</h1>
                </div>
                <br>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="dropno_memo">Numero de Memorandum</label>
                        <input name="no_memo" id="no_memo" type="text" disabled class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="dropfecha_memo">Fecha de Memorandum</label>
                        <input name="fecha_memo" id="fecha_memo" type="date" disabled class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="drouni_cap">Unidad de Capacitación</label>
                        <input name="uni_cap" id="uni_cap" type="text" disabled class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="droparea">Area de Adscripcion</label>
                        <input name="area" id="area" type="text" disabled class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="dropnombre_dir">Nombre del Director de Unidad</label>
                        <input name="nombre_dir" id="nombre_dir" type="text" disabled class="form-control">
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
        <form>
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
                        <input name="ccpa1" id="ccpa1" class="form-control" placeholder="Area">
                    </div>
                </div>
                <div id="div5" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <input name="ccp2" id="ccp2" class="form-control" placeholder="Nombre Completo">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa2" id="ccpa2" class="form-control" placeholder="Area">
                    </div>
                </div>
                <div id="div6" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <input name="ccp3" id="ccp3" class="form-control" placeholder="Nombre Completo">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa3" id="ccpa3" class="form-control" placeholder="Area">
                    </div>
                </div>
                <div id="div7" class="form-row d-none d-print-none">
                    <div class="form-group col-md-4">
                        <input name="ccp4" id="ccp4" class="form-control" placeholder="Nombre Completo">
                    </div>
                    <div class="form-group col-md-4">
                        <input name="ccpa4" id="ccpa4" class="form-control" placeholder="Area">
                    </div>
                </div>
            <!--END CCP-->
                <div id="confval" class="row d-none d-print-none">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-success" >Confirmar Validación</button>
                        </div>
                    </div>
                </div>
                <br>
        </form>
    </section>
@stop
