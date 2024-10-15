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
@section('title', 'Formatos Rf001 enviados a revisión | SIVyC Icatech')
@section('content')
    <div id="loader-overlay">
        <div id="loader"></div>
        <div id="loader-text">
            Espere un momento mientras se realiza el proceso<span> . </span><span> . </span><span> . </span>
        </div>
    </div>
    @php
        $bandera = Crypt::encrypt('solicitud');
        $encrypted = base64_encode($bandera);
        $encrypted = str_replace(['+', '/', '='], ['-', '_', ''], $encrypted);
    @endphp
    <div class="card-header">
        Reportes / Reportes RF-001 a Revisión
    </div>
    <div class="card card-body  p-5" style=" min-height:450px;">
        @if (session('message'))
            <div class="alert alert-success" role="alert">
                {{ session('message') }}
            </div>
        @endif
        <div class="col-12" style="margin-bottom: 5px;">
            {{ Form::open(['route' => 'reporte.rf001.sent', 'method' => 'get', 'id' => 'frm', 'enctype' => 'multipart/form-data', 'target' => '_self']) }}
            <div class="form-row">
                <div class="form-group col-md-3">
                    {{ Form::label('lblmemorandum', 'N° MEMORANDUM', ['class' => 'awesome']) }}
                    {{ Form::text('memorandum', '', ['id' => 'memorandum', 'class' => 'form-control mr-2', 'placeholder' => 'N° MEMORANDUM', 'title' => 'NO. RECIBO / FOLIO DE GRUPO / CLAVE ', 'size' => 25, 'autocomplete' => 'off']) }}
                </div>
                <div class="form-group col-md-3 pt-4">
                    {{ Form::submit('FILTRAR', ['id' => 'filtrar', 'class' => 'btn mr-5 mt-1']) }}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="col-12">
            @if (count($data) > 0)
                <div class="p-0 m-0">
                    {{ Form::open(['route' => 'reporte.rf001.store', 'method' => 'POST', 'id' => 'frmString']) }}
                    <table class="table table-hover" id="tabla">
                        <thead>
                            <tr>
                                <th scope="col">ESTADO</th>
                                <th scope="col">MEMORANDUM</th>
                                <th scope="col">UNIDAD</th>
                                <th scope="col">PERIODO</th>
                                <th scope="col">DOCUMENTO</th>
                                <th scope="col">DETALLES</th>
                                <th scope="col">ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                @php
                                    $periodoInicio = Carbon\Carbon::parse($item->periodo_inicio);
                                    $periodoFin = Carbon\Carbon::parse($item->periodo_fin);
                                    $formatoInicio = $periodoInicio->format('d/m/Y');
                                    $formatoFin = $periodoFin->format('d/m/Y');
                                @endphp
                                <tr>
                                    @switch($item->estado)
                                        @case('GENERADO')
                                            <td
                                                style="background-color: #5dade2; width: 15px; text-align: center; vertical-align: middle;">
                                                <b>{{ $item->estado }}</b>
                                            </td>
                                        @break

                                        @case('GENERARDOCUMENTO')
                                            <td
                                                style="background-color: #f7dc6f; width: 15px; text-align: center; vertical-align: middle;">
                                                <b>DOCUMENTO GENERADO</b>
                                            </td>
                                        @break

                                        @case('REVISION')
                                            <td
                                                style="background-color: #e67e22; width: 15px; text-align: center; vertical-align: middle;">
                                                <b>{{ $item->estado }}</b>
                                            </td>
                                        @break

                                        @case('FIRMADO')
                                            <td
                                                style="background-color: #f7dc6f; width: 15px; text-align: center; vertical-align: middle;">
                                                <b>{{ $item->estado }}</b>
                                            </td>
                                        @break

                                        @case('RETORNADO')
                                            <td
                                                style="background-color: #CD5C5C; width: 15px; text-align: center; vertical-align: middle;">
                                                <b style="color: #f0f0f0;">{{ $item->estado }}</b>
                                            </td>
                                        @break

                                        @case('APROBADO')
                                            <td
                                                style="background-color: #58d68d; width: 15px; text-align: center; vertical-align: middle;">
                                                <b style="color: #f0f0f0;">{{ $item->estado }}</b>
                                            </td>
                                        @break

                                        @case('ENFIRMA')
                                            <td
                                                style="background-color: #52be80; width: 15px; text-align: center; vertical-align: middle;">
                                                <b>EN FIRMA</b>
                                            </td>
                                        @break

                                        @case('PARASELLAR')
                                            <td
                                                style="background-color: #d68910; width: 15px; text-align: center; vertical-align: middle;">
                                                <b>PARA SELLAR</b>
                                            </td>
                                        @break

                                        @default
                                            <td
                                                style="background-color: #922b21; width: 15px; text-align: center; vertical-align: middle;">
                                                <b style="color: #f0f0f0;">{{ $item->estado }}</b>
                                            </td>
                                    @endswitch
                                    <td style="width: 30px;">{{ $item->memorandum }}</td>
                                    <td>{{ $item->unidad }}</td>
                                    <td class="text-left">
                                        {{ $formatoInicio . ' - ' . $formatoFin }}
                                    </td>
                                    <td class="text-left">
                                        @switch($item->estado)
                                            @case('RETORNADO')
                                            @case('GENERADO')
                                                @can('actualizar.rf001')
                                                    @if ($item->tipo == 'CANCELADO')
                                                        <a class="nav-link pt-0"
                                                            href="{{ route('reporte.rf001.edit', ['id' => $item->id]) }}">
                                                            <i class="fa fa-edit  fa-2x fa-lg text-primary" aria-hidden="true"
                                                                style="padding-right: 12px;" title='EDITAR RECIBOS CANCELADOS'></i>
                                                        </a>
                                                    @else
                                                        <a class="nav-link pt-0"
                                                            href="{{ route('reporte.rf001.details', ['concentrado' => $item->id]) }}">
                                                            <i class="fa fa-edit  fa-2x fa-lg text-primary" aria-hidden="true"
                                                                style="padding-right: 12px;" title='EDITAR REGISTROS'></i>
                                                        </a>
                                                    @endif
                                                @endcan
                                            @break

                                            @case('ENFIRMA' || 'FIRMADO' || 'GENERARDOCUMENTO' || 'SELLADO')
                                                {{-- <a class="nav-link pt-0"
                                                    href="{{ route('reporte.generar.firma', ['id' => $item->id, 'solicitud' => $dato]) }}">
                                                    <i class="fas fa-pen fa-2x fa-lg text-danger" aria-hidden="true"
                                                        title='PARA FIRMA'></i>
                                                </a> --}}
                                                @if ($item->tipo != 'CANCELADO')
                                                    <a class="nav-link pt-0"
                                                        href="{{ route('reporte.rf001.getpdf', ['id' => $item->id]) }}"
                                                        target="_blank">
                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}"
                                                            alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                    </a>
                                                @else
                                                    <a class="nav-link pt-0"
                                                        href="{{ route('reporte.rf001.pdf.cancelado', ['id' => $item->id]) }}"
                                                        target="_blank">
                                                        <img class="rounded" src="{{ asset('img/pdf.png') }}"
                                                            alt="{{ asset('img/pdf.png') }}" width="30px" height="30px">
                                                    </a>
                                                @endif
                                            @break

                                            @default
                                        @endswitch
                                    </td>
                                    <td class="text-left">
                                        <a class="nav-link pt-0"
                                            href="{{ route('reporte.rf001.set.details', ['id' => $item->id]) }}">
                                            <i class="fa fa-eye fa-2x fa-lg text-grey" aria-hidden="true"
                                                title="MOSTRAR FORMATO RF001"></i>
                                        </a>
                                    </td>
                                    <td class="text-left">
                                        @if ($item->estado == 'FIRMADO')
                                            <a class="nav-link pt-0" href="javascript:;"
                                                id="enviarSellado_{{ $item->id }}">
                                                <i class="fa fa-paper-plane fa-2x fa-lg text-success" aria-hidden="true"
                                                    style="padding-right: 12px;" title='ENVIAR A SELLAR'></i>
                                            </a>
                                        @endif

                                        @if ($item->estado == 'ENFIRMA' && $item->tipo == 'CANCELADO')
                                            <a class="nav-link pt-0" href="javascript:;"
                                                id="enviarSellado_{{ $item->id }}">
                                                <i class="fa fa-paper-plane fa-2x fa-lg text-success" aria-hidden="true"
                                                    style="padding-right: 12px;" title='ENVIAR A SELLAR'></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan='14'>
                                    {{ $data->links() }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    {!! Form::close() !!}
                </div>
            @else
                <div class="text-center p-4 mb-2 bg-light">
                    <h5> <b>NO SE ENCONTRARON REGISTROS</b></h5>
                </div>
            @endif
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-md-4">
                    {{-- ocutar lo siguiente porque sólo se activa si la bandera trae el dato que necesito --}}
                        @can('solicitud.rf001')
                            <a href="{{ route('reporte.rf001.ingreso-propio') }}" class="btn">
                                <i class="fas fa-plus"></i> RF001
                            </a>
                        @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script_content_js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        $(document).ready(function() {
            $('a[id^="enviarSellado_"]').on('click', function() {
                // Obtenemos el id del elemento al que se le hizo clic
                var id = $(this).attr('id');
                // Extraemos la parte del id después de 'enviarSellado_'
                var itemId = id.split('_')[1];

                let URL = "{{ route('reporte.rf001.cambio.sello', ['id' => ':id']) }}";
                // Reemplaza el marcador de posición con el itemId
                URL = URL.replace(':id', itemId);
                try {
                    document.getElementById('loader-overlay').style.display = 'block';
                    $.ajax({
                        url: URL,
                        type: 'GET',
                        dataType: "json",
                        success: function(response) {
                            // console.log(response); return;
                            setTimeout(function() {
                                // Ocultar el loader y mostrar el contenido después de la carga
                                document.getElementById('loader-overlay').style
                                    .display =
                                    'none';
                                if (response.data) {
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
                        // reject(textStatus);

                        // Si deseas mostrar un mensaje de error más detallado
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            alert('Error: ' + jqXHR.responseJSON.message);
                        } else {
                            alert('Error: ' + textStatus);
                        }
                    });;
                } catch (error) {
                    console.error(error.statusText);
                    document.getElementById('loader-overlay').style.display = 'none';
                }
            });

            $('#memorandum').on('keyup', function(){
                let value = $(this).val().toUpperCase();
                $('#tabla tbody tr').filter(function(){
                    // Mostrar u ocultar la fila según si contiene el texto ingresado
                    $(this).toggle($(this).find('td:nth-child(2)').text().toUpperCase().indexOf(value) > -1)
                });
            });
        });
    </script>
@endsection
