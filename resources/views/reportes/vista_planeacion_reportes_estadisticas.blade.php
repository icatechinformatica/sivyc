@extends('theme.sivyc.layout')

@section('title', 'Estadisticas | SIVYC ICATECH')

@section('content')
    <div class="container-fluid">
        <div class="row my-4">
            <div class="col d-flex justify-content-center"><h4><strong>REPORTE ESTADISTICO DEL FORMATO T</strong></h4></div>
        </div>

        <form id="formFechas" action="{{route('reportes.planeacion.estadisticas')}}" method="get">
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
                    <th style="width: 600px">Categoria</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Cursos Realizados</td>
                    <td>{{$totalCursos}}</td>
                </tr>
                <tr>
                    <td>Total de Beneficiarios</td>
                    <td>{{$beneficiarios}}</td>
                </tr>
                <tr>
                    <td>Total de Horas</td>
                    <td>{{$horas}}</td>
                </tr>
                <tr>
                    <td>Total de Mujeres</td>
                    <td>{{$mujeres}}</td>
                </tr>
                <tr>
                    <td>Total de Hombres</td>
                    <td>{{$hombres}}</td>
                </tr>
                <tr>
                    <td>Total de Egresados</td>
                    <td>{{$egresados}}</td>
                </tr>
                <tr>
                    <td>Total de Deserción</td>
                    <td>{{$desercion}}</td>
                </tr>
                <tr>
                    <td>Cursos EXT</td>
                    <td>{{$ext}}</td>
                </tr>
                <tr>
                    <td>Cursos CAE</td>
                    <td>{{$cae}}</td>
                </tr>
                <tr>
                    <td>Cursos EMP</td>
                    <td>{{$emp}}</td>
                </tr>
                <tr>
                    <td>Municipios Atendidos</td>
                    <td>{{$totalMunicipios}}</td>
                </tr>

            </tbody>
        </table>

        <div class="row d-flex justify-content-end">
            <button id="btnReporteXls" type="button" class="btn btn-success mb-4">Generar Reporte XLS</button>
            <form target="_blank" id="formReporteXls" action="{{ route('reportes.planeacion.estadisticasXls') }}" method="get">@csrf</form>

            <button id="btnReporteCronograma" type="button" class="btn btn-info mb-4">Generar Reporte PDF</button>
            <form target="_blank" id="formReporteCronograma" action="{{ route('reportes.planeacion.estadisticasPdf') }}" method="get">@csrf</form>
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
