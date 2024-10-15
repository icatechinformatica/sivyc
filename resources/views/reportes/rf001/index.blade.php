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

        a {
            color: inherit;
            /* Hereda el color del elemento padre */
            text-decoration: none;
            /* Elimina el subrayado */
        }

        a:hover {
            color: #f2f2f2;
            /* Cambia el color al pasar el ratón por encima */
            text-decoration: underline;
            /* Subraya el enlace al pasar el ratón por encima */
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

        /* Estilo del loader */
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Fondo semi-transparente */
            z-index: 9999;
            /* Asegura que esté por encima de otros elementos */
            display: none;
            /* Ocultar inicialmente */
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

        #loader-text {
            color: #fff;
            margin-top: 150px;
            text-align: center;
            font-size: 20px;
        }

        /* Texto loader */
        #loader-text span {
            opacity: 0;
            /* Inicia los puntos como invisibles */
            font-size: 30px;
            font-weight: bold;
            animation: fadeIn 1s infinite;
            /* Aplica la animación de aparecer */
        }

        @keyframes fadeIn {

            0%,
            100% {
                opacity: 0;
            }

            50% {
                opacity: 1;
            }
        }

        #loader-text span:nth-child(1) {
            animation-delay: 0.5s;
        }

        #loader-text span:nth-child(2) {
            animation-delay: 1s;
        }

        #loader-text span:nth-child(3) {
            animation-delay: 1.5s;
        }
    </style>
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
    <div id="loader-overlay">
        <div id="loader"></div>
        <div id="loader-text">
            Espere un momento mientras se realiza el proceso<span> . </span><span> . </span><span> . </span>
        </div>
    </div>
    {{-- <div class="d-none" id="vHTMLSignature"></div> --}}
    {{-- <input class="d-none" id="token" name="token" type="text" value="{{$token}}"> --}}
    @php
        $bandera = Crypt::encrypt('solicitud');
        $encrypted = base64_encode($bandera);
        $encrypted = str_replace(['+', '/', '='], ['-', '_', ''], $encrypted);
    @endphp
    <!-- cabecera -->
    <div class="card-header">
        <a href="{{ route('reporte.rf001.sent', ['generado' => $encrypted]) }}"> Reportes </a> / Reportes RF-001
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
                {{ Form::open(['route' => 'reporte.rf001.ingreso-propio', 'method' => 'get', 'id' => 'frm', 'enctype' => 'multipart/form-data', 'target' => '_self']) }}
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
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    {{ Form::label('estado', 'Estado', ['class' => 'awesome']) }}
                    {{ Form::select('estado', ['' => '--- SELECCIONAR ---', 'ENVIADO' => 'ENVIADO', 'CARGADO' => 'CARGADO', 'CANCELADO' => 'CANCELADO'], 'seleccionar', ['id' => 'status_folio', 'class' => 'form-control mr-2']) }}
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
                        {{ Form::open(['route' => 'reporte.rf001.store', 'method' => 'POST', 'id' => 'formString']) }}
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
                                    <th scope="row">{{ $item->num_recibo }}</th>
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
                                                <input class="form-check-input concentrado-checkbox" type="checkbox"
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
                    {{-- utilizaremos flex-box --}}

                    <div class="form-row" style="display: flex; margin-left: 29em;">
                        {{-- formulario --}}
                        <div class="form-inline">
                            <div class="col-4 mt-4">
                                <div class="form-group mb-2">
                                    <label for="consecutivo" class="sr-only">Memorándum</label>
                                    <input type="text" name="consecutivo" class="form-control" id="consecutivo"
                                        autocomplete="off" placeholder="Memorándum"
                                        value="{{ $getConcentrado ? $getConcentrado->memorandum : '' }}">
                                </div>

                                {{ Form::hidden('id_unidad', $idUnidad, ['id' => 'id_unidad', 'class' => 'form-control ']) }}
                                {{ Form::hidden('unidad', $unidad, ['id' => 'unidad', 'class' => 'form-control ']) }}
                                {{ Form::hidden('periodoInicio', $periodoInicio, ['id' => 'periodoInicio', 'class' => 'form-control']) }}
                                {{ Form::hidden('periodoFIn', $periodoFin, ['class' => 'form-control mr-sm-2', 'id' => 'periodoFIn']) }}
                                {!! Form::hidden('tipoSolicitud', $tipoSolicitud, ['class' => 'form-control mr-sm-2', 'id' => 'tipoSolicitud']) !!}
                            </div>
                        </div>
                        {{-- formulario END --}}
                        <div class="col-auto mt-4">
                            <button type="submit" class="btn mb-2">
                                {{ $getConcentrado ? 'Modificar' : 'Generar' }}
                            </button>
                        </div>
                        {!! Form::close() !!}
                        <div class="col-auto mt-4">
                            @if ($getConcentrado)
                                <a id="enviar" class="btn btn-danger">
                                    GENERAR DOCUMENTO
                                </a>
                            @endif
                        </div>
                    </div>

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
                        <p>¿Está seguro de ejecutar la acción para generar el documento en pdf?</p>
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
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        $(document).ready(function() {

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

            //sessionStorage
            $('.concentrado-checkbox').each(function() {
                let checkboxId = $(this).attr('id');
                let isChecked = sessionStorage.getItem(checkboxId);
                if (isChecked === 'true') {
                    $(this).prop('checked', true);
                }

                // Guardar el estado en sessionStorage cuando se seleccione/deseleccione un checkbox
                $(this).on('change', function() {
                    sessionStorage.setItem(checkboxId, $(this).prop('checked'));
                });
            });

            $('#formString').on('submit', function(e) {
                e.preventDefault();
                // Limpiar el sessionStorage
                $('.concentrado-checkbox').each(function() {
                    sessionStorage.removeItem($(this).attr('id'));
                });
                // Enviar el formulario
                this.submit();
            });

            // Cuando el select cambie de valor
            $('#status_folio').on('change', function() {
                // Recorre cada elemento con la clase '.concentrado-checkbox'
                $('.concentrado-checkbox').each(function() {
                    sessionStorage.removeItem($(this).attr('id'));
                });
            });

        });

        $("#enviar").click(function() {
            $('#exampleModal').modal('show');
            // if (confirm("Esta seguro de ejecutar la acción para efirma?") == true) {
            //     loader('show');
            // }
        });

        $("#corfirmarEfirma").click(async function(event) {
            event.preventDefault(); // prevenir envío tradicional de formulario
            let URL = "{{ route('reportes.rf001.xml.generar', ['id' => $idRf001]) }}";
            $('#exampleModal').modal('hide');
            try {
                document.getElementById('loader-overlay').style.display = 'block';
                await $.ajax({
                    url: URL,
                    type: 'POST',
                    dataType: "json",
                    success: function(response) {
                        setTimeout(function() {
                            // Ocultar el loader y mostrar el contenido después de la carga
                            document.getElementById('loader-overlay').style.display =
                                'none';
                            if (response.resp) {
                                window.location.href =
                                    "{{ route('reporte.rf001.sent', ['generado' => $encrypted]) }}";
                            }
                        }, 2500); // 2 segundos de tiempo simulado

                    },
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    // Maneja el error aquí
                    console.error('Error:', jqXHR);
                    console.warning('TextStatus:', textStatus);
                    console.error('ErrorThrown:', errorThrown);
                    reject(textStatus);

                    // Si deseas mostrar un mensaje de error más detallado
                    if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                        alert('Error: ' + jqXHR.responseJSON.message);
                    } else {
                        alert('Error: ' + textStatus);
                    }
                });
            } catch (error) {
                console.error(error.statusText);
                document.getElementById('loader-overlay').style.display = 'none';
            }


            // let form = $(document.createElement('form'));
            // $(form).attr("action", URL);
            // $(form).attr("method", "POST");

            // // Añadir el token CSRF como un campo oculto dentro del formulario
            // let csrfToken = "{{ csrf_token() }}";
            // let input = $(document.createElement('input'));
            // $(input).attr("type", "hidden");
            // $(input).attr("name", "_token");
            // $(input).attr("value", csrfToken);
            // $(form).append(input);

            // $('body').append(form);
            // $(form).submit();
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
    </script>
@endsection
