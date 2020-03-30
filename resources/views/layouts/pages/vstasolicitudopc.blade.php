<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Formulario de Contrato | Sivyc Icatech')
@section('content')
    <div class="container g-pt-50">
        <form action="{{ route('save-doc') }}" method="post" id="registercontrato" enctype="multipart/form-data">
            @csrf
            <div style="text-align: right;width:50%">
                <label for="titulo"><h1>Inicio SUPRE</h1></label>
            </div>
            <hr style="border-color:dimgray">
            <br>
            <div class="form-row">
                <div class="form-group col-md-2">
                </div>
                <div class="form-group col-md-4">
                    <a class="btn btn-info btn-lg" href="{{route('supre-inicio')}}">Consulta de SUPRE</a>
                </div>
                <div class="form-group col-md-4">
                    <a class="btn btn-info btn-lg" href="{{route('solicitud-folio')}}">Consulta de Folios</a>
                </div>
                <div class="form-group col-md-2">
                </div>
            </div>
            <br>
            <div class="pull-center">
                <a class="btn btn-danger" href="{{URL::previous()}}">Regresar</a>
            </div>
            <br>
        </form>
        <br>
    </div>
@endsection
