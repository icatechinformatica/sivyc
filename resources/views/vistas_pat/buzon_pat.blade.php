<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Buzón Planeación | SIVyC Icatech')
    <!--seccion-->

@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        * {
            box-sizing: border-box;
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

            .form-control { height: 40px; }

            .nav-tabs .nav-link.active {
                color: #3976c5;
            }

            .validado {color: green;}

            .pendiente {color: red;}

            .pdfGeneral {
                position: relative;
                top: -20px;
                left: 50px;
                z-index: 2;
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


    </style>
@endsection

@section('content')
    <div class="card-header py-2">
        <h3>Buzón Planeación</h3>
    </div>

    {{-- Loader --}}
    <div id="loader-overlay">
        <div id="loader"></div>
    </div>

    {{-- card para el contenido --}}
    <div class="card card-body" style=" min-height:450px;">
        <div class="container-fluid px-5 pt-3">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            {{-- select del ejercicio --}}
            @if (count($ejercicio) > 1)
                <div class="d-flex col-8 pl-2 mb-2">
                    <div class="col-3 d-flex flex-row px-0">
                        <form action="" id="form_eje">
                            <select name="sel_ejercicio" id="" class="form-control mx-2" onchange="cambioEjercicio()">
                                @foreach ($ejercicio as $anioeje)
                                    <option {{$anioeje == $anio ? 'selected' : '' }} value="{{$anioeje}}">{{$anioeje}}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Tabla y opcion de selección --}}
            <div class="container">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{$mes == null ? 'active show' : ''}}" data-toggle="pill" href="#home" id="gotometa">Metas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$mes != null ? 'active show' : ''}}" data-toggle="pill" href="#menu1">Avances</a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- APARTADO DE METAS --}}
                    <div id="home" class="tab-pane fade {{$mes == null ? 'show active' : ''}}  mt-4">
                        <form action="" id="form_meta">
                            <div class="px-0 col-3">
                                <select name="sel_meta" id="sel_meta" class="form-control ml-3"  onchange="metas_status()">
                                    <option {{$sel_meta == 'GENERAL' ? 'selected' : ''}} value="GENERAL">GENERAL</option>
                                    <option {{$sel_meta == 'PENDIENTES' ? 'selected' : ''}} value="PENDIENTES">PENDIENTES</option>
                                    <option {{$sel_meta == 'RETORNADOS' ? 'selected' : ''}} value="RETORNADOS">RETORNADOS</option>
                                    <option {{$sel_meta == 'VALIDADOS' ? 'selected' : ''}} value="VALIDADOS">VALIDADOS</option>
                                </select>
                            </div>
                        </form>

                        <h4 class="text-center font-weight-bold"><u>METAS</u></h4>
                        <table class="table table-hover table-responsive-md" id='tableperfiles'>
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">ORGANISMO</th>
                                    <th scope="col">PERIODO</th>
                                    <th scope="col">FECHA DE ENVIO</th>
                                    <th scope="col">STATUS</th>
                                    <th scope="col">PDF</th>
                                    <th scope="col">TIPO DOCUMENTO</th>
                                    <th scope="col" class="text-center">VER DETALLES</th>
                                    <th scope="col" class="text-center">CANCELAR DOCUMENTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < count($data); $i++)
                                    <tr>
                                        <td class="font-weight-bold">{{$i+1}}</td>
                                        <td class="{{$data[$i]->id_parent == 1 || $data[$i]->id_parent == 0 ? 'font-weight-bold' : ''}}">{{$data[$i]->nombre}}</td>
                                        <td class="{{$data[$i]->id_parent == 1 || $data[$i]->id_parent == 0 ? 'font-weight-bold' : ''}}">{{$data[$i]->periodo}}</td>
                                        <td class="{{$data[$i]->id_parent == 1 || $data[$i]->id_parent == 0 ? 'font-weight-bold' : ''}}">{{$data[$i]->fecha_meta['fecenvioplane_m'] != "" ? $data[$i]->fecha_meta['fecenvioplane_m'] : 'Pendiente por enviar'}}</td>
                                        <td class="{{$data[$i]->id_parent == 1 || $data[$i]->id_parent == 0 ? 'font-weight-bold' : ''}}">

                                            {{-- validacion v2 --}}
                                            @if ($data[$i]->status_meta['proceso'] == '1')
                                                <span class="pendiente">Pendiente</span>

                                            @elseif($data[$i]->status_meta['retornado'] == '1')
                                                <span class="pendiente">Retornado</span>

                                            @elseif($data[$i]->status_meta['validado'] == '1')
                                                <span class="validado">Validado</span>
                                            @else
                                                <span class="">Sin movimiento</span>
                                            @endif
                                            <a class="btn-transparent" id=""
                                                href="{{route('pat.metavance.envioplane', ['id' => $data[$i]->id_org])}}" target="_blank">
                                                <i class="fa fa-arrow-circle-o-right" style="color: rgb(65, 120, 203);" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                        <td>
                                            @if( !empty($data[$i]->fecha_meta['id_efirma']) && !empty($data[$i]->fecha_meta['mod_documento']) )
                                            {{-- Ejecutar el documento firmado electronicamente --}}
                                                <a class="" href="{{route('pat.buzon.pdf.efirma', ['id' => $data[$i]->fecha_meta['id_efirma'], 'org'=> $data[$i]->id_org ]) }}" target="_blank">
                                                    <i class="far fa-file-pdf  fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                                </a>

                                            @elseif( !empty($data[$i]->fecha_meta['urldoc_firm']) )
                                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                                    data-placement="top" title="Ver pdf firmado" id=""
                                                    href="{{$data[$i]->fecha_meta['urldoc_firm']}}" target="_blank">
                                                    <i class="far fa-file-pdf  fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                                </a>
                                            @else
                                                <i class="far fa-file-pdf fa-2x fa-lg from-control" style="color: rgb(155, 156, 159);" aria-hidden="true"></i>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- status documento --}}
                                            @if ( !empty($data[$i]->fecha_meta['id_efirma']) &&  !empty($data[$i]->fecha_meta['mod_documento']) && !empty($data[$i]->fecha_meta['status_efirma']) )
                                                @if ($data[$i]->fecha_meta['status_efirma'] == 'validado')
                                                    Electronico <br>(SELLADO)
                                                @else
                                                    Electronico <br>(EN FIRMA)
                                                @endif
                                            @elseif($data[$i]->status_meta['validado'] == '1' &&  !empty($data[$i]->fecha_meta['urldoc_firm']))
                                                Tradicional
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                                data-placement="top" title="Ir a validación" id="btnMostrarFrmEdit"
                                                href="{{route('pat.metavance.envioplane', ['id' => $data[$i]->id_org])}}">
                                                <i class="fa fa-search fa-2x mt-2" style="color: rgb(65, 120, 203);" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            @if ( (isset($data[$i]->fecha_meta['status_efirma']) && $data[$i]->fecha_meta['status_efirma'] == 'validado') || !empty($data[$i]->fecha_meta['urldoc_firm']) )
                                                <a class="btn-circle btn-circle-sm"
                                                    onclick="eliminar_documento('meta', '{{$data[$i]->id_org}}', '{{$anio}}')"> <i class="fa fa-times fa-2x mt-2" style="color: rgb(179, 43, 19);" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    {{-- APARTADO DE AVANCES --}}
                    <div id="menu1" class="tab-pane fade {{$mes != null ? 'show active' : ''}} mt-4">
                        @php if($mes == null) $mes = 'seleccionar'; @endphp
                        <div class="col-3 px-0">
                            <form action="" id="formConsul">
                                <div class="d-flex flex-row">
                                    <select name="sel_mes" id="sel_mes" class="form-control" onchange="cambiarMes()">
                                        <option value="seleccionar">Seleccionar Mes</option>
                                        @for ($i = 0; $i < count($mesGlob); $i++)
                                            <option {{$mesGlob[$i] == $mes ? 'selected' : ''}} value="{{$mesGlob[$i]}}">{{$mesGlob[$i]}}</option>
                                        @endfor
                                    </select>
                                    <select name="sel_status" id="sel_status" class="form-control ml-3"  onchange="cambiarMes()">
                                        <option {{$sel_status == 'GENERAL' ? 'selected' : ''}} value="GENERAL">GENERAL</option>
                                        <option {{$sel_status == 'PENDIENTE' ? 'selected' : ''}} value="PENDIENTE">PENDIENTES</option>
                                        <option {{$sel_status == 'RETORNADO' ? 'selected' : ''}} value="RETORNADO">RETORNADOS</option>
                                        <option {{$sel_status == 'AUTORIZADO' ? 'selected' : ''}} value="AUTORIZADO">AUTORIZADOS</option>
                                    </select>
                                </div>
                            </form>
                        </div>

                        @if ($mes != 'seleccionar')
                            <h4 class="mt-2 text-center font-weight-bold"><u>AVANCES {{strtoupper($mes)}}</u></h4>
                            <table class="table table-hover table-responsive-md" id='tableperfiles'>
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">ORGANISMO</th>
                                        <th scope="col">PERIODO</th>
                                        {{-- <th scope="col">MES DE AVANCE</th> --}}
                                        <th scope="col">FECHA DE ENVIO</th>
                                        <th scope="col">STATUS</th>
                                        <th scope="col">PDF</th>
                                        <th scope="col">TIPO DOCUMENTO</th>
                                        <th scope="col">VER DETALLES</th>
                                        <th scope="col">PDF DIRECCIÓNES</th>
                                        <th scope="col">CANCELAR DOCUMENTO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @dd($data[2]) --}}
                                    @for ($i = 0; $i < count($data); $i++)
                                        <tr>
                                            <td class="font-weight-bold">{{$i+1}}</td>
                                            <td class="{{$data[$i]->id_parent == 1 || $data[$i]->id_parent == 0 ? 'font-weight-bold' : ''}}">{{$data[$i]->nombre}}</td>
                                            <td class="{{$data[$i]->id_parent == 1 || $data[$i]->id_parent == 0 ? 'font-weight-bold' : ''}}">{{$data[$i]->periodo}}</td>
                                            {{-- <td class="font-weight-bold">{{$mes}}</td> --}}
                                            <td class="{{$data[$i]->id_parent == 1 || $data[$i]->id_parent == 0 ? 'font-weight-bold' : ''}}">{{$data[$i]->fechas_avance[$mes]['fecenvioplane_a'] != '' ? $data[$i]->fechas_avance[$mes]['fecenvioplane_a'] : 'Pendiente por enviar'}}</td>
                                            <td class="{{$data[$i]->id_parent == 1 || $data[$i]->id_parent == 0 ? 'font-weight-bold' : ''}}">

                                                {{-- if de nueva validacion para ver si funciona mejor --}}
                                                @if ($data[$i]->status_avance['proceso'] == '1' && $data[$i]->fechas_avance[$mes]['fecenvioplane_a'] != '' && $data[$i]->fechas_avance[$mes]['statusmes'] == '')
                                                    <span class="pendiente">Pendiente</span>

                                                @elseif($data[$i]->status_avance['retornado'] == '1' && $data[$i]->fechas_avance[$mes]['fecavanreturn'] != '' && $data[$i]->fechas_avance[$mes]['statusmes'] == '')
                                                    <span class="pendiente">Retornado</span>

                                                @elseif ($data[$i]->fechas_avance[$mes]['statusmes'] == 'autorizado')
                                                    <span class="validado">Autorizado</span>

                                                @else
                                                    <span class="">Sin movimiento</span>
                                                @endif
                                                <a class="btn-transparent" id=""
                                                        href="{{route('pat.metavance.envioplane', ['id' => $data[$i]->id_org])}}" target="_blank">
                                                        <i class="fa fa-arrow-circle-o-right" style="color: rgb(65, 120, 203);" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                            <td class="text-center">

                                                @if( !empty($data[$i]->fechas_avance[$mes]['id_efirma']) &&  !empty($data[$i]->fechas_avance[$mes]['mod_documento']) && !empty($data[$i]->fechas_avance[$mes]['status_efirma']))
                                                    {{-- Ejecutar el documento firmado electronicamente --}}
                                                    <a class="" href="{{route('pat.buzon.pdf.efirma', ['id' => $data[$i]->fechas_avance[$mes]['id_efirma'], 'org'=> $data[$i]->id_org]) }}" target="_blank">
                                                        <i class="far fa-file-pdf  fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                                    </a>
                                                @elseif ($data[$i]->fechas_avance[$mes]['statusmes'] == 'autorizado' &&  !empty($data[$i]->fechas_avance[$mes]['urldoc_firmav']) )
                                                    <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                                        data-placement="top" title="Ver pdf firmado" id=""
                                                        href="{{$data[$i]->fechas_avance[$mes]['urldoc_firmav']}}" target="_blank">
                                                        <i class="far fa-file-pdf  fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                                    </a>
                                                @else
                                                    <i class="far fa-file-pdf fa-2x fa-lg from-control" style="color: rgb(155, 156, 159);" aria-hidden="true"></i>
                                                @endif
                                            </td>
                                            <td>
                                                {{-- status documento --}}
                                                @if (!empty($data[$i]->fechas_avance[$mes]['id_efirma']) && !empty($data[$i]->fechas_avance[$mes]['mod_documento']) && !empty($data[$i]->fechas_avance[$mes]['status_efirma']))
                                                    @if ($data[$i]->fechas_avance[$mes]['status_efirma'] == 'validado')
                                                        Electronico <br>(SELLADO)
                                                    @else
                                                        Electronico <br>(EN FIRMA)
                                                    @endif
                                                @elseif($data[$i]->fechas_avance[$mes]['statusmes'] == 'autorizado' &&  !empty($data[$i]->fechas_avance[$mes]['urldoc_firmav']))
                                                        Tradicional
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                                    data-placement="top" title="Ir a validación" id=""
                                                    href="{{route('pat.metavance.envioplane', ['id' => $data[$i]->id_org])}}" target="_blank">
                                                    <i class="fa fa-search fa-2x mt-2" style="color: rgb(65, 120, 203);" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                @if ($data[$i]->id_parent == 1)
                                                    <a class="btn-sm btn-info text-white" data-toggle="tooltip" target="_blank"
                                                        data-placement="top" title="Descargar PDF" href="{{ route('pat.buzon.pdf.general', ['mes' => $mes, 'opcion' => $data[$i]->id_org]) }}">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ( (isset($data[$i]->fechas_avance[$mes]['status_efirma']) && $data[$i]->fechas_avance[$mes]['status_efirma'] == 'validado') || !empty($data[$i]->fechas_avance[$mes]['urldoc_firmav']) )
                                                    <a class="btn-circle btn-circle-sm"
                                                        onclick="eliminar_documento('{{$mes}}', '{{$data[$i]->id_org}}', '{{$anio}}')"> <i class="fa fa-times fa-2x mt-2" style="color: rgb(179, 43, 19);" aria-hidden="true"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-primary mt-3" role="alert">
                                <strong>Seleccione un mes para ver lista de organismos</strong>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

         {{-- Paginación --}}
        <div class="row py-4">
            <div class="col d-flex justify-content-center">
                {{$data->appends(request()->query())->links()}}
            </div>
        </div>
    </div>
    {{-- termino del card --}}


    {{-- Modal --}}
    <div class="modal fade" id="modalUpdate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        {{-- color en el modal --}}
        <div class="modal-dialog modal-sm modal-notify modal-success" id="" role="document">
        <!--Content-->
        <div class="modal-content text-center">
            <!--Header-->
            <div class="modal-header d-flex justify-content-center">
                {{-- Mensaje para el modal --}}
            <p class="heading font-weight-bold" id="mensajeModal"></p>
            </div>

            <!--Body-->
            <div class="modal-body">
                <input type="hidden" name="id_reg" id="id_reg">
                <input type="hidden" name="tipo_reg" id="tipo_reg">
                <input type="hidden" name="mes_reg" id="mes_reg">
                <label for="fechaUpdate1" class="d-block text-left">Fecha de Emisión</label>
                <input type="date" class="form-control datepicker  mr-sm-3 mb-3" name="fechaUpdate1" id="fechaUpdate1" value="" placeholder="FECHA EMISIÓN">
                <label for="fechaUpdate2" class="d-block text-left">Fecha Limite</label>
                <input type="date" class="form-control datepicker  mr-sm-3" name="fechaUpdate2" id="fechaUpdate2" value="" placeholder="FECHA LIMITE">
            </div>

            <!--Footer-->
            <div class="modal-footer flex-center">
            <button class="btn btn-outline-success" id="">GUARDAR</button>
            <a type="button" class="btn btn-success waves-effect" id="" data-dismiss="modal">CANCELAR</a>
            </div>
        </div>
        <!--/.Content-->
        </div>
    </div>



        @section('script_content_js')
        <script language="javascript">
        $(document).ready(function(){

        });

        // paginacion
        $(document).on('click', '.pagination a', function(e) {
            //e.preventDefault(); // Evita el comportamiento predeterminado del enlace
            loader('show');
        });



        function loader(make) {
            if(make == 'hide') make = 'none';
            if(make == 'show') make = 'block';
            document.getElementById('loader-overlay').style.display = make;
        }

        function cambiarMes() {
            let selMes = document.getElementById('sel_mes').value;
            if (selMes != '') {
                    loader('show');
                    let url = "{{ route('pat.buzon.index') }}";

                    $('#formConsul').attr('action', url);
                    $("#formConsul").attr("target", '_self');
                    $('#formConsul').submit();
                }else{
                    alert("SELECCIONE UN MES")
                }
        }

        function metas_status() {
            loader('show');
            let url = "{{ route('pat.buzon.index') }}";
            $('#form_meta').attr('action', url);
            $("#form_meta").attr("target", '_self');
            $('#form_meta').submit();
        }

        // Cambiar de ejercicio
        function cambioEjercicio() {
            loader('show');
            let url = "{{ route('pat.buzon.index') }}";
            $('#form_eje').attr('action', url);
            $("#form_eje").attr("target", '_self');
            $('#form_eje').submit();
        }

        $("#gotometa").click(function() {
            loader('show');
            window.location.href = "{{route('pat.buzon.index')}}";
        });

        $("#btnGenPdf").click(function() {
            // let selOpcion = document.getElementById('selOpcion').value;
            // let selMespdf = document.getElementById('selmes_ava').value;

            // if (selMespdf != "" && selOpcion != "") {
            //     let url = "{{ route('pat.buzon.pdf.general', [':mes', ':opcion']) }}";
            //     url = url.replace(':mes', selMespdf).replace(':opcion', selOpcion);
            //     window.open(url, "_blank");
            // } else {
            //     alert("SELECCIONE UNA OPCIÓN")
            // }
        });

        //Cancelar o eliminar documento
        function eliminar_documento(tipo, organismo, periodo) {
            if (!confirm("¿ESTÁS SEGURO DE CANCELAR EL DOCUMENTO DE FORMA PERMANENTE")) return false;

            let data = {
                    "_token": $("meta[name='csrf-token']").attr("content"),
                    "tipo": tipo,
                    "organismo": organismo,
                    "periodo": periodo,
                }
                $.ajax({
                    type:"post",
                    url: "{{ route('pat.buzon.cancel.doc') }}",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        // console.log(response);
                        location.reload();
                    }
                });
        }

        </script>
        @endsection
@endsection
