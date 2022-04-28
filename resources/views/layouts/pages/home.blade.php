<!-- Creado por Daniel Méndez-->
@extends('theme.sivyc.layout')

@section('title', 'Inicio | Sivyc Icatech')

@section('content')
    <div class="card card-body" style=" min-height:450px;">
        <div class="row">
            <div class="form-group col-md-6">
                {!! Form::open(['route' => 'home.post', 'method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data']) !!}
                <h2 style="text-align:center;">RANKING (GESTION EXTEMPORANEA)</h2>
                <div class="form-row mb-2">
                    <div class="form-row col-md-3 ml-2">
                        <label for="">MES:</label>
                        {!! Form::select('mes', $meses, $mes, ['id'=>'mes','placeholder'=>'-- SELECCIONAR --','class'=>'form-control']) !!}
                    </div>   
                    <div class=" form-row col-md-3 ml-2">
                        <label for="">EJERCICIO:</label>
                        {!! Form::select('ejercicio', $ejercicios, $anio, ['id'=>'ejercicio','placeholder'=>'-- SELECCIONAR --','class'=>'form-control']) !!}
                    </div>
                    <div class="form-group col-md-1">
                        {{ Form::button('FILTRAR', ['id'=>'buscar','class' => 'btn btn-primary']) }}
                    </div>
                </div>
                <div class="form-group">
                    <canvas id="myChart" width="600" height="300"></canvas>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="form-group col-md-6">
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
                            label:'TOTAL DE CURSOS',
                            data: curso.total,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)'
                        },
                        {
                            label:'GESTIONES EXTEMPORANEAS ARC01',
                            data: curso.ex1,
                            backgroundColor: 'rgba(255, 99, 132, 0.5)'
                        },
                        {
                            label:'GESTIONES EXTEMPORANEAS ARC02',
                            data: curso.ex2,
                            backgroundColor: 'rgba(153, 102, 255, 0.5)'
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
            });
        </script>
    @endsection
@endsection
