<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.formatos.hlayout_pat')
@section('title', 'PAT-ICATECH-002.1 | SIVyC Icatech')
@section('css')
    <style>
         @page { margin-bottom: 20px; } /*107*/
        .tb {width: 100%; border-collapse: collapse; text-align: center; }
        .tb th{border: 1px solid black; padding: 1px; font-weight: normal; font-size: 10px;}
        .tb td{border: 1px solid black; padding: 1px; font-size: 10px; height: auto;}
        .tablaf { border-collapse: collapse; width: 100%; font-size: 12px !important; text-align: center; margin-top:0px;}
        .tablaf tr, .tablaf td {padding: 0px}
        p {margin:5px; padding:0px;font-size: 10px;}
        #titulo{position: fixed; top: 55px; left: 0; right: 0; text-align: center;}
        #titulo h2{padding:0px; margin:0px 0px 2px 0px; font-size: 13px; font-weight:normal;}
        #titulo h3{padding:0px; margin:0px; font-size: 12px; font-weight:normal;}
        #titulo table{position: fixed; top: 93px;}
        #para {position: relative; top: 15px; height:auto; width:60%; font-size: 14px; margin-bottom:-15;}
        #fontotext { color: white; font-size: 12px; font-weight:bold; font-style: italic;}
        .showlast{border-top: none; border-left: 1px solid #000 !important; border-bottom: 1px;}
        .showborders{border-left: 1px solid #000 !important; border-top: none; border-bottom: none;}
        .prueba{page-break-inside: avoid;}
    </style>
@endsection
@section('header')
    <div id="titulo">
        <h3>SUBSECRETARÍA DE EDUCACIÓN MEDIA SUPERIOR</h3>
        <h3>DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO</h3>
        <div id="fontotext"><b style="background: black;">"CALENDARIZADO ANUAL PROGRAMÁTICO DEL PROGRAMA ANUAL DE TRABAJO
            @if ($global_ejercicio != strval(date('Y')))
                {{' '.$global_ejercicio}}"
            @else
                {{' '.date('Y')}}"
            @endif
        </b></div>
        <h3>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS</h3>
    </div>
@endsection
@section('body')
        <div id="para">
            <strong>Dirección: </strong> {{isset($nom_direc_depto) ? $nom_direc_depto->direccion : ''}}
            <br>
            <strong>Departamento: </strong> {{isset($nom_direc_depto) ? $nom_direc_depto->depto : ''}}
        </div>

        <br><br><br><br>

        {{-- TABLA V2 --}}
        <table class="tb" style="">
            <thead>
                <tr style="background: #EAECEE;">
                  <th rowspan="2" width="20px">No <br> FUN</th>
                  <th rowspan="2" width="190px">FUNCIONES</th>
                  <th rowspan="2" width="190px">ACTIVIDADES</th>
                  <th rowspan="2" width="65px">UNIDAD <br> DE <br> MEDIDA</th>
                  <th rowspan="2" width="40px">TIPO <br> DE <br> U.M</th>
                  <th rowspan="2" width="40px">META <br> ANUAL</th>
                  <th colspan="12" width="240px">CALENDARIZACIÓN</th>
                  <th rowspan="2" width="70px">OBSERVACIÓN</th>
                </tr>
                <tr style="background: #EAECEE;">
                  <th width="20px">I</th>
                  <th width="20px">II</th>
                  <th width="20px">III</th>
                  <th width="20px">IV</th>
                  <th width="20px">V</th>
                  <th width="20px">VI</th>
                  <th width="20px">VII</th>
                  <th width="20px">VIII</th>
                  <th width="20px">IX</th>
                  <th width="20px">X</th>
                  <th width="20px">XI</th>
                  <th width="20px">XII</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($funciones as $key => $item)
                @php $conta = $key; @endphp
                    @if (count($procedimientos[$key]) == 1))
                        <tr>
                            <td>{{$conta+1}}</td>
                            <td align="left" style="padding: 3px;">{{$item->fun_proc}}</td>
                            <td align="left">{{$procedimientos[$key][0]->fun_proc}}</td>
                            <td>({{$procedimientos[$key][0]->numero}}) {{$procedimientos[$key][0]->unidadm}}</td>
                            <td>{{$procedimientos[$key][0]->tipo_unidadm}}</td>
                            <td>{{$procedimientos[$key][0]->total}}</td>
                            <td>{{$procedimientos[$key][0]->enero['meta']}}</td>
                            <td>{{$procedimientos[$key][0]->febrero['meta']}}</td>
                            <td>{{$procedimientos[$key][0]->marzo['meta']}}</td>
                            <td>{{$procedimientos[$key][0]->abril['meta']}}</td>
                            <td>{{$procedimientos[$key][0]->mayo['meta']}}</td>
                            <td>{{$procedimientos[$key][0]->junio['meta']}}</td>
                            <td>{{$procedimientos[$key][0]->julio['meta']}}</td>
                            <td>{{$procedimientos[$key][0]->agosto['meta']}}</td>
                            <td>{{$procedimientos[$key][0]->septiembre['meta']}}</td>
                            <td>{{$procedimientos[$key][0]->octubre['meta']}}</td>
                            <td>{{$procedimientos[$key][0]->noviembre['meta']}}</td>
                            <td>{{$procedimientos[$key][0]->diciembre['meta']}}</td>
                            <td>{{$procedimientos[$key][0]->observmeta}}</td>
                        </tr>
                    @endif

                    @if (count($procedimientos[$key]) > 1)

                        @for ($i = 0; $i < count($procedimientos[$key]); $i++)
                            @php
                                $divi = count($procedimientos[$key]) / 2;
                                $res = round($divi, 0);
                                $ultimo = count($procedimientos[$key]);
                            @endphp
                            <tr class="prueba">


                                <td
                                    @if ($i == $ultimo-1)
                                        style="border-top: none; border-left: 1px solid #000 !important; border-bottom: 1px; "
                                    @else
                                        style="border-left: 1px solid #000 !important; border-top: none; border-bottom: none;"
                                    @endif
                                >
                                    {{$i == $res-1 ? $conta+1 : ''}}

                                </td>

                                <td align="left"
                                    @if ($i == $ultimo-1)
                                        style="padding: 3px; border-top: none; border-left: 1px solid #000 !important; border-bottom: 1px;"
                                    @else
                                        style="padding: 3px; border-left: 1px solid #000 !important; border-bottom: none; border-top: none;"
                                    @endif
                                >
                                    @if ($i == $res-1) {{$item->fun_proc}} @endif
                                </td>

                                <td align="left">
                                    {{$procedimientos[$key][$i]->fun_proc}}
                                </td>
                                <td>({{$procedimientos[$key][$i]->numero}}) {{$procedimientos[$key][$i]->unidadm}}</td>
                                <td>{{$procedimientos[$key][$i]->tipo_unidadm}}</td>
                                <td>{{$procedimientos[$key][$i]->total}}</td>
                                <td>{{$procedimientos[$key][$i]->enero['meta']}}</td>
                                <td>{{$procedimientos[$key][$i]->febrero['meta']}}</td>
                                <td>{{$procedimientos[$key][$i]->marzo['meta']}}</td>
                                <td>{{$procedimientos[$key][$i]->abril['meta']}}</td>
                                <td>{{$procedimientos[$key][$i]->mayo['meta']}}</td>
                                <td>{{$procedimientos[$key][$i]->junio['meta']}}</td>
                                <td>{{$procedimientos[$key][$i]->julio['meta']}}</td>
                                <td>{{$procedimientos[$key][$i]->agosto['meta']}}</td>
                                <td>{{$procedimientos[$key][$i]->septiembre['meta']}}</td>
                                <td>{{$procedimientos[$key][$i]->octubre['meta']}}</td>
                                <td>{{$procedimientos[$key][$i]->noviembre['meta']}}</td>
                                <td>{{$procedimientos[$key][$i]->diciembre['meta']}}</td>
                                <td>{{$procedimientos[$key][$i]->observmeta}}</td>
                            </tr>
                        @endfor
                    @endif
                @endforeach
            </tbody>
        </table>

        <table class="tablaf" style="margin-top: 20px;">
            <tr>
                <td style="line-height: 1;">
                    <p style="margin-bottom: 35px;">ELABORÓ</p>
                    <p>___<u>{{isset($area_org) ? $area_org->titulo.' '.$area_org->funcionario : ''}}</u>___</p>
                    <p><b>{{isset($area_org) ? $area_org->cargo : ''}}</b></p>
                </td>
                <td>
                    <p></p>
                    <p></p>
                    <p></p>
                    <p>Fecha: {{isset($fecha_meta) ? $fecha_meta : ''}}</p>
                </td>
                <td style="line-height: 1;">
                    <p style="margin-bottom: 35px;">Vo. Bo.</p>
                    <p>___<u>{{isset($org) ? $org->titulo.' '.$org->funcionario : ''}}</u>___</p>
                    <p><b>{{isset($org) ? $org->cargo : ''}}</b></p>
                </td>
            </tr>
        </table>


{{-- $pdf->text(40, 530, "Pág. $PAGE_NUM de $PAGE_COUNT", $font, 8); pienso que 530 es la parte del eje y--}}
@endsection
@section('js')
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(40, 570, "Pág. $PAGE_NUM de $PAGE_COUNT", $font, 8);
            ');
        }

    </script>
@endsection
