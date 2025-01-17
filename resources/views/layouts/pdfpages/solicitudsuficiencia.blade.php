@extends('theme.formatos.hlayout2025')
@section('title', 'SOLICITUD DE SUFICIENCIA PRESUPUESTAL | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        /* div.content
        {
            margin-top: 60%;
            margin-bottom: 70%;
            margin-right: 25%;
            margin-left: 0%;
        } */
    </style>
@endsection
@section('content')
    <div>
        {!!$bodyTabla!!}
        @if(!is_null($uuid))
            <br><br><div style="display: inline-block; width: 85%;">
                <p style="text-align: left; font-size: 9px;"><b>SOLICITA</b></p>
                <table style="width: 100%; font-size: 8px;">
                    @foreach ($objeto['firmantes']['firmante'][0] as $key=>$moist)
                        <tr>
                            <td style="width: 10%; font-size: 9px;"><b>Nombre del firmante:</b></td>
                            <td style="width: 90%; font-size: 9px;">{{ $moist['_attributes']['nombre_firmante'] }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; font-size: 9px;"><b>Firma Electrónica:</b></td>
                            <td style="font-size: 9px;">{{ wordwrap($moist['_attributes']['firma_firmante'], 43, "\n", true) }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 9px;"><b>Puesto:</b></td>
                            <td style="font-size: 9px; height: 25px;">{{$puestos[$key]}}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 9px;"><b>Fecha de Firma:</b></td>
                            <td style="font-size: 9px;">{{ $moist['_attributes']['fecha_firmado_firmante'] }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 9px;"><b>Número de Serie:</b></td>
                            <td style="font-size: 9px;">{{ $moist['_attributes']['no_serie_firmante'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <div style="display: inline-block; width: 15%;">
                {{-- <img style="position: fixed; width: 100%; top: 55%; left: 80%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR"> --}}
                <img style="position: fixed; width: 15%; top: 45%; left: 75%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
            </div>
        @else
            <div align=center> <b>SOLICITA
                <br><br>
                <br><small>C. {{$funcionarios['director']}}.</small>
                <br><small>{{$funcionarios['directorp']}}.</small>
            </div>
        @endif
    </div>
@endsection
@section('script_content_js')
@endsection
