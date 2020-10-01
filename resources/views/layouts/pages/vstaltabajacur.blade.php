<!-- Creado por Orlando Chávez -->
@extends('theme.sivyc.layout')
@section('title', 'Alta/Baja de Instructor | Sivyc Icatech')
<head>
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
                            @if ($available->CHK_TUXTLA == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_tuxtla" name='chk_tuxtla' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_tuxtla" name='chk_tuxtla'>
                            @endif
                            <label class="custom-control-label" for="chk_tuxtla"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Tapachula</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_TAPACHULA == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_tapachula" name='chk_tapachula' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_tapachula" name='chk_tapachula'>
                            @endif
                            <label class="custom-control-label" for="chk_tapachula"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Comitán</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_COMITAN == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_comitan" name='chk_comitan' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_comitan" name='chk_comitan'>
                            @endif
                            <label class="custom-control-label" for="chk_comitan"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Reforma</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_REFORMA == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_reforma" name='chk_reforma' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_reforma" name='chk_reforma'>
                            @endif
                            <label class="custom-control-label" for="chk_reforma"></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Tonalá</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_TONALA == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_tonala" name='chk_tonala' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_tonala" name='chk_tonala'>
                            @endif
                            <label class="custom-control-label" for="chk_tonala"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Villaflores</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_VILLAFLORES == TRUE)
                        <input type="checkbox" class="custom-control-input" id="chk_villaflores" name='chk_villaflores' checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="chk_villaflores" name='chk_villaflores'>
                        @endif
                        <label class="custom-control-label" for="chk_villaflores"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Jiquipilas</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_JIQUIPILAS == TRUE)
                        <input type="checkbox" class="custom-control-input" id="chk_jiquipilas" name='chk_jiquipilas' checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="chk_jiquipilas" name='chk_jiquipilas'>
                        @endif
                        <label class="custom-control-label" for="chk_jiquipilas"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Catazajá</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_CATAZAJA == TRUE)
                            <input type="checkbox" class="custom-control-input" id="chk_catazaja" name='chk_catazaja' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_catazaja" name='chk_catazaja'>
                            @endif
                            <label class="custom-control-label" for="chk_catazaja"></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Yajalón</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_YAJALON == TRUE)
                            <input type="checkbox" class="custom-control-input" id="chk_yajalon" name='chk_yajalon' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_yajalon" name='chk_yajalon'>
                            @endif
                            <label class="custom-control-label" for="chk_yajalon"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>San Cristóbal</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_SAN_CRISTOBAL == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_san_cristobal" name='chk_san_cristobal' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_san_cristobal" name='chk_san_cristobal'>
                            @endif
                            <label class="custom-control-label" for="chk_san_cristobal"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Chiapa de Corzo</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_CHIAPA_DE_CORZO == TRUE)
                        <input type="checkbox" class="custom-control-input" id="chk_chiapa_de_corzo" name='chk_chiapa_de_corzo' checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="chk_chiapa_de_corzo" name='chk_chiapa_de_corzo'>
                        @endif
                        <label class="custom-control-label" for="chk_chiapa_de_corzo"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Motozintla</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_MOTOZINTLA == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_motozintla" name='chk_motozintla' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_motozintla" name='chk_motozintla'>
                            @endif
                            <label class="custom-control-label" for="chk_motozintla"></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Berriozabal</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_BERRIOZABAL == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_berriozabal" name='chk_berriozabal' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_berriozabal" name='chk_berriozabal'>
                            @endif
                            <label class="custom-control-label" for="chk_berriozabal"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Pijijiapan</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_PIJIJIAPAN == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_pijijiapan" name='chk_pijijiapan' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_pijijiapan" name='chk_pijijiapan'>
                            @endif
                            <label class="custom-control-label" for="chk_pijijiapan"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Jitotol</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_JITOTOL == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_jitotol" name='chk_jitotol' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_jitotol" name='chk_jitotol'>
                            @endif
                            <label class="custom-control-label" for="chk_jitotol"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>La Concordia</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_LA_CONCORDIA == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_la_concordia" name='chk_la_concordia' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_la_concordia" name='chk_la_concordia'>
                            @endif
                            <label class="custom-control-label" for="chk_la_concordia"></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Venustiano Carranza</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_VENUSTIANO_CARRANZA == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_venustiano_carranza" name='chk_venustiano_carranza' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_venustiano_carranza" name='chk_venustiano_carranza'>
                            @endif
                            <label class="custom-control-label" for="chk_venustiano_carranza"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Tila</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_TILA == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_tila" name='chk_tila' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_tila" name='chk_tila'>
                            @endif
                            <label class="custom-control-label" for="chk_tila"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Teopisca</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_TEOPISCA == TRUE)
                            <input type="checkbox" class="custom-control-input" id="chk_teopisca" name='chk_teopisca' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_teopisca" name='chk_teopisca'>
                            @endif
                            <label class="custom-control-label" for="chk_teopisca"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Ocosingo</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_OCOSINGO == TRUE)
                            <input type="checkbox" class="custom-control-input" id="chk_ocosingo" name='chk_ocosingo' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_ocosingo" name='chk_ocosingo'>
                            @endif
                            <label class="custom-control-label" for="chk_ocosingo"></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Cintalapa</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_CINTALAPA == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_cintalapa" name='chk_cintalapa' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_cintalapa" name='chk_cintalapa'>
                            @endif
                            <label class="custom-control-label" for="chk_cintalapa"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Copainalá</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_COPAINALA == TRUE)
                            <input type="checkbox" class="custom-control-input" id="chk_copainala" name='chk_copainala' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_copainala" name='chk_copainala'>
                            @endif
                            <label class="custom-control-label" for="chk_copainala"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Soyaló</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_SOYALO == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_soyalo" name='chk_soyalo' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_soyalo" name='chk_soyalo'>
                            @endif
                            <label class="custom-control-label" for="chk_soyalo"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Ángel Albino Corzo</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_ANGEL_ALBINO_CORZO == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_angel_albino_corzo" name='chk_angel_albino_corzo' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_angel_albino_corzo" name='chk_angel_albino_corzo'>
                            @endif
                            <label class="custom-control-label" for="chk_angel_albino_corzo"></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Arriaga</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_ARRIAGA == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_arriaga" name='chk_arriaga' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_arriaga" name='chk_arriaga'>
                            @endif
                            <label class="custom-control-label" for="chk_arriaga"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Juárez</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_JUAREZ == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_juarez" name='chk_juarez' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_juarez" name='chk_juarez'>
                            @endif
                            <label class="custom-control-label" for="chk_juarez"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Pichucalco</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_PICHUCALCO == TRUE)
                            <input type="checkbox" class="custom-control-input" id="chk_pichucalco" name='chk_pichucalco' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_pichucalco" name='chk_pichucalco'>
                            @endif
                            <label class="custom-control-label" for="chk_pichucalco"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Simojovel</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_SIMOJOVEL == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_simojovel" name='chk_simojovel' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_simojovel" name='chk_simojovel'>
                            @endif
                            <label class="custom-control-label" for="chk_simojovel"></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Mapastepec</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_MAPASTEPEC == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_mapastepec" name='chk_mapastepec' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_mapastepec" name='chk_mapastepec'>
                            @endif
                            <label class="custom-control-label" for="chk_mapastepec"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Villa Corzo</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_VILLA_CORZO == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_villa_corzo" name='chk_villa_corzo' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_villa_corzo" name='chk_villa_corzo'>
                            @endif
                            <label class="custom-control-label" for="chk_villa_corzo"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Cacahoatán</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_CACAHOTAN == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_cacahoatan" name='chk_cacahoatan' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_cacahoatan" name='chk_cacahoatan'>
                            @endif
                            <label class="custom-control-label" for="chk_cacahoatan"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Once de Abril</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_ONCE_DE_ABRIL == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_once_de_abril" name='chk_once_de_abril' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_once_de_abril" name='chk_once_de_abril'>
                            @endif
                            <label class="custom-control-label" for="chk_once_de_abril"></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Tuxtla Chico</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_TUXTLA_CHICO == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_tuxtla_chico" name='chk_tuxtla_chico' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_tuxtla_chico" name='chk_tuxtla_chico'>
                            @endif
                            <label class="custom-control-label" for="chk_tuxtla_chico"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Oxchuc</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_OXCHUC == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_oxchuc" name='chk_oxchuc' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_oxchuc" name='chk_oxchuc'>
                            @endif
                            <label class="custom-control-label" for="chk_oxchuc"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Chamula</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_CHAMULA == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_chamula" name='chk_chamula' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_chamula" name='chk_chamula'>
                            @endif
                            <label class="custom-control-label" for="chk_chamula"></label>
                        </td>
                        <td style="vertical-align:bottom;"><strong>Ostuacán</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_OSTUACAN == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_ostuacan" name='chk_ostuacan' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_ostuacan" name='chk_ostuacan'>
                            @endif
                            <label class="custom-control-label" for="chk_ostuacan"></label>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:bottom;"><strong>Palenque</strong></td>
                        <td class="custom-checkbox checkbox-xl" id='choice-td'>
                            @if ($available->CHK_PALENQUE == TRUE)
                                <input type="checkbox" class="custom-control-input" id="chk_palenque" name='chk_palenque' checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="chk_palenque" name='chk_palenque'>
                            @endif
                            <label class="custom-control-label" for="chk_palenque"></label>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-success">Confirmar Alta/Baja</button>
                        <input type="hidden" name="id_available" id='id_available' value="{{$available->id}}">
                    </div>
                    <div class="pull-left">
                        <a class="btn btn-warning" href="{{URL::previous()}}">Regresar</a>
                    </div>
                </div>
            </div>
        </form>
    </section>
@stop
