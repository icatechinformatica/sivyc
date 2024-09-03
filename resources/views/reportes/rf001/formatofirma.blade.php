@extends('theme.sivyc.layout')
@section('content_script_css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <style>
        /* Estilo personalizado para el interruptor */
        .custom-switch .custom-control-label::before {
            width: 28px;
            /* Ajusta el ancho del interruptor */
            height: 17;
            /* Ajusta la altura del interruptor */
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }

        .filter-form {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .filter-form input,
        .filter-form select {
            padding: 5px;
            margin-right: 10px;
        }

        .no-records {
            text-align: center;
            padding: 20px;
            background-color: #f0f0f0;
            margin-bottom: 20px;
        }

        .new-form {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .new-form input,
        .new-form select {
            margin-right: 10px;
            padding: 10px;
            box-sizing: border-box;
        }

        .new-form button {
            padding: 10px 20px;
        }

        a {
            color: inherit;
            /* Hereda el color del elemento padre */
            text-decoration: none;
            /* Elimina el subrayado */
        }

        a:hover {
            color: #f2f2f2;
            /* Cambia el color al pasar el ratón por encima */
            text-decoration: underline;
            /* Subraya el enlace al pasar el ratón por encima */
        }

        @media (max-width: 768px) {

            .filter-form,
            .new-form {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-form input,
            .filter-form select,
            .filter-form button,
            .new-form input,
            .new-form select,
            .new-form button {
                margin-right: 0;
            }
        }
    </style>
@endsection
@section('title', 'Formatos Rf001 enviados a revisión | SIVyC Icatech')
@php
    $bandera = Crypt::encrypt($solicitud);
    $encrypted = base64_encode($bandera);
    $encrypted = str_replace(['+', '/', '='], ['-', '_', ''], $encrypted);
@endphp
@section('content')
    <div class="card-header">
        <a href="{{ route('reporte.rf001.sent', ['generado' => $encrypted]) }}">INDICE </a> / EN ESPERA DE FIRMA FORMATO RF001 {{ $idFormato }}
    </div>
    <div class="card card-body  p-5" style=" min-height:450px;">
        @if (session('message'))
            <div class="alert alert-success" role="alert">
                {{ session('message') }}
            </div>
        @endif
        <div class="col-12" style="margin-bottom: 5px;">
            {{ Form::open(['route' => 'reporte.rf001.index', 'method' => 'get', 'id' => 'frm', 'enctype' => 'multipart/form-data', 'target' => '_self']) }}
            <div class="form-row">
                <div class="form-group col-md-4 pt-4">
                    <h2><b>ICATECH/2024/102/1B</b></h2>
                </div>
                <div class="form-group col-md-4 pt-4">
                    {{ Form::submit('FILTRAR', ['id' => 'filtrar', 'class' => 'btn mr-5 mt-1']) }}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="col-12">
            <div class="p-0 m-0">
                {{ Form::open(['route' => 'reporte.rf001.store', 'method' => 'POST', 'id' => 'frmString']) }}

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
