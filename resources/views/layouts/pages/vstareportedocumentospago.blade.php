<!-- Creado por Orlando Chávez 28102021-->
@extends('theme.sivyc.layout')
@section('title', 'Reporte de Tramites Recepcionados| Sivyc Icatech')
@section('content_script_css')
    <link rel="stylesheet" href="{{asset('css/global.css') }}" />
    <style>
        .radio-xl .custom-control-label::before,
        .radio-xl .custom-control-label::after {
        top: 1.2rem;
        width: 1.85rem;
        height: 1.85rem;
        }

        .radio-xl .custom-control-label {
        padding-top: 23px;
        padding-left: 10px;
        }

        td {
            text-align: center; /* center checkbox horizontally */
            vertical-align: middle; /* center checkbox vertically */
        }
        #choice-td{
            background-color: white;
        }
        table {
            border: 1px solid;
            width: 200px;
        }
        tr {
            height: 65px;
        }
       .modal
        {
            position: fixed;
            z-index: 999;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            background-color: Black;
            filter: alpha(opacity=60);
            opacity: 0.6;
            -moz-opacity: 0.8;
        }
        .center
        {
            z-index: 1000;
            margin: 300px auto;
            padding: 10px;
            width: 150px;
            background-color: White;
            border-radius: 10px;
            filter: alpha(opacity=100);
            opacity: 1;
            -moz-opacity: 1;
        }
        .center img
        {
            height: 128px;
            width: 128px;
        }
    </style>
@endsection
@section('content')       
    <div class="card-header">
        Reportes / Trámites Recepcionados       
    </div>
    <div class="card card-body">        
        <form action="{{ route('documentospago.pdf') }}" method="post" id="registercontrato">
            @csrf            
            <div class="form-row">
                <div class="form-group col-md-4"></div>
                <div class="form-group col-md-3">
                    <label for="unidad"><h3>Unidad:</h3></label>
                    <select name="unidad" id="unidad" class="form-control">
                        <option value="SIN ESPECIFICAR">SIN ESPECIFICAR</option>
                        @foreach ($unidades as $data )
                            <option value="{{$data->ubicacion}}">{{$data->ubicacion}}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="form-group col-md-3">
                    <label for="tipo"><h3>Tipo de Curso:</h3></label>
                    <select name="tipo" id="tipo" class="form-control">
                        <option value="SIN ESPECIFICAR">SIN ESPECIFICAR</option>
                        <option value="CURSO">Honorarios</option>
                        <option value="CERTIFICACION">Certificación Extraordinaria</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="modalidad"><h3>Modalidad de Curso:</h3></label>
                    <select name="modalidad" id="modalidad" class="form-control">
                        <option value="SIN ESPECIFICAR">SIN ESPECIFICAR</option>
                        <option value="PRESENCIAL">Presencial</option>
                        <option value="A DISTANCIA">A Distancia</option>
                    </select>
                </div> --}}
            </div>
            <div class="form-row">
                <div class="form-group col-md-3"></div>
                <div class="form-group col-md-3">
                    <label for="fecha1"><h3>Fecha Inicio:</h3></label>
                    <input type="date" name="fecha1" id="fecha1" class="form-control" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="fecha2"><h3>Fecha Termino :</h3></label>
                    <input type="date" name="fecha2" id="fecha2" class="form-control" required>
                </div>
            </div>
            <br>
            <div class="row d-flex justify-content-between">
                <a class="btn btn" href="{{URL::previous()}}"> < Regresar</a>
                <button id="submit" name="submit" type="submit" class="btn btn-danger">Generar</button>
            </div>            
        </form>
        <!--display modal-->
        <div class="modal">
            <div class="center">
                <img alt="" src="{{URL::asset('/img/cargando.gif')}}" />
            </div>
        </div>
    </div>
@endsection
@section('script_content_js')
<script src="{{ asset("js/validate/autocomplete.js") }}"></script>
<script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
@endsection
