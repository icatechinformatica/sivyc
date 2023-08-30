{{-- Realizado por Jose Luis Morenoa Arcos --}}
@extends('theme.formatos.vlayout')
@section('title', 'Soporte de Entrega | SIVyC Icatech')

@section('content_script_css')
    <style>
        .contenedor{
            margin-top: 2cm;
            margin-left: 1cm;
            margin-right: 1cm;
            /* margin-bottom: 50px; */
            /* background-color: rgb(192, 184, 184); */
            margin-bottom: 1cm;
        }
        .bloque_uno{
            font-weight: bold;
            font-size: 14px;
        }
        .bloque_dos{
            font-weight: bold;
            font-size: 13px;
        }
        .contenido{
            font-size: 14px;
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
        .tablas{border-collapse: collapse;width: 100%;}
        .tablas tr{font-size: 9px; border: gray 1px solid; text-align: center; padding: 2px;}
        .tablas th{font-size: 12px; border: gray 1px solid; text-align: center; padding: 2px;}
        .colortexto{
            color: #0000;
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
        $cargo_fun = $organismo->cargo_fun;
    }
@endphp
@section('content')
    <div class="contenedor">
        <div class="bloque_uno" align="right">
            <p class="delet_space_p">UNIDAD DE CAPACITACIÓN {{strtoupper($unidad)}}</p>
            <p class="delet_space_p">OFICIO NÚM. {{$numficio}}</p>
            <p class="delet_space_p">{{$municipio}}, CHIAPAS; <span style="color: black;">{{strtoupper($fecha_comp)}}</span></p>
        </div>
        <br><br><br>
        <div class="bloque_dos" align="left">
            <p class="delet_space_p">C.
                @if (count($partes_titu) == 2)
                    {{$partes_titu[0]}}
                @else
                    {{$nombre_titular}}
                @endif
            </p>
            <p class="delet_space_p">
                @if (count($partes_titu) == 2)
                    {{$partes_titu[1]}}
                @else
                    {{$cargo_fun}}
                @endif
                {{-- {{$organismo->cargo_fun != null ? $organismo->cargo_fun : 'CARGO REQUERIDO'}} --}}
            </p>
            <p class="delet_space_p">{{$municipio}}, CHIAPAS</p>
        </div>
        <br>
        <div class="contenido" align="justify">
            Reciba un cordial saludo y respetuosamente me dirijo a usted, con la finalidad de hacer entrega física de <span style="color: red">{{$total_folios}}</span>
            constancias originales que corresponden a la modalidad de
            <span>
                @if($tabla_contenido[0]->mod == 'EXT')
                    Extensión,
                @elseif($tabla_contenido[0]->mod == 'CAE')
                    Capacitación Acelerada Especifica (CAE),
                @else
                    FALTA MODALIDAD
                @endif
            </span>los cuales
            han sido agrupados en <span class="">{{$total_cursos}}</span> cursos diferentes e impartidos por esta Unida de Capacitación a mi cargo, durante el
            periodo <span class="">{{$rango_mes->mes_minimo}} – {{$rango_mes->mes_maximo}}</span> de la presente anualidad.
            <br><br>
            De igual forma adjunto original del formato RIACD-02 de certificación
            correspondiente a cada curso, para su conocimiento y validación de los folios de constancias que pertenecen al número
            asignado a los alumnos, al momento de ser entregadas las constancias originales para su verificación.
            <br><br>
            A continuación,
            se describen los <span class="">{{$total_cursos}}</span> cursos del total de <span class="">{{$total_folios}}</span>
            constancias, que se encuentran listas para ser entregadas a los alumnos:
        </div>
        <br>
        <div class="tabla_alumnos">
            <table class="tablas" border="1">
                <thead>
                    <tr>
                        <th><b>NÚM</b></th>
                        <th><b>CLAVE</b></th>
                        <th><b>CURSO</b></th>
                        <th><b>MODALIDAD</b></th>
                        {{-- <th><b>FECHA DE INICIO/TERMINO/HORARIO</b></th> --}}
                        <th><b>FOLIOS</b></th>
                        <th><b>TOTAL</b></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($tabla_contenido as $key => $curso)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$curso->clave}}</td>
                        <td>{{$curso->curso}}</td>
                        <td>{{$curso->tcapacitacion}}</td>
                        {{-- <td>DEL {{$curso->format('d/m/Y')}} AL 16/06/2023 HORARIO: 09:00 A 11:00 HRS.</td> --}}
                        <td align="center">{{$curso->primer_folio.' - '.$curso->ultimo_folio}}</td>
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
            <span class="">{{$data->dunidad}}</span> <br>
            <span class="">{{$data->pdunidad}}</span>
            <div class="textofin">
                <br>
                C.C.P. ARCHIVO/MINUTARIO
                <br><br>
                ELABORÓ: <span class="">{{$data->realizo}}</span>
                <br>
                VALIDÓ: <span class="">{{$data->valido}}</span>
            </div>
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

