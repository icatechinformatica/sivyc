@extends('theme.formatos.vlayout2025')
@section('title', 'SOLICITUD DE PAGO DE INSTRUCTOR | SIVyC Icatech')
@section('content_script_css')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{{ public_path('vendor/bootstrap/3.4.1/bootstrap.min.css') }}">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-wfSDFE50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <style>
            table, td {
              border:1px solid black;
            }
            table {
              border-collapse:collapse;
              width:100%;
            }
            td {
              padding:px;
            }

            .table1, .table1 td {
                border:0px ;
            }
            .table1 td {
                padding:5px;
            }
            small {
                font-size: .7em
            }
            header {left: 25px;}
            .overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                background-color: transparent; /* Fondo semitransparente para hacerlo visible */
                color: white;
                z-index: 10; /* Asegura que esté encima del contenido */
                padding: 10px;
                text-align: center;
                line-height: -1;
                align-items: center;
            }
        </style>
        @if(!is_null($objeto))
            <style>
                .sello {
                    position: relative;
                    top: -12%;
                    right: -10%;
                    text-align: center;
                    line-height: 0;
                }
            </style>
        @else
            <style>
                .sello {
                    position: relative;
                    top: -20%;
                    right: -35%;
                    text-align: center;
                    line-height: 0;
                }
            </style>
        @endif
@endsection
@section('content')
        {{-- {!! $body_html['header'] !!} --}}
        {{-- <div class= "container g-pt-30"> --}}
            <div id="content">
                {!! $body_html['body'] !!}
                @if(!is_null($objeto))
                    {{-- <div style="display: inline-block; width: 85%;"> --}}
                        <table style="width: 85%; font-size: 8px; border-collapse: collapse; border: none;">
                            @foreach ($objeto['firmantes']['firmante'][0] as $keys=>$moist)
                            <tr>
                                <td style="width: 10%; font-size: 8px; border: none;"><b>Nombre del firmante:</b></td>
                                <td style="width: 90%; font-size: 8px; border: none;">{{ $moist['_attributes']['nombre_firmante'] }}</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top; font-size: 8px; border: none;"><b>Firma Electrónica:</b></td>
                                <td style="font-size: 8px; border: none;">{{ wordwrap($moist['_attributes']['firma_firmante'], 100, "\n", true) }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 8px; border: none;"><b>Puesto:</b></td>
                                @if(isset($body_html['firmantes']))
                                    @foreach($body_html['firmantes'] as $firma)
                                        @if($firma->curp == $moist['_attributes']['curp_firmante'])
                                            <td style="font-size: 8px; height: 25px; border: none;">{{ $firma->cargo }}</td>
                                        @endif
                                    @endforeach
                                @else
                                    <td style="font-size: 8px; height: 25px; border: none;">{{ $puesto->cargo }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td style="font-size: 8px; border: none;"><b>Fecha de Firma:</b></td>
                                <td style="font-size: 8px; border: none;">{{ $moist['_attributes']['fecha_firmado_firmante'] }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: 8px; border: none;"><b>Número de Serie:</b></td>
                                <td style="font-size: 8px; border: none;">{{ $moist['_attributes']['no_serie_firmante'] }}</td>
                            </tr>
                            @endforeach
                        </table>
                    {{-- </div> --}}
                    <div style="display: inline-block; width: 15%;">
                        {{-- <img style="position: fixed; width: 100%; top: 55%; left: 80%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR"> --}}
                        <img style="position: fixed; width: 15%; top: 60%; left: 79%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
                    </div>
                @else
                    <table class="table1">
                        <tr>
                            <td><p align="center">Atentamente</p></td>
                        </tr>
                        <tr>
                            <td></td>
                        </tr>
                        <tr>
                            <td><div align="center">{{$funcionarios['director']}}</td></div>
                        </tr>
                        <tr>
                            <td><div align="center">{{$funcionarios['directorp']}}</td></div>
                        </tr>
                    </table>
                @endif
                <div class="sello" style="height: 0;">
                    @php  $sello = explode('*', $sello); @endphp
                    <p class="overlay" ><h4 style="line-height: 0;">{{$sello[0]}}</h4>
                        <br style="line-height: 0;"><h5 style="line-height: 0;">{{$sello[1]}}
                            <br style="line-height: 0;"><h3 style="line-height: 0;">{{$sello[2]}}</h3>
                        <br style="line-height: -1;"> {{$sello[3]}}
                    <br style="line-height: 1;"><h4 style="line-height: 0;">{{$sello[4]}}</h4></h5></p>
                </div>
                {!! $body_html['ccp'] !!}
                {{-- <p style="line-height:0.8em;">
                    <b><small>C.c.p.{{$funcionarios['ccp1']}}.- {{$funcionarios['ccp1p']}}.-Para su conocimiento.</small></b><br/>
                    <b><small>C.c.p.{{$funcionarios['ccp2']}}.- {{$funcionarios['ccp2p']}}.-Mismo fin.</small></b><br/>
                    <b><small>C.c.p.{{$funcionarios['delegado']}}.- {{$funcionarios['delegadop']}}.-Mismo fin.</small></b><br/>
                    <b><small>Archivo/ Minutario<small></b><br/>
                    <b><small>Validó: {{$funcionarios['delegado']}}.- {{$funcionarios['delegadop']}}.</small></b><br/>
                    <b><small>Elaboró: {{$funcionarios['delegado']}}.- {{$funcionarios['delegadop']}}.</small></b>
                </p> --}}
            </div>
        {{-- </div> --}}
        {{-- {!! $body_html['footer'] !!} --}}
@endsection
@section('script_content_js')
