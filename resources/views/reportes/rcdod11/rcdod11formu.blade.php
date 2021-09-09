<?php
$i=1;
?>
@extends('theme.sivyc.layout')
@section('title', 'Reportes | SIVyC Icatech')
@section('content')
<link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <style>
        table tr th .nav-link {padding: 25px; margin: 0;}
    </style>
    <div class="card-header">
        Reporte RCDOD-11

    </div>
    <div class="card card-body">
        @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{$message}}</p>
                </div>
        @endif
        <form method="POST" id="frm" name="frm">
            <div class="row form-inline">
                <div class="form-group">
                    <select name="unidades" id="unidades" class="form-control" placeholder=" ">
                        <option selected disabled="">Selecciona la unidad</option>
                        <option value="TODO">TODO</option>
                        @if($tipo=='string')
                        <option value="{{$unidades}}">{{$unidades}}</option>
                        @else
                        @foreach($unidades as $unidad)
                        <option value="{{$unidad}}">{{$unidad}}</option>
                        @endforeach
                        @endif  
                    </select>
                </div>
                <div class="form-group">
                    <input type="date" name="fecha_inicio" class="form-control" placeholder="Fecha de inicio">
                </div>
                <div class="form-group">
                    <input type="date" name="fecha_termino" class="form-control" placeholder="Fecha de termino">
                </div>
                <div class="form-group">
                    <input type="submit" value="Filtrar" name="filtrar" id="filtrar" class="btn">
                </div>
                <div class="form-group">
                    <input type="submit" value="PDF" name="pdf" id="pdf" class="btn">
                </div>
                {{csrf_field()}}
            </div>
        </form>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <td>NUM</td>
                            <td>NÚMERO DE CONTROL</td>
                            <td style="whidth:35%; text-align:center"><P>NOMBRE DEL ALUMNO</P><P>PRIMER APELLIDO/SEGUNDO APELLIDO/NOMBRE(S)</P></td>
                            <td>FOLIO DEL DIPLOMA</td>
                            <td>FOLIO DEL DUPLICADO</td>
                            <td>FECHA DE RECIBIDO</td>
                            <td>FIRMA DEL ALUMNO</td>
                        </tr>
                    </thead>
                    @if(isset($consulta))
                    <tbody>
                        @foreach($consulta as $item)
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{$item->matricula}}</td>
                            <td>{{$item->nombre}}</td>
                            <td>{{$item->folio}}</td>
                            <td>{{$item->duplicado}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script_content_js') 
        <script language="javascript">           
            $(document).ready(function(){ 
                $("#filtrar" ).click(function(){ $('#frm').attr('action', "{{route('carter')}}"); $('#frm').submit(); });
                $("#pdf" ).click(function(){ $('#frm').attr('action', "{{route('carter.pdf')}}"); $("#frm").attr("target", '_blanck');$('#frm').submit();});                                
            });
        </script>  
@endsection