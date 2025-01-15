@extends('theme.formatos.vlayout2025')
@section('title', 'REPORTE FOTOGRAFICO | SIVyC Icatech')
@section('content_script_css')
    <style>
        /* body {
            font-family: sans-serif;
            margin-top: 15%;
            margin-bottom: 4%;
        } */

        /* @page {
            margin: 35px 30px 150px 30px;
        } */

        /* header {
            position: fixed;
            left: 0px;
            top: -80px;
            right: 0px;
            text-align: center;
        } */

        /* header h6 {
            height: 0;
            line-height: 14px;
            padding: 8px;
            margin: 0;
        } */

        /* table #curso {
            font-size: 8px;
            padding: 10px;
            line-height: 18px;
            text-align: justify;
        } */

        main {
            padding: 0;
            margin: 0;
            margin-top: 0px;
        }

        .tabla {
            border-collapse: collapse;
            width: 100%;
        }

        .tabla tr td,
        .tabla tr th {
            font-size: 8px;
            border: gray 1px solid;
            text-align: center;
            /* padding: 3px; */
        }

        .tab {
            margin-left: 10px;
            margin-right: 50px;
        }

        .tab1 {
            margin-left: 15px;
            margin-right: 50px;
        }

        .tab2 {
            margin-left: 5px;
            margin-right: 20px;
        }

        /* footer {
            position: fixed;
            left: 0px;
            bottom: -170px;
            height: 150px;
            width: 100%;
        } */

        footer .page:after {
            content: counter(page, sans-serif);
        }

        .tablaf {
            border-collapse: collapse;
            width: 100%;
        }

        .tablaf tr td {
            font-size: 9px;
            text-align: center;
            padding: 3px;
        }

        .tab {
            margin-left: 20px;
            margin-right: 50px;
        }

        .tab1 {
            margin-left: 10px;
            margin-right: 20px;
        }

        .tab2 {
            margin-left: 10px;
            margin-right: 60px;
        }

        /* by Jose Luis Moreno */
        .estilo_tabla {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            /* text-align: center; */
            /* margin-top: 20px; */
        }

        .estilo_colum {
            border: 1px solid #ddd;
            padding: 3px;
            /* text-align: left; */
            font-size: 12px;
        }

        /* .direccion {
            top: 1.3cm;
            text-align: left;
            position: absolute;
            bottom: 60px;
            left: 20px;
            font-size: 8px;
            color:#FFF;
            font-weight: bold;
            line-height: 1;
        } */

        .content {
            margin-top: 15%;
            margin-bottom: 4%;
        }

    </style>
    @php $reporte_fotografico = true; @endphp
