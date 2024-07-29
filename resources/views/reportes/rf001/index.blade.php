@extends('theme.sivyc.layout')
@section('content_script_css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <style>
        /* Estilo personalizado para el interruptor */
        .custom-switch .custom-control-label::before {
            width: 28px;
            /* Ajusta el ancho del interruptor */
            height: 17;
            /* Ajusta la altura del interruptor */
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }

        .filter-form {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .filter-form input,
        .filter-form select {
            padding: 5px;
            margin-right: 10px;
        }

        .no-records {
            text-align: center;
            padding: 20px;
            background-color: #f0f0f0;
            margin-bottom: 20px;
        }

        .new-form {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .new-form input,
        .new-form select {
            margin-right: 10px;
            padding: 10px;
            box-sizing: border-box;
        }

        .new-form button {
            padding: 10px 20px;
        }

        @media (max-width: 768px) {

            .filter-form,
            .new-form {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-form input,
            .filter-form select,
            .filter-form button,
            .new-form input,
            .new-form select,
            .new-form button {
                margin-right: 0;
            }
        }
    </style>
    {{-- links de prueba y de produccion --}}
    {{-- <link rel="stylesheet" type="text/css" href="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/bootstrap-4.3.1/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/css/jasny-bootstrap.min.css" /> --}}
@endsection
@section('title', 'Rf001 | SIVyC Icatech')
@php
    $dateInit = \Carbon\Carbon::parse($periodoInicio);
    $dateEnd = \Carbon\Carbon::parse($periodoFin);
    $dateInit->locale('es'); // Configurar el idioma a español
    $dateEnd->locale('es');
    $monthNameInit = $dateInit->translatedFormat('F');
    $monthNameEnd = $dateEnd->translatedFormat('F');
@endphp
@section('content')

    {{-- <div class="d-none" id="vHTMLSignature"></div> --}}
    {{-- <input class="d-none" id="token" name="token" type="text" value="{{$token}}"> --}}
    <!-- cabecera -->
    <div class="card-header">
        Reportes / Reportes RF-001
    </div>
    <div class="card card-body  p-5" style=" min-height:450px;">
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="col-12">
            @if ($getConcentrado)
                {{ Form::open(['route' => ['reporte.rf001.details', 'concentrado' => $getConcentrado->id], 'method' => 'get', 'id' => 'frm', 'enctype' => 'multipart/form-data', 'target' => '_self']) }}
            @else
                {{ Form::open(['route' => 'reporte.rf001.index', 'method' => 'get', 'id' => 'frm', 'enctype' => 'multipart/form-data', 'target' => '_self']) }}
            @endif
            <div class="form-row">
                <div class="form-group col-md-1">
                    <b>AÑO {{ $currentYear }} </b> <br>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::select('unidad', $datos['unidades'], $getConcentrado ? $getConcentrado->unidad : '', ['id' => 'unidad', 'placeholder' => '- UNIDAD -', 'class' => 'form-control  mr-sm-2']) }}
                </div>
                <div class="form-group col-md-3">
                    <b>Periodo Del {{ $dateInit->day }} de {{ $monthNameInit }} al {{ $dateEnd->day }} de
                        {{ $monthNameEnd }}</b>
                </div>
                @if ($getConcentrado)
                    <div class="col-md-3">
                        {{-- <a id="enviar" class="btn btn-danger"
                            href="{{ route('reporte.rf001.xml.format', ['id' => $idRf001]) }}">
                            ENVIAR PARA EFIRMA
                        </a> --}}
                        <a id="enviar" class="btn btn-danger">
                            ENVIAR PARA EFIRMA
                        </a>
                    </div>
                @endif
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    {{ Form::label('fechaInicio', 'Fecha Inicio', ['class' => 'awesome']) }}
                    {{ Form::date('fechaInicio', '', ['class' => 'form-control mr-sm-2', 'id' => 'fechaInicio']) }}
                </div>
                <div class="form-group col-md-3">
                    {{ Form::label('fechaFin', 'Fecha Fecha Fin', ['class' => 'awesome']) }}
                    {{ Form::date('fechaFin', '', ['class' => 'form-control mr-sm-2', 'id' => 'fechaFin']) }}
                </div>
                <div class="form-group col-md-3">
                    {{ Form::label('folio_grupo', 'N°. Recibo', ['class' => 'awesome']) }}
                    {{ Form::text('folio_grupo', '', ['id' => 'folio_grupo', 'class' => 'form-control mr-2', 'placeholder' => 'N°.RECIBO', 'title' => 'NO. RECIBO / FOLIO DE GRUPO / CLAVE ', 'size' => 25]) }}
                </div>
                <div class="form-group col-md-3 pt-4">
                    {{ Form::submit('FILTRAR', ['id' => 'filtrar', 'class' => 'btn mr-5 mt-1']) }}
                </div>
            </div>
            {!! Form::close() !!}
        </div>

        <div class="col-12">
            @if (count($query) > 0)
                <div class="p-0 m-0">
                    @if ($getConcentrado)
                        {{ Form::open(['route' => ['reporte.rf001.update', 'id' => $getConcentrado->id], 'method' => 'POST', 'id' => 'frmString']) }}
                        @method('PUT')
                        @csrf
                    @else
                        {{ Form::open(['route' => 'reporte.rf001.store', 'method' => 'POST', 'id' => 'frmString']) }}
                        @csrf
                    @endif

                    <table class="table table-hover" id="tabla">
                        <thead>
                            <tr>
                                <th scope="col">N°.</th>
                                <th scope="col">CONCEPTO</th>
                                <th scope="col" style="width: 25%;">MOVIMIENTO BANCARIO</th>
                                <th scope="col">RECIBO</th>
                                <th scope="col">IMPORTE</th>
                                <th scope="col">FECHA EXPEDICIÓN</th>
                                <th scope="col">SELECCIONAR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($query as $item)
                                <tr>
                                    <th scope="row">{{ $item->id }}</th>
                                    <td>{{ $item->concepto }}</td>
                                    <td>
                                        @php
                                            $depositos = json_decode($item->depositos, true);
                                        @endphp
                                        @foreach ($depositos as $k)
                                            {{ $k['folio'] }} &nbsp;
                                        @endforeach
                                    </td>
                                    <td>{{ $item->folio_recibo }} </td>
                                    <td>${{ number_format($item->importe, 2, '.', ',') }}</td>
                                    <td>
                                        {{ date('d/m/Y', strtotime($item->fecha_expedicion)) }}
                                    </td>
                                    <td class="text-center">
                                        @if ($getConcentrado)
                                            <div class="form-check">
                                                <input class="form-check-input inputCurso" type="checkbox"
                                                    value="{{ $item->clave_contrato . '_' . $item->num_recibo . '_' . $item->id . '_' . $getConcentrado->id . '_' . $item->folio_recibo }}"
                                                    id="seleccionar_{{ $item->folio_recibo }}"
                                                    @if (in_array($item->folio_recibo, $foliosMovimientos)) checked @endif name="seleccionados[]">
                                            </div>
                                        @else
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    value="{{ $item->clave_contrato . '_' . $item->num_recibo . '_' . $item->id }}"
                                                    id="seleccionar_{{ $item->folio_recibo }}" name="seleccionados[]"
                                                    @if (in_array($item->clave_contrato . '_' . $item->num_recibo . '_' . $item->id, $selectedCheckboxes)) checked @endif>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan='14'>
                                    {{ $query->appends(['seleccionados' => $selectedCheckboxes])->links() }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- formulario  --}}
                    <div class="col-4 mt-4">
                        <form class="form-inline">
                            <div class="form-group mb-2">
                                <label for="consecutivo" class="sr-only">Password</label>
                                <input type="text" name="consecutivo" class="form-control" id="consecutivo"
                                    autocomplete="off" placeholder="Memorándum"
                                    value="{{ $getConcentrado ? $getConcentrado->memorandum : '' }}">
                            </div>
                            <button type="submit" class="btn mb-2">
                                {{ $getConcentrado ? 'Modificar' : 'Generar' }}
                            </button>
                        </form>
                        {{ Form::hidden('id_unidad', $idUnidad, ['id' => 'id_unidad', 'class' => 'form-control ']) }}
                        {{ Form::Hidden('unidad', $unidad, ['id' => 'unidad', 'class' => 'form-control ']) }}
                        {{ Form::hidden('periodoInicio', $periodoInicio, ['id' => 'periodoInicio', 'class' => 'form-control']) }}
                        {{ Form::hidden('periodoFIn', $periodoFin, ['class' => 'form-control mr-sm-2', 'id' => 'periodoFIn']) }}
                    </div>
                    {{-- formulario END --}}
                    {!! Form::close() !!}
                </div>
            @else
                <div class="text-center p-5 bg-light">
                    <h5> <b>NO SE ENCONTRARON REGISTROS</b></h5>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h3>
                        <p>¿Está seguro de ejecutar la acción para efirma?</p>
                    </h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn" id="corfirmarEfirma">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script_content_js')

    {{-- Todos estos links se ocupan en prueba y en producción --}}
    {{-- <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/jquery-3.4.1/jquery-3.4.1.min.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/jasny-bootstrap4/js/jasny-bootstrap.min.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/plugins/bootstrap-4.3.1/js/bootstrap.min.js"></script>

    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sjcl.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sha1_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/llave.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/jsbn.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/jsbn2.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rsa.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rsa2.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/base64_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/crypto-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/asn1hex-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rsasign-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/x509-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/pbkdf2.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/tripledes_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/aes.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rc2.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/asn1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/base64.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/hex_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/yahoo-min.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/hex.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/base64x-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/x64-core.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/tripledes.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/core.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/md5.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sha1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sha256.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/ripemd160.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/sha512.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/enc-base64.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/hmac.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/pbkdf2_002.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/cipher-core.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/asn1-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/rsapem-1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-sat/keyutil-1.js"></script>

    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/forge-0.7.1/forge-0.7.1.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/mistake.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/validate.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/access.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/dataSign.js"></script>
    <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/utilities-scg/dataTransportSign.js"></script> --}}

    {{-- link de prueba signature-spv021_doctos-prueba --}}
    {{-- <script src="https://firmaelectronica.shyfpchiapas.gob.mx:8443/tools/library/signedjs-2.1/signature-spv021_doctos-prueba.js"></script> --}}


    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        $(document).ready(function() {
            $('#btnsignature').attr('onclick', 'firmar();');

            async function enviarCurso(parametro1, parametro2) {
                try {
                    const resultado = await new Promise((resolve, reject) => {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('reporte.rf001.jsonStore') }}",
                            dataType: "json",
                            data: {
                                elemento: parametro1,
                                details: parametro2
                            },
                            success: function(response) {
                                console.log(response);
                                resolve(response);
                            }
                        }).fail(function(jqXHR, textStatus, errorThrown) {
                            // Maneja el error aquí
                            console.log('Error:', jqXHR);
                            console.log('TextStatus:', textStatus);
                            console.log('ErrorThrown:', errorThrown);
                            reject(textStatus);

                            // Si deseas mostrar un mensaje de error más detallado
                            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                                alert('Error: ' + jqXHR.responseJSON.message);
                            } else {
                                alert('Error: ' + textStatus);
                            }
                        });
                    });

                    return resultado;
                } catch (error) {
                    if (error.responseJSON && error.responseJSON.message) {
                        alert('Error: ' + error.responseJSON.message);
                    } else {
                        alert('Error: ' + error.statusText);
                    }
                }
            }

            // Manejar el evento onclick del checkbox
            $('.inputCurso').on('click', async function() {
                try {
                    const idConAttr = $(this).attr('id');
                    // Verificar si el checkbox está marcado o desmarcado
                    let valor = $('#' + idConAttr).val();
                    let checked = $(this).is(':checked');
                    await enviarCurso(valor, checked);
                } catch (error) {
                    console.error(`Error después de la llamada Ajax: ${error}`);
                }
            });
        });

        $("#enviar").click(function() {
            $('#exampleModal').modal('show');
            // if (confirm("Esta seguro de ejecutar la acción para efirma?") == true) {
            //     loader('show');
            // }
        });

        $("#corfirmarEfirma").click(function() {
            let URL = "{{ route('reportes.rf001.xml.generar') }}";
            let form = $(document.createElement('form'));
            $(form).attr("action", URL);
            $(form).attr("method", "POST");

            // Añadir el token CSRF como un campo oculto dentro del formulario
            let csrfToken = "{{ csrf_token() }}";
            let input = $(document.createElement('input'));
            $(input).attr("type", "hidden");
            $(input).attr("name", "_token");
            $(input).attr("value", csrfToken);
            $(form).append(input);

            $('body').append(form);
            $(form).submit();
        });

        function generarToken() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: "{{ url('/firma/token') }}",
                    data: {
                        'nombre': '',
                        'key': '',
                        '_token': $("meta[name='csrf-token']").attr("content"),
                    },
                    success: function(result) {
                        resolve(result);

                    },
                    error: function(jqXHR, textStatus) {
                        reject('error');
                    }
                });
            })
        }

        function firmarDocumento(token) {
            var vresponseSignature = sign(cadena, curp, $('#txtpassword').val(), '87', token);
            // el sistema 87 es el de produccion 30 es de pruebas
            console.log(curp)
            return vresponseSignature;
        }

        function continueProcess(response) {
            if (response.statusResponse) {
                $('#fechaFirmado').val(response.date);
                $('#serieFirmante').val(response.certifiedSeries)
                $('#firma').val(response.sign);
                $('#curp').val(curp);
                $('#certificado').val(response.certificated)
                $('#idFile').val(idFile);
                $('#formUpdate').submit();
            } else {
                confirm(response.messageResponse + ' ' + response.descriptionResponse)
                location.reload;
            }
        }

        function firmar() {
            let response = firmarDocumento($('#token').val());
            if (response.codeResponse == '401') {
                generarToken().then((value) => {
                    response = firmarDocumento(value);
                    continueProcess(response);
                }).catch((error) => {
                    continueProcess(response);
                });
            } else {
                continueProcess(response);
            }
        }
    </script>
@endsection
