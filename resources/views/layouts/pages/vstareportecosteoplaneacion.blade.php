<!-- Creado por Orlando Chávez 04012021-->
@extends('theme.sivyc.layout')
@section('title', 'Reporte de Costeo de Suficiencias Presupuestales| Sivyc Icatech')
<@section('content_script_css')
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
</style>
@endsection
@section('content')       
    <div class="card-header">
        Reportes / Costeo de Suficiencias Presupuestales
    </div>
    <div class="card card-body" >
        <form action="{{ route('planeacion.reporte.costeo.xl') }}" method="post" id="registercontrato" class="p-5">
            @csrf            
            <div class="form-row">
                <h2>Ingrese el periódo:</h2>
            </div>
            <hr/>
            <div class="form-row">
                <div class="col-md-6">
                    <label for="inputid_curso"><h3>De:</h3></label>
                    <input type="date" name="fecha1" id="fecha1" class="form-control col-md-6" required>
                </div>
                <div class="col-md-6">
                    <label for="inputid_curso"><h3>Hasta:</h3></label>
                    <input type="date" name="fecha2" id="fecha2" class="form-control col-md-6" required>                
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
<script src="{{ asset("js/validate/orlandoValidate.js") }}"></script>
@endsection
