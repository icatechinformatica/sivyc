{{-- create by Daniel Mendez  --}}
@extends('theme.formatos.verticallayout', ['distintivo' => '"2023, Año de Francisco Villa, el Revolucionario del Pueblo"', 'direccion' => isset($direccion) ? $direccion->direccion : '4a. Pte. Nte. N°239 Esq. Circunvalación Pichucalco , Col. Moctezuma C.P. 29030 Tuxtla Gutiérrez, Chiapas. Teléfono: (961) 6121621 / 6127121'])
{{-- @extends('theme.sivyc.layout') --}}

{{-- sections --}}
@section('title', 'Formato de Requisición')

@section('content_script_css')
    <style>
        .contenedor{
            font-family: Arial;
            font-size: 11px;
        }
        table,
        th,
        td {
            /* border: 1px solid black; */
            border-collapse: collapse;
            border: none;
        }

        th,
        td {
            padding: 1px;
        }

        th {
            text-align: left;
        }

        .tablas {
            width: 100%
        }

        .tablas tr td {
            border: none;
            text-align: center;
            padding: 1px 1px;
        }

        .tablas th {
            text-align: center;
            padding: 1px 1px;
        }

        .tabla_con_border {
            border-collapse: collapse;
            font-family: sans-serif;
            position: relative;
            margin: auto;
            margin-top: 20px;
        }

        .tabla_con_border tr td {
            border: 1px solid #000000;
            word-wrap: break-word;
        }

        .tabla_con_border td {
            page-break-inside: avoid;
        }

        .tabla_con_border thead tr th {
            border: 1px solid #000000;
        }

        /* espaciado */
        center.espaciado {
            padding-top: 10px;
        }

        .celdaAsignado {
            word-break: break-all !important;
            white-space: nowrap !important;
            width: 40px !important;
        }

        .tablas_notas {
            border-collapse: collapse;
            width: 90%;
            position: relative;
            margin: auto;
        }

        .tablas_notas tbody tr td {
            /* border: #000000 1.8px solid; */
            text-align: justify;
            padding: 1px 1px;
            height: 2em;
        }

        .tablas_notas th {
            /* border: gray 1px solid; */
            text-align: justify;
            padding: 1px 1px;
        }

        .table_inside {
            border-collapse: collapse;
            width: 100%;
        }

        .table_inside thead tr th {
            border-top: none;
        }

        .table_inside tbody tr td {
            border-bottom: none;
        }

        /* tablas para firmas */
        .tablaf {
            border-collapse: collapse;
            width: 100%;
            margin-top: 7em;
        }

        .tablaf tr td {
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
            text-align: left;
            padding: 2px;
        }

        .page-break {
            page-break-after: always;
        }
        .tabla_inicio {
            position: relative;
            margin: auto;
            margin-top: 40px;
        }

        .tabla_inicio tr td {
            /* border: 1px solid #000000; */
        }
    </style>
@endsection

@section('header')
    <img class="izquierda"
        src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/instituto_oficial.png'))) }}">
@endsection


@section('content')

    <div class="contenedor">
        <table class="tabla_inicio" style="width: 90%;">
            <tbody>
                <tr>
                    <td>
                        <strong>ÁREA QUE SOLICITA:</strong> UNIDAD EJECUTIVA
                    </td>
                    <td>
                        <strong>DEPARTAMENTO: </strong> INFORMÁTICA
                    </td>
                    <td>
                        <strong>FECHA:</strong> 29/11/2023
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="tabla_con_border" style="width: 90%;">
            @php
                $j = 1;
            @endphp
            <thead>
                <tr>
                    <th style="width: 100%; text-align: center; background-color:#C5C5C5;">ARTÍCULO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($req as $item => $value)
                    <tr>
                        <td data-label="PARTIDA PRESUPUESTAL"
                            style="max-height: 10px; text-align: center; background-color:#C5C5C5;">
                            <b>{{ $value->clave_partida . ' --- ' . $value->descripcion }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @php
                                $i = 1;
                            @endphp
                            @if (count($bienes) > 0)
                                <table class="table_inside" id="partidaPresupuestal_{{ $i }}_{{ $j }}"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%;">CANT.</th>
                                            <th style="width: 20%;">UNIDAD</th>
                                            <th style="width: 50%;">DESCRIPCIÓN</th>
                                        </tr>
                                    </thead>
                                    @foreach ($bienes as $item)
                                        @if ($item->idPartidaCat == $value->id)
                                            <tbody>
                                                <tr>
                                                    <td data-label="CANT.">
                                                        <label>{{ $item->cantidadReq }}</label>
                                                    </td>
                                                    <td data-label="UNIDAD">
                                                        <label>{{ $item->UnidadReq }}</label>
                                                    </td>
                                                    <td data-label="DESCRIPCIÓN">
                                                        <label>
                                                            {{ $item->descripcionCat }}
                                                        </label>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        @endif
                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach
                                </table>
                            @endif
                        </td>
                    </tr>
                    @php
                        $j++;
                    @endphp
                @endforeach
            </tbody>
        </table>
        <br>
        <table class="tablas_notas" style="width: 90%;">
            <thead>
                <tr>
                    <th>JUSTIFICACIÓN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ $just->justificacion }}
                    </td>
                </tr>

            </tbody>
        </table>
        <table class="tablaf">
            <tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td align="center">ELABORÓ<br><br><br></td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td></td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td align="center">AUTORIZA<br><br><br></td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
            </tr>
            <tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td align="center"><br>_____________________________________________________</td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td></td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td align="center"><br>____________________________________________________</td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
            </tr>
            <tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td align="center" style="padding-top: 10px;">{{ auth()->user()->name }}</td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td></td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td align="center" style="padding-top: 10px;">ALEJANDRO MONTOYA RUIZ</td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
            </tr>
            <tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td align="center">{{ auth()->user()->puesto }}</td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td></td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td align="center">JEFE DEL ÁREA DE INFORMÁTICA</td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
            </tr>
        </table>
    </div>
@endsection
