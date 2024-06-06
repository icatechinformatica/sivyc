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
@section('title', 'Rf001 | SIVyC Icatech')
@section('content')
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
                    <b>AÑO ACTUAL {{ $currentYear }} </b> <br>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::select('unidad', $datos['unidades'], $getConcentrado ? $getConcentrado->unidad : '', ['id' => 'unidad', 'placeholder' => '- UNIDAD -', 'class' => 'form-control  mr-sm-2']) }}
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    {{ Form::label('fechaInicio', 'Fecha Inicio', ['class' => 'awesome']) }}
                    {{ Form::date('fechaInicio', $fechaInicio, ['class' => 'form-control mr-sm-2', 'id' => 'fechaInicio']) }}
                </div>
                <div class="form-group col-md-3">
                    {{ Form::label('fechaFin', 'Fecha Fecha Fin', ['class' => 'awesome']) }}
                    {{ Form::date('fechaFin', $fechaFin, ['class' => 'form-control mr-sm-2', 'id' => 'fechaFin']) }}
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
                                            $depositos = json_decode($item->depositos);
                                        @endphp
                                        @foreach ($depositos as $k)
                                            {{ $k->folio }} &nbsp;
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
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $item->clave_contrato . '_' . $item->num_recibo . '_' . $item->id }}"
                                                        id="seleccionar_{{ $item->folio_recibo }}"
                                                        @if (in_array($item->folio_recibo, $foliosMovimientos)) checked @endif

                                                        name="seleccionados[]">
                                                </div>
                                            @else
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $item->clave_contrato . '_' . $item->num_recibo . '_' . $item->id }}"
                                                        id="seleccionar_{{ $item->folio_recibo }}" name="seleccionados[]"
                                                        @if (in_array($item->clave_contrato . '_' . $item->num_recibo . '_' . $item->id, $selectedCheckboxes)) checked @endif
                                                        >
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
                            <button type="submit"
                                class="btn mb-2">{{ $getConcentrado ? 'Modificar' : 'Generar' }}</button>
                        </form>
                        {{ Form::hidden('id_unidad', $idUnidad, ['id' => 'id_unidad', 'class' => 'form-control ']) }}
                        {{ Form::hidden('periodoInicio', $fechaInicio, ['class' => 'form-control mr-sm-2', 'id' => 'periodoInicio']) }}
                        {{ Form::hidden('periodoFIn', $fechaFin, ['class' => 'form-control mr-sm-2', 'id' => 'periodoFIn']) }}
                        {{ Form::Hidden('unidad', $unidad, ['id' => 'unidad', 'class' => 'form-control ']) }}
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
@endsection
