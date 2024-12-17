<!--Creado por Jose Luis Moreno luisito08672@gmail.com-->
@extends('theme.sivyc.layout')

<!--llamar a la plantilla -->
@section('title', 'Buzon Folios | SIVyC Icatech')

    <!--seccion-->

@section('content_script_css')
    <style>
        * {
            box-sizing: border-box;
        }

        .card-header{
            font-variant: small-caps;
            background-color: #621132 !important;
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


        #text_buscar_curso {
            height: fit-content;
            width: auto;
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

    {{-- links de prueba y de produccion --}}
    <link rel="stylesheet" type="text/css" href="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/bootstrap-4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/css/jasny-bootstrap.min.css" />

@endsection

@section('content')
    <div class="d-none" id="vHTMLSignature"></div>
    <input class="d-none" id="token" name="token" type="text" value="{{$token}}">
    <input class="d-none" id="curpfir" name="curpfir" type="text" value="{{$curpf}}">

    <div class="card-header py-2">
        <h3>EFIRMA CONSTANCIAS</h3>
    </div>

    {{-- Loader --}}
    <div id="loader-overlay">
        <div id="loader"></div>
    </div>

    {{-- card para el contenido --}}
    <div class="card card-body" style=" min-height:450px;">
        <div class="container-fluid">
            @if ($message = Session::get('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif
            {{-- regresar --}}
            <div style="position: absolute;  right: 4%;">
                <div class="d-flex justify-content-end">
                    <a href="{{ url('/') }}" class="btn btn-info">Regresar</a>
                </div>
            </div>
            {{-- Filtrados --}}
            <form action="" class="form-inline mt-4" method="post" id="frmefirma">
                @csrf
                <div class="col-12">
                    <select name="anio" id="anio" class="form-control mr-2">
                        @for ($i = 2024; $i <= date('Y'); $i++)
                            <option value="{{$i}}" {{ $ejercicio_e == $i ? 'selected' : '' }}>{{$i}}</option>
                        @endfor
                    </select>
                    <select name="status" id="status" class="form-control mr-2">
                        @foreach($estados as $valor => $texto)
                            <option value="{{ $valor }}" {{ $filtro_e == $valor ? 'selected' : '' }}>{{ $valor != 'EnFirma' ? $texto.'S' : $texto }}</option>
                        @endforeach
                    </select>
                    <input type="text" class="form-control" name="txtclave" id="txtclave" placeholder="FOLIO O CLAVE" value="{{$clave_e ?? ''}}">
                    <input type="text" class="form-control" name="txtmatricula" id="txtmatricula" placeholder="MATRICULA" value="{{$matricula ?? ''}}">
                    <button class="btn" style="background: #12322b; color:#fff;" id="btnBuscar">BUSCAR</button>
                    <button class="btn btn-info" id="btnLimpiar">LIMPIAR</button>

                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-end">
                        <div><b>CLAVE / FOLIO:</b> {{$clave_e ?? ''}}</div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div><b>UNIDAD:</b> {{$ubicacion}}</div>
                    </div>
                </div>
            </form>

            <hr style="border-color: #ddd; border-width: 2px; margin: 10px 0;">


            {{-- Tabla --}}
            <div class="mt-4">
                @if (count($data) > 0)
                    {{-- Botones de firmado --}}
                    @if (!empty($curpf) || $slug == 'admin')
                        @if ($filtro_e == 'EnFirma')
                            <div class="d-flex justify-content-end mb-2">
                                @if ($existcurp == true && $existfirma == false)
                                    <button class="btn-sm btn-danger mr-1 font-weight-bold" href="#" data-toggle="modal" data-target="#mdlLoadViewSignature" onclick="abriModal({{json_encode($cad_original)}})">Firmar {{count($data) >= 2 ? 'Todo' : ''}}</button>
                                @endif
                                <button class="btn-sm btn-warning modal_cancelar font-weight-bold">Cancelar {{count($data) >= 2 ? 'Todo' : ''}}</button>
                            </div>
                        @elseif($existcurp == true && $filtro_e == 'firmado' && $slug != 'director_unidad')
                            <div class="d-flex justify-content-end mb-2">
                                <button class="btn-sm btn-danger mr-1 font-weight-bold" data-toggle="modal" data-target="#modalsellar">Sellar {{count($data) >= 2 ? 'Todo' : ''}}</button>
                                <button class="btn-sm btn-warning modal_cancelar font-weight-bold">Cancelar {{count($data) >= 2 ? 'Todo' : ''}}</button>
                            </div>
                        @elseif($filtro_e == 'sellado')
                            <div class="d-flex justify-content-end mb-2">
                                <button class="btn-sm btn-danger modal_cancelar font-weight-bold">Anular {{count($data) >= 2 ? 'Todo' : ''}}</button>
                            </div>
                        @endif
                    @endif


                    <table class="table table-hover table-bordered" id=''>
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center font-weight-bold" scope="col">ID</th>
                                <th class="text-center font-weight-bold" scope="col">MATRICULA</th>
                                <th class="text-center font-weight-bold" scope="col">FOLIO</th>
                                <th class="text-center font-weight-bold" scope="col">NOMBRE</th>
                                <th class="text-center font-weight-bold" scope="col">VER DOCUMENTO</th>
                                <th class="text-center font-weight-bold" scope="col">FIRMANTES</th>
                                <th class="text-center font-weight-bold" scope="col">CREADO</th>
                                <th class="text-center font-weight-bold" scope="col">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $dato)
                                @php
                                    $firmantes = []; $obj = json_decode($dato->obj_documento, true);
                                    foreach ($obj['firmantes']['firmante'][0] as $value) {
                                        if(empty($value['_attributes']['firma_firmante'])){
                                            array_push($firmantes, $value['_attributes']['nombre_firmante'].' (NO)');
                                        } else {
                                            array_push($firmantes, $value['_attributes']['nombre_firmante'].' (SI)');
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $key+1 }}</td>
                                    <td class="text-center">{{ $dato->matricula }}</td>
                                    <td class="text-center">{{ $dato->efolio }}</td>
                                    <td class="text-center">{{ $dato->nombre }}</td>
                                    <td class="text-center">
                                        <a href="{{route('grupo.efirma.pdf', ['id' => $dato->id])}}" target="_blank">
                                            <img class="rounded" src="{{ asset('img/pdf.png') }}" alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                        </a>
                                    </td>
                                    <td class="text-left">
                                        <small>
                                            @foreach ($firmantes as $item)<p class="my-0">{{$item}}</p>@endforeach
                                        </small>
                                    </td>
                                    <td class="text-center">{{ $dato->fecha_creacion}}</td>
                                    <td class="text-center">
                                        @if ($dato->status_doc == 'EnFirma' || $dato->status_doc == 'EnFirmaUno')
                                            EN FIRMA
                                        @elseif($dato->status_doc == 'firmado')
                                            FIRMADO
                                        @elseif($dato->status_doc == 'sellado')
                                            SELLADO
                                        @elseif($dato->status_doc == 'cancelado')
                                            CANCELADO
                                        @elseif($dato->status_doc == 'cancelado_icti')
                                            ANULADO
                                        @endif
                                        {{-- @foreach ($estados as $valor => $texto){{ $valor == $dato->status_doc ? $texto : ''}}@endforeach --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center p-5 bg-light">
                        <h5> <b>NO SE ENCONTRARON REGISTROS</b></h5>
                    </div>
                @endif

            </div>
        </div>
    </div>
    {{-- termino del card --}}

    <form id="formUpdfirm" action="{{route('grupo.efirma.update')}}" method="post">
        @csrf
        <input type="hidden" name="respuesta" id="respuesta">
        <input type="hidden" name="curp" id="curp">
        <input type="hidden" name="correctos" id="correctos">
        <input type="hidden" name="errores" id="errores">
        <input type="hidden" name="mensaje" id="mensaje">
        <input type="hidden" name="clave_f" value="{{$clave_e ?? ''}}">
        <input type="hidden" name="matricula_f" value="{{$matricula ?? ''}}">
    </form>

    {{-- Modal de mensaje de alertas --}}
    <div class="modal fade" id="modalp" tabindex="-1" role="dialog" aria-labelledby="modalp" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #343a40; color:#fff">
                    <p class="modal-title font-weight-bold text-center" style="font-size:18px;" id="">¡Mensaje!</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p id="mensaje_modal" style="font-size:16px;"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de sellado --}}
    <div class="modal fade" id="modalsellar" tabindex="-1" role="dialog" aria-labelledby="modalsellar" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #343a40; color:#fff">
                    <p class="modal-title font-weight-bold text-center" style="font-size:18px;" id="">¡Mensaje!</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('grupo.efirma.sellar')}}" method="post" id="frm_sellar">
                    @csrf
                    <div class="modal-body text-center">
                        <p style="font-size:16px;">¿Estás seguro de sellar todos los documentos?</p>
                        <input type="hidden" name="ids_sellar" id="ids_sellar">
                        <input type="hidden" name="clave_s" value="{{$clave_e ?? ''}}">
                        <input type="hidden" name="matricula_s" value="{{$matricula ?? ''}}">
                        <p style="color:#ff3547" id="error_sellado"></p>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="sellar({{json_encode($ids)}})">Aceptar</button>
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal cancelar documento --}}
    <div class="modal fade" id="modal_cancelardoc" tabindex="-1" role="dialog" aria-labelledby="modal_cancelardoc" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #ff3547; color:#fff">
                    <p class="modal-title font-weight-bold text-center" style="font-size:18px;">Cancelar documento</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-left">
                    <textarea class="form-control" name="motivoRechazo" id="motivoRechazo" placeholder="Escribe el motivo del rechazo..." rows="4" required></textarea>
                    <p class="mb-0 mt-2" id="mensaje_can" style="font-size:15px; color:#ff3547;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="cancelar_documento({{json_encode($ids)}}, '{{$filtro_e}}')">Aceptar</button>
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('script_content_js')

    {{-- Todos estos links se ocupan en prueba y en producción --}}
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/jquery-3.4.1/jquery-3.4.1.min.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/js/jasny-bootstrap.min.js"></script>
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/plugins/bootstrap-4.3.1/js/bootstrap.min.js"></script>

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

    {{-- Link de producción signature-spv021_doctos --}}
    <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/signedjs-2.1/signature-spv021_doctos.js"></script>

    {{-- link de prueba signature-spv021_doctos-prueba--}}
    {{-- <script src="https://resources.firma.chiapas.gob.mx:8443/tools/library/signedjs-2.1/signature-spv021_doctos-prueba.js"></script> --}}


    <script language="javascript">
        var arrayCadenasG = [];
        var curpG = '';
        $(document).ready(function(){
            $('#btnsignature').attr('onclick', 'firmar();'); //boton firma modal

            $("#btnBuscar").click(function(e){
                e.preventDefault();
                if ($("#txtclave").val().trim() != '') {
                    loader('show');
                    $('#frmefirma').attr('action', "{{ route('grupo.efirma.index')}}");
                    $('#frmefirma').submit();
                }else{
                    modal("¡Campo vacío! <br> Ingresa una clave o folio valido", "show");
                }
            });

            // //Generar reporte excel
            $('#btnLimpiar').click(function(e) {
                e.preventDefault();
                $("#txtclave").val("");
                $("#txtmatricula").val("");
            });

            //Abrir modal cancelar documento
            $('.modal_cancelar').click(function(e) {
                e.preventDefault();
                $("#modal_cancelardoc").modal("show");
            });
        });

        function loader(make) {
            if(make == 'hide') make = 'none';
            if(make == 'show') make = 'block';
            document.getElementById('loader-overlay').style.display = make;
        }

        function modal(mensaje, accion) {
            if(accion == 'show'){
                $("#mensaje_modal").html(mensaje);
                $("#modalp").modal("show");
            }else{
                $("#modalp").modal("hide");
            }
        }

        function cancelar_documento(id_registros, status) {
            let estado_doc = status;
            let ids = id_registros;
            let descripcion = $("#motivoRechazo").val().trim();
            if (estado_doc != null && descripcion != "" && ids.length != 0) {
                loader('show');
                let data = {
                "_token": $("meta[name='csrf-token']").attr("content"),
                "descripcion": descripcion,
                "estado_doc" : estado_doc,
                "ids" : ids
                }
                $.ajax({
                    type:"post",
                    url: "{{ route('grupo.efirma.canceldoc') }}",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        if(response.status == 200){
                            alert("¡Cancelado con exito!")
                            location.reload();
                        }
                    }
                });

            }else{
                $("#mensaje_can").html("¡Ingrese el motivo de la cancelación.!");
            }
            // console.log(descripcion);
        }

        function abriModal(array_cadena) {
            $('#vHTMLSignature').removeClass('d-none');
            arrayCadenasG = generarArray(array_cadena);
            curpG = $('#curpfir').val();
        }

        // Función para generar el array de cadenas
        function generarArray(array_cadena){
            let chainTransports = [];
            Object.entries(array_cadena).forEach(([clave, valor], index) => {

                let nuevaInstancia = new ChainTransport();
                nuevaInstancia.Dataid_cadenaOriginal = clave;
                nuevaInstancia.DatacadenaOriginal = valor;
                chainTransports.push(nuevaInstancia);
            });
            return chainTransports;
        }

        // Realiza el proceso de firmado electronico
        function firmar() {
            if (curpG == '') {
                alert("No puedes realizar el proceso de firmado \nya que no coinciden tus datos con los documentos a firmar.\nPor favor, verifica tu información y vuelve a intentarlo.");
                return false;
            }
            //Activar el loader
            loader('show');
            var response = firmarDocumento($('#token').val());

            if(response[0].codeResponse == '401') {
                generarToken().then((value) => {
                    response = firmarDocumento(value);
                    continueProcess(response);
                }).catch((error) => {
                    console.error('Error al generar token: ', error);
                    continueProcess(response);
                });
            } else {
                continueProcess(response);
            }
        }

        // Envia los datos para devolver respuesta del firmado
        function firmarDocumento(token) {
            var vresponseSignature = sigchains(arrayCadenasG, curpG, $('#txtpassword').val(), '87', token);
            // el sistema 87 es el de produccion 30 es de pruebas
            return vresponseSignature;
        }

        //Generacion de Token
        function generarToken() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/grupos/efirma/token') }}",
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

        //Porcesar y enviar la info por formulario submit
        function continueProcess(response) {
            console.log(response);
            let errores = 0, correctos = 0, message = '';
            let respuesta = [];
            if (response) {
                response.forEach((element, index) => {
                    if(element.statusResponse){
                        const objeto = {
                            certificado: element.certificated,
                            no_seriefirmante: element.certifiedSeries,
                            //fechafirma: element.date, //Prueba
                            fechafirma: element.date_sign, //Producción
                            firma_cadena: element.sign,
                            idCadena: element.idCadena
                        };
                        respuesta.push(objeto);
                        correctos ++;
                    }else{
                        errores ++;
                        message += `Respuesta ${index + 1}  Status: ${element.statusResponse}   Código: ${element.codeResponse}  Mensaje: ${element.descriptionResponse}\n`;
                        //prueba element.messageResponse /  Producción element.descriptionResponse
                    }

                });
                //Enviarmos al submit los datos
                if (correctos !=0 || errores !=0) {
                    let respuestaJSON = JSON.stringify(respuesta);
                    $('#respuesta').val(respuestaJSON);
                    $('#correctos').val(correctos);
                    $('#curp').val(curpG);
                    $('#errores').val(errores);
                    $('#mensaje').val(message);
                    $('#formUpdfirm').submit();
                }else{
                    loader('hide');
                    alert("Error al procesar los datos de firmas");
                }

            }else{
                loader('hide');
                alert("Error obtener la respuesta del servidor");
                console.log(response);
            }
        }

        //Envio de datos para el sellado
        function sellar(ids) {
            if (ids.length > 0) {
                loader('show');
                let idsJSON = JSON.stringify(ids);
                $('#ids_sellar').val(idsJSON);
                $('#frm_sellar').submit();
            }else{
                $("#error_sellado").html("¡No existen datos para procesar el sellado de documentos! Intente de nuevo");
                console.log("No hay registros");
            }
        }

    </script>
@endsection
