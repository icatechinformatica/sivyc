<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Alta/Baja de Instructor | Sivyc Icatech')
<head>
    <script type="text/javascript">
        function determinador(checkbox)
        {
                document.getElementById(checkbox).checked = true;
        }
    </script>
    <style>
        .checkbox-xl .custom-control-label::before,
        .checkbox-xl .custom-control-label::after {
        top: 1.2rem;
        width: 1.85rem;
        height: 1.85rem;
        }

        .checkbox-xl .custom-control-label {
        padding-top: 23px;
        padding-left: 10px;
        }

        td {
            text-align: center; /* center checkbox horizontally */
            vertical-align: middle; /* center checkbox vertically */
        }
        #choice-td{
            background-color: lightsteelblue;
        }
        table {
            border: 1px solid;
            width: 200px;
        }
        tr {
            height: 65px;
        }
    </style>
</head>
@section('content')
    <section class="container g-py-40 g-pt-40 g-pb-0">
        <form method="POST" action="{{ route('curso-alta-baja-save') }}" id="alta-bajacurso">
            @csrf
            <div class="text-center">
                <h1>Alta/Baja de Curso<h1>
            </div>
            <br>
            <table  id="table-instructor" class="table table-bordered table-responsive-md">
                <caption>Catalogo de Unidades</caption>
                <thead>
                    <tr>
                        <th scope="col">Unidad</th>
                        <th scope="col">Disponible</th>
                        <th scope="col">Unidad</th>
                        <th scope="col">Disponible</th>
                        <th scope="col">Unidad</th>
                        <th scope="col">Disponible</th>
                        <th scope="col">Unidad</th>
                        <th scope="col">Disponible</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Tuxtla Gutiérrez</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_tuxtla" name='chk_tuxtla'>
                            <label class="custom-control-label" for="chk_tuxtla"></label>
                        </td>
                        @foreach ($available as $data)
                            @if ($data == 'TUXTLA')
                                <script type="text/javascript">determinador("chk_tuxtla");</script>
                            @endif
                        @endforeach
                        <td style="vertical-align:bottom;"><strong>Tapachula</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_tapachula" name='chk_tapachula'>
                            <label class="custom-control-label" for="chk_tapachula"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'TAPACHULA')
                                <script type="text/javascript">determinador("chk_tapachula");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Comitán</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_comitan" name='chk_comitan'>
                            <label class="custom-control-label" for="chk_comitan"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'COMITAN')
                                <script type="text/javascript">determinador("chk_comitan");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Reforma</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_reforma" name='chk_reforma'>
                            <label class="custom-control-label" for="chk_reforma"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'REFORMA')
                                <script type="text/javascript">determinador("chk_reforma");</script>
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Tonalá</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_tonala" name='chk_tonala'>
                            <label class="custom-control-label" for="chk_tonala"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'TONALA')
                                <script type="text/javascript">determinador("chk_tonala");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Villaflores</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_villaflores" name='chk_villaflores'>
                            <label class="custom-control-label" for="chk_villaflores"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'VILLAFLORES')
                                <script type="text/javascript">determinador("chk_villaflores");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Jiquipilas</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_jiquipilas" name='chk_jiquipilas'>
                            <label class="custom-control-label" for="chk_jiquipilas"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'JIQUIPILAS')
                                <script type="text/javascript">determinador("chk_jiquipilas");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Catazajá</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_catazaja" name='chk_catazaja'>
                            <label class="custom-control-label" for="chk_catazaja"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'CATAZAJA')
                                <script type="text/javascript">determinador("chk_catazaja");</script>
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Yajalón</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_yajalon" name='chk_yajalon'>
                            <label class="custom-control-label" for="chk_yajalon"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'YAJALON')
                                <script type="text/javascript">determinador("chk_yajalon");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>San Cristóbal</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_san_cristobal" name='chk_san_cristobal'>
                            <label class="custom-control-label" for="chk_san_cristobal"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'SAN CRISTOBAL')
                                <script type="text/javascript">determinador("chk_san_cristobal");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Chiapa de Corzo</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_chiapa_de_corzo" name='chk_chiapa_de_corzo'>
                            <label class="custom-control-label" for="chk_chiapa_de_corzo"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'CHIAPA DE CORZO')
                                <script type="text/javascript">determinador("chk_chiapa_de_corzo");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Motozintla</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_motozintla" name='chk_motozintla'>
                            <label class="custom-control-label" for="chk_motozintla"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'MOTOZINTLA')
                                <script type="text/javascript">determinador("chk_motozintla");</script>
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Berriozabal</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_berriozabal" name='chk_berriozabal'>
                            <label class="custom-control-label" for="chk_berriozabal"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'BERRIOZABAL')
                                <script type="text/javascript">determinador("chk_berriozabal");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Pijijiapan</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_pijijiapan" name='chk_pijijiapan'>
                            <label class="custom-control-label" for="chk_pijijiapan"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'PIJIJIAPAN')
                                <script type="text/javascript">determinador("chk_pijijiapan");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Jitotol</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_jitotol" name='chk_jitotol'>
                            <label class="custom-control-label" for="chk_jitotol"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'JITOTOL')
                                <script type="text/javascript">determinador("chk_jitotol");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>La Concordia</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_la_concordia" name='chk_la_concordia'>
                            <label class="custom-control-label" for="chk_la_concordia"></label>
                        </td>
                        @foreach ( $available as $data)
                        @if ($data == 'LA CONCORDIA')
                        <script type="text/javascript">determinador("chk_la_concordia");</script>
                    @endif
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Venustiano Carranza</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_venustiano_carranza" name='chk_venustiano_carranza'>
                            <label class="custom-control-label" for="chk_venustiano_carranza"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'VENUSTIANO CARRANZA')
                                <script type="text/javascript">determinador("chk_venustiano_carranza");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Tila</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_tila" name='chk_tila'>
                            <label class="custom-control-label" for="chk_tila"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'TILA')
                                <script type="text/javascript">determinador("chk_tila");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Teopisca</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_teopisca" name='chk_teopisca'>
                            <label class="custom-control-label" for="chk_teopisca"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'TEOPISCA')
                                <script type="text/javascript">determinador("chk_teopisca");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Ocosingo</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_ocosingo" name='chk_ocosingo'>
                            <label class="custom-control-label" for="chk_ocosingo"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'OCOSINGO')
                                <script type="text/javascript">determinador("chk_ocosingo");</script>
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Cintalapa</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_cintalapa" name='chk_cintalapa'>
                            <label class="custom-control-label" for="chk_cintalapa"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'CINTALAPA')
                                <script type="text/javascript">determinador("chk_cintalapa");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Copainalá</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_copainala" name='chk_copainala'>
                            <label class="custom-control-label" for="chk_copainala"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'COPAINALA')
                                <script type="text/javascript">determinador("chk_copainala");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Soyaló</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_soyalo" name='chk_soyalo'>
                            <label class="custom-control-label" for="chk_soyalo"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'SOYALO')
                                <script type="text/javascript">determinador("chk_soyalo");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Ángel Albino Corzo</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_angel_albino_corzo" name='chk_angel_albino_corzo'>
                            <label class="custom-control-label" for="chk_angel_albino_corzo"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'ANGEL ALBINO CORZO')
                                <script type="text/javascript">determinador("chk_angel_albino_corzo");</script>
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Arriaga</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_arriaga" name='chk_arriaga'>
                            <label class="custom-control-label" for="chk_arriaga"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'ARRIAGA')
                                <script type="text/javascript">determinador("chk_arriaga");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Juárez</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_juarez" name='chk_juarez'>
                            <label class="custom-control-label" for="chk_juarez"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'JUAREZ')
                                <script type="text/javascript">determinador("chk_juarez");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Pichucalco</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_pichucalco" name='chk_pichucalco'>
                            <label class="custom-control-label" for="chk_pichucalco"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'PICHUCALCO')
                                <script type="text/javascript">determinador("chk_pichucalco");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Simojovel</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_simojovel" name='chk_simojovel'>
                            <label class="custom-control-label" for="chk_simojovel"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'SIMOJOVEL')
                                <script type="text/javascript">determinador("chk_simojovel");</script>
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Mapastepec</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_mapastepec" name='chk_mapastepec'>
                            <label class="custom-control-label" for="chk_mapastepec"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'MAPASTEPEC')
                                <script type="text/javascript">determinador("chk_mapastepec");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Villa Corzo</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_villa_corzo" name='chk_villa_corzo'>
                            <label class="custom-control-label" for="chk_villa_corzo"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'VILLA CORZO')
                                <script type="text/javascript">determinador("chk_villa_corzo");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Cacahoatán</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_cacahoatan" name='chk_cacahoatan'>
                            <label class="custom-control-label" for="chk_cacahoatan"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'CACAHOATAN')
                                <script type="text/javascript">determinador("chk_cacahoatan");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Once de Abril</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_once_de_abril" name='chk_once_de_abril'>
                            <label class="custom-control-label" for="chk_once_de_abril"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'ONCE DE ABRIL')
                                <script type="text/javascript">determinador("chk_once_de_abril");</script>
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Tuxtla Chico</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_tuxtla_chico" name='chk_tuxtla_chico'>
                            <label class="custom-control-label" for="chk_tuxtla_chico"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'TUXTLA CHICO')
                                <script type="text/javascript">determinador("chk_tuxtla_chico");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Oxchuc</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_oxchuc" name='chk_oxchuc'>
                            <label class="custom-control-label" for="chk_oxchuc"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'OXCHUC')
                                <script type="text/javascript">determinador("chk_oxchuc");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Chamula</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_chamula" name='chk_chamula'>
                            <label class="custom-control-label" for="chk_chamula"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'CHAMULA')
                                <script type="text/javascript">determinador("chk_chamula");</script>
                            @endif
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Ostuacán</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_ostuacan" name='chk_ostuacan'>
                            <label class="custom-control-label" for="chk_ostuacan"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'OSTUACAN')
                                <script type="text/javascript">determinador("chk_ostuacan");</script>
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Palenque</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_palenque" name='chk_palenque'>
                            <label class="custom-control-label" for="chk_palenque"></label>
                        </td>
                        @foreach ( $available as $data)
                            @if ($data == 'PALENQUE')
                                <script type="text/javascript">determinador("chk_palenque");</script>
                            @endif
                        @endforeach
                    </tr>
                </tbody>
            </table>
            <br>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-success">Confirmar Alta/Baja</button>
                        <input type="hidden" name="id_available" id='id_available' value="{{$id}}">
                    </div>
                    <div class="pull-left">
                        <a class="btn btn-warning" href="{{URL::previous()}}">Regresar</a>
                    </div>
                </div>
            </div>
        </form>
    </section>
@stop
