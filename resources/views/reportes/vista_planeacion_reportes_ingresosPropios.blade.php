@extends('theme.sivyc.layout')

@section('title', 'Reporte Ingresos Propios | SIVYC ICATECH')

@section('content')
    <div class="container-fluid">
        <div class="row my-4">
            <div class="col d-flex justify-content-center"><h4><strong>REPORTE DE INGRESOS PROPIOS</strong></h4></div>
        </div>

        <form id="formFechas" action="{{route('reportes.planeacion.ingresos_propios')}}" method="get">
            @csrf
            
            <div class="row mt-2 d-flex justify-content-center">
                <!-- fecha inicial -->
                <div class="col-3">
                    <div class="form-group">
                        <label for="fecha_inicio" class="control-label">Fecha de Inicio</label>
                        <input type='text' id="fecha_inicio" autocomplete="off" readonly="readonly" name="fecha_inicio"
                            class="form-control datepicker" value="{{$fechaInicio}}" required>
                    </div>
                </div>

                <!-- Fecha conclusion -->
                <div class="col-3">
                    <div class="form-group">
                        <label for="fecha_termino" class="control-label">Fecha de Termino</label>
                        <input type='text' id="fecha_termino" autocomplete="off" readonly="readonly" name="fecha_termino"
                            class="form-control datepicker" value="{{$fechaTermino}}" required>
                    </div>
                </div>

                <div class="col-3 d-flex align-items-center">
                    <button type="submit" id="btnBuscarCurso" class="btn btn-primary">FILTRAR</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th>Unidad</th>
                    <th>{{$yearAnt}}</th>
                    <th>{{$year}}</th>
                    <th>Diferencia vs {{$yearAnt}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($totalesPeriodoActual as $key => $item)
                    <tr>
                        <td>{{$item->ubicacion}}</td>
                        <td>
                            @if (isset($totalesPeriodoAnterior[$key]->total))
                                {{$totalesPeriodoAnterior[$key]->total}}
                            @else
                                0.0
                            @endif
                        </td>
                        <td>{{$item->total}}</td>
                        <td>
                            @if (isset($totalesPeriodoAnterior[$key]->total))
                                {{$item->total - $totalesPeriodoAnterior[$key]->total}}
                            @else
                                {{$item->total}}
                            @endif
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <td><strong>Total</strong></td>
                    <td>
                        <div class="d-none">{{$totalPeriodoAnt = 0}}</div>
                        @foreach ($totalesPeriodoAnterior as $item1)
                            <div class="d-none">{{$totalPeriodoAnt += $item1->total}}</div>
                        @endforeach
                        <strong>{{$totalPeriodoAnt}}</strong>
                    </td>
                    <td>
                        <div class="d-none">{{$totalPeriodoAct = 0}}</div>
                        @foreach ($totalesPeriodoActual as $item2)
                            <div class="d-none">{{$totalPeriodoAct += $item2->total}}</div>
                        @endforeach
                        <strong>{{$totalPeriodoAct}}</strong>
                    </td>
                    <td>
                        <div class="d-none">{{$totalDiferencia = 0}}</div>
                        @foreach ($totalesPeriodoActual as $key => $item3)
                            @if (isset($totalesPeriodoAnterior[$key]->total))
                                <div class="d-none">{{$totalDiferencia += ($item3->total - $totalesPeriodoAnterior[$key]->total)}}</div>
                            @else
                                <div class="d-none">{{$totalDiferencia += $item3->total}}</div>
                            @endif
                        @endforeach
                        <strong>{{$totalDiferencia}}</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="row d-flex justify-content-end">
            <button id="btnReporteXls" type="button" class="btn btn-success mb-4">Generar Reporte XLS</button>
            <form target="_blank" id="formReporteXls" action="{{ route('reportes.planeacion.ingresos_propiosXls') }}" method="get">@csrf</form>

            <button id="btnReporteCronograma" type="button" class="btn btn-info mb-4">Generar Reporte PDF</button>
            <form target="_blank" id="formReporteCronograma" action="{{ route('reportes.planeacion.ingresos_propiosPdf') }}" method="get">@csrf</form>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalMessages" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background: #541533">
                        <h5 class="modal-title text-white" id="titulo">cj</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <strong id="mensaje"></strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script_content_js')
    <script>
        // formato fechas
        var dateFormat = "yy-mm-dd",
            from = $("#fecha_inicio").datepicker({
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: 'yy-mm-dd'
            }).on("change", function() {
                to.datepicker("option", "minDate", getDate(this));
            }),
            to = $("#fecha_termino").datepicker({
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: 'yy-mm-dd'
            })
            .on("change", function() {
                // from.datepicker("option", "maxDate", getDate(this));
            });

        function getDate(element) {
            var date;
            try {
                date = $.datepicker.parseDate(dateFormat, element.value);
            } catch (error) {
                date = null;
            }
            return date;
        }

        $('#formFechas').validate({
            rules: {
                fecha_inicio: {
                    required: true
                },
                fecha_termino: {
                    required: true
                }
            },
            messages: {
                fecha_inicio: {
                    required: 'Campo requerido'
                },
                fecha_termino: {
                    required: 'Campo requerido'
                }
            }
        });

        $('#btnReporteCronograma').click(function (){
            if ($('#fecha_inicio').val() == '' || $('#fecha_termino').val() == '') {
                $('#titulo').html('Generar Reporte');
                $('#mensaje').html('Debe realizar una busqueda antes de generar un reporte');
                $('#modalMessages').modal('show');
            } else {
                $('#formReporteCronograma').submit();
            }
        });
        
        $('#btnReporteXls').click(function (){
            if ($('#fecha_inicio').val() == '' || $('#fecha_termino').val() == '') {
                $('#titulo').html('Generar Reporte');
                $('#mensaje').html('Debe realizar una busqueda antes de generar un reporte');
                $('#modalMessages').modal('show');
            } else {
                $('#formReporteXls').submit();
            }
        });
    </script>
@endsection