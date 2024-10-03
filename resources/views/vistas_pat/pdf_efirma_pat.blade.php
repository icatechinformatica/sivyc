<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.formatos.hlayout_pat')
@section('title', 'PAT-ICATECH-002.1 | SIVyC Icatech')
@section('header')

@endsection
@section('body')
    <div>
        {!! $cadena_html_meta !!}
    </div>
    <div style="page-break-before: always;">
        @if (count($firmantes) > 0)
        <div style="font-size: 12px; margin-left: 42px;"><b>FOLIO: </b>{{$no_oficio}}</div>
        <div style="max-width: 100%; margin-right: 40px; margin-left: 40px; margin-top: 40px;">
                @foreach ($firmantes as $item)
                    <p style="font-size: 12px; font-weight:bold; margin-bottom: 0px;">NOMBRE DEL FIRMANTE</p>
                    <p style="font-size: 12px; margin-top: 0px;">{{ $item['nombre'] }}</p>
                    <p style="font-size: 12px; font-weight:bold; margin-bottom: 0px;">FIRMA ELECTRONICA</p>
                    <p style="font-size: 12px; margin-top: 0px;">{{ wordwrap( $item['firma'], 120, "\n", true) }}</p>
                    <p style="font-size: 12px; font-weight:bold; margin-bottom: 0px;">PUESTO</p>
                    <p style="font-size: 12px; margin-top: 0px;">{{ $item['puesto'] }}</p>
                    <p style="font-size: 12px; font-weight:bold; margin-bottom: 0px;">FECHA DE FIRMA</p>
                    <p style="font-size: 12px; margin-top: 0px;">{{ $item['fecha_firma'] }}</p>
                    <p style="font-size: 12px; font-weight:bold; margin-bottom: 0px;">NÚMERO DE SERIE</p>
                    <p style="font-size: 12px; margin-top: 0px;">{{ $item['serie'] }}</p>
                    <br><br>
                @endforeach
            </div>
            <div style="display: inline-block; width: 10%;">
                <img style="position: fixed; width: 16%; top: 70%; left: 78%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
            </div>
        @endif
    </div>

@endsection

@section('footer')
    @if(!empty($uuid))
        <div style="font-size:11px; text-align:justify; position: absolute; top:11%; margin-right: 20px; margin-left: 20px;">
            <span style="">Sello Digital: | GUID: {{$uuid}} | Sello: {{$cadena_sello}} | Fecha de emisión: {{$fecha_sello}} | Este documento ha sido Firmado Electrónicamente, teniendo el mismo valor que la firma autógrafa de acuerdo a los Artículos 1, 3, 8 y 11 de la Ley de Firma Electrónica Avanzada del Estado de Chiapas.</span>
        </div>
    @endif
@endsection
