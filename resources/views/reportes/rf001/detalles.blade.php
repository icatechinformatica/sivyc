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

        .info-box {
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
            border-radius: .25rem;
            background-color: #fff;
            display: -ms-flexbox;
            display: flex;
            margin-bottom: 1rem;
            min-height: 80px;
            padding: .5rem;
            position: relative;
            width: 100%;
        }

        .info-box-content {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-direction: column;
            flex-direction: column;
            -ms-flex-pack: center;
            justify-content: center;
            line-height: 1.8;
            -ms-flex: 1;
            flex: 1;
            padding: 0 10px;
            overflow: hidden;
        }

        .info-box .info-box-text,
        .info-box .progress-description {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .text-center {
            text-align: center !important;
        }

        .mb-0,
        .my-0 {
            margin-bottom: 0 !important;
        }

        .info-box .info-box-number {
            display: block;
            margin-top: .25rem;
            font-weight: 700;
        }

        .list-unstyled {
            padding-left: 0;
            list-style: none;
        }

        dl,
        ol,
        ul {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        ul {
            display: block;
            list-style-type: disc;
            margin-block-start: 1em;
            margin-block-end: 1em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            padding-inline-start: 40px;
            unicode-bidi: isolate;
        }

        .text-secondary {
            color: #6c757d !important;
        }

        .btn-link {
            font-weight: 400;
            color: #007bff;
            text-decoration: none;
        }

        a {
            color: #007bff;
            text-decoration: none;
            background-color: transparent;
        }

        .post {
            border-bottom: 1px solid #adb5bd;
            color: #666;
            margin-bottom: 15px;
            padding-bottom: 15px;
        }

        .post .user-block {
            margin-bottom: 5px;
            width: 100%;
        }

        /* Estilos de la tabla */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #dee2e6;
            text-align: left;
        }

        table thead {
            background-color: #007bff;
            color: white;
        }

        table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Estilos personalizados para enlaces */
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

        /* Estilos responsivos */
        @media screen and (max-width: 768px) {

            table th,
            table td {
                display: block;
                width: 100%;
            }

            table thead {
                display: none;
            }

            table tbody tr {
                display: block;
                margin-bottom: 10px;
            }

            table tbody tr td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            table tbody tr td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 10px;
                font-weight: bold;
                text-align: left;
            }

        }
    </style>
@endsection
@section('title', 'Formatos Rf001 enviados a revisión | SIVyC Icatech')
@php
    $movimiento = json_decode($getConcentrado->movimientos, true);
    $uploadFiles = json_decode($getConcentrado->archivos, true);
    $importeTotal = 0;
@endphp
@section('content')
    <div class="content-wrapper">
        <section class="content p-4">
            @if (session('message'))
                <div class="alert alert-success" role="alert">
                    {{ session('message') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <a href="{{ route('reporte.rf001.sent') }}">A Revisión </a>/ Detalles de Reporte RF-001
                    </div>
                </div>
                <div class="card-body" style="display:block;">
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1">
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-center text-muted">
                                                Memorándum
                                            </span>
                                            <span
                                                class="info-box-number text-center text-muted mb-0">{{ $getConcentrado->memorandum }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-center text-muted">
                                                Periodo del concentrado
                                            </span>
                                            <span
                                                class="info-box-number text-center text-muted mb-0">{{ $getConcentrado->periodo_inicio . ' - ' . $getConcentrado->periodo_fin }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-center text-muted">
                                                Estado
                                            </span>
                                            <span
                                                class="info-box-number text-center text-muted mb-0">{{ $getConcentrado->estado }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h4>DETALLES DEL CONCENTRADO DE INGRESOS</h4>
                                    <div class="post">
                                        <div class="user-block">
                                            <span class="username">UNIDAD:</span>
                                            <span class="description"
                                                style="font-weight: bold;">{{ $getConcentrado->unidad }}</span>
                                        </div>
                                        <div class="user-block">
                                            <span class="username">ENVIA:</span>
                                            <span class="description" style="font-weight: bold;">
                                                {{ $getConcentrado->envia != null ? $getConcentrado->envia : 'NO ESPECIFICADO' }}
                                            </span>
                                        </div>
                                        <div class="user-block">
                                            <span class="username">DIRIGIDO A:</span>
                                            <span class="description" style="font-weight: bold;">
                                                {{ $getConcentrado->dirigido != null ? $getConcentrado->dirigido : 'NO ESPECIFICADO' }}
                                            </span>
                                        </div>
                                        <div class="user-block">
                                            <ul class="list-unstyled">
                                                <li>
                                                    <a href="" class="btn-link text-secondary">
                                                        <i class="far fa-fw fa-file-pdf"></i>
                                                        Generar Formato de Concentrados
                                                    </a>
                                                </li>
                                                {{-- <li>
                                                    <a href="" class="btn-link text-secondary">
                                                        <i class="far fa-fw fa-file-word"></i>
                                                        Functional-requirements.docx
                                                    </a>
                                                </li> --}}
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>FOLIO</th>
                                                    <th>CURSO</th>
                                                    <th>CONCEPTO</th>
                                                    <th>DOCUMENTO</th>
                                                    <th>IMPORTES</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($movimiento as $item)
                                                    <tr>
                                                        <td>{{ $item['folio'] }}</td>
                                                        <td>
                                                           @if ($item['curso'] != null)
                                                            {{ $item['curso'] }}
                                                           @else
                                                            {{ $item['descripcion'] }}
                                                           @endif
                                                        </td>
                                                        <td>{{ $item['concepto'] }}</td>
                                                        <td>
                                                            <a class="nav-link pt-0" href="{{$pathFile}}{{ $item['documento'] }}" target="_blank">
                                                                <i  class="far fa-file-pdf  fa-2x text-danger"  title='DESCARGAR RECIBO DE PAGO OFICIALIZADO.'></i>
                                                            </a>
                                                        </td>
                                                        <td style="text-align: end;">
                                                           $ {{ number_format($item['importe'], 2, '.', ',') }}
                                                        </td>
                                                    </tr>
                                                   @php
                                                    $importeTotal += $item['importe'];
                                                   @endphp
                                                @endforeach
                                                <tr>
                                                    <td colspan="4" style="text-align: end;"><b>SUBTOTAL</b></td>
                                                    <td><b>$ {{ number_format($importeTotal, 2, '.', ',') }}</b></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-4 order-1 order-md-2">
                            <h3 class="text-primary">
                                <i class="fa fa-file-archive-o"></i>
                                Elementos Adjuntos
                            </h3>
                            <h5 class="mt-5 text-muted">Archivos</h5>
                            <ul class="list-unstyled">
                                @if (!empty($uploadFiles))
                                    @foreach ($uploadFiles as $file)
                                        <li>
                                            <a href="" class="btn-link text-secondary">
                                                <i class="far fa-fw fa-file-word"></i>
                                                Functional-requirements.docx
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    <li>
                                        <h4>No hay Elementos Adjutos</h4>
                                    </li>
                                @endif
                            </ul>
                            <div class="text-left mt-5 mb-3">
                                <a href="http://" class="btn btn-warning  mt-1">CARGAR DOCUMENTOS</a>
                                <a href="http://" class="btn mr-3 mt-2">ENVIAR</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