@endsection
@section('content')
    {{-- <header>
        <img src="img/instituto_oficial.png" alt="Logo Izquierdo" width="30%" style="position:fixed; left:0; top:0;" />
        <img src="img/chiapas.png" alt="Logo Derecho" width="25%" style="position:fixed; right:0; top:0;" />
    </header> --}}
    {{-- {!! $body['header'] !!} --}}
    {!! $body['body'] !!}
    {{-- <div style="margin-top: -9%; margin-bottom: 4%;">
        <h6 style="text-align: center;">{{isset($leyenda) ? $leyenda : ''}}</h6>
    </div>
    <div style="text-align:center;">
        <span style="text-align: center;">REPORTE FOTOGRÁFICO DE INSTRUCTOR EXTERNO</span>
    </div> --}}
    {{-- <h6 style="text-align: center;">{{isset($leyenda) ? $leyenda : ''}}</h6> --}}
    {{-- tabla --}}
    {{-- @if ($cursopdf)
        {{-- Lugar y fecha
        <div style="text-align: right;">
            <p style="font-size: 14px; margin-bottom: 5px;">
            @if ($cursopdf->ubicacion != $cursopdf->unidad)
            UNIDAD DE CAPACITACIÓN {{$cursopdf->ubicacion}}, CENTRO DE TRABAJO ACCIÓN MÓVIL {{$cursopdf->unidad}}.
            @else
            UNIDAD DE CAPACITACIÓN {{$cursopdf->ubicacion}}.
            @endif
            </p>
            {{-- @if(!is_null($EFolio))
                <p>EFOLIO: {{$EFolio}}</p>
            @endif
            <p style="font-size: 14px; margin-top: 0px; margin-bottom: 25px;">{{mb_strtoupper($cursopdf->municipio, 'UTF-8')}}, CHIAPAS. A {{$fechapdf}}.</p>
        </div>

        <table border="1" class="estilo_tabla" width="100%">
            <tbody>
                <tr class="estilo_colum">
                    <td class="estilo_colum" colspan="2"><b>CURSO: </b>{{$cursopdf->curso}}</td>
                </tr>
                <tr class="estilo_colum">
                    <td class="estilo_colum" colspan="2"><b>TIPO: </b>{{$cursopdf->tcapacitacion}}</td>
                </tr>
                <tr class="estilo_colum">
                    <td class="estilo_colum"><b>FECHA DE INICIO: </b>{{$cursopdf->inicio}}</td>
                    <td class="estilo_colum"><b>FECHA DE TÉRMINO: </b>{{$cursopdf->termino}}</td>
                </tr>
                <tr class="estilo_colum">
                    <td class="estilo_colum"><b>CLAVE: </b>{{$cursopdf->clave}}</td>
                    <td class="estilo_colum"><b>HORARIO: </b>{{$cursopdf->hini. ' A '. $cursopdf->hfin}}</td>
                </tr>
                <tr class="estilo_colum">
                    <td class="estilo_colum"><b>NOMBRE DEL TITULAR DE LA U.C: </b>{{$cursopdf->dunidad}}</td>
                    <td class="estilo_colum"><b>NOMBRE DEL INSTRUCTOR: </b>{{$cursopdf->nombre}}</td>
                </tr>
            </tbody>
        </table>

    @endif --}}
    <br>
     {{-- Mostrar imagenes --}}
     @if (count($base64Images) > 0)
     {{-- <div class="" style="text-align: center; border: 1px solid red;"> --}}
         @foreach($base64Images as $key => $base64)
            <div @if ($objeto == null && $key == (count($base64Images)-1)) @else @if($key != 0) style= "page-break-after: always; text-align: center; margin-top: 15%;" @else  style= "page-break-after: always; text-align: center;" @endif @endif>
                <img style="width: auto; height: auto; max-width: 70%; max-height:70%;" src="data:image/jpeg;base64,{{$base64}}" alt="Foto">
                <small style="text-align: right; display:block; max-width: 70%; margin-top:2px;">{{basename($array_fotos[$key])}}</small>
            </div>
         @endforeach
     {{-- </div> --}}
     @endif

    {{-- Apartado de mostrar firmas --}}
    <div>
        @if(!is_null($objeto))
            <div style="display: inline-block; width: 85%; margin-left: 12px; margin-top: 15%;">
                <table style="width: 100%;">
                    @foreach ($objeto['firmantes']['firmante'][0] as $key=>$moist)
                        <tr style="">
                            <td style="font-size: 11px; padding-bottom: 10px;"><b>Nombre del firmante:</b></td>
                            <td style="font-size: 11px; padding-bottom: 10px;">{{ $moist['_attributes']['nombre_firmante'] }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; font-size: 11px; padding-bottom: 10px;"><b>Firma Electrónica:</b></td>
                            <td style="font-size: 11px; padding-bottom: 10px;">{{ wordwrap($moist['_attributes']['firma_firmante'], 87, "\n", true) }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 11px; padding-bottom: 10px;"><b>Puesto:</b></td>
                            @if ($dataFirmante->curp == $moist['_attributes']['curp_firmante'])
                                <td style="font-size: 11px; padding-bottom: 10px;">{{ $dataFirmante->cargo }}</td>
                            @else
                                <td style="font-size: 11px; padding-bottom: 10px;">INSTRUCTOR EXTERNO</td>
                            @endif
                        </tr>
                        <tr>
                            <td style="font-size: 11px; padding-bottom: 10px;"><b>Fecha de Firma:</b></td>
                            <td style="font-size: 11px; padding-bottom: 10px;">{{ $moist['_attributes']['fecha_firmado_firmante'] }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 11px; padding-bottom: 10px;"><b>Número de Serie:</b></td>
                            <td style="font-size: 10px; padding-bottom: 10px;">{{ $moist['_attributes']['no_serie_firmante'] }}</td>
                        </tr>
                    @endforeach
                </table>
                <div style="display: inline-block; width: 10%;">
                    <img style="position: fixed; width: 19%; top: 62%; left: 75%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
                </div>
            </div>
        @endif
    </div>
        {{-- <img style="position: fixed; width: 18%; top: 30%; left: 75%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR"> --}}
@endsection
@section('script_content_js')
