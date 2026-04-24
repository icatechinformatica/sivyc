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
        
        {!! Form::open(['route' => 'home.post', 'method' => 'post', 'id'=>'frm', 'enctype' => 'multipart/form-data']) !!}
        <div class="d-flex justify-content-center mb-4">
            <div class="form-inline">
                <label class="mr-2 font-weight-bold">Período de Análisis:</label>
                {{ Form::select('mes', $meses, $mes ,['id'=>'mes','class' => 'form-control mr-sm-2','title' => 'MES','placeholder' => 'MES']) }}  
                {{ Form::select('ejercicio', $ejercicios, $anio, ['id'=>'ejercicio','class' => 'form-control mr-sm-2','title' => 'EJERCICIO','placeholder'=>'EJERCICIO']) }}
                {{ Form::button('FILTRAR', ['id'=>'buscar','class' => 'btn btn-primary']) }}                    
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6 border-right">
                <h4 class="text-center">GESTIÓN EXTEMPORÁNEA</h4><BR/>
                <div class="form-group">
                    <canvas id="myChart" width="600" height="300"></canvas>
                </div>
            </div>
            <div class="form-group col-md-6">                
                <h4 class="text-center">SOLICITUDES DE ASISTENCIA TÉCNICA</h4>
                <h5 class="text-center text-secondary">{{$subtit}}</h5><BR/>
                <div class="form-group">
                    <canvas id="myChart2" width="600" height="320"></canvas>
                </div>
                <div class="mt-4" id="top-requerimientos-container" style="display: none;">
                    <h6 class="text-center font-weight-bold text-muted">MAYOR INCIDENCIA DE REQUERIMIENTOS:</h6>
                    <ul id="top-requerimientos-list" class="list-group list-group-flush text-sm" style="font-size: 13px;">
                        <!-- JS populated -->
                    </ul>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    @section('script_content_js') 
        <script src="{{ asset('js/tablero/Chart.min.js') }}"></script>        	
        <script language="javascript"> 
            $(document).ready(function(){


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
                var ctx2 = document.getElementById('myChart2').getContext('2d');
                var curso2 = <?php echo $data_asistencia ? json_encode(json_decode($data_asistencia)) : '{"label":[], "total":[]}'; ?>;
                var myChart2 = new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: curso2.label,
                        datasets: [{
                            label:'SOLICITUDES',
                            data: curso2.total,
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

                // Llenar el top requerimientos (carga inicial)
                function renderTopRequerimientos(asistenciaData) {
                    if (asistenciaData && asistenciaData.top_requerimientos && Array.isArray(asistenciaData.top_requerimientos) && asistenciaData.top_requerimientos.length > 0) {
                        $('#top-requerimientos-container').show();
                        var reqList = $('#top-requerimientos-list');
                        reqList.empty();
                        asistenciaData.top_requerimientos.forEach(function(req) {
                            reqList.append('<li class="list-group-item py-1 border-0"><i class="fa fa-caret-right text-danger mr-2"></i> ' + (req.nombre || req) + ' <span class="badge badge-secondary float-right">' + (req.cantidad || '') + '</span></li>');
                        });
                    } else {
                        $('#top-requerimientos-container').hide();
                    }
                }
                
                renderTopRequerimientos(curso2);

                // Función AJAX para actualizar ambas gráficas
                $("#buscar" ).click(function(e){ 
                    e.preventDefault();
                    var btn = $(this);
                    btn.prop('disabled', true).text('CARGANDO...');
                    
                    $.ajax({
                        url: "{{route('home.post')}}",
                        type: 'POST',
                        data: $('#frm').serialize(),
                        success: function(response) {
                            btn.prop('disabled', false).text('FILTRAR');
                            
                            // Actualizar Gráfica 1
                            if(response.cursos) {
                                myChart.data.labels = response.cursos.label || [];
                                myChart.data.datasets[0].data = response.cursos.total || [];
                                myChart.data.datasets[1].data = response.cursos.ex1 || [];
                                myChart.data.datasets[2].data = response.cursos.ex2 || [];
                                myChart.update();
                            }
                            
                            // Actualizar Gráfica 2
                            $('.text-secondary').text(response.subtit);
                            
                            if(response.asistencia) {
                                myChart2.data.labels = response.asistencia.label || [];
                                myChart2.data.datasets[0].data = response.asistencia.total || [];
                                renderTopRequerimientos(response.asistencia);
                            } else {
                                myChart2.data.labels = [];
                                myChart2.data.datasets[0].data = [];
                                renderTopRequerimientos(null);
                            }
                            myChart2.update();
                        },
                        error: function(err) {
                            console.error(err);
                            btn.prop('disabled', false).text('FILTRAR');
                            alert('Ocurrió un error al consultar los datos.');
                        }
                    });
                });
            });

            

        </script>
    @endsection
@endsection
