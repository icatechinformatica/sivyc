{{-- Realizado por Jose Luis Morenoa Arcos --}}
@extends('theme.formatos.vlayout')
@section('title', 'Soporte de Entrega | SIVyC Icatech')

@section('content_script_css')
    <style>
        /* @page {margin: 0px 15px 15px 15px; } */
        .contenedor{
            margin-left: 1cm;
            margin-right: 1cm;
        }
        body{
            /* margin-top: 70px; */
            /* margin-bottom: -120px; */
            padding-top: 45px;
            padding-bottom: 60px;
            /* background-color: aqua; */
        }
        .bloque_uno{
            padding-top: 40px;
            font-weight: bold;
            font-size: 13px;
            font: bold;
        }
        .bloque_dos{
            font-weight: bold;
            font-size: 12px;
            font: bold;
        }
        .contenido{
            font-size: 13px;
            line-height: 1.5;
        }
        .delet_space_p{
            margin-top: 0px;
            margin-bottom: 0px;
        }
        .textofin{
            font-size: 11px;
            font-style: italic;
        }
        .color_text{color:black;}
        .tablas{border-collapse: collapse;width: 100%;}
        .tablas tr{font-size: 9px; border: gray 1px solid; text-align: center; padding: 2px;}
        .tablas th{font-size: 10px; border: gray 1px solid; text-align: center; padding: 2px;}
        .colortexto{
            color: #0000;
        }
        .firmau{
            font-size: 12px;
            font:normal;
        }


    </style>
@endsection
@php
    // dd($distintivo, $direccion, $data, $unidad, $organismo, $numficio,
    //     $partes_titu, $municipio, $fecha_comp, $tabla_contenido, $rango_mes, $total_cursos, $total_folios);
    // dd($municipio);
    $nombre_titular = $cargo_fun = 'DATO REQUERIDO';
    if ($organismo) {
        $nombre_titular = $organismo->nombre_titular;
        $cargo_fun = mb_strtoupper($organismo->cargo_fun, 'UTF-8');
    }
@endphp
@section('content')
    <div class="contenedor">
        <div class="bloque_uno" align="right">
            <p class="delet_space_p color_text">UNIDAD DE CAPACITACIÓN {{strtoupper($unidad)}}</p>
            <p class="delet_space_p color_text">OFICIO NÚM. {{$numficio}}</p>
            <p class="delet_space_p color_text">{{$municipio}}, CHIAPAS; <span class="color_text">{{strtoupper($fecha_comp)}}</span></p>
        </div>
        <br><br><br>
        <div class="bloque_dos" align="left">
            <p class="delet_space_p color_text">C.
                @if (count($partes_titu) > 0)
                    {{$partes_titu[0]}}
                @else
                    {{strtoupper($nombre_titular)}}
                @endif
            </p>
            <p class="delet_space_p color_text">
                @if (count($partes_titu) > 1)
                    {{$partes_titu[1]}}
                @else
                    {{$cargo_fun}}
                @endif
                {{-- {{$organismo->cargo_fun != null ? $organismo->cargo_fun : 'CARGO REQUERIDO'}} --}}
            </p>
            <p class="delet_space_p color_text">PRESENTE.</p>
            {{-- <p class="delet_space_p color_text">{{$municipio}}, CHIAPAS</p> --}}
        </div>
        <br>
        <div class="contenido" align="justify">
            Reciba un cordial saludo y respetuosamente me dirijo a usted, con la finalidad de hacer entrega física de <span class="color_text">{{$total_folios}}</span>
            constancias de cursos de Capacitación "originales", los cuales han sido agrupados en <span class="color_text"> {{$total_cursos}}</span> curso(s) diferente(s) e
            impartido(s) por esta Unidad de Capacitación a mi cargo, durante el periodo <span class="color_text"> {{$rango_mes}}</span> de la presente anualidad.
            <br><br>
            De igual forma adjunto original del formato RIACD-02 de certificación
            correspondiente a cada curso, para su conocimiento y validación de los folios de constancias que pertenecen al número
            asignado a los alumnos, al momento de ser entregadas las constancias originales para su verificación.
            <br><br>
            A continuación,
            se describe(n) <span class="color_text">{{$total_cursos}}</span> curso(s) del total de <span class="color_text">{{$total_folios}}</span>
            constancias, que se encuentran listas para ser entregadas a los alumnos:
        </div>
        <br>
        <div class="tabla_alumnos">
            <table class="tablas" border="1">
                <thead>
                    <tr>
                        <th><p width="5px">NÚM</p></th>
                        <th><p width="20px">CURSO</p></th>
                        <th><p width="20px">CLAVE DTA</p></th>
                        <th><p width="20px">INSTRUCTOR</p></th>
                        <th><p width="20px">FECHA DE INICIO/TERMINO/HORARIO</p></th>
                        <th><p width="10px">FOLIOS</p></th>
                        <th><p width="5px">TOTAL</p></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($tabla_contenido as $key => $curso)
                    <tr>
                        <td>{{($key < 9 ? '0' : '') . ($key + 1)}}</td>
                        <td>{{$curso->curso}}</td>
                        <td>{{$curso->clave}}</td>
                        <td>{{$curso->nombre}}</td>
                        <td>DEL {{ \Carbon\Carbon::createFromFormat('Y-m-d', $curso->inicio)->format('d/m/Y') }}
                            AL {{ \Carbon\Carbon::createFromFormat('Y-m-d', $curso->termino)->format('d/m/Y') }}
                            HORARIO: {{date("H:i", strtotime($curso->hini))}} A {{date("H:i", strtotime($curso->hfin))}} HRS.</td>
                        {{-- <td align="center">{{$curso->primer_folio.' - '.$curso->ultimo_folio}}</td> --}}
                        <td align="center">
                            @for ($i = 0; $i < count($rango_folios[$key]); $i++)
                                @if ($i % 2 == 0)
                                    {{'A'.$rango_folios[$key][$i].' -'}}
                                @else
                                    {{'A'.$rango_folios[$key][$i]}}
                                @endif
                            @endfor
                        </td>
                        <td>{{$curso->cantidad_folios}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <p style="font-size: 14px">Esperando contar con su apoyo y sin otro particular, quedo de usted.  </p>
        </div>
        <div class="contenido">
            ATENTAMENTE
            <br><br><br><br>
            <span class="color_text firmau">{{$data->dunidad}}</span> <br>
            <span class="color_text firmau">{{$data->pdunidad.' '.$unidad}}</span>
        </div>
        <br>
        <div class="textofin">
            <p class="delet_space_p">C.C.P. ARCHIVO</p>
            <p class="delet_space_p">{{isset($dta_certificacion) ? $dta_certificacion : 'dato requerido'}}
                TITULAR DEL DEPARTAMENTO DE CERTIFICACIÓN Y CONTROL DE LA DIRECCIÓN TÉCNICA ACADÉMICA.</p>
        </div>
    </div>


@endsection

@section('script_content_js')
    {{-- <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(73, 760, "Pág. $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }
    </script> --}}
@endsection

