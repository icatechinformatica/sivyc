<!--Creado por Romelia Pérez Nangüelú--rpnanguelu@gmail.com -->
@extends('theme.global.layout')
@section('title', 'SITUACIÓN ACTUAL | Sivyc Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/tablero/unidades.css') }}"/>
    @if ($message = Session::get('success'))
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0">
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0">                        
                    <h2 class="titulo">Situaci&oacute;n Actual de las Unidades</h2>
                    <br />
                    <div class="row align-items-center">
                        <div class="col-8">                            
                            {!! Form::open(['route' => 'tablero.unidades.index', 'method'=> 'POST', 'role'=> 'search', 'class' => 'form-inline', 'id'=>'frm', 'name'=>'frm', 'enctype'=>'multipart/form-data' ]) !!}
                                 {!! Form::select('id_unidad', $lst_unidad,$id_unidad ,array('id'=>'id_unidad','class' => 'form-control  mr-sm-2','placeholder' => 'UNIDAD')) !!}                                 
                                 {!! Form::select('mes_inicio', $lst_meses,$mes_inicio ,array('id'=>'mes_inicio','class' => 'form-control  mr-sm-2','placeholder' => 'MES INICIAL')) !!}
                                 {!! Form::select('mes_fin', $lst_meses,$mes_fin ,array('id'=>'mes_fin','class' => 'form-control  mr-sm-2','placeholder' => 'MES FINAL')) !!}
                                 {!! Form::select('ejercicio', $lst_ejercicio,$ejercicio ,array('id'=>'ejercicio','class' => 'form-control  mr-sm-2')) !!}
                                 {{ Form::button('FILTRAR', array('class' => 'btn btn-outline-info my-1 my-sm-0', 'type' => 'button', 'id' => 'filtrar' )) }}
                            {!! Form::close() !!}                                
                        </div>                            
                    </div>
                </div>
                 <div class="table-responsive" style="min-height:420px" id="tabla">
                    <table class="table align-items-center table-flush text-center">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col"></th>  
                                <th scope="col"></th>                                
                                <th scope="col" colspan="4" class="text-center"><a href="#" onclick="generarGRAF(0,'CURSOS APERTURADOS')"  class="fa fa-cube text-blue" data-toggle="modal" data-target="#textGRAF"> CURSOS</a></th>                                
                                <th scope="col" colspan="4" class="text-center"><a href="#" onclick="generarGRAF(1,'BENEFICIARIOS')"  class="fa fa-cube text-blue" data-toggle="modal" data-target="#textGRAF"> BENEFICIARIOS </a></th>
                                <th scope="col" colspan="4" class="text-center"><a href="#" onclick="generarGRAF(2, 'EN HORAS')"  class="fa fa-cube text-blue" data-toggle="modal" data-target="#textGRAF"> HORAS </a></th>
                                <th scope="col" colspan="2" class="text-center"><a href="#" onclick="generaGRAF_LINE()"  class="fa fa-cube text-blue" data-toggle="modal" data-target="#textGRAF"> PAGADO </a></th>
                            </tr>
                            <tr>
                                <th scope="col" style="background:#30302f; color: white;">#</th>  
                                <th scope="col" class="text-left" style="background:#30302f; color: white" >UNIDAD DE CAPACITACI&Oacute;N</th>                                
                                <th scope="col" style="background:#30302f; color: white">PROG</th>                                
                                <th scope="col" style="background:#30302f; color: white">APER</th>
                                <th scope="col" style="background:#30302f; color: white">DIF</th>
                                <th scope="col" style="background:#30302f; color: white">%CUMPL</th>
                                <th scope="col" style="background:#30302f; color: white">PROG</th>                                
                                <th scope="col" style="background:#30302f; color: white">APER</th>
                                <th scope="col" style="background:#30302f; color: white">DIF</th>
                                <th scope="col" style="background:#30302f; color: white">%CUMPL</th>
                                <th scope="col" style="background:#30302f; color: white">PROG</th>                                
                                <th scope="col" style="background:#30302f; color: white">APER</th>
                                <th scope="col" style="background:#30302f; color: white">DIF</th>
                                <th scope="col" style="background:#30302f; color: white">%CUMPL</th>
                                <th scope="col" style="background:#30302f; color: white">CURSOS</th> 
                                <th scope="col"style="background:#30302f; color: white">INVERSI&Oacute;N</th>                                                                 
                            </tr>
                        </thead>
                        <tbody class="list">
                            <?php 
                            $i=1;   
                            $rojo = 'class="text-danger"';
                            $rojo2 = 'class="bg-light text-danger"';                            
                            $gris = 'class="bg-light"';                               
                            ?>    
                        @foreach($data as $items)
                             @if($items->id_plantel==0)
                                <?php  
                                    $t = $tthis->totales($data, $items->id_unidad,$items->cursos_p,$items->horas_p); 
                                    $dif_cur =  $t['cursos_r']-$items->cursos_p;
                                    $dif_benef = $t['benef_r']-$items->cursos_p*$items->promedio_benef;
                                    $dif_hr = $t['horas_r']-$items->horas_p ;                               
                                    ?>                            
                                <tr class="text-dark" style="background-color: gray;">
                                    <td>{{ $i++ }}</td>                                    
                                    <td class="text-left" >{{ $items->unidad }}</td>                                    
                                    <td>{{ $items->cursos_p }}</td>
                                    <td>{{ $t['cursos_r'] }}</td>
                                    <td @if($dif_cur<0)<?php echo $rojo ?>@endif >{{ $dif_cur}}</td>
                                    <td>{{ $t['porc_cur_r'] }}%</td>                                    
                                    <td>{{  $items->cursos_p*$items->promedio_benef }}</td>
                                    <td>{{ $t['benef_r'] }}</td>
                                    <td @if($dif_benef<0)<?php echo $rojo ?>@endif >{{ $dif_benef }}</td>
                                    <td>
                                        @if($t['benef_r']>0 AND $items->cursos_p>0 AND $items->promedio_benef>0)
                                        {{ ROUND($t['benef_r']*100/($items->cursos_p*$items->promedio_benef)) }}% 
                                        @else {{ 0 }} @endif
                                    </td>
                                    <td>{{ $items->horas_p }}</td>
                                    <td>{{ $t['horas_r'] }}</td>
                                    <td @if($dif_hr<0)<?php echo $rojo ?>@endif >{{ $dif_hr }}</td>
                                    <td>{{ $t['porc_hr_r']}} %</td>                                    
                                    <td>{{ $t['cursos_pagados'] }}</td>                                              
                                    <td>{{ $t['inversion'] }}</td>
                                </tr>
                            @else
                                <?php
                                $dif_cur = $items->cursos_r-$items->cursos_p;
                                $dif_benef = $items->benef_r-$items->benef_p;
                                $dif_hr = $items->horas_r-$items->horas_p;
                                ?>
                                <tr>                                                        
                                    <td>{{ $i++ }}</td>                                    
                                    <td class="text-left" >{{ $items->unidad }}</td>                                    
                                    <td  class=" bg-light">{{ $items->cursos_p }}</td>
                                    <td  class=" bg-light">{{ $items->cursos_r }}</td>
                                    <td  @if($dif_cur<0)<?php echo $rojo2 ?>@else <?php echo $gris ?> @endif >{{ $dif_cur }}</td>                                    
                                    <td  class=" bg-light">{{ $items->porc_cur_p}}%</td>
                                    <td>{{ $items->benef_p }}</td>
                                    <td>{{ $items->benef_r }}</td>
                                    <td @if($dif_benef<0)<?php echo $rojo ?>@endif >{{ $dif_benef }}</td>
                                    <td>@if($items->benef_r>0 AND $items->benef_p>0){{ ROUND($items->benef_r*100/$items->benef_p) }}% 
                                    @else {{ 0 }} @endif</td>
                                    <td  class=" bg-light">{{ $items->horas_p }}</td>
                                    <td  class=" bg-light">{{ $items->horas_r }}</td>
                                    <td  @if($dif_hr<0)<?php echo $rojo2 ?>@else <?php echo $gris ?> @endif >{{ $dif_hr }}</td>                                    
                                    <td  class=" bg-light">{{ $items->porc_hr_p}}%</td>
                                    <td>{{ $items->cursos_pagados }}</td>
                                    <td>{{ $items->inversion }}</td>
                                </tr>
                            @endif
                        @endforeach                           
                        </tbody>
                    </table>
                    <br /><br />
                </div>
            </div>
        </div>
    </div>
    <div class="row">        
        <div id="textGRAF" class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body" id="graf">
                    <canvas id="myChart" width="600" height="300"></canvas>  
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">CERRAR</button>        
              </div>
            </div>
          </div>
        </div>
    </div>
    
  @endsection  
  @section('scripts_content')
    <script src="{{ asset('js/tablero/Chart.min.js') }}"></script>
    <script>
        $( "#filtrar" ).click(function() {
            var mes_ini = $( "#mes_inicio" ).val();
            var mes_fin = $( "#mes_fin" ).val();
            if(mes_ini>mes_fin && mes_fin) alert("Fechas no v\u00E1lidas, vuelve a intentar.");
            else $( "#frm" ).submit();
            
        });
        
        function generarGRAF(opt, title){      
            var dataP = <?php echo json_encode ($dataP)?>;
            var dataR = <?php echo json_encode ($dataR)?>;
            var ctx = document.getElementById("myChart");
            $("#title").text(title+' .- ICATECH');
            var data = {
                 labels: <?php echo json_encode($labels) ?>,
                 datasets: [{
                   label: '# PROGRAMADOS',
                    data: dataP[opt],
                    backgroundColor: "rgba(0, 100, 0, 0.7)",
                    borderColor: "rgba(0, 100, 0, 1)",
                    borderWidth: 1
                 }, {
                   label: '# APERTURADOS',
                    data: dataR[opt],
                    backgroundColor:"rgba(218, 165, 32, 0.7)",
                    borderColor: "rgba(218, 165, 32, 1)",
                    borderWidth: 1
                 }]
            }
            if(Math.max(...data.datasets[0].data)>Math.max(...data.datasets[1].data)){
                var BarMax = Math.max(...data.datasets[0].data);
            }else{
                var BarMax = Math.max(...data.datasets[1].data);
            }
            var myChart = new Chart(ctx, {
                 type: 'bar',
                 data: data,
                 options: {
                   "hover": {
                     "animationDuration": 0
                   },
                   "animation": {
                     "duration": 1,
                     "onComplete": function() {
                       var chartInstance = this.chart,
                         ctx = chartInstance.ctx;
            
                       ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                       ctx.textAlign = 'center';
                       ctx.textBaseline = 'bottom';
            
                       this.data.datasets.forEach(function(dataset, i) {
                         var meta = chartInstance.controller.getDatasetMeta(i);
                         meta.data.forEach(function(bar, index) {
                           var data = dataset.data[index];
                           ctx.fillText(data, bar._model.x, bar._model.y - 5);
                         });
                       });
                     }
                   },
                   legend: {
                        "display": true
                   },
                   tooltips: {
                        "enabled": false
                   },
                   scales: {
                        yAxes: [{
                            display: true,
                            gridLines: {
                                display: true
                            },
                       ticks: {
                            max: Math.max(BarMax+ Math.round(BarMax*.12)),
                            display: true,
                            beginAtZero: true
                       }
                     }],
                     xAxes: [{
                        gridLines: {
                            display: true
                        },
                       ticks: {
                            beginAtZero: false
                       }
                     }]
                }
            }
        
        });
     }
     
     function generaGRAF_LINE(){
        var speedCanvas = document.getElementById("myChart");
        var dataR = <?php echo json_encode ($dataR)?>;
        
        Chart.defaults.global.defaultFontFamily = "Arial";
        Chart.defaults.global.defaultFontSize = 12;
        $("#title").text('INVERSI\u00D3N DE CURSOS PAGADOS .- ICATECH');
        var speedData = {
            labels: <?php echo json_encode($labels) ?>,
            datasets: [{
                label: "Inversi\u00F3n",
                data:dataR[3],
                backgroundColor: "#bbedc9",
            }]
        };
        
        var chartOptions = {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    boxWidth: 80,
                    fontColor: 'black'
                }
            }
        };
        
        var lineChart = new Chart(speedCanvas, {
            type: 'line',
            data: speedData,
            options: chartOptions
        });
    }
</script>
@endsection
