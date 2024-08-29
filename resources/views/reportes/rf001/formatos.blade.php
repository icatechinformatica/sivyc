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
@endsection
@section('title', 'Formatos Rf001 enviados a revisión | SIVyC Icatech')
@section('content')
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
            @if (count($data) > 0)
                <div class="p-0 m-0">
                    {{ Form::open(['route' => 'reporte.rf001.store', 'method' => 'POST', 'id' => 'frmString']) }}
                    <table class="table table-hover" id="tabla">
                        <thead>
                            <tr>
                                <th scope="col">MEMORANDUM</th>
                                <th scope="col">UNIDAD</th>
                                <th scope="col">ESTADO</th>
                                <th scope="col">PERIODO</th>
                                <th scope="col">ACCIONES</th>
                                <th scope="col">DETALLES</th>
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
                                    <th scope="row">{{ $item->memorandum }}</th>
                                    <td>{{ $item->unidad }}</td>
                                    <td>{{ $item->estado }}</td>
                                    <td>
                                        {{ $formatoInicio . ' - ' . $formatoFin }}
                                    </td>
                                    <td class="text-left">
                                        @switch($item->estado)
                                            @case('GENERADO')
                                                <a class="nav-link pt-0"
                                                    href="{{ route('reporte.rf001.details', ['concentrado' => $item->id]) }}">
                                                    <i class="fa fa-edit  fa-2x fa-lg text-primary" aria-hidden="true"
                                                        padding-right: 12px;" title='EDITAR REGISTROS'></i>
                                                </a>
                                            @break

                                            @case('ENFIRMA')
                                                <a class="nav-link pt-0"
                                                    href="{{ route('reporte.generar.firma', ['id' => $item->id]) }}">
                                                    <i class="fas fa-pen fa-2x fa-lg text-danger" aria-hidden="true" title='EN FIRMA'></i>
                                                </a>
                                            @break

                                            @default
                                        @endswitch
                                    </td>
                                    <td class="text-left">
                                        <a class="nav-link pt-0" href="{{ route('reporte.rf001.set.details', ['id' => $item->id]) }}">
                                            <i class="fa fa-eye fa-2x fa-lg text-grey" aria-hidden="true" title="MOSTRAR FORMATO RF001"></i>
                                        </a>
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
                <div class="text-center p-5 bg-light">
                    <h5> <b>NO SE ENCONTRARON REGISTROS</b></h5>
                </div>
            @endif
        </div>
    </div>
@endsection
