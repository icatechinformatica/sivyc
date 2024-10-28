{{-- Realizado por Jose Luis Morenoa Arcos --}}
{{-- @extends('theme.formatos.vlayout') --}}
<title>Constancia Alumno | SIVyC Icatech</title>

<style>
    body{
    font-family: sans-serif;
    font-size: 1.3em;
    /* background-color: antiquewhite; */
    /* margin: 10px; */
    /* margin-bottom: 80px; */
    }
    @page {
        /* margin: 110px 20px 53px; */
        /* margin: 120px 40px 40px 40px; */
    }
    header {
        position: fixed;
        left: 0px;
        top: -100px;
        right: 0px;
        color: black;
        text-align: center;
        line-height: 30px;
        height: 100px;
        /* background-color: aqua; */
    }
    /* footer {
        position: fixed;
        left: 0px;
        bottom: -30px;
        right: 0px;
        height: 100px;
        text-align: center;
        line-height: 60px;
        background-color: aquamarine;
    } */
    /* footer {
            position: fixed;
            left: 0px;
            bottom: -100px;
            height: 150px;
            width: 100%;
    } */
    footer {
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        background-color: #f5f5f5; /* Color de fondo opcional */
        border-top: 1px solid #ddd; /* Borde superior opcional */
        padding: 10px; /* Espaciado interno opcional */
    }

        /* @page {margin: 0px 15px 15px 15px; } */
        html, body {
    /* height: 100%; */
    margin: 0;
}

    #fondo1 {
        background-image: url('img/econstancias_alumnos/fondo_constancia1.png');
        background-size: cover;
        background-position: center;
        width: 100%;
        margin: auto;
        height: 100%;
    }

    #fondo2 {
        background-image: url('img/econstancias_alumnos/fondo_constancia2.png');
        background-size: cover;
        background-position: center;
        width: 100%;
        margin: auto;
        height: 100%;
    }

</style>


