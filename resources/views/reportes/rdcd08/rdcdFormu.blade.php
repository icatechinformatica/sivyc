<?php
$i=1;
$a=0;
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
        <form action="{{route('lolipop')}}" method="POST" id="cacahuate">
            <div class="row form-inline">
                <div class="form-group">
                    <select name="unidades" class="form-control" placeholder=" " id="unidades">
                        <option value=0 selected disabled="">Selecciona una Unidad</option>
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
                    <select name="modalidad" class="form-control" placeholder=" " id="modalidad">
                        <option value="0" selcted disable="">Seleciona una Modalidad</option>
                        <option value="TODO">TODO</option>
                        <option value="CAE">CAE</option>
                        <option value="EXT">EXT</option>
                        <option value="GRAL">GENERAL</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="submit" value="FILTRAR" class="btn">
                </div>
            </div>
            {{csrf_field()}}
        </form>
        <br />
        <table class="table">
            <tr>
                <td>#</td>
                <td>UNIDAD</td>
                <td>FECHA ACTA</td>
                <td>FOLIO INICIAL</td>
                <td>FOLIO FINAL</td>
                <td>MODALIDAD</td>
                <td>TOTAL</td>
                <td>EXISTENTES</td>
                <td> </td>
            </tr>
            @foreach ($actas as $key => $item)
            <tr>     
                 <td>{{ $i++ }}</td>          
                 <td>{{ $item->unidad }}</td>
                 <td>{{ $item->facta }}</td> 
                 <td>{{ $item->finicial }}</td>
                 <td>{{ $item->ffinal }}</td>
                 <td>{{ $item->mod }}</td>
                 <td>{{ $item->total }}</td>
                 <td>
                    @php $a= $item->total - ($cuerpo2[$key][0]->expedidos); @endphp {{$a}}
                 </td>
                 <td><a type="button" class="btn {{$a==0 ? 'btn btn-success' : 'btn-danger'}}" href="{{route('nombre',['id'=>$item->id])}}" target="_blank">PDF</a></td>                
            </tr>
        @endforeach
        </table>
    </div>
@endsection
