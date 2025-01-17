@extends('theme.formatos.vlayout2025')
@section('title', 'SOLICITUD DE SUFICIENCIA PRESUPUESTAL | SIVyC Icatech')
@section('content_script_css')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-wfSDFE50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <style>
            /* div.content
            {
                margin-bottom: 750%;
                margin-right: 0%;
                margin-left: 0%;
            } */
            .landscape {
                page: landscape;
                size: landscape;
            }
            .page-break {
                page-break-after: always;
            }
            .page-break-non {
                page-break-after: avoid;
            }
            /* .contenedor {
                position:RELATIVE;
                top:120px;
                width:100%;
                margin:auto;
                font-size: 12px;
            } */
        </style>
@endsection
@section('content')
    <div class= "contenedor">
        {!!$bodySupre!!}
        @if(!is_null($uuid))
            <br><br><br><div style="display: inline-block; width: 85%;">
                <p style="text-align: left; font-size: 9px;"><b>ATENTAMENTE.</b></p>
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
                <img style="position: fixed; width: 18%; top: 58%; left: 78%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
            </div>
        @else
            <br><p class="text-left"><p>Atentamente.</p></p>
            <br><b> C. {{$funcionarios['director']}}. </b>
            <br><b> {{$funcionarios['directorp']}}.</b>
        @endif
        {{-- aqui usar el fi --}}
        <div style="font-size: 10px;">
            @if(!is_null($bodyCcp))
                {!!$bodyCcp!!}
            @else
                <br><br><small><b>C.c.p. {{$funcionarios['ccp1']}}.- {{$funcionarios['ccp1p']}}.-Para su conocimiento</b></small>
                <br><small><b>C.c.p. {{$funcionarios['ccp2']}}.- {{$funcionarios['ccp2p']}}.-Mismo Fin</b></small>
                <br><small><b>Archivo.<b></small>
                <br><br><small><small><b>Validó: {{$funcionarios['director']}}.- {{$funcionarios['directorp']}}</b></small></small>
                <br><small><small><b>Elaboró: {{$funcionarios['delegado']}}.- {{$funcionarios['delegadop']}}</b></small></small>
            @endif
        </div>
    </div>
@endsection
@section('script_content_js')
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
@endsection
