<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Metas y Avances | SIVyC Icatech')
    <!--seccion-->

@section('content')
    <style>
        * {
            box-sizing: border-box;
        }

        .tabla-contenedor {
            /* overflow: auto;
            max-height: 100%; */
            }
            table {
            table-layout: fixed;
            width: 160%!important;
            }
            th, td {
            width: 30%;
            padding-left: 6px !important;
            }
            #loco{
                width: 12%;
            }
            table, input {
                width: 28px;
                padding-left: 7px !important;
                border: none;
                background-color: transparent !important;
            }
            table, textarea {
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

    </style>


    {{-- <link rel="stylesheet" href="{{asset('css/global.css') }}" /> --}}
    <div class="card-header">
        @if (isset($dif_perfil))
            Validación de Metas y Avances PAT
        @else
            Registro de Metas y Avances PAT
        @endif
    </div>

    <div class="card card-body" style="min-height:450px;">
        <div class="">
            {{-- container-fluid px-5 g-pt-30 --}}
            {{-- @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            @endif --}}

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
                            <div class="pull-left">
                                <h4><b>Dirección :</b> {{isset($org->nombre) ? $org->nombre : ''}}</h4>
                            </div>
                            <div class="pull-left">
                                <h4><b>Area/Depto :</b> {{isset($area_org->nombre) ? $area_org->nombre : ''}}</h4>
                            </div>
                            {{-- Muestra la fecha dando click --}}
                            {{-- <a tabindex="0" class="btn-circle btn-circle-sm" role="button"
                                data-toggle="popover" data-trigger="focus" title="Fechas de llenado"
                                data-content="
                                    @if($datos_status_meta[0] == 'activo')
                                        ({{$datos_status_meta[3][0]}}) al ({{$datos_status_meta[3][1]}})
                                    @endif
                                    @if($datos_status_avance[0] == 'activo')
                                        ({{$datos_status_avance[3][0]}}) al ({{$datos_status_avance[3][1]}})
                                    @endif
                                ">
                            <i class="fa fa-calendar fa-2x mt-1 fa-heart" aria-hidden="true" style="color: #5f0f30;"></i>
                            </a> --}}
                            <span class="badge fondo_fecha mt-3">
                                <span class="d-block text-left pt-1"><u>Periodo de Entrega</u></span>
                                @if($datos_status_meta[0] == 'activo')
                                    <p class="my-2"> ({{$datos_status_meta[3][0]}}) al ({{$datos_status_meta[3][1]}})</p>
                                @endif

                                @if($datos_status_avance[0] == 'activo')
                                    <p class="my-2">({{$datos_status_avance[3][0]}}) al ({{$datos_status_avance[3][1]}})</p>
                                @endif

                                @if ($datos_status_meta[0] == 'inactivo' && $datos_status_avance[0] == 'inactivo')
                                    <p class="my-2">Inactivo</p>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="d-flex flex-column align-items-end">
                            <div class="pull-right">
                                <h4><b>Fecha actual :</b> {{isset($fechaNow) ? $fechaNow : ''}}</h4>
                            </div>
                            <div class="pull-right">
                                <h4><b>Asunto :</b>
                                    {{$datos_status_meta[0] == 'activo' ? 'Meta' : ''}}
                                    {{$datos_status_avance[0] == 'activo' ? 'Avances - '.$datos_status_avance[2] : ''}}

                                </h4>
                            </div>
                            <div class="pull-right">
                                <h4><b>Status :</b>
                                    {{$datos_status_meta[0] == 'activo' ? $datos_status_meta[1] : ''}}
                                    {{$datos_status_avance[0] == 'activo' ? $datos_status_avance[1] : ''}}
                                </h4>
                            </div>
                            {{-- <div class="pull-right mt-3">
                                <label for="selOpenLink">Descargar pdfs firmados</label>
                                <select name="selOpenLink" id="selOpenLink" class="form-control" onchange="pdfOpenfirm()">
                                    <option value="">PDFs FIRMADOS</option>
                                    @if ($fecha_meta_avance->fecha_meta['urldoc_firm'] != '')
                                        <option value="{{$fecha_meta_avance->fecha_meta['urldoc_firm']}}">Metas</option>
                                    @endif
                                    @for ($i = 0; $i < count($mesGlob); $i++)
                                        @if ($fecha_meta_avance->fechas_avance[$mesGlob[$i]]['urldoc_firmav'] != '')
                                            <option value="{{$fecha_meta_avance->fechas_avance[$mesGlob[$i]]['urldoc_firmav']}}">AVANCE {{$mesGlob[$i]}} </option>
                                        @endif
                                    @endfor
                                </select>
                            </div> --}}

                            <div class="card" style="width: 15rem;">
                                <div class="card-body px-2 py-2">
                                    <p class="card-text text-center mb-2">Descargar pdfs firmados</p>
                                    <div class="d-flex justify-content-center">
                                        <select name="selOpenLink" id="selOpenLink" class="form-control" onchange="pdfOpenfirm()">
                                            <option value="">PDFs FIRMADOS</option>
                                            @if ($fecha_meta_avance->fecha_meta['urldoc_firm'] != '')
                                                <option value="{{$fecha_meta_avance->fecha_meta['urldoc_firm']}}">Metas</option>
                                            @endif
                                            @for ($i = 0; $i < count($mesGlob); $i++)
                                                @if ($fecha_meta_avance->fechas_avance[$mesGlob[$i]]['urldoc_firmav'] != '')
                                                    <option value="{{$fecha_meta_avance->fechas_avance[$mesGlob[$i]]['urldoc_firmav']}}">AVANCE {{$mesGlob[$i]}} </option>
                                                @endif
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                              </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORMULARIO DE BUSQUEDA --}}
            {{-- <div class="form-row">
                <div class="col-8">
                    <form class="form-inline"  action="" method="get" id="formBusqueda">
                        <input type="text" class="form-control mr-sm-2" name="busqueda_funcion" id="busqueda_funcion" placeholder="BUSCAR FUNCION">
                        <button type="submit" name="botonBuscar" id="botonBuscar" class="btn btn-outline-primary">BUSCAR</button>
                    </form>
                </div>
            </div> --}}

            {{-- TABLA DE METAS Y AVANCES --}}
            <div style="overflow:auto;">
            <table id="tabla" class="table table-bordered table-striped mt-5 table-wrapper">
                <thead>
                    <tr>
                        <th scope="col"  id="loco"></th>
                        <th scope="col" class="col-10 v-center"></th>
                        <th scope="col" class="col-10 v-center"></th>
                        <th scope="col" class="col-4 v-center"></th>
                        <th scope="col" class="col-3 v-center"></th>
                        <th scope="col" class="col-3 v-center"></th>
                        <th scope="col" class="text-center" colspan="2">ENE</th>
                        <th scope="col" class="text-center" colspan="2">FEB</th>
                        <th scope="col" class="text-center" colspan="2">MAR</th>
                        <th scope="col" class="text-center" colspan="2">ABRIL</th>
                        <th scope="col" class="text-center" colspan="2">MAY</th>
                        <th scope="col" class="text-center" colspan="2">JUN</th>
                        <th scope="col" class="text-center" colspan="2">JUL</th>
                        <th scope="col" class="text-center" colspan="2">AGO</th>
                        <th scope="col" class="text-center" colspan="2">SEP</th>
                        <th scope="col" class="text-center" colspan="2">OCT</th>
                        <th scope="col" class="text-center" colspan="2">NOV</th>
                        <th scope="col" class="text-center" colspan="2">DIC</th>
                        <th scope="col" class="col-7 v-center"></th>
                        <th scope="col" class="col-7 v-center"></th>

                    </tr>
                    <tr>
                        <th scope="col">#</th>
                        <th class="text-center">FUNCION</th>
                        <th class="text-center">PROCEDIMIENTO</th>
                        <th class="text-center">UNIDAD DE MEDIDA</th>
                        <th class="text-center">TIPO DE U.M</th>
                        <th class="text-center">META ANUAL</th>
                        @for ($i = 1; $i <= 12; $i++)
                            <th scope="col" class="text-center">
                                <div class="diagonal mt--2">
                                    <span class="letter small">M</span>
                                    <span class="letter small ml-1">e</span>
                                    <span class="letter small">t</span>
                                    <span class="letter small">a</span>
                                </div>
                            </th>
                            <th scope="col" class="text-center">
                                <div class="diagonal">
                                    <span class="letter small">A</span>
                                    <span class="letter small">v</span>
                                    <span class="letter small">a</span>
                                    <span class="letter small">n</span>
                                    <span class="letter small">c</span>
                                    <span class="letter small">e</span>
                                </div>
                            </th>
                        @endfor
                        <th class="text-center">
                            {{$datos_status_meta[0] == 'activo' ? 'OBSERVACIONES' : ''}}
                            {{$datos_status_avance[0] == 'activo' ? 'EXPLICACIÓN A LAS DESVIACIONES': ''}}
                        </th>
                        <th class="text-center">OBSERVACIONES PLANEACIÓN</th>
                    </tr>
                </thead>
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
                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="enero_meta_{{$datos[$conta][0]->id}}" value="{{$json_ene->meta}}" required maxlength="4"></td>
                                        <td class="{{$datos_status_avance[2] == 'enero' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'enero' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'enero' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="enero-avance-{{$datos[$conta][0]->id}}" value="{{$json_ene->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="febrero_meta_{{$datos[$conta][0]->id}}" value="{{$json_feb->meta}}" required maxlength="4"></td>
                                        <td class="{{$datos_status_avance[2] == 'febrero' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'febrero' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'febrero' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="febrero-avance-{{$datos[$conta][0]->id}}" value="{{$json_feb->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="marzo_meta_{{$datos[$conta][0]->id}}" value="{{$json_mar->meta}}" required maxlength="4"></td>
                                        <td class="{{$datos_status_avance[2] == 'marzo' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'marzo' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'marzo' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="marzo-avance-{{$datos[$conta][0]->id}}" value="{{$json_mar->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="abril_meta_{{$datos[$conta][0]->id}}" value="{{$json_abr->meta}}" required maxlength="4"></td>
                                        <td class="{{$datos_status_avance[2] == 'abril' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'abril' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{ $datos_status_avance[2] == 'abril' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }}  name="abril-avance-{{$datos[$conta][0]->id}}" value="{{$json_abr->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="mayo_meta_{{$datos[$conta][0]->id}}" value="{{$json_may->meta}}" required maxlength="4"></td>
                                        <td class="{{$datos_status_avance[2] == 'mayo' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'mayo' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'mayo' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="mayo-avance-{{$datos[$conta][0]->id}}" value="{{$json_may->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="junio_meta_{{$datos[$conta][0]->id}}"value="{{$json_jun->meta}}" required maxlength="4"></td>
                                        <td class="{{$datos_status_avance[2] == 'junio' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'junio' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'junio' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="junio-avance-{{$datos[$conta][0]->id}}" value="{{$json_jun->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="julio_meta_{{$datos[$conta][0]->id}}" value="{{$json_jul->meta}}" required maxlength="4"></td>
                                        <td class="{{$datos_status_avance[2] == 'julio' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'julio' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'julio' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="julio-avance-{{$datos[$conta][0]->id}}" value="{{$json_jul->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="agosto_meta_{{$datos[$conta][0]->id}}" value="{{$json_ago->meta}}" required maxlength="4"></td>
                                        <td class="{{$datos_status_avance[2] == 'agosto' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'agosto' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'agosto' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="agosto-avance-{{$datos[$conta][0]->id}}" value="{{$json_ago->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="septiembre_meta_{{$datos[$conta][0]->id}}" value="{{$json_sep->meta}}" required maxlength="4"></td>
                                        <td class="{{$datos_status_avance[2] == 'septiembre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'septiembre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'septiembre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="septiembre-avance-{{$datos[$conta][0]->id}}" value="{{$json_sep->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="octubre_meta_{{$datos[$conta][0]->id}}" value="{{$json_oct->meta}}" required maxlength="4"></td>
                                        <td class="{{$datos_status_avance[2] == 'octubre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'octubre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'octubre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="octubre-avance-{{$datos[$conta][0]->id}}" value="{{$json_oct->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="noviembre_meta_{{$datos[$conta][0]->id}}" value="{{$json_nov->meta}}" required maxlength="4"></td>
                                        <td class="{{$datos_status_avance[2] == 'noviembre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'noviembre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'noviembre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="noviembre-avance-{{$datos[$conta][0]->id}}" value="{{$json_nov->avance}}" required maxlength="4"></td>

                                        <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="diciembre_meta_{{$datos[$conta][0]->id}}" value="{{$json_dic->meta}}" required maxlength="4"></td>
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
                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="enero_meta_{{$datos[$i][$x]->id}}" value="{{$json_ene->meta}}" required maxlength="4"></td>
                                            <td class="{{$datos_status_avance[2] == 'enero' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'enero' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'enero' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="enero-avance-{{$datos[$i][$x]->id}}" value="{{$json_ene->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="febrero_meta_{{$datos[$i][$x]->id}}" value="{{$json_feb->meta}}" required maxlength="4"></td>
                                            <td class="{{$datos_status_avance[2] == 'febrero' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'febrero' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'febrero' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="febrero-avance-{{$datos[$i][$x]->id}}" value="{{$json_feb->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="marzo_meta_{{$datos[$i][$x]->id}}" value="{{$json_mar->meta}}" required maxlength="4"></td>
                                            <td class="{{$datos_status_avance[2] == 'marzo' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'marzo' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'marzo' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="marzo-avance-{{$datos[$i][$x]->id}}" value="{{$json_mar->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="abril_meta_{{$datos[$i][$x]->id}}" value="{{$json_abr->meta}}" required maxlength="4"></td>
                                            <td class="{{$datos_status_avance[2] == 'abril' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'abril' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'abril' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }}  name="abril-avance-{{$datos[$i][$x]->id}}" value="{{$json_abr->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="mayo_meta_{{$datos[$i][$x]->id}}" value="{{$json_may->meta}}" required maxlength="4"></td>
                                            <td class="{{$datos_status_avance[2] == 'mayo' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'mayo' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'mayo' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="mayo-avance-{{$datos[$i][$x]->id}}" value="{{$json_may->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="junio_meta_{{$datos[$i][$x]->id}}" value="{{$json_jun->meta}}" required maxlength="4"></td>
                                            <td class="{{$datos_status_avance[2] == 'junio' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'junio' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'junio' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="junio-avance-{{$datos[$i][$x]->id}}" value="{{$json_jun->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="julio_meta_{{$datos[$i][$x]->id}}" value="{{$json_jul->meta}}" required maxlength="4"></td>
                                            <td class="{{$datos_status_avance[2] == 'julio' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'julio' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'julio' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="julio-avance-{{$datos[$i][$x]->id}}" value="{{$json_jul->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="agosto_meta_{{$datos[$i][$x]->id}}" value="{{$json_ago->meta}}" required maxlength="4"></td>
                                            <td class="{{$datos_status_avance[2] == 'agosto' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'agosto' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'agosto' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="agosto-avance-{{$datos[$i][$x]->id}}" value="{{$json_ago->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="septiembre_meta_{{$datos[$i][$x]->id}}" value="{{$json_sep->meta}}" required maxlength="4"></td>
                                            <td class="{{$datos_status_avance[2] == 'septiembre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'septiembre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'septiembre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="septiembre-avance-{{$datos[$i][$x]->id}}" value="{{$json_sep->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="octubre_meta_{{$datos[$i][$x]->id}}" value="{{$json_oct->meta}}" required maxlength="4"></td>
                                            <td class="{{$datos_status_avance[2] == 'octubre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'octubre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'octubre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="octubre-avance-{{$datos[$i][$x]->id}}" value="{{$json_oct->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="noviembre_meta_{{$datos[$i][$x]->id}}" value="{{$json_nov->meta}}" required maxlength="4"></td>
                                            <td class="{{$datos_status_avance[2] == 'noviembre' && $datos_status_avance[4] == 'activo' ? 'activo_celda' : 'fondo_celda'}}"><input class="{{$datos_status_avance[2] == 'noviembre' && $datos_status_avance[4] == 'activo' ? 'texto_activo' : 'colortext'}}" type="text" {{$datos_status_avance[2] == 'noviembre' && $datos_status_avance[4] == 'activo' ? '' : 'disabled' }} name="noviembre-avance-{{$datos[$i][$x]->id}}" value="{{$json_nov->avance}}" required maxlength="4"></td>

                                            <td class="{{$datos_status_meta[2] == 'inactivo' ? 'fondo_celda' : 'activo_celda'}}"><input class="{{$datos_status_meta[2] == 'inactivo' ? 'colortext' : 'texto_activo'}}" type="text" {{$datos_status_meta[2] == 'inactivo' ? 'disabled' : '' }} name="diciembre_meta_{{$datos[$i][$x]->id}}" value="{{$json_dic->meta}}" required maxlength="4"></td>
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

            @if (isset($datos) && count($datos) != 0)
                {{-- BOTONES USADOS TANTO PLANEACION COMO ORGANISMO --}}
                <div class="row mt-2">
                    <div class="col-lg-12 row mt-3">
                        <div class="col-lg-6">
                            <div class="d-flex flex-column align-items-start">
                                @if (!isset($dif_perfil))
                                    <div class="card w-100">
                                        <div class="card-body my-0 pb-3 pt-2 {{$fecha_meta_avance->status_meta['validado'] != '1' ? 'd-none' : ''}}">
                                            <div class="col-12 mb-2">
                                                <b class="">SUBIR DOCUMENTOS FIRMADOS DE METAS Y AVANCES POR MES</b>
                                            </div>
                                            <div class="col-8">
                                                <form method="POST" class="d-flex flex-row" enctype="multipart/form-data" action="" id="doc_meta">
                                                    <input type="file" id="pdfInputMeta" accept=".pdf" style="display: none;" onchange="cargarNomFileMeta()">
                                                    <input class="form-control" type="text" id="nomPdfMeta" onclick="document.getElementById('pdfInputMeta').click()" placeholder="PDF firmado de Metas">
                                                    <a class="btn" id="btnEnvPdfMeta" onclick="upPdfMetaFirm(event, '{{$fecha_meta_avance->fecha_meta['nomdoc_firm']}}')"><i class="fa fa-cloud-upload" aria-hidden="true"></i></a>
                                                </form>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <form method="POST" class="d-flex flex-row" enctype="multipart/form-data" action="" id="doc_avance">
                                                    {{-- esta parte si quieres desaparecer todo le tiene que quitar tambien el d-flex y usar d-none --}}
                                                    <div class="d-flex col-4 px-0" id="select_meses_pdf">
                                                        <select class="form-control mr-2" name="select_mes" id="seldoc_mes">
                                                            @for ($i = 0; $i < count($mesGlob); $i++)
                                                                @if ($fecha_meta_avance->fechas_avance[$mesGlob[$i]]['statusmes'] == 'autorizado')
                                                                    <option value="{{$mesGlob[$i].'/'.$fecha_meta_avance->fechas_avance[$mesGlob[$i]]['nomdoc_firmav']}}"> {{$mesGlob[$i]}} </option>
                                                                @endif
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="d-flex col-6 px-0">
                                                        <input type="file" id="pdfInputAvance" accept=".pdf" style="display: none;" onchange="cargarNomFileAvance()">
                                                        <input class="form-control" type="text" id="nomPdfAvance" onclick="document.getElementById('pdfInputAvance').click()" placeholder="PDF firmado de avances">
                                                    </div>
                                                    <div class="d-flex col-2 px-0">
                                                        <a class="btn" id="btnEnvPdfAva" onclick="upPdfAvanceFirm(event)"><i class="fa fa-cloud-upload" aria-hidden="true"></i></a>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6">
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
            <div class="col-12 row ml-1 mt-5">
                <div class="col-4 shadow-sm p-3 mb-5 {{$datos_status_meta[0] == 'activo' ? 'color_car text-white' : 'bg-light'}} rounded row d-flex justify-content-between mr-5">
                    <div>
                        <div><h5 class="font-weight-bold mr-2">Metas</h5></div>
                        <div class="{{$fecha_meta_avance->status_meta['captura'] == '1' ? 'font-weight-bold' : ''}}"><i class="{{$fecha_meta_avance->status_meta['captura'] == '1' ? 'fa fa-arrow-circle-right' : ''}} " aria-hidden="true"></i> Registro de metas</div>
                        <div class="{{$fecha_meta_avance->status_meta['proceso'] == '1' ? 'font-weight-bold' : ''}}"><i class="{{$fecha_meta_avance->status_meta['proceso'] == '1' ? 'fa fa-arrow-circle-right' : ''}} " aria-hidden="true"></i> Envio a validacion</div>
                        <div class="{{$fecha_meta_avance->status_meta['retornado'] == '1' ? 'font-weight-bold' : ''}}"><i class="{{$fecha_meta_avance->status_meta['retornado'] == '1' ? 'fa fa-arrow-circle-right' : ''}} " aria-hidden="true"></i> Devuelto por planeación</div>
                        <div class="{{$fecha_meta_avance->status_meta['validado'] == '1' ? 'font-weight-bold' : ''}}"><i class="{{$fecha_meta_avance->status_meta['validado'] == '1' ? 'fa fa-arrow-circle-right' : ''}} " aria-hidden="true"></i> Validado por planeación</div>
                        {{-- <div class="{{$fecha_meta_avance->status_meta['validado'] == '1' ? 'font-weight-bold' : 'd-none'}}"><a href="{{ route('pat.metavance.genpdf.meta', ['accion' => 'meta', 'idorg' => isset($id_organismo) ? $id_organismo : 'null' ]) }}" target="_blank">Descargar PDF <i class="fa fa-file-pdf-o" aria-hidden="true"></i></a></div> --}}
                    </div>
                    <div>
                        <div><h5 class="font-weight-bold ml-2">Status</h5></div>
                        <div class="{{$fecha_meta_avance->status_meta['captura'] == '1' ? 'font-weight-bold' : ''}}">En Captura <i class="{{$fecha_meta_avance->status_meta['captura'] == '1' ? 'fa fa-arrow-circle-left ' : ''}} " aria-hidden="true"></i> </div>
                        <div class="{{$fecha_meta_avance->status_meta['proceso'] == '1' ? 'font-weight-bold' : ''}}">En Proceso <i class="{{$fecha_meta_avance->status_meta['proceso'] == '1' ? 'fa fa-arrow-circle-left ' : ''}} " aria-hidden="true"></i></div>
                        <div class="{{$fecha_meta_avance->status_meta['retornado'] == '1' ? 'font-weight-bold' : ''}}">Retornado <i class="{{$fecha_meta_avance->status_meta['retornado'] == '1' ? 'fa fa-arrow-circle-left ' : ''}} " aria-hidden="true"></i></div>
                        <div class="{{$fecha_meta_avance->status_meta['validado'] == '1' ? 'font-weight-bold' : ''}}">Validado <i class="{{$fecha_meta_avance->status_meta['validado'] == '1' ? 'fa fa-arrow-circle-left ' : ''}} " aria-hidden="true"></i></div>
                    </div>
                </div>

                <div class="col-4 shadow-sm p-3 mb-5 {{$datos_status_avance[0] == 'activo' ? 'color_car text-white' : 'bg-light'}} rounded row d-flex justify-content-between mr-5">
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
                <div class="col-2 shadow-sm p-3 mb-5 bg-light rounded row d-flex justify-content-between mr-5">
                    <div>
                        <h6 class="font-weight-bold">Meses validados</h6>
                        <div class="row mx-1">
                                @for ($i = 0; $i < count($mesGlob); $i++)
                                    @if ($fecha_meta_avance->fechas_avance[$mesGlob[$i]]['statusmes'] == 'autorizado')
                                    <div>
                                        <a href="#" rel="noopener noreferrer" onclick="generarPdfA('avance', '{{$mesGlob[$i]}}', '{{isset($id_organismo) ? $id_organismo : 'null'}}', '{{$fecha_meta_avance->fechas_avance[$mesGlob[$i]]['fecavanpdf'] }}')">
                                            {{-- generarPdfA('avance', '{{$datos_status_avance[2]}}', '{{isset($id_organismo) ? $id_organismo : 'null'}}', '{{$fecha_meta_avance->fechas_avance[$datos_status_avance[2]]['fecavanpdf']}}') --}}
                                            <span class="badge badge-pill badge-success ml-1">{{$mesGlob[$i]}}</span>
                                        </a>
                                    </div>
                                    @endif
                                @endfor
                        </div>
                    </div>
                </div>
                @endif

            </div>

              {{-- tabla --}}
              {{-- <div style="overflow:auto;">
                <table id="tabla" class="table table-bordered table-striped mt-5 table-wrapper">
                    <thead>
                        <tr>
                            <th scope="col"  id="loco"></th>
                            <th scope="col" class="col-7 v-center"></th>
                            <th scope="col" class="col-7 v-center"></th>
                            <th scope="col" class="text-center" colspan="2">ENE</th>
                            <th scope="col" class="text-center" colspan="2">FEB</th>
                            <th scope="col" class="text-center" colspan="2">MAR</th>
                            <th scope="col" class="text-center" colspan="2">ABRIL</th>
                            <th scope="col" class="text-center" colspan="2">MAY</th>
                            <th scope="col" class="text-center" colspan="2">JUN</th>
                            <th scope="col" class="text-center" colspan="2">JUL</th>
                            <th scope="col" class="text-center" colspan="2">AGO</th>
                            <th scope="col" class="text-center" colspan="2">SEP</th>
                            <th scope="col" class="text-center" colspan="2">OCT</th>
                            <th scope="col" class="text-center" colspan="2">NOV</th>
                            <th scope="col" class="text-center" colspan="2">DIC</th>
                            <th scope="col" class="col-7 v-center"></th>

                        </tr>
                        <tr>
                            <th scope="col">#</th>
                            <th>FUNCION</th>
                            <th>PROCEDIMIENTO</th>
                            @for ($i = 1; $i <= 12; $i++)
                                <th scope="col" class="text-center">
                                    <div class="diagonal mt--2">
                                        <span class="letter small">M</span>
                                        <span class="letter small ml-1">e</span>
                                        <span class="letter small">t</span>
                                        <span class="letter small">a</span>
                                    </div>
                                </th>
                                <th scope="col" class="text-center">
                                    <div class="diagonal">
                                        <span class="letter small">A</span>
                                        <span class="letter small">v</span>
                                        <span class="letter small">a</span>
                                        <span class="letter small">n</span>
                                        <span class="letter small">c</span>
                                        <span class="letter small">e</span>
                                    </div>
                                </th>
                            @endfor
                            <th>OBSERVACIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr>
                                <td scope="row">1</td>
                                <td>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy</td>
                                <td>making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney</td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</td>
                            </tr>
                            <tr>
                                <td scope="row">2</td>
                                <td></td>
                                <td>when an unknown printer took a galley of type and scrambled it to make a type specimen book.</td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td><input type="text" value="0" required maxlength="4"></td>
                                <td>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</td>
                            </tr>
                    </tbody>
                </table>
              </div> --}}
              {{-- tabla --}}


        </div>
    </div>



    {{-- Aqui termina el modal --}}

        @section('script_content_js')
        <script language="javascript">
            $(document).ready(function(){
                //Para ver las fechas de vigencia
                // $(function(){
                //     $('[data-toggle="popover"]').popover()
                // });

                //Quitar espacion en blanco a los textareas
                const textareas = document.getElementsByTagName("textarea");
                for (let i = 0; i < textareas.length; i++) {
                    textareas[i].value = textareas[i].value.trim();
                }

           });

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
                for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].value != "" && regex.test(inputs[i].value)) {
                        palabrasSeparadas = inputs[i].name.split("_");

                        if (palabrasSeparadas[0] == "enero") {
                            palabrasSeparadas.push(inputs[i].value);
                            enero.push(palabrasSeparadas);

                        }else if(palabrasSeparadas[0] == "febrero"){
                            palabrasSeparadas.push(inputs[i].value);
                           febrero.push(palabrasSeparadas);
                        }
                        else if(palabrasSeparadas[0] == "marzo"){
                            palabrasSeparadas.push(inputs[i].value);
                           marzo.push(palabrasSeparadas);
                        }
                        else if(palabrasSeparadas[0] == "abril"){
                            palabrasSeparadas.push(inputs[i].value);
                           abril.push(palabrasSeparadas);
                        }
                        else if(palabrasSeparadas[0] == "mayo"){
                            palabrasSeparadas.push(inputs[i].value);
                           mayo.push(palabrasSeparadas);
                        }
                        else if(palabrasSeparadas[0] == "junio"){
                            palabrasSeparadas.push(inputs[i].value);
                           junio.push(palabrasSeparadas);
                        }
                        else if(palabrasSeparadas[0] == "julio"){
                            palabrasSeparadas.push(inputs[i].value);
                           julio.push(palabrasSeparadas);
                        }
                        else if(palabrasSeparadas[0] == "agosto"){
                            palabrasSeparadas.push(inputs[i].value);
                           agosto.push(palabrasSeparadas);
                        }
                        else if(palabrasSeparadas[0] == "septiembre"){
                            palabrasSeparadas.push(inputs[i].value);
                           septiembre.push(palabrasSeparadas);
                        }
                        else if(palabrasSeparadas[0] == "octubre"){
                            palabrasSeparadas.push(inputs[i].value);
                           octubre.push(palabrasSeparadas);
                        }
                        else if(palabrasSeparadas[0] == "noviembre"){
                            palabrasSeparadas.push(inputs[i].value);
                           noviembre.push(palabrasSeparadas);
                        }
                        else if(palabrasSeparadas[0] == "diciembre"){
                            palabrasSeparadas.push(inputs[i].value);
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

                //CONDICION (validDesv[1] true si hay desviacion, valAreaDesv == true si esta lleno el campo)
                if (valAreaDesv != false) {
                    //Ciclo para obtener datos de los avances
                    for (let i = 0; i < inputs.length; i++) {
                        if (inputs[i].value != "" && regex.test(inputs[i].value)) {
                            palabrasSeparadas = inputs[i].name.split("-");
                            if (palabrasSeparadas[0] == mes_activo) {
                                valores = [palabrasSeparadas[0], palabrasSeparadas[1], palabrasSeparadas[2], inputs[i].value];
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
                    let mensaje = "Clic 'Aceptar' para generar con fecha  (" + fechapdf + "). 'Cancelar' para generar con fecha actual?";
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
                    let mensaje = "Clic 'Aceptar' para generar con fecha  (" + fechapdf + "). 'Cancelar' para generar con fecha actual?";
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
                //mes y nom archivo
                mes = document.getElementById("seldoc_mes").value;
                delimitador = "/";
                res = mes.split(delimitador);

                let accion_doc = "";
                if (res[0] == null || res[0] == "")  return
                if (res[1] !== "") {
                    if (confirm("YA HAS REALIZADO ESTA ACCIÓN ANTERIORMENTE ¿DESEAS REEMPLAZAR EL DOCUMENTO CON UNO NUEVO?")) {
                        accion_doc = "reemplazar";
                    } else {
                    return; //cancelar
                    }
                }else accion_doc = "libre";

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
                formData.append('acciondoc', accion_doc);
                formData.append('nomDoc', res[1]);
                formData.append('mes', res[0]);

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
                console.log(link);
                window.open(link, "_blank");
            }

        </script>
        @endsection
@endsection
