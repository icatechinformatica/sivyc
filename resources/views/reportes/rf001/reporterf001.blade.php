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
    </style>
@endsection
@php
    $nombre_titular = $cargo_fun = 'DATO REQUERIDO';
    if ($organismo) {
        $nombre_titular = $organismo->nombre_titular;
        $cargo_fun = mb_strtoupper($organismo->cargo_fun, 'UTF-8');
    }
@endphp
@section('content')
    @php
        $datoJson = json_decode($rf001->movimientos, true);
        $startDate = Carbon\Carbon::parse($rf001->periodo_inicio);
        $endDate = Carbon\Carbon::parse($rf001->periodo_fin);
        $formattedStartDate = $startDate->format('d');
        $formattedEndDate = $endDate->format('d');
        $mes = $startDate->translatedFormat('F');
        $anio = $startDate->format('Y');
    @endphp
    <div class="contenedor">
        <div class="bloque_uno" align="right">
            <p class="delet_space_p color_text">UNIDAD DE CAPACITACIÓN {{ strtoupper($unidad) }}</p>
            <p class="delet_space_p color_text">OFICIO NÚM. {{ $rf001->memorandum }}</p>
            <p class="delet_space_p color_text">{{ $municipio }}, CHIAPAS; <span
                    class="color_text">{{ strtoupper($fecha_comp) }}</span></p>
        </div>
        <br><br><br>
        <div class="bloque_dos" align="left">
            <p class="delet_space_p color_text">C.
                {{ strtoupper($dirigido->titulo) }} {{ strtoupper($dirigido->nombre) }}
            </p>
            <p class="delet_space_p color_text">
                {{ $dirigido->cargo }}
            </p>
            <p class="delet_space_p color_text">PRESENTE.</p>
        </div>
        <br>
        <div class="contenido" align="justify">
            Por medio del presente, envío a usted Original del formato de concentrado de ingresos propios (RF-001),
            original, copias de fichas de depósito y recibos oficiales correspondientes a los cursos generados en la unidad
            de Capacitación <span class="color_text"> {{ $unidad }}</span>, con los siguientes movimientos.
            <br>
        </div>
        <br>
        <div class="tabla_alumnos">
            <ul style="font-size: 14px">
                @foreach ($datoJson as $key => $value)
                    @php
                        $depositos = isset($value['depositos']) ? json_decode($value['depositos'], true) : [];
                    @endphp
                    <li style="font-size: 12px;">
                        <b>{{ $value['curso'] == null ? strtolower($value['descripcion']) : strtolower($value['curso']) }}</b>
                        con el siguiente folio
                        {{ $value['folio'] }}</li>
                @endforeach
            </ul>
            <p style="font-size: 14px">Correspondientes al periodo comprendido del {{ $formattedStartDate }} al
                {{ $formattedEndDate }} de {{ $mes }} del {{ $anio }}, lo anterior, para contabilización
                respectiva.</p>
            <p style="font-size: 14px">Sin otro particular aprovecho la ocasión para saludarlo. </p>
            <br>
        </div>
        {{-- <div class="contenido">
            ATENTAMENTE
            <br><br><br><br>
            <span class="color_text firmau"><b>{{ $data->dunidad }}</b></span> <br>
            <span class="color_text firmau"><b>{{ $data->pdunidad . ' ' . $unidad }}</b></span>
        </div>
        <br> --}}
        {{-- <div class="textofin">
            <p class="delet_space_p">C.C.P. {{ $conocimiento->titulo }} {{ $conocimiento->nombre_funcionario }} - {{ $conocimiento->cargo }} - Para su conocimiento.</p>
            <p class="delet_space_p">ARCHIVO / MINUTARIO</p>
            <p class="delet_space_p">ELABORÓ: {{ $nombreElaboro }}. - {{ $puestoElaboro }}</p>
            <p class="delet_space_p">VALIDÓ: {{ $delegado->nombre }}. - {{ $delegado->cargo }}</p>
        </div> --}}
    </div>


@endsection

@section('script_content_js')
@endsection
