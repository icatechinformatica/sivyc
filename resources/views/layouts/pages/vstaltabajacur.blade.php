<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Alta/Baja de Instructor | Sivyc Icatech')
<head>
    <script type="text/javascript">
        function determinador(variable, constante, checkbox)
        {
            if (variable == constante)
            {
                document.getElementById(checkbox).checked = true;
            }
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
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "TUXTLA", "chk_tuxtla");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Tapachula</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_tapachula" name='chk_tapachula'>
                            <label class="custom-control-label" for="chk_tapachula"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "TAPACHULA", "chk_tapachula");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Comitán</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_comitan" name='chk_comitan'>
                            <label class="custom-control-label" for="chk_comitan"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "COMITAN", "chk_comitan");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Reforma</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_reforma" name='chk_reforma'>
                            <label class="custom-control-label" for="chk_reforma"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "REFORMA", "chk_reforma");</script>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Tonalá</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_tonala" name='chk_tonala'>
                            <label class="custom-control-label" for="chk_tonala"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "TONALA", "chk_tonala");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Villaflores</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_villaflores" name='chk_villaflores'>
                            <label class="custom-control-label" for="chk_villaflores"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "VILLAFLORES", "chk_villaflores");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Jiquipilas</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_jiquipilas" name='chk_jiquipilas'>
                            <label class="custom-control-label" for="chk_jiquipilas"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "JIQUIPILAS", "chk_jiquipilas");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Catazajá</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_catazaja" name='chk_catazaja'>
                            <label class="custom-control-label" for="chk_catazaja"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "CATAZAJA", "chk_catazaja");</script>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Yajalón</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_yajalon" name='chk_yajalon'>
                            <label class="custom-control-label" for="chk_yajalon"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "YAJALON", "chk_yajalon");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>San Cristóbal</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_san_cristobal" name='chk_san_cristobal'>
                            <label class="custom-control-label" for="chk_san_cristobal"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "SAN_CRISTOBAL", "chk_san_cristobal");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Chiapa de Corzo</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_chiapa_de_corzo" name='chk_chiapa_de_corzo'>
                            <label class="custom-control-label" for="chk_chiapa_de_corzo"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "CHIAPA_DE_CORZO", "chk_chiapa_de_corzo");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Motozintla</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_motozintla" name='chk_motozintla'>
                            <label class="custom-control-label" for="chk_motozintla"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "MOTOZINTLA", "chk_motozintla");</script>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Berriozabal</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_berriozabal" name='chk_berriozabal'>
                            <label class="custom-control-label" for="chk_berriozabal"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "BERRIOZABAL", "chk_berriozabal");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Pijijiapan</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_pijijiapan" name='chk_pijijiapan'>
                            <label class="custom-control-label" for="chk_pijijiapan"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "PIJIJIAPAN", "chk_pijijiapan");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Jitotol</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_jitotol" name='chk_jitotol'>
                            <label class="custom-control-label" for="chk_jitotol"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "JITOTOL", "chk_jitotol");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>La Concordia</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_la_concordia" name='chk_la_concordia'>
                            <label class="custom-control-label" for="chk_la_concordia"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "LA_CONCORDIA", "chk_la_concordia");</script>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Venustiano Carranza</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_venustiano_carranza" name='chk_venustiano_carranza'>
                            <label class="custom-control-label" for="chk_venustiano_carranza"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "VENUSTIANO_CARRANZA", "chk_venustiano_carranza");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Tila</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_tila" name='chk_tila'>
                            <label class="custom-control-label" for="chk_tila"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "TILA", "chk_tila");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Teopisca</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_teopisca" name='chk_teopisca'>
                            <label class="custom-control-label" for="chk_teopisca"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "TEOPISCA", "chk_teopisca");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Ocosingo</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_ocosingo" name='chk_ocosingo'>
                            <label class="custom-control-label" for="chk_ocosingo"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "OCOSINGO", "chk_ocosingo");</script>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Cintalapa</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_cintalapa" name='chk_cintalapa'>
                            <label class="custom-control-label" for="chk_cintalapa"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "CINTALAPA", "chk_cintalapa");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Copainalá</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_copainala" name='chk_copainala'>
                            <label class="custom-control-label" for="chk_copainala"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "COPAINALA", "chk_copainala");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Soyaló</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_soyalo" name='chk_soyalo'>
                            <label class="custom-control-label" for="chk_soyalo"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "SOYALO", "chk_soyalo");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Ángel Albino Corzo</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_angel_albino_corzo" name='chk_angel_albino_corzo'>
                            <label class="custom-control-label" for="chk_angel_albino_corzo"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "ANGEL_ALBINO_CORZO", "chk_angel_albino_corzo");</script>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Arriaga</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_arriaga" name='chk_arriaga'>
                            <label class="custom-control-label" for="chk_arriaga"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "ARRIAGA", "chk_arriaga");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Juárez</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_juarez" name='chk_juarez'>
                            <label class="custom-control-label" for="chk_juarez"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "JUAREZ", "chk_juarez");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Pichucalco</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_pichucalco" name='chk_pichucalco'>
                            <label class="custom-control-label" for="chk_pichucalco"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "PICHUCALCO", "chk_pichucalco");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Simojovel</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_simojovel" name='chk_simojovel'>
                            <label class="custom-control-label" for="chk_simojovel"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "SIMOJOVEL", "chk_simojovel");</script>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Mapastepec</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_mapastepec" name='chk_mapastepec'>
                            <label class="custom-control-label" for="chk_mapastepec"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "MAPASTEPEC", "chk_mapastepec");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Villa Corzo</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_villa_corzo" name='chk_villa_corzo'>
                            <label class="custom-control-label" for="chk_villa_corzo"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "VILLA_CORZO", "chk_villa_corzo");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Cacahoatán</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_cacahoatan" name='chk_cacahoatan'>
                            <label class="custom-control-label" for="chk_cacahoatan"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "CACAHOATAN", "chk_cacahoatan");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Once de Abril</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_once_de_abril" name='chk_once_de_abril'>
                            <label class="custom-control-label" for="chk_once_de_abril"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "ONCE_DE_ABRIL", "chk_once_de_abril");</script>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Tuxtla Chico</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_tuxtla_chico" name='chk_tuxtla_chico'>
                            <label class="custom-control-label" for="chk_tuxtla_chico"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "TUXTLA_CHICO", "chk_tuxtla_chico");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Oxchuc</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_oxchuc" name='chk_oxchuc'>
                            <label class="custom-control-label" for="chk_oxchuc"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "OXCHUC", "chk_oxchuc");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Chamula</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_chamula" name='chk_chamula'>
                            <label class="custom-control-label" for="chk_chamula"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "CHAMULA", "chk_chamula");</script>
                        @endforeach

                        <td style="vertical-align:bottom;"><strong>Ostuacán</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_ostuacan" name='chk_ostuacan'>
                            <label class="custom-control-label" for="chk_ostuacan"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "OSTUACAN", "chk_ostuacan");</script>
                        @endforeach
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Palenque</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            <input type="checkbox" class="custom-control-input" id="chk_palenque" name='chk_palenque'>
                            <label class="custom-control-label" for="chk_palenque"></label>
                        </td>
                        @foreach ( $available as $data)
                            <script type="text/javascript">determinador($data, "PALENQUE", "chk_palenque");</script>
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
