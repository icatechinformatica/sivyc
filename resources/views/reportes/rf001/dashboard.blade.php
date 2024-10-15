{{-- MODIFICADO DANIEL MÉNDEZ CRUZ - danielmendez.88@live.com --}}
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
@extends('theme.sivyc.layout')
@section('title', 'Grupos- Recibos de Pago | SIVyC Icatech')
@section('content')
    <div class="card-header">
        Validación de Ingresos Propios
    </div>
    <div class="container">
        <div class="card-group mt-4 mb-4">
            <div class="card text-center mr-5" style="height: 300px;">
                <div class="card-body">
                    <h3 class="card-title">Validación del ingreso propio</h3>
                    <p class="card-text">
                        <h5>
                            <b>Mostrar información sobre los ingresos de la unidad {{ $unidad }} para generar el concentrado.</b>
                        </h5>
                    </p>
                    <a href="{{ route('reporte.rf001.ingreso-propio') }}" class="btn">Ingresar</a>
                </div>
            </div>

            <div class="card text-center mr-5" style="height: 300px;">
                <div class="card-body" >
                    <h3 class="card-title">Concentrado de Unidad {{ $unidad }}</h3>
                    <p class="card-text">
                        <h5>
                            <b>Mostrar la Información dpel concentrado de ingresos propios y el seguimiento que se ha dado por parte de recursos financieros</b>
                        </h5>
                    </p>
                    <a href="{{ route('reporte.rf001.sent') }}" class="btn">Ingresar</a>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('script_content_js')
@endsection
