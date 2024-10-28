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
    </style>
@endsection
@section('title', 'Revisión de formatos Rf001 en unidad Administrativa | SIVyC Icatech')
@section('content')
    <div class="card-header">
        <a href="">INDICE </a> REVISIÓN FORMATOS RF001 POR UNIDAD
    </div>
    <div class="card card-body  p-5" style=" min-height:450px;">
        @if (session('message'))
            <div class="alert alert-success" role="alert">
                {{ session('message') }}
            </div>
        @endif
        @if (request()->has('message'))
            <div id="success-message" class="alert alert-success" role="alert">
                <b>{{ request()->get('message') }}</b>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="col-12" style="margin-bottom: 5px;">
            {{ Form::open(['route' => 'reporte.rf001.index', 'method' => 'get', 'id' => 'frm', 'enctype' => 'multipart/form-data', 'target' => '_self']) }}
            <div class="form-row">
                <div class="form-group col-md-3">
                    {{ Form::label('memorandum', 'N° MEMORANDUM', ['class' => 'awesome']) }}
                    {{ Form::text('memorandum', '', ['id' => 'memorandum', 'class' => 'form-control mr-2', 'placeholder' => 'N° MEMORANDUM', 'title' => 'NO. RECIBO / FOLIO DE GRUPO / CLAVE ', 'size' => 25, 'autocomplete' => 'off']) }}
                </div>
                <div class="form-group col-md-3 pt-4">
                    {{ Form::submit('FILTRAR', ['id' => 'filtrar', 'class' => 'btn mr-5 mt-1']) }}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="col-12">
            <div class="col-12">
                @if (count($datos) > 0)
                    <div class="p-0 m-0">
                        {{ Form::open(['route' => 'reporte.rf001.store', 'method' => 'POST', 'id' => 'frmString']) }}
                        <table class="table table-hover" id="tabla">
                            <thead>
                                <tr>
                                    <th scope="col">ESTADO</th>
                                    <th scope="col">MEMORANDUM</th>
                                    <th scope="col">UNIDAD</th>
                                    <th scope="col">PERIODO</th>
                                    <th scope="col">ASIGNADO</th>
                                    <th scope="col">ACCIONES</th>
                                    <th scope="col">DETALLES</th>
                                    <th scope="col">SELLADO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datos as $item)
                                    @php
                                        $periodoInicio = Carbon\Carbon::parse($item->periodo_inicio);
                                        $periodoFin = Carbon\Carbon::parse($item->periodo_fin);
                                        $formatoInicio = $periodoInicio->format('d/m/Y');
                                        $formatoFin = $periodoFin->format('d/m/Y');
                                    @endphp
                                    <tr>
                                        @switch($item->estado)
                                            @case('GENERADO')
                                                <td style="background-color: #3ac122; width: 15px;"><b>{{ $item->estado }}</b></td>
                                            @break

                                            @case('REVISION')
                                                <td style="background-color: #c9420c; width: 15px;"><b>{{ $item->estado }}</b></td>
                                            @break

                                            @case('FIRMADO')
                                                <td style="background-color: #f7dc6f; width: 15px;"><b>{{ $item->estado }}</b></td>
                                            @break

                                            @case('ENSELLADO')
                                                <td style="background-color: #2596be; width: 15px;"><b style="color:#f0f0f0;">PARA
                                                        SELLAR</b></td>
                                            @break

                                            @default
                                                <td style="background-color: #3ac122; width: 15px;"><b>{{ $item->estado }}</b></td>
                                        @endswitch
                                        <td style="width: 30px;">{{ $item->memorandum }}</td>
                                        <td>{{ $item->unidad }}</td>
                                        <td class="text-left">
                                            {{ $formatoInicio . ' - ' . $formatoFin }}
                                        </td>
                                        <td class="text-left">{{ $item->dirigido }}</td>
                                        <td class="text-left">
                                            @switch($item->estado)
                                                @case('GENERADO')
                                                    @can('actualizar.rf001')
                                                        <a class="nav-link pt-0"
                                                            href="{{ route('reporte.rf001.details', ['concentrado' => $item->id]) }}">
                                                            <i class="fa fa-edit  fa-2x fa-lg text-primary" aria-hidden="true"
                                                                padding-right: 12px;" title='EDITAR REGISTROS'></i>
                                                        </a>
                                                    @endcan
                                                @break

                                                @case('ENFIRMA' || 'FIRMADO')
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
                                            @can('validacion.rf001')
                                                <a class="nav-link pt-0"
                                                    href="{{ route('administrativo.rf001.details', ['id' => $item->id]) }}">
                                                    <i class="fa fa-eye fa-2x fa-lg text-grey" aria-hidden="true"
                                                        title="MOSTRAR FORMATO RF001"></i>
                                                </a>
                                            @endcan
                                        </td>
                                        <td class="text-left">
                                            @if ($item->estado == 'ENSELLADO')
                                                <a class="nav-link pt-0 openModal" data-toggle="modal"
                                                    data-target="#exampleModal" href="javascript:;"
                                                    data-id="{{ $item->id }}" data-memo="{{ $item->memorandum }}">
                                                    <i class="fas fa-stamp fa-2x fa-lg" aria-hidden="true"
                                                        title="SELLADO DIGITAL" style="color:red"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan='14'>
                                        {{ $datos->links() }}
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
        </div>
    </div>
    @include('reportes.rf001.modal.selladoModal')
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
            $('.openModal').on('click', function() {
                const idRf001 = $(this).data('id');
                const memo = $(this).data('memo');
                $("#rf001Id").val(idRf001);
                $("#memo").html(memo);
            });

            $('#memorandum').on('keyup', function() {
                let value = $(this).val().toUpperCase();
                $('#tabla tbody tr').filter(function() {
                    // Mostrar u ocultar la fila según si contiene el texto ingresado
                    $(this).toggle($(this).find('td:nth-child(2)').text().toUpperCase().indexOf(
                        value) > -1)
                });
            });
        });
    </script>
@endsection
