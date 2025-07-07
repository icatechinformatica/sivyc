{{-- Realizado por Jose Luis Morenoa Arcos --}}
@extends('theme.formatos.vlayout_expeunico')
@section('title', 'Expediente Unico | SIVyC Icatech')

@section('content_script_css')
    <style>
        /* @page {margin: 0px 15px 15px 15px; } */
        .contenedor{
            margin-left: -1.3cm;
            margin-right: -0.6cm;
        }

        .bloque_uno{
            /* padding-top: 40px; */
            margin-top: -45px;
            font-weight: bold;
            font-size: 14px;
            font: bold;
        }

        .contenido{
            font-size: 13px;
            line-height: 5;
        }


        /* estilos de tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            /* margin-top: 20px; */
        }

        th, td {
            border: 1px solid #2b2a2a;
            padding: 5px;
            /* text-align: left; */
            font-size: 12px;
        }

        th {
            background-color: #a4a1a1;
        }

        .negrita{
            font-weight: bold;
        }
        header { top: 110px; text-align: center;width:100%;line-height: 15px; font-weight: bold; font-size: 15px;}

    </style>
@endsection

@section('content')
    <div class="contenedor">
        <div class="bloque_uno" align="right">
            <p style="text-align: center">Lista de verificación de Expediente Único</p>
        </div>
        <br>
        @php $distintivo = null;  @endphp
        {{-- Tabla encabezado --}}
        @if (isset($curso))
            <table>
                <thead>
                    <tr>
                        <th colspan="3">CONDICIONES DE CAPACITACIÓN</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td><span class="negrita">TIPO DE CAPACITACIÓN:</span> {{($curso->tipo_curso == 'CURSO') ? 'CURSO' : 'CERTIFICACION EXTRAORDINARIA'}}</td>
                        <td><span class="negrita">NOMBRE DEL INSTRUCTOR:</span> {{$curso->nombre}}</td>
                        <td><span class="negrita">CUOTA GENERAL:</span> {{$curso->costo}}</td>
                    </tr>
                    <tr>
                        <td><span class="negrita">NOMBRE DEL CURSO:</span> {{$curso->curso}}</td>
                        <td><span class="negrita">ESPECIALIDAD DEL INSTRUCTOR:</span> {{$curso->espe}}</td>
                        <td><span class="negrita">FECHA INICIO:</span> {{ \Carbon\Carbon::createFromFormat('Y-m-d', $curso->inicio)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><span class="negrita">TIPO DE CURSO:</span> {{$curso->tcapacitacion}}</td>
                        <td><span class="negrita">TIPO DE PAGO:</span> {{$curso->tpago}}</td>
                        <td><span class="negrita">FECHA TERMINO:</span> {{ \Carbon\Carbon::createFromFormat('Y-m-d', $curso->termino)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><span class="negrita">CLAVE DEL CURSO:</span> {{$curso->clave}}</td>
                        <td><span class="negrita">UNIDAD:</span> {{$curso->unidad}}</td>
                        <td><span class="negrita">HORARIO:</span> {{$curso->hini .' a '.$curso->hfin}}</td>
                    </tr>
                    <tr>
                        <td><span class="negrita">FOLIO: </span> {{$curso->folio_grupo}}</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        @endif


        <br>

        {{-- Tabla curpo formulario --}}
        @if (isset($json_dptos))
            <table>
                <thead>
                    <tr>
                        <th colspan="6">CONTROL ESCOLAR ARTICULO 30 DE LPPVC</th>
                    </tr>
                    <tr>
                        <th width="5%">NO.</th>
                        <th width="45%">EVIDENCIAS</th>
                        <th width="5%">SI</th>
                        <th width="5%">NO</th>
                        <th width="10%">NO APLICA</th>
                        <th width="30%">OBSERVACIONES</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- Vinculacion --}}
                    <tr>
                        <th colspan="2">DEPARTAMENTO DE VINCULACIÓN</th>
                        <th colspan="4"></th>
                    </tr>
                    @for ($i = 1; $i <= 7; $i++)
                        <tr>
                            <td>{{$abecedario[$i-1]}}</td>
                            <td>{{$evid_vincu['doc'.$i]}}</td>
                            {{-- <td>{{$json_dptos->vinculacion['doc_'.$i]['nom_doc']}}</td> --}}
                            @php
                            $exis_evid = $json_dptos->vinculacion['doc_'.$i]['existe_evidencia'];
                            $observ = $json_dptos->vinculacion['doc_'.$i]['observaciones'];
                            @endphp
                            <td align="center">{{($exis_evid == 'si') ? 'X' : ''}}</td>
                            <td align="center">{{($exis_evid == 'no') ? 'X' : ''}}</td>
                            <td align="center">{{($exis_evid == 'no_aplica') ? 'X' : ''}}</td>
                            <td>{{($observ != null || $observ != '') ? $observ : ''}}</td>
                        </tr>
                    @endfor

                    {{-- Academico --}}
                    <tr>
                        <th colspan="2">DEPARTAMENTO ACADÉMICO</th>
                        <th colspan="4"></th>
                    </tr>
                    @for ($i = 8; $i <= 19; $i++)
                        <tr>
                            <td>{{$abecedario[$i-8]}}</td>
                            @php
                                // $docIndex = ($i == 20) ? 25 : $i;
                                $exis_evid = $json_dptos->academico['doc_'.$i]['existe_evidencia'];
                                $observ = $json_dptos->academico['doc_'.$i]['observaciones'];
                            @endphp
                            <td>{{$evid_acad['doc'.$i]}}</td>
                            <td align="center">{{($exis_evid == 'si') ? 'X' : ''}}</td>
                            <td align="center">{{($exis_evid == 'no') ? 'X' : ''}}</td>
                            <td align="center">{{($exis_evid == 'no_aplica') ? 'X' : ''}}</td>
                            <td>{{($observ != null || $observ != '') ? $observ : ''}}</td>
                        </tr>
                    @endfor

                    {{-- Administrativo --}}
                    <tr>
                        <th colspan="2">DELEGACIÓN ADMINISTRATIVA</th>
                        <th colspan="4"></th>
                    </tr>
                    @for ($i = 20; $i <= 24; $i++)
                        <tr>
                            <td>{{$abecedario[$i-20]}}</td>
                            @php
                                $exis_evid = $json_dptos->administrativo['doc_'.$i]['existe_evidencia'];
                                $observ = $json_dptos->administrativo['doc_'.$i]['observaciones'];
                            @endphp
                            <td>{{$evid_admin['doc'.$i]}}</td>
                            <td align="center">{{($exis_evid == 'si') ? 'X' : ''}}</td>
                            <td align="center">{{($exis_evid == 'no') ? 'X' : ''}}</td>
                            <td align="center">{{($exis_evid == 'no_aplica') ? 'X' : ''}}</td>
                            <td>{{($observ != null || $observ != '') ? $observ : ''}}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        @endif

        {{-- Firmantes --}}
        {{-- <table class="tablaf" style="margin-top: 20px;">
            <tr class="snborde">
                <td class="snborde">
                    <p class="parrafo">___<u>Jose Luis Moreno Arcos</u>___</p>
                    <p class="parrafo"><b>Nombre del cargo publico</b></p>
                </td>

                <td class="snborde">
                    <p class="parrafo">___<u>LIC MARIO MENDEZ LOPEZ</u>___</p>
                    <p class="parrafo"><b>Nombre del cargo que funge</b></p>
                </td>
            </tr>
        </table> --}}


    </div>


@endsection

@section('script_content_js')

@endsection