<body>
    {{-- <header>
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 43%; text-align: left;">
                    <img src="img/logo_edupublica.png" alt="" style="width: 88%; padding-top:15px;">
                </td>
                <td style="width: 43%; text-align: center;">
                    <img src="img/instituto_oficial.png" alt="" style="width: 88%;">
                </td>
                <td style="width: 14%; text-align: right;">
                    <img src="img/logo_dgcft.png" alt="" style="width: 65%;">
                </td>
            </tr>
        </table>
    </header>
    <footer>
        @if(!empty($uuid))
        <div style="font-size:11px; text-align:justify; position: relative;">
            <span style="">Sello Digital: | GUID: {{$uuid}} | Sello: {{$cadena_sello}} | Fecha: {{$fecha_sello}} Este documento ha sido Firmado Electrónicamente, teniendo el mismo valor que la firma autógrafa de acuerdo a los Artículos 1, 3, 8 y 11 de la Ley de Firma Electrónica Avanzada del Estado de Chiapas.</span>
        </div>

        @endif
    </footer> --}}
    <div class="" id="fondo1">
        <div class="" align="center" style="">
            <p class="" style="font-size: 20px; font:bold; margin-bottom: 0px; margin-top:22%;">SECRETARÍA DE EDUCACIÓN PÚBLICA</p>
            <p class="" style="font-size: 20px; font:bold; margin: 0px;">SISTEMA EDUCATIVO NACIONAL</p>
            <p class="" style="font-size: 15px; font:bold; margin: 0px;">DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO</p>

            <p class="" style="font-size: 20px; font:bold; margin-bottom: 0px; margin-top: 17px;">INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN</p>
            <p class="" style="font-size: 20px; font:bold; margin-top:0px; margin-bottom: 35px">TECNOLÓGICA DEL ESTADO DE CHIAPAS</p>
        </div>


        <div class="" align="center">
            <span class="" style="font-size: 17px;">{{($data['unidad'] != $data['ubicacion']) ? 'Centro de trabajo acción movil: ' : 'Unidad de capacitación: '}}
                <b>{{$data['plantel'] .' '. $data['unidad'] }}</b>
            </span>
            <span class="" style="font-size: 17px; margin-left: 40px;">Con CCT: <b>{{ $data['cct'] }}</b></span>
            @if ($data['stps'] != '')
                <p class="" style="font-size: 17px; margin: 0px;">NÚMERO DE REGISTRO STPS: <b>{{ $data['stps'] }}</b> </p>
            @endif
        </div>


        <div class="" align="center">
            <p class="" style="font-size: 16px; margin-bottom: 5px; margin-top: 3px;">OTORGA LA PRESENTE</p>
            {{-- <p class="" style="font-size: 40px; font-weight:bold; margin-top: 0px;">CONSTANCIA</p> --}}
        </div>
        <div class="" align="center">
            <p class="" style="font-size: 22px; font-weight:bold; margin-top: 100px">A: {{ $data['nombre'] }}</p>
        </div>
        {{-- <div align="center" style="margin-bottom: 0px;">
        </div> --}}

        <div class="" align="justify" style="margin-right: 50px; margin-left: 80px;">
            <p class="" style="font-size: 16px; margin-top: 0px; margin-bottom: 0px;">con Clave Única de Registro de Población  <b>{{ $data['curp'] }}</b></p>

            <p class="" style="font-size: 18px; line-height: 1.5; margin-top: 0px;">en virtud de haber acreditado los conocimientos,
                habilidades, destrezas y aptitudes del curso, conforme al programa de capacitación de acuerdo a los documentos
                que obran en los archivos del Instituto.
            </p>
            <p class="" style="font-size: 20px; font-weight:bold; text-align:center">{{ $data['curso'] }}</p>
            <p class="" style="font-size: 18px; line-height: 1.5;">Con una duración de <b>{{ $data['dura'] }}</b> horas, en la modalidad de <b>{{ ($data['modalidad'] == 'CAE') ? 'CAPACITACIÓN ACELERADA ESPECIFICA.' : 'EXTENSIÓN' }}</b></p>
        </div>

        {{-- <p class="" style="margin-left: 40px; font-size: 18px; font-weight:bold; transform: rotate(-90deg); transform-origin: left top;">FOLIO: {{ $data['folio'] }}</p> --}}
        {{-- <p class="" style="position: absolute; top: 80%; left: 40px; font-size: 18px; font-weight:bold; transform: rotate(-90deg); transform-origin: left top;">
            <b style="color: #c72a22;">Folio:</b>
            <b style="background-color:#847f7f; color:#fff; margin: 2px;">{{ $data['folio'] }}</b>
        </p> --}}
        <p class="" style="position: absolute; top: 80%; left: 30px; font-size: 18px; font-weight:bold; transform: rotate(-90deg); transform-origin: left top;">
            <b style="color: #c72a22;">Folio:</b>
        </p>
        <p style="border-radius: 10px; border: 2px solid dashed rgb(102, 99, 99); padding: 4px; position: absolute; top: 74%; left: 30px; font-size: 20px; font-weight:bold; transform: rotate(-90deg); transform-origin: left top;">
            <span style="background-color:transparent; color: rgb(81, 79, 79); border-radius: 8px;">{{ $data['folio'] }}</span>
        </p>

        <div class="" align="left" style="margin-right: 50px; margin-left: 80px;">
            <p class="" style="font-size: 18px; margin-left: 8px; margin-bottom: 7px; margin-top: 40px">La presente se expide en <b>{{ $data['municipio'].', CHIAPAS' }}</b></p>
            <p class="" style="font-size: 18px; margin-left: 8px; margin-top: 0px;">A los <b>{{ $data['diaexp'] }}</b> días del mes de <b>{{ $data['mesexp']}}</b> del <b>{{ $data['anioexp']}}.</b></p>
        </div>

        {{-- footer primera pagina --}}
        @if(!empty($uuid))
            <div style="font-size:11px; text-align:justify; position: absolute; top:96%; margin-right: 40px; margin-left: 40px;">
                <span style="">Sello Digital: | GUID: {{$uuid}} | Sello: {{$cadena_sello}} | Fecha de emisión: {{$fecha_sello}} | Este documento ha sido Firmado Electrónicamente, teniendo el mismo valor que la firma autógrafa de acuerdo a los Artículos 1, 3, 8 y 11 de la Ley de Firma Electrónica Avanzada del Estado de Chiapas.</span>
            </div>
        @endif

    </div>

    <div style="page-break-after: always;"></div>

    <div id="fondo2">
        {{-- contenido tematico --}}
        @if (isset($cont_tematico) && count($cont_tematico) > 0)
            <div style="margin-right: 40px; margin-left: 40px;">
                <table style="width: 100%; margin-top: 50px; border: 2px solid #000">
                    <thead style="font-size:14px; text-align: center; font-weight:bold; background-color:#000; color: #fff;">
                        <th style="margin-bottom: 10px; width: 80%;">CONTENIDO TEMATICO</th>
                        <th style="margin-bottom: 10px; width:20%;">HORAS</th>
                    </thead>
                    <tbody style="">
                        @foreach ($cont_tematico as $tema)
                            <tr>
                                <td>
                                    <p style="font-size:12px; padding-bottom: 0px; padding-top: 0px; margin-top: 0px; margin-bottom: 0px;">{{mb_strtoupper($tema['nombre_modulo'])}}</p>
                                </td>
                                <td style="text-align: center;"><p style="font-size:12px; padding-bottom: 0px; padding-top: 0px; margin-top: 0px; margin-bottom: 0px;">{{strtoupper($tema['hora']).' '.$tema['tipo']}}</p></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tr>
                        <td></td>
                        <td><p style="text-align:center; font-size:12px; padding-bottom: 0px; padding-top: 0px; margin-top: 0px; margin-bottom: 0px; font-weight:bold; margin-left: 0px; margin-right: 0px;">TOTAL: {{$data['dura'] }} </p></td>
                    </tr>
                </table>
            </div>
        @endif

        {{-- firmas --}}
        @if (count($firmantes) > 0)
            <div style="max-width: 100%; margin-right: 40px; margin-left: 40px; margin-top: 40px;">
                @foreach ($firmantes as $item)
                    <p style="font-size: 12px; font-weight:bold; margin-bottom: 0px;">NOMBRE DEL FIRMANTE</p>
                    <p style="font-size: 12px; margin-top: 0px;">{{ $item['nombre'] }}</p>
                    <p style="font-size: 12px; font-weight:bold; margin-bottom: 0px;">FIRMA ELECTRONICA</p>
                    <p style="font-size: 12px;  margin-top: 0px;">{{ wordwrap( $item['firma'], 87, "\n", true) }}</p>
                    <p style="font-size: 12px; font-weight:bold; margin-bottom: 0px;">PUESTO</p>
                    <p style="font-size: 12px;  margin-top: 0px;">{{ $item['puesto'] }}</p>
                    <p style="font-size: 12px; font-weight:bold; margin-bottom: 0px;">FECHA DE FIRMA</p>
                    <p style="font-size: 12px;  margin-top: 0px;">{{ $item['fecha_firma'] }}</p>
                    <p style="font-size: 12px; font-weight:bold; margin-bottom: 0px;">NÚMERO DE SERIE</p>
                    <p style="font-size: 12px;  margin-top: 0px;">{{ $item['serie'] }}</p>
                @endforeach
            </div>
            <div style="display: inline-block; width: 10%;">
                <img style="position: fixed; width: 19%; top: 67%; left: 75%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
            </div>
        @endif
    </div>

    {{-- footer primera pagina --}}
    @if(!empty($uuid))
        <div style="font-size:11px; text-align:justify; position: absolute; top:85%; margin-right: 40px; margin-left: 40px;">
            <span style="">Sello Digital: | GUID: {{$uuid}} | Sello: {{$cadena_sello}} | Fecha de emisión: {{$fecha_sello}} | Este documento ha sido Firmado Electrónicamente, teniendo el mismo valor que la firma autógrafa de acuerdo a los Artículos 1, 3, 8 y 11 de la Ley de Firma Electrónica Avanzada del Estado de Chiapas.</span>
        </div>
    @endif


</body>


{{-- @section('content') --}}

{{-- @endsection --}}

{{-- @section('script_content_js') --}}
    {{-- <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(73, 760, "Pág. $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }
    </script> --}}
{{-- @endsection --}}

