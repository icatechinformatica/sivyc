<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Metas y Avances | SIVyC Icatech')
    <!--seccion-->

@section('content_script_css')
    <style>
        * {
            box-sizing: border-box;
        }

        /* Estilo del loader */
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fondo semi-transparente */
            z-index: 9999; /* Asegura que esté por encima de otros elementos */
            display: none; /* Ocultar inicialmente */
        }

        #loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            border: 6px solid #fff;
            border-top: 6px solid #621132;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }
            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        #tabla thead tr th{
            padding: 3px;
            margin: 0px;
            vertical-align: middle;
        }
        #tabla tbody tr td{
            padding: 3px;
            margin: 0px;
            vertical-align: middle;
            /* font-size: 12px; */
        }

        #tabla input {
            width: 28px;
            padding-left: 7px !important;
            border: none;
            background-color: transparent !important;
        }
        #tabla textarea {
            border: none;
            background-color: transparent !important;
        }
        .diagonal {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            transform: rotate(-45deg);
            /* transform-origin: top left; */
        }
        .letter {
            width: 6px;
            text-align: center;
        }
        /* tamaño del input busqueda */
        #busqueda_funcion{
            width: 40% !important;
        }
        .color_car {
            background-color: #880e4f;
        }
        .fa-heart {
        color: white;
        }

        .card-header{
            font-variant: small-caps;
            background-color: #621132;
            color: white;
            margin: 1.7% 1.7% 1% 1.7%;
            padding: 1.3% 39px 1.3% 39px;
            font-style: normal;
            font-size: 22px;
        }

        .card-body{
            margin: 1%;
            margin-left: 1.7%;
            margin-right: 1.7%;
            /* padding: 55px; */
            -webkit-box-shadow: 0 8px 6px -6px #999;
            -moz-box-shadow: 0 8px 6px -6px #999;
            box-shadow: 0 8px 6px -6px #999;
        }
        .card-body.card-msg{
            background-color: yellow;
            margin: .5% 1.7% .5% 1.7%;
            padding: .5% 5px .5% 25px;
        }

        body { background-color: #E6E6E6; }

        .btn, .btn:focus{ color: white; background: #12322b; font-size: 14px; border-color: #12322b; margin: 0 5px 0 5px; padding: 10px 13px 10px 13px; }
        .btn:hover { color: white; background:#2a4c44; border-color: #12322b; }

        /* Se agrega cuando el usuario este inacitvo */
        .fondo_celda {
            /* rgb(129, 129, 134); */
            background-color:  #d4d4d4;
        }
        .colortext{
            color: #8b8888;
            font-weight: bold;
        }
        /* cuando el usuario este activo */
        .activo_celda {
            /* background-color: #ffff; */
        }
        .texto_activo{
            color: #000;
            font-weight: bold;
        }
        /* de la caja que muestra la fecha */
        .fondo_fecha{
            background-color: #5f0f30;
        }
        /*Deshabilitamos la parte de forzar mayusculas*/
        input[type=text],
        select,
        textarea {
            text-transform: none !important;
        }
        /* Estilo de la tabla */
        .table-container {
            max-height: 500px;
            overflow-y: scroll;
        }

        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-container thead {
            position: sticky;
            top: 0;
            background-color: #a19f9f;
            color: #1a1919;
            font-weight: bold;
        }

    </style>

    <link rel="stylesheet" type="text/css" href="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/css/jasny-bootstrap.min.css" />

    {{-- Producción --}}
    <link rel="stylesheet" type="text/css" href="https://resources.firma.chiapas.gob.mx:8443/tools/library/signedjs-generic/css/firma.css ">

    {{-- prueba --}}
    {{-- <link rel="stylesheet" type="text/css" href="https://resources.firma.chiapas.gob.mx:8443/tools/library/signedjs-generic-prueba/css/firma.css"> --}}

@endsection

@section('content')
    {{-- Loader --}}
    <div id="loader-overlay">
        <div id="loader"></div>
    </div>

    <div class="card-header">
        @if (isset($dif_perfil))
            Validación de Metas y Avances PAT
        @else
            Registro de Metas y Avances PAT
        @endif
    </div>

    <div class="card card-body" style="min-height:450px;">
        @if ($message = Session::get('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <strong>{{ $message }}</strong>
            </div>
        @endif

        {{-- Para el firmado electronico --}}
        <div class="d-none" id="vHTMLSignature"></div>
        <input type="hidden" id="cadena_original" value="{{ !empty($consul_efirma['cadena_original']) ? $consul_efirma['cadena_original'] : $consul_efirma_avance['cadena_original_ava'] }}">
        <input type="hidden" id="curp_firmante" value="{{$consul_efirma['curp_activo']}}">
        <input type="hidden" id="token" value="{{$consul_efirma['token_efirma']}}">
        <input type="hidden" id="idFile" value="{{ !empty($consul_efirma['idEfirmaMeta']) ? $consul_efirma['idEfirmaMeta'] : $consul_efirma_avance['idEfirmaAvance'] }}">

        <div class="">

            {{-- Mostrar horario --}}
            <div class="alert alert-info py-1" role="alert">
                <strong>PERIODO DE ENTREGA</strong>
                @if($datos_status_meta[0] == 'activo')
                    <span class="my-2"> ({{$datos_status_meta[3][0]}}) al ({{$datos_status_meta[3][1]}})</span>
                @endif

                @if($datos_status_avance[0] == 'activo')
                    <span class="my-2">({{$datos_status_avance[3][0]}}) al ({{$datos_status_avance[3][1]}})</span>
                @endif

                @if ($datos_status_meta[0] == 'inactivo' && $datos_status_avance[0] == 'inactivo')
                    <span class="my-2">Inactivo</span>
                @endif
            </div>

            {{-- DATOS MOSTRADOS DESPUES DEL ENCABEZADO --}}
            <div class="row">
                <div class="col-lg-12 row">
                    <div class="col-lg-6">
                        <div class="d-flex flex-column align-items-start">
                            @if (isset($dif_perfil))
                                <div class="pull-left">
                                    <b><span class="badge badge-success">Planeación</span></b>
                                </div>
                            @endif

                            {{-- contenedor del select de organismos cuando son mas de uno --}}
                            @if (isset($array_organismos) && count($array_organismos) > 1)
                                <div class="mb-2 col-12 px-0">
                                    <select name="org_new" id="org_new" class="form-control" onchange="cambiar_organismo()">
                                        @foreach ($array_organismos as $arrItem)
                                            <option {{$organismo == $arrItem->id ? 'selected' : ''}} value="{{$arrItem->id}}">{{$arrItem->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="pull-left">
                                <h5><b>Dirección :</b> {{isset($org->nombre) ? $org->nombre : ''}}</h5>
                            </div>
                            <div class="pull-left">
                                <h5><b>Area/Depto :</b> {{isset($area_org->nombre) ? $area_org->nombre : ''}}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="d-flex flex-column align-items-end">
                            @if (!isset($dif_perfil))
                                @if (count($ejercicio) > 1)
                                    <div class="pull-right">
                                        <div class="row">
                                            <h5><b>Ejercicio: </b></h5>
                                            <form action="" id="form_eje" class="ml-2 mr-3">
                                                <select name="sel_ejercicio" id="" class="form-control-sm" onchange="cambioEjercicio()">
                                                    @foreach ($ejercicio as $anioeje)
                                                        <option {{$anioeje == $anio_eje ? 'selected' : '' }} value="{{$anioeje}}">{{$anioeje}}</option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            <div class="pull-right">
                                <h5><b>Fecha actual :</b> {{isset($fechaNow) ? strtoupper($fechaNow) : ''}}</h5>
                            </div>
                            <div class="row mb-2">
                                <div class="pull-right mr-3">
                                    <h5><b>Asunto :</b>
                                        {{$datos_status_meta[0] == 'activo' ? 'Meta' : ''}}
                                        {{$datos_status_avance[0] == 'activo' ? 'Avances - '.$datos_status_avance[2] : ''}}

                                    </h5>
                                </div>
                                <div class="pull-right ml-2">
                                    <h5><b>Estado :</b>
                                        {{$datos_status_meta[0] == 'activo' ? $datos_status_meta[1] : ''}}
                                        {{$datos_status_avance[0] == 'activo' ? $datos_status_avance[1] : ''}}
                                    </h5>
                                </div>
                            </div>

                            <div class="card bg-warning" style="width: 17rem;">
                                <div class="card-body px-2 py-2">
                                    {{-- <p class="card-text text-center mb-2">Descargar pdfs firmados</p> --}}
                                    <div class="d-flex justify-content-center">
                                        {{-- <i  class="far fa-file-pdf ml-2 text-danger"></i> --}}
                                        <select name="selOpenLink" id="selOpenLink" class="form-control" onchange="pdfOpenfirm()">
                                            <option value="" disabled selected >Descargar PDF Oficializado</option>
                                            @if ($fecha_meta_avance->fecha_meta['urldoc_firm'] != '')
                                                <optgroup label="METAS PDF">
                                                    <option value="{{$fecha_meta_avance->fecha_meta['urldoc_firm']}}">Meta Anual</option>
                                                </optgroup>
                                            @endif
                                            <optgroup label="AVANCES PDF">
                                                @for ($i = 0; $i < count($mesGlob); $i++)
                                                    @if ($fecha_meta_avance->fechas_avance[$mesGlob[$i]]['urldoc_firmav'] != '')
                                                        <option value="{{$fecha_meta_avance->fechas_avance[$mesGlob[$i]]['urldoc_firmav']}}"> {{ucfirst($mesGlob[$i])}}</option>
                                                    @endif
                                                @endfor
                                            </optgroup>
                                            <optgroup label="FIRMA ELECTRONICA">
                                                @if (!empty($consul_efirma['id_meta_valid']))
                                                    <option value="{{$consul_efirma['id_meta_valid']}}">Meta Anual</option>
                                                @endif
                                                @if (count($meses_validados) > 0)
                                                    @foreach ($meses_validados as $key => $valor)
                                                        <option value="{{ $valor }}">Avance {{$key}} </option>
                                                    @endforeach
                                                @endif
                                                {{-- <option value="">Enero</option> --}}
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                              </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- TABLA DE METAS Y AVANCES --}}
            <div style="overflow:auto; margin:0px; padding:0px;" class="table-container mt-2">
            <table id="tabla" class="table table-bordered table-striped table-wrapper">
                {{-- nuevo encabezado para ver que tal como funciona --}}
                <thead>
                    <tr>
                      <th rowspan="2" class="text-center">No.</th>
                      <th rowspan="2" class="text-center"> <p style="width: 250px;">FUNCIONES</p></th>
                      <th rowspan="2" class="text-center"> <p style="width: 250px;">PROCEDIMIENTOS</p></th>
                      <th rowspan="2" class="text-center">UNIDAD <br> DE <br> MEDIDA</th>
                      <th rowspan="2" class="text-center">TIPO <br> DE <br> U.M</th>
                      <th rowspan="2" class="text-center">META <br> ANUAL</th>
                      <th colspan="2" class="text-center">Ene</th>
                      <th colspan="2" class="text-center">Feb</th>
                      <th colspan="2" class="text-center">Mar</th>
                      <th colspan="2" class="text-center">Abril</th>
                      <th colspan="2" class="text-center">May</th>
                      <th colspan="2" class="text-center">Jun</th>
                      <th colspan="2" class="text-center">Jul</th>
                      <th colspan="2" class="text-center">Ago</th>
                      <th colspan="2" class="text-center">Sep</th>
                      <th colspan="2" class="text-center">Oct</th>
                      <th colspan="2" class="text-center">Nov</th>
                      <th colspan="2" class="text-center">Dic</th>
                      <th rowspan="2" class="text-center">
                        {{$datos_status_meta[0] == 'activo' ? 'OBSERVACIONES' : ''}}
                        {{$datos_status_avance[0] == 'activo' ? 'EXPLICACIÓN A LAS DESVIACIONES': ''}}
                      </th>
                        <th rowspan="2" class="text-center">OBSERVACIONES PLANEACIÓN</th>
                    </tr>
                    <tr>
                        @for ($i = 1; $i <= 12; $i++)
                        <th scope="col" class="text-center">
                            <div class="diagonal mt--2" style="font-weight:bold;">
                                <span class="letter small" style="font-weight:bold;">M</span>
                                <span class="letter small ml-1" style="font-weight:bold;">e</span>
                                <span class="letter small" style="font-weight:bold;">t</span>
                                <span class="letter small" style="font-weight:bold;">a</span>
                            </div>
                        </th>
                        <th scope="col" class="text-center">
                            <div class="diagonal">
                                <span class="letter small" style="font-weight:bold;">A</span>
                                <span class="letter small" style="font-weight:bold;">v</span>
                                <span class="letter small" style="font-weight:bold;">a</span>
                                <span class="letter small" style="font-weight:bold;">n</span>
                                <span class="letter small" style="font-weight:bold;">c</span>
                                <span class="letter small" style="font-weight:bold;">e</span>
                            </div>
                        </th>
                    @endfor
                    </tr>

                </thead>
                {{-- aqui termina el encabezado --}}
                <tbody>

                        @php
                            $conta = 0; $consec = 0;
                        @endphp
                        @for ($i = 0; $i < count($datos); $i++)
                        @php
                            $conta = $i + 1; $consec +=1;
                        @endphp
                            <tr>
                                <td scope="row">{{$consec}}</td>
                                <td><strong>{{$datos[$i]}}</strong></td>
                                {{-- Solo se ejecuta una vez para rellenar con un procedimiento en primera fila con la funcion--}}
                                @if (isset($datos[$conta][0]->fun_proc))
                                    <td>{{$datos[$conta][0]->fun_proc}}</td>
                                    <td class="text-center">({{$datos[$conta][0]->numero}}) {{$datos[$conta][0]->unidadm}}</td>
                                    <td class="text-center">{{$datos[$conta][0]->tipo_unidadm}}</td>
                                    <td class="text-center">{{$datos[$conta][0]->total}}</td>
                                    {{-- valores --}}
                                        @php
                                            $enero_json = $datos[$conta][0]->enero;$feb_json = $datos[$conta][0]->febrero;$mar_json = $datos[$conta][0]->marzo;$abril_json = $datos[$conta][0]->abril;
                                            $mayo_json = $datos[$conta][0]->mayo;$jun_json = $datos[$conta][0]->junio;$jul_json = $datos[$conta][0]->julio;$ago_json = $datos[$conta][0]->agosto;
                                            $sep_json = $datos[$conta][0]->septiembre;$oct_json = $datos[$conta][0]->octubre;$nov_json = $datos[$conta][0]->noviembre;$dic_json = $datos[$conta][0]->diciembre;
                                            $json_ene = json_decode($enero_json); $json_feb = json_decode($feb_json); $json_mar = json_decode($mar_json); $json_abr = json_decode($abril_json);
                                            $json_may = json_decode($mayo_json); $json_jun = json_decode($jun_json); $json_jul = json_decode($jul_json); $json_ago = json_decode($ago_json);
                                            $json_sep = json_decode($sep_json); $json_oct = json_decode($oct_json); $json_nov = json_decode($nov_json); $json_dic = json_decode($dic_json);
                                        @endphp
                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="enero_meta_{{$datos[$conta][0]->id}}" value="{{$json_ene->meta}}" required maxlength="5"></td>
                                        <td class="{{$datos_status_avance[2] == 'enero' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'enero' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'enero' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="enero-avance-{{$datos[$conta][0]->id}}" value="{{$json_ene->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="febrero_meta_{{$datos[$conta][0]->id}}" value="{{$json_feb->meta}}" required maxlength="5"></td>
                                        <td class="{{$datos_status_avance[2] == 'febrero' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'febrero' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'febrero' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="febrero-avance-{{$datos[$conta][0]->id}}" value="{{$json_feb->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="marzo_meta_{{$datos[$conta][0]->id}}" value="{{$json_mar->meta}}" required maxlength="5"></td>
                                        <td class="{{$datos_status_avance[2] == 'marzo' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'marzo' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'marzo' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="marzo-avance-{{$datos[$conta][0]->id}}" value="{{$json_mar->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="abril_meta_{{$datos[$conta][0]->id}}" value="{{$json_abr->meta}}" required maxlength="5"></td>
                                        <td class="{{$datos_status_avance[2] == 'abril' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'abril' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{ $datos_status_avance[2] == 'abril' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }}  name="abril-avance-{{$datos[$conta][0]->id}}" value="{{$json_abr->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="mayo_meta_{{$datos[$conta][0]->id}}" value="{{$json_may->meta}}" required maxlength="5"></td>
                                        <td class="{{$datos_status_avance[2] == 'mayo' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'mayo' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'mayo' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="mayo-avance-{{$datos[$conta][0]->id}}" value="{{$json_may->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="junio_meta_{{$datos[$conta][0]->id}}"value="{{$json_jun->meta}}" required maxlength="5"></td>
                                        <td class="{{$datos_status_avance[2] == 'junio' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'junio' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'junio' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="junio-avance-{{$datos[$conta][0]->id}}" value="{{$json_jun->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="julio_meta_{{$datos[$conta][0]->id}}" value="{{$json_jul->meta}}" required maxlength="5"></td>
                                        <td class="{{$datos_status_avance[2] == 'julio' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'julio' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'julio' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="julio-avance-{{$datos[$conta][0]->id}}" value="{{$json_jul->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="agosto_meta_{{$datos[$conta][0]->id}}" value="{{$json_ago->meta}}" required maxlength="5"></td>
                                        <td class="{{$datos_status_avance[2] == 'agosto' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'agosto' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'agosto' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="agosto-avance-{{$datos[$conta][0]->id}}" value="{{$json_ago->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="septiembre_meta_{{$datos[$conta][0]->id}}" value="{{$json_sep->meta}}" required maxlength="5"></td>
                                        <td class="{{$datos_status_avance[2] == 'septiembre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'septiembre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'septiembre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="septiembre-avance-{{$datos[$conta][0]->id}}" value="{{$json_sep->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="octubre_meta_{{$datos[$conta][0]->id}}" value="{{$json_oct->meta}}" required maxlength="5"></td>
                                        <td class="{{$datos_status_avance[2] == 'octubre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'octubre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'octubre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="octubre-avance-{{$datos[$conta][0]->id}}" value="{{$json_oct->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="noviembre_meta_{{$datos[$conta][0]->id}}" value="{{$json_nov->meta}}" required maxlength="5"></td>
                                        <td class="{{$datos_status_avance[2] == 'noviembre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'noviembre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'noviembre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="noviembre-avance-{{$datos[$conta][0]->id}}" value="{{$json_nov->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="diciembre_meta_{{$datos[$conta][0]->id}}" value="{{$json_dic->meta}}" required maxlength="5"></td>
                                        <td class="{{$datos_status_avance[2] == 'diciembre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'diciembre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'diciembre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="diciembre-avance-{{$datos[$conta][0]->id}}" value="{{$json_dic->avance}}" required maxlength="4"></td>
                                        <td>
                                            <textarea class="area_desv" name="desviacion_{{$datos[$conta][0]->id}}" id="" cols="18" rows="2">
                                                @if ($datos_status_avance[2] == 'enero') {{$json_ene->expdesviaciones}} @endif @if ($datos_status_avance[2] == 'febrero') {{$json_feb->expdesviaciones}} @endif
                                                @if ($datos_status_avance[2] == 'marzo') {{$json_mar->expdesviaciones}} @endif @if ($datos_status_avance[2] == 'abril') {{$json_abr->expdesviaciones}} @endif
                                                @if ($datos_status_avance[2] == 'mayo') {{$json_may->expdesviaciones}} @endif @if ($datos_status_avance[2] == 'junio') {{$json_jun->expdesviaciones}} @endif
                                                @if ($datos_status_avance[2] == 'julio') {{$json_jul->expdesviaciones}} @endif @if ($datos_status_avance[2] == 'agosto') {{$json_ago->expdesviaciones}} @endif
                                                @if ($datos_status_avance[2] == 'septiembre') {{$json_sep->expdesviaciones}} @endif @if ($datos_status_avance[2] == 'octubre') {{$json_oct->expdesviaciones}} @endif
                                                @if ($datos_status_avance[2] == 'noviembre') {{$json_nov->expdesviaciones}} @endif @if ($datos_status_avance[2] == 'diciembre') {{$json_dic->expdesviaciones}} @endif
                                                @if ($datos_status_meta[0] != 'inactivo') {{$datos[$conta][0]->observmeta}} @endif
                                            </textarea>
                                        </td>
                                        <td><textarea name="planeacion_{{$datos[$conta][0]->id}}" id="" cols="18" rows="{{$datos[$conta][0]->observaciones != '' ? '5' : '1'}}">{{$datos[$conta][0]->observaciones}}</textarea></td>

                                @endif

                            </tr>
                            @php $i++; @endphp
                            @for ($x = 1; $x < count($datos[$i]); $x++)
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>{{$datos[$i][$x]->fun_proc}}</td>
                                    <td class="text-center">({{$datos[$i][$x]->numero}}) {{$datos[$i][$x]->unidadm}}</td>
                                    <td class="text-center">{{$datos[$i][$x]->tipo_unidadm}}</td>
                                    <td class="text-center">{{$datos[$i][$x]->total}}</td>
                                    {{-- se imprime metas y avances  --}}
                                        @php
                                            $enero_json = $datos[$i][$x]->enero;$feb_json = $datos[$i][$x]->febrero;$mar_json = $datos[$i][$x]->marzo;$abril_json = $datos[$i][$x]->abril;
                                            $mayo_json = $datos[$i][$x]->mayo;$jun_json = $datos[$i][$x]->junio;$jul_json = $datos[$i][$x]->julio;$ago_json = $datos[$i][$x]->agosto;
                                            $sep_json = $datos[$i][$x]->septiembre;$oct_json = $datos[$i][$x]->octubre;$nov_json = $datos[$i][$x]->noviembre;$dic_json = $datos[$i][$x]->diciembre;
                                            $json_ene = json_decode($enero_json); $json_feb = json_decode($feb_json); $json_mar = json_decode($mar_json); $json_abr = json_decode($abril_json);
                                            $json_may = json_decode($mayo_json); $json_jun = json_decode($jun_json); $json_jul = json_decode($jul_json); $json_ago = json_decode($ago_json);
                                            $json_sep = json_decode($sep_json); $json_oct = json_decode($oct_json); $json_nov = json_decode($nov_json); $json_dic = json_decode($dic_json);


                                        @endphp
                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="enero_meta_{{$datos[$i][$x]->id}}" value="{{$json_ene->meta}}" required maxlength="5"></td>
                                            <td class="{{$datos_status_avance[2] == 'enero' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'enero' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'enero' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="enero-avance-{{$datos[$i][$x]->id}}" value="{{$json_ene->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="febrero_meta_{{$datos[$i][$x]->id}}" value="{{$json_feb->meta}}" required maxlength="5"></td>
                                            <td class="{{$datos_status_avance[2] == 'febrero' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'febrero' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'febrero' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="febrero-avance-{{$datos[$i][$x]->id}}" value="{{$json_feb->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="marzo_meta_{{$datos[$i][$x]->id}}" value="{{$json_mar->meta}}" required maxlength="5"></td>
                                            <td class="{{$datos_status_avance[2] == 'marzo' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'marzo' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'marzo' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="marzo-avance-{{$datos[$i][$x]->id}}" value="{{$json_mar->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="abril_meta_{{$datos[$i][$x]->id}}" value="{{$json_abr->meta}}" required maxlength="5"></td>
                                            <td class="{{$datos_status_avance[2] == 'abril' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'abril' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'abril' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }}  name="abril-avance-{{$datos[$i][$x]->id}}" value="{{$json_abr->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="mayo_meta_{{$datos[$i][$x]->id}}" value="{{$json_may->meta}}" required maxlength="5"></td>
                                            <td class="{{$datos_status_avance[2] == 'mayo' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'mayo' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'mayo' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="mayo-avance-{{$datos[$i][$x]->id}}" value="{{$json_may->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="junio_meta_{{$datos[$i][$x]->id}}" value="{{$json_jun->meta}}" required maxlength="5"></td>
                                            <td class="{{$datos_status_avance[2] == 'junio' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'junio' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'junio' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="junio-avance-{{$datos[$i][$x]->id}}" value="{{$json_jun->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="julio_meta_{{$datos[$i][$x]->id}}" value="{{$json_jul->meta}}" required maxlength="5"></td>
                                            <td class="{{$datos_status_avance[2] == 'julio' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'julio' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'julio' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="julio-avance-{{$datos[$i][$x]->id}}" value="{{$json_jul->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="agosto_meta_{{$datos[$i][$x]->id}}" value="{{$json_ago->meta}}" required maxlength="5"></td>
                                            <td class="{{$datos_status_avance[2] == 'agosto' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'agosto' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'agosto' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="agosto-avance-{{$datos[$i][$x]->id}}" value="{{$json_ago->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="septiembre_meta_{{$datos[$i][$x]->id}}" value="{{$json_sep->meta}}" required maxlength="5"></td>
                                            <td class="{{$datos_status_avance[2] == 'septiembre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'septiembre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'septiembre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="septiembre-avance-{{$datos[$i][$x]->id}}" value="{{$json_sep->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="octubre_meta_{{$datos[$i][$x]->id}}" value="{{$json_oct->meta}}" required maxlength="5"></td>
                                            <td class="{{$datos_status_avance[2] == 'octubre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'octubre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'octubre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="octubre-avance-{{$datos[$i][$x]->id}}" value="{{$json_oct->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="noviembre_meta_{{$datos[$i][$x]->id}}" value="{{$json_nov->meta}}" required maxlength="5"></td>
                                            <td class="{{$datos_status_avance[2] == 'noviembre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'noviembre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'noviembre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="noviembre-avance-{{$datos[$i][$x]->id}}" value="{{$json_nov->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="diciembre_meta_{{$datos[$i][$x]->id}}" value="{{$json_dic->meta}}" required maxlength="5"></td>
                                            <td class="{{$datos_status_avance[2] == 'diciembre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'diciembre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'diciembre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="diciembre-avance-{{$datos[$i][$x]->id}}" value="{{$json_dic->avance}}" required maxlength="4"></td>
                                            <td>
                                                <textarea class="area_desv" name="desviacion_{{$datos[$i][$x]->id}}" id="" cols="18" rows="2">
                                                    @if ($datos_status_avance[2] == 'enero') {{$json_ene->expdesviaciones}} @endif @if ($datos_status_avance[2] == 'febrero') {{$json_feb->expdesviaciones}} @endif
                                                    @if ($datos_status_avance[2] == 'marzo') {{$json_mar->expdesviaciones}} @endif @if ($datos_status_avance[2] == 'abril') {{$json_abr->expdesviaciones}} @endif
                                                    @if ($datos_status_avance[2] == 'mayo') {{$json_may->expdesviaciones}} @endif @if ($datos_status_avance[2] == 'junio') {{$json_jun->expdesviaciones}} @endif
                                                    @if ($datos_status_avance[2] == 'julio') {{$json_jul->expdesviaciones}} @endif @if ($datos_status_avance[2] == 'agosto') {{$json_ago->expdesviaciones}} @endif
                                                    @if ($datos_status_avance[2] == 'septiembre') {{$json_sep->expdesviaciones}} @endif @if ($datos_status_avance[2] == 'octubre') {{$json_oct->expdesviaciones}} @endif
                                                    @if ($datos_status_avance[2] == 'noviembre') {{$json_nov->expdesviaciones}} @endif @if ($datos_status_avance[2] == 'diciembre') {{$json_dic->expdesviaciones}} @endif
                                                    @if ($datos_status_meta[0] != 'inactivo') {{$datos[$i][$x]->observmeta}} @endif
                                                </textarea>
                                            </td>
                                            <td><textarea name="planeacion_{{$datos[$i][$x]->id}}" id="" cols="18" rows="{{$datos[$i][$x]->observaciones != '' ? '5' : '1'}}">{{$datos[$i][$x]->observaciones}}</textarea></td>
                                </tr>
                            @endfor

                        @endfor
                </tbody>
            </table>
            </div>

            {{-- Formularios para firma electronica --}}
            {{-- Generacion de xml meta anual --}}
            @if (!isset($dif_perfil))
                <form action="" method="post" id="frmGenXml">
                    @csrf
                    <input type="hidden" name="organismo" value="{{$organismo}}">
                    <input type="hidden" name="ejercicio" value="{{$anio_eje}}">
                    <input type="hidden" name="tipo_efirma" id="tipo_efirma" value="">
                    <input type="hidden" name="mesavance" id="mesavance" value="">
                </form>
                {{-- Firmar meta anual --}}
                <form action="{{route('pat.metavance.firmar.documento')}}" method="post" id="frm_firmar">
                    @csrf
                    <input type="hidden" name="fechaFirmado" id="fechaFirmado">
                    <input type="hidden" name="serieFirmante" id="serieFirmante">
                    <input type="hidden" name="firma" id="firma">
                    <input type="hidden" name="curp" id="curp">
                    <input type="hidden" name="certificado" id="certificado">
                    <input type="hidden" name="idDoc" id="idDoc">
                </form>
                {{-- sellar --}}
                <form id="frm_sellar" action="{{route('pat.metavance.sellar.documento')}}" method="post">
                    @csrf
                    <input type="hidden" name="txtIdFirmado" id="txtIdFirmado">
                    <input type="hidden" name="organismo" value="{{$organismo}}">
                    <input type="hidden" name="ejercicio" value="{{$anio_eje}}">
                    <input type="hidden" name="tipo_doc" id="tipo_doc" value="">
                </form>
            @endif



            @if (isset($datos) && count($datos) != 0)
                {{-- BOTONES USADOS TANTO PLANEACION COMO ORGANISMO --}}
                <div class="row mt-2">
                    <div class="col-lg-12 row mt-3">
                        <div class="col-lg-7">
                            <div class="d-flex flex-column align-items-start">
                                @if (!isset($dif_perfil))
                                    {{-- META ANUAL CARGA Y FIRMA DE DOCUMENTO --}}
                                    <div class="card w-100
                                        @if(!empty($fecha_meta_avance->fecha_meta['nomdoc_firm']) || $consul_efirma['status_doc'] == 'VALIDADO' )
                                            d-none
                                        @endif
                                    ">
                                        <div class="card-body my-0 pb-3 pt-2">

                                            {{-- Botones de efirma y tradicional --}}
                                            @if (empty($fecha_meta_avance->fecha_meta['mod_documento']))
                                                    <div class="mb-2 font-weight-bold">META ANUAL (CARGA DE DOCUMENTO / FIRMADO ELECTRONICO)</div>
                                                    <button class="btn" id="btnGenXml" onclick="generar_xml('META_ANUAL')">GENERAR EFIRMA</button>
                                                    <button class="btn" onclick="showTradicional(event,'meta','{{$organismo}}','{{$anio_eje}}')">CARGAR PDF</button>
                                            @endif

                                            {{-- Efirma --}}
                                            {{-- @dd($consul_efirma) --}}
                                            @if (isset($fecha_meta_avance->fecha_meta['mod_documento']) && $fecha_meta_avance->fecha_meta['mod_documento'] == 'efirma')
                                                @if ($consul_efirma['status_doc'] != 'VALIDADO')
                                                    <b class="">FIRMADO ELECTRONICO DEL CALENDARIZADO ANUAL PAT</b>
                                                    <div class="d-flex flex-row mt-2">
                                                        <div class="">
                                                            @if ($consul_efirma['status_firma'] != 'FIRMADO')
                                                                <button class="btn btn-info" onclick="openModal()">FIRMAR</button>
                                                            @endif
                                                            @if ($consul_efirma['pos_firm_activo'] == 0 && $consul_efirma['status_doc'] == 'EnFirma' && $consul_efirma['firmado_uno'] == 'SI' && $consul_efirma['firmado_dos'] == 'SI')
                                                                <button class="btn btn-info" onclick="sellarDocumento('{{$consul_efirma['idEfirmaMeta']}}', 'meta' )">SELLAR</button>
                                                            @endif
                                                        </div>
                                                        <div class="">
                                                            @if ($consul_efirma['status_doc'] != 'VALIDADO')
                                                                <button class="btn btn-danger" onclick="cancelUploadDocs(event, 'meta', 'efirma', '{{$anio_eje}}', '{{$organismo}}', '{{$consul_efirma['idEfirmaMeta']}}' )">CANCELAR</button>
                                                            @endif
                                                        </div>
                                                        <div class="pt-1 ml-2">
                                                            <a class="" href="{{route('pat.metavance.pdf.efirma', ['id' => $consul_efirma['idEfirmaMeta'] ]) }}" target="_blank">
                                                                <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="33px" height="33px">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <div class="d-flex flex-column ml-2">
                                                            <span style="font-size: 12px;" class="text-left font-weight-bold">{{$consul_efirma['firmante_uno']}} {{($consul_efirma['firmado_uno'] == 'SI') ? '( FIRMADO )' : '( FALTA FIRMA )'}}</span>
                                                            <span style="font-size: 12px;" class="text-left font-weight-bold">{{$consul_efirma['firmante_dos']}} {{($consul_efirma['firmado_dos'] == 'SI') ? '( FIRMADO )' : '( FALTA FIRMA )'}}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif

                                            {{-- Tradicional --}}
                                            <div class="form-inline
                                                @if(isset($fecha_meta_avance->fecha_meta['mod_documento']) && $fecha_meta_avance->fecha_meta['mod_documento'] == 'tradicional'
                                                && empty($fecha_meta_avance->fecha_meta['urldoc_firm']))
                                                @else
                                                    d-none
                                                @endif
                                            ">
                                                <input type="file" id="pdfInputMeta" accept=".pdf" style="display: none;" onchange="cargarNomFileMeta()">
                                                <input class="form-control" type="text" id="nomPdfMeta" onclick="document.getElementById('pdfInputMeta').click()" placeholder="PDF firmado de Metas">
                                                <a class="btn" id="btnEnvPdfMeta" onclick="upPdfMetaFirm(event, '{{$fecha_meta_avance->fecha_meta['nomdoc_firm']}}')"><i class="fas fa-cloud-upload-alt" aria-hidden="true"></i></a>
                                                <a class="btn btn-danger" onclick="cancelUploadDocs(event, 'meta', 'tradicional', '{{$anio_eje}}', '{{$organismo}}', '{{$consul_efirma['idEfirmaMeta']}}')">CANCELAR</a>
                                            </div>

                                        </div>
                                    </div>


                                    {{-- AVANCE MENSUAL CARGA Y FIRMA DE DOCUMENTO --}}
                                    <div class="card w-100
                                        @if($consul_efirma_avance['active_form_avance'] != 'activo')
                                            d-none
                                        @endif
                                    ">
                                        <div class="card-body my-0 pb-3 pt-2 {{$fecha_meta_avance->status_meta['validado'] != '1' ? 'd-none' : ''}}">
                                            <div class="col-12 mb-2 px-0">
                                                <b class="">AVANCE MENSUAL</b>
                                            </div>
                                            {{-- Si hay una documento pendiente por firmar entonces no mostrar esto de los botones --}}
                                            @if (empty($consul_efirma_avance['mes_activo']))
                                                <div class="row ml-3">
                                                    <div class="d-flex col-3 px-0">
                                                        <select class="form-control mr-2" name="select_mes" id="seldoc_mes">
                                                            @foreach ($meses_pendientes as $mes)
                                                                <option value="{{$mes}}"> {{$mes}} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="d-flex col-4 px-0">
                                                        <select class="form-control mr-2" name="" id="opciones_avance" onchange="opciones_avance()">
                                                            <option value="">Seleccionar</option>
                                                            <option value="carga_avance">Cargar documento</option>
                                                            <option value="efirma_avance">Firmado electronico</option>
                                                        </select>
                                                    </div>

                                                    <button class="btn d-none" id="btnGenXmlAv" onclick="generar_xml('AVANCE_MES')">GENERAR EFIRMA</button>
                                                </div>

                                            @else
                                            {{-- Mostrar botones de firmado --}}
                                                <b>EFIRMA AVANCE DEL MES DE {{strtoupper($consul_efirma_avance['mes_activo'])}}</b>
                                                <div class="d-flex flex-row mt-2">
                                                    <div class="">
                                                        @if ($consul_efirma_avance['status_firma_ava'] != 'FIRMADO')
                                                            <button class="btn btn-info" onclick="openModal()">FIRMAR</button>
                                                        @endif
                                                        @if ($consul_efirma_avance['pos_firm_activo_ava'] == 0 && $consul_efirma_avance['status_doc_ava'] == 'EnFirma' && $consul_efirma_avance['firmado_uno_ava'] == 'SI' && $consul_efirma_avance['firmado_dos_ava'] == 'SI')
                                                            <button class="btn btn-info" onclick="sellarDocumento('{{$consul_efirma_avance['idEfirmaAvance']}}', '{{$consul_efirma_avance['mes_activo']}}' )">SELLAR</button>
                                                        @endif
                                                    </div>
                                                    <div class="">
                                                        @if ($consul_efirma_avance['status_doc_ava'] != 'VALIDADO')
                                                            <button class="btn btn-danger" onclick="cancelUploadDocs(event, '{{'avance_'.$consul_efirma_avance['mes_activo']}}', 'efirma', '{{$anio_eje}}', '{{$organismo}}', '{{$consul_efirma_avance['idEfirmaAvance']}}' )">CANCELAR</button>
                                                        @endif
                                                    </div>
                                                    <div class="pt-1 ml-2">
                                                        <a class="" href="{{route('pat.metavance.pdf.efirma', ['id' => $consul_efirma_avance['idEfirmaAvance'] ]) }}" target="_blank">
                                                            <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="33px" height="33px">
                                                        </a>
                                                    </div>

                                                </div>
                                                <div class="mt-3">
                                                    <div class="d-flex flex-column ml-2">
                                                        <span style="font-size: 12px;" class="text-left font-weight-bold">1.- {{$consul_efirma_avance['firmante_uno_ava']}} {{($consul_efirma_avance['firmado_uno_ava'] == 'SI') ? '( FIRMADO )' : '( FALTA FIRMA )'}}</span>
                                                        <span style="font-size: 12px;" class="text-left font-weight-bold">2.- {{$consul_efirma_avance['firmante_dos_ava']}} {{($consul_efirma_avance['firmado_dos_ava'] == 'SI') ? '( FIRMADO )' : '( FALTA FIRMA )'}}</span>
                                                    </div>
                                                </div>

                                            @endif


                                            {{-- TRADICIONAL --}}
                                            <div class="col-12 mt-3 d-none" id="content_avance">
                                                <form method="POST" class="d-flex flex-row" enctype="multipart/form-data" action="" id="doc_avance">
                                                    <div class="d-flex col-6 px-0">
                                                        <input type="file" id="pdfInputAvance" accept=".pdf" style="display: none;" onchange="cargarNomFileAvance()">
                                                        <input class="form-control" type="text" id="nomPdfAvance" onclick="document.getElementById('pdfInputAvance').click()" placeholder="PDF firmado de avances">
                                                    </div>
                                                    <div class="d-flex col-2 px-0">
                                                        <a class="btn" id="btnEnvPdfAva" onclick="upPdfAvanceFirm(event)"><i class="fas fa-cloud-upload-alt" aria-hidden="true"></i></a>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                @endif
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="d-flex flex-column align-items-end">
                                <div class="row">
                                    @if (isset($dif_perfil))
                                        {{-- Mostramos el boton de pdf meta --}}
                                        @if ($datos_status_meta[0] == 'activo' or $datos_status_meta[0] == 'inactivo' and $datos_status_meta[1] == 'En proceso' or $datos_status_meta[1] == 'Validado' and isset($dif_perfil))
                                            {{-- Gen pdf meta --}}
                                            <button class="btn" onclick="generarPdfM('meta', '{{isset($id_organismo) ? $id_organismo : 'null'}}', '{{$fecha_meta_avance->fecha_meta['fecmetapdf']}}')">Generar PDF META</button>
                                        @endif
                                        @if ($datos_status_meta[0] == 'activo' and $datos_status_meta[1] == 'En proceso' and isset($dif_perfil))
                                            <button class="btn btn-warning" onclick="confirmacionPlaneacion('retornar', '{{$datos_status_meta[0]}}' ,'{{$datos_status_avance[0]}}', '{{isset($id_organismo) ? $id_organismo : ''}}', '{{$datos_status_avance[2]}}')">Retornar</button>
                                            <button class="btn btn-danger" onclick="confirmacionPlaneacion('validar', '{{$datos_status_meta[0]}}' ,'{{$datos_status_avance[0]}}', '{{isset($id_organismo) ? $id_organismo : ''}}', '{{$datos_status_avance[2]}}')">Validar</button>
                                        @endif

                                        @if ($datos_status_avance[0] == 'activo' and $datos_status_avance[1] == 'En proceso' and isset($dif_perfil))
                                            {{-- gen pdf avance --}}
                                            <button class="btn" onclick="generarPdfA('avance', '{{$datos_status_avance[2]}}', '{{isset($id_organismo) ? $id_organismo : 'null'}}', '{{$fecha_meta_avance->fechas_avance[$datos_status_avance[2]]['fecavanpdf']}}')">Generar PDF AVANCE</button>
                                            <button class="btn btn-warning" onclick="confirmacionPlaneacion('retornar', '{{$datos_status_meta[0]}}' ,'{{$datos_status_avance[0]}}', '{{isset($id_organismo) ? $id_organismo : ''}}', '{{$datos_status_avance[2]}}') ">Retornar</button>
                                            <button class="btn btn-danger" onclick="confirmacionPlaneacion('validar', '{{$datos_status_meta[0]}}' ,'{{$datos_status_avance[0]}}', '{{isset($id_organismo) ? $id_organismo : ''}}', '{{$datos_status_avance[2]}}' )">Validar</button>
                                        @endif
                                    @else
                                        {{-- perfil del organismo --}}
                                        {{-- META --}}
                                        @if ($datos_status_meta[0] == 'activo' or $datos_status_meta[0] == 'inactivo')

                                            @if ($datos_status_meta[1] == 'En captura' or $datos_status_meta[1] == 'Retornado' or $datos_status_meta[1] == 'Validado' or $datos_status_meta[1] == 'Proceso')
                                                <button class="btn" onclick="generarPdfM('meta', '{{isset($id_organismo) ? $id_organismo : 'null'}}', '{{$fecha_meta_avance->fecha_meta['fecmetapdf']}}')">Generar PDF META</button>
                                            @endif

                                            @if ($datos_status_meta[1] == 'En captura' or $datos_status_meta[1] == 'Retornado')
                                                <button class="btn" onclick="confirmacionMeta('save')">Guardar</button>
                                                <button class="btn btn-danger" onclick="confirmacionMeta('send')">Enviar <i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                                            @endif
                                        @endif

                                        {{-- AVANCE --}}
                                        @if ($datos_status_avance[0] == 'activo' or $datos_status_avance[0] == 'inactivo')

                                            @if ($datos_status_avance[1] == 'En captura' or $datos_status_avance[1] == 'Retornado' or $datos_status_avance[1] == 'En proceso')
                                                <button class="btn" onclick="generarPdfA('avance', '{{$datos_status_avance[2]}}', '{{isset($id_organismo) ? $id_organismo : 'null'}}', '{{$fecha_meta_avance->fechas_avance[$datos_status_avance[2]]['fecavanpdf']}}')">Generar PDF AVANCE</button>
                                            @endif

                                            @if ($datos_status_avance[1] == 'En captura' or $datos_status_avance[1] == 'Retornado')
                                                <button class="btn" onclick="confirmacionAvance('{{$datos_status_avance[2]}}', 'save')">Guardar</button>
                                                <button class="btn btn-danger" onclick="confirmacionAvance('{{$datos_status_avance[2]}}', 'send')">Enviar <i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-danger mt-4" role="alert" id="">
                    <h4 class="alert-heading">¡No existen funciones ni procedimientos!</h4>
                    ¡Registra tus funciones y procedimientos para agregar las metas y avances!
                </div>
            @endif



              {{-- SEGUIMIENTO DE STATUS --}}
            <div class="col-12 row ml-1 d-flex justify-content-between mt-5">
                <div class="col-4 shadow-sm p-3 mb-5 {{$datos_status_meta[0] == 'activo' ? 'color_car text-white' : 'bg-light'}} rounded row d-flex justify-content-between">
                    <div>
                        <div><h5 class="font-weight-bold mr-2">Metas</h5></div>
                        <div class="{{$fecha_meta_avance->status_meta['captura'] == '1' ? 'font-weight-bold' : ''}}"><i class="{{$fecha_meta_avance->status_meta['captura'] == '1' ? 'fa fa-arrow-circle-right' : ''}} " aria-hidden="true"></i> Registro de metas</div>
                        <div class="{{$fecha_meta_avance->status_meta['proceso'] == '1' ? 'font-weight-bold' : ''}}"><i class="{{$fecha_meta_avance->status_meta['proceso'] == '1' ? 'fa fa-arrow-circle-right' : ''}} " aria-hidden="true"></i> Envio a validacion</div>
                        <div class="{{$fecha_meta_avance->status_meta['retornado'] == '1' ? 'font-weight-bold' : ''}}"><i class="{{$fecha_meta_avance->status_meta['retornado'] == '1' ? 'fa fa-arrow-circle-right' : ''}} " aria-hidden="true"></i> Devuelto por planeación</div>
                        <div class="{{$fecha_meta_avance->status_meta['validado'] == '1' ? 'font-weight-bold' : ''}}"><i class="{{$fecha_meta_avance->status_meta['validado'] == '1' ? 'fa fa-arrow-circle-right' : ''}} " aria-hidden="true"></i> Validado por planeación</div>
                        {{-- <div class="{{$fecha_meta_avance->status_meta['validado'] == '1' ? 'font-weight-bold' : 'd-none'}}"><a href="{{ route('pat.metavance.genpdf.meta', ['accion' => 'meta', 'idorg' => isset($id_organismo) ? $id_organismo : 'null' ]) }}" target="_blank">Descargar PDF <i class="far fa-file-pdf" aria-hidden="true"></i></a></div> --}}
                    </div>
                    <div>
                        <div><h5 class="font-weight-bold ml-2">Status</h5></div>
                        <div class="{{$fecha_meta_avance->status_meta['captura'] == '1' ? 'font-weight-bold' : ''}}">En Captura <i class="{{$fecha_meta_avance->status_meta['captura'] == '1' ? 'fa fa-arrow-circle-left ' : ''}} " aria-hidden="true"></i> </div>
                        <div class="{{$fecha_meta_avance->status_meta['proceso'] == '1' ? 'font-weight-bold' : ''}}">En Proceso <i class="{{$fecha_meta_avance->status_meta['proceso'] == '1' ? 'fa fa-arrow-circle-left ' : ''}} " aria-hidden="true"></i></div>
                        <div class="{{$fecha_meta_avance->status_meta['retornado'] == '1' ? 'font-weight-bold' : ''}}">Retornado <i class="{{$fecha_meta_avance->status_meta['retornado'] == '1' ? 'fa fa-arrow-circle-left ' : ''}} " aria-hidden="true"></i></div>
                        <div class="{{$fecha_meta_avance->status_meta['validado'] == '1' ? 'font-weight-bold' : ''}}">Validado <i class="{{$fecha_meta_avance->status_meta['validado'] == '1' ? 'fa fa-arrow-circle-left ' : ''}} " aria-hidden="true"></i></div>
                    </div>
                </div>

                <div class="col-4 shadow-sm p-3 mb-5 {{$datos_status_avance[0] == 'activo' ? 'color_car text-white' : 'bg-light'}} rounded row d-flex justify-content-between">
                    <div>
                        <div><h5 class="font-weight-bold mr-2">Avances {{ $datos_status_avance[2] != 'no_mes' ? '('.$datos_status_avance[2].')' : ''}}</h5></div>
                        <div class="{{$fecha_meta_avance->status_avance['captura'] == '1' ? 'font-weight-bold' : ''}}"><i class="{{$fecha_meta_avance->status_avance['captura'] == '1' ? 'fa fa-arrow-circle-right' : ''}} " aria-hidden="true"></i> Registro de metas</div>
                        <div class="{{$fecha_meta_avance->status_avance['proceso'] == '1' ? 'font-weight-bold' : ''}}"><i class="{{$fecha_meta_avance->status_avance['proceso'] == '1' ? 'fa fa-arrow-circle-right' : ''}} " aria-hidden="true"></i> Envio a validacion</div>
                        <div class="{{$fecha_meta_avance->status_avance['retornado'] == '1' ? 'font-weight-bold' : ''}}"><i class="{{$fecha_meta_avance->status_avance['retornado'] == '1' ? 'fa fa-arrow-circle-right' : ''}} " aria-hidden="true"></i> Devuelto por planeación</div>
                        <div class="{{$fecha_meta_avance->status_avance['autorizado'] == '1' ? 'font-weight-bold' : ''}}"><i class="{{$fecha_meta_avance->status_avance['autorizado'] == '1' ? 'fa fa-arrow-circle-right' : ''}} " aria-hidden="true"></i> Autorizado por planeación</div>
                    </div>
                    <div>
                        <div><h5 class="font-weight-bold ml-2">Status</h5></div>
                        <div class="{{$fecha_meta_avance->status_avance['captura'] == '1' ? 'font-weight-bold' : ''}}">En Captura <i class="{{$fecha_meta_avance->status_avance['captura'] == '1' ? 'fa fa-arrow-circle-left ' : ''}} " aria-hidden="true"></i> </div>
                        <div class="{{$fecha_meta_avance->status_avance['proceso'] == '1' ? 'font-weight-bold' : ''}}">En Proceso <i class="{{$fecha_meta_avance->status_avance['proceso'] == '1' ? 'fa fa-arrow-circle-left ' : ''}} " aria-hidden="true"></i></div>
                        <div class="{{$fecha_meta_avance->status_avance['retornado'] == '1' ? 'font-weight-bold' : ''}}">Retornado <i class="{{$fecha_meta_avance->status_avance['retornado'] == '1' ? 'fa fa-arrow-circle-left ' : ''}} " aria-hidden="true"></i></div>
                        <div class="{{$fecha_meta_avance->status_avance['autorizado'] == '1' ? 'font-weight-bold' : ''}}">Autorizado <i class="{{$fecha_meta_avance->status_avance['autorizado'] == '1' ? 'fa fa-arrow-circle-left ' : ''}} " aria-hidden="true"></i></div>
                    </div>
                </div>
                {{-- MUESTRA LOS MESES DE AVANCE VALIDADOS --}}
                @if (isset($mesGlob))
                <div class="col-4 shadow-sm p-1 mb-5 bg-light rounded row d-flex justify-content-between">
                    <div>
                        <h6 class="font-weight-bold ml-2 mt-2">Generar Avances Validados</h6>
                        <div class="row mx-1">
                                @for ($i = 0; $i < count($mesGlob); $i++)
                                    @if ($fecha_meta_avance->fechas_avance[$mesGlob[$i]]['statusmes'] == 'autorizado')
                                    <div>
                                        <a href="#" rel="noopener noreferrer" onclick="generarPdfA('avance', '{{$mesGlob[$i]}}', '{{isset($id_organismo) ? $id_organismo : 'null'}}', '{{$fecha_meta_avance->fechas_avance[$mesGlob[$i]]['fecavanpdf'] }}')"
                                            data-toggle="tooltip" data-placement="top" title="Generar PDF Mes de {{$mesGlob[$i]}}">
                                            {{-- generarPdfA('avance', '{{$datos_status_avance[2]}}', '{{isset($id_organismo) ? $id_organismo : 'null'}}', '{{$fecha_meta_avance->fechas_avance[$datos_status_avance[2]]['fecavanpdf']}}') --}}
                                            {{-- <span class="badge badge-pill badge-warning ml-1">{{$mesGlob[$i]}}<i class="far fa-file-pdf ml-2 text-danger" aria-hidden="true"></i></span> --}}
                                            <span class="btn btn-sm m-1 p-2">{{$mesGlob[$i]}} pdf<i class="far fa-file-pdf ml-2 text-white" aria-hidden="true"></i></span>
                                        </a>
                                    </div>
                                    @endif
                                @endfor
                        </div>
                    </div>
                </div>
                @endif

            </div>

        </div>
    </div>

@endsection

    {{-- Aqui termina el modal --}}
@section('script_content_js')
    {{-- Todos estos links se ocupan en prueba y en producción --}}
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/jquery-3.4.1/jquery-3.4.1.min.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/js/jasny-bootstrap.min.js"></script>
    {{-- <script src="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/bootstrap-4.3.1/js/bootstrap.min.js"></script> --}}

    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/sjcl.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/sha1_002.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/llave.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/jsbn.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/jsbn2.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/rsa.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/rsa2.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/base64_002.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/crypto-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/asn1hex-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/rsasign-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/x509-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/pbkdf2.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/tripledes_002.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/aes.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/rc2.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/asn1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/base64.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/hex_002.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/yahoo-min.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/hex.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/base64x-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/x64-core.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/tripledes.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/core.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/md5.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/sha1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/sha256.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/ripemd160.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/sha512.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/enc-base64.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/hmac.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/pbkdf2_002.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/cipher-core.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/asn1-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/rsapem-1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-sat/keyutil-1.js"></script>

    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/forge-0.7.1/forge-0.7.1.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-scg/mistake.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-scg/validate.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-scg/access.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-scg/dataSign.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-scg/dataTransportSign.js"></script>

    {{-- Para el envio por arreglo --}}
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/utilities-scg/ChainTransport.js"></script>

    {{-- Nueva configuracion de producción --}}
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/signedjs-generic/js/firmado_produccion.js"></script>

    {{-- Nueva configuración de prueba--}}
    {{-- <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/signedjs-generic-prueba/js/firmado_prueba.js"></script> --}}


    <script language="javascript">

        $(document).ready(function(){
            //Firma electronica
            $('#btnsignature').attr('onclick', 'firmaElectronica();');

            /*Deshabilitamos la parte de convertir a mayusculas*/
            $("input[type=text], textarea, select").off("keyup");

            //Quitar espacion en blanco a los textareas
            const textareas = document.getElementsByTagName("textarea");
            for (let i = 0; i < textareas.length; i++) {
                textareas[i].value = textareas[i].value.trim();
            }
        });

        function loader(make) {
            if(make == 'hide') make = 'none';
            if(make == 'show') make = 'block';
            document.getElementById('loader-overlay').style.display = make;
        }

        function confirmacionMeta(tipo_accion) {
            switch (tipo_accion) {
                case 'send':
                    if (confirm("¿ESTÁS SEGURO DE ENVIAR LOS DATOS PARA SU VALIDACIÓN?, UNA VEZ GUARDADO YA NO PODRÁ EDITARLOS?"))  guardarDatosMetas(tipo_accion);
                    break;
                case 'save':
                    if (confirm("¿ESTÁS SEGURO DE GUARDAR LOS DATOS?")) guardarDatosMetas(tipo_accion);
                    break
                default:
                    break;
            }
        }

        function confirmacionAvance(mesactivo, tipo_acc_av) {
            switch (tipo_acc_av) {
                case 'send':
                    if (confirm("¿ESTÁS SEGURO DE ENVIAR LOS DATOS PARA SU VALIDACIÓN?, UNA VEZ GUARDADO YA NO PODRÁ EDITARLOS")) guardarDatosAvances(mesactivo, tipo_acc_av);
                    break;
                case 'save':
                    if (confirm("¿ESTÁS SEGURO DE GUARDAR LOS DATOS?"))  guardarDatosAvances(mesactivo, tipo_acc_av);
                    break;
                default:
                    break;
            }
        }

        function confirmacionPlaneacion(tipoValid, status_meta, status_avance, id_organismo, mes) {

            if (tipoValid == 'validar' && status_meta == 'activo') {
                if (confirm("¿ESTÁS SEGURO DE VALIDAR LOS DATOS DE METAS?, UNA VEZ VALIDADO YA NO PODRÁ REALIZAR NINGUNA ACCIÓN")) accionPlaneacion(tipoValid, status_meta, status_avance, id_organismo, mes);

            }else if(tipoValid == 'retornar' && status_meta == 'activo'){
                if (confirm("¿ESTÁS SEGURO DE RETORNAR LOS DATOS DE METAS?")) accionPlaneacion(tipoValid, status_meta, status_avance, id_organismo, mes);
            }

            if (tipoValid == 'validar' && status_avance == 'activo') {
                if (confirm("¿ESTÁS SEGURO DE VALIDAR LOS DATOS DE AVANCES?, UNA VEZ VALIDADO YA NO PODRÁ REALIZAR NINGUNA ACCIÓN")) accionPlaneacion(tipoValid, status_meta, status_avance, id_organismo, mes);

            }else if(tipoValid == 'retornar' && status_avance == 'activo'){
                if (confirm("¿ESTÁS SEGURO DE RETORNAR LOS DATOS DE AVANCES?")) accionPlaneacion(tipoValid, status_meta, status_avance, id_organismo, mes);
            }
        }

        function guardarDatosMetas(tipo_accion) {
            let table = document.getElementById("tabla");
            let inputs = table.getElementsByTagName("input");
            let textarea = table.getElementsByTagName("textarea");


            //var data = [];
            let palabrasSeparadas = [];
            let valores = [];
            let array_observ = [];

            let enero = [];
            let febrero = [];
            let marzo = [];
            let abril = [];
            let mayo = [];
            let junio = [];
            let julio = [];
            let agosto = [];
            let septiembre = [];
            let octubre = [];
            let noviembre = [];
            let diciembre = [];
            let regex = /^[0-9]+$/;

            //ciclo para ir guardando los datos de todos los eses por cada registro
            let valorInt = 0;
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].value != "" && regex.test(inputs[i].value)) {
                    palabrasSeparadas = inputs[i].name.split("_");

                    valorInt = parseInt(inputs[i].value); //convertimos a entero el valor
                    if (palabrasSeparadas[0] == "enero") {
                        palabrasSeparadas.push(valorInt);
                        enero.push(palabrasSeparadas);

                    }else if(palabrasSeparadas[0] == "febrero"){
                        palabrasSeparadas.push(valorInt);
                        febrero.push(palabrasSeparadas);
                    }
                    else if(palabrasSeparadas[0] == "marzo"){
                        palabrasSeparadas.push(valorInt);
                        marzo.push(palabrasSeparadas);
                    }
                    else if(palabrasSeparadas[0] == "abril"){
                        palabrasSeparadas.push(valorInt);
                        abril.push(palabrasSeparadas);
                    }
                    else if(palabrasSeparadas[0] == "mayo"){
                        palabrasSeparadas.push(valorInt);
                        mayo.push(palabrasSeparadas);
                    }
                    else if(palabrasSeparadas[0] == "junio"){
                        palabrasSeparadas.push(valorInt);
                        junio.push(palabrasSeparadas);
                    }
                    else if(palabrasSeparadas[0] == "julio"){
                        palabrasSeparadas.push(valorInt);
                        julio.push(palabrasSeparadas);
                    }
                    else if(palabrasSeparadas[0] == "agosto"){
                        palabrasSeparadas.push(valorInt);
                        agosto.push(palabrasSeparadas);
                    }
                    else if(palabrasSeparadas[0] == "septiembre"){
                        palabrasSeparadas.push(valorInt);
                        septiembre.push(palabrasSeparadas);
                    }
                    else if(palabrasSeparadas[0] == "octubre"){
                        palabrasSeparadas.push(valorInt);
                        octubre.push(palabrasSeparadas);
                    }
                    else if(palabrasSeparadas[0] == "noviembre"){
                        palabrasSeparadas.push(valorInt);
                        noviembre.push(palabrasSeparadas);
                    }
                    else if(palabrasSeparadas[0] == "diciembre"){
                        palabrasSeparadas.push(valorInt);
                        diciembre.push(palabrasSeparadas);
                    }
                }else{
                    alert("¡NO TAN RÁPIDO!, OLVIDASTE INGRESAR UN VALOR EN UN CAMPO");
                    break;
                }
            }

            //Ciclo para obtener las observaciones de metas

            for (let i = 0; i < textarea.length; i++) {
                palabrasSeparadas = textarea[i].name.split("_");
                if (palabrasSeparadas[0] == 'desviacion') {
                    valores = [textarea[i].value];
                    array_observ.push(valores);
                }
            }


            let anexo = [];
            let datos = [];
            for (let i = 0; i < enero.length; i++) {
                //id[2], meta[1], mes[0], valor[3]
                anexo = [enero[i][2], enero[i][1], enero[i][0], enero[i][3], febrero[i][0], febrero[i][3],
                marzo[i][0], marzo[i][3], abril[i][0], abril[i][3], mayo[i][0], mayo[i][3],
                junio[i][0], junio[i][3], julio[i][0], julio[i][3], agosto[i][0], agosto[i][3],
                septiembre[i][0], septiembre[i][3], octubre[i][0], octubre[i][3], noviembre[i][0], noviembre[i][3],
                diciembre[i][0], diciembre[i][3], array_observ[i][0]
                ];
                datos.push(anexo);
            }

            //Ajax para enviar el array de datos
            let data = {
                    "_token": $("meta[name='csrf-token']").attr("content"),
                    "datos": datos,
                    "tipo_accion": tipo_accion,
                }
                $.ajax({
                    type:"post",
                    url: "{{ route('pat.metavance.guardar.meta') }}",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        location.reload();
                    }
                });
        }


        function guardarDatosAvances(mesactivo, tipo_acc_av) {

            let table = document.getElementById("tabla");
            let textarea = table.getElementsByTagName("textarea");
            let inputs = table.getElementsByTagName("input");

            // let id_orgpdf = idorgpdf;
            let mes_activo = mesactivo; //esto viene del backend que se manda como parametro a la funcion
            let arrayMesAvance = []; //el que va al ajax
            let array_desv = [];
            let array_avance = [];
            let valores = [];
            let palabrasSeparadas = [];

            //COMPARA META CON AVANCE Y PINTA TEXTAREA
            let validDesv = validDesviacion(mes_activo);

            //VERIFICA SI LOS CAMPOS MARCADOS ESTAN VACIOS
            let valAreaDesv = validAreaDesv(validDesv[0]);
            let regex = /^[0-9]+$/;
            let valorInt = 0;

            //CONDICION (validDesv[1] true si hay desviacion, valAreaDesv == true si esta lleno el campo)
            if (valAreaDesv != false) {
                //Ciclo para obtener datos de los avances
                for (let i = 0; i < inputs.length; i++) {
                    if (inputs[i].value != "" && regex.test(inputs[i].value)) {
                        palabrasSeparadas = inputs[i].name.split("-");
                        if (palabrasSeparadas[0] == mes_activo) {
                            valorInt = parseInt(inputs[i].value);//convertimos a entero los valores
                            valores = [palabrasSeparadas[0], palabrasSeparadas[1], palabrasSeparadas[2], valorInt];
                            array_avance.push(valores);
                        }
                    }else{
                        alert("NO TAN RÁPIDO!, OLVIDASTE INGRESAR UN VALOR EN UN CAMPO");
                        break;
                        return;
                    }
                }

                //Ciclo para obtener datos de las desviaciones
                for (let i = 0; i < textarea.length; i++) {
                    palabrasSeparadas = textarea[i].name.split("_");
                    if (palabrasSeparadas[0] == 'desviacion') {
                        valores = [textarea[i].value];
                        array_desv.push(valores);
                    }
                }

                //Fusionamos arrays para mandarlos en un solo array
                for (let i = 0; i < array_desv.length; i++) {
                    valores = [array_avance[i][0], array_avance[i][1], array_avance[i][2], array_avance[i][3], array_desv[i][0]];
                    arrayMesAvance.push(valores);
                }

                //Ajax para enviar el array de datos
                let data = {
                        "_token": $("meta[name='csrf-token']").attr("content"),
                        "datos": arrayMesAvance,
                        "tipo_accion": tipo_acc_av,
                    }
                $.ajax({
                type:"post",
                url: "{{ route('pat.metavance.guardar.avance') }}",
                data: data,
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    // if (response.accion == 'save') {
                    //     let datos_pdf = "avances_"+mes_activo;
                    //     let url = "{{ route('pat.metavance.genpdf.meta', [':datos_pdf', ':id_orgpdf']) }}";
                    //     url = url.replace(':datos_pdf', datos_pdf).replace(':id_orgpdf', id_orgpdf);
                    //     window.open(url, "_blank");
                    // }

                    location.reload();
                }
                });

            } else {
                alert("ES NECESARIO LLENAR LOS CAMPOS DE DESVIACIONES MARCADOS EN ROJO");
            }

        }

        //Funciones para planeacion al validar
        function accionPlaneacion(tipoValid, status_meta, status_avance, id_organismo, mes) {

            //Actualiza el status ya sea validar o retornar asi mismo agrega las observaciones
            let table = document.getElementById("tabla");
            let textarea = table.getElementsByTagName("textarea");
            let datos = []; let valores = [];
            let palabrasSeparadas = [];
            for (var i = 0; i < textarea.length; i++) {
                if (textarea[i].name !="") {
                    palabrasSeparadas = []; //comenzamo en cero de nuevo
                    palabrasSeparadas = textarea[i].name.split("_");
                    if (palabrasSeparadas[0] == 'planeacion') {

                        valores = [palabrasSeparadas[1], textarea[i].value];
                        datos.push(valores);
                    }
                }

            }

            //Ajax para enviar el array de datos
            let data = {
                    "_token": $("meta[name='csrf-token']").attr("content"),
                    "datos": datos,
                    "tipo_valid": tipoValid,
                    "status_meta": status_meta,
                    "status_avance": status_avance,
                    "id_orga": id_organismo,
                    "mes": mes
                }
                $.ajax({
                    type:"post",
                    url: "{{ route('pat.metavance.validar') }}",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        // console.log(response);
                        location.reload();
                    }
                });
        }

        //compara si hay desviacion y pinta los campos en rojo
        function validDesviacion(mes) {
            let separar = [];
            let table = document.getElementById("tabla");
            let inputs = table.getElementsByTagName("input");
            let validacion = false;
            let ids_areas = [];

            for (let i = 0; i < inputs.length; i++) {

                if (inputs[i].value != "") {
                    separar = inputs[i].name.split("-");
                    if (separar[0] == mes) {
                        //Obtenemos el valor de avance y meta
                        let refAvance = document.getElementsByName(inputs[i].name)[0];
                        let avance = refAvance.value;

                        let refMeta = document.getElementsByName(separar[0]+'_meta_'+separar[2])[0];
                        let meta = refMeta.value;

                        if (avance != meta) {
                            validacion = true;
                            let miTextarea = document.getElementsByName("desviacion_"+separar[2])[0];
                            miTextarea.classList.remove('d-none');
                            miTextarea.style.border = "2px solid red";
                            ids_areas.push("desviacion_"+separar[2]);
                            // miTextarea.focus();

                        }else{
                            let miTextarea = document.getElementsByName("desviacion_"+separar[2])[0];
                            miTextarea.style.border = "none";
                            miTextarea.value = "";
                        }
                    }
                }
                // else{
                //     alert("¡NO TAN RÁPIDO!, OLVIDASTE INGRESAR UN VALOR EN UN CAMPO");
                //     break;
                // }
            }
            return [ids_areas, validacion];
        }
        //Verifica que las desviaciones se registren en caso de ser necesario
        function validAreaDesv(arrayIds) {
            let validarArea = true;
            for (let i = 0; i < arrayIds.length; i++) {
                let textArea = document.getElementsByName(arrayIds[i])[0];
                let valor = textArea.value;
                if (valor.trim() == "") {
                    validarArea = false;
                }
            }
            return validarArea;
        }

        function generarPdfM(accion, id_orgpdf, fechapdf) {
            let fechaActual = new Date(); let fechaBD = new Date(fechapdf);
            let dia = fechaActual.getDate() + 1; let mes = fechaActual.getMonth() + 1; let anio = fechaActual.getFullYear();
            let diabd = fechaBD.getDate() + 2; let mesbd = fechaBD.getMonth() + 1; let aniobd = fechaBD.getFullYear();

            dia = (dia < 10) ? "0" + dia : dia; mes = (mes < 10) ? "0" + mes : mes;
            diabd = (diabd < 10) ? "0" + diabd : diabd; mesbd = (mesbd < 10) ? "0" + mesbd : mesbd;

            let fechaNow = new Date(anio+'-'+mes+'-'+dia); let fechapdfbd = new Date(aniobd+'-'+mesbd+'-'+diabd);
            let status_enviar = "";

            if (fechapdf == "") {
                //se crea un nuevo registro en la bd y se general el pdf
                status_enviar = "crear";
            }else if (fechaNow.getTime() === fechapdfbd.getTime()){
                // se genera de manera normal el pdf
                status_enviar = "generar";

            }else {
                // let mensaje = "¿Le gustaría generar el PDF con la fecha original (" + fechapdf + ")?";
                let mensaje = "Clic 'Aceptar' para generar con fecha  (" + fechapdf + "). 'Cancelar' para generar con fecha actual.";
                let respuesta = confirm(mensaje);

                if (respuesta) {
                    // Generando PDF con la fecha original
                    status_enviar = "genOrigin";
                } else {
                    // Generando PDF con la fecha actual
                    status_enviar = "genActual";
                }

            }

            let url = "{{ route('pat.metavance.genpdf.meta', [':datos_pdf', ':id_orgpdf']) }}";
            url = url.replace(':datos_pdf', accion+'_'+status_enviar).replace(':id_orgpdf', id_orgpdf);
            window.open(url, "_blank");
        }

        function generarPdfA(accion, mes_activo, id_orgpdf, fechapdf) {
            let fechaActual = new Date(); let fechaBD = new Date(fechapdf);
            let dia = fechaActual.getDate() + 1; let mes = fechaActual.getMonth() + 1; let anio = fechaActual.getFullYear();
            let diabd = fechaBD.getDate() + 2; let mesbd = fechaBD.getMonth() + 1; let aniobd = fechaBD.getFullYear();

            dia = (dia < 10) ? "0" + dia : dia; mes = (mes < 10) ? "0" + mes : mes;
            diabd = (diabd < 10) ? "0" + diabd : diabd; mesbd = (mesbd < 10) ? "0" + mesbd : mesbd;

            let fechaNow = new Date(anio+'-'+mes+'-'+dia); let fechapdfbd = new Date(aniobd+'-'+mesbd+'-'+diabd);

            let status_enviar = "";

            if (fechapdf == "") {
                //se crea un nuevo registro en la bd y se general el pdf
                status_enviar = "crear";
            }else if (fechaNow.getTime() === fechapdfbd.getTime()){
                // se genera de manera normal el pdf
                status_enviar = "generar";

            }else {
                // let mensaje = "¿Le gustaría generar el PDF con la fecha original (" + fechapdf + ")?";
                let mensaje = "Clic 'Aceptar' para generar con fecha  (" + fechapdf + "). 'Cancelar' para generar con fecha actual.";
                let respuesta = confirm(mensaje);

                if (respuesta) {
                    // Generando PDF con la fecha original
                    status_enviar = "genOrigin";
                } else {
                    // Generando PDF con la fecha actual
                    status_enviar = "genActual";
                }
            }


            let datos_pdf = "avances_"+mes_activo+"_"+status_enviar;
            let url = "{{ route('pat.metavance.genpdf.meta', [':datos_pdf', ':id_orgpdf']) }}";
            url = url.replace(':datos_pdf', datos_pdf).replace(':id_orgpdf', id_orgpdf);
            window.open(url, "_blank");
        }

        function cargarNomFileMeta() {
            let inputFile = document.getElementById('pdfInputMeta');
            let nomArchivo = inputFile.files[0].name;
            let labelNomArchivo = document.getElementById('nomPdfMeta');
            labelNomArchivo.value = nomArchivo;
        }

        function cargarNomFileAvance() {
            let inputFile = document.getElementById('pdfInputAvance');
            let nomArchivo = inputFile.files[0].name;
            let labelNomArchivo = document.getElementById('nomPdfAvance');
            labelNomArchivo.value = nomArchivo;
        }

        function upPdfMetaFirm(event, nomDoc) {
            event.preventDefault();
            if (!confirm("¿Estas seguro de cargar y enviar el documento a planeación?")){return false;}

            let accion_doc = "";
            if (nomDoc !== "") {
                if (confirm("YA HAS REALIZADO ESTA ACCIÓN ANTERIORMENTE ¿DESEAS REEMPLAZAR EL DOCUMENTO CON UNO NUEVO?")) {
                // La opción "Aceptar" fue seleccionada
                    accion_doc = "reemplazar";
                } else {
                // La opción "Cancelar" fue seleccionada o se cerró el cuadro de diálogo
                return;
                }
            }else accion_doc = "libre";

            //por parametro obtener url del doc en caso de que ya haya subido y pregunar si quiere reemplazar
            let nombreArchivo = document.getElementById("nomPdfMeta").value;
            let inputFile = document.getElementById('pdfInputMeta');
            if (inputFile.files.length === 0) {
                alert("POR FAVOR, SELECCIONA UN ARCHIVO PDF.");
                return;
            }
            let archivo = inputFile.files[0];
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('archivoPDF', archivo);
            formData.append('acciondoc', accion_doc);
            formData.append('nomDoc', nomDoc);

            $.ajax({
                type: "POST",
                url: "{{ route('pat.metavance.guardar.updpdfmeta') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    alert(response.mensaje);
                    location.reload();
                    // setTimeout(function() { location.reload(); }, 3000);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    alert("Error al enviar el archivo.");
                }
            });
        }

        function upPdfAvanceFirm (event){
            event.preventDefault();
            if (!confirm("¿Estas seguro de cargar y enviar el documento a planeación?")){return false;}
            //mes y nom archivo
            mes = document.getElementById("seldoc_mes").value;

            //por parametro obtener url del doc en caso de que ya haya subido y pregunar si quiere reemplazar
            // let nombreArchivo = document.getElementById("nomPdfMeta").value;
            let inputFile = document.getElementById('pdfInputAvance');
            if (inputFile.files.length === 0) {
                alert("POR FAVOR, SELECCIONA UN ARCHIVO PDF.");
                return;
            }
            let archivo = inputFile.files[0];
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('archivoPDF', archivo);
            formData.append('mes', mes);

            $.ajax({
                type: "POST",
                url: "{{ route('pat.metavance.guardar.updpdfavance') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    alert(response.mensaje);
                    location.reload();
                    // setTimeout(function() { location.reload(); }, 3000);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    alert("Error al enviar el archivo.");
                }
            });

        }

        function pdfOpenfirm() {
            let link = document.getElementById('selOpenLink').value;
            if (!isNaN(link) && link.trim() !== '') { //Numero
                let url = "{{ route('pat.metavance.pdf.efirma', [':id']) }}";
                url = url.replace(':id', link);
                window.open(url, "_blank");
            } else { //Cadena
                window.open(link, "_blank");
            }
        }

        function cambiar_organismo() {
            loader('show');
            let id_org = document.getElementById("org_new").value;
            let url = "{{ route('pat.metavance.mostrar', [':idorg']) }}";
            url = url.replace(':idorg', id_org);
            window.open(url, "_self");
        }

        // Cambio de ejercicio
        function cambioEjercicio() {
            let url = "{{ route('pat.metavance.mostrar') }}";
            $('#form_eje').attr('action', url);
            $("#form_eje").attr("target", '_self');
            $('#form_eje').submit();
        }

        //Boton cancelar operacion carga tradicional o firma electronica
        function cancelUploadDocs(event, documento, tipo_doc, anio, org, idEfirma) {
            event.preventDefault();
            //Ajax para enviar el array de datos
            if(confirm("¿Estas seguro de cancelar el proceso?")==true){
                let data = {
                    "_token": $("meta[name='csrf-token']").attr("content"),
                    "documento": documento,
                    "tipo_doc": tipo_doc,
                    "anio":anio,
                    "org":org,
                    "idEfirma": idEfirma
                }
                $.ajax({
                    type:"post",
                    url: "{{ route('pat.metavance.upload.cancel') }}",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        if(response.status === 200){
                            // alert(response.mensaje);
                            location.reload();
                        }
                    }
                });
            }

        }

        function showTradicional(event, tipo, organismo, anio) {
            event.preventDefault();
            loader('show');
            let data = {
                    "_token": $("meta[name='csrf-token']").attr("content"),
                    "tipo":tipo,
                    "id_organismo": organismo,
                    "ejercicio": anio
            }
            $.ajax({
                type:"post",
                url: "{{ route('pat.metavance.opcion.cargarpdf') }}",
                data: data,
                dataType: "json",
                success: function (response) {
                    // console.log(response);
                    if(response.status === 200){
                        // alert(response.mensaje);
                        location.reload();
                    }
                }
            });
        }

        //Generar xml para firma electronica
        function generar_xml(tipo_efirma) {
            if(confirm("¿Esta seguro de generar el documento para firmado electronico?")==true){
                loader('show');
                $('#tipo_efirma').val(tipo_efirma); //Agregamos valor al campo de texto

                // Validar solo si es avance mensual
                if (tipo_efirma == 'AVANCE_MES') {
                    mes_avance = $('#seldoc_mes').val();
                    if(mes_avance != ''){$('#mesavance').val(mes_avance);}
                }

                // console.log(tipo_efirma, mes_avance);
                // return false;
                $('#frmGenXml').attr('action', "{{route('pat.metavance.generar.xml')}}"); $('#frmGenXml').submit();
            }
        }
        //Se ejecuta al presionar firmar en el modal de firmado
        function firmaElectronica() {
            firmarDocumento($('#token').val()).then(signature => {
                if (signature.codeResponse == '114') {  //114 Unauthorized (Token invalido) 401
                    generarToken().then((value) => {
                        //Generar nuevo token e intentar de nuevo
                        firmarDocumento(value).then(response => {
                            continueProcess(response);
                        }).catch(err => {
                            console.error("Error:", error);
                        });
                    }).catch((error) => {
                        console.log(error);
                    });
                } else if (signature.codeResponse == '00') {
                    continueProcess(signature);
                } else {
                    continueProcess(signature);
                }
            }).catch(error => {
                console.error("Error:", error);
            });

        }

        async function firmarDocumento(token) {
            // el sistema 87 es el de produccion 30 es de pruebas
            try {
                const cadena = document.getElementById('cadena_original').value;
                const curp = document.getElementById('curp_firmante').value;
                const password = document.getElementById('txtpassword').value;
                const version = "87";
                return await sign(cadena, curp, password, version, token);
            } catch (error) {
                console.error("Error en sign:", error);
                throw error;
            }
        }

        function continueProcess(response) {
            if (response.statusResponse) {
                $('#fechaFirmado').val(response.date);
                $('#serieFirmante').val(response.certifiedSeries)
                $('#firma').val(response.sign);
                $('#curp').val($('#curp_firmante').val());
                $('#certificado').val(response.certificated)
                $('#idDoc').val($('#idFile').val());
                $('#frm_firmar').submit();
            } else {
                confirm(response.messageResponse + ' ' + response.descriptionResponse)
                location.reload;
            }
        }

            //Generacion de Token
        function generarToken() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/vistas/pat/metasav/token') }}",
                    data: {
                        'nombre': '',
                        'key': '',
                        '_token': $("meta[name='csrf-token']").attr("content"),
                    },
                    success: function(result) {
                        resolve(result);

                    },
                    error: function(jqXHR, textStatus) {
                        console.error("Error en la solicitud:", textStatus, errorThrown);
                        reject('error');
                    }
                });
            })
        }

        function sellarDocumento(id, tipoDoc) {
            // console.log(id, tipoDoc);
            // return false;

            if (confirm("¿Está seguro de sellar el documento?") == true) {
                $('#txtIdFirmado').val(id);
                $('#tipo_doc').val(tipoDoc);
                $('#frm_sellar').submit();
            }
        }

        //Seleccionar opciones de avance
        function opciones_avance() {
            opcion = $('#opciones_avance').val();
            mes_avance = $('#seldoc_mes').val();

            if (opcion == 'efirma_avance' && mes_avance != null) {
                 $('#content_avance').addClass('d-none');
                 $('#btnGenXmlAv').removeClass('d-none');

            }else if(opcion == 'carga_avance' && mes_avance != null){
                $('#content_avance').removeClass('d-none');
                $('#btnGenXmlAv').addClass('d-none');
            }else{
                $('#content_avance').addClass('d-none');
                $('#btnGenXmlAv').addClass('d-none');
            }
        }

    </script>
@endsection

