<!-- Creado por Daniel Méndez-->
@extends('theme.sivyc.layout')

@section('title', 'Inicio | Sivyc Icatech')

@section('content')
    @php
        if($data=='[]') $data = '{"label":["CATAZAJA","COMITAN","JIQUIPILAS","OCOSINGO","REFORMA","SAN CRISTOBAL","TAPACHULA","TONALA","TUXTLA","VILLAFLORES","YAJALON"],"total":[0,0,0,0,0,0,0,0,0,0,0]}';
        if($data_asistencia== null){
            $data_asistencia = '{"label":["CATAZAJA","COMITAN","JIQUIPILAS","OCOSINGO","REFORMA","SAN CRISTOBAL","TAPACHULA","TONALA","TUXTLA","VILLAFLORES","YAJALON"],"total":[0,0,0,0,0,0,0,0,0,0,0]}';
            $subtit = "NO DISPONBILE";
        }else $subtit = $mes_ant." ". date("Y");
    @endphp
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <div class="card card-body">
        <h3 class="text-center text-muted font-weight-bold">RANKING</h2>
        <div class="row">
            <div class="form-group col-md-6">
                {{ html()->form('POST', route('home.post'))->id('frm')->acceptsFiles()->open() }}
                <h4 class="text-center">GESTIÓN EXTEMPORÁNEA</h4><BR/>
                <div class="form-row form-inline">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    {{ html()->select('mes', $meses, $mes)
                        ->id('mes')
                        ->class('form-control mr-sm-2')
                        ->attribute('title', 'MES')
                        ->placeholder('SELECCIONAR') }}

                    {{ html()->select('ejercicio', $ejercicios, $anio)
                        ->id('ejercicio')
                        ->class('form-control mr-sm-2')
                        ->attribute('title', 'EJERCICIO')
                        ->placeholder('SELECCIONAR') }}

                    {{ html()->button('FILTRAR')
                        ->id('buscar')
                        ->class('btn') }}

                </div>
                <div class="form-group">
                    <canvas id="myChart" width="600" height="300"></canvas>
                </div>
                {{ html()->form()->close() }}

            </div>
            <div class="form-group col-md-6">
                <h4 class="text-center">SOLICITUDES DE ASISTENCIA TÉCNICA</h4><h5 class="text-center">{{$subtit}}</h5><BR/>
                <div class="form-group">
                    <canvas id="myChart2" width="600" height="320"></canvas>
                </div>
            </div>
        </div>
    </div>
    @section('script_content_js')
        <script src="{{ asset('js/tablero/Chart.min.js') }}"></script>
        <script language="javascript">
            $(document).ready(function(){
                $("#buscar" ).click(function(){ $('#frm').attr('action', "{{route('home.post')}}"); $('#frm').attr('target', '_self').submit();});

                var ctx = document.getElementById('myChart').getContext('2d');
                var curso = JSON.parse('<?php echo $data; ?>');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: curso.label,
                        datasets: [{
                            label:'CURSOS',
                            data: curso.total,
                            backgroundColor: 'rgba(176, 154, 91,0.8)'
                        },
                        {
                            label:'EXTEMPORANEIDAD ARC01',
                            data: curso.ex1,
                            backgroundColor: 'rgba(98,17,50, 0.8)'
                        },
                        {
                            label:'EXTEMPORANEIDAD ARC02',
                            data: curso.ex2,
                            backgroundColor: 'rgba(51,51,51, 0.8)'
                        }]
                    },
                    options: {
                        scales:{
                            yAxes:[{
                                ticks:{
                                    beginAtZero: true
                                }
                            }]
                        },
                        "hover": {
                            "animationDuration": 0
                        },
                        "animation": {
                            "duration": 1,
                            "onComplete": function() {
                                var chartInstance = this.chart,
                                    ctx = chartInstance.ctx;

                                ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart
                                    .defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'bottom';

                                this.data.datasets.forEach(function(dataset, i) {
                                    var meta = chartInstance.controller.getDatasetMeta(i);
                                    meta.data.forEach(function(bar, index) {
                                        var data = dataset.data[index];
                                        ctx.fillText(data, bar._model.x, bar._model.y);
                                    });
                                });

                            }
                        },
                        legend: {
                            "display": true
                        },
                        tooltips: {
                            "enabled": false
                        }
                    }
                });

                /*RANKING SOLICITUDES DE APOYO O ASISTENCIA TÉCNICA*/
                var ctx = document.getElementById('myChart2').getContext('2d');
                //var curso = JSON.parse('{"label":["CATAZAJA","COMITAN","JIQUIPILAS","OCOSINGO","REFORMA","SAN CRISTOBAL","TAPACHULA","TONALA","TUXTLA","VILLAFLORES","YAJALON","DTA"],"total":[5,7,1,7,0,3,4,3,14,2,7,6]}');
                var curso = JSON.parse('<?php echo $data_asistencia; ?>');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: curso.label,
                        datasets: [{
                            label:'SOLICITUDES',
                            data: curso.total,
                            backgroundColor: 'rgba(98,17,50,0.8)'
                        },
                        ]
                    },
                    options: {
                        scales:{
                            yAxes:[{
                                ticks:{
                                    beginAtZero: true
                                }
                            }]
                        },
                        "hover": {
                            "animationDuration": 0
                        },
                        "animation": {
                            "duration": 1,
                            "onComplete": function() {
                                var chartInstance = this.chart,
                                    ctx = chartInstance.ctx;

                                ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart
                                    .defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'bottom';

                                this.data.datasets.forEach(function(dataset, i) {
                                    var meta = chartInstance.controller.getDatasetMeta(i);
                                    meta.data.forEach(function(bar, index) {
                                        var data = dataset.data[index];
                                        ctx.fillText(data, bar._model.x, bar._model.y);
                                    });
                                });

                            }
                        },
                        legend: {
                            "display": true
                        },
                        tooltips: {
                            "enabled": false
                        }
                    }
                });

            });



        </script>
    @endsection
@endsection
