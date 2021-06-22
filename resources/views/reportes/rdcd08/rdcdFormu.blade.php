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
        Reporte RDCD-08

    </div>
    <div class="card card-body" >
        <br />
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <br>
                    <form action="{{route('lolipop')}}" method="POST" id="cacahuate" target="_blank">
                        <div class="row form-inline">
                            <table>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <select name="unidades" class="form-control" placeholder="Seleciona una Unidad" id="unidades">
                                                <option value=0 selected disabled="">Selecciona una Unidad</option>
                                                @if($tipo=='string')
                                                <option>{{$unidades}}</option>
                                                @else
                                                @foreach($unidades as $unidad)
                                                <option>{{$unidad}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select name="modalidad" class="form-control" placeholder="Selecciona una Modalidad" id="modalidad">
                                                <option selected disabled="">Selecciona una Modalidad</option>
                                                <option>CAE</option>
                                                <option>EXT</option>
                                                <option>GRAL</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="date" name="fecha_acta" class="form-control" id="fecha_acta" placeholder="Fecha de acta">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="submit" value="Generar PDF" class="btn">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        {{csrf_field()}}
                    </form>
                </div>
            </div>
        </div>
        <table class="table">
            <tr>
                <td>#</td>
                <td>UNIDAD</td>
                <td>FECHA ACTA</td>
                <td>FOLIO INICIAL</td>
                <td>FOLIO FINAL</td>
                <td>MODALIDAD</td>
                <td>TOTAL</td>
                <td> </td>
            </tr>
            @foreach ($actas as $item)
            <tr>     
                 <td>{{ $i++ }}</td>          
                 <td>{{ $item->unidad }}</td>
                 <td>{{ $item->facta }}</td> 
                 <td>{{ $item->finicial }}</td>
                 <td>{{ $item->ffinal }}</td>
                 <td>{{ $item->mod }}</td>
                 <td>{{ $item->total }}</td>
                 <td><a type="button" class="btn btn-primary" href="{{route('nombre',['id'=>$item->id])}}" target="_blank">PDF</a></td>                
            </tr>
        @endforeach
        </table>
    </div>
@endsection
@section('script_content_js')
<script language="javascript">
    $( function() {
        $('#cacahuate').validate({
            rules: {
                unidades: { required: true },
                modalidad: {required: true },
                fecha_acta: {required: true }
            },
            messages: {
                unidades: { required: 'Por favor ingrese la unidad' },
                modalidad: { required: 'Por favor ingrese la modalidad' },
                fecha_acta: { required: 'Por favor ingrese la fecha del acta' }
            }
        });
    });
</script>
@endsection
