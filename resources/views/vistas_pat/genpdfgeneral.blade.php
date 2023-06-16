<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.formatos.hlayout_pat')
@section('title', 'PDF AVANCE | SIVyC Icatech')
@section('css')
    <style>
         @page { margin-bottom: 107px; } /*107*/
        .tb {width: 100%; border-collapse: collapse; text-align: center; }
        .tb th{border: 1px solid black; padding: 1px; font-weight: normal; font-size: 9px;}
        .tb td{border: 1px solid black; padding: 1px; font-size: 9px; height: auto;}
        .tablaf { border-collapse: collapse; width: 100%; font-size: 12px !important; text-align: center; margin-top:0px;}
        .tablaf tr, .tablaf td {padding: 0px}
        p {margin:5px; padding:0px;font-size: 10px;}
        #titulo{position: fixed; top: 55px;}
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
        <div id="fontotext"><b style="background: black;">"INFORME MENSUAL DE AVANCE PROGRAMÁTICO DEL PROGRAMA ANUAL DE TRABAJO {{date('Y')}}"</b></div>
        <h3>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS</h3>
    </div>
@endsection
@section('body')
    @for ($i = 0; $i < count($areas); $i++)
        <div id="para">
            <strong>DIRECCIÓN: </strong> {{$direcciones[$i]->nom_direc != 'DIRECCIÓN' ? $direcciones[$i]->nom_direc : $areas[$i]->nom_dpto}}
            <br>
            <strong>DPTO: </strong> {{$direcciones[$i]->nom_direc != 'DIRECCIÓN' ? $areas[$i]->nom_dpto : $direcciones[$i]->nom_direc}}
        </div>
        <br><br><br><br>

        {{-- Nueva tabla v2 para ver que tal imprimer --}}
        <table class="tb" style="">
            <thead>
                <tr style="background: #EAECEE;">
                <th rowspan="3" width="35px">No <br> FUN</th>
                <th rowspan="3" width="140px">FUNCIONES</th>
                <th rowspan="3" width="140px">ACTIVIDADES</th>
                <th rowspan="3" width="45px">UNIDAD <br> DE <br> MEDIDA</th>
                <th rowspan="3">TIPO <br> DE <br> U.M</th>
                <th rowspan="3">META <br> ANUAL</th>

                <th colspan="4">MES QUE INFORMA   <strong style="background: #000; color:white; padding: 5px; margin-left: 10px;">{{strtoupper($fechas_pdf_global[$i]['mes_avance'])}}</strong></th>
                <th colspan="4">ACUMULADO AL MES QUE INFORMA</th>
                <th rowspan="3">EXPLICACIÓN A LAS DESVIACIONES</th>
                </tr>
                <tr style="background: #EAECEE;">
                <th colspan="2">METAS</th>
                <th colspan="2">DESVIACIÓN</th>
                <th colspan="2">METAS</th>
                <th colspan="2">DESVIACIÓN</th>
                </tr>
                <tr style="background: #EAECEE;">
                    <th>PROGR.</th>
                    <th>ALCANZA</th>
                    <th>NUM.</th>
                    <th>%</th>
                    <th>PROGR.</th>
                    <th>ALCANZA</th>
                    <th>NUM.</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                @for ($f = 0; $f < count($funciones_global[$i]); $f++)
                    @php $conta = $f; @endphp
                    @if (count($proced_global[$i][$f]) == 1))
                        <tr>
                            <td>{{$conta+1}}</td>
                            <td align="left">{{$funciones_global[$i][$f]->fun_proc}}</td> {{-- nomb de la fun --}}
                            <td align="left">{{$proced_global[$i][$f][0]->fun_proc}}</td> {{-- nom del proced --}}
                            <td>({{$proced_global[$i][$f][0]->numero}}) {{$proced_global[$i][$f][0]->unidadm}}</td>
                            <td>{{$proced_global[$i][$f][0]->tipo_unidadm}}</td>
                            <td>{{$proced_global[$i][$f][0]->total}}</td>

                            <td>{{$mes_meta_avance_global[$i][$f][0]['meta']}}</td>
                            <td>{{$mes_meta_avance_global[$i][$f][0]['avance']}}</td>
                            <td>{{$mes_meta_avance_global[$i][$f][0]['resta']}}</td>
                            <td>{{$mes_meta_avance_global[$i][$f][0]['porcentaje']}}</td>

                            <td>{{$mes_meta_avance_global[$i][$f][0]['metas_acum']}}</td>
                            <td>{{$mes_meta_avance_global[$i][$f][0]['avance_acum']}}</td>
                            <td>{{$mes_meta_avance_global[$i][$f][0]['resta_acum']}}</td>
                            <td>{{$mes_meta_avance_global[$i][$f][0]['porcentaje_acum']}}</td>

                            <td align="left">{{$mes_meta_avance_global[$i][$f][0]['exp_desviacion']}}</td>
                        </tr>
                    @endif

                    @if (count($proced_global[$i][$f]) > 1)

                        @for ($p = 0; $p < count($proced_global[$i][$f]); $p++)
                            @php
                                $divi = count($proced_global[$i][$f]) / 2;
                                $res = round($divi, 0);
                                $ultimo = count($proced_global[$i][$f]);
                            @endphp
                            <tr class="prueba">
                                <td
                                    @if ($p == $ultimo-1)
                                        style="border-top: none; border-left: 1px solid #000 !important; border-bottom: 1px; "
                                    @else
                                        style="border-left: 1px solid #000 !important; border-top: none; border-bottom: none;"
                                    @endif
                                >
                                    {{$p == $res-1 ? $conta+1 : ''}}
                                </td>

                                <td align="left"
                                    @if ($p == $ultimo-1)
                                        style="border-top: none; border-left: 1px solid #000 !important; border-bottom: 1px;"
                                    @else
                                        style="border-left: 1px solid #000 !important; border-bottom: none; border-top: none;"
                                    @endif
                                >
                                    @if ($p == $res-1) {{$funciones_global[$i][$f]->fun_proc}} @endif
                                </td>

                                <td align="left">{{$proced_global[$i][$f][$p]->fun_proc}}</td>
                                <td>({{$proced_global[$i][$f][$p]->numero}}) {{$proced_global[$i][$f][$p]->unidadm}}</td>
                                <td>{{$proced_global[$i][$f][$p]->tipo_unidadm}}</td>
                                <td>{{$proced_global[$i][$f][$p]->total}}</td>

                                <td>{{$mes_meta_avance_global[$i][$f][$p]['meta']}}</td>
                                <td>{{$mes_meta_avance_global[$i][$f][$p]['avance']}}</td>
                                <td>{{$mes_meta_avance_global[$i][$f][$p]['resta']}}</td>
                                <td>{{$mes_meta_avance_global[$i][$f][$p]['porcentaje']}}</td>

                                <td>{{$mes_meta_avance_global[$i][$f][$p]['metas_acum']}}</td>
                                <td>{{$mes_meta_avance_global[$i][$f][$p]['avance_acum']}}</td>
                                <td>{{$mes_meta_avance_global[$i][$f][$p]['resta_acum']}}</td>
                                <td>{{$mes_meta_avance_global[$i][$f][$p]['porcentaje_acum']}}</td>

                                <td align="left">{{$mes_meta_avance_global[$i][$f][$p]['exp_desviacion']}}</td>
                            </tr>
                        @endfor
                    @endif

                @endfor

            </tbody>
        </table>

        <br><br><br>
        {{-- aqui termina la tabla --}}
        <br/>
        <br/>
        {{-- Firmas --}}
        <table class="tablaf">
            <tr>
                <td>
                    <p>ELABORÓ</p><br><br><br>
                    <p>{{isset($areas) ? $areas[$i]->funcionario : ''}}</p>
                    <p>_________________________________________</p>
                    <p><b>{{isset($areas) ? $areas[$i]->cargo : ''}}</b></p>
                </td>
                <td>
                    <p></p>
                    <p></p>
                    <p></p>
                    <p>Fecha: {{isset($fechas_pdf_global[$i]['fecha_avance']) ? $fechas_pdf_global[$i]['fecha_avance'] : ''}}</p>
                </td>
                <td>
                    <p>Vo. Bo.</p><br><br><br>
                    <p>{{isset($direcciones) ? $direcciones[$i]->funcionario : ''}}</p>
                    <p>_________________________________________</p>
                    <p><b>{{isset($direcciones) ? $direcciones[$i]->cargo : ''}}</b></p>
                </td>
            </tr>
        </table>
        <div style="page-break-after: always"></div>
    @endfor

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
    <script>
        // $(document).ready(function(){});
    </script>
@endsection
