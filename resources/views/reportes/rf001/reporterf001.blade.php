{{-- Realizado por Jose Luis Morenoa Arcos --}}
@extends('theme.formatos.vlayout')
@section('title', 'Soporte de Entrega | SIVyC Icatech')

@section('content_script_css')
    <style>
        /* @page {margin: 0px 15px 15px 15px; } */
        .contenedor {
            margin-left: 1cm;
            margin-right: 1cm;
        }

        body {
            /* margin-top: 70px; */
            /* margin-bottom: -120px; */
            padding-top: 45px;
            padding-bottom: 60px;
            /* background-color: aqua; */
        }

        .bloque_uno {
            padding-top: 40px;
            font-weight: bold;
            font-size: 13px;
            font: bold;
        }

        .bloque_dos {
            font-weight: bold;
            font-size: 12px;
            font: bold;
        }

        .contenido {
            font-size: 13px;
            line-height: 1.5;
        }

        .delet_space_p {
            margin-top: 0px;
            margin-bottom: 0px;
        }

        .textofin {
            font-size: 10PX;
            font-style: italic;
        }

        .color_text {
            color: black;
        }

        .tablas {
            border-collapse: collapse;
            width: 100%;
        }

        .tablas tr {
            font-size: 9px;
            border: gray 1px solid;
            text-align: center;
            padding: 2px;
        }

        .tablas th {
            font-size: 10px;
            border: gray 1px solid;
            text-align: center;
            padding: 2px;
        }

        .colortexto {
            color: #0000;
        }

        .firmau {
            font-size: 12px;
            font: normal;
        }

        .contenedor {
            margin-left: 1cm;
            margin-right: 1cm;
        }

        .tablas {
            width: 100%
        }

        .tablas tr td {
            font-size: 11px;
            border: none;
            text-align: center;
            padding: 1px 1px;
        }

        .tablas th {
            font-size: 8px;
            text-align: center;
            padding: 1px 1px;
        }

        .tabla_con_border {
            border-collapse: collapse;
            font-size: 10px;
            font-family: sans-serif;
            width: 100%;
        }

        .tabla_con_border tr td {
            border: 1px solid #000000;
            word-wrap: break-word;
        }

        /* .tabla_con_border td {
                                                                                                                 page-break-inside: avoid;
                                                                                                             } */

        .tabla_con_border thead tr th {
            border: 1px solid #000000;
        }

        /* espaciado */
        center.espaciado {
            padding-top: 10px;
            font-size: 11px;
        }

        .celdaAsignado {
            word-break: break-all !important;
            white-space: nowrap !important;
            width: 40px !important;
        }

        .tablas_notas {
            border-collapse: collapse;
            width: 100%;
            page-break-before: auto;
        }

        .tablas_notas tr td {
            font-size: 9px;
            border: #000000 1.8px solid;
            text-align: justify;
            height: 2em;
        }

        .tablas_notas th {
            font-size: 9px;
            border: gray 1px solid;
            text-align: justify;
            padding: 1px 1px;
        }

        /* tablas para firmas */
        .tablaf {
            border-collapse: collapse;
            width: 100%;
            margin-top: 1em;
        }

        .tablaf tr td {
            font-size: 8px;
            text-align: center;
            padding: 0px 0px;
        }

        /* tabla para notas */
        .tablad {
            border-collapse: collapse;
            width: 100%;
            margin-top: 0.6em;
        }

        .tablad tr td {
            font-size: 8px;
            text-align: left;
            padding: 2px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
@endsection
@section('content')
    {!! $bodyMemo !!}
    @if (!is_null($uuid))
        <br>
        <div class="contenedor">
            <table style="width: 100%; font-size: 10px;">
                @foreach ($objeto['firmantes']['firmante'][0] as $key => $moist)
                    @if (\Str::contains($puestos[$key], $unidad) && \Str::startsWith($puestos[$key], 'DIRECT'))
                        <tr>
                            <td style="width: 10%; font-size: 9px;"><b>Nombre del firmante:</b></td>
                            <td style="width: 90%; font-size: 9px;">{{ $moist['_attributes']['nombre_firmante'] }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; font-size: 9px;"><b>Firma Electrónica:</b></td>
                            <td style="font-size: 9px;">
                                {{ wordwrap($moist['_attributes']['firma_firmante'], 87, "\n", true) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 9px;"><b>Puesto:</b></td>
                            <td style="font-size: 9px; height: 25px;">{{ $puestos[$key] }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 9px;"><b>Fecha de Firma:</b></td>
                            <td style="font-size: 9px;">{{ $moist['_attributes']['fecha_firmado_firmante'] }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 9px;"><b>Número de Serie:</b></td>
                            <td style="font-size: 9px;">{{ $moist['_attributes']['no_serie_firmante'] }}</td>
                        </tr>
                    @endif
                @endforeach
            </table>
            <div style="display: inline-block; width: 16%;">
                {{-- <img style="position: fixed; width: 100%; top: 55%; left: 80%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR"> --}}
                <img style="position: fixed; width: 16%; top: 60%; left: 77%"
                    src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
            </div>
        </div>

    @endif
    <div class="page-break"></div>
    @php
        $html_sin_saltos = str_replace(["\r", "\n"], '', $bodyRf001);
    @endphp
    {!! $html_sin_saltos !!}
    <br>
    @if (!is_null($uuid))
        <div class="contenedor">
            <table style="width: 100%; font-size: 10px;">
                @foreach ($objeto['firmantes']['firmante'][0] as $key => $moist)
                    <tr>
                        <td style="width: 10%; font-size: 9px;"><b>Nombre del firmante:</b></td>
                        <td style="width: 90%; font-size: 9px;">{{ $moist['_attributes']['nombre_firmante'] }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; font-size: 9px;"><b>Firma Electrónica:</b></td>
                        <td style="font-size: 9px;">
                            {{ wordwrap($moist['_attributes']['firma_firmante'], 87, "\n", true) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 9px;"><b>Puesto:</b></td>
                        <td style="font-size: 9px; height: 25px;">{{ $puestos[$key] }}</td>
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
            <div style="display: inline-block; width: 16%;">
                {{-- <img style="position: fixed; width: 100%; top: 55%; left: 80%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR"> --}}
                <img style="position: fixed; width: 16%; top: 60%; left: 77%"
                    src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
            </div>
        </div>
    @endif
@endsection

@section('script_content_js')
@endsection
