@extends('theme.formatos.hlayout2025')
@section('title', 'VALIDACIÓN DE SUFICIENCIA PRESUPUESTAL | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style type="text/css">

        #wrappertop {
            margin-top: -2%;
            background-position: 5px 10px;
            background-repeat: no-repeat;
            background-size: 32px;
            width: 100%;
            line-height: 60%;
            font-size: 16px;
            padding: 0px;
            border: 1px solid transparent;
            margin-bottom: 0px;
        }
        #wrapperbot {
            background-position: 5px 10px;
            background-repeat: no-repeat;
            background-size: 32px;
            width: 100%;
            line-height: 70%;
            font-size: 16px;
            padding: 0px 0px 0px 0px;
            border: 1px solid transparent;
            margin-bottom: 0px;
        }

        div.a {
            text-align: center;
        }

        div.b {
            text-align: left;
        }

        div.c {
            text-align: right;
        }

        div.d {
            text-align: justify;
        }
    </style>
@endsection
@section('content')
    <div style="padding-top: 2%; font-size: 5px;">
        {!!$body_html!!}
        @if(!is_null($uuid))
            <br><div style="display: inline-block; width: 85%;">
            <table style="width: 100%; font-size: 9px;">
                @foreach ($objeto['firmantes']['firmante'][0] as $key=>$moist)
                    <tr>
                        <td style="width: 10%; font-size: 9px;"><b>Nombre del firmante:</b></td>
                        <td style="width: 90%; font-size: 9px;">{{ $moist['_attributes']['nombre_firmante'] }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; font-size: 9px;"><b>Firma Electrónica:</b></td>
                        <td style="font-size: 9px;">{{ wordwrap($moist['_attributes']['firma_firmante'], 110, "\n", true) }}</td>
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
            <img style="position: fixed; width: 13%; top: 57%; left: 77%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
        </div>
    @else
        <div align=center style="font-size: 8px;">
            <small><small>C. {{$funcionarios['remitente']}}</small></small>
            <br><small>________________________________________</small><br/>
            <br><small><small>{{$funcionarios['remitentep']}}</small></small></b>
        </div>
        <br><br><br><br><br><br>
    @endif
        {!!$ccp_html!!}
    </div>
@endsection
@section('script_content_js')
@endsection

