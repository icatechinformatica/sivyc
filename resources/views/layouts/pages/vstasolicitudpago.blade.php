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
            <div class="form-row" style="text-align: center;">
                <div class="form-gorup col-md-4">
                </div>
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
