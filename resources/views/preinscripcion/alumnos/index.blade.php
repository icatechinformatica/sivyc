@extends('theme.sivyc.layout')

@section('title', 'Alumnos | SIVyC Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />   
    <style>
        .table tr th { text-align: center; padding:12px;}       
    </style>
@endsection
@section('content')
<div class="card-header">
    Preinscripción / Alumnos Matriculados
</div>
<div class="card card-body">
        @if($message)
            <div class="row ">
                <div @if(isset($message["ERROR"])) class="col-md-12 alert alert-danger" @else class="col-md-12 alert alert-success"  @endif>
                    <p>@if(isset($message["ERROR"])) {{ $message["ERROR"] }} @else {{ $message["ALERT"] }} @endif </p>
                </div>
            </div>
        @endif        
        {!! Form::open(['route' => 'preinscripcion.alumnos', 'method' => 'GET', 'class' => 'form-inline' ]) !!}
            <div class="row form-inline">
                {{ Form::text('busquedapor', $buscar, ['id'=>'busquedapor', 'class' => 'form-control ml-3 mr-3 pl-4 pr-4', 'placeholder' => 'CURP / NOMBRE / No.CONTROL/ CURSO / No.GRUPO', 'aria-label' => 'CLAVE DEL CURSO', 'required' => 'required', 'size' => 48]) }}
                {{ Form::submit('BUSCAR', ['id'=>'buscar','class' => 'btn pl-4 pr-4']) }}
            </div>
        {!! Form::close() !!}
            <table class="table table-bordered">                
                <thead>
                    <tr>
                        <th>MATRÍCULA</th>
                        <th>FOLIO</th>
                        <th style="width: 150px;">NOMBRE</th>
                        <th>N° GRUPO</th>
                        <th>CLAVE</th>
                        <th style="width: 200px;">CURSO</th>
                        <th>FECHAS</th>
                        <th>HORARIO</th>                        
                        <th>SID</th>
                    </tr>
                </thead>
                <tbody>
                @if($alumnos)
                    @foreach ($alumnos as $itemData)
                        <tr>
                            <td>{{$itemData->matricula}}</td>
                            <td>{{$itemData->folio}}</td>
                            <td>{{$itemData->alumno}}</td>
                            <td>{{$itemData->folio_grupo}}</td>
                            <td>{{ $itemData->clave }}</td>
                            <td>{{ $itemData->nombre_curso }}</td>
                            <td>{{$itemData->inicio}} AL {{$itemData->termino}}</td>
                            <td>{{$itemData->horario}}</td>                            
                            <td>
                                <a target="_blank" href="{{route('documento.sid', ['nocontrol' => base64_encode($itemData->id_reg)])}}" class="nav-link" ><i class="fa fa-eye fa-2x fa-lg text-black" title="Imprimir SID"></i></a>
                            </td>                           
                        </tr>
                    @endforeach
                
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            {{ $alumnos->appends(request()->query())->links() }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        <br>
    </div>
    <br>
@endsection