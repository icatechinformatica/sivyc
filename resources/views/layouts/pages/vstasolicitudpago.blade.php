<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Formulario de Contrato | Sivyc Icatech')
@section('content')
    <div class="container g-pt-50">
        <form action="{{ route('save-doc') }}" method="post" id="registercontrato" enctype="multipart/form-data">
            @csrf
            <div style="text-align: right;width:62%">
                <label for="titulo"><h1>Solicitud de Pago</h1></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputno_contrato">Confirmacion de Numero de Contrato</label>
                    <input id="no_contrato" name="no_contrato" value="{{$datac->numero_contrato}}" disabled class="form-control">
                </div>
                <div class="form-group col-md-4">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputfolio">Confirmacion de Numero de Suficiencia</label>
                    <input id="no_suficiencia" name="no_suficiencia" value="{{$dataf->folio_validacion}}" disabled class="form-control">
                </div>
            </div>
            <hr style="border-color:dimgray">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputno_memo">Numero de Memorandum</label>
                    <input id="no_memo" name="no_memo" type="text" class="form-control">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputelaboro">Nombre de Quien Elabora</label>
                    <input id="elaboro" name="elaboro" type="text" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputnombre_para">Nombre del Destinatario</label>
                    <input id="nombre_para" name="nombre_para" type="text" class="form-control">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputpuesto_para">Puesto del Destinatario</label>
                    <input id="puesto_para" name="puesto_para" type="text" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="input doc_pdf" class="control-label"><h4>Documentación para Soporte de Pago</h4></label>
                    <input type="file" accept="application/pdf" class="form-control" id="doc_pdf" name="doc_pdf" placeholder="Archivo PDF">
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label class="text-justify"><h6>La extension del archivo debe ser PDF
                           <br>Se recomienda comprimir el pdf <a href='https://smallpdf.com/es/comprimir-pdf' target="blank">aqui</a>
                           <br>Peso maximo: 4 MB
                    </label></h6>
                </div>
            </div>
            <hr style="border-color:dimgray">
            <h2>Con Copia Para</h2>
            <br>
            <!-- START CCP -->
                <h3>CCP 1</h3>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputnombre_ccp1">Nombre</label>
                        <input id="nombre_ccp1" name="nombre_ccp1" type="text" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_para">Puesto</label>
                        <input id="puesto_ccp1" name="puesto_ccp1" type="text" class="form-control">
                    </div>
                </div>
                <h3>CCP 2</h3>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputnombre_ccp2">Nombre</label>
                        <input id="nombre_ccp2" name="nombre_ccp2" type="text" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_para">Puesto</label>
                        <input id="puesto_ccp2" name="puesto_ccp2" type="text" class="form-control">
                    </div>
                </div>
                <h3>CCP 3</h3>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputnombre_ccp3">Nombre</label>
                        <input id="nombre_ccp3" name="nombre_ccp3" type="text" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputpuesto_para">Puesto</label>
                        <input id="puesto_ccp3" name="puesto_ccp3" type="text" class="form-control">
                    </div>
                </div>
            <!-- END CC -->
            <input hidden id='id_folio' name="id_folio" value="{{$dataf->id_folios}}">
            <input hidden id='id_contrato' name="id_contrato" value="{{$datac->id_contrato}}">
            <div class="pull-left">
                <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
            </div>
            <div class="pull-right">
                <button type="submit" class="btn btn-primary" >Guardar</button>
            </div>
            <br>
        </form>
        <br>
    </div>
@endsection
