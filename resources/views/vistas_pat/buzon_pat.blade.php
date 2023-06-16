<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Buzón Planeación | SIVyC Icatech')
    <!--seccion-->

@section('content')
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

            {{-- Generar pdf general avance por mes --}}
            <div class="float-right border border-primary p-3 pdfGeneral">
                <span class="text-center d-block mb-2"><b>Descargar pdf general de avances</b></span>
                <div class="d-flex flex-row">
                    <select name="" id="selOpcion" class="form-control-sm mr-2">
                        <option value="">SELECCIONAR</option>
                        <option value="centrales">OFICINAS CENTRALES</option>
                        <option value="unidades">UNIDADES</option>
                    </select>
                    <select name="selmes_ava" id="selmes_ava" class="form-control-sm">
                        <option value="">MES</option>
                        @for ($i = 0; $i < count($mesGlob); $i++)
                            <option value="{{$mesGlob[$i]}}">{{$mesGlob[$i]}}</option>
                        @endfor
                    </select>
                    <a class="ml-2" id="btnGenPdf"><i class="fa fa-cloud-download fa-2x" aria-hidden="true"></i></a>
                </div>
            </div>

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
                        <h4 class="text-center font-weight-bold"><u>METAS</u></h4>
                        <table class="table table-responsive-md" id='tableperfiles'>
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">ORGANISMO</th>
                                    <th scope="col">PERIODO</th>
                                    <th scope="col">FECHA DE ENVIO</th>
                                    <th scope="col">STATUS</th>
                                    <th scope="col">ACCION</th>
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
                                        </td>
                                        <td>
                                            {{-- validacion v2 --}}
                                            @if ($data[$i]->status_meta['proceso'] == '1')
                                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                                    data-placement="top" title="Ir a validación" id="btnMostrarFrmEdit"
                                                    href="{{route('pat.metavance.envioplane', ['id' => $data[$i]->id_org])}}">
                                                    <i class="fa fa-search fa-2x mt-2" style="color: rgb(65, 120, 203);" aria-hidden="true"></i>
                                                </a>

                                            @elseif($data[$i]->status_meta['validado'] == '1')
                                                @if ($data[$i]->fecha_meta['urldoc_firm'] != '')
                                                    <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                                        data-placement="top" title="Ver pdf firmado" id=""
                                                        href="{{$data[$i]->fecha_meta['urldoc_firm']}}" target="_blank">
                                                        <i class="fa fa-file-pdf-o  fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                                    </a>
                                                @else
                                                    <span class="">Pendiente PDF</span>
                                                @endif

                                            @else

                                            @endif

                                            {{-- @if ($data[$i]->status_meta['proceso'] == '1' || $data[$i]->status_meta['captura'] == '1'
                                                || $data[$i]->status_meta['retornado'] == '1')
                                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                                    data-placement="top" title="Ir a validación" id="btnMostrarFrmEdit"
                                                    href="{{route('pat.metavance.envioplane', ['id' => $data[$i]->id_org])}}">
                                                    <i class="fa fa-search fa-2x mt-2" style="color: rgb(65, 120, 203);" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                            @if ($data[$i]->status_meta['validado'] == '1')

                                                @if ($data[$i]->fecha_meta['urldoc_firm'] != '')
                                                    <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                                        data-placement="top" title="Ver pdf firmado" id=""
                                                        href="{{$data[$i]->fecha_meta['urldoc_firm']}}" target="_blank">
                                                        <i class="fa fa-file-pdf-o  fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                                    </a>
                                                @else
                                                    <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                                        data-placement="top" title="Ir a validación" id=""
                                                        href="{{route('pat.metavance.envioplane', ['id' => $data[$i]->id_org])}}">
                                                        <i class="fa fa-search fa-2x mt-2" style="color: rgb(65, 120, 203);" aria-hidden="true"></i>
                                                    </a>
                                                @endif

                                            @endif --}}
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    {{-- APARTADO DE AVANCES --}}
                    <div id="menu1" class="tab-pane fade {{$mes != null ? 'show active' : ''}} mt-4">
                        @php if($mes == null) $mes = 'enero'; @endphp
                        <div class="col-3 px-0">
                            <form action="" id="formConsul">
                                <select name="sel_mes" id="sel_mes" class="form-control" onchange="cambiarMes()">
                                    @for ($i = 0; $i < count($mesGlob); $i++)
                                        <option {{$mesGlob[$i] == $mes ? 'selected' : ''}} value="{{$mesGlob[$i]}}">{{$mesGlob[$i]}}</option>
                                    @endfor
                                </select>
                            </form>
                        </div>

                        <h4 class="mt-2 text-center font-weight-bold"><u>AVANCES {{strtoupper($mes)}}</u></h4>
                        <table class="table table-responsive-md" id='tableperfiles'>
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">ORGANISMO</th>
                                    <th scope="col">PERIODO</th>
                                    {{-- <th scope="col">MES DE AVANCE</th> --}}
                                    <th scope="col">FECHA DE ENVIO</th>
                                    <th scope="col">STATUS</th>
                                    <th scope="col">ACCION</th>
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
                                        </td>
                                        <td>
                                            {{-- Otra validacion nueva --}}
                                            @if ($data[$i]->status_avance['proceso'] == '1' && $data[$i]->fechas_avance[$mes]['fecenvioplane_a'] != '' && $data[$i]->fechas_avance[$mes]['statusmes'] == '')
                                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                                    data-placement="top" title="Ir a validación" id=""
                                                    href="{{route('pat.metavance.envioplane', ['id' => $data[$i]->id_org])}}" target="_blank">
                                                    <i class="fa fa-search fa-2x mt-2" style="color: rgb(65, 120, 203);" aria-hidden="true"></i>
                                                </a>

                                            @elseif ($data[$i]->fechas_avance[$mes]['statusmes'] == 'autorizado')
                                                @if ($data[$i]->fechas_avance[$mes]['urldoc_firmav'] != '')
                                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                                    data-placement="top" title="Ver pdf firmado" id=""
                                                    href="{{$data[$i]->fechas_avance[$mes]['urldoc_firmav']}}" target="_blank">
                                                    <i class="fa fa-file-pdf-o  fa-2x fa-lg text-danger from-control" aria-hidden="true"></i>
                                                </a>
                                                @else
                                                    <span class="">Pendiente PDF</span>
                                                @endif
                                            @else

                                            @endif
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            {{-- Tabla de Avances --}}
            {{-- <table class="table table-bordered table-striped d-none" id="tabla_avance">
                <p class="h4 text-center font-weight-bold mt-5 mb-3" id="tituloTabla">REGISTROS DE FECHAS (META)</p>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col" class="col-5 v-center">ORGANISMO/AREA</th>
                        <th scope="col" class="col-5 v-center">FECHA DE EMISIÓN    ({{strtoupper($mesLetra)}})</th>
                        <th scope="col" class="col-5 v-center">FECHA LIMITE   ({{strtoupper($mesLetra)}})</th>
                        <th scope="col" class="text-center">MODIFICAR</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < count($data); $i++)
                        <tr>
                            <td scope="row">{{($i+1)}}</td>
                            <td>{{$data[$i]['nombre']}}</td>
                            <td>{{
                                isset($data[$i]['fechas_avance'][$mesLetra]['fechaemision']) ? $data[$i]['fechas_avance'][$mesLetra]['fechaemision'] : ''
                                }}
                            </td>
                            <td>{{
                                isset($data[$i]['fechas_avance'][$mesLetra]['fechafin']) ? $data[$i]['fechas_avance'][$mesLetra]['fechafin'] : ''
                                }}
                            </td>
                            <td class="d-block text-center">
                                <a class="btn-circle btn-circle-sm" data-toggle="tooltip"
                                        data-placement="top" title="EDITAR" id=""
                                        href="#" onclick="ModalUpdate({{$data[$i]['id']}}, 'avance', '{{$mesLetra}}' )">
                                        <i class="fa fa-pencil-square-o fa-2x mt-2" style="color: #f1ad24;" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table> --}}
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
                // alert(selMes);
                    // let url = "{{ route('pat.buzon.index', [':mes']) }}";
                    //url = url.replace(':mes', selMes);
                    loader('show');
                    let url = "{{ route('pat.buzon.index') }}";

                    $('#formConsul').attr('action', url);
                    $("#formConsul").attr("target", '_self');
                    $('#formConsul').submit();
                }else{
                    alert("SELECCIONE UN MES")
                }
        }

        $("#gotometa").click(function() {
            loader('show');
            window.location.href = "{{route('pat.buzon.index')}}";
        });

        $("#btnGenPdf").click(function() {
            let selOpcion = document.getElementById('selOpcion').value;
            let selMespdf = document.getElementById('selmes_ava').value;

            if (selMespdf != "" && selOpcion != "") {
                let url = "{{ route('pat.buzon.pdf.general', [':mes', ':opcion']) }}";
                url = url.replace(':mes', selMespdf).replace(':opcion', selOpcion);
                window.open(url, "_blank");
            } else {
                alert("SELECCIONE UNA OPCIÓN")
            }
        });

        </script>
        @endsection
@endsection
