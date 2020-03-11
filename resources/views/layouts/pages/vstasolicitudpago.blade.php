<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Formulario de Contrato | Sivyc Icatech')
@section('content')
    <div class="container g-pt-50">
        <form method="POST">
            <div style="text-align: right;width:60%">
                <label for="titulo"><h1>Solicitud de Pago</h1></label>
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
                    </h6></label>
                </div>
            </div>
        </form>
        <br>
    </div>
@endsection
