<!--Creado por Romelia Pérez Nangüelú--rpnanguelu@gmail.com -->
@extends('theme.global.layout')
@section('title', 'CURSOS APERTURADOS | Sivyc Icatech')
@section('css_content')    
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"/>    
    <link rel="stylesheet" href="{{asset('css/tablero/cursos.css') }}"/>
    <link href="https://fonts.googleapis.com/css?family=Montserrat|Playfair+Display&display=swap" rel="stylesheet" />
@endsection
@section('content')  
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
    <div class="row" >
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0">                        
                    <h2 class="titulo" >Indicadores Cursos Aperturados</h2>                    
                    <div class="row align-items-center">
                        <div class="col-8"> 
                            {!! Form::open(['route' => 'tablero.cursos.index', 'method'=> 'POST', 'role'=> 'search', 'class' => 'form-inline', 'id'=>'frmBuscar', 'name'=>'frmBuscar', 'enctype'=>'multipart/form-data' ]) !!}
                                 {!! Form::select('ubicacion', $lst_ubicacion, $ubicacion ,array('id'=>'ubicacion','class' => 'form-control  mr-sm-2','placeholder' => 'UNIDAD')) !!}                                 
                                 {{ Form::date('fecha_inicio', $fecha_inicio , ['id'=>'fecha_inicio', 'data-date'=>$fecha_inicio, 'class' => 'form-control datepicker mr-sm-1', 'placeholder' => 'FECHA INICIO', 'readonly' =>'readonly']) }}
                                 {{ Form::date('fecha_termino', $fecha_termino , ['id'=>'fecha_termino', 'class' => 'form-control datepicker mr-sm-1', 'placeholder' => 'FECHA TERMINO', 'readonly' =>'readonly']) }}
                                 <!--{!! Form::select('status', [],'' ,array('id'=>'status','class' => 'form-control  mr-sm-2','placeholder' => 'STATUS')) !!}-->                                 
                                 {{ Form::submit('FILTRAR', array('class' => 'btn btn-outline-info my-2 my-sm-0', 'type' => 'button', 'id' => 'filtrar' )) }}                                 
                            {!! Form::close() !!}                                
                        </div>                            
                    </div>
                </div>                
                <div class="table-responsive" style="min-height:400px" id="tabla">
                    <table class="table align-items-center table-striped" >
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">UNIDAD /<br/>ACCI&Oacute;N M&Oacute;VIL</th>                                
                                <th scope="col" class="min-w" >CLAVE<br/>CURSO</th>
                                <th scope="col" class="min-w">CURSO</th>
                                <th scope="col">ESTATUS<br/>CURSO</th>
                                <th scope="col">FECHA<br />APERT</th>
                                <th scope="col">FECHAS<br />INI-TER</th>
                                <th scope="col">MOD</th>
                                <th scope="col" class="min-w">TIPO</th>
                                <th scope="col" class="min-w">INSTRUCTOR</th>
                                <th scope="col">STATUS<br/>PAGO</th>
                                <!--<th scope="col">FECHA<br/>PAGO</th>-->
                                <th scope="col">HONORARIOS</th>
                                <th scope="col">TOTAL<br/>ALUM</th>
                                <th scope="col">HRS</th>                                
                                <th scope="col" class="min-w">ORGANISMO</th>
                            </tr>
                        </thead>
                        <tbody class="list">    
                            @foreach($data as $items)
                                <tr>
                                    <td scope="row">{{ $items->unidad}}</td>
                                    <td class="text-center">{{ $items->clave}}</td>
                                    <td>{{ $items->curso}}</td>
                                    <td>{{ $items->status_curso}}</td>
                                    <td><?php echo date('d/m/Y',strtotime($items->fecha_apertura))?></td>
                                    <td><?php echo date('d/m/Y',strtotime($items->inicio))."- ".date('d/m/Y',strtotime($items->termino));?></td>
                                    <td class="text-center">{{ $items->modalidad}}</td>
                                    <td>{{ $items->tipo}}</td>
                                    <td>{{ $items->instructor}}</td>
                                    <td class="text-right">{{ $items->status_pago}}</td>                         
                                    <td class="text-right">{{ $items->honorarios}}</td>
                                    <td class="text-center">{{ $items->total_alumnos}}</td>
                                    <td class="text-center">{{ $items->horas}}</td>                                    
                                    <td>{{ $items->organismo}}</td>
                                </tr>
                            @endforeach                           
                        </tbody>
                    </table>
                </div>
                                <!-- Card footer -->
                <div class="card-footer py-4">
                    <nav aria-label="...">
                        <ul class="pagination justify-content-end mb-0">
                            <li class="page-item">                            
                            {{ $data->appends(Request::only(['ubicacion','fecha_inicio','fecha_termino']))->render() }}
                            </li>
                        </ul>
                    </nav>
                </div>
                
            </div>
        </div>
    </div>
  
@endsection
@section('scripts_content')
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>    

    <script>
        $(function() {
            $( ".datepicker" ).datepicker({
                dateFormat: "yy-mm-dd"
            });            
         });         
 
    </script>  
@endsection
