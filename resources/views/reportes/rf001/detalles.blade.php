<!--ELABORO ROMELIA PEREZ - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('content_script_css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <style>
        .custom-file-label::after {
            content: "Examinar";
        }

        .fixed-width-label {
            max-width: 200px;
            /* Adjust the value as needed */
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            margin-bottom: -1px;
        }
    </style>
@endsection
@php
    $movimiento = json_decode($getConcentrado->movimientos, true);
    $uploadFiles = json_decode($getConcentrado->archivos, true);
    $importeTotal = 0;
@endphp
@section('title', 'Detalles del formato RF001 con ' . $getConcentrado->memorandum . '| SIVyC Icatech')
@section('content')
    <div class="card-header">
        Grupos / Recibos de Pago
    </div>
    <div class="card card-body">
        {{ Form::open(['method' => 'post', 'id' => 'frm', 'enctype' => 'multipart/form-data']) }}
        @csrf
        <div class="row form-inline">
            <div class="form-group col-md-6">
                <h4><b>REGRESAR</b></h4>
            </div>
            <div class="form-group col-md-6 justify-content-end ">
                <h4 class="bg-light p-2">
                    &nbsp; <b>Memorándum</b> &nbsp;
                    <span class="bg-white p-1">
                        <b class="text-danger">
                            {{ $getConcentrado->memorandum }}
                        </b>&nbsp;
                    </span>
                    &nbsp;
                </h4>
                @switch($getConcentrado->estado)
                    @case('ENVIADO')
                        <h4 class="text-center text-white p-2" style="background-color: #33A731;">
                            &nbsp; {{ $getConcentrado->estado }} &nbsp;
                        </h4>
                    @break

                    @case('CONCENTRADO')
                        <h4 class="bg-warning text-center p-2">&nbsp;{{ $getConcentrado->estado }} &nbsp;</h4>
                    @break

                    @case('CANCELADO')
                        <h4 class="text-center text-white bg-danger p-2">&nbsp;{{ $getConcentrado->estado }} &nbsp;</h4>
                    @break

                    @default
                @endswitch

                <a class="nav-link pt-0" href="" target="_blank">
                    <i class="far fa-file-pdf  fa-3x text-danger" title='DESCARGAR RECIBO DE PAGO OFICIALIZADO.'></i>
                </a>
            </div>
        </div>

        <div class="row bg-light" style="padding:35px; line-height: 1.5em;">
            <div class="form-group col-md-6"> FOLIO GRUPO: <b>321321</b></div>
            <div class="form-group col-md-6">
                UNIDAD/ACCIÓN MÓVIL:
                <b>DATOS</b>
            </div>
            <div class="form-group col-md-6">CURSO: <b>CURSO</b></div>
            <div class="form-group col-md-6">
                CLAVE:
                FDSKLJSDF <b class="text-danger">ESTADO &nbsp;</b> <b>12143 &nbsp;</b>
                ESTATUS: <b class="text-danger"> DATOSNUEVOS </b>
            </div>
            <div class="form-group col-md-6">INSTRUCTOR: <b>DLSK</b></div>
            <div class="form-group col-md-6">ARC-01: <b>DSADSA</b></div>
            <div class="form-group col-md-6">TOTAL BENEFICIADOS: <b>ADSDSA</b></div>
            <div class="form-group col-md-6">FECHAS: <b>DSADSADSA AL DASDSADSADS</b></div>
            <div class="form-group col-md-6">TOTAL CUOTA DE RECUPERACIÓN: <b>$ 2311323</b></div>
            <div class="form-group col-md-6">HORARIO: <b>DE SADDSA A DSADSASDA </b></div>
            <div class="form-group col-md-6">ESTATUS RECIBO: <b>SADSADDSA</b></div>
            <div class="form-group col-md-6">TIPO DE PAGO: <b>ASDDSADSAASD</b></div>
        </div>

        <div class="row w-100 form-inline justify-content-end mt-4">
            <h5 class="bg-light p-2">
                RECIBO No.
                <span class="bg-white p-1">&nbsp;<b>DATA</b> <b class="text-danger">NEW DATA</b>&nbsp;</span>
            </h5>

            <a class="nav-link pt-0" target="_blank">
                <i class="far fa-file-pdf  fa-3x text-danger" title='DESCARGAR RECIBO DE PAGO OFICIALIZADO.'></i>
            </a>


            <div class="custom-file col-md-2" id="inputFile" style="display:none">
                <input id="file_recibo" type="file" name="file_recibo" class="custom-file-input" accept=".pdf">
                <label class="custom-file-label" for="file_recibo">&nbsp;&nbsp;</label>
            </div>

        </div>
        {!! Form::close() !!}
    </div>
    @section('script_content_js')
    @endsection
@endsection
